-- La moneda y precio de compra están definidos en el proceso de COMPRA
CREATE TABLE vehiculos
(
	idvehiculo			INT AUTO_INCREMENT PRIMARY KEY,
	idmodelo 			INT 			NOT NULL,
    version				VARCHAR(20) 	NOT NULL,
    condicion			ENUM('nuevo', 'seminuevo') NOT NULL DEFAULT 'nuevo',
    idcombustible 		INT 			NOT NULL,
    color 				VARCHAR(30) 	NULL,
    chasis 				VARCHAR(30)		NULL,
    placa 				VARCHAR(10)	 	NULL,
    placarotativa		VARCHAR(10) 	NULL,
    seriemotor 			VARCHAR(20) 	NULL,
    moneda				ENUM('USD', 'PEN') NULL DEFAULT 'USD', -- VENTA
    precioventa			DECIMAL(9,2) 	NULL,
    disponibilidad 		ENUM('proceso', 'libre', 'separado', 'vendido', 'recuperado') NOT NULL,
    idlogistica			INT 			NOT NULL COMMENT 'Usuario del área de logística',
    idlocal 			INT 			NULL COMMENT 'Piso: CHINCHA - ICA',
    origen 				ENUM ('OCP', 'OLD','CTZ') NOT NULL COMMENT 'OCP = Orden de compra (conducto regular), OLD (Contratos anteriores al sistema), CTZ (Cotizado por asesor)',
    estado 				ENUM('0', '1') NULL DEFAULT '1',
	creado 				DATETIME 		NOT NULL DEFAULT NOW(),
    modificado 			DATETIME 		NULL,
    eliminado			DATETIME		NULL,
    CONSTRAINT fk_idmodelo_veh FOREIGN KEY (idvehiculo) REFERENCES modelos (idmodelo),
    CONSTRAINT fk_idcombustible_veh FOREIGN KEY (idcombustible) REFERENCES combustibles (idcombustible),
    CONSTRAINT fk_idlocal_veh FOREIGN KEY (idlocal) REFERENCES locales (idlocal),
    CONSTRAINT fk_idlogistica_veh FOREIGN KEY (idlogistica) REFERENCES colaboradores (idcolaborador)
)ENGINE = INNODB;


-- ALTER TABLE vehiculos ADD COLUMN estado ENUM('0', '1') NULL DEFAULT '1' AFTER `origen`;
-- ALTER TABLE vehiculos ADD COLUMN eliminado DATETIME NULL;