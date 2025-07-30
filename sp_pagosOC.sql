
USE motorpark;
SELECT * FROM pagosOC;
SELECT * FROM detordencompra;
SELECT * FROM ordenescompra;

SHOW EVENTS;
-- CREATE TABLE pagosOC(
-- idpagooc		INT NOT NULL PRIMARY KEY,
-- idlogistica		INT NOT NULL, -- Persona que regsitro el pago
-- amortizacon		DECIMAL(10,2) NOT NULL,  -- Lo que se ha adelantado
-- saldo			DECIMAL(10,2) NOT NULL, -- El saldo a pagar o lo que falta pagar si es que se ha hehco amortización
-- comprobante    VARCHAR(300) NOT NULL, -- Ruta del comprobante
-- fecha		   DATETIME NOT NULL,   -- Fecha y hora de que se regsitro el pago
-- CONSTRAINT fk_idlo_pagoOC FOREIGN KEY(idlogistica) REFERENCES colaboradores(idcolaborador)
-- )ENGINE=InnoDB;

SELECT * FROM compras;
SELECT * FROM ordenescompra;
UPDATE ordenescompra 
SET estado='anulado', observaciones='prueba', fechanulado=NOW() 
WHERE idordencompra = 13;





-- muestra el saldo que falta pagar(Saldo restante)
SELECT 
    oc.idordencompra AS idorden,
    (SELECT IFNULL(SUM(preciocompra * 1.18),0) 
     FROM detordencompra 
     WHERE idordencompra = oc.idordencompra) AS totalOC,

    (SELECT IFNULL(SUM(amortizacion),0) 
     FROM pagosOC 
     WHERE idorden = oc.idordencompra) AS totalPagado,

    (
      (SELECT IFNULL(SUM(preciocompra * 1.18),0) 
       FROM detordencompra 
       WHERE idordencompra = oc.idordencompra)
      -
      (SELECT IFNULL(SUM(amortizacion),0) 
       FROM pagosOC 
       WHERE idorden = oc.idordencompra)
    ) AS saldoRestante
FROM ordenescompra oc
WHERE oc.idordencompra = 24;



DROP TRIGGER IF EXISTS tr_set_saldo_pagoOC;
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


SELECT * FROM pagosOC; -- id 13

INSERT INTO pagosOC (idorden, idlogistica, amortizacion, comprobante, fecha)
VALUES (15, 2, 45548, 'comprobantes/pago1.pdf', NOW());


SELECT * FROM detordencompra;
SELECT * FROM pagosOC;





-- TRAER LO QEU FALTA PAGAR :


SELECT * FROM ordenescompra;
SELECT * FROM 


UPDATE ordenescompra SET estado='proceso' WHERE idordencompra = 6;



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

SELECT * FROM ordenescompra;
SELECT * FROM detordencompra;
SELECT * FROM vehiculos;


DELIMITER //








CREATE PROCEDURE sp_eliminar_OC(
IN idordencompra_ INT
)
BEGIN
	DELETE FROM detordencompra WHERE idordencompra = idordencompra_;
    DELETE FROM ordenescompra WHERE idordencompra = idordencompra_;

END //







