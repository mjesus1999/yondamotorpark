USE motorpark;
-- Procedimiento almacenado modificado
USE motorpark;

DROP PROCEDURE IF EXISTS sp_get_cronogramas_by_idcontrato;

DELIMITER $$

CREATE PROCEDURE sp_get_cronogramas_by_idcontrato(IN idcontrato_ INT)
BEGIN
    SELECT 
        cro.idcronograma,
        cro.numcuota,
        cro.fechapago,
        cro.interes,
        cro.abonocapital,
        coti.valorcuota, 
        cro.penalidad,
        cro.saldocapital,

        -- Total amortización acumulada
        COALESCE((
            SELECT SUM(pag.amortizacion)
            FROM pagos pag
            WHERE pag.idcronograma = cro.idcronograma
        ), 0) AS amortizacion,

        -- Cálculo del saldo restante (dinámico)
        (coti.valorcuota + cro.penalidad) - COALESCE((
            SELECT SUM(pag.amortizacion)
            FROM pagos pag
            WHERE pag.idcronograma = cro.idcronograma
        ), 0) AS saldorestante,

        cro.estado,
        cro.aplicapenalidad

    FROM cronogramas cro
    INNER JOIN contratos cont ON cro.idcontrato = cont.idcontrato
    INNER JOIN cotizaciones coti ON cont.idcotizacion = coti.idcotizacion
    WHERE cont.idcontrato = idcontrato_
    ORDER BY cro.numcuota;
END$$

DELIMITER;

CALL sp_get_cronogramas_by_idcontrato (1);

SELECT * FROM cronogramas;

SELECT * FROM pagos

USE motorpark;

DROP PROCEDURE sp_addPagoCronograma

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
    IN observacion_ VARCHAR(300)
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
        observacion
    ) VALUES (
        idcronograma_,
        IFNULL(NULLIF(idcuentapago_,''),NULL),
        idcolcaja_,
        mediopago_,
        IFNULL(NULLIF(numerotransaccion_, ''), NULL),
        fechapago_,
        amortizacion_,
        IFNULL(NULLIF(comprobante_, ''), NULL), 
        IFNULL(NULLIF(observacion_, ''), NULL)
    );
    
    SELECT LAST_INSERT_ID() AS last_insert_id;
END$$

DELIMITER;

DROP PROCEDURE sp_get_pagos_by_contrato


DELIMITER $$

CREATE PROCEDURE sp_get_pagos_by_contrato(IN p_idcontrato INT)
BEGIN
    SELECT 
        p.idpago,
        p.idcronograma,
        c.numcuota,
      DATE_FORMAT(c.fechapago,'%m/%d/%Y') AS fecha_vencimiento,
DATE_FORMAT(p.fechapago,'%m/%d/%Y') AS fecha_pago,

        p.amortizacion,
        p.saldorestante,
        p.mediopago,
        p.numerotransaccion,
        p.comprobante
    FROM pagos p
    INNER JOIN cronogramas c ON p.idcronograma = c.idcronograma
    WHERE c.idcontrato = p_idcontrato
    ORDER BY c.numcuota, p.fechapago;
END$$

CALL sp_get_pagos_by_contrato (2);

DELIMITER;

USE motorpark;

SELECT * FROM pagos;

SELECT * FROM cronogramas;

UPDATE cronogramas SET fechapago = '2025-08-08' WHERE numcuota = 11;

SELECT * FROM pagos;

SELECT * FROM cronogramas;

SELECT * FROM cuentaspago;

INSERT INTO
    cuentaspago (
        identidadpago,
        moneda,
        numcuenta
    )
VALUES (
        1,
        'Soles',
        '425895859658585258'
    );

SELECT *
FROM entidadespago
INSERT INTO
    entidadespago (entidad, tipo)
VALUES ('BCP', 'Banco');

INSERT INTO
    pagos (
        idcronograma,
        idcuentapago,
        idcolcaja,
        mediopago,
        numerotransaccion,
        fechapago,
        amortizacion,
        comprobante
    )
VALUES (
        43,
        3,
        2,
        'Yape',
        '458585858558',
        now(),
        100,
        'hghfd/ghfghdf'
    );

SELECT * FROM pagos;

SELECT *
FROM cronogramas
WHERE
    estado = 'Pendiente'
    AND fechapago < CURDATE();

UPDATE cronogramas
SET
    estado = 'Vencido'
WHERE
    estado = 'Pendiente'
    AND fechapago < CURDATE();