-- BD seleccionada en phpMyAdmin (ej. u322322994_motorpark).
-- Migración de `pagos` antigua (solo cronograma) al modelo unificado (inicial/contado/vistas).
-- Si sale error de columna o FK duplicada, omitir esa línea y seguir.
-- Después: importar vistas-db/vw_cotizacion_tras_patch_pagos.sql para restaurar totales iniciales y contado.

CREATE TABLE IF NOT EXISTS conceptospago (
    idconcepto INT PRIMARY KEY AUTO_INCREMENT,
    idcolregistra INT NOT NULL,
    idcolactualiza INT NULL,
    concepto VARCHAR(150) NOT NULL,
    descripcion TEXT NULL,
    montosugerido DECIMAL(10,2) NULL,
    fecharegistro DATETIME NOT NULL DEFAULT NOW(),
    fechamodificacion DATETIME NULL
) ENGINE = InnoDB;

ALTER TABLE pagos MODIFY COLUMN idcronograma INT NULL;

ALTER TABLE pagos ADD COLUMN idcotizacion INT NULL COMMENT 'Pago inicial u otros ligados a cotización';
ALTER TABLE pagos ADD COLUMN idcliente INT NULL COMMENT 'Pago al contado';
ALTER TABLE pagos ADD COLUMN idconcepto INT NULL COMMENT 'Concepto de pago';
ALTER TABLE pagos ADD COLUMN idvehiculo INT NULL COMMENT 'Inicial o contado';
ALTER TABLE pagos ADD COLUMN idasesorvendedor INT NULL COMMENT 'Contado';

ALTER TABLE pagos ADD COLUMN moneda ENUM('USD', 'PEN') NULL;
ALTER TABLE pagos ADD COLUMN montomonedaoriginal DECIMAL(10, 2) NULL;
ALTER TABLE pagos ADD COLUMN tipocambioaplicado DECIMAL(10, 4) NULL;

ALTER TABLE pagos MODIFY COLUMN tipo ENUM('Cuota', 'Penalidad', 'Otro') NOT NULL DEFAULT 'Cuota';

ALTER TABLE pagos ADD CONSTRAINT fk_idconcepto_pagos FOREIGN KEY (idconcepto) REFERENCES conceptospago (idconcepto);
ALTER TABLE pagos ADD CONSTRAINT fk_idvehiculo_pagos FOREIGN KEY (idvehiculo) REFERENCES vehiculos (idvehiculo);
ALTER TABLE pagos ADD CONSTRAINT fk_idasesorvendedor_pagos FOREIGN KEY (idasesorvendedor) REFERENCES colaboradores (idcolaborador);
ALTER TABLE pagos ADD CONSTRAINT fk_idcliente_pagos FOREIGN KEY (idcliente) REFERENCES clientes (idcliente);
ALTER TABLE pagos ADD CONSTRAINT fk_idcotizacion_pagos FOREIGN KEY (idcotizacion) REFERENCES cotizaciones (idcotizacion);
