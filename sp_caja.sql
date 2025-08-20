SHOW DATABASES;

DROP DATABASE  motorpark2;
-- Procedimiento almacenado modificado
USE motorpark2;

SELECT * FROM cronogramas;
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
    WHERE cro.fechapago < CURDATE()
      AND cro.estado != 'Pagado'
      AND cont.idcontrato = idcontrato_;

    -- Luego seleccionamos los datos
    SELECT 
        cro.idcronograma,
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


CALL sp_get_cronogramas_by_idcontrato (2);

SELECT * FROM cronogramas;
UPDATE cronogramas SET fechapago = '2025-08-13' WHERE  idcontrato = 2 AND numcuota = 6 ;
SELECT * FROM contratos;

SELECT * FROM pagos;

SELECT * FROM pagos


USE motorpark2;

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
        IFNULL(NULLIF(comprobante_, ''), NULL),
        IFNULL(NULLIF(observacion_, ''), NULL),
        tipo_ 
    );

    SELECT LAST_INSERT_ID() AS last_insert_id;
END$$

DELIMITER ;


DELIMITER;

USE motorpark2;
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
        p.comprobante,
        p.tipo,
        p.observacion
    FROM pagos p
    INNER JOIN cronogramas c ON p.idcronograma = c.idcronograma
    WHERE c.idcontrato = p_idcontrato
    ORDER BY c.numcuota, p.fechapago;
END$$

CALL sp_get_pagos_by_contrato (2);

DELIMITER;

USE motorpark2

SELECT * FROM contratos;

SELECT * FROM pagos;

SELECT * FROM cronogramas;


UPDATE cronogramas SET fechapago = '2025-08-14' WHERE idcronograma = 1777;
UPDATE cronogramas SET fechapago = '2026-03-13', penalidad = 0, aplicapenalidad = 'N', estado = 'Pagado' WHERE idcronograma = 1521;
UPDATE  cronogramas SET fechapago = '2026-05-13', penalidad = 0, aplicapenalidad ='N', estado = 'Pendiente' WHERE idcronograma = 1521;


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
        '4258958596585852'
    );

SELECT *
FROM entidadespago;

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


DELETE  FROM pagos;