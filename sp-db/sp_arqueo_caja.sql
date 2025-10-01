
USE MOTORPARK;
DROP PROCEDURE sp_obtener_saldo_inicial;
DROP PROCEDURE IF EXISTS sp_obtener_datos_ultimo_arqueo;
SHOW CREATE PROCEDURE sp_obtener_datos_ultimo_arqueo;

DELIMITER //
CREATE PROCEDURE sp_obtener_datos_ultimo_arqueo()
BEGIN
    SELECT
        -- SI el último arqueo está marcado como ENTREGADO ('S'), el saldo inicial es 0.00.
        -- De lo contrario, usa el monto físico del último arqueo.
        CASE 
            WHEN T1.entregado = 'S' THEN 0.00 
            ELSE IFNULL(T1.monto_fisico, 0.00) 
        END AS saldo_inicial_para_hoy,
        
        TIME(T1.hora_fin) AS hora_ultimo_arqueo,
        DATE(T1.fecha) AS fecha_ultimo_arqueo
    FROM arqueocaja T1
    ORDER BY T1.fecha DESC, T1.hora_fin DESC, T1.idarqueo DESC 
    LIMIT 1;
END //
DELIMITER ;
CALL sp_obtener_datos_ultimo_arqueo ();


DROP PROCEDURE IF EXISTS sp_obtener_egresos_desde;

DELIMITER $$

  CREATE PROCEDURE sp_obtener_egresos_desde(
      IN fecha_ref DATE,
      IN hora_ref TIME
  )
  BEGIN
      DECLARE limite DATETIME;
      SET limite = TIMESTAMP(fecha_ref, hora_ref);

      SELECT
          COALESCE(SUM(monto), 0) AS total_egresos_nuevos
      FROM egresos
      WHERE creado > limite;
  END$$

  DELIMITER ;

CALL sp_obtener_egresos_desde('2025-09-25', '00:00');


DROP PROCEDURE IF EXISTS sp_obtener_ingresos_desde;
SHOW CREATE PROCEDURE sp_obtener_ingresos_desde;
DELIMITER $$

CREATE PROCEDURE sp_obtener_ingresos_desde(
    IN fecha_ref DATE,
    IN hora_ref TIME
)
BEGIN
    DECLARE limite DATETIME;
    SET limite = TIMESTAMP(fecha_ref, hora_ref); 

    SELECT
        COALESCE(SUM(CASE WHEN mediopago = 'Efectivo' THEN amortizacion ELSE 0 END), 0) AS ingresos_efectivo_nuevos,
        COALESCE(SUM(CASE WHEN mediopago != 'Efectivo' THEN amortizacion ELSE 0 END), 0) AS ingresos_digital_nuevos
    FROM pagos
    WHERE 
        fecharegistro > limite  
        AND amortizacion > 0;
END$$

DELIMITER ;


CALL sp_obtener_ingresos_desde ('2025-09-30','08:23:13');


UPDATE arqueocaja SET hora_fin = '15:10:13' WHERE idarqueo = 2;

SELECT * FROM pagos;
DELETE FROM cronogramas;
DELETE FROM pagos;
SELECT * FROM egresos;

UPDATE egresos 
SET fecha = '2025-09-24' 
WHERE idegreso = 43;


UPDATE egresos 
SET creado = '2025-09-24 23:59:59' 
WHERE idegreso = 43;

UPDATE egresos 
SET creado = '2025-09-24 23:59:59' 
WHERE idegreso = 39;

UPDATE pagos 
SET fecharegistro = '2024-09-24 23:59:59' 
WHERE idpago = 400;

UPDATE pagos 
SET fecharegistro= '2024-09-24 10:00:05' 
WHERE idpago = 411;

UPDATE pagos 
SET fechapago= '2024-09-24' 
WHERE idpago = 411;

CALL sp_obtener_ingresos_desde ('2025-09-25', '10:00:00');

SELECT * FROM Arqueocaja;

UPDATE arqueocaja SET hora_fin = '10:45' WHERE idarqueo = 2;
DROP PROCEDURE IF EXISTS sp_listar_arqueos_consolidados;

DELIMITER / /

