
DROP TRIGGER IF EXISTS tr_calcular_saldorestante_before_insert;

DELIMITER $$

CREATE TRIGGER tr_calcular_saldorestante_before_insert
BEFORE INSERT ON pagos
FOR EACH ROW
BEGIN
    DECLARE v_valorcuota DECIMAL(10,2);
    DECLARE v_penalidad DECIMAL(10,2);
    DECLARE v_amortizado_anterior DECIMAL(10,2);

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
END$$

DELIMITER ;




DROP TRIGGER IF EXISTS tr_actualizar_estado_cronograma_after_insert;

DELIMITER $$

CREATE TRIGGER tr_actualizar_estado_cronograma_after_insert
AFTER INSERT ON pagos
FOR EACH ROW
BEGIN
    DECLARE v_valorcuota DECIMAL(10,2);
    DECLARE v_penalidad DECIMAL(10,2);
    DECLARE v_total_amortizado DECIMAL(10,2);

    -- Obtener valor cuota y penalidad actual
    SELECT coti.valorcuota, cro.penalidad INTO v_valorcuota, v_penalidad
    FROM cronogramas cro
    JOIN contratos cont ON cro.idcontrato = cont.idcontrato
    JOIN cotizaciones coti ON cont.idcotizacion = coti.idcotizacion
    WHERE cro.idcronograma = NEW.idcronograma;

    -- Sumar amortizaciones actuales
    SELECT COALESCE(SUM(amortizacion), 0) INTO v_total_amortizado
    FROM pagos
    WHERE idcronograma = NEW.idcronograma;

    -- Si pagó todo (cuota + penalidad), marcar como Pagado
    IF v_total_amortizado >= (v_valorcuota + v_penalidad) THEN
        UPDATE cronogramas
        SET estado = 'Pagado'
        WHERE idcronograma = NEW.idcronograma;
    END IF;
END$$

DELIMITER ;



SELECT * FROM cronogramas;
SELECT * FROM pagos WHERE idcronograma = 45;


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



SHOW TRIGGERS



