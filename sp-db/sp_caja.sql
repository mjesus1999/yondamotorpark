-- USE motorpark; -- hosting: seleccionar BD en phpMyAdmin

DROP PROCEDURE IF EXISTS sp_getAll_contratos_caja;
DELIMITER $$
CREATE PROCEDURE sp_getAll_contratos_caja()
BEGIN 
     SELECT
                    con.idcontrato,
                    CONCAT(p.apellidos, ' ', p.nombres) AS cliente,
                    p.tipodoc AS documento,
                    p.nrodoc AS ndocumento,
				CONCAT(
                l.tienda, ' / ',
                dep.departamento,' / ',
                d.distrito, ' / ',
                pro.provincia) AS tienda,
                    CONCAT(
                        IFNULL(mar.marca, 'Sin marca'), ' / ',
                        IFNULL(model.modelo, 'Sin modelo'),
                        ' / ',
                        IFNULL(c.combustible, 'Sin combustible'),
                        ' / ',
                        IFNULL(v.color, 'Sin color')
                    ) AS vehiculo,

                    cot.numcuotas AS meses,
                    cot.valorcuota AS cuota
                FROM
                    cotizaciones AS cot
                JOIN
                    clientes AS cli ON cot.idcliente = cli.idcliente
                JOIN
                    personas AS p ON cli.idpersona = p.idpersona
                JOIN
                    vehiculos AS v ON cot.idvehiculo = v.idvehiculo
                JOIN
                    modelos AS model ON v.idmodelo = model.idmodelo
                JOIN
                    marcas AS mar ON model.idmarca = mar.idmarca
                JOIN
                    combustibles AS c ON v.idcombustible = c.idcombustible
                LEFT JOIN
                    contratos AS con ON cot.idcotizacion = con.idcotizacion
                LEFT JOIN
                    locales AS l ON con.idlocal = l.idlocal
				JOIN distritos AS d ON l.iddistrito = d.iddistrito
                JOIN provincias AS pro ON d.idprovincia = pro.idprovincia
                JOIN departamentos AS dep ON pro.iddepartamento = dep.iddepartamento
                WHERE con.estado  = 'ACT'
                ORDER BY con.idcontrato DESC;
                    
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS sp_get_cronogramas_by_idcontrato;

DELIMITER $$
CREATE PROCEDURE sp_get_cronogramas_by_idcontrato(IN idcontrato_ INT)
BEGIN
    -- Primero actualizamos las cuotas vencidas
    UPDATE cronogramas cro
    INNER JOIN contratos cont ON cro.idcontrato = cont.idcontrato
    INNER JOIN cotizaciones coti ON cont.idcotizacion = coti.idcotizacion
    SET 
        cro.estado = 'Vencido',
        cro.aplicapenalidad = 'S',
        cro.penalidad = coti.valorcuota * cont.penalidadbase
    WHERE cro.fechapago < DATE_SUB(CURDATE(), INTERVAL 3 DAY)
      AND cro.estado != 'Pagado'
      AND cont.idcontrato = idcontrato_;

    -- Luego seleccionamos los datos
    SELECT 
        cro.idcronograma,
        cont.idcontrato,
        cro.numcuota,
        cro.fechapago,
        cro.interes,
        cro.abonocapital,
        coti.valorcuota, 
        cro.penalidad,
        cro.saldocapital,

        COALESCE((
            SELECT SUM(pag.amortizacion)
            FROM pagos pag
            WHERE pag.idcronograma = cro.idcronograma
        ), 0) AS amortizacion,

        (coti.valorcuota + cro.penalidad) - COALESCE((
            SELECT SUM(pag.amortizacion)
            FROM pagos pag
            WHERE pag.idcronograma = cro.idcronograma
        ), 0) AS saldorestante,


        coti.valorcuota - COALESCE((
            SELECT SUM(pag.amortizacion)
            FROM pagos pag
            WHERE pag.idcronograma = cro.idcronograma
            AND pag.tipo = 'Cuota'
        ), 0) AS saldocuota_pendiente,

        --   CÁLCULO DEL SALDO PENDIENTE DE LA PENALIDAD
        cro.penalidad - COALESCE((
            SELECT SUM(pag.amortizacion)
            FROM pagos pag
            WHERE pag.idcronograma = cro.idcronograma
            AND pag.tipo = 'Penalidad'
        ), 0) AS penalidad_pendiente,

        cro.estado,
        cro.aplicapenalidad

    FROM cronogramas cro
    INNER JOIN contratos cont ON cro.idcontrato = cont.idcontrato
    INNER JOIN cotizaciones coti ON cont.idcotizacion = coti.idcotizacion
    WHERE cont.idcontrato = idcontrato_
    ORDER BY cro.numcuota;
