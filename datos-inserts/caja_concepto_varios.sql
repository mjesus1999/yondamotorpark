-- Concepto de respaldo para líneas añadidas manualmente en Caja (Pagos por conceptos).
-- Ejecutar en la BD de producción tras respaldo. Ajusta idcolregistra si hace falta (colaborador existente).
INSERT INTO conceptospago (idcolregistra, concepto, descripcion, montosugerido)
SELECT
    1,
    'Varios caja (manual)',
    'Líneas ingresadas manualmente en Caja; no usar como comisión asociada a cuotas.',
    0.00
FROM (SELECT 0 AS s) AS t
WHERE NOT EXISTS (
    SELECT 1 FROM conceptospago c
    WHERE c.concepto = 'Varios caja (manual)'
    LIMIT 1
);
