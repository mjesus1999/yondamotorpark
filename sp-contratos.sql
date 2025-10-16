
USE motorpark;

-- La moneda y precio de compra están definidos en el proceso de COMPRA
CREATE TABLE vehiculos (
    idvehiculo INT AUTO_INCREMENT PRIMARY KEY,
    idmodelo INT NOT NULL,
    idcombustible INT NOT NULL,
    idlogistica INT NULL,
    idlocal INT NULL,
    version VARCHAR(20) NOT NULL,
    condicion ENUM('nuevo', 'seminuevo') NOT NULL DEFAULT 'nuevo',
    color VARCHAR(30) NULL,
    chasis VARCHAR(30) NULL,
    placa VARCHAR(10) NULL,
    placarotativa VARCHAR(10) NULL,
    seriemotor VARCHAR(20) NULL,
    moneda ENUM('USD', 'PEN') NULL DEFAULT 'USD', -- VENTA
    precioventa DECIMAL(9, 2) NULL,
    disponibilidad ENUM(
        'proceso',
        'libre',
        'separado',
        'vendido',
        'recuperado'
    ) NOT NULL,
    origen ENUM('OCP', 'OLD', 'CTZ') NOT NULL COMMENT 'OCP = Orden de compra (conducto regular), OLD (Contratos anteriores al sistema), CTZ (Cotizado por asesor)',
    estado ENUM('0', '1') NULL DEFAULT '1',
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    eliminado DATETIME NULL,
    CONSTRAINT fk_idmodelo_veh FOREIGN KEY (idmodelo) REFERENCES modelos (idmodelo),
    CONSTRAINT fk_idcombustible_veh FOREIGN KEY (idcombustible) REFERENCES combustibles (idcombustible),
    CONSTRAINT fk_idlocal_veh FOREIGN KEY (idlocal) REFERENCES locales (idlocal),
    CONSTRAINT fk_idlogistica_veh FOREIGN KEY (idlogistica) REFERENCES colaboradores (idcolaborador)
) ENGINE = INNODB;




CREATE TABLE cotizaciones (
    idcotizacion INT AUTO_INCREMENT PRIMARY KEY,
    idformato INT NOT NULL,
    idcliente INT NULL,
    idasesor INT NOT NULL COMMENT 'Colaborador del área de VENTA', -- 
    idvehiculo INT NOT NULL,
    moneda ENUM('PEN', 'USD') NOT NULL,
    precioventa DECIMAL(9, 2) NOT NULL,
    vigenciadias TINYINT NOT NULL COMMENT 'Días válidos de la cotización' DEFAULT 7,
    inicial DECIMAL(9, 2) NOT NULL,
    numcuotas SMALLINT NOT NULL,
    gastosadministrativos DECIMAL(9,2) NOT NULL DEFAULT 0.00 COMMENT 'Gastos administrativos de la cotización',
    valorcuota DECIMAL(9, 2) NOT NULL, -- Se usara en la tabla de cronogramas
    estadocotizacion ENUM('P', 'S','O', 'A', 'C', 'R','CONT') NOT NULL DEFAULT 'P' COMMENT 'Pendiente | Separada ( Ya se realizo un pago )| OBSERVADA  | Aprobada | Cancelada (cliente) | Rechazada (Analista crédito) | CONTRATO',
    comentarios TEXT,
    fechaseguimiento DATETIME NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    fechareactivacion DATETIME NULL,
    CONSTRAINT fk_idformato_cot FOREIGN KEY (idformato) REFERENCES formatocotizacion (idformato),
    CONSTRAINT fk_idcliente_cot FOREIGN KEY (idcliente) REFERENCES clientes (idcliente),
    CONSTRAINT fk_idvehiculo_cot FOREIGN KEY (idvehiculo) REFERENCES  (idvehiculo),
    CONSTRAINT fk_idcolventa_cot FOREIGN KEY (idasesor) REFERENCES colaboradores (idcolaborador)
) ENGINE = INNODB;