END$$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_addPagoCronograma;

DELIMITER $$

CREATE PROCEDURE sp_addPagoCronograma(
    IN idcronograma_ INT,
    IN idcuentapago_ INT,
    IN idcolcaja_ INT,
    IN mediopago_ VARCHAR(100),
    IN numerotransaccion_ VARCHAR(30),
    IN fechapago_ DATE,
    IN amortizacion_ DECIMAL(10,2),
    IN comprobante_ VARCHAR(200),
    IN observacion_ VARCHAR(300),
    IN tipo_ ENUM('Cuota','Penalidad')
)
BEGIN
    INSERT INTO pagos(
        idcronograma,
        idcuentapago,
        idcolcaja,
        mediopago,
        numerotransaccion,
        fechapago,
        amortizacion,
        comprobante,
        observacion,
        tipo 
    ) VALUES (
        idcronograma_,
        IFNULL(NULLIF(idcuentapago_,''),NULL),
        idcolcaja_,
        mediopago_,
        IFNULL(NULLIF(numerotransaccion_, ''), NULL),
        fechapago_,
        amortizacion_,
        comprobante_, 
        IFNULL(NULLIF(observacion_, ''), NULL),
        tipo_ 
    );

    SELECT LAST_INSERT_ID() AS last_insert_id;
END$$

DELIMITER;

DROP PROCEDURE IF EXISTS sp_get_pagos_by_contrato;
DELIMITER $$

CREATE PROCEDURE sp_get_pagos_by_contrato(IN p_idcontrato INT)
BEGIN
    SELECT 
        p.idpago,
        c.idcontrato,
        p.idcronograma,
        c.numcuota,
      DATE_FORMAT(c.fechapago,'%d/%m/%Y') AS fecha_vencimiento,
      DATE_FORMAT(p.fechapago,'%d/%m/%Y') AS fecha_pago,

        p.amortizacion,
        p.saldorestante,
        p.mediopago,
        p.numerotransaccion,
        p.comprobante,
        p.enlace_pdf_nubefact,
        p.numero_boleta_sunat,
        p.comprobante_serie,
        p.comprobante_tipo_nubefact,
        p.tipo,
        p.observacion
    FROM pagos p
    INNER JOIN cronogramas c ON p.idcronograma = c.idcronograma
    WHERE c.idcontrato = p_idcontrato
    ORDER BY c.numcuota, p.fechapago;
END$$

DELIMITER ;

DROP PROCEDURE IF EXISTS sp_getClienteBy_DNI;


DELIMITER //
CREATE PROCEDURE sp_getClienteBy_DNI(
    IN dni_ VARCHAR(11)
)
BEGIN
    IF CHAR_LENGTH(TRIM(dni_)) = 8 THEN
        SELECT
            cl.idcliente,
            CONCAT(p.apellidos, ' ' COLLATE utf8mb4_general_ci, p.nombres) AS cliente,
            p.nrodoc,
            p.direccion,
            p.email
        FROM clientes cl
            INNER JOIN personas p ON cl.idpersona = p.idpersona
        WHERE p.nrodoc = TRIM(dni_) COLLATE utf8mb4_general_ci
          AND cl.tipocliente = 'P' COLLATE utf8mb4_unicode_ci
          AND cl.estado = 'ACT' COLLATE utf8mb4_unicode_ci
        LIMIT 1;
    ELSEIF CHAR_LENGTH(TRIM(dni_)) = 11 THEN
        SELECT
            cl.idcliente,
            e.razonsocial AS cliente,
            e.ruc AS nrodoc,
            e.direccion,
            e.email
        FROM clientes cl
            INNER JOIN empresas e ON cl.idempresa = e.idempresa
        WHERE e.ruc = TRIM(dni_) COLLATE utf8mb4_general_ci
          AND cl.tipocliente = 'E' COLLATE utf8mb4_unicode_ci
          AND cl.estado = 'ACT' COLLATE utf8mb4_unicode_ci
        LIMIT 1;
    END IF;
END //
DELIMITER ;
-- CALL sp_getClienteBy_DNI('71882015');
-- CALL sp_getClienteBy_DNI('20123456789');




DROP PROCEDURE IF EXISTS sp_registrar_pago_compuesto;

DELIMITER $$

