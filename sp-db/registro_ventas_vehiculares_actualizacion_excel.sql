-- Actualización de datos según Excel (por CHASIS).
-- Ejecutar en el VPS (en MySQL motorpark):
--   mysql -u yhondb -p motorpark < /var/www/yondamotorpark/sp-db/registro_ventas_vehiculares_actualizacion_excel.sql
--
-- Este script:
-- 1) Ajusta esquema para soportar texto en "deudas pendientes" (detalle) y 3 decimales en mora/total.
-- 2) Actualiza filas existentes por CHASIS.

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- 1) Esquema: columna de detalle (texto) y precisión 3 decimales para mora/total.
SET @db_name := DATABASE();

SET @has_detalle := (
  SELECT COUNT(*)
  FROM information_schema.columns
  WHERE table_schema = @db_name
    AND table_name = 'registro_ventas_vehiculares'
    AND column_name = 'deudas_pendientes_detalle'
);

SET @sql := IF(
  @has_detalle = 0,
  'ALTER TABLE registro_ventas_vehiculares ADD COLUMN deudas_pendientes_detalle TEXT NULL AFTER deudas_pendientes',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Asegurar 3 decimales (si ya está en 3, no pasa nada).
ALTER TABLE registro_ventas_vehiculares
  MODIFY COLUMN mora_3_dias DECIMAL(10,3) NULL,
  MODIFY COLUMN total_con_mora DECIMAL(10,3) NULL;

-- 2) Updates por CHASIS.
-- Nota: el Excel trae "S/." y comas; acá ya van normalizados a DECIMAL.
-- Nota 2: en varios registros el Excel no trae placa; se deja NULL.
-- Nota 3: para compatibilidad con lo que ya venía guardándose:
--   - monto_interes = mora_3_dias
--   - cuota_total_mensual = total_con_mora

