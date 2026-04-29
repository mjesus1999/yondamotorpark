-- Actualizar montos sugeridos del combo de Caja (conceptospago).
-- Ejecutar en la BD: motorpark
--
-- Recomendación: NO borrar conceptos existentes (pueden estar referenciados por detpagos/pagos históricos).
-- Este script solo ajusta montos y normaliza nombres si hace falta.

USE motorpark;

-- RECOJO VEHICULAR
UPDATE conceptospago SET concepto = 'RECOJO VEHICULAR', montosugerido = 350.00
WHERE UPPER(concepto) LIKE '%RECOJO%VEHIC%' LIMIT 1;

-- BUSQUEDA DE LLAVE
UPDATE conceptospago SET concepto = 'BUSQUEDA DE LLAVE', montosugerido = 10.00
WHERE UPPER(concepto) LIKE '%BUSQUEDA%LLAVE%' LIMIT 1;

-- DUPLICADO DE CONTRATO
UPDATE conceptospago SET concepto = 'DUPLICADO DE CONTRATO', montosugerido = 20.00
WHERE UPPER(concepto) LIKE '%DUPLICADO%CONTRATO%' LIMIT 1;

-- DUPLICADO DE TARJETA
UPDATE conceptospago SET concepto = 'DUPLICADO DE TARJETA', montosugerido = 10.00
WHERE UPPER(concepto) LIKE '%DUPLICADO%TARJETA%' LIMIT 1;

-- CARTA PODER
UPDATE conceptospago SET concepto = 'CARTA PODER', montosugerido = 100.00
WHERE UPPER(concepto) LIKE '%CARTA%PODER%' LIMIT 1;

-- VIGENCIA DE PODER
UPDATE conceptospago SET concepto = 'VIGENCIA DE PODER', montosugerido = 50.00
WHERE UPPER(concepto) LIKE '%VIGENCIA%PODER%' LIMIT 1;

-- GPS
UPDATE conceptospago SET concepto = 'GPS', montosugerido = 10.00
WHERE UPPER(concepto) = 'GPS' LIMIT 1;

-- Verificación rápida
SELECT idconcepto, concepto, montosugerido
FROM conceptospago
WHERE UPPER(concepto) IN (
  'RECOJO VEHICULAR',
  'BUSQUEDA DE LLAVE',
  'DUPLICADO DE CONTRATO',
  'DUPLICADO DE TARJETA',
  'CARTA PODER',
  'VIGENCIA DE PODER',
  'GPS',
  'VARIOS CAJA (MANUAL)'
)
ORDER BY concepto;

