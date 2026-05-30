-- =============================================================================
-- Notas de crédito (Nubefact) — Yonda Motor Park
-- Ejecutar en phpMyAdmin (Hostinger) UNA vez.
-- No modifica datos existentes; solo agrega tabla/columnas opcionales.
-- =============================================================================

-- 1) Referencia fiscal del comprobante original (para NC y consultas)
-- Si alguna columna ya existe, ignore el error de esa línea y continúe.
ALTER TABLE pagos
  ADD COLUMN comprobante_serie VARCHAR(4) NULL
    COMMENT 'Serie SUNAT del comprobante emitido (BBB1, FFF1, etc.)'
    AFTER numero_boleta_sunat;

ALTER TABLE pagos
  ADD COLUMN comprobante_tipo_nubefact TINYINT NULL
    COMMENT '1=Factura, 2=Boleta (Nubefact)'
    AFTER comprobante_serie;

-- 2) Registro de notas de crédito emitidas
CREATE TABLE IF NOT EXISTS notas_credito (
  idnota_credito INT AUTO_INCREMENT PRIMARY KEY,
  idpago INT NOT NULL,
  idcontrato INT NULL,
  idcolaborador INT NULL,
  tipo_nota_credito VARCHAR(2) NOT NULL DEFAULT '01'
    COMMENT 'Cat. SUNAT: 01=Anulación operación, 06=Devolución total, 07=Devolución parcial, etc.',
  documento_modifica_tipo TINYINT NOT NULL COMMENT '1=Factura, 2=Boleta (documento afectado)',
  documento_modifica_serie VARCHAR(4) NOT NULL,
  documento_modifica_numero INT NOT NULL,
  nc_serie VARCHAR(4) NOT NULL,
  nc_numero INT NOT NULL,
  total DECIMAL(10, 2) NOT NULL,
  motivo VARCHAR(300) NULL,
  enlace_pdf_nubefact VARCHAR(500) NULL,
  enlace_xml_nubefact VARCHAR(500) NULL,
  enlace_del_cdr VARCHAR(500) NULL,
  estado ENUM('PENDIENTE', 'EMITIDA', 'ERROR') NOT NULL DEFAULT 'PENDIENTE',
  mensaje_error VARCHAR(500) NULL,
  creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_notas_credito_idpago (idpago),
  INDEX idx_notas_credito_contrato (idcontrato),
  CONSTRAINT fk_notas_credito_pago FOREIGN KEY (idpago) REFERENCES pagos (idpago)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- 3) Series de correlativo para NC (configurar las mismas series en panel Nubefact)
INSERT INTO series_nubefact (serie, ultimo_numero) VALUES ('BCN1', 0)
ON DUPLICATE KEY UPDATE serie = serie;

INSERT INTO series_nubefact (serie, ultimo_numero) VALUES ('FCN1', 0)
ON DUPLICATE KEY UPDATE serie = serie;

-- 4) SP historial: incluir datos para botón de NC (opcional; si no ejecuta, el API igual funciona)
DROP PROCEDURE IF EXISTS sp_get_pagos_by_contrato;
DELIMITER $$
CREATE PROCEDURE sp_get_pagos_by_contrato(IN p_idcontrato INT)
BEGIN
    SELECT
        p.idpago,
        c.idcontrato,
        p.idcronograma,
        c.numcuota,
        DATE_FORMAT(c.fechapago, '%d/%m/%Y') AS fecha_vencimiento,
        DATE_FORMAT(p.fechapago, '%d/%m/%Y') AS fecha_pago,
        p.amortizacion,
        p.saldorestante,
        p.mediopago,
        p.numerotransaccion,
        p.comprobante,
        p.enlace_pdf_nubefact,
        p.numero_boleta_sunat,
        p.comprobante_serie,
        p.comprobante_tipo_nubefact,
        p.tipo,
        p.observacion
    FROM pagos p
    INNER JOIN cronogramas c ON p.idcronograma = c.idcronograma
    WHERE c.idcontrato = p_idcontrato
    ORDER BY c.numcuota, p.fechapago;
END$$
DELIMITER ;
