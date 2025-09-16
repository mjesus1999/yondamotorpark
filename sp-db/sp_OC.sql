
USE motorpark2;

DROP PROCEDURE IF EXISTS sp_oc_por_estado;
DELIMITER $$
CREATE PROCEDURE sp_oc_por_estado(IN p_estado VARCHAR(50))
BEGIN
    SELECT 
        oc.idordencompra,
        oc.serie,
        DATE_FORMAT(oc.emision, '%d-%m-%Y') AS emision, 
        oc.moneda,
        con.razonsocial,
        CONCAT(dep.departamento, ' / ', p.provincia, ' / ', d.distrito) AS ubicacion,
        fn_format_oc_display_id(oc.idordencompra, oc.serie) AS numeroOCIdentificador,

        ROUND((
            SELECT IFNULL(SUM(preciocompra * 1.18),0) 
            FROM detordencompra 
            WHERE idordencompra = oc.idordencompra
        ), 2) AS totalOC,

        ROUND((
            SELECT IFNULL(SUM(
                CASE 
                    WHEN moneda = 'PEN' AND tipocambio > 0 THEN amortizacion / tipocambio
                    ELSE amortizacion
                END
            ),0)
            FROM pagosOC
            WHERE idorden = oc.idordencompra
        ), 2) AS totalPagado,

        ROUND((
            (SELECT IFNULL(SUM(preciocompra * 1.18),0) 
             FROM detordencompra 
             WHERE idordencompra = oc.idordencompra)
            -
            (SELECT IFNULL(SUM(
                CASE 
                    WHEN moneda = 'PEN' AND tipocambio > 0 THEN amortizacion / tipocambio
                    ELSE amortizacion
                END
            ),0)
             FROM pagosOC 
             WHERE idorden = oc.idordencompra)
        ), 2) AS saldoRestante

    FROM ordenescompra oc
    JOIN tiendas t ON oc.idtienda = t.idtienda
    JOIN concesionarios con ON t.idconcesionario = con.idconcesionario
    JOIN distritos d ON t.iddistrito = d.iddistrito
    JOIN provincias p ON d.idprovincia = p.idprovincia
    JOIN departamentos dep ON p.iddepartamento = dep.iddepartamento
    WHERE oc.estado = CONVERT(p_estado USING utf8mb4) COLLATE utf8mb4_general_ci
    ORDER BY oc.idordencompra DESC;
END $$
DELIMITER ;

-- INSERT INTO entidadespago(entidad)VALUES('Interbank');
-- INSERT INTO entidadespago(entidad)VALUES('BBVA');

-- INSERT INTO entidadespago(entidad)VALUES('Scotiabank');
-- INSERT INTO entidadespago(entidad)VALUES('Banco Pichincha');
-- INSERT INTO entidadespago(entidad)VALUES('Banco de la Nación');
-- INSERT INTO entidadespago(entidad)VALUES('Banco GNB');
-- INSERT INTO entidadespago(entidad)VALUES('Banco Falabella');
-- INSERT INTO entidadespago(entidad)VALUES('Banco Ripley');
-- INSERT INTO entidadespago(entidad)VALUES('Banco Azteca');


CALL sp_oc_por_estado('proceso');



DROP PROCEDURE sp_getAll_OC_Compras;