ALTER TABLE cotizaciones MODIFY COLUMN estadocotizacion ENUM('P', 'S','O', 'A', 'C', 'R','CONT') NOT NULL DEFAULT 'P' COMMENT 'Pendiente | Separada ( Ya se realizo un pago )| OBSERVADA  | Aprobada | Cancelada (cliente) | Rechazada (Analista crédito) | CONTRATO';

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
    idpago INT AUTO_INCREMENT PRIMARY KEY,
    idcotizacion INT NULL COMMENT 'SOLO TENDRÁ UN VALOR CUANDO EL PAGO SEA EN CONCEPTO "INICIAL DE UNA COTIZACION"',
    idcliente INT NULL COMMENT 'CUANDO SE REALIZA UN PAGO AL CONTADO SE IDENTIFICA QUE CLIENTE REALIZO EL PAGO',
    idconcepto  INT NULL COMMENT 'CONCEPTO DE PAGO',
    idvehiculo  INT NULL COMMENT 'TENDRÁ UN VALOR CUANDO EL CONCEPTO DE PAGO SEA INICIAL O CONTADO',
    idasesorvendedor INT NULL COMMENT 'TENDRÁ UN VALOR CUANDO EL CONCEPTO DE PAGO SEA CONTADO',
    idcronograma INT NULL,
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
    tipo        ENUM('Cuota','Penalidad', 'Otro') NOT NULL DEFAULT 'Cuota',
    CONSTRAINT fk_idcronograma_pagos FOREIGN KEY (idcronograma) REFERENCES cronogramas (idcronograma),
    CONSTRAINT fk_idcuentapago_pagos FOREIGN KEY (idcuentapago) REFERENCES cuentaspago (idcuentapago),
    CONSTRAINT fk_idcolcaja_pagos FOREIGN KEY (idcolcaja) REFERENCES colaboradores (idcolaborador),
    CONSTRAINT fk_idconcepto_pagos FOREIGN KEY(idconcepto) REFERENCES conceptospago(idconcepto),
    CONSTRAINT fk_idvehiculo_pagos FOREIGN KEY(idvehiculo) REFERENCES vehiculos(idvehiculo),
    CONSTRAINT fk_idasesorvendedor_pagos FOREIGN KEY(idasesorvendedor) REFERENCES colaboradores(idcolaborador),
    CONSTRAINT fk_idcliente_pagos  FOREIGN KEY(idcliente) REFERENCES clientes(idcliente)
) ENGINE = InnoDB;

SELECT * FROM pagos;
-- ALTER TABLE pagos ADD COLUMN idcotizacion INT NULL AFTER idpago;

-- ALTER TABLE pagos ADD CONSTRAINT fk_idcotizacion_pagos FOREIGN KEY (idcotizacion) REFERENCES cotizaciones (idcotizacion);

CREATE TABLE cuentaspago (
    idcuentapago INT AUTO_INCREMENT PRIMARY KEY,
    identidadpago INT NOT NULL,
    moneda ENUM('Soles', 'Dolares') NOT NULL,
    numcuenta VARCHAR(35) NOT NULL,
    CONSTRAINT fk_identipago_cuentas FOREIGN KEY (identidadpago) REFERENCES entidadespago (identidadpago)
) ENGINE = InnoDb;

-- ALTER TABLE cuentaspago
-- CHANGE COLUMN monedad moneda ENUM('Soles', 'Dolares') NOT NULL;

CREATE TABLE entidadespago (
    identidadpago INT AUTO_INCREMENT PRIMARY KEY,
    entidad VARCHAR(20) NOT NULL,
    tipo ENUM('Banco', 'Caja', 'Financiera') NOT NULL DEFAULT 'Banco',
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT uk_entidad_epg UNIQUE (entidad)
) ENGINE = INNODB;

