USE motorpark;
SELECT * FROM cuentaspago;
SELECT * FROM entidadespago;

CREATE TABLE pagosOC (
    idpagooc INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    idorden INT NOT NULL, -- ID OC
    idlogistica INT NOT NULL, -- Persona que registro el pago
    amortizacion DECIMAL(10, 2) NOT NULL, -- Lo que se ha adelantado
    saldo DECIMAL(10, 2) NOT NULL, -- El saldo a pagar o lo que falta pagar si es que se ha hehco amortización
    comprobante VARCHAR(300) NOT NULL, -- Ruta del comprobante
    fecha DATETIME NOT NULL DEFAULT NOW(), -- Fecha y hora de que se regsitro el pago
    fecharealpago DATETIME NOT NULL,
    CONSTRAINT fk_idlo_pagoOC FOREIGN KEY (idlogistica) REFERENCES colaboradores (idcolaborador),
    CONSTRAINT fk_idorde_pagosOC FOREIGN KEY (idorden) REFERENCES ordenescompra (idordencompra)
) ENGINE = InnoDB;



CREATE TABLE amortizacionesoc (
    idamortizacion INT AUTO_INCREMENT PRIMARY KEY,
    idorden INT NOT NULL,
    idlogistica INT NOT NULL,
    identidadpago INT NOT NULL,
    fechapago DATE NOT NULL,
    numtransaccion VARCHAR(20) NOT NULL,
    moneda ENUM('USD', 'PEN') NOT NULL,
    tipocambio DECIMAL(5, 2) NULL,
    amortizacion DECIMAL(9, 2) NOT NULL,
    saldo DECIMAL(9, 2) NOT NULL,
    comprobante VARCHAR(200) NULL,
    observaciones VARCHAR(400) NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT fk_idorden_aoc FOREIGN KEY (idorden) REFERENCES ordenescompra (idordencompra),
    CONSTRAINT fk_idlogistica_aoc FOREIGN KEY (idlogistica) REFERENCES colaboradores (idcolaborador),
    CONSTRAINT fk_identidadpago_aoc FOREIGN KEY (identidadpago) REFERENCES entidadespago (identidadpago)
) ENGINE = INNODB;








-- // SP PARA LAS OC QEU ESTAN EN PROCESO. REPORTE GENREAL DE LAS OC k

DELIMITER $$

CREATE PROCEDURE sp_oc_por_estado_reporte(IN p_estado VARCHAR(50))
BEGIN
    SELECT 
        oc.idordencompra,
        oc.serie,
        DATE_FORMAT(oc.emision, '%d-%m-%Y') AS emision, 
        oc.moneda,
        con.razonsocial,
        (
            SELECT IFNULL(SUM(preciocompra * 1.18),0) 
            FROM detordencompra 
            WHERE idordencompra = oc.idordencompra
        ) AS totalOC,
        (
            SELECT IFNULL(SUM(amortizacion),0) 
            FROM pagosOC 
            WHERE idorden = oc.idordencompra
        ) AS totalPagado,
        (
            (SELECT IFNULL(SUM(amortizacion),0) 
             FROM pagosOC 
             WHERE idorden = oc.idordencompra) /
            NULLIF((SELECT IFNULL(SUM(preciocompra * 1.18),0) 
             FROM detordencompra 
             WHERE idordencompra = oc.idordencompra),0) * 100
        ) AS avancePorcentaje,
        (
            SELECT COUNT(*) 
            FROM detordencompra 
            WHERE idordencompra = oc.idordencompra
        ) AS totalVehiculos,
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
    JOIN tiendas t ON oc.idtienda = t.idtienda
    JOIN concesionarios con ON t.idconcesionario = con.idconcesionario
    WHERE oc.estado = CONVERT(p_estado USING utf8mb4) COLLATE utf8mb4_general_ci
    ORDER BY oc.idordencompra DESC;
END $$

DELIMITER ;
CALL sp_oc_por_estado_reporte('proceso');
select * from pagosoc;