DELIMITER //
CREATE PROCEDURE sp_getAll_OC_Compras()
BEGIN
    SELECT
        orden.idordencompra,
        
        com.idcompra,
        
        orden.serie AS serie_oc,
        
        orden.emisiON AS fecha_emision_oc,
        
        com.fechacompra,
        
        com.numdocumento AS num_factura,
        
        concesionario.nombrecomercial,
        
        CONCAT_WS(' / ',
        dep.departamento,
        prov.provincia,
        dist.distrito,
         tienda.direccion) AS direccion_completa_concesionario,
        
        COUNT(
    CASE 
           
    WHEN veh.disponibilidad = 'proceso' 
           
        AND (veh.chasis IS NULL
        OR TRIM(veh.chasis) = ''
        OR veh.seriemotOR IS NULL
        OR TRIM(veh.seriemotor) = '')
            THEN
    1 
           
    ELSE NULL 
        END) AS vehiculos_pendientes,
        COUNT(
  CASE
   
    WHEN veh.disponibilidad = 'proceso'
     AND veh.chasis IS NOT NULL
        AND TRIM(veh.chasis) <> ''
     AND veh.seriemotOR IS NOT NULL
        AND TRIM(veh.seriemotor) <> ''
    THEN
    1
   
    ELSE NULL
  END
) AS listos_para_liberar

    FROM
        comprAS AS com
    INNER JOIN
        ordenescompra AS orden
    ON com.idorden = orden.idordencompra
    INNER JOIN
        tiendAS AS tienda
    ON orden.idtienda = tienda.idtienda
    INNER JOIN
        concesionarios AS concesionario
    ON tienda.idconcesionario = concesionario.idconcesionario
    INNER JOIN
        distritos AS dist
    ON tienda.iddistrito = dist.iddistrito
    INNER JOIN
        provinciAS AS prov
    ON dist.idprovincia = prov.idprovincia
    INNER JOIN
        departamentos AS dep
    ON prov.iddepartamento = dep.iddepartamento
    INNER JOIN
        detordencompra AS detoc
    ON orden.idordencompra = detoc.idordencompra
    INNER JOIN
        vehiculos AS veh
    ON detoc.idvehiculo = veh.idvehiculo
    WHERE
        detoc.estado = '1'
   
    GROUP BY 
        orden.idordencompra,
        com.idcompra,
        orden.serie,
        orden.emision,
        com.fechacompra,
        com.numdocumento,
        concesionario.nombrecomercial,
        direccion_completa_concesionario
    HAVING
        vehiculos_pendientes > 0
        OR listos_para_liberar > 0
   
ORDER BY 
        com.fechacompra DESC;
END //
DELIMITER ;


DROP PROCEDURE IF EXISTS sp_get_OC_details_for_recepcion;

DELIMITER //
CREATE PROCEDURE sp_get_OC_details_for_recepcion(IN p_idcompra INT)
BEGIN
 DECLARE v_idorden INT;
 DECLARE v_total_costo DECIMAL(10, 2);
 DECLARE v_total_amortizado_usd DECIMAL(10, 2);

 -- Obtener el ID de la orden de compra y el costo total de los vehículos
 SELECT
  orden.idordencompra,
  SUM(doc.preciocompra)
 INTO
  v_idorden,
  v_total_costo
 FROM
  compras AS com
 INNER JOIN
  ordenescompra AS orden ON com.idorden = orden.idordencompra
 INNER JOIN
  detordencompra AS doc ON orden.idordencompra = doc.idordencompra
 WHERE
  com.idcompra = p_idcompra AND doc.estado = '1'
 GROUP BY
  orden.idordencompra;

 -- Obtener el total amortizado para la OC, convirtiendo los montos a USD si es necesario
 SELECT
  IFNULL(SUM(
    CASE 
      WHEN pagos.moneda = 'PEN' AND pagos.tipocambio > 0 THEN pagos.amortizacion / pagos.tipocambio
      ELSE pagos.amortizacion
    END
  ), 0)
 INTO
  v_total_amortizado_usd
 FROM
  pagosOC AS pagos
 WHERE
  pagos.idorden = v_idorden;

 -- Primer conjunto de resultados: Información del Concesionario y datos de la OC
 SELECT
  concesionario.razonsocial AS razonsocial_concesionario,
  com.fechacompra,
  v_total_amortizado_usd AS total_amortizado,
  v_total_costo * 1.18 AS totalcompra,
  (v_total_costo * 1.18) - v_total_amortizado_usd AS saldopendiente
 FROM
  compras AS com
 INNER JOIN
  ordenescompra AS orden ON com.idorden = orden.idordencompra
 INNER JOIN
  tiendas AS tienda ON orden.idtienda = tienda.idtienda
 INNER JOIN
  concesionarios AS concesionario ON tienda.idconcesionario = concesionario.idconcesionario
 WHERE
  com.idcompra = p_idcompra
 LIMIT 1;

 -- Segundo conjunto de resultados: Lista de vehículos por recepcionar
 SELECT
  veh.idlocal,
  veh.idvehiculo,
  modelo.modelo AS modelo,
  veh.version,
  comb.combustible AS combustible,
  modelo.anio,
  veh.color,
  veh.chasis,
  veh.placa,
  veh.placarotativa,
  veh.seriemotor,
  veh.disponibilidad
 FROM
  detordencompra AS detoc
 INNER JOIN
  compras AS com ON detoc.idordencompra = com.idorden
 INNER JOIN
  vehiculos AS veh ON detoc.idvehiculo = veh.idvehiculo
 INNER JOIN
  modelos AS modelo ON veh.idmodelo = modelo.idmodelo
 INNER JOIN
  combustibles AS comb ON veh.idcombustible = comb.idcombustible
 WHERE
  com.idcompra = p_idcompra
  AND detoc.estado = '1'
  AND veh.disponibilidad = 'proceso'
 ORDER BY
  modelo.anio DESC;

