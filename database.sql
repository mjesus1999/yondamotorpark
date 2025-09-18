CREATE DATABASE motorpark;

USE motorpark;

-- Necesitaremos de una función que calcula la hora actual, esto será
-- útil cuando la aplicación se aloje en un servidor remoto
/*
DELIMITER $$
CREATE FUNCTION GETDATE()
RETURNS DATETIME
DETERMINISTIC
BEGIN
RETURN NOW() - INTERVAL 5 HOUR;
END $$
DELIMITER ;
*/

-- Tabla controlar los de las personas y su acceso al sistema
CREATE TABLE departamentos (
    iddepartamento INT PRIMARY KEY AUTO_INCREMENT,
    departamento VARCHAR(150) NOT NULL
) ENGINE = INNODB;

CREATE TABLE provincias (
    idprovincia INT PRIMARY KEY AUTO_INCREMENT,
    iddepartamento INT NOT NULL,
    provincia VARCHAR(150) NOT NULL,
    CONSTRAINT uk_iddepartamento_pro FOREIGN KEY (iddepartamento) REFERENCES departamentos (iddepartamento)
) ENGINE = INNODB;

CREATE TABLE distritos (
    iddistrito INT PRIMARY KEY AUTO_INCREMENT,
    idprovincia INT NOT NULL,
    distrito VARCHAR(150) NOT NULL,
    ubigeoinei VARCHAR(12) NOT NULL,
    CONSTRAINT fk_idprovincia_dis FOREIGN KEY (idprovincia) REFERENCES provincias (idprovincia)
) ENGINE = INNODB;

CREATE TABLE personas (
    idpersona INT PRIMARY KEY AUTO_INCREMENT,
    iddistrito INT NULL,
    apellidos VARCHAR(70) NOT NULL,
    nombres VARCHAR(70) NOT NULL,
    tipodoc ENUM('DNI', 'CEX', 'PAS') NOT NULL DEFAULT 'DNI',
    nrodoc VARCHAR(12) NOT NULL,
    genero ENUM('M', 'F') NOT NULL,
    fechanac DATE NULL,
    estadocivil ENUM(
        'SOL',
        'CAS',
        'VDO',
        'DVC',
        'CNV'
    ) NULL COMMENT 'Soltero, casado, viudo, divorciado y conviviente',
    email VARCHAR(150) NULL,
    direccion VARCHAR(200) NULL,
    referencia VARCHAR(200) NULL,
    latitud VARCHAR(20) NULL,
    longitud VARCHAR(20) NULL,
    telprimario CHAR(9) NOT NULL,
    telalternativo CHAR(9) NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT uk_nrodoc UNIQUE (tipodoc, nrodoc),
    CONSTRAINT fk_iddistrito_per FOREIGN KEY (iddistrito) REFERENCES distritos (iddistrito)
) ENGINE = INNODB;

USE motorpark;
SELECT * FROM personas;
SELECT * FROM empresas;
SHOW COLUMNS FROM personas;
-- ALTER TABLE personas ADD COLUMN latitud VARCHAR(20) NULL;
-- ALTER TABLE personas ADD COLUMN longitud VARCHAR(20) NULL;

CREATE TABLE empresas (
    idempresa INT PRIMARY KEY AUTO_INCREMENT,
    iddistrito INT NOT NULL,
    razonsocial VARCHAR(300) NOT NULL,
    nombrecomercial VARCHAR(100) NOT NULL,
    ruc CHAR(11) NOT NULL UNIQUE,
    representante VARCHAR(50) NOT NULL,
    email VARCHAR(100) NULL UNIQUE,
    direccion VARCHAR(300) NULL,
    referencia VARCHAR(280) NULL,
    latitud VARCHAR(20) NULL,
    longitud VARCHAR(20) NULL,
    telprimario VARCHAR(12) NOT NULL UNIQUE,
    telsecundario VARCHAR(12) NULL UNIQUE,
    estado ENUM('ACT', 'INACT') DEFAULT 'ACT' NOT NULL,
    CONSTRAINT fk_iddistrito_empre FOREIGN KEY (iddistrito) REFERENCES distritos (iddistrito)
) ENGINE = INNODB;