CREATE PROCEDURE sp_registrar_pago_compuesto(

    IN p_idcliente INT,

    IN p_idcolcaja INT,

    IN p_mediopago VARCHAR(50),

    IN p_numerotransaccion VARCHAR(30),

    IN p_idcuentapago INT,

    IN p_amortizacion DECIMAL(10, 2),

    IN p_detalles_json TEXT

)
BEGIN

    DECLARE v_idpago INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION

    BEGIN

        ROLLBACK;

        RESIGNAL;

    END;



    START TRANSACTION;



    -- 1. Insertar Cabecera (PAGOS)

    INSERT INTO pagos (

        idcliente, idcolcaja, mediopago, numerotransaccion, idcuentapago, 

        fechapago, amortizacion, tipo, 

        facturado, declarado -- Por defecto S y N

    ) VALUES (

        p_idcliente, p_idcolcaja, p_mediopago, p_numerotransaccion, p_idcuentapago,

        CURDATE(), p_amortizacion, 'Otro', 
        'S', 'N' -- Se asume que se intentará facturar inmediatamente
    );



    SET v_idpago = LAST_INSERT_ID();



    -- 2. Insertar Detalles (DETPAGOS) usando JSON_TABLE

    -- El JSON se espera con estructura: [{"idconcepto": 1, "monto": 50.00, "nombre": "...", "obs": "..."}]

    INSERT INTO detpagos (

        idpago, idconcepto, monto_detalle, nombre_concepto_manual, observacion_detalle

    )

    SELECT 

        v_idpago,

        jt.idconcepto,

        jt.monto_detalle,

        jt.nombre_manual,

        jt.observacion

    FROM JSON_TABLE(p_detalles_json, '$[*]' COLUMNS (

        idconcepto INT PATH '$.idconcepto',

        monto_detalle DECIMAL(10,2) PATH '$.monto',

        nombre_manual VARCHAR(150) PATH '$.nombre',

        observacion VARCHAR(300) PATH '$.obs'

    )) AS jt;



    COMMIT;
    -- Devolver ID para PHP

    SELECT v_idpago AS id_pago_generado;

END$$

DELIMITER ;

DROP PROCEDURE IF EXISTS sp_get_contratos_completados;

DELIMITER $$
CREATE PROCEDURE sp_get_contratos_completados()
BEGIN
    SELECT
        con.idcontrato,
        CONCAT(p.apellidos, ' ', p.nombres) AS cliente,
        p.tipodoc AS documento,
        p.nrodoc AS ndocumento,
        p.telprimario,
        CONCAT(
            IFNULL(dep.departamento, 'Sin departamento'), ' / ',
            IFNULL(pro.provincia, 'Sin provincia'), ' / ',
            IFNULL(d.distrito, 'Sin distrito')
        ) AS ubigeo,
        IFNULL(p.direccion, 'Sin dirección') AS direccion,
        CONCAT(
            l.tienda, ' / ',
            dep.departamento,' / ',
            d.distrito, ' / ',
            pro.provincia
        ) AS tienda,
        CONCAT(
            IFNULL(mar.marca, 'Sin marca'), ' / ',
            IFNULL(model.modelo, 'Sin modelo'), ' / ',
            IFNULL(c.combustible, 'Sin combustible'), ' / ',
            IFNULL(v.color, 'Sin color')
        ) AS vehiculo,
        cot.numcuotas AS meses,
        cot.valorcuota AS cuota,
        SUM(cot.valorcuota) AS totalPagado
    FROM cotizaciones AS cot
    JOIN clientes AS cli ON cot.idcliente = cli.idcliente
    JOIN personas AS p ON cli.idpersona = p.idpersona
    JOIN vehiculos AS v ON cot.idvehiculo = v.idvehiculo
    JOIN modelos AS model ON v.idmodelo = model.idmodelo
    JOIN marcas AS mar ON model.idmarca = mar.idmarca
    JOIN combustibles AS c ON v.idcombustible = c.idcombustible
    LEFT JOIN contratos AS con ON cot.idcotizacion = con.idcotizacion
    LEFT JOIN locales AS l ON con.idlocal = l.idlocal
    JOIN distritos AS d ON l.iddistrito = d.iddistrito
    JOIN provincias AS pro ON d.idprovincia = pro.idprovincia
    JOIN departamentos AS dep ON pro.iddepartamento = dep.iddepartamento
    JOIN cronogramas cro ON con.idcontrato = cro.idcontrato
    WHERE cro.estado = 'Pagado'
      AND con.estado = 'FIN'
    GROUP BY
        con.idcontrato,
        p.apellidos, p.nombres,
        p.tipodoc, p.nrodoc,
        l.tienda,
        dep.departamento,
        d.distrito,
        pro.provincia,
        mar.marca,
        model.modelo,
        c.combustible,
        v.color,
        cot.numcuotas,
        cot.valorcuota
    ORDER BY con.idcontrato ASC;
END $$

DELIMITER ;