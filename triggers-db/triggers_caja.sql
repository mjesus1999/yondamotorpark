
USE motorpark;
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



DELIMITER $$

CREATE TRIGGER trg_verificar_fin_contrato
AFTER UPDATE ON cronogramas
FOR EACH ROW
BEGIN
    DECLARE cuotas_restantes INT;


    IF NEW.estado = 'Pagado' AND OLD.estado != 'Pagado' THEN
        
        -- Contamos cuántas cuotas quedan pendientes o vencidas para este contrato
        SELECT COUNT(*) INTO cuotas_restantes
        FROM cronogramas
        WHERE idcontrato = NEW.idcontrato 
          AND estado != 'Pagado'; -- Buscamos cualquier cosa que no esté pagada

        -- Si ya no queda ninguna cuota pendiente (es decir, es 0)
        IF cuotas_restantes = 0 THEN
            UPDATE contratos 
            SET estado = 'FIN' 
            WHERE idcontrato = NEW.idcontrato;
        END IF;
        
    END IF;
END$$

DELIMITER ;

-- SELECT * FROM cronogramas;

-- select count(numcuota) FROM cronogramas WHERE idcontrato = 16;


