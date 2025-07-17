SELECT * FROM detordencompra;
USE motorpark;

DELIMITER //
CREATE FUNCTION fn_format_oc_display_id(
    p_idordencompra INT,
    p_anio CHAR(4)
)
RETURNS VARCHAR(10)
DETERMINISTIC
BEGIN
    -- Rellena el idordencompra con ceros a la izquierda hasta 5 dígitos Y lo concatena con el año.
    RETURN CONCAT(p_anio, '-', LPAD(p_idordencompra, 5, '0'));
END //

-- DROP PROCEDURE IF EXISTS sp_detOC_By_IdOC; 

DELIMITER //
CREATE PROCEDURE sp_detOC_By_IdOC(
    IN p_idordencompra INT
)
BEGIN
    SELECT
        -- Identificador del Detalle de Orden de Compra
        doc.iddetordencompra AS id_detalle_orden,

        -- Datos de la Orden de Compra (OC)
        oc.idordencompra AS numero_oc_interno, -- El ID numérico interno
        oc.serie AS anio_oc,
        -- Genera el número de OC formateado usando la función fn_format_oc_display_id
        fn_format_oc_display_id(oc.idordencompra, oc.serie) AS numero_oc_formateado,
        -- Formato de fecha a 'dd/MM/yyyy'
        DATE_FORMAT(oc.emision, '%d/%m/%Y') AS fecha_emision_oc,
        oc.observaciones AS observaciones_oc,
        oc.moneda AS moneda_oc,

        -- Datos del Concesionario
        con.razonsocial AS concesionario_razon_social,
        con.ruc AS concesionario_ruc,
        -- Ubigeo completo del concesionario en una sola columna (Distrito/Provincia/Departamento)
        CONCAT(dis.distrito, ' / ', prov.provincia, ' / ', dep.departamento) AS concesionario_ubigeo_completo,
        t.direccion AS concesionario_direccion,
        t.telefono AS concesionario_telefono,
        t.contacto AS concesionario_vendedor_contacto,

        -- Datos del Vehículo
        mar.marca AS vehiculo_marca,
        modl.modelo AS vehiculo_modelo,
        veh.version AS vehiculo_version,
        com.combustible AS vehiculo_combustible,
        modl.anio AS vehiculo_anio_modelo,
        -- Identificadores del vehículo en columnas separadas
        veh.placa AS vehiculo_placa,
        veh.placarotativa AS vehiculo_placa_rotativa,
        veh.chasis AS vehiculo_chasis,
        veh.seriemotor AS vehiculo_serie_motor,
        veh.color AS vehiculo_color,
        doc.preciocompra AS vehiculo_precio_unitario,

        -- Cálculos Financieros por vehículo.
        doc.preciocompra AS valor_venta_unitario,
        (doc.preciocompra * 0.18) AS igv_unitario, -- IGV 18%
        (doc.preciocompra * 1.18) AS total_unitario, -- Valor Venta + IGV

        -- Cálculos Financieros Totales para toda la Orden de Compra
        (SELECT SUM(doc2.preciocompra) FROM detordencompra doc2 WHERE doc2.idordencompra = oc.idordencompra) AS total_valor_venta_orden,
        (SELECT SUM(doc2.preciocompra) * 0.18 FROM detordencompra doc2 WHERE doc2.idordencompra = oc.idordencompra) AS total_igv_orden,
        (SELECT SUM(doc2.preciocompra) * 1.18 FROM detordencompra doc2 WHERE doc2.idordencompra = oc.idordencompra) AS total_general_orden

    FROM
        ordenescompra oc
    JOIN
        detordencompra doc ON oc.idordencompra = doc.idordencompra
    JOIN
        vehiculos veh ON doc.idvehiculo = veh.idvehiculo
    JOIN
        modelos modl ON veh.idmodelo = modl.idmodelo
    JOIN
        marcas mar ON modl.idmarca = mar.idmarca
    JOIN
        combustibles com ON veh.idcombustible = com.idcombustible
    JOIN
        tiendas t ON oc.idtienda = t.idtienda
    JOIN
        concesionarios con ON t.idconcesionario = con.idconcesionario
    JOIN
        distritos dis ON t.iddistrito = dis.iddistrito
    JOIN
        provincias prov ON dis.idprovincia = prov.idprovincia
    JOIN
        departamentos dep ON prov.iddepartamento = dep.iddepartamento
    WHERE
        oc.idordencompra = p_idordencompra; 
END //

-- 	SELECT * FROM vehiculos;
--     SELECT * FROM marcas; -- 21
--     SELECT * FROM modelos;
--     INSERT INTO modelos(idtipovehiculo,idmarca,modelo,anio)VALUES(1,21,'Yaris','2025');
--     SELECT * FROM tipovehiculos; -- 1
    
-- CALL sp_detOC_By_IdOC(14);

-- SHOW PROCEDURE STATUS WHERE Db = 'motorpark' AND Name = 'sp_get_reporte_orden_compra';


-- SELECT * FROM ordenescompra;
-- SELECT * FROM detordencompra
-- SELECT * FROM concesionarios;
-- UPDATE concesionarios SET razonsocial = 'Toyota del Perú S.A.' WHERE idconcesionario = 9;

