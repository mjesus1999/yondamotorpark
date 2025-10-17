
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
        v.disponibilidad IN ('libre')
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




SELECT * FROM vehiculos;

SELECT  precioventa FROM vehiculos WHERE idvehiculo = 186;

SELECT * FROM pagos;

SELECT * FROM pagos WHERE idconcepto = 1; 

SELECT * FROM conceptospago;

SELECT * FROM pagoS where idvehiculo = 114;



DELIMITER //
CREATE PROCEDURE sp_pagoInicial(
    IN idconcepto_ INT,
    IN idcotizacion_ INT,
    IN idvehiculo_ INT,
    IN idcuentapago_ INT,
    IN idcolcaja_ INT,
    IN mediopago_ VARCHAR(80),
    IN numerotransaccion_ VARCHAR(30),
    IN fechapago_ DATE,
    IN amortizacion_ DECIMAL(10,2),
    IN saldorestante_ DECIMAL(10,2),
    IN comprobante_ VARCHAR(200),
    IN observacion_ VARCHAR(300),
    IN moneda_ ENUM('USD', 'PEN'),
    IN montomonedaoriginal_ DECIMAL(10,2),
    IN tipocambioaplicado_ DECIMAL(10,4)
)
BEGIN
    DECLARE v_disponibilidad VARCHAR(20);
    DECLARE v_idpago BIGINT DEFAULT 0;
    DECLARE v_cotizacion_pago INT;

    START TRANSACTION;
    -- Bloquear el vehículo para evitar condiciones de carrera
    SELECT disponibilidad
    INTO v_disponibilidad
    FROM vehiculos
    WHERE idvehiculo = idvehiculo_
    FOR UPDATE;
    -- Validar que el vehículo no esté vendido
    IF v_disponibilidad = 'vendido' THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'No se puede registrar el pago inicial: el vehículo ya fue vendido';
    END IF;

    -- Buscar si ya existe un pago inicial para este vehículo
    SELECT p.idcotizacion
    INTO v_cotizacion_pago
    FROM pagos p
    JOIN conceptospago cp ON cp.idconcepto = p.idconcepto
    WHERE p.idvehiculo = idvehiculo_
      AND cp.concepto = 'Inicial'
    ORDER BY p.idpago ASC
    LIMIT 1;

    -- Validar que no esté separado/vendido por otra cotización
    IF v_cotizacion_pago IS NOT NULL AND v_cotizacion_pago <> idcotizacion_ THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'El vehículo ya fue separado o vendido';
    END IF;

    -- Insertar el nuevo pago
    INSERT INTO pagos(
        idconcepto, idcotizacion, idvehiculo, idcuentapago, idcolcaja,
        mediopago, numerotransaccion, fechapago, amortizacion, saldorestante,
        comprobante, observacion, tipo,
        moneda, montomonedaoriginal, tipocambioaplicado
    )
    VALUES(
        idconcepto_, idcotizacion_, idvehiculo_, 
        IF(idcuentapago_ = 0, NULL, idcuentapago_), 
        idcolcaja_, mediopago_, numerotransaccion_, fechapago_, 
        amortizacion_, saldorestante_, comprobante_, observacion_, 
        'Otro', moneda_, montomonedaoriginal_, tipocambioaplicado_
    );

    SET v_idpago = LAST_INSERT_ID();

    -- Actualizar estado de cotización a 'S' (Separado)
    UPDATE cotizaciones
    SET estadocotizacion = 'S'
    WHERE idcotizacion = idcotizacion_ AND estadocotizacion = 'P';

    -- Si el vehículo está disponible, marcarlo como separado
    IF v_disponibilidad <> 'separado' AND v_disponibilidad <> 'vendido' THEN
        UPDATE vehiculos
        SET disponibilidad = 'separado', modificado = NOW()
        WHERE idvehiculo = idvehiculo_;
    END IF;

    COMMIT;

    SELECT v_idpago AS idpago;
END //
DELIMITER ;







DELIMITER // 
CREATE PROCEDURE sp_getDataVentaVehiculoContado(
    IN p_idvehiculo INT
)
BEGIN

         SELECT 
              v.idvehiculo,
              CONCAT(per.apellidos, ' ', per.nombres) AS cliente,
              per.nrodoc,
              per.telprimario,
              CONCAT(per.direccion, ' - ', dist.distrito, ' - ', pro.provincia, ' - ', dep.departamento) AS ubicacion,
              mar.marca,
              m.modelo,
              v.chasis,
              v.seriemotor,
              m.anio,
              v.color
          FROM vehiculos v
          INNER JOIN pagos p 
              ON v.idvehiculo = p.idvehiculo
          INNER JOIN conceptospago cp 
              ON p.idconcepto = cp.idconcepto
          INNER JOIN clientes cl 
              ON p.idcliente = cl.idcliente
          INNER JOIN personas per 
              ON cl.idpersona = per.idpersona
          INNER JOIN distritos dist 
              ON per.iddistrito = dist.iddistrito
          INNER JOIN provincias pro 
              ON dist.idprovincia = pro.idprovincia
          INNER JOIN departamentos dep 
              ON pro.iddepartamento = dep.iddepartamento
          INNER JOIN modelos m 
              ON v.idmodelo = m.idmodelo
          INNER JOIN marcas mar 
              ON m.idmarca = mar.idmarca
          INNER JOIN tipovehiculos tv 
              ON m.idtipovehiculo = tv.idtipovehiculo
          INNER JOIN combustibles com 
              ON v.idcombustible = com.idcombustible
          WHERE 
              v.disponibilidad = 'vendido'
              AND cp.concepto = 'Contado'
              AND v.idvehiculo = p_idvehiculo;
END //
DELIMITER;

CALL sp_getDataVentaVehiculoContado(186);