-- ALTER TABLE pagos ADD COLUMN  idcliente INT NULL COMMENT 'CUANDO SE REALIZA UN PAGO AL CONTADO SE IDENTIFICA QUE CLIENTE REALIZO EL PAGO' AFTER idpago;
-- ALTER TABLE pagos ADD  CONSTRAINT fk_idcliente_pagos  FOREIGN KEY(idcliente) REFERENCES clientes(idcliente);
-- ALTER TABLE pagos MODIFY COLUMN idcronograma INT NULL;
-- ALTER TABLE pagos ADD COLUMN idconcepto INT NULL AFTER idpago;
-- ALTER TABLE pagos ADD COLUMN idvehiculo INT NULL AFTER idconcepto;
-- ALTER TABLE pagos ADD COLUMN idasesorvendedor INT NULL AFTER idvehiculo;
-- ALTER TABLE pagos ADD CONSTRAINT fk_idconcepto_pagos FOREIGN KEY(idconcepto) REFERENCES conceptospago(idconcepto);
-- ALTER TABLE pagos ADD CONSTRAINT fk_idvehiculo_pagos FOREIGN KEY(idvehiculo) REFERENCES vehiculos(idvehiculo);
-- ALTER TABLE pagos MODIFY COLUMN tipo ENUM('Cuota','Penalidad', 'Otro') NOT NULL DEFAULT 'Cuota'tipo        ENUM('Cuota','Penalidad', 'Otro') NOT NULL DEFAULT 'Cuota',;


SELECT * FROM pagos;
    SHoW COLUMNS from pagos;
SELECT * FROM clientes;


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



SELECT * FROM conceptospago;
SELECT * FROM cotizaciones;


SELECT * FROM vehiculos WHERE disponibilidad = 'libre';
SELECT * FROM vehiculos WHERE disponibilidad != 'PROCESO' AND  disponibilidad != 'libre';

SELECT * FROM vehiculos;


SELECT 
              idcotizacion,
              idvehiculo,
              tipocotizacion,
              CONCAT(vehiculo, ' / ', color) AS vehiculo,
              precioventa,
              moneda,
              inicial,
              nombrecliente,
              documento,
              telefono,
              direccion,
              numcuotas,
              valorcuota,
              estadocotizacion
            FROM vwGetAllCotizacion
            WHERE idcotizacion = :idcotizacion LIMIT 1








SELECT p.idpago, p.fechapago, p.amortizacion, p.saldorestante, c.concepto
FROM pagos p
INNER JOIN conceptospago c ON p.idconcepto = c.idconcepto
WHERE p.idvehiculo = ? 
ORDER BY p.fechapago ASC;

SELECT 
    v.idvehiculo,
    c.idcotizacion,
    c.inicial AS monto_inicial,
    COALESCE(SUM(p.amortizacion), 0) AS totalpagado,
    COALESCE(
        (
            SELECT pp.saldorestante
            FROM pagos pp
            WHERE pp.idvehiculo = v.idvehiculo
              AND pp.idconcepto = (SELECT idconcepto FROM conceptospago WHERE concepto = 'Inicial' LIMIT 1)
            ORDER BY pp.fechapago DESC, pp.idpago DESC
            LIMIT 1
        ),
        c.inicial
    ) AS saldorestante
FROM cotizaciones c
INNER JOIN vehiculos v 
    ON v.idvehiculo = c.idvehiculo
LEFT JOIN pagos p 
    ON p.idvehiculo = v.idvehiculo
   AND p.idconcepto = (SELECT idconcepto FROM conceptospago WHERE concepto = 'Inicial' LIMIT 1)
WHERE c.estadocotizacion = 'A'  
  AND c.idcotizacion = 20
GROUP BY v.idvehiculo, c.idcotizacion, c.inicial;




SELECT * FROM cuentaspago;
SELECT * FROM entidadespago;


-- TRANSFERENCIA
INSERT INTO pagos(idconcepto,idvehiculo,idcuentapago,mediopago,numerotransaccion,fechapago,amortizacion,saldorestante,comprobante,tipo)