-- ALTER TABLE empresas MODIFY COLUMN direccion VARCHAR(300) NULL;
-- ALTER TABLE empresas MODIFY COLUMN telprimario VARCHAR(12) NOT NULL UNIQUE;
-- ALTER TABLE empresas MODIFY COLUMN telsecundario VARCHAR(12) NULL UNIQUE;

CREATE TABLE clientes (
    idcliente INT PRIMARY KEY AUTO_INCREMENT,
    idpersona INT NULL,
    idempresa INT NULL,
    idcolregistra INT NULL,
    idcolactualiza INT NULL,
    tipocliente ENUM('P', 'E') NOT NULL,
    estado ENUM('ACT', 'INACT') DEFAULT 'ACT' NOT NULL,
    CONSTRAINT fk_idpersona_client FOREIGN KEY (idpersona) REFERENCES personas (idpersona),
    CONSTRAINT fk_idcolregistra_client FOREIGN KEY (idcolregistra) REFERENCES colaboradores (idcolaborador),
    CONSTRAINT fk_idcolactualiza_client FOREIGN KEY (idcolactualiza) REFERENCES colaboradores (idcolaborador)
) ENGINE = INNODB;

/* ALTER TABLE clientes ADD COLUMN estado ENUM('ACT', 'INACT') DEFAULT 'ACT' NOT NULL; */

CREATE TABLE areas (
    idarea INT AUTO_INCREMENT PRIMARY KEY,
    area VARCHAR(40) NOT NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT uk_area_are UNIQUE (area)
) ENGINE = INNODB;

CREATE TABLE cargos (
    idcargo INT AUTO_INCREMENT PRIMARY KEY,
    idarea INT NOT NULL,
    cargo VARCHAR(40) NOT NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT fk_idarea_car FOREIGN KEY (idarea) REFERENCES areas (idarea)
) ENGINE = INNODB;

CREATE TABLE contratoslaborales (
    idcontratolaboral INT AUTO_INCREMENT PRIMARY KEY,
    idpersona INT NOT NULL,
    idcargo INT NOT NULL,
    fechainicio DATE NOT NULL,
    fechafin DATE NULL,
    tipocontrato ENUM('P', 'R') NOT NULL COMMENT 'Planilla - Recibos',
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT fk_idpersona_cla FOREIGN KEY (idpersona) REFERENCES personas (idpersona),
    CONSTRAINT fk_idcargocla FOREIGN KEY (idcargo) REFERENCES cargos (idcargo)
) ENGINE = INNODB;


CREATE TABLE colaboradores (
    idcolaborador INT AUTO_INCREMENT PRIMARY KEY,
    idcontratolaboral INT NOT NULL,
    usernick VARCHAR(40) NOT NULL,
    userpassword VARCHAR(70) NOT NULL,
    avatar VARCHAR(150) NULL,
    ultimoacceso DATETIME NULL,
    habilitado ENUM('S', 'N') NOT NULL DEFAULT 'S',
    restriccionhoraria ENUM('S', 'N') 	NOT NULL DEFAULT 'S',
    estado ENUM('0', '1') NULL DEFAULT '1',
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT fk_idcontratolaboral_col FOREIGN KEY (idcontratolaboral) REFERENCES contratoslaborales (idcontratolaboral),
    CONSTRAINT uk_usernick_col UNIQUE (usernick)
) ENGINE = INNODB;


-- AFTER ES PARA INDICAR EN QUE ORDEN VA.
--  ALTER TABLE colaboradores ADD COLUMN  AFTER `habilitado`;
-- ALTER TABLE colaboradores ADD COLUMN estado ENUM('0', '1') NULL DEFAULT '1' AFTER `restriccionhoraria`;

CREATE TABLE concesionarios (
    idconcesionario INT AUTO_INCREMENT PRIMARY KEY,
    ruc CHAR(11) NOT NULL,
    razonsocial VARCHAR(350) NOT NULL,
    nombrecomercial VARCHAR(150) NOT NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT uk_ruc_con UNIQUE (ruc)
) ENGINE = INNODB;

CREATE TABLE tiendas (
    idtienda INT AUTO_INCREMENT PRIMARY KEY,
    iddistrito INT NOT NULL,
    idconcesionario INT NOT NULL,
    direccion VARCHAR(300) NULL,
    email VARCHAR(200) NULL,
    telefono VARCHAR(12) NOT NULL,
    contacto VARCHAR(100) NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT fk_iddistrito_tnd FOREIGN KEY (iddistrito) REFERENCES distritos (iddistrito),
    CONSTRAINT fk_idconcesionario_tnd FOREIGN KEY (idconcesionario) REFERENCES concesionarios (idconcesionario)
) ENGINE = INNODB;

