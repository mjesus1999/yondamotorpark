
USE motorpark2;
-- DELIMITER $$

-- CREATE TRIGGER tr_calcular_saldorestante_before_insert
-- BEFORE INSERT ON pagos
-- FOR EACH ROW
-- BEGIN
--     DECLARE v_valorcuota DECIMAL(10,2);
--     DECLARE v_penalidad DECIMAL(10,2);
--     DECLARE v_amortizado_anterior DECIMAL(10,2);

--     -- Obtener cuota y penalidad del cronograma correspondiente
--     SELECT coti.valorcuota, cro.penalidad INTO v_valorcuota, v_penalidad
--     FROM cronogramas cro
--     JOIN contratos cont ON cro.idcontrato = cont.idcontrato
--     JOIN cotizaciones coti ON cont.idcotizacion = coti.idcotizacion
--     WHERE cro.idcronograma = NEW.idcronograma;

--     -- Sumar amortizaciones anteriores
--     SELECT COALESCE(SUM(amortizacion), 0) INTO v_amortizado_anterior
--     FROM pagos
--     WHERE idcronograma = NEW.idcronograma;

--     -- Calcular el nuevo saldo restante
--     SET NEW.saldorestante = (v_valorcuota + v_penalidad) - (v_amortizado_anterior + NEW.amortizacion);
-- END$$

-- DELIMITER ;

DROP TRIGGER tr_calcular_saldorestante_before_insert;
DELIMITER $$

CREATE TRIGGER tr_calcular_saldorestante_before_insert
BEFORE INSERT ON pagos
FOR EACH ROW
BEGIN
    DECLARE v_valorcuota DECIMAL(10,2);
    DECLARE v_penalidad DECIMAL(10,2);
    DECLARE v_amortizado_anterior DECIMAL(10,2);

    IF NEW.idcronograma IS NOT NULL THEN
        -- Obtener cuota y penalidad del cronograma correspondiente
        SELECT coti.valorcuota, cro.penalidad INTO v_valorcuota, v_penalidad
        FROM cronogramas cro
        JOIN contratos cont ON cro.idcontrato = cont.idcontrato
        JOIN cotizaciones coti ON cont.idcotizacion = coti.idcotizacion
        WHERE cro.idcronograma = NEW.idcronograma;

        -- Sumar amortizaciones anteriores
        SELECT COALESCE(SUM(amortizacion), 0) INTO v_amortizado_anterior
        FROM pagos
        WHERE idcronograma = NEW.idcronograma;

        -- Calcular el nuevo saldo restante
        SET NEW.saldorestante = (v_valorcuota + v_penalidad) - (v_amortizado_anterior + NEW.amortizacion);
    END IF;
END$$

DELIMITER ;





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
        45,
        3,
        2,
        'Yape',
        '458585858558',
        now(),
        1096,
        'hghfd/ghfghdf'
    );



SHOW TRIGGERS;
SHOW EVENTS;