END //

DELIMITER CALL sp_get_OC_details_for_recepcion(13);




DELIMITER //

CREATE PROCEDURE sp_anular_OC(
    IN estado_ VARCHAR(20),
    IN observaciones_ VARCHAR(400),
    IN idordencompra_ INT
)
BEGIN
    -- 1. Actualizar la orden
    UPDATE ordenescompra 
    SET estado = estado_,
        observaciones = observaciones_,
        fechanulado = NOW(),
        anulacion = CURDATE()
    WHERE idordencompra = idordencompra_;

    -- 2. Actualizar vehículos vinculados a la orden
    UPDATE vehiculos v
    INNER JOIN detordencompra d ON v.idvehiculo = d.idvehiculo
    SET v.estado = '0',
        v.eliminado = NOW()
    WHERE d.idordencompra = idordencompra_;

    -- 3. Actualizar detordencompra 
    UPDATE detordencompra
    SET estado = '0'
    WHERE idordencompra = idordencompra_;
END //

DELIMITER ;



DELIMITER //
CREATE PROCEDURE sp_reporte_general_oc_proceso()
BEGIN
    -- Declaración de variables para el Resumen Ejecutivo
    DECLARE total_oc_proceso INT;
    DECLARE total_vehiculos_pendientes INT;
    DECLARE deuda_global DECIMAL(15, 2);
    DECLARE pagos_recibidos DECIMAL(15, 2);
    DECLARE saldo_pendiente DECIMAL(15, 2);
    DECLARE avance_general DECIMAL(5, 2);

    --  Consulta para el Resumen Ejecutivo (Primer conjunto de resultados)
    -- Contar Órdenes de Compra en estado 'proceso'
    SELECT COUNT(*)
    INTO total_oc_proceso
    FROM ordenescompra
    WHERE estado = 'proceso';

    -- Sumar los precios de compra de todos los vehículos de las OCs en proceso, aplicando el IGV (1.18)
    SELECT COALESCE(SUM(totalOC), 0)
    INTO deuda_global
    FROM (
        SELECT SUM(doc.preciocompra * 1.18) AS totalOC
        FROM detordencompra doc
        INNER JOIN ordenescompra oc ON doc.idordencompra = oc.idordencompra
        WHERE oc.estado = 'proceso'
        GROUP BY oc.idordencompra
    ) AS subquery;

    -- Sumar la amortización (pagos) de todas las OCs en proceso, convirtiendo PEN a USD
    SELECT COALESCE(SUM(totalPagado), 0)
    INTO pagos_recibidos
    FROM (
        SELECT SUM(
            CASE
                WHEN poc.moneda = 'PEN' AND poc.tipocambio > 0 THEN poc.amortizacion / poc.tipocambio
                ELSE poc.amortizacion
            END
        ) AS totalPagado
        FROM pagosOC poc
        INNER JOIN ordenescompra oc ON poc.idorden = oc.idordencompra
        WHERE oc.estado = 'proceso'
        GROUP BY oc.idordencompra
    ) AS subquery;

    -- Contar el total de vehículos en OCs en proceso
    SELECT COALESCE(COUNT(doc.idvehiculo), 0)
    INTO total_vehiculos_pendientes
    FROM detordencompra doc
    INNER JOIN ordenescompra oc ON doc.idordencompra = oc.idordencompra
    WHERE oc.estado = 'proceso';
    
    -- Calcular el saldo pendiente y el avance general
    SET saldo_pendiente = deuda_global - pagos_recibidos;
    SET avance_general = IF(deuda_global > 0, (pagos_recibidos / deuda_global) * 100, 0);
    
    -- Primer conjunto de resultados: Resumen Ejecutivo
    SELECT
        total_oc_proceso AS 'totalOCProceso',
        total_vehiculos_pendientes AS 'totalVehiculos',
        ROUND(deuda_global, 2) AS 'Deudaglobal',
        ROUND(pagos_recibidos, 2) AS 'totalPagosGlobal',
        ROUND(saldo_pendiente, 2) AS 'saldoPendienteGlobal',
        ROUND(avance_general, 2) AS 'porcentajeAvanceGlobal';

    -- Consulta para el Detalle de Órdenes de Compra y Vehículos (Segundo conjunto de resultados)
    SELECT
        CONCAT(oc.serie, '-', LPAD(oc.idordencompra, 5, '0')) AS 'OCIdentificador',
        con.nombrecomercial AS 'Concesionario',
        CONCAT_WS(', ', dep.departamento, p.provincia, d.distrito) AS 'ubicacionConcesionario',
        ROUND((
            SELECT IFNULL(SUM(preciocompra * 1.18), 0)
            FROM detordencompra
            WHERE idordencompra = oc.idordencompra
        ), 2) AS 'totalOC',
        ROUND((
            SELECT IFNULL(SUM(
                CASE 
                    WHEN moneda = 'PEN' AND tipocambio > 0 THEN amortizacion / tipocambio
                    ELSE amortizacion
                END
            ), 0)
            FROM pagosOC
            WHERE idorden = oc.idordencompra
        ), 2) AS 'pagado',
        ROUND((
            (SELECT IFNULL(SUM(preciocompra * 1.18), 0) FROM detordencompra WHERE idordencompra = oc.idordencompra) -
            (SELECT IFNULL(SUM(
                CASE 
                    WHEN moneda = 'PEN' AND tipocambio > 0 THEN amortizacion / tipocambio
                    ELSE amortizacion
                END
            ), 0) FROM pagosOC WHERE idorden = oc.idordencompra)
        ), 2) AS 'saldo',
        IF(
            (SELECT IFNULL(SUM(preciocompra * 1.18), 0) FROM detordencompra WHERE idordencompra = oc.idordencompra) > 0,
            ROUND(
                (
                    (SELECT IFNULL(SUM(
                        CASE 
                            WHEN moneda = 'PEN' AND tipocambio > 0 THEN amortizacion / tipocambio
                            ELSE amortizacion
                        END
                    ), 0) FROM pagosOC WHERE idorden = oc.idordencompra)
                    /
                    (SELECT IFNULL(SUM(preciocompra * 1.18), 0) FROM detordencompra WHERE idordencompra = oc.idordencompra)
                ) * 100, 2),
            0
        ) AS 'avancePorcentaje',
        (
            SELECT COUNT(d.idvehiculo) 
            FROM detordencompra d 
            WHERE d.idordencompra = oc.idordencompra
        ) AS 'totalVehiculos',
        oc.emision,
        GROUP_CONCAT(CONCAT(mar.marca, ' ', modl.modelo, ' / ', v.chasis) SEPARATOR ', ') AS 'Detalle de Vehículos'
    FROM
        ordenescompra oc
    LEFT JOIN
        detordencompra doc ON oc.idordencompra = doc.idordencompra
    LEFT JOIN
        tiendas t ON oc.idtienda = t.idtienda
    LEFT JOIN
        concesionarios con ON t.idconcesionario = con.idconcesionario
    LEFT JOIN
        vehiculos v ON doc.idvehiculo = v.idvehiculo
    LEFT JOIN
        modelos modl ON v.idmodelo = modl.idmodelo
    LEFT JOIN
        marcas mar ON modl.idmarca = mar.idmarca
    LEFT JOIN
        distritos d ON t.iddistrito = d.iddistrito
    LEFT JOIN
        provincias p ON d.idprovincia = p.idprovincia
    LEFT JOIN
        departamentos dep ON p.iddepartamento = dep.iddepartamento
    WHERE
        oc.estado = 'proceso'
    GROUP BY
        oc.idordencompra, ubicacionConcesionario
    ORDER BY
        oc.idordencompra DESC;