CREATE TABLE marcas (
    idmarca INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(40) NOT NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT uk_marca_mar UNIQUE (marca)
) ENGINE = INNODB;

CREATE TABLE tipovehiculos (
    idtipovehiculo INT AUTO_INCREMENT PRIMARY KEY,
    tipovehiculo VARCHAR(40) NOT NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT uk_tipovehiculo_tve UNIQUE (tipovehiculo)
) ENGINE = INNODB;

-- El unique para modelos debe ser tipovehiculo + marca + año
CREATE TABLE modelos (
    idmodelo INT AUTO_INCREMENT PRIMARY KEY,
    idtipovehiculo INT NOT NULL,
    idmarca INT NOT NULL,
    modelo VARCHAR(40) NOT NULL,
    anio CHAR(4) NOT NULL,
    imagenreferencial VARCHAR(200) NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT fk_idtipovehiculo_mod FOREIGN KEY (idtipovehiculo) REFERENCES tipovehiculos (idtipovehiculo),
    CONSTRAINT fk_idmarca_mod FOREIGN KEY (idmarca) REFERENCES marcas (idmarca),
    CONSTRAINT uk_modelo_mod UNIQUE (idmarca, modelo, anio)
) ENGINE = INNODB;

CREATE TABLE combustibles (
    idcombustible INT AUTO_INCREMENT PRIMARY KEY,
    combustible VARCHAR(40) NOT NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT uk_combustible_cmb UNIQUE (combustible)
) ENGINE = INNODB;

CREATE TABLE motorpark (
    idmotorpark INT AUTO_INCREMENT PRIMARY KEY,
    ruc CHAR(11) NOT NULL,
    razonsocial VARCHAR(300) NOT NULL,
    nombrecomercial VARCHAR(100) NOT NULL,
    representante VARCHAR(100) NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT uk_ruc_mtp UNIQUE (ruc)
) ENGINE = INNODB;

CREATE TABLE locales (
    idlocal INT AUTO_INCREMENT PRIMARY KEY,
    tienda VARCHAR(40) NOT NULL,
    iddistrito INT NOT NULL,
    idmotorpark INT NOT NULL,
    principal ENUM('S', 'N') NOT NULL,
    responsable VARCHAR(100) NOT NULL,
    correo VARCHAR(200) NULL,
    direccion VARCHAR(300) NULL,
    telefono VARCHAR(12) NULL,
    latitud VARCHAR(20) NULL,
    longitud VARCHAR(20) NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT fk_iddistrito_loc FOREIGN KEY (iddistrito) REFERENCES distritos (iddistrito),
    CONSTRAINT fk_idmotorpark_loc FOREIGN KEY (idmotorpark) REFERENCES motorpark (idmotorpark)
) ENGINE = INNODB;

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



-- ALTER TABLE vehiculos ADD COLUMN estado ENUM('0', '1') NULL DEFAULT '1';

--ALTER TABLE vehiculos ADD COLUMN eliminado DATETIME NULL;
--SHOW COLUMNS FROM vehiculos;
-- ALTER TABLE vehiculos MODIFY COLUMN idlogistica INT NULL;
-- ALTER TABLE vehiculos DROP CONSTRAINT fk_idmodelo_veh;

-- ALTER TABLE vehiculos DROP CONSTRAINT fk_idmodelo_veh;
-- ALTER TABLE vehiculos
-- ADD CONSTRAINT fk_idmodelo_veh FOREIGN KEY (idmodelo) REFERENCES modelos (idmodelo);

-- Cuando se compra un vehículo, este además de su valor, supone pagos adicioanles como:
-- Tarjeta de propiedad y placa, Flete picanto, gastos administrativos
-- No se requiere indiciar la moneda porque esto se especifica al momento de realizar la comrpa
CREATE TABLE gastos (
    idgasto INT AUTO_INCREMENT PRIMARY KEY,
    idvehiculo INT NOT NULL,
    descripcion VARCHAR(300) NOT NULL,
    importe DECIMAL(9, 2) NOT NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT fk_idvehiculo_gst FOREIGN KEY (idvehiculo) REFERENCES vehiculos (idvehiculo)
) ENGINE = INNODB;

