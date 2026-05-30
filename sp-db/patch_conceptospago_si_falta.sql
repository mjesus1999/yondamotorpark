-- BD ya seleccionada en phpMyAdmin (ej. u322322994_motorpark).
-- Nombre real de la tabla: conceptospago (todo minúsculas). En Linux MySQL
-- "conceptosPAGO" es OTRO nombre y puede dar #1146.

CREATE TABLE IF NOT EXISTS conceptospago (
    idconcepto INT PRIMARY KEY AUTO_INCREMENT,
    idcolregistra INT NOT NULL,
    idcolactualiza INT NULL,
    concepto VARCHAR(150) NOT NULL,
    descripcion TEXT NULL,
    montosugerido DECIMAL(10,2) NULL,
    fecharegistro DATETIME NOT NULL DEFAULT NOW(),
    fechamodificacion DATETIME NULL
) ENGINE=InnoDB;
