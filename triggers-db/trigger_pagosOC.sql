

DROP TRIGGER IF EXISTS tr_set_saldo_pagoOC;

DELIMITER //
CREATE TRIGGER tr_set_saldo_pagoOC
BEFORE INSERT ON pagosOC
FOR EACH ROW
BEGIN
    DECLARE totalOC DECIMAL(10,2);
    DECLARE totalPagosUSD DECIMAL(10,4); 
    DECLARE totalPagosRedondeado DECIMAL(10,2); -- Nuevo: Variable para el valor redondeado

    -- Obtener el total de la OC con IGV
    SELECT ROUND(IFNULL(SUM(preciocompra * 1.18),0),2) INTO totalOC
    FROM detordencompra
    WHERE idordencompra = NEW.idorden;

    -- Calcular el total de todos los pagos (existentes + el nuevo pago) en USD
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
    
    -- Redondear la suma total de pagos antes de la validación
    SET totalPagosRedondeado = ROUND(totalPagosUSD, 2);

    -- Validar que el nuevo pago no exceda el saldo usando el valor redondeado
    IF totalPagosRedondeado > totalOC THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El monto de amortizacion excede el saldo de la orden';
    END IF;

    -- Calcular saldo restante con redondeo al final
    SET NEW.saldo = ROUND(GREATEST(0, totalOC - totalPagosRedondeado), 2);
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







SHOW TRIGGERS;


SELECT * FROM pagosOC;


use motorpark;


SHOW EVENTS FROM motorpark;

SHOW TRIGGERS;