CREATE TABLE ordenescompra (
    idordencompra INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Será el número de orden de compra',
    idtienda INT NOT NULL,
    idlogistica INT NOT NULL,
    moneda ENUM('USD', 'PEN') NOT NULL,
    serie CHAR(4) NOT NULL COMMENT 'Será el año',
    emision DATE NOT NULL COMMENT 'Se creó la orden de compra',
    aprobacion DATE NULL COMMENT 'Gerencia aprueba la orden',
    presentacion DATE NULL COMMENT 'Logística envía la orden al concesionario',
    anulacion DATE NULL COMMENT 'Logística anula la orden de compra',
    numstock VARCHAR(20) NULL COMMENT 'Este dato será provisto por el concesionario - opcional',
    observaciones VARCHAR(400) NULL,
    estado ENUM(
        'emitido',
        'proceso',
        'anulado',
        'pagado'
    ) NOT NULL DEFAULT 'emitido',
    creado DATETIME NOT NULL DEFAULT NOW(),
    fechanulado DATETIME NULL,
    facturado ENUM('S', 'N') NOT NULL DEFAULT 'N', -- Identificar que ya se haya registrado la factura que manda el Concesionario
    CONSTRAINT fk_idtienda_ocp FOREIGN KEY (idtienda) REFERENCES tiendas (idtienda),
    CONSTRAINT fk_idlogistica_ocp FOREIGN KEY (idlogistica) REFERENCES colaboradores (idcolaborador)
) ENGINE = INNODB;

USE motorpark;
SELECT * FROM ordenescompra;

--ALTER TABLE ordenescompra ADD COLUMN  facturado ENUM('S','N') NOT NULL DEFAULT 'N' ;
-- ALTER TABLE ordenescompra ADD COLUMN creado DATETIME NOT NULL DEFAULT NOW();
-- ALTER TABLE ordenescompra ADD COLUMN fechanulado DATETIME NULL;
--ALTER TABLE ordenescompra MODIFY COLUMN estado ENUM('emitido','proceso','anulado','pagado') NOT NULL DEFAULT 'emitido';

CREATE TABLE pagosOC (
    idpagooc INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    idorden INT NOT NULL, -- ID OC
    idlogistica INT NOT NULL, -- Persona que registro el pago
    identidadpago INT NOT NULL,
    fecharealpago DATETIME NOT NULL,
    numtransaccion VARCHAR(20) NOT NULL,
    moneda ENUM('USD', 'PEN') NOT NULL, -- Moneda en que se realizo el pago
    tipocambio DECIMAL(5,2) NULL, -- Tipo de cambio si es  que se paga en SOLES 
    valorUSD DECIMAL(10,2) NULL,
    amortizacion DECIMAL(10, 2) NOT NULL, -- Lo que se ha adelantado
    saldo DECIMAL(10, 2) NOT NULL, -- El saldo a pagar o lo que falta pagar si es que se ha hecho amortización
    comprobante VARCHAR(300) NOT NULL, -- Ruta del comprobante
    observaciones VARCHAR(400) NULL,
    modificado DATETIME NULL,   
    fecha DATETIME NOT NULL DEFAULT NOW(), -- Fecha y hora de que se regsitro el pago
    CONSTRAINT fk_idlo_pagoOC FOREIGN KEY (idlogistica) REFERENCES colaboradores (idcolaborador),
    CONSTRAINT fk_idorde_pagosOC FOREIGN KEY (idorden) REFERENCES ordenescompra (idordencompra),
    CONSTRAINT fk_identidadpago_pagosOC FOREIGN KEY (identidadpago) REFERENCES entidadespago (identidadpago);
) ENGINE = InnoDB;

-- ALTER TABLE pagosOC CHANGE COLUMN valorSoles valorUSD DECIMAL(10,2) NULL AFTER tipocambio;
-- UPDATE pagosOC SET identidadpago = 1;
-- ALTER TABLE pagosOC ADD COLUMN identidadpago INT NOT NULL AFTER idlogistica;
-- 
-- ALTER TABLE pagoSoc ADD COLUMN numtransaccion VARCHAR(20) NOT NULL AFTER fecharealpago;
-- ALTER TABLE pagosOC ADD COLUMN moneda ENUM('USD', 'PEN') NOT NULL AFTER numtransaccion;
-- ALTER TABLE pagosOC ADD COLUMN tipocambio DECIMAL(5,2) NULL AFTER moneda;
-- ALTER TABLE pagosOC ADD COLUMN observaciones VARCHAR(400) NULL AFTER comprobante;





