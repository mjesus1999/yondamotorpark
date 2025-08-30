

-- PROCEDIMIENTO: Obtener estadísticas de morosos
DROP PROCEDURE IF EXISTS sp_get_estadisticas_morosos;
DELIMITER $$
CREATE PROCEDURE sp_get_estadisticas_morosos()
BEGIN
    SELECT 
        COUNT(DISTINCT c.idcontrato) AS total_morosos,
        COALESCE(SUM(
            (cot.valorcuota + cro.penalidad) - COALESCE((
                SELECT SUM(pag.amortizacion)
                FROM pagos pag
                WHERE pag.idcronograma = cro.idcronograma
            ), 0)
        ), 0) AS deuda_total,
        
        -- Seguimientos registrados hoy
        (SELECT COUNT(*) 
         FROM seguimientos_morosos 
         WHERE DATE(fecha_seguimiento) = CURDATE()) AS seguimientos_hoy,
        
        -- Días promedio de atraso
        COALESCE(AVG(DATEDIFF(CURDATE(), cro.fechapago)), 0) AS dias_promedio
        
    FROM cronogramas cro
    INNER JOIN contratos c ON cro.idcontrato = c.idcontrato
    INNER JOIN cotizaciones cot ON c.idcotizacion = cot.idcotizacion
    WHERE c.estado = 'ACT'
      AND cro.estado IN ('Vencido', 'Pendiente')
      AND cro.fechapago < CURDATE()
      AND (
          -- Tiene saldo pendiente en cuota
          cot.valorcuota > COALESCE((
              SELECT SUM(pag.amortizacion)
              FROM pagos pag
              WHERE pag.idcronograma = cro.idcronograma
              AND pag.tipo = 'Cuota'
          ), 0)
          OR 
          -- Tiene saldo pendiente en penalidad
          cro.penalidad > COALESCE((
              SELECT SUM(pag.amortizacion)
              FROM pagos pag
              WHERE pag.idcronograma = cro.idcronograma
              AND pag.tipo = 'Penalidad'
          ), 0)
      );
END$$
DELIMITER ;


-- PROCEDIMIENTO: Obtener morosos clasificados
DROP PROCEDURE IF EXISTS sp_get_morosos_clasificados;
DELIMITER $$
CREATE PROCEDURE sp_get_morosos_clasificados()
BEGIN
    -- Primero actualizamos las cuotas vencidas
    UPDATE cronogramas cro
    INNER JOIN contratos cont ON cro.idcontrato = cont.idcontrato
    INNER JOIN cotizaciones coti ON cont.idcotizacion = coti.idcotizacion
    SET 
        cro.estado = 'Vencido',
        cro.aplicapenalidad = 'S',
        cro.penalidad = coti.valorcuota * cont.penalidadbase
    WHERE cro.fechapago < CURDATE()
      AND cro.estado != 'Pagado'
      AND cont.estado = 'ACT';

    -- Luego obtenemos los morosos con saldo pendiente
    SELECT 
        c.idcontrato,
        CONCAT(p.apellidos, ' ', p.nombres) AS cliente,
        p.tipodoc AS documento,
        p.nrodoc AS ndocumento,
        -- usar telprimario o telalternativo
        COALESCE(p.telprimario, p.telalternativo, '') AS telefono,
        
        -- Dirección completa
        CONCAT(
            COALESCE(l.tienda, 'Sin tienda'), ' / ',
            COALESCE(dep.departamento, 'Sin depto'), ' / ',
            COALESCE(d.distrito, 'Sin distrito'), ' / ',
            COALESCE(pro.provincia, 'Sin provincia')
        ) AS direccion,
        
        -- Información del vehículo
        CONCAT(
            COALESCE(mar.marca, 'Sin marca'), ' / ',
            COALESCE(model.modelo, 'Sin modelo'),
            ' / ',
            COALESCE(cb.combustible, 'Sin combustible'),
            ' / ',
            COALESCE(v.color, 'Sin color')
        ) AS vehiculo,
        
        cro.numcuota,
        DATE_FORMAT(cro.fechapago, '%d/%m/%Y') AS fecha_vencimiento,
        DATEDIFF(CURDATE(), cro.fechapago) AS dias_atraso,
        
        -- Saldo pendiente total de la cuota
        (cot.valorcuota + cro.penalidad) - COALESCE((
            SELECT SUM(pag.amortizacion)
            FROM pagos pag
            WHERE pag.idcronograma = cro.idcronograma
        ), 0) AS saldo_pendiente,
        
        -- Saldo pendiente solo de la cuota
        cot.valorcuota - COALESCE((
            SELECT SUM(pag.amortizacion)
            FROM pagos pag
            WHERE pag.idcronograma = cro.idcronograma
            AND pag.tipo = 'Cuota'
        ), 0) AS saldo_cuota,
        
        -- Saldo pendiente de penalidad
        cro.penalidad - COALESCE((
            SELECT SUM(pag.amortizacion)
            FROM pagos pag
            WHERE pag.idcronograma = cro.idcronograma
            AND pag.tipo = 'Penalidad'
        ), 0) AS saldo_penalidad,
        
        cro.estado,
        cro.idcronograma
        
    FROM cronogramas cro
    INNER JOIN contratos c ON cro.idcontrato = c.idcontrato
    INNER JOIN cotizaciones cot ON c.idcotizacion = cot.idcotizacion
    INNER JOIN clientes cli ON cot.idcliente = cli.idcliente
    INNER JOIN personas p ON cli.idpersona = p.idpersona
    INNER JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    INNER JOIN modelos model ON v.idmodelo = model.idmodelo
    INNER JOIN marcas mar ON model.idmarca = mar.idmarca
    INNER JOIN combustibles cb ON v.idcombustible = cb.idcombustible
    LEFT JOIN locales l ON c.idlocal = l.idlocal
    LEFT JOIN distritos d ON l.iddistrito = d.iddistrito
    LEFT JOIN provincias pro ON d.idprovincia = pro.idprovincia
    LEFT JOIN departamentos dep ON pro.iddepartamento = dep.iddepartamento
    
    WHERE c.estado = 'ACT'
      AND cro.estado IN ('Vencido', 'Pendiente')
      AND cro.fechapago < CURDATE()
      AND (
          -- Tiene saldo pendiente en cuota
          cot.valorcuota > COALESCE((
              SELECT SUM(pag.amortizacion)
              FROM pagos pag
              WHERE pag.idcronograma = cro.idcronograma
              AND pag.tipo = 'Cuota'
          ), 0)
          OR 
          -- Tiene saldo pendiente en penalidad
          cro.penalidad > COALESCE((
              SELECT SUM(pag.amortizacion)
              FROM pagos pag
              WHERE pag.idcronograma = cro.idcronograma
              AND pag.tipo = 'Penalidad'
          ), 0)
      )
    ORDER BY dias_atraso DESC, cro.numcuota;
