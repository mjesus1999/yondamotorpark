
USE motorpark;

DELIMITER //
CREATE TRIGGER tr_set_saldo_pagoOC
BEFORE INSERT ON pagosOC
FOR EACH ROW
BEGIN
    DECLARE totalOC DECIMAL(10,2);
    DECLARE totalPagado DECIMAL(10,2);

    -- Total de la OC con IGV
    SELECT IFNULL(SUM(preciocompra * 1.18),0) INTO totalOC
    FROM detordencompra
    WHERE idordencompra = NEW.idorden;

    -- Total pagado hasta ahora
    SELECT IFNULL(SUM(amortizacion),0) INTO totalPagado
    FROM pagosOC
    WHERE idorden = NEW.idorden;

    -- Validar que el nuevo pago no exceda el saldo
    IF (totalPagado + NEW.amortizacion) > totalOC THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El monto de amortizacion excede el saldo de la orden';
    END IF;

    -- Calcular saldo restante (mínimo 0)
    SET NEW.saldo = GREATEST(0, totalOC - (totalPagado + NEW.amortizacion));
END;
//
DELIMITER ;



-- TRIGGER PARA CUABDO EL SALDO SEA 0, OC PASA A PAGADO 
DROP TRIGGER IF EXISTS tr_update_estado_oc_pagado;
DELIMITER $$
CREATE TRIGGER tr_update_estado_oc_pagado
AFTER INSERT ON pagosOC
FOR EACH ROW
BEGIN
    DECLARE totalOC DECIMAL(10,2);
    DECLARE totalPagado DECIMAL(10,2);

    SELECT IFNULL(SUM(preciocompra * 1.18),0) INTO totalOC
    FROM detordencompra
    WHERE idordencompra = NEW.idorden;

    SELECT IFNULL(SUM(amortizacion),0) INTO totalPagado
    FROM pagosOC
    WHERE idorden = NEW.idorden;

    IF totalPagado >= totalOC AND totalOC > 0 THEN
        UPDATE ordenescompra
        SET estado = 'pagado'
        WHERE idordencompra = NEW.idorden
        AND estado != 'anulado';
    END IF;
END $$
DELIMITER ;




CREATE PROCEDURE sp_eliminar_OC(
IN idordencompra_ INT
)
BEGIN
	DELETE FROM detordencompra WHERE idordencompra = idordencompra_;
    DELETE FROM ordenescompra WHERE idordencompra = idordencompra_;

END //


USE motorpark2;

SHOW TRIGGERS;


