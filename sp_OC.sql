
USE motorpark;
DELIMITER $$
DROP PROCEDURE sp_oc_por_estado;
CREATE PROCEDURE sp_oc_por_estado(IN p_estado VARCHAR(50))
BEGIN
    SELECT 
        oc.idordencompra,
        oc.serie,
         DATE_FORMAT(oc.emision, '%d-%m-%Y') AS emision, 
        oc.moneda,
        con.razonsocial,

        (
            SELECT IFNULL(SUM(preciocompra * 1.18),0) 
            FROM detordencompra 
            WHERE idordencompra = oc.idordencompra
        ) AS totalOC,

        (
            SELECT IFNULL(SUM(amortizacion),0) 
            FROM pagosOC 
            WHERE idorden = oc.idordencompra
        ) AS totalPagado,

        (
            (SELECT IFNULL(SUM(preciocompra * 1.18),0) 
             FROM detordencompra 
             WHERE idordencompra = oc.idordencompra)
            -
            (SELECT IFNULL(SUM(amortizacion),0) 
             FROM pagosOC 
             WHERE idorden = oc.idordencompra)
        ) AS saldoRestante

    FROM ordenescompra oc
    JOIN tiendas t ON oc.idtienda = t.idtienda
    JOIN concesionarios con ON t.idconcesionario = con.idconcesionario
    WHERE oc.estado = CONVERT(p_estado USING utf8mb4) COLLATE utf8mb4_general_ci
    ORDER BY oc.idordencompra DESC;
END $$

DELIMITER ;

CALL sp_oc_por_estado('proceso');

-- UPDATE concesionarios SET razonsocial = 'HYUNDAI PERU' WHERE idconcesionario = 7;

SELECT * FROM concesionarios;
--SP PARA OBTENER LOS OC QUE SON COMPRAS.
USE motorpark;
DROP PROCEDURE sp_getAll_OC_Compras;

SELECT * FROM contratos;
SELECT * FROM cronogramas;
SELECT * FROM cotizaciones;
SHOW COLUMNS FROM cotizaciones;

DELIMITER //

CREATE PROCEDURE sp_getAll_OC_Compras()
BEGIN
    SELECT
        orden.idordencompra,
        com.idcompra,
        orden.serie AS serie_oc,
        orden.emision AS fecha_emision_oc,
        com.fechacompra,
        com.numdocumento AS num_factura,
        concesionario.nombrecomercial,
        CONCAT_WS(' / ',dep.departamento,prov.provincia,dist.distrito,  tienda.direccion) AS direccion_completa_concesionario,
        COUNT(CASE 
            WHEN veh.disponibilidad = 'proceso' 
            AND (veh.chasis IS NULL OR TRIM(veh.chasis) = '' OR veh.seriemotor IS NULL OR TRIM(veh.seriemotor) = '')
            THEN 1 
            ELSE NULL 
        END) AS vehiculos_pendientes,
        COUNT(
  CASE
    WHEN veh.disponibilidad = 'proceso'
     AND veh.chasis IS NOT NULL AND TRIM(veh.chasis) <> ''
     AND veh.seriemotor IS NOT NULL AND TRIM(veh.seriemotor) <> ''
    THEN 1
    ELSE NULL
  END
) AS listos_para_liberar

    FROM
        compras AS com
    INNER JOIN
        ordenescompra AS orden ON com.idorden = orden.idordencompra
    INNER JOIN
        tiendas AS tienda ON orden.idtienda = tienda.idtienda
    INNER JOIN
        concesionarios AS concesionario ON tienda.idconcesionario = concesionario.idconcesionario
    INNER JOIN
        distritos AS dist ON tienda.iddistrito = dist.iddistrito
    INNER JOIN
        provincias AS prov ON dist.idprovincia = prov.idprovincia
    INNER JOIN
        departamentos AS dep ON prov.iddepartamento = dep.iddepartamento
    INNER JOIN
        detordencompra AS detoc ON orden.idordencompra = detoc.idordencompra
    INNER JOIN
        vehiculos AS veh ON detoc.idvehiculo = veh.idvehiculo
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
    ORDER BY
        com.fechacompra DESC;
END //

DELIMITER ;

-- DELIMITER //

-- CREATE PROCEDURE sp_getAll_OC_Compras()
-- BEGIN
--     SELECT 
--         orden.idordencompra,
--         com.idcompra,
--         orden.serie AS serie_oc,
--         orden.emision AS fecha_emision_oc,
--         com.fechacompra,
--         com.numdocumento AS num_factura,
--         concesionario.nombrecomercial,
        
