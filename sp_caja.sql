
USE motorpark;

DROP PROCEDURE IF EXISTS sp_get_cronogramas_by_idcontrato;
-- Delimiter para permitir múltiples sentencias dentro del SPDROP PROCEDURE IF EXISTS sp_get_cronogramas_by_idcontrato;
DELIMITER $$

CREATE PROCEDURE sp_get_cronogramas_by_idcontrato(IN idcontrato_ INT)
BEGIN
    SELECT 
        cro.idcronograma,
        cro.numcuota,
        cro.fechapago,
        cro.interes,
        cro.abonocapital,
        coti.valorcuota, -- Valor original de la cuota
        cro.penalidad,
        cro.saldocapital,

        -- Estado calculado según saldo restante
        CASE
            WHEN coti.valorcuota - COALESCE(SUM(pag.amortizacion), 0) = 0 THEN 'Pagado'
            ELSE 'Pendiente'
        END AS estado,

        -- Monto total amortizado
        COALESCE(SUM(pag.amortizacion), 0) AS amortizacion,

        -- Saldo restante real
        coti.valorcuota - COALESCE(SUM(pag.amortizacion), 0) AS saldorestante

    FROM cronogramas cro
    INNER JOIN contratos cont ON cro.idcontrato = cont.idcontrato
    INNER JOIN cotizaciones coti ON cont.idcotizacion = coti.idcotizacion
    LEFT JOIN pagos pag ON cro.idcronograma = pag.idcronograma
    WHERE cont.idcontrato = idcontrato_
    GROUP BY
        cro.idcronograma,
        cro.numcuota,
        cro.fechapago,
        cro.interes,
        cro.abonocapital,
        coti.valorcuota,
        cro.penalidad,
        cro.saldocapital
    ORDER BY cro.numcuota;
END$$


DROP TRIGGER tr_actualizar_estado_cronograma_after_update

DELIMITER $$

CREATE TRIGGER tr_actualizar_estado_cronograma_after_insert
AFTER INSERT ON pagos
FOR EACH ROW
BEGIN
    DECLARE v_valorcuota DECIMAL(10,2);
    DECLARE v_total_amortizado DECIMAL(10,2);

    -- Obtener valor de la cuota
    SELECT coti.valorcuota INTO v_valorcuota
    FROM cronogramas cro
    JOIN contratos cont ON cro.idcontrato = cont.idcontrato
    JOIN cotizaciones coti ON cont.idcotizacion = coti.idcotizacion
    WHERE cro.idcronograma = NEW.idcronograma;

    -- Sumar amortizaciones acumuladas
    SELECT COALESCE(SUM(amortizacion), 0) INTO v_total_amortizado
    FROM pagos
    WHERE idcronograma = NEW.idcronograma;

    -- Actualizar estado si está pagado
    IF v_total_amortizado >= v_valorcuota THEN
        UPDATE cronogramas
        SET estado = 'Pagado'
        WHERE idcronograma = NEW.idcronograma;
    END IF;
END$$
DELIMITER ;










DELIMITER $$

CREATE TRIGGER tr_actualizar_estado_cronograma_after_update
AFTER UPDATE ON pagos
FOR EACH ROW
BEGIN
    DECLARE v_valorcuota DECIMAL(10,2);
    DECLARE v_total_amortizado DECIMAL(10,2);

    -- Obtener valor de la cuota
    SELECT coti.valorcuota INTO v_valorcuota
    FROM cronogramas cro
    JOIN contratos cont ON cro.idcontrato = cont.idcontrato
    JOIN cotizaciones coti ON cont.idcotizacion = coti.idcotizacion
    WHERE cro.idcronograma = NEW.idcronograma;

    -- Sumar amortizaciones acumuladas
    SELECT COALESCE(SUM(amortizacion), 0) INTO v_total_amortizado
    FROM pagos
    WHERE idcronograma = NEW.idcronograma;

    -- Actualizar estado si está pagado
    IF v_total_amortizado >= v_valorcuota THEN
        UPDATE cronogramas
        SET estado = 'Pagado'
        WHERE idcronograma = NEW.idcronograma;
    END IF;
END$$
DELIMITER ;




INSERT INTO pagos(idcronograma,idcuentapago,idcolcaja,mediopago,numerotransaccion,fechapago,amortizacion,comprobante)VALUES(37,3,2,'Yape','458585858558',now(), 1996,'hghfd/ghfghdf');

SELECT * FROM pagos;
SELECT * FROM cronogramas;


SELECT * FROM cuentaspago;

INSERT INTO cuentaspago(identidadpago,moneda,numcuenta)VALUES(1,'Soles','425895859658585258');

SELECT * FROM entidadespago
INSERT INTO entidadespago(entidad,tipo) VALUES('BCP','Banco');