UPDATE registro_ventas_vehiculares
SET
  motor = 'AZXWSC45639',
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '955 283 341',
  telefono_2 = NULL,
  precio_total = 16700.00,
  pago_inicial = 0.00,
  fecha_inicio_credito = NULL,
  plazo_meses = 27,
  fecha_fin_credito = NULL,
  cuota_base = 1182.11,
  tasa_interes = 10.00,
  monto_interes = 118.211,
  cuota_total_mensual = 1300.321,
  mora_3_dias = 118.211,
  total_con_mora = 1300.321,
  numero_cuota_pagada = 1,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MD2A25AX0TWC00351';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '963 889 086',
  telefono_2 = '944 298 142',
  precio_total = 19900.00,
  pago_inicial = 5970.00,
  fecha_inicio_credito = '2026-02-27',
  plazo_meses = 12,
  fecha_fin_credito = '2027-02-27',
  cuota_base = 1573.45,
  tasa_interes = 10.00,
  monto_interes = 157.345,
  cuota_total_mensual = 1730.795,
  mora_3_dias = 157.345,
  total_con_mora = 1730.795,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'VBKJYC409RN14986';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '985 226 840',
  telefono_2 = NULL,
  precio_total = 14090.00,
  pago_inicial = 3000.00,
  fecha_inicio_credito = '2026-02-27',
  plazo_meses = 12,
  fecha_fin_credito = '2027-02-27',
  cuota_base = 1252.66,
  tasa_interes = 10.00,
  monto_interes = 125.266,
  cuota_total_mensual = 1377.926,
  mora_3_dias = 125.266,
  total_con_mora = 1377.926,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MD2A49AX2TWG60029';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '913 808 142',
  telefono_2 = NULL,
  precio_total = 9600.00,
  pago_inicial = 3000.00,
  fecha_inicio_credito = '2026-02-28',
  plazo_meses = 18,
  fecha_fin_credito = '2027-08-28',
  cuota_base = 565.50,
  tasa_interes = 10.00,
  monto_interes = 56.55,
  cuota_total_mensual = 622.05,
  mora_3_dias = 56.55,
  total_con_mora = 622.05,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MD2A49AX3SWH00178';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '902 771 125',
  telefono_2 = NULL,
  precio_total = 50004.00,
  pago_inicial = 13500.00,
  fecha_inicio_credito = '2026-02-27',
  plazo_meses = 60,
  fecha_fin_credito = '2031-02-27',
  cuota_base = 1869.11,
  tasa_interes = 10.00,
  monto_interes = 186.911,
  cuota_total_mensual = 2056.021,
  mora_3_dias = 186.911,
  total_con_mora = 2056.021,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'KNAB2511AVT460468';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '942 130 236',
  telefono_2 = NULL,
  precio_total = 17000.00,
  pago_inicial = 3000.00,
  fecha_inicio_credito = '2026-03-21',
  plazo_meses = 24,
  fecha_fin_credito = '2028-02-21',
  cuota_base = 1016.58,
  tasa_interes = 10.00,
  monto_interes = 101.658,
  cuota_total_mensual = 1118.238,
  mora_3_dias = 101.658,
  total_con_mora = 1118.238,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MD6M14LA7T4AA7218';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '915 173 600',
  telefono_2 = NULL,
  precio_total = 9600.00,
  pago_inicial = 3000.00,
  fecha_inicio_credito = '2026-02-20',
  plazo_meses = 12,
  fecha_fin_credito = '2027-02-20',
  cuota_base = 745.49,
  tasa_interes = 10.00,
  monto_interes = 74.549,
  cuota_total_mensual = 820.039,
  mora_3_dias = 74.549,
  total_con_mora = 820.039,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MD2A49AX4SWM10321';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '976 103 055',
  telefono_2 = NULL,
  precio_total = 14090.00,
  pago_inicial = 3000.00,
  fecha_inicio_credito = '2026-02-18',
  plazo_meses = 24,
  fecha_fin_credito = '2028-02-18',
  cuota_base = 805.28,
  tasa_interes = 10.00,
  monto_interes = 80.528,
  cuota_total_mensual = 885.808,
  mora_3_dias = 80.528,
  total_con_mora = 885.808,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MD2A49AX2TWD60037';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '990 133 225',
  telefono_2 = NULL,
  precio_total = 48840.00,
  pago_inicial = 10000.00,
  fecha_inicio_credito = '2026-02-18',
  plazo_meses = 36,
  fecha_fin_credito = '2029-02-18',
  cuota_base = 2129.10,
  tasa_interes = 10.00,
  monto_interes = 212.91,
  cuota_total_mensual = 2342.01,
  mora_3_dias = 212.91,
  total_con_mora = 2342.01,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MALB251AATM696463';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '912 508 426',
  telefono_2 = NULL,
  precio_total = 15034.00,
  pago_inicial = 3000.00,
  fecha_inicio_credito = '2026-02-13',
  plazo_meses = 24,
  fecha_fin_credito = '2028-02-13',
  cuota_base = 873.83,
  tasa_interes = 10.00,
  monto_interes = 87.383,
  cuota_total_mensual = 961.213,
  mora_3_dias = 87.383,
  total_con_mora = 961.213,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MD2A49AXTWE00165';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '927 876 977',
  telefono_2 = NULL,
  precio_total = 14090.00,
  pago_inicial = 3000.00,
  fecha_inicio_credito = '2026-02-10',
  plazo_meses = 24,
  fecha_fin_credito = '2028-02-10',
  cuota_base = 805.28,
  tasa_interes = 10.00,
  monto_interes = 80.528,
  cuota_total_mensual = 885.808,
  mora_3_dias = 80.528,
  total_con_mora = 885.808,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MD2A49AX9TWE00114';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '968 656 837',
  telefono_2 = '924 000 581',
  precio_total = 48840.00,
  pago_inicial = 10000.00,
  fecha_inicio_credito = '2026-03-27',
  plazo_meses = 36,
  fecha_fin_credito = '2029-02-27',
  cuota_base = 2129.10,
  tasa_interes = 10.00,
  monto_interes = 212.91,
  cuota_total_mensual = 2342.01,
  mora_3_dias = 212.91,
  total_con_mora = 2342.01,
  numero_cuota_pagada = 1,
  deudas_pendientes_detalle = 'Falta pagar 10 soles de GPS'