--         CONCAT_WS(', ', tienda.direccion, dist.distrito, prov.provincia, dep.departamento) AS direccion_completa_concesionario,
--         (SELECT COUNT(iddetordencompra) FROM detordencompra WHERE idordencompra = orden.idordencompra AND estado = '1') AS cantidadvehiculos
--     FROM compras com
--     INNER JOIN ordenescompra orden ON com.idorden = orden.idordencompra
--     INNER JOIN tiendas tienda ON orden.idtienda = tienda.idtienda
--     INNER JOIN concesionarios concesionario ON tienda.idconcesionario = concesionario.idconcesionario
--     INNER JOIN distritos dist ON tienda.iddistrito = dist.iddistrito
--     INNER JOIN provincias prov ON dist.idprovincia = prov.idprovincia
--     INNER JOIN departamentos dep ON prov.iddepartamento = dep.iddepartamento
--     ORDER BY com.fechacompra DESC; 
-- END //

-- DELIMITER ;
--  CALL sp_getAll_OC_Compras();



 SELECT * FROM compras;



USE motorpark;

DROP PROCEDURE sp_get_OC_details_for_recepcion;
DELIMITER //
CREATE PROCEDURE sp_get_OC_details_for_recepcion(IN p_idcompra INT)
BEGIN
  DECLARE v_idorden INT;
  DECLARE v_total_costo DECIMAL(10, 2);
  DECLARE v_total_amortizado DECIMAL(10, 2);

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

  -- Obtener el total amortizado para la OC
  SELECT
    IFNULL(SUM(pagos.amortizacion), 0)
  INTO
    v_total_amortizado
  FROM
    pagosOC AS pagos
  WHERE
    pagos.idorden = v_idorden;

  -- Primer conjunto de resultados: Información del Concesionario y datos de la OC
  SELECT
    concesionario.razonsocial AS razonsocial_concesionario,
    com.fechacompra,
    v_total_amortizado AS total_amortizado,
    v_total_costo * 1.18 AS totalcompra,
    (v_total_costo * 1.18) - v_total_amortizado AS saldopendiente
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

DELIMITER ;


CALL sp_get_OC_details_for_recepcion(13);

-- DELIMITER //

-- CREATE PROCEDURE sp_get_OC_details_for_recepcion(IN p_idcompra INT)
-- BEGIN
--     -- Primer conjunto de resultados: Información del Concesionario y Datos de la OC
--     SELECT
--         concesionario.razonsocial AS razonsocial_concesionario,
--         com.fechacompra,
--         IFNULL(SUM(pagos.amortizacion), 0) AS total_amortizado,
        
--         (SELECT IFNULL(SUM(doc.preciocompra), 0) FROM detordencompra doc WHERE doc.idordencompra = orden.idordencompra AND doc.estado = '1') * 1.18 AS totalcompra,
       
--         ( (SELECT IFNULL(SUM(doc.preciocompra), 0) FROM detordencompra doc WHERE doc.idordencompra = orden.idordencompra AND doc.estado = '1') * 1.18 ) - IFNULL(SUM(pagos.amortizacion), 0) AS saldopendiente
--     FROM compras com
--     INNER JOIN ordenescompra orden ON com.idorden = orden.idordencompra
--     INNER JOIN tiendas tienda ON orden.idtienda = tienda.idtienda
--     INNER JOIN concesionarios concesionario ON tienda.idconcesionario = concesionario.idconcesionario
--     LEFT JOIN pagosOC pagos ON orden.idordencompra = pagos.idorden
--     WHERE com.idcompra = p_idcompra
--     GROUP BY
--         com.idcompra;

--     -- Segundo conjunto de resultados: Listado de Vehículos
--     SELECT
--         veh.idlocal,
--         veh.idvehiculo,
--         modelo.modelo,
--         veh.version,
--         comb.combustible,
--         modelo.anio,
--         veh.chasis,
--         veh.placa,
--         veh.placarotativa,
--         veh.seriemotor,
--         veh.color
--     FROM detordencompra detoc
--     INNER JOIN compras com ON detoc.idordencompra = com.idorden
--     INNER JOIN vehiculos veh ON detoc.idvehiculo = veh.idvehiculo
--     INNER JOIN modelos modelo ON veh.idmodelo = modelo.idmodelo
--     INNER JOIN combustibles comb ON veh.idcombustible = comb.idcombustible
--     WHERE com.idcompra = p_idcompra AND detoc.estado = '1' AND veh.disponibilidad = 'proceso' AND veh.origen = 'OCP';
-- END //

-- DELIMITER ;
CALL sp_get_OC_details_for_recepcion(12)


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


SELECT * FROM vehiculos;

SELECT * FROM vehiculos WHERE idvehiculo = 63;