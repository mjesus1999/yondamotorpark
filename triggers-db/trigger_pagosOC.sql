-- Triggers para pagosOC (esquema IMPORTAR_OK_2026: sin moneda/tipocambio).
-- Usar DELIMITER en cliente si aplica; en phpMyAdmin suele bastar importar tal cual.

DROP TRIGGER IF EXISTS tr_set_saldo_pagoOC;
DROP TRIGGER IF EXISTS tr_update_estado_oc_pagado;

DELIMITER $$

CREATE TRIGGER tr_set_saldo_pagoOC
BEFORE INSERT ON pagosOC
FOR EACH ROW
BEGIN
    DECLARE totalOC DECIMAL(10,2);
    DECLARE totalPagado DECIMAL(10,2);

    SELECT IFNULL(SUM(preciocompra * 1.18), 0) INTO totalOC
    FROM detordencompra
    WHERE idordencompra = NEW.idorden;

    SELECT IFNULL(SUM(amortizacion), 0) INTO totalPagado
    FROM pagosOC
    WHERE idorden = NEW.idorden;

    IF (totalPagado + NEW.amortizacion) > totalOC THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El monto de amortizacion excede el saldo de la orden';
    END IF;

    SET NEW.saldo = ROUND(GREATEST(0, totalOC - (totalPagado + NEW.amortizacion)), 2);
END$$

CREATE TRIGGER tr_update_estado_oc_pagado
AFTER INSERT ON pagosOC
FOR EACH ROW
BEGIN
    DECLARE totalOC DECIMAL(10,2);
    DECLARE totalPagado DECIMAL(10,2);

    SELECT IFNULL(SUM(preciocompra * 1.18), 0) INTO totalOC
    FROM detordencompra
    WHERE idordencompra = NEW.idorden;

    SELECT IFNULL(SUM(amortizacion), 0) INTO totalPagado
    FROM pagosOC
    WHERE idorden = NEW.idorden;

    IF totalPagado >= totalOC AND totalOC > 0 THEN
        UPDATE ordenescompra
        SET estado = 'pagado'
        WHERE idordencompra = NEW.idorden
          AND estado != 'anulado';
    END IF;
END$$

DELIMITER ;