WHERE chasis = 'MALB251AATM698291';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '958 120 436',
  telefono_2 = NULL,
  precio_total = 17000.00,
  pago_inicial = 3000.00,
  fecha_inicio_credito = '2026-02-26',
  plazo_meses = 24,
  fecha_fin_credito = '2028-02-26',
  cuota_base = 1016.58,
  tasa_interes = 10.00,
  monto_interes = 101.658,
  cuota_total_mensual = 1118.238,
  mora_3_dias = 101.658,
  total_con_mora = 1118.238,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MD6M14LAXT4AA7164';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '907 655 084',
  telefono_2 = '919 478 869',
  precio_total = 5700.00,
  pago_inicial = 1710.00,
  fecha_inicio_credito = '2026-02-26',
  plazo_meses = 12,
  fecha_fin_credito = '2027-02-26',
  cuota_base = 450.69,
  tasa_interes = 10.00,
  monto_interes = 45.069,
  cuota_total_mensual = 495.759,
  mora_3_dias = 45.069,
  total_con_mora = 495.759,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'LPPFCKLS9T2000021';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '972 456 806',
  telefono_2 = NULL,
  precio_total = 68043.00,
  pago_inicial = 20412.00,
  fecha_inicio_credito = '2026-02-23',
  plazo_meses = 36,
  fecha_fin_credito = '2029-02-23',
  cuota_base = 2610.95,
  tasa_interes = 10.00,
  monto_interes = 261.095,
  cuota_total_mensual = 2872.045,
  mora_3_dias = 261.095,
  total_con_mora = 2872.045,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'LB3SX2036TX802757';

