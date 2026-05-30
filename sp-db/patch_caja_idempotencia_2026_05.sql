-- Patch: idempotencia para cobros compuestos en Caja.
-- Ejecutar en la BD motorpark:
--   mysql -u <usuario> -p motorpark < sp-db/patch_caja_idempotencia_2026_05.sql

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE IF NOT EXISTS pagos_idempotencia (
    request_id VARCHAR(120) NOT NULL PRIMARY KEY,
    idcliente INT NOT NULL,
    monto_total DECIMAL(10,2) NOT NULL,
    payload_hash CHAR(64) NOT NULL,
    estado ENUM('PROCESSING','COMPLETED','FAILED') NOT NULL DEFAULT 'PROCESSING',
    idpago INT NULL,
    mensaje VARCHAR(300) NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT fk_pagos_idempotencia_cliente FOREIGN KEY (idcliente) REFERENCES clientes(idcliente),
    CONSTRAINT fk_pagos_idempotencia_pago FOREIGN KEY (idpago) REFERENCES pagos(idpago),
    INDEX idx_pagos_idempotencia_estado (estado),
    INDEX idx_pagos_idempotencia_idpago (idpago)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
