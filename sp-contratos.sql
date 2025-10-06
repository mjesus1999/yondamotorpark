
USE MOTORPARK;

SELECT * FROM fichasolicitud;
SELECT * FROM cotizaciones WHERE estadocotizacion = 'O';
UPDATE cotizaciones SET estadocotizacion = 'A' WHERE idcotizacion =25;

SELECT * FROM cotizaciones;
CREATE TABLE conceptospago(
idconcepto    INT PRIMARY KEY AUTO_INCREMENT,
idcolregistra INT NOT NULL,
idcolactualiza  INT NULL,
concepto     VARCHAR(150) NOT NULL,
descripcion   TEXT NULL,
montosugerido DECIMAL(10,2)  NULL,
)ENGINE=INNODB;


CREATE TABLE pagosvarios(
idpagovario   INT PRIMARY KEY AUTO_INCREMENT,
idcotizacion  INT NOT NULL,
idconcepto    INT NOT NULL,
idcuentapago  INT NOT NULL,
montofinal    DECIMAL(10,2) NOT NULL,
fechapago     DATE NOT NULL,
)ENGINE=INNODB;