VALUES(2,167,1,'Transferencia Bancaria','05552555555',NOW(),3000,36000,'comprobantes/comprobante_68e68f970c0bc_bbva.webp','Otro');



--EFETCIVO
INSERT INTO pagos(idconcepto,idvehiculo,mediopago,fechapago,amortizacion,saldorestante,tipo)
VALUES(2,167,'Efectivo',NOW(),3000,33000,'Otro');




SELECT * FROM pagos;


UPDATE pagos SET saldorestante = 39000 WHERE idpago = 541;




-- PAGOS REALIZADOS A UAN COTIZACION:

SELECT 
    p.fechapago,
    e.entidad AS entidadbancaria,
    cp.numcuenta,
    cp.moneda,
    p.mediopago,
    p.numerotransaccion,
    p.amortizacion,
    p.saldorestante,
    p.comprobante,
    p.observacion
FROM cotizaciones c
INNER JOIN vehiculos v 
    ON v.idvehiculo = c.idvehiculo
INNER JOIN pagos p 
    ON p.idvehiculo = v.idvehiculo
   AND p.idconcepto = (SELECT idconcepto FROM conceptospago WHERE concepto = 'Inicial' LIMIT 1)
LEFT JOIN cuentaspago cp 
    ON p.idcuentapago = cp.idcuentapago
LEFT JOIN entidadespago e 
    ON cp.identidadpago = e.identidadpago
WHERE c.idcotizacion = 34
ORDER BY p.fechapago ASC, p.idpago ASC;


SELECT idconcepto,concepto FROM conceptospago;
SELECT identidadpago, entidad FROM entidadespago ORDER BY entidad ASC;
SELECT * FROM cuentasPago;
SELECT * FROM pagos;

SELECT * FROM cotizaciones;


SELECT * FROM vehiculos WHERE idvehiculo = 70;

SELECT * FROM vehiculos;
UPDATE cotizaciones SET estadocotizacion = 'P' WHERE idcotizacion = 38;



SELECT COUNT(*) AS cnt
FROM pagos p
JOIN conceptospago cp ON p.idconcepto = cp.idconcepto
WHERE p.idcotizacion = 37
  AND cp.concepto = 'Inicial';

  SELECT * FROM pagos;



SELECT 
SUM(amortizacion) amortizacion,
cot.inicial
FROM pagos p 
JOIN cotizaciones cot ON p.idcotizacion = cot.idcotizacion
WHERE p.idcotizacion = 37 AND cot.estadocotizacion = 'A';


SELECT 
    cot.idcotizacion,
    cot.inicial,
    COALESCE(SUM(p.amortizacion),0) AS total_pagado,
    CASE 
        WHEN cot.estadocotizacion IN ('A','P') 
             AND COALESCE(SUM(p.amortizacion),0) >= cot.inicial
        THEN 1 ELSE 0 
    END AS habilitar_contrato
FROM cotizaciones cot
LEFT JOIN pagos p ON p.idcotizacion = cot.idcotizacion
WHERE cot.idcotizacion = 46
GROUP BY cot.idcotizacion, cot.inicial, cot.estadocotizacion;



SELECT * FROM pagos;

UPDATE pagos SET amortizacion = 20000, saldorestante = 10000 WHERE idpago = 571;

SELECT * FROM fichasolicitud;

DELETE FROM fichasolicitud;

UPDATE cotizaciones SET estadocotizacion = 'S' WHERE idcotizacion IN(39);

SELECT * FROM cotizaciones;

SELECT * FROM pagos;
SELECT * FROM contratos;

DELETE FROM cronogramas;


DELETE FROM contratos;


SELECT * FROM cronogramas;

SELECT * FROM conceptospago;
SELECT * FROM contratos;









SELECT * FROM locales;
SELECT * FROM distritos;
SELECT * FROM provincias;

UPDATE contratos SET estado = 'ACT';

SELECT * FROM cotizaciones;