END$$
DELIMITER ;

-- PROCEDIMIENTO: Registrar seguimiento de moroso
DROP PROCEDURE IF EXISTS sp_registrar_seguimiento_moroso;
DELIMITER $$
CREATE PROCEDURE sp_registrar_seguimiento_moroso(
    IN p_idcontrato INT,
    IN p_tipo ENUM('documento', 'evidencia'),
    IN p_observaciones TEXT,
    IN p_evidencia VARCHAR(255),
    IN p_fecha_seguimiento DATETIME,
    IN p_usuario_registro INT
)
BEGIN
    INSERT INTO seguimientos_morosos (
        idcontrato,
        tipo,
        observaciones,
        evidencia,
        fecha_seguimiento,
        usuario_registro
    ) VALUES (
        p_idcontrato,
        p_tipo,
        p_observaciones,
        p_evidencia,
        p_fecha_seguimiento,
        p_usuario_registro
    );
    
    SELECT LAST_INSERT_ID() AS last_insert_id;
END$$
DELIMITER ;

-- PROCEDIMIENTO: Obtener historial de seguimientos
DROP PROCEDURE IF EXISTS sp_get_historial_seguimientos;
DELIMITER $$
CREATE PROCEDURE sp_get_historial_seguimientos(IN p_idcontrato INT)
BEGIN
    SELECT 
        sm.idseguimiento,
        sm.tipo,
        sm.observaciones,
        sm.evidencia,
        DATE_FORMAT(sm.fecha_seguimiento, '%d/%m/%Y %H:%i') AS fecha_seguimiento,
        COALESCE(CONCAT(p.nombres, ' ', p.apellidos), co.usernick, NULL) AS usuario_registro,
        sm.created_at
    FROM seguimientos_morosos sm
    LEFT JOIN colaboradores co ON sm.usuario_registro = co.idcolaborador
    LEFT JOIN contratoslaborales cl ON co.idcontratolaboral = cl.idcontratolaboral
    LEFT JOIN personas p ON cl.idpersona = p.idpersona
    WHERE sm.idcontrato = p_idcontrato
    ORDER BY sm.fecha_seguimiento DESC, sm.idseguimiento DESC;
END$$
DELIMITER ;

DELIMITER $$

CREATE PROCEDURE getMorososResumen()
BEGIN
    SELECT
        cont.idcontrato,
        CONCAT(p.apellidos, ' ', p.nombres) AS cliente,
        p.nrodoc AS ndocumento,
        COALESCE(SUM((coti.valorcuota + cro.penalidad) - COALESCE(ps.total_amortizado, 0)), 0) AS deuda_total,
        COALESCE(MAX(
            CASE
                WHEN cro.fechapago < CURDATE() AND cro.estado != 'Pagado' THEN DATEDIFF(CURDATE(), cro.fechapago)
                ELSE 0
            END
        ), 0) AS dias_max_vencido,
        COALESCE(SUM(
            CASE
                WHEN cro.fechapago < CURDATE() AND cro.estado != 'Pagado' THEN 1
                ELSE 0
            END
        ), 0) AS cuotas_vencidas,
        cont.idlocal
    FROM contratos cont
    JOIN cotizaciones coti ON cont.idcotizacion = coti.idcotizacion
    JOIN cronogramas cro ON cro.idcontrato = cont.idcontrato
    JOIN clientes cli ON coti.idcliente = cli.idcliente
    JOIN personas p ON cli.idpersona = p.idpersona
    LEFT JOIN (
        SELECT idcronograma, COALESCE(SUM(amortizacion), 0) AS total_amortizado
        FROM pagos
        GROUP BY idcronograma
    ) ps ON ps.idcronograma = cro.idcronograma
    WHERE cont.estado = 'ACT'
    GROUP BY cont.idcontrato, cliente, ndocumento, cont.idlocal
    HAVING deuda_total > 0 OR cuotas_vencidas > 0
    ORDER BY dias_max_vencido DESC, deuda_total DESC;
END $$

DELIMITER ;



/*
-- Probar estadísticas
CALL sp_get_estadisticas_morosos();
-- Probar morosos clasificados
CALL sp_get_morosos_clasificados();
-- Probar historial
CALL sp_get_historial_seguimientos(2);
*/