CREATE TABLE detordencompra (
    iddetordencompra INT AUTO_INCREMENT PRIMARY KEY,
    idordencompra INT NOT NULL,
    idvehiculo INT NOT NULL,
    preciocompra DECIMAL(9, 2) NOT NULL, -- PRECIO
    escorrecto ENUM('S', 'N') NULL COMMENT 'Define si el vehículo llego de acuerdo a los datos de la factura',
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL CONSTRAINT fk_idordencompra_doc FOREIGN KEY (idordencompra) REFERENCES ordenescompra (idordencompra),
    estado ENUM('0','1') NOT NULL DEFAULT '1';

CONSTRAINT fk_idvehiculo_doc FOREIGN KEY (idvehiculo) REFERENCES vehiculos (idvehiculo),
    CONSTRAINT uk_idvehiculo_doc UNIQUE (idvehiculo) -- Relación uno a uno
) ENGINE = INNODB;

-- ALTER TABLE detordencompra
-- ADD COLUMN estado ENUM('0', '1') NOT NULL DEFAULT '1';

--ALTER TABLE detordencompra ADD COLUMN creado   DATETIME        NOT NULL DEFAULT NOW();

--ALTER TABLE detordencompra ADD COLUMN modificado  DATETIME        NULL;

-- show COLUMNS FROM detordencompra;

-- SELECT * FROM detordencompra;

CREATE TABLE compras (
    idcompra INT AUTO_INCREMENT PRIMARY KEY,
    idorden INT NOT NULL COMMENT 'De esta clave se obtendrán las datos de los vehículos',
    idlogistica INT NOT NULL COMMENT 'Colaborador que realiza el registro',
    fechacompra DATE NOT NULL,
    fecharecepcion DATE NULL,
    tipodoc ENUM('B', 'F') NOT NULL DEFAULT 'F' COMMENT 'Boleta o Factura', -- LA MAYORIA ES FACTURA
    serie VARCHAR(10) NOT NULL,
    numdocumento VARCHAR(30) NOT NULL,
    rutadoc VARCHAR(200) NULL, -- RUTA DEL PDF
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT fk_idorden_cmp FOREIGN KEY (idorden) REFERENCES ordenescompra (idordencompra),
    CONSTRAINT fk_idlogistica_cmp FOREIGN KEY (idlogistica) REFERENCES colaboradores (idcolaborador)
) ENGINE = INNODB;

-- USE motorpark2;

-- SELECT * FROM COMPRAS;
-- SELECT * FROM pagos;
--SHOW COLUMNS FROM compras

--ALTER TABLE compras DROP COLUMN pathxml

--ALTER TABLE compras ADD COLUMN rutadoc VARCHAR(200) NULL;
CREATE TABLE formatocotizacion (
    idformato INT PRIMARY KEY AUTO_INCREMENT,
    tipocotizacion VARCHAR(200) NOT NULL,
    fechainicio DATE NOT NULL,
    fechafin DATE NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL
) ENGINE = INNODB;

CREATE TABLE requisitos (
    idrequisito INT PRIMARY KEY AUTO_INCREMENT,
    requisito VARCHAR(500) NOT NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT uk_requisito_req UNIQUE (requisito)
) ENGINE = INNODB;

-- drop table detallerequisitos;
CREATE TABLE detallerequisitos (
    iddetrequisito INT PRIMARY KEY AUTO_INCREMENT,
    idformato INT NOT NULL,
    idrequisito INT NOT NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    CONSTRAINT fk_idformato_dre FOREIGN KEY (idformato) REFERENCES formatocotizacion (idformato),
    CONSTRAINT fk_idrequisito_dre FOREIGN KEY (idrequisito) REFERENCES requisitos (idrequisito)
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
    estadocotizacion ENUM('P', 'E', 'A', 'C', 'R') NOT NULL DEFAULT 'P' COMMENT 'Pendiente | Evaluación | Aprobada | Cancelada (cliente) | Rechazada (Analista crédito)',
     comentarios TEXT,
      fechaseguimiento DATETIME NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    fechareactivacion DATETIME NULL,
    CONSTRAINT fk_idformato_cot FOREIGN KEY (idformato) REFERENCES formatocotizacion (idformato),
    CONSTRAINT fk_idcliente_cot FOREIGN KEY (idcliente) REFERENCES clientes (idcliente),
    CONSTRAINT fk_idvehiculo_cot FOREIGN KEY (idvehiculo) REFERENCES + (idvehiculo),
    CONSTRAINT fk_idcolventa_cot FOREIGN KEY (idasesor) REFERENCES colaboradores (idcolaborador)
) ENGINE = INNODB;
USE motorpark;

