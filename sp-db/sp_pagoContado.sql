USE motorpark;

SELECT * FROM conceptosPAGO;

-- BUSCAR CLIENTE POR DNI EN EL SISTEMA
SELECT c.idcliente,
CONCAT(p.apellidos, ' ' ,p.nombres) AS cliente,
p.nrodoc,
p.telprimario 
FROM clientes c
INNER JOIN personas p ON p.idpersona = c.idpersona
WHERE p.nrodoc = :dni AND c.tipocliente = 'P'
LIMIT 1;


-- VEHICULOS LIBRES O EN PROCESO

DROP PROCEDURE sp_buscar_vehiculos;

-- DELIMITER $$
-- CREATE PROCEDURE sp_buscar_vehiculos(IN p_busqueda VARCHAR(100))
-- BEGIN
--     SELECT 
--         v.idvehiculo,
--         CONCAT(ma.marca, ' / ',tv.tipovehiculo, ' / ', m.modelo, ' / ',v.version, ' / ',comb.combustible ,' / ', m.anio, ' / ', v.color, ' / ', v.condicion) AS nombre,
--         ma.marca,
--         m.modelo,
--         v.condicion,
--         v.version,
--         comb.combustible,
--         tv.tipovehiculo,
--         m.anio,
--         v.color,
--         v.disponibilidad,
--         v.precioventa
--     FROM vehiculos v
--     INNER JOIN modelos m ON v.idmodelo = m.idmodelo
--     INNER JOIN tipovehiculos tv ON m.idtipovehiculo = tv.idtipovehiculo
--     INNER JOIN marcas ma ON m.idmarca = ma.idmarca
--     INNER JOIN combustibles comb ON v.idcombustible = comb.idcombustible
--     WHERE v.disponibilidad IN ('libre', 'proceso')
--       AND v.precioventa IS NOT NULL 
--       AND v.precioventa > 0
--       AND v.color IS NOT NULL 
--       AND v.color <> ''
--       AND (
--           ma.marca COLLATE utf8mb4_general_ci LIKE CONCAT('%', p_busqueda, '%')
--           OR m.modelo COLLATE utf8mb4_general_ci LIKE CONCAT('%', p_busqueda, '%')
--           OR v.color COLLATE utf8mb4_general_ci LIKE CONCAT('%', p_busqueda, '%')
--           OR CONCAT(ma.marca, ' ', m.modelo) COLLATE utf8mb4_general_ci LIKE CONCAT('%', p_busqueda, '%')
--       )
--     ORDER BY ma.marca, m.modelo;

-- END$$

-- DELIMITER ;

DROP PROCEDURE IF EXISTS sp_buscar_vehiculos;

DELIMITER $$
CREATE PROCEDURE sp_buscar_vehiculos(IN p_busqueda VARCHAR(255))
BEGIN
    DECLARE busqueda_limpia VARCHAR(255);
    SET busqueda_limpia = TRIM(IFNULL(p_busqueda, ''));

    SELECT 
        v.idvehiculo,
        CONCAT(ma.marca, ' / ',tv.tipovehiculo, ' / ', m.modelo, ' / ',v.version, ' / ',comb.combustible ,' / ', m.anio, ' / ', v.color, ' / ', v.condicion) AS nombre,
        ma.marca,
        m.modelo,
        v.condicion,
        v.version,
        comb.combustible,
        tv.tipovehiculo,
        m.anio,
        v.color,
        v.disponibilidad,
        v.precioventa
    FROM vehiculos v
    INNER JOIN modelos m ON v.idmodelo = m.idmodelo
    INNER JOIN tipovehiculos tv ON m.idtipovehiculo = tv.idtipovehiculo
    INNER JOIN marcas ma ON m.idmarca = ma.idmarca
    INNER JOIN combustibles comb ON v.idcombustible = comb.idcombustible
    WHERE 
        v.disponibilidad IN ('libre', 'proceso')
        AND v.precioventa IS NOT NULL AND v.precioventa > 0
        AND v.color IS NOT NULL AND v.color <> ''
        AND busqueda_limpia <> '' 
        AND CONCAT_WS(' ', ma.marca, m.modelo, v.version, comb.combustible, tv.tipovehiculo, m.anio, v.color, v.condicion) 
            COLLATE utf8mb4_general_ci LIKE CONCAT('%', REPLACE(busqueda_limpia, ' ', '%'), '%')
            
    ORDER BY ma.marca, m.modelo;