CREATE PROCEDURE sp_listar_arqueos_consolidados()
BEGIN
    SELECT
        T1.fecha,
        
        (SELECT saldo_inicial 
         FROM arqueocaja 
         WHERE fecha = T1.fecha 
         ORDER BY creado ASC LIMIT 1) AS saldo_inicial_dia,


        (SELECT monto_fisico 
         FROM arqueocaja 
         WHERE fecha = T1.fecha 
         ORDER BY creado DESC LIMIT 1) AS monto_fisico_dia,

     
        (SELECT idarqueo 
         FROM arqueocaja 
         WHERE fecha = T1.fecha 
         ORDER BY creado DESC LIMIT 1) AS ultimo_idarqueo,
        (SELECT diferencia 
         FROM arqueocaja 
         WHERE fecha = T1.fecha 
         ORDER BY creado DESC LIMIT 1) AS diferencia_final,
        
       
        CASE WHEN MIN(T1.entregado) = 'S' AND MAX(T1.entregado) = 'S' THEN 'S' ELSE 'N' END AS entregado_dia,
        
        GROUP_CONCAT(T1.idarqueo ORDER BY T1.creado ASC) AS ids_arqueos_del_dia
    FROM arqueocaja T1
    WHERE T1.entregado = 'N'
    GROUP BY T1.fecha
    ORDER BY T1.fecha DESC;
END//

DELIMITER;
call sp_listar_arqueos_consolidados ();

CREATE PROCEDURE spu_arqueo_caja_insert(
    IN _idcolaborador INT,
    IN _hora_inicio TIME,
    IN _hora_fin TIME,
    IN _saldo_inicial DECIMAL(10,2),
    IN _ingresos_efectivo DECIMAL(10,2),
    IN _ingresos_digital DECIMAL(10,2),
    IN _egresos_dia DECIMAL(10,2),
    IN _monto_teorico DECIMAL(10,2),
    IN _monto_fisico DECIMAL(10,2),
    IN _diferencia DECIMAL(10,2),
    IN _observaciones VARCHAR(500)
)
BEGIN
    DECLARE _estado ENUM('Cuadrado', 'Faltante', 'Sobrante');
    
    -- Determinar el estado según la diferencia
    IF _diferencia = 0 THEN
        SET _estado = 'Cuadrado';
    ELSEIF _diferencia < 0 THEN
        SET _estado = 'Faltante';
    ELSE
        SET _estado = 'Sobrante';
    END IF;
    
    INSERT INTO arqueocaja (
        idcolaborador,
        fecha,
        hora_inicio,
        hora_fin,
        saldo_inicial,
        ingresos_efectivo,
        ingresos_digital,
        egresos_dia,
        monto_teorico,
        monto_fisico,
        diferencia,
        observaciones,
        estado,
        entregado
    ) VALUES (
        _idcolaborador,
        CURDATE(),
        _hora_inicio,
        _hora_fin,
        _saldo_inicial,
        _ingresos_efectivo,
        _ingresos_digital,
        _egresos_dia,
        _monto_teorico,
        _monto_fisico,
        _diferencia,
        _observaciones,
        _estado,
        'N'
    );
END //

DELIMITER;
SELECT * FROM arqueocaja;


DROP PROCEDURE IF EXISTS sp_reporte_arqueo_por_fecha_por_ciclo;

