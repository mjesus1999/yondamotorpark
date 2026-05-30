-- Ejecutar en phpMyAdmin con la BD u322322994_motorpark seleccionada
-- Solo crea la tabla si no existe (para instalaciones que usaron IMPORTAR_OK sin esta tabla).

CREATE TABLE IF NOT EXISTS fichasolicitud (
    idficha INT PRIMARY KEY AUTO_INCREMENT,
    idcotizacion INT NOT NULL UNIQUE,
    idcolcredito INT NOT NULL,
    idconyuge INT NULL COMMENT 'CONYUGE DEL SOLICITANTE',
    idaval INT NULL COMMENT 'AVAL DEL SOLICITANTE',
    idavalconyuge INT NULL COMMENT 'CONYUGE DEL AVAL',
    fechavisita DATE NOT NULL,
    rutaficha VARCHAR(300) NOT NULL,
    comentarios TEXT NULL,
    estado ENUM('Aprobado', 'Observado', 'Anulado') NOT NULL,
    CONSTRAINT fk_idcotizacion_ficha FOREIGN KEY (idcotizacion) REFERENCES cotizaciones (idcotizacion),
    CONSTRAINT fk_idcolcredito_ficha FOREIGN KEY (idcolcredito) REFERENCES colaboradores (idcolaborador),
    CONSTRAINT fk_idconyugue_ficha FOREIGN KEY (idconyuge) REFERENCES personas (idpersona),
    CONSTRAINT fk_idaval_ficha FOREIGN KEY (idaval) REFERENCES personas (idpersona),
    CONSTRAINT fk_idavalconyuge_ficha FOREIGN KEY (idavalconyuge) REFERENCES personas (idpersona)
) ENGINE = INNODB;
