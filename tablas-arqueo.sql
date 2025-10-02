
USE Motorpark;

CREATE TABLE arqueocaja (
    idarqueo INT PRIMARY KEY AUTO_INCREMENT,
    idcolaborador INT NOT NULL,
    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    saldo_inicial DECIMAL(10, 2) NOT NULL,
    ingresos_efectivo DECIMAL(10, 2) NOT NULL,
    ingresos_digital DECIMAL(10, 2) NOT NULL,
    egresos_dia DECIMAL(10, 2) NOT NULL,
    monto_teorico DECIMAL(10, 2) NOT NULL,
    monto_fisico DECIMAL(10, 2) NOT NULL,
    diferencia DECIMAL(10, 2) NOT NULL,
    observaciones VARCHAR(500) NULL,
    estado ENUM('Cuadrado', 'Faltante','Sobrante') NOT NULL DEFAULT 'Cuadrado',
    entregado ENUM('S', 'N') NOT NULL DEFAULT 'N',
    creado DATETIME NOT NULL DEFAULT NOW(),
    CONSTRAINT fk_colaborador_arqueo FOREIGN KEY (idcolaborador) REFERENCES colaboradores(idcolaborador)
) ENGINE = InnoDB;



CREATE TABLE entregasdinero (
    identrega INT AUTO_INCREMENT PRIMARY KEY,
    idcolentrega INT NOT NULL,
    fechaentrega DATETIME NOT NULL DEFAULT NOW(),
    montoentregado DECIMAL(10, 2) NOT NULL,
    observaciones VARCHAR(500) NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    CONSTRAINT fk_colaborador_entrega FOREIGN KEY (idcolentrega) REFERENCES colaboradores(idcolaborador)
) ENGINE = InnoDB;


CREATE TABLE entregasdineroarqueos (
    identregaarqueo INT AUTO_INCREMENT PRIMARY KEY,
    identrega INT NOT NULL,
    idarqueo INT NOT NULL,
    CONSTRAINT fk_entrega_arqueo FOREIGN KEY (identrega) REFERENCES entregasdinero(identrega),
    CONSTRAINT fk_arqueo_entrega FOREIGN KEY (idarqueo) REFERENCES arqueocaja(idarqueo)
) ENGINE = InnoDB;



CREATE TABLE entregasdinero_destinos (
    identregadestino INT AUTO_INCREMENT PRIMARY KEY,
    identrega INT NOT NULL,
    tipodestino ENUM('Gerente', 'Deposito') NOT NULL,
    iddestino INT NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    CONSTRAINT fk_entrega_destino FOREIGN KEY (identrega) REFERENCES entregasdinero(identrega)
) ENGINE = InnoDB;


DROP TABLE arqueocaja;
DROP TABLE entregasdinero;
DROP TABLE entregasdineroarqueos;
DROP TABLE entregasdinero_destinos;


SELECT * FROM entregasdinero;
SELECT * FROM entregasdineroarqueos;
SELECT * FROM arqueocaja;

UPDATE arqueocaja SET creado = '2025-10-01 16:46:30' WHERE idarqueo= 43;
UPDATE entregasdinero SET fechaentrega = '2025-10-01 16:48:26' WHERE identrega = 10;

SELECT * FROM egresos;
SELECT * FROM pagos;

DELETE FROM egresos;
DELETE FROM comprobantes;
UPDATE pagos SET fechapago = '2025-09-24' WHERE idpago = 393;

SELECT * FROM egresos;
Update egresos SET fecha = '2025-09-24' WHERE idegreso = 3;



INSERT INTO arqueocaja (
    idcolaborador, fecha, hora_inicio, hora_fin,
    saldo_inicial, ingresos_efectivo, ingresos_digital,
    egresos_dia, monto_teorico, monto_fisico, diferencia, observaciones
) VALUES (
    3, CURDATE(), CURTIME(), CURTIME(),  -- fechas/horas
    500.00, 0.00, 0.00,  -- saldo inicial, ingresos
    0.00, 500.00, 500.00, 0.00, NULL     -- egresos, teorico, fisico, diferencia
);




SELECT * FROM arqueocaja WHERE entregado = 'N' ORDER BY fecha DESC;




SELECT * FROM arqueocaja;
SELECT * FROM egresos;
SELECT * FROM pagos;

SELECT * FROM contratos;


SELECT DATE_FORMAT(fechaentrega,'%H:%i') AS ultima_hora_entrega
FROM entregasdinero
WHERE DATE(fechaentrega) = CURDATE()
ORDER BY identrega DESC
LIMIT 1;



SELECT * FROM pagos;