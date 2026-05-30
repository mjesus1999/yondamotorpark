-- Validación cronograma en Hostinger (misma info que reporte CSV local).
-- Ejecutar en phpMyAdmin → exportar resultado a Excel si lo desea.

SELECT *
FROM (
    SELECT 'enero' AS mes_import, id_contrato, dni, cliente_nombre AS cliente, chasis,
           fecha_comienzo, duracion_meses AS plazo_meses, cuota_mensual,
           ROUND(GREATEST(0, IFNULL(monto_valor,0) - IFNULL(monto_inicial,0)), 2) AS monto_financiar,
           (CASE WHEN fecha_pago_5 IS NOT NULL AND fecha_pago_5 <> '0000-00-00' THEN 5
                 WHEN fecha_pago_4 IS NOT NULL AND fecha_pago_4 <> '0000-00-00' THEN 4
                 WHEN fecha_pago_3 IS NOT NULL AND fecha_pago_3 <> '0000-00-00' THEN 3
                 WHEN fecha_pago_2 IS NOT NULL AND fecha_pago_2 <> '0000-00-00' THEN 2
                 WHEN fecha_pago_1 IS NOT NULL AND fecha_pago_1 <> '0000-00-00' THEN 1
                 ELSE 0 END) AS cuotas_pagadas,
           fecha_pago_1, fecha_pago_2, fecha_pago_3, fecha_pago_4, fecha_pago_5,
           TRIM(BOTH ';' FROM CONCAT_WS(';',
             IF(fecha_comienzo IS NULL OR fecha_comienzo = '0000-00-00', 'SIN_FECHA_COMIENZO', NULL),
             IF(duracion_meses IS NULL OR duracion_meses <= 0, 'SIN_PLAZO', NULL),
             IF(cuota_mensual IS NULL OR cuota_mensual <= 0, 'SIN_CUOTA_MENSUAL', NULL),
             IF(chasis IS NULL OR TRIM(chasis) = '', 'SIN_CHASIS', NULL)
           )) AS alertas
    FROM import_contratos_enero_2026
    UNION ALL
    SELECT 'febrero', id_contrato, dni, cliente_nombre, chasis,
           fecha_comienzo, duracion_meses, cuota_mensual,
           ROUND(GREATEST(0, IFNULL(monto_valor,0) - IFNULL(monto_inicial,0)), 2),
           (CASE WHEN fecha_pago_5 IS NOT NULL AND fecha_pago_5 <> '0000-00-00' THEN 5
                 WHEN fecha_pago_4 IS NOT NULL AND fecha_pago_4 <> '0000-00-00' THEN 4
                 WHEN fecha_pago_3 IS NOT NULL AND fecha_pago_3 <> '0000-00-00' THEN 3
                 WHEN fecha_pago_2 IS NOT NULL AND fecha_pago_2 <> '0000-00-00' THEN 2
                 WHEN fecha_pago_1 IS NOT NULL AND fecha_pago_1 <> '0000-00-00' THEN 1 ELSE 0 END),
           fecha_pago_1, fecha_pago_2, fecha_pago_3, fecha_pago_4, fecha_pago_5,
           TRIM(BOTH ';' FROM CONCAT_WS(';',
             IF(fecha_comienzo IS NULL OR fecha_comienzo = '0000-00-00', 'SIN_FECHA_COMIENZO', NULL),
             IF(duracion_meses IS NULL OR duracion_meses <= 0, 'SIN_PLAZO', NULL),
             IF(cuota_mensual IS NULL OR cuota_mensual <= 0, 'SIN_CUOTA_MENSUAL', NULL),
             IF(chasis IS NULL OR TRIM(chasis) = '', 'SIN_CHASIS', NULL)
           ))
    FROM import_contratos_febrero_2026
    UNION ALL
    SELECT 'marzo', id_contrato, dni, cliente_nombre, chasis,
           fecha_comienzo, duracion_meses, cuota_mensual,
           ROUND(GREATEST(0, IFNULL(monto_valor,0) - IFNULL(monto_inicial,0)), 2),
           (CASE WHEN fecha_pago_5 IS NOT NULL AND fecha_pago_5 <> '0000-00-00' THEN 5
                 WHEN fecha_pago_4 IS NOT NULL AND fecha_pago_4 <> '0000-00-00' THEN 4
                 WHEN fecha_pago_3 IS NOT NULL AND fecha_pago_3 <> '0000-00-00' THEN 3
                 WHEN fecha_pago_2 IS NOT NULL AND fecha_pago_2 <> '0000-00-00' THEN 2
                 WHEN fecha_pago_1 IS NOT NULL AND fecha_pago_1 <> '0000-00-00' THEN 1 ELSE 0 END),
           fecha_pago_1, fecha_pago_2, fecha_pago_3, fecha_pago_4, fecha_pago_5,
           TRIM(BOTH ';' FROM CONCAT_WS(';',
             IF(fecha_comienzo IS NULL OR fecha_comienzo = '0000-00-00', 'SIN_FECHA_COMIENZO', NULL),
             IF(duracion_meses IS NULL OR duracion_meses <= 0, 'SIN_PLAZO', NULL),
             IF(cuota_mensual IS NULL OR cuota_mensual <= 0, 'SIN_CUOTA_MENSUAL', NULL),
             IF(chasis IS NULL OR TRIM(chasis) = '', 'SIN_CHASIS', NULL)
           ))
    FROM import_contratos_marzo_2026
    UNION ALL
    SELECT 'abril', id_contrato, dni, cliente_nombre, chasis,
           fecha_comienzo, duracion_meses, cuota_mensual,
           ROUND(GREATEST(0, IFNULL(monto_valor,0) - IFNULL(monto_inicial,0)), 2),
           (CASE WHEN fecha_pago_5 IS NOT NULL AND fecha_pago_5 <> '0000-00-00' THEN 5
                 WHEN fecha_pago_4 IS NOT NULL AND fecha_pago_4 <> '0000-00-00' THEN 4
                 WHEN fecha_pago_3 IS NOT NULL AND fecha_pago_3 <> '0000-00-00' THEN 3
                 WHEN fecha_pago_2 IS NOT NULL AND fecha_pago_2 <> '0000-00-00' THEN 2
                 WHEN fecha_pago_1 IS NOT NULL AND fecha_pago_1 <> '0000-00-00' THEN 1 ELSE 0 END),
           fecha_pago_1, fecha_pago_2, fecha_pago_3, fecha_pago_4, fecha_pago_5,
           TRIM(BOTH ';' FROM CONCAT_WS(';',
             IF(fecha_comienzo IS NULL OR fecha_comienzo = '0000-00-00', 'SIN_FECHA_COMIENZO', NULL),
             IF(duracion_meses IS NULL OR duracion_meses <= 0, 'SIN_PLAZO', NULL),
             IF(cuota_mensual IS NULL OR cuota_mensual <= 0, 'SIN_CUOTA_MENSUAL', NULL),
             IF(chasis IS NULL OR TRIM(chasis) = '', 'SIN_CHASIS', NULL)
           ))
    FROM import_contratos_abril_2026
    UNION ALL
    SELECT 'mayo', id_contrato, dni, cliente_nombre, chasis,
           fecha_comienzo, duracion_meses, cuota_mensual,
           ROUND(GREATEST(0, IFNULL(monto_valor,0) - IFNULL(monto_inicial,0)), 2),
           (CASE WHEN fecha_pago_5 IS NOT NULL AND fecha_pago_5 <> '0000-00-00' THEN 5
                 WHEN fecha_pago_4 IS NOT NULL AND fecha_pago_4 <> '0000-00-00' THEN 4
                 WHEN fecha_pago_3 IS NOT NULL AND fecha_pago_3 <> '0000-00-00' THEN 3
                 WHEN fecha_pago_2 IS NOT NULL AND fecha_pago_2 <> '0000-00-00' THEN 2
                 WHEN fecha_pago_1 IS NOT NULL AND fecha_pago_1 <> '0000-00-00' THEN 1 ELSE 0 END),
           fecha_pago_1, fecha_pago_2, fecha_pago_3, fecha_pago_4, fecha_pago_5,
           TRIM(BOTH ';' FROM CONCAT_WS(';',
             IF(fecha_comienzo IS NULL OR fecha_comienzo = '0000-00-00', 'SIN_FECHA_COMIENZO', NULL),
             IF(duracion_meses IS NULL OR duracion_meses <= 0, 'SIN_PLAZO', NULL),
             IF(cuota_mensual IS NULL OR cuota_mensual <= 0, 'SIN_CUOTA_MENSUAL', NULL),
             IF(chasis IS NULL OR TRIM(chasis) = '', 'SIN_CHASIS', NULL)
           ))
    FROM import_contratos_mayo_2026
) AS t
ORDER BY mes_import, id_contrato;

-- Solo los que tienen alertas en Hostinger:
-- SELECT * FROM ( ... mismo query ... ) x WHERE alertas <> '';