DELIMITER //
CREATE PROCEDURE sp_reporte_arqueo_por_fecha_por_ciclo(
    IN ids_arqueo_param TEXT
)
BEGIN
   
    DECLARE v_id_inicio INT;
    DECLARE v_saldo_inicial DECIMAL(10, 2);
    DECLARE v_hora_inicio_dia TIME;
    DECLARE v_nombre_cajero_inicio VARCHAR(255);
    
    DECLARE v_id_cierre INT;
    DECLARE v_fecha_cierre DATE;
    DECLARE v_monto_fisico_final DECIMAL(10, 2);
    DECLARE v_hora_fin_dia TIME;
    DECLARE v_nombre_cajero_fin VARCHAR(255);
    
    -- Variables para los acumulados
    DECLARE v_ingresos_efectivo_total DECIMAL(10, 2);
    DECLARE v_ingresos_digital_total DECIMAL(10, 2);
    DECLARE v_egresos_dia_total DECIMAL(10, 2);
    
    DECLARE v_fecha_hora_inicio DATETIME;
    DECLARE v_fecha_hora_fin DATETIME;

  
    SELECT 
        MIN(ac.idarqueo),
        MAX(ac.idarqueo)
    INTO 
        v_id_inicio,
        v_id_cierre
    FROM arqueocaja ac
    WHERE FIND_IN_SET(ac.idarqueo, ids_arqueo_param) > 0;


    SELECT 
        ac.saldo_inicial,
        ac.hora_inicio,
        CONCAT(p.nombres, ' ', p.apellidos)
    INTO 
        v_saldo_inicial, 
        v_hora_inicio_dia,
        v_nombre_cajero_inicio
    FROM arqueocaja ac
    JOIN colaboradores c ON ac.idcolaborador = c.idcolaborador
    JOIN contratoslaborales cl ON c.idcontratolaboral = cl.idcontratolaboral
    JOIN personas p ON cl.idpersona = p.idpersona
    WHERE ac.idarqueo = v_id_inicio
    LIMIT 1;
    
  
    SELECT 
        ac.fecha,
        ac.monto_fisico,
        ac.hora_fin,
        CONCAT(p.nombres, ' ', p.apellidos)
    INTO 
        v_fecha_cierre,
        v_monto_fisico_final,
        v_hora_fin_dia,
        v_nombre_cajero_fin
    FROM arqueocaja ac
    JOIN colaboradores c ON ac.idcolaborador = c.idcolaborador
    JOIN contratoslaborales cl ON c.idcontratolaboral = cl.idcontratolaboral
    JOIN personas p ON cl.idpersona = p.idpersona
    WHERE ac.idarqueo = v_id_cierre
    LIMIT 1;
        
  
    SELECT 
        SUM(ac.ingresos_efectivo),
        SUM(ac.ingresos_digital),
        SUM(ac.egresos_dia)
    INTO
        v_ingresos_efectivo_total,
        v_ingresos_digital_total,
        v_egresos_dia_total
    FROM arqueocaja ac
    WHERE FIND_IN_SET(ac.idarqueo, ids_arqueo_param) > 0;

    
    SELECT TIMESTAMP(fecha, hora_inicio) INTO v_fecha_hora_inicio FROM arqueocaja WHERE idarqueo = v_id_inicio;
    SELECT TIMESTAMP(fecha, hora_fin) INTO v_fecha_hora_fin FROM arqueocaja WHERE idarqueo = v_id_cierre;
    

    SELECT 
        v_fecha_cierre AS fecha,
        v_hora_inicio_dia AS hora_inicio,
        v_hora_fin_dia AS hora_fin,
        v_saldo_inicial AS saldo_inicial,
        v_ingresos_efectivo_total AS ingresos_efectivo_total,
        v_ingresos_digital_total AS ingresos_digital_total,
        v_egresos_dia_total AS egresos_dia_total,
        (v_saldo_inicial + v_ingresos_efectivo_total - v_egresos_dia_total) AS monto_teorico_final,
        v_monto_fisico_final AS monto_fisico_final,
        (v_monto_fisico_final - (v_saldo_inicial + v_ingresos_efectivo_total - v_egresos_dia_total)) AS diferencia,
        GROUP_CONCAT(DISTINCT ac.observaciones SEPARATOR ' | ') AS observaciones, 
        CONCAT('Inicio: ', v_nombre_cajero_inicio, ' | Cierre: ', v_nombre_cajero_fin) AS nombre_cajero
    FROM 
        arqueocaja ac 
    WHERE 
        FIND_IN_SET(ac.idarqueo, ids_arqueo_param) > 0
    LIMIT 1; 


SELECT
    ce.concepto,
    SUM(e.monto) AS monto_total
FROM egresos e
JOIN conceptoegreso ce ON e.idconceptoegreso = ce.idconceptoegreso
LEFT JOIN comprobantes com ON e.idegreso = com.idegreso
WHERE 
  
    e.creado BETWEEN v_fecha_hora_inicio AND v_fecha_hora_fin 
    AND (e.requierecomprobante = 'N' 
         OR (e.requierecomprobante = 'S' AND com.cargadocontabilidad = 'S'))
GROUP BY ce.concepto
ORDER BY monto_total DESC;



SELECT
    p.mediopago AS concepto,
    ep.entidad AS entidad_bancaria,
    SUM(p.amortizacion) AS monto
FROM pagos p
LEFT JOIN cuentaspago cp ON p.idcuentapago = cp.idcuentapago
LEFT JOIN entidadespago ep ON cp.identidadpago = ep.identidadpago
WHERE 
    
    p.fecharegistro BETWEEN v_fecha_hora_inicio AND v_fecha_hora_fin
    AND p.mediopago != 'Efectivo'
GROUP BY p.mediopago, ep.entidad
ORDER BY p.mediopago, ep.entidad;


END //
DELIMITER ;
CALL sp_reporte_arqueo_por_fecha_por_ciclo('21,22,23');



DROP PROCEDURE IF EXISTS sp_reporte_arqueo_por_fecha;



DELIMITER / /

DROP PROCEDURE IF EXISTS spu_arqueo_caja_insert;
SHOW CREATE PROCEDURE spu_arqueo_caja_insert;;