-- ALTER TABLE cotizaciones ADD COLUMN comentarios TEXT AFTER estadocotizacion;
-- --  ALTER TABLE cotizaciones ADD COLUMN fechaseguimiento DATETIME NULL AFTER comentarios;


-- ALTER TABLE cotizaciones ADD COLUMN gastosadministrativos DECIMAL(9,2) NOT NULL DEFAULT 0.00 COMMENT 'Gastos administrativos de la cotización' AFTER valorcuota;
--  ALTER TABLE cotizaciones ADD COLUMN fechareactivacion DATETIME NULL AFTER modificado;
CREATE TABLE contratos (
    idcontrato INT AUTO_INCREMENT PRIMARY KEY,
    idlocal INT NOT NULL,
    idcotizacion INT NOT NULL,
    idlogistica INT NULL,
    fechainicio DATE NOT NULL,
    diapago TINYINT NOT NULL,
    escredito ENUM('S', 'N') NOT NULL DEFAULT 'S',
    fecharevision DATE NULL,
    penalidadbase DECIMAL(10, 2) NOT NULL DEFAULT 0.1,
    observaciones VARCHAR(350) NULL,
    estado ENUM('ACT', 'INACT') DEFAULT 'ACT',
    CONSTRAINT fk_idlocal_contrato FOREIGN KEY (idlocal) REFERENCES locales (idlocal),
    CONSTRAINT fk_idcotizacion_contrato FOREIGN KEY (idcotizacion) REFERENCES cotizaciones (idcotizacion),
    CONSTRAINT fk_idlogistica_contrato FOREIGN KEY (idlogistica) REFERENCES colaboradores (idcolaborador)
) ENGINE = InnoDB;


-- ALTER TABLE contratos
-- ADD COLUMN penalidadbase DECIMAL(10, 2) NOT NULL DEFAULT 0.1;

-- ALTER TABLE contratos
-- MODIFY COLUMN estado ENUM('ACT', 'INACT') DEFAULT 'ACT';

CREATE TABLE cronogramas (
    idcronograma INT AUTO_INCREMENT PRIMARY KEY,
    idcontrato INT NOT NULL, -- El valor de cuota se encuentra en contrato <---> cotizaciones
    fechapago DATE NOT NULL,
    interes DECIMAL(10, 2) NOT NULL,
    abonocapital DECIMAL(10, 2) NOT NULL,
    numcuota TINYINT NOT NULL,
    penalidad DECIMAL(10, 2) NULL,
    saldocapital DECIMAL(10, 2) NOT NULL,
    aplicapenalidad ENUM('S', 'N') NOT NULL DEFAULT 'N',
    estado ENUM(
        'Pendiente',
        'Pagado',
        'Vencido'
    ) DEFAULT 'Pendiente',
    CONSTRAINT fk_idcont_cronogramas FOREIGN KEY (idcontrato) REFERENCES contratos (idcontrato)
) ENGINE = InnoDB;

-- ALTER TABLE cronogramas
-- MODIFY COLUMN penalidad DECIMAL(10, 2) NULL DEFAULT 0;

ALTER TABLE cronogramas
MODIFY COLUMN estado ENUM(
    'Pendiente',
    'Pagado',
    'Vencido'
) DEFAULT 'Pendiente';

-- ALTER TABLE cronogramas MODIFY COLUMN interes DECIMAL(10, 2) NOT NULL;

-- ALTER TABLE cronogramas
-- MODIFY COLUMN abonocapital DECIMAL(10, 2) NOT NULL;

-- USE motorpark;

