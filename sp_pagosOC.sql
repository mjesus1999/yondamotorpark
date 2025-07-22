USE motorpark;
SELECT * FROM pagosOC;
SELECT * FROM detordencompra;
SELECT * FROM ordenescompra;

SHOW EVENTS;
CREATE TABLE pagosOC(
idpagooc		INT NOT NULL PRIMARY KEY,
idlogistica		INT NOT NULL, -- Persona que regsitro el pago
amortizacon		DECIMAL(10,2) NOT NULL,  -- Lo que se ha adelantado
saldo			DECIMAL(10,2) NOT NULL, -- El saldo a pagar o lo que falta pagar si es que se ha hehco amortización
comprobante    VARCHAR(300) NOT NULL, -- Ruta del comprobante
fecha		   DATETIME NOT NULL,   -- Fecha y hora de que se regsitro el pago
CONSTRAINT fk_idlo_pagoOC FOREIGN KEY(idlogistica) REFERENCES colaboradores(idcolaborador)
)ENGINE=InnoDB;

SELECT * FROM compras;
SELECT * FROM ordenescompra;
UPDATE ordenescompra 
SET estado='anulado', observaciones='prueba', fechanulado=NOW() 
WHERE idordencompra = 13;



DROP trigger tr_set_saldo_pagoOC;
-- TRIGEGR PARA EL PAGO: 
DELIMITER //
CREATE TRIGGER tr_set_saldo_pagoOC
BEFORE INSERT ON pagosOC
FOR EACH ROW
BEGIN
    DECLARE totalOC DECIMAL(10,2);
    DECLARE totalPagado DECIMAL(10,2);

    -- Total de la orden de compra
    SELECT IFNULL(SUM(preciocompra * 1.18),0) INTO totalOC
    FROM detordencompra
    WHERE idordencompra = NEW.idorden;

    -- Total pagado antes de este registro
    SELECT IFNULL(SUM(amortizacion),0) INTO totalPagado
    FROM pagosOC
    WHERE idorden = NEW.idorden;

    -- Calcular saldo restante
    SET NEW.saldo = totalOC - (totalPagado + NEW.amortizacion);
END;
//
DELIMITER ;

SELECT * FROM pagosOC; -- id 13

INSERT INTO pagosOC (idorden, idlogistica, amortizacion, comprobante, fecha)
VALUES (13, 2, 10404.00, 'comprobantes/pago1.pdf', NOW());


SELECT * FROM detordencompra;
SELECT * FROM pagosOC;





-- TRAER LO QEU FALTA PAGAR :

SELECT 
    oc.idordencompra AS idorden,
    -- Total de la OC (vehículos)
    (SELECT IFNULL(SUM(preciocompra * 1.18),0) 
     FROM detordencompra 
     WHERE idordencompra = oc.idordencompra) AS totalOC,

    -- Total pagado
    (SELECT IFNULL(SUM(amortizacion * 1.18),0) 
     FROM pagosOC 
     WHERE idorden = oc.idordencompra) AS totalPagado,

    -- Saldo restante
    (
      (SELECT IFNULL(SUM(preciocompra),0) 
       FROM detordencompra 
       WHERE idordencompra = oc.idordencompra)
      -
      (SELECT IFNULL(SUM(amortizacion *1.18),0) 
       FROM pagosOC 
       WHERE idorden = oc.idordencompra)
    ) AS saldoRestante

FROM ordenescompra oc
WHERE oc.idordencompra = 16;



SELECT * FROM ordenescompra;
SELECT * FROM 




-- TRIGGER PARA CUABDO EL SALDO SEA 0, OC PASA A PAGADO 

DELIMITER $$

CREATE TRIGGER tr_update_estado_oc_pagado
AFTER INSERT ON pagosOC
FOR EACH ROW
BEGIN
    DECLARE totalOC DECIMAL(10,2);
    DECLARE totalPagado DECIMAL(10,2);

    -- Total de la OC (precio total de los vehículos)
    SELECT IFNULL(SUM(preciocompra * 1.18),0)
    INTO totalOC
    FROM detordencompra
    WHERE idordencompra = NEW.idorden;

    -- Total pagado (incluyendo el nuevo pago)
    SELECT IFNULL(SUM(amortizacion),0)
    INTO totalPagado
    FROM pagosOC
    WHERE idorden = NEW.idorden;

    -- Si ya se pagó todo, se cambia el estado de la OC
    IF totalPagado >= totalOC AND totalOC > 0 THEN
        UPDATE ordenescompra
        SET estado = 'pagado'
        WHERE idordencompra = NEW.idorden;
    END IF;
END $$

DELIMITER ;

SELECT * FROM ordenescompra;