SELECT * FROM arqueocaja;
SELECT * FROM distritos;
SELECT * FROM locales;
SELECT * FROM contratos;
SELECT * FROM cronogramas;
SELECT * FROM pagos;
SELECT * FROM colaboradores;
SELECT * FROM contratoslaborales;


SELECT * FROM motorpark;

DELETE FROM locales WHERE idlocal = 3;




DROP PROCEDURE IF EXISTS sp_reporte_arqueo_por_fecha_por_ciclo;




CALL sp_reporte_arqueo_por_fecha_por_ciclo('30,31,32');
SELECT * FROM egresos;
SELECT * FROM arqueocaja;

SELECT entregasdineros
















---------------------------------------------------------------------------------------------------------------------------------------------------------------








DROP PROCEDURE sp_reporte_arqueo_por_fecha_y_sede;
DELIMITER //

CREATE PROCEDURE sp_reporte_arqueo_por_fecha_y_sede(
    IN ids_arqueo_param TEXT,
    IN idlocal_param INT
)
BEGIN
    DECLARE v_id_inicio INT;
    DECLARE v_id_cierre INT;
    DECLARE v_fecha_hora_inicio DATETIME;
    DECLARE v_fecha_hora_fin DATETIME;

    -- obtener arqueo inicial y final
    SELECT MIN(idarqueo), MAX(idarqueo)
    INTO v_id_inicio, v_id_cierre
    FROM arqueocaja
    WHERE FIND_IN_SET(idarqueo, ids_arqueo_param) > 0;

    -- obtener límites de fecha/hora
    SELECT TIMESTAMP(fecha, hora_inicio) INTO v_fecha_hora_inicio
    FROM arqueocaja WHERE idarqueo = v_id_inicio;

    SELECT TIMESTAMP(fecha, hora_fin) INTO v_fecha_hora_fin
    FROM arqueocaja WHERE idarqueo = v_id_cierre;

    -- ingresos de la sede solicitada
    SELECT
        l.idlocal,
        l.tienda,
        CONCAT(dep.departamento, ' / ', pro.provincia, ' / ', dist.distrito, ' / ', l.direccion) AS ubicacion,
        l.responsable,
        COALESCE(SUM(CASE WHEN p.mediopago = 'Efectivo' THEN p.amortizacion ELSE 0 END),0) AS ingresos_efectivo,
        COALESCE(SUM(CASE WHEN p.mediopago != 'Efectivo' THEN p.amortizacion ELSE 0 END),0) AS ingresos_digital,
        COALESCE(SUM(p.amortizacion),0) AS total_ingresos
    FROM pagos p
    JOIN cronogramas cr ON p.idcronograma = cr.idcronograma
    JOIN contratos ct ON cr.idcontrato = ct.idcontrato
    JOIN locales l ON ct.idlocal = l.idlocal
    JOIN distritos dist ON l.iddistrito = dist.iddistrito
    JOIN provincias pro ON dist.idprovincia = pro.idprovincia
    JOIN departamentos dep ON pro.iddepartamento = dep.iddepartamento
    WHERE p.fecharegistro BETWEEN v_fecha_hora_inicio AND v_fecha_hora_fin
      AND ct.idlocal = idlocal_param
      AND p.amortizacion > 0
    GROUP BY l.idlocal, l.tienda, l.direccion, l.responsable;

   
    SELECT
        p.mediopago AS concepto,
        ep.entidad AS entidad_bancaria,
        SUM(p.amortizacion) AS monto
    FROM pagos p
    LEFT JOIN cuentaspago cp ON p.idcuentapago = cp.idcuentapago
    LEFT JOIN entidadespago ep ON cp.identidadpago = ep.identidadpago
    JOIN cronogramas cr ON p.idcronograma = cr.idcronograma
    JOIN contratos ct ON cr.idcontrato = ct.idcontrato
    WHERE p.fecharegistro BETWEEN v_fecha_hora_inicio AND v_fecha_hora_fin
      AND p.mediopago != 'Efectivo'
      AND ct.idlocal = idlocal_param
      AND p.amortizacion > 0
    GROUP BY p.mediopago, ep.entidad
    ORDER BY p.mediopago, ep.entidad;

END //

DELIMITER ;

CALL  sp_reporte_arqueo_por_fecha_y_sede('30,31,32,33,34',7);



SELECT * FROM locales;
SELECT * FROM distritos;
SELECT * FROM provincias;
SELECT * FROM departamentos;
SELECT * FROM pagos;
SELECT * FROM contratos;