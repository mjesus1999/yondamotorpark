
USE MOTORPARK;

SELECT * FROM cronogramas;
SELECT * FROM cotizaciones;

SELECT * FROM contratos;




CREATE TABLE conceptospago(
idconcepto    INT PRIMARY KEY AUTO_INCREMENT,
idcolregistra INT NOT NULL,
idcolactualiza  INT NULL,
concepto     VARCHAR(150) NOT NULL,
descripcion   TEXT NULL,
montosugerido DECIMAL(10,2)  NULL,
)ENGINE=INNODB;

-- SELECT * FROM cotizaciones;
-- SELECT * FROM personas;

CREATE TABLE pagosvarios(
idpagovario   INT PRIMARY KEY AUTO_INCREMENT,
idcotizacion  INT NOT NULL,
idconcepto    INT NOT NULL,
idcuentapago  INT NOT NULL,
montofinal    DECIMAL(10,2) NOT NULL,
fechapago     DATE NOT NULL,
)ENGINE=INNODB;




SELECT * FROM empresas;

    -- SELECT nombres FROM personas WHERE nombres LIKE 'j%';



CREATE TABLE conceptospago (
    idconcepto INT PRIMARY KEY AUTO_INCREMENT,
    idcolregistra INT NOT NULL,
    idcolactualiza INT NULL,
    concepto VARCHAR(150) NOT NULL,
    descripcion TEXT NULL,
    montosugerido DECIMAL(10,2) NULL,
    fecharegistro DATETIME NOT NULL DEFAULT NOW(),
    fechamodificacion DATETIME NULL
) ENGINE=InnoDB;


CREATE TABLE pagos (
    idingreso INT PRIMARY KEY AUTO_INCREMENT,
    idconcepto INT NOT NULL,
    idcolcaja INT NULL,
    idcronograma INT NULL,
    idcuentapago INT NULL,
    mediopago ENUM('Yape', 'Plin', 'Transferencia Bancaria', 'Efectivo') NOT NULL,
    numerotransaccion VARCHAR(30) NULL,
    fechapago DATE NOT NULL,
    fecharegistro DATETIME NOT NULL DEFAULT NOW(),
    monto DECIMAL(10,2) NOT NULL,
    comprobante VARCHAR(200) NULL,
    observacion VARCHAR(300) NULL,
    facturado ENUM('S','N') DEFAULT 'S',
    declarado ENUM('S','N') DEFAULT 'N',
    tipo ENUM('Cuota','Penalidad','Otro') NOT NULL DEFAULT 'Cuota',
    CONSTRAINT fk_idconcepto_ingresos FOREIGN KEY (idconcepto) REFERENCES conceptospago(idconcepto),
    CONSTRAINT fk_idcolcaja_ingresos FOREIGN KEY (idcolcaja) REFERENCES colaboradores(idcolaborador),
    CONSTRAINT fk_idcuentapago_ingresos FOREIGN KEY (idcuentapago) REFERENCES cuentaspago(idcuentapago),
      CONSTRAINT fk_idcronograma_pagos FOREIGN KEY (idcronograma) REFERENCES cronogramas (idcronograma),
) ENGINE=InnoDB;
















SELECT * FROM cotizaciones;
SELECT * FROM contratos;



CREATE TABLE pagosTMP (
    idpago INT AUTO_INCREMENT PRIMARY KEY,
    idconcepto INT NULL,
    idvehiculo  INT NULL,
    idasesorvendedor INT NULL,
    idcronograma INT  NULL,
    idcuentapago INT NULL,
    idcolcaja INT NULL,
    mediopago ENUM(
        'Yape',
        'Plin',
        'Transferencia Bancaria',
        'Efectivo'
    ) NOT NULL,
    numerotransaccion VARCHAR(30) NULL,
    fechapago DATE NOT NULL,
    fecharegistro DATETIME NOT NULL DEFAULT NOW(),
    amortizacion DECIMAL(10, 2) NOT NULL,
    saldorestante DECIMAL(10, 2) NULL,
    comprobante VARCHAR(200) NULL,
    observacion VARCHAR(300) NULL,
    facturado ENUM('S', 'N') DEFAULT 'S',
    declarado ENUM('S', 'N') DEFAULT 'N',
    tipo        ENUM('Cuota','Penalidad') NOT NULL DEFAULT 'Cuota',
    CONSTRAINT fk_idcronograma_pagos FOREIGN KEY (idcronograma) REFERENCES cronogramas (idcronograma),
    CONSTRAINT fk_idcuentapago_pagos FOREIGN KEY (idcuentapago) REFERENCES cuentaspago (idcuentapago),
    CONSTRAINT fk_idcolcaja_pagos FOREIGN KEY (idcolcaja) REFERENCES colaboradores (idcolaborador)
) ENGINE = InnoDB;