UPDATE registro_ventas_vehiculares
SET
  motor = 'AZXWSG73419',
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '926 047 589',
  telefono_2 = NULL,
  precio_total = 21444.00,
  pago_inicial = 4000.00,
  fecha_inicio_credito = '2026-02-19',
  plazo_meses = 18,
  fecha_fin_credito = '2027-08-19',
  cuota_base = 1494.62,
  tasa_interes = 10.00,
  monto_interes = 149.462,
  cuota_total_mensual = 1644.082,
  mora_3_dias = 149.462,
  total_con_mora = 1644.082,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MD2A45AX3TWG00530';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '989 022 244',
  telefono_2 = NULL,
  precio_total = 105043.00,
  pago_inicial = 31512.90,
  fecha_inicio_credito = '2026-02-18',
  plazo_meses = 48,
  fecha_fin_credito = '2030-02-18',
  cuota_base = 3622.11,
  tasa_interes = 10.00,
  monto_interes = 362.211,
  cuota_total_mensual = 3984.321,
  mora_3_dias = 362.211,
  total_con_mora = 3984.321,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'LB3FX1S71TB007401';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '917 077 909',
  telefono_2 = '928 745 285',
  precio_total = 47520.00,
  pago_inicial = 10000.00,
  fecha_inicio_credito = '2026-02-17',
  plazo_meses = 48,
  fecha_fin_credito = '2030-02-17',
  cuota_base = 1999.25,
  tasa_interes = 10.00,
  monto_interes = 199.925,
  cuota_total_mensual = 2199.175,
  mora_3_dias = 199.925,
  total_con_mora = 2199.175,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'M4LB25AATM681551';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = 'Y2J-576',
  telefono_1 = '935 616 900',
  telefono_2 = NULL,
  precio_total = 43993.00,
  pago_inicial = 10000.00,
  fecha_inicio_credito = '2026-03-14',
  plazo_meses = 3,
  fecha_fin_credito = '2026-05-14',
  cuota_base = 12310.15,
  tasa_interes = 10.00,
  monto_interes = 1231.015,
  cuota_total_mensual = 13541.165,
  mora_3_dias = 1231.015,
  total_con_mora = 13541.165,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'KNAB2511ATT355970';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '960 683 026',
  telefono_2 = NULL,
  precio_total = 11500.00,
  pago_inicial = 3000.00,
  fecha_inicio_credito = '2026-02-14',
  plazo_meses = 12,
  fecha_fin_credito = '2027-02-14',
  cuota_base = 1298.97,
  tasa_interes = 10.00,
  monto_interes = 129.897,
  cuota_total_mensual = 1428.867,
  mora_3_dias = 129.897,
  total_con_mora = 1428.867,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MD2A45SWL11791';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '981 389 176',
  telefono_2 = '936 210 563',
  precio_total = 21444.00,
  pago_inicial = 3000.00,
  fecha_inicio_credito = '2026-02-13',
  plazo_meses = 24,
  fecha_fin_credito = '2028-02-13',
  cuota_base = 1339.28,
  tasa_interes = 10.00,
  monto_interes = 133.928,
  cuota_total_mensual = 1473.208,
  mora_3_dias = 133.928,
  total_con_mora = 1473.208,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MD2A45AX9TWG00208';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '961316730',
  telefono_2 = NULL,
  precio_total = 14090.00,
  pago_inicial = 46065.00,
  fecha_inicio_credito = '2028-02-12',
  plazo_meses = 24,
  fecha_fin_credito = '2028-02-12',
  cuota_base = 805.28,
  tasa_interes = 10.00,
  monto_interes = 80.528,
  cuota_total_mensual = 885.808,
  mora_3_dias = 80.528,
  total_con_mora = 885.808,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MD2A49AX8TWE00153';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '961 536 940',
  telefono_2 = '903 281 403',
  precio_total = 14600.00,
  pago_inicial = 4380.00,
  fecha_inicio_credito = '2026-02-10',
  plazo_meses = 18,
  fecha_fin_credito = '2027-08-10',
  cuota_base = 875.66,
  tasa_interes = 10.00,
  monto_interes = 87.566,
  cuota_total_mensual = 963.226,
  mora_3_dias = 87.566,
  total_con_mora = 963.226,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'LW8HDNZ19SW001076';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '997 014 179',
  telefono_2 = '913 749 053',
  precio_total = 21444.00,
  pago_inicial = 3000.00,
  fecha_inicio_credito = '2026-09-02',
  plazo_meses = 24,
  fecha_fin_credito = '2028-02-09',
  cuota_base = 1339.28,
  tasa_interes = 10.00,
  monto_interes = 133.928,
  cuota_total_mensual = 1473.208,
  mora_3_dias = 133.928,
  total_con_mora = 1473.208,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MD2A45AX1TWG00414';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '968 830 275',
  telefono_2 = NULL,
  precio_total = 48840.00,
  pago_inicial = 10000.00,
  fecha_inicio_credito = '2026-02-09',
  plazo_meses = 24,
  fecha_fin_credito = '2028-02-09',
  cuota_base = 2616.03,
  tasa_interes = 10.00,
  monto_interes = 261.603,
  cuota_total_mensual = 2877.633,
  mora_3_dias = 261.603,
  total_con_mora = 2877.633,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MALB251AATM676127';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '902 584 335',
  telefono_2 = '951 056 233',
  precio_total = 19400.00,
  pago_inicial = 3000.00,
  fecha_inicio_credito = '2026-02-07',
  plazo_meses = 18,
  fecha_fin_credito = '2027-08-07',
  cuota_base = 1405.17,
  tasa_interes = 10.00,
  monto_interes = 140.517,
  cuota_total_mensual = 1545.687,
  mora_3_dias = 140.517,
  total_con_mora = 1545.687,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MD6M1LLH8T4A4030';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '934 574 787',
  telefono_2 = '931 563 690',
  precio_total = 21444.00,
  pago_inicial = 3000.00,
  fecha_inicio_credito = '2026-02-06',
  plazo_meses = 18,
  fecha_fin_credito = '2027-08-09',
  cuota_base = 1580.30,
  tasa_interes = 10.00,
  monto_interes = 158.03,
  cuota_total_mensual = 1738.33,
  mora_3_dias = 158.03,
  total_con_mora = 1738.33,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MD2A45AX7TWG00532';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = '4999-AY',
  telefono_1 = '970 725 581',
  telefono_2 = NULL,
  precio_total = 5300.00,
  pago_inicial = 1600.00,
  fecha_inicio_credito = '2026-02-04',
  plazo_meses = 18,
  fecha_fin_credito = '2027-08-04',
  cuota_base = 3417.02,
  tasa_interes = 10.00,
  monto_interes = 341.702,
  cuota_total_mensual = 3758.722,
  mora_3_dias = 341.702,
  total_con_mora = 3758.722,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = '157FMI4R1025272';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '940 873 801',
  telefono_2 = '933 600 465',
  precio_total = 48840.00,
  pago_inicial = 10000.00,
  fecha_inicio_credito = '2026-03-04',
  plazo_meses = 24,
  fecha_fin_credito = '2028-02-04',
  cuota_base = 2616.03,
  tasa_interes = 10.00,
  monto_interes = 261.603,
  cuota_total_mensual = 2877.633,
  mora_3_dias = 261.603,
  total_con_mora = 2877.633,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MALB251AATMG75828';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '903 273 124',
  telefono_2 = NULL,
  precio_total = 14090.00,
  pago_inicial = 3000.00,
  fecha_inicio_credito = '2026-02-04',
  plazo_meses = 24,
  fecha_fin_credito = '2028-02-04',
  cuota_base = 805.28,
  tasa_interes = 10.00,
  monto_interes = 80.528,
  cuota_total_mensual = 885.808,
  mora_3_dias = 80.528,
  total_con_mora = 885.808,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MD2A9AX0TWD60098';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '946302366',
  telefono_2 = NULL,
  precio_total = 47520.00,
  pago_inicial = 10000.00,
  fecha_inicio_credito = '2026-02-03',
  plazo_meses = 36,
  fecha_fin_credito = '2029-03-02',
  cuota_base = 2217.00,
  tasa_interes = 10.00,
  monto_interes = 221.7,
  cuota_total_mensual = 2438.7,
  mora_3_dias = 221.7,
  total_con_mora = 2438.7,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MALB251AATM678008';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = 'Y2K-698',
  telefono_1 = '924940530',
  telefono_2 = NULL,
  precio_total = 65083.00,
  pago_inicial = 20000.00,
  fecha_inicio_credito = '2026-02-03',
  plazo_meses = 18,
  fecha_fin_credito = '2027-08-03',
  cuota_base = 3637.34,
  tasa_interes = 10.00,
  monto_interes = 363.734,
  cuota_total_mensual = 4001.074,
  mora_3_dias = 363.734,
  total_con_mora = 4001.074,
  numero_cuota_pagada = 1,
  deudas_pendientes_detalle = 'Falta pagar 10 soles de GPS'
WHERE chasis = 'LB3SX2042TX800268';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '947597788',
  telefono_2 = NULL,
  precio_total = 14090.00,
  pago_inicial = 6900.00,
  fecha_inicio_credito = '2026-02-02',
  plazo_meses = 12,
  fecha_fin_credito = '2027-02-02',
  cuota_base = 812.14,
  tasa_interes = 10.00,
  monto_interes = 81.214,
  cuota_total_mensual = 893.354,
  mora_3_dias = 81.214,
  total_con_mora = 893.354,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MD2A49AXOTWD60019';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '941692274',
  telefono_2 = NULL,
  precio_total = 19600.00,
  pago_inicial = 4000.00,
  fecha_inicio_credito = '2026-02-02',
  plazo_meses = 18,
  fecha_fin_credito = '2027-08-02',
  cuota_base = 1336.62,
  tasa_interes = 10.00,
  monto_interes = 133.662,
  cuota_total_mensual = 1470.282,
  mora_3_dias = 133.662,
  total_con_mora = 1470.282,
  numero_cuota_pagada = NULL,
  deudas_pendientes_detalle = NULL
WHERE chasis = 'MD6M14LA3T4AA7197';

UPDATE registro_ventas_vehiculares
SET
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '996300410',
  telefono_2 = NULL,
  precio_total = 66204.00,
  pago_inicial = 39000.00,
  fecha_inicio_credito = '2026-02-02',
  plazo_meses = 60,
  fecha_fin_credito = '2031-02-02',
  cuota_base = 1422.51,
  tasa_interes = 10.00,
  monto_interes = 142.251,
  cuota_total_mensual = 1564.761,
  mora_3_dias = 142.251,
  total_con_mora = 1564.761,
  numero_cuota_pagada = 2,
  deudas_pendientes_detalle = NULL
WHERE chasis = '9BWBL6DF3TT359420';