END 

DELIMITER ;
CALL sp_reporte_general_oc_proceso();



DROP PROCEDURE IF EXISTS sp_reporte_concesionario_detallado;

DELIMITER //
CREATE PROCEDURE sp_reporte_concesionario_detallado(
  IN concesionario_id INT
)
BEGIN
  -- Declaración de variables para el Resumen Ejecutivo
  DECLARE total_oc_proceso INT;
  DECLARE total_vehiculos_pendientes INT;
  DECLARE deuda_global DECIMAL(15, 2);
  DECLARE pagos_recibidos DECIMAL(15, 2);
  DECLARE saldo_pendiente DECIMAL(15, 2);
  DECLARE avance_general DECIMAL(5, 2);

  -- 1. Resumen Ejecutivo (Primer conjunto de resultados)
  -- Contar OCs en proceso para el concesionario
  SELECT COUNT(DISTINCT oc.idordencompra)
  INTO total_oc_proceso
  FROM ordenescompra oc
  INNER JOIN tiendas t ON oc.idtienda = t.idtienda
  INNER JOIN concesionarios con ON t.idconcesionario = con.idconcesionario
  WHERE oc.estado = 'proceso'
  AND con.idconcesionario = concesionario_id;

  -- Calcular la deuda global (suma de los totales de cada OC)
  SELECT COALESCE(SUM(totalOC_sub), 0)
  INTO deuda_global
  FROM (
    SELECT oc.idordencompra, SUM(doc.preciocompra * 1.18) AS totalOC_sub
    FROM ordenescompra oc
    INNER JOIN tiendas t ON oc.idtienda = t.idtienda
    INNER JOIN concesionarios con ON t.idconcesionario = con.idconcesionario
    INNER JOIN detordencompra doc ON oc.idordencompra = doc.idordencompra
    WHERE oc.estado = 'proceso'
    AND con.idconcesionario = concesionario_id
    GROUP BY oc.idordencompra
  ) AS sub_query;

  -- Calcular los pagos recibidos (suma de los pagos de cada OC, convertidos a USD)
  SELECT COALESCE(SUM(pagos_sub), 0)
  INTO pagos_recibidos
  FROM (
    SELECT oc.idordencompra, SUM(
      CASE
        WHEN poc.moneda = 'PEN' AND poc.tipocambio > 0 THEN poc.amortizacion / poc.tipocambio
        ELSE poc.amortizacion
      END
    ) AS pagos_sub
    FROM ordenescompra oc
    INNER JOIN tiendas t ON oc.idtienda = t.idtienda
    INNER JOIN concesionarios con ON t.idconcesionario = con.idconcesionario
    INNER JOIN pagosOC poc ON oc.idordencompra = poc.idorden
    WHERE oc.estado = 'proceso'
    AND con.idconcesionario = concesionario_id
    GROUP BY oc.idordencompra
  ) AS sub_query;

  -- Contar el total de vehículos
  SELECT COALESCE(COUNT(doc.idvehiculo), 0)
  INTO total_vehiculos_pendientes
  FROM detordencompra doc
  INNER JOIN ordenescompra oc ON doc.idordencompra = oc.idordencompra
  INNER JOIN tiendas t ON oc.idtienda = t.idtienda
  INNER JOIN concesionarios con ON t.idconcesionario = con.idconcesionario
  WHERE oc.estado = 'proceso'
  AND con.idconcesionario = concesionario_id;
  
  -- Calcular el saldo y el avance
  SET saldo_pendiente = deuda_global - pagos_recibidos;
  SET avance_general = IF(deuda_global > 0, (pagos_recibidos / deuda_global) * 100, 0);
  
  -- Mostrar el resumen ejecutivo
  SELECT
    total_oc_proceso AS 'totalOCs',
    total_vehiculos_pendientes AS 'totalvehiculos',
    ROUND(deuda_global, 2) AS 'deudatotal',
    ROUND(pagos_recibidos, 2) AS 'totalpagado',
    ROUND(saldo_pendiente, 2) AS 'saldopendiente',
    ROUND(avance_general, 2) AS 'avance';

  -- 2. Detalle de Pagos (Segundo conjunto de resultados)
  SELECT
    oc.idordencompra,
    CONCAT(oc.serie, '-', LPAD(oc.idordencompra, 5, '0')) AS 'OCIdentificador',
    poc.fecharealpago AS 'fechapago',
    edp.entidad AS 'entidad',
    poc.numtransaccion AS 'numtransaccion',
    poc.moneda AS 'moneda',
    poc.amortizacion AS 'montopagado',
    poc.tipocambio AS 'tipocambio',
    CASE 
      WHEN poc.moneda = 'PEN' AND poc.tipocambio > 0 THEN ROUND(poc.amortizacion / poc.tipocambio, 2)
      ELSE poc.amortizacion 
    END AS 'valordolares'
  FROM
    pagosOC poc
  INNER JOIN ordenescompra oc ON poc.idorden = oc.idordencompra
  INNER JOIN tiendas t ON oc.idtienda = t.idtienda
  INNER JOIN concesionarios con ON t.idconcesionario = con.idconcesionario
  INNER JOIN entidadespago edp ON poc.identidadpago = edp.identidadpago
  WHERE
    oc.estado = 'proceso'
    AND con.idconcesionario = concesionario_id
  ORDER BY
    oc.idordencompra DESC, poc.fecharealpago ASC;
    
  -- 3. Detalle de Vehículos (Tercer conjunto de resultados)
  SELECT
    oc.idordencompra,
    CONCAT(oc.serie, '-', LPAD(oc.idordencompra, 5, '0')) AS 'OCIdentificador',
    DATE_FORMAT(oc.emision, '%d-%m-%Y') AS 'fechaemision',
    CONCAT(mar.marca, ' / ', modl.modelo) AS 'marcaymodelo',
    CONCAT(
      tp.tipovehiculo, ' / ',
      v.version, ' / ',
      com.combustible, ' / ',
      v.color
    ) AS 'caracteristicas',
    v.chasis AS 'chasis',
    v.placa AS 'placa',
    v.placarotativa AS 'placarotativa',
    v.seriemotor AS 'seriemotor',
    modl.anio AS 'Anio',
    v.condicion AS 'condicion',
    doc.preciocompra AS 'preciocompra',
    (SELECT SUM(sub_doc.preciocompra * 1.18) FROM detordencompra sub_doc WHERE sub_doc.idordencompra = oc.idordencompra) AS 'totalOC'
  FROM
    ordenescompra oc
    JOIN tiendas t ON oc.idtienda = t.idtienda
    JOIN concesionarios con ON t.idconcesionario = con.idconcesionario
    JOIN detordencompra doc ON oc.idordencompra = doc.idordencompra
    JOIN vehiculos v ON doc.idvehiculo = v.idvehiculo
    JOIN modelos modl ON v.idmodelo = modl.idmodelo
    JOIN marcas mar ON modl.idmarca = mar.idmarca
    JOIN tipovehiculos tp ON modl.idtipovehiculo = tp.idtipovehiculo
    JOIN combustibles com ON v.idcombustible = com.idcombustible
  WHERE
    oc.estado = 'proceso'
    AND con.idconcesionario = concesionario_id
  ORDER BY
    oc.idordencompra DESC;

END //
DELIMITER ;
CALL sp_reporte_concesionario_detallado(25);