END$$
DELIMITER ;
CALL sp_buscar_vehiculos('kia rojo');



DELIMITER //
CREATE PROCEDURE sp_registrarVentaContado(
    IN p_idcliente INT,
    IN p_idconcepto INT,
    IN p_idvehiculo INT,
    IN p_idasesorvendedor INT,
    IN p_idcuentapago INT,
    IN p_mediopago VARCHAR(80),
    IN p_numerotransaccion VARCHAR(30),
    IN p_fechapago DATE,
    IN p_amortizacion DECIMAL(10,2),
    IN p_montomonedaoriginal DECIMAL(10,2),
    IN p_tipocambioaplicado DECIMAL(10,4),
    IN p_moneda ENUM('USD','PEN'),
    IN p_comprobante VARCHAR(200),
    IN p_observacion VARCHAR(300)
)
BEGIN
    DECLARE v_disponibilidad VARCHAR(50);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SELECT disponibilidad INTO v_disponibilidad
    FROM vehiculos
    WHERE idvehiculo = p_idvehiculo
    FOR UPDATE;
    IF v_disponibilidad IN ('libre', 'proceso') THEN

        INSERT INTO pagos(
            idcliente, idconcepto, idvehiculo, idasesorvendedor, idcuentapago,
            mediopago, numerotransaccion, fechapago, amortizacion,
            montomonedaoriginal, tipocambioaplicado, saldorestante, moneda,
            comprobante, observacion, tipo
        ) VALUES (
            p_idcliente, p_idconcepto, p_idvehiculo, p_idasesorvendedor, p_idcuentapago,
            p_mediopago, p_numerotransaccion, p_fechapago, p_amortizacion,
            p_montomonedaoriginal, p_tipocambioaplicado, 0.00,
            p_moneda, p_comprobante, p_observacion, 'Otro'
        );

        UPDATE vehiculos
        SET disponibilidad = 'vendido'
        WHERE idvehiculo = p_idvehiculo;

        SELECT LAST_INSERT_ID() AS idpago;

    ELSE
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El vehículo ya no está disponible para la venta.';
    END IF;

    COMMIT;

END //
DELIMITER ;



-- DROP PROCEDURE sp_vehiculosVendidoAlContado;
DELIMITER //
CREATE PROCEDURE sp_vehiculosVendidoAlContado()
BEGIN
    SELECT
        v.idvehiculo,
        CONCAT(ma.marca, ' / ',tv.tipovehiculo, ' / ', m.modelo, ' / ',v.version, ' / ',comb.combustible ,' / ', m.anio, ' / ', v.color, ' / ', v.condicion) AS vehiculo,
        CONCAT(per.apellidos, ' ', per.nombres) AS cliente,
        CONCAT(dep.departamento, ' / ', pro.provincia, ' / ', dist.distrito) AS ubicacion,
        per.telprimario,
        per.direccion,
        DATE_FORMAT(p.fechapago,'%d-%m-%Y') AS fechapago,
        CASE 
            WHEN p.moneda = 'USD' THEN p.montomonedaoriginal
            ELSE p.amortizacion 
        END AS amortizacion,
        p.moneda
    FROM
        vehiculos AS v
    INNER JOIN
        pagos AS p ON v.idvehiculo = p.idvehiculo
    INNER JOIN 
        clientes AS c ON p.idcliente = c.idcliente
    INNER JOIN 
        personas AS per ON c.idpersona = per.idpersona
    INNER JOIN 
        conceptospago con ON p.idconcepto = con.idconcepto
    INNER JOIN 
        distritos dist ON per.iddistrito = dist.iddistrito
    INNER JOIN 
        provincias pro ON dist.idprovincia = pro.idprovincia
    INNER JOIN 
        departamentos dep ON pro.iddepartamento = dep.iddepartamento 
    INNER JOIN 
        modelos m ON v.idmodelo = m.idmodelo
    INNER JOIN 
        tipovehiculos tv ON m.idtipovehiculo = tv.idtipovehiculo
    INNER JOIN 
        marcas ma ON m.idmarca = ma.idmarca
    INNER JOIN 
        combustibles comb ON v.idcombustible = comb.idcombustible
    WHERE
        v.disponibilidad = 'vendido'
        AND con.concepto = 'Contado'
    ORDER BY 
        p.idpago DESC;
END //

DELIMITER ;
CALL sp_vehiculosVendidoAlContado();


SELECT * FROM cotizaciones;

SELECT * FROM colaboradores;
SELECT * FROM contratoslaborales;