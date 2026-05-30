-- Sugerencias de conceptos para Caja (mayo 2026)
-- Inserta si no existe y actualiza monto sugerido si ya existe.
-- Ejecutar en phpMyAdmin dentro de la BD motorpark.

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Crear tabla si aún no existe en este entorno.
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

-- RECOJO VEHICULAR - S/350.00
UPDATE conceptospago
SET montosugerido = 350.00,
    descripcion = 'Recojo vehicular',
    fechamodificacion = NOW()
WHERE UPPER(TRIM(concepto)) = 'RECOJO VEHICULAR';

INSERT INTO conceptospago (idcolregistra, concepto, descripcion, montosugerido)
SELECT 1, 'RECOJO VEHICULAR', 'Recojo vehicular', 350.00
WHERE NOT EXISTS (
    SELECT 1 FROM conceptospago WHERE UPPER(TRIM(concepto)) = 'RECOJO VEHICULAR'
);

-- BUSQUEDA DE LLAVE - S/10.00
UPDATE conceptospago
SET montosugerido = 10.00,
    descripcion = 'Busqueda de llave',
    fechamodificacion = NOW()
WHERE UPPER(TRIM(concepto)) = 'BUSQUEDA DE LLAVE';

INSERT INTO conceptospago (idcolregistra, concepto, descripcion, montosugerido)
SELECT 1, 'BUSQUEDA DE LLAVE', 'Busqueda de llave', 10.00
WHERE NOT EXISTS (
    SELECT 1 FROM conceptospago WHERE UPPER(TRIM(concepto)) = 'BUSQUEDA DE LLAVE'
);

-- DUPLICADO DE CONTRATO - S/20.00
UPDATE conceptospago
SET montosugerido = 20.00,
    descripcion = 'Duplicado de contrato',
    fechamodificacion = NOW()
WHERE UPPER(TRIM(concepto)) = 'DUPLICADO DE CONTRATO';

INSERT INTO conceptospago (idcolregistra, concepto, descripcion, montosugerido)
SELECT 1, 'DUPLICADO DE CONTRATO', 'Duplicado de contrato', 20.00
WHERE NOT EXISTS (
    SELECT 1 FROM conceptospago WHERE UPPER(TRIM(concepto)) = 'DUPLICADO DE CONTRATO'
);

-- DUPLICADO DE TARJETA - S/10.00
UPDATE conceptospago
SET montosugerido = 10.00,
    descripcion = 'Duplicado de tarjeta',
    fechamodificacion = NOW()
WHERE UPPER(TRIM(concepto)) = 'DUPLICADO DE TARJETA';

INSERT INTO conceptospago (idcolregistra, concepto, descripcion, montosugerido)
SELECT 1, 'DUPLICADO DE TARJETA', 'Duplicado de tarjeta', 10.00
WHERE NOT EXISTS (
    SELECT 1 FROM conceptospago WHERE UPPER(TRIM(concepto)) = 'DUPLICADO DE TARJETA'
);

-- CARTA PODER - S/100.00
UPDATE conceptospago
SET montosugerido = 100.00,
    descripcion = 'Carta poder',
    fechamodificacion = NOW()
WHERE UPPER(TRIM(concepto)) = 'CARTA PODER';

INSERT INTO conceptospago (idcolregistra, concepto, descripcion, montosugerido)
SELECT 1, 'CARTA PODER', 'Carta poder', 100.00
WHERE NOT EXISTS (
    SELECT 1 FROM conceptospago WHERE UPPER(TRIM(concepto)) = 'CARTA PODER'
);

-- VIGENCIA DE PODER - S/50.00
UPDATE conceptospago
SET montosugerido = 50.00,
    descripcion = 'Vigencia de poder',
    fechamodificacion = NOW()
WHERE UPPER(TRIM(concepto)) = 'VIGENCIA DE PODER';

INSERT INTO conceptospago (idcolregistra, concepto, descripcion, montosugerido)
SELECT 1, 'VIGENCIA DE PODER', 'Vigencia de poder', 50.00
WHERE NOT EXISTS (
    SELECT 1 FROM conceptospago WHERE UPPER(TRIM(concepto)) = 'VIGENCIA DE PODER'
);

-- GPS - S/10.00
UPDATE conceptospago
SET montosugerido = 10.00,
    descripcion = 'Cobro de GPS',
    fechamodificacion = NOW()
WHERE UPPER(TRIM(concepto)) = 'GPS';

INSERT INTO conceptospago (idcolregistra, concepto, descripcion, montosugerido)
SELECT 1, 'GPS', 'Cobro de GPS', 10.00
WHERE NOT EXISTS (
    SELECT 1 FROM conceptospago WHERE UPPER(TRIM(concepto)) = 'GPS'
);

-- Concepto técnico para líneas manuales (necesario para Caja)
UPDATE conceptospago
SET montosugerido = 0.00,
    descripcion = 'Concepto variable para cobros manuales',
    fechamodificacion = NOW()
WHERE UPPER(TRIM(concepto)) = UPPER('Varios caja (manual)');

INSERT INTO conceptospago (idcolregistra, concepto, descripcion, montosugerido)
SELECT 1, 'Varios caja (manual)', 'Concepto variable para cobros manuales', 0.00
WHERE NOT EXISTS (
    SELECT 1 FROM conceptospago WHERE UPPER(TRIM(concepto)) = UPPER('Varios caja (manual)')
);

-- Verificación rápida
SELECT idconcepto, concepto, montosugerido
FROM conceptospago
WHERE UPPER(TRIM(concepto)) IN (
  'RECOJO VEHICULAR',
  'BUSQUEDA DE LLAVE',
  'DUPLICADO DE CONTRATO',
  'DUPLICADO DE TARJETA',
  'CARTA PODER',
  'VIGENCIA DE PODER',
  'GPS',
  UPPER('Varios caja (manual)')
)
ORDER BY concepto;
