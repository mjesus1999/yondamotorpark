
USE motorpark2;

DROP TRIGGER IF EXISTS tr_set_saldo_pagoOC;
DELIMITER //
CREATE TRIGGER tr_set_saldo_pagoOC
BEFORE INSERT ON pagosOC
FOR EACH ROW
BEGIN
    DECLARE totalOC DECIMAL(10,2);
    DECLARE totalPagosUSD DECIMAL(10,4); -- Usar más decimales internamente para mayor precisión

    -- Obtener el total de la OC con IGV
    SELECT ROUND(IFNULL(SUM(preciocompra * 1.18),0),2) INTO totalOC
    FROM detordencompra
    WHERE idordencompra = NEW.idorden;

    -- Calcular el total de todos los pagos (existentes + el nuevo pago) en USD
    -- Se hace un solo cálculo para evitar errores de redondeo acumulados
    SELECT IFNULL(SUM(
        CASE
            WHEN moneda = 'PEN' AND tipocambio > 0 THEN amortizacion / tipocambio
            ELSE amortizacion
        END
    ), 0) INTO totalPagosUSD
    FROM pagosOC
    WHERE idorden = NEW.idorden;
    
    SET totalPagosUSD = totalPagosUSD + (
        CASE
            WHEN NEW.moneda = 'PEN' AND NEW.tipocambio > 0 THEN NEW.amortizacion / NEW.tipocambio
            ELSE NEW.amortizacion
        END
    );

    -- Validar que el nuevo pago no exceda el saldo
    -- La validación se hace con el valor sin redondear para mayor precisión
    IF totalPagosUSD > totalOC + 0.001 THEN -- Se agrega un pequeño margen para evitar problemas de flotantes
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El monto de amortizacion excede el saldo de la orden';
    END IF;

    -- Calcular saldo restante con redondeo al final
    SET NEW.saldo = ROUND(GREATEST(0, totalOC - totalPagosUSD), 2);
END;
//
DELIMITER ;

SELECT * FROM pagosOc;

SHOW COLUMNS FROM pagosOC;


-- TRIGGER PARA CUABDO EL SALDO SEA 0, OC PASA A PAGADO 
DROP TRIGGER IF EXISTS tr_update_estado_oc_pagado;
DELIMITER $$
CREATE TRIGGER tr_update_estado_oc_pagado
AFTER INSERT ON pagosOC
FOR EACH ROW
BEGIN
    DECLARE totalOC DECIMAL(10,2);
    DECLARE totalPagado DECIMAL(10,2);

    SELECT IFNULL(SUM(preciocompra * 1.18),0) INTO totalOC -- SUMA TODOS LOS PRECIOSCOMPRA DE ESA OC
    FROM detordencompra
    WHERE idordencompra = NEW.idorden;

    SELECT IFNULL(SUM(
        CASE
            WHEN moneda = 'PEN' AND tipocambio > 0 THEN amortizacion / tipocambio
            ELSE amortizacion
        END
    ),0) INTO totalPagado
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