CREATE TABLE pagos (
    idpago INT AUTO_INCREMENT PRIMARY KEY,
    idcronograma INT NOT NULL,
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

-- ALTER TABLE pagos MODIFY COLUMN comprobante VARCHAR(200) NULL;
-- ALTER TABLE pagos MODIFY COLUMN numerotransaccion VARCHAR(30) NULL;
-- ADD COLUMN tipo ENUM('Cuota', 'Penalidad') NOT NULL DEFAULT 'Cuota';
-- -- USE motorpark;
-- SELECT * FROM pagos;

--     use motorpark2;

-- SHOW COLUMNS FROM pagos;

-- ALTER TABLE pagos MODIFY COLUMN idcuentapago INT NULL;

-- ALTER TABLE pagos
-- MODIFY COLUMN saldorestante DECIMAL(10, 2) NULL;

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






-- DB DE DEYANIRA:


CREATE TABLE accesos (

  idaccesos 			INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  idcargo 				INT NOT NULL,
  modulo 				VARCHAR(50) NOT NULL,
  permisos 				TINYINT(1) NOT NULL DEFAULT 0,
  creado 			    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  modificado 			DATETIME DEFAULT NULL,
  CONSTRAINT fk_accesos_idcargo FOREIGN KEY (idcargo) REFERENCES cargos (idcargo)

)ENGINE = INNODB;

CREATE TABLE seguimientos_morosos (
    idseguimiento INT AUTO_INCREMENT PRIMARY KEY,
    idcontrato INT NOT NULL,
    tipo ENUM('documento','evidencia') NOT NULL,
    observaciones TEXT NOT NULL,
    evidencia VARCHAR(255) NULL,
    fecha_seguimiento DATETIME NOT NULL,
    usuario_registro INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_seguimiento_contrato (idcontrato),
    INDEX idx_seguimiento_fecha (fecha_seguimiento),
    INDEX idx_seguimiento_usuario (usuario_registro),
    CONSTRAINT fk_seguimiento_contrato FOREIGN KEY (idcontrato) REFERENCES contratos(idcontrato) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_seguimiento_colaborador FOREIGN KEY (usuario_registro) REFERENCES colaboradores(idcolaborador) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB;



CREATE TABLE cotizacion_financiamiento (
    idfinanciamiento INT AUTO_INCREMENT PRIMARY KEY,
    idcotizacion INT NOT NULL,
    numcuotas SMALLINT NOT NULL,
    inicial DECIMAL(9,2) NOT NULL DEFAULT 0,
    valorcuota DECIMAL(9,2) NOT NULL,
    moneda ENUM('PEN','USD') NOT NULL DEFAULT 'PEN',
    precioventa DECIMAL(12,2) NOT NULL DEFAULT 0,
    creado DATETIME NOT NULL DEFAULT NOW(),
    CONSTRAINT fk_cotfin_cot FOREIGN KEY (idcotizacion) REFERENCES cotizaciones (idcotizacion) ON DELETE CASCADE
) ENGINE=INNODB;



CREATE TABLE conceptoegreso(
    idconceptoegreso INT PRIMARY KEY AUTO_INCREMENT,
    concepto VARCHAR(150) NOT NULL,
    descripcion VARCHAR(350) NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT uk_concepto_egreso UNIQUE (concepto)
)ENGINE = INNODB;

CREATE TABLE egresos(
    idegreso INT PRIMARY KEY AUTO_INCREMENT,
    idconceptoegreso INT NOT NULL,
    idcolacaja INT NOT NULL COMMENT 'Colaborador que registra el egreso',
    idcolsolicitante INT NOT NULL COMMENT 'Colaborador que solicita el egreso',
    monto DECIMAL(10,2) NOT NULL,
    comentario VARCHAR(300) NULL,
    requierecomprobante ENUM('S','N') NOT NULL DEFAULT 'N',
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT fk_idconcepto_egreso FOREIGN KEY (idconceptoegreso) REFERENCES conceptoegreso (idconceptoegreso),
    CONSTRAINT fk_colaborador_egreso_registra FOREIGN KEY (idcolsolicitante) REFERENCES colaboradores (idcolaborador),
    CONSTRAINT fk_colaborador_egreso_solicita FOREIGN KEY (idcolsolicitante) REFERENCES colaboradores (idcolaborador)

)ENGINE = INNODB;

CREATE TABLE comprobantes(
    idcomprobante INT PRIMARY KEY AUTO_INCREMENT,
    idegreso INT NOT NULL,
    idproovedor INT NOT NULL,
    tipodoc ENUM('B','F') NOT NULL COMMENT  'Boleta, Factura',
    -- rucproovedor CHAR(11) NOT NULL COMMENT 'RUC del proovedor',
    serie VARCHAR(50) NOT NULL,
    numdocumento VARCHAR(50) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,   
    cargadocontabilidad ENUM('S','N') NOT NULL DEFAULT 'N' COMMENT 'Indica si ya se cargo a contabilidad',
    rutacomprobante VARCHAR(255) NOT NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT fk_egreso_comprobante FOREIGN KEY (idegreso) REFERENCES egresos (idegreso),
    CONSTRAINT fk_proovedor_comprobante FOREIGN KEY (idproovedor) REFERENCES proovedores (idproovedor),
    CONSTRAINT uk_serie_numdoc_proovedor UNIQUE (idproovedor, tipodoc, serie, numdocumento)
)ENGINE = INNODB;

-- ALTER TABLE comprobantes ADD COLUMN idproovedor INT NOT NULL AFTER idegreso;
-- ALTER TABLE comprobantes ADD CONSTRAINT fk_proovedor_comprobante FOREIGN KEY (idproovedor) REFERENCES proovedores (idproovedor);
-- ALTER TABLE comprobantes DROP COLUMN rucproovedor;


CREATE TABLE proovedores(
    idproovedor INT PRIMARY KEY AUTO_INCREMENT,
    razonsocial VARCHAR(300) NOT NULL,
    nombrecomercial VARCHAR(150) NOT NULL,
    ruc CHAR(11) NOT NULL UNIQUE,
    telefono VARCHAR(12) NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT uk_ruc_proovedor UNIQUE (ruc)
)ENGINE = INNODB;

SELECT * FROM proovedores;

-- INSERT INTO proovedores (razonsocial, nombrecomercial, ruc, telefono) VALUES
-- ('SHALOM EMPRESARIAL S.A.C.', 'Shalom', '20512528458', '987654321')

INSERT INTO proovedores (razonsocial, nombrecomercial, ruc, telefono) VALUES
('TRANSPORTES Y SERVICIOS GENERALES S.A.C.', 'TransServ', '20567891234', '912345678'),
('SERVICIOS ADMINISTRATIVOS INTEGRALES EIRL', 'ServAdmin', '20678912345', '923456789'),
('MANTENIMIENTO Y LOGÍSTICA S.A.C.', 'ManLog', '20789123456', '934567890'),
('SOLUCIONES EMPRESARIALES S.A.C.', 'SolEmp', '20891234567', '945678901'),
('GESTIÓN Y SERVICIOS S.A.C.', 'GesServ', '20912345678', '956789012'),
('ADMINISTRACIÓN Y LOGÍSTICA S.A.C.', 'AdmLog', '21023456789', '967890123'),
('SERVICIOS INTEGRALES S.A.C.', 'ServInt', '21134567890', '978901234'),
('LOGÍSTICA Y TRANSPORTE S.A.C.', 'LogTrans', '21245678901', '989012345'),
('GESTIÓN EMPRESARIAL S.A.C.', 'GesEmp', '21356789012', '990123456');


USE motorpark;

INSERT INTO conceptoegreso (concepto, descripcion) VALUES
('Gastos administrativos', 'Gastos administrativos por trámites diversos'),
('Combustible', 'Gastos por consumo de combustible'),
('Mantenimiento de vehículos', 'Gastos por mantenimiento y reparaciones de vehículos'),
('Servicios básicos', 'Pago de servicios como agua, luz, internet, etc.'),
('Alquiler de local', 'Pago mensual por alquiler del local'),
('Publicidad y marketing', 'Gastos en campañas publicitarias y marketing'),
('Sueldos y salarios', 'Pago de sueldos y beneficios a los colaboradores'),
('Impuestos y tasas', 'Pago de impuestos municipales y otros tributos'),
('Papelería y suministros', 'Compra de materiales de oficina y papelería'),
('Otros gastos operativos', 'Gastos varios relacionados con la operación del negocio');

SELECT * FROM conceptoegreso;




SELECT * FROM colaboradores;



SELECT * FROM contratoslaborales; -- TRAER LOS COLABORADORES DE LA TABLA CONTRATOLABORALES.

SELECT * FROM cargos;


SELECT * FROM areas;


SELECT * FROM personas;
SELECT * FROM egresos;