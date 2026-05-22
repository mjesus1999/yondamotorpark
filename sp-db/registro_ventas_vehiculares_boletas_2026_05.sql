-- Carga puntual de 4 registros para validación de boletas.
-- Fuente: Excel compartido por negocio (Mayo 2026).
--
-- Ejecutar en MySQL:
--   mysql -u <usuario> -p motorpark < sp-db/registro_ventas_vehiculares_boletas_2026_05.sql
--
-- Este script:
-- 1) Asegura columnas usadas por la importación.
-- 2) Actualiza por CHASIS si ya existe el registro.
-- 3) Inserta si no existe (evita duplicados por carga repetida).

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

SET @db_name := DATABASE();

-- Asegurar columna de detalle de deudas (texto).
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

-- Asegurar precisión de mora/total (3 decimales).
ALTER TABLE registro_ventas_vehiculares
  MODIFY COLUMN mora_3_dias DECIMAL(10,3) NULL,
  MODIFY COLUMN total_con_mora DECIMAL(10,3) NULL;

-- =========================
-- 1) YESENIA GIOVANNA PARIONA YACHE
-- =========================
UPDATE registro_ventas_vehiculares
SET
  fecha_venta = '2026-01-10',
  dni_cliente = '21871286',
  nombre_cliente = 'Yesenia Giovanna Pariona Yache',
  aval = NULL,
  dni_aval = NULL,
  direccion = 'Jr Colon n-501',
  modelo = 'N400',
  marca = 'CHEVROLET',
  chasis = 'LZWADAGA3TC802572',
  motor = 'LARICS72310004',
  color = 'Plata Twinkling',
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '917 793 934',
  telefono_2 = NULL,
  precio_total = 61142.00,
  pago_inicial = 22100.00,
  deudas_pendientes = NULL,
  deudas_pendientes_detalle = 'Por pagar GPS S/.10.00 (cuota 1 y cuota 2)',
  fecha_inicio_credito = '2026-01-10',
  plazo_meses = 60,
  fecha_fin_credito = '2031-01-10',
  cuota_base = 1811.90,
  tasa_interes = 10.00,
  monto_interes = 181.190,
  cuota_total_mensual = 1993.09,
  mora_3_dias = 181.190,
  total_con_mora = 1993.090,
  numero_cuota_pagada = NULL
WHERE chasis = 'LZWADAGA3TC802572';

INSERT INTO registro_ventas_vehiculares (
  fecha_venta, dni_cliente, nombre_cliente, aval, dni_aval, direccion,
  modelo, marca, chasis, motor, color, estado_tramite, placa, telefono_1, telefono_2,
  precio_total, pago_inicial, deudas_pendientes, deudas_pendientes_detalle,
  fecha_inicio_credito, plazo_meses, fecha_fin_credito,
  cuota_base, tasa_interes, monto_interes, cuota_total_mensual, mora_3_dias, total_con_mora, numero_cuota_pagada
)
SELECT
  '2026-01-10', '21871286', 'Yesenia Giovanna Pariona Yache', NULL, NULL, 'Jr Colon n-501',
  'N400', 'CHEVROLET', 'LZWADAGA3TC802572', 'LARICS72310004', 'Plata Twinkling', 'En tramite', NULL, '917 793 934', NULL,
  61142.00, 22100.00, NULL, 'Por pagar GPS S/.10.00 (cuota 1 y cuota 2)',
  '2026-01-10', 60, '2031-01-10',
  1811.90, 10.00, 181.190, 1993.09, 181.190, 1993.090, NULL
WHERE NOT EXISTS (
  SELECT 1 FROM registro_ventas_vehiculares WHERE chasis = 'LZWADAGA3TC802572'
);

-- =========================
-- 2) JIMY PALOMINO PEREZ
-- =========================
UPDATE registro_ventas_vehiculares
SET
  fecha_venta = '2026-01-12',
  dni_cliente = '44310275',
  nombre_cliente = 'Jimy Palomino Perez',
  aval = NULL,
  dni_aval = NULL,
  direccion = 'AA.HH 21 de noviembre MZ.G lote- 27',
  modelo = 'TERROTORY TREND AT',
  marca = 'FROD',
  chasis = 'LJXCU2BB8THF47958',
  motor = 'S9G097266',
  color = 'Azul pantera',
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '917 491 170',
  telefono_2 = NULL,
  precio_total = 95004.00,
  pago_inicial = 30000.00,
  deudas_pendientes = NULL,
  deudas_pendientes_detalle = 'Por pagar GPS S/.10.00 (cuotas 1, 2 y 3)',
  fecha_inicio_credito = '2026-01-12',
  plazo_meses = 36,
  fecha_fin_credito = '2029-01-12',
  cuota_base = 3783.34,
  tasa_interes = 10.00,
  monto_interes = 378.330,
  cuota_total_mensual = 4161.67,
  mora_3_dias = 378.330,
  total_con_mora = 4161.670,
  numero_cuota_pagada = NULL
WHERE chasis = 'LJXCU2BB8THF47958';

INSERT INTO registro_ventas_vehiculares (
  fecha_venta, dni_cliente, nombre_cliente, aval, dni_aval, direccion,
  modelo, marca, chasis, motor, color, estado_tramite, placa, telefono_1, telefono_2,
  precio_total, pago_inicial, deudas_pendientes, deudas_pendientes_detalle,
  fecha_inicio_credito, plazo_meses, fecha_fin_credito,
  cuota_base, tasa_interes, monto_interes, cuota_total_mensual, mora_3_dias, total_con_mora, numero_cuota_pagada
)
SELECT
  '2026-01-12', '44310275', 'Jimy Palomino Perez', NULL, NULL, 'AA.HH 21 de noviembre MZ.G lote- 27',
  'TERROTORY TREND AT', 'FROD', 'LJXCU2BB8THF47958', 'S9G097266', 'Azul pantera', 'En tramite', NULL, '917 491 170', NULL,
  95004.00, 30000.00, NULL, 'Por pagar GPS S/.10.00 (cuotas 1, 2 y 3)',
  '2026-01-12', 36, '2029-01-12',
  3783.34, 10.00, 378.330, 4161.67, 378.330, 4161.670, NULL
WHERE NOT EXISTS (
  SELECT 1 FROM registro_ventas_vehiculares WHERE chasis = 'LJXCU2BB8THF47958'
);

-- =========================
-- 3) MARIA ESTHER PACHAS CASTILLA
-- =========================
UPDATE registro_ventas_vehiculares
SET
  fecha_venta = '2026-02-10',
  dni_cliente = '41678324',
  nombre_cliente = 'Maria Esther Pachas Castilla',
  aval = NULL,
  dni_aval = NULL,
  direccion = 'Calle Jose Anton 179',
  modelo = 'TVS KING DURAMAX 225 DEPORTIVA GNV',
  marca = 'TVS',
  chasis = 'MDGM1LCHOT4AA0697',
  motor = 'AL4AT41A7700',
  color = 'Rojo',
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '926 761 714',
  telefono_2 = NULL,
  precio_total = 19299.00,
  pago_inicial = 4000.00,
  deudas_pendientes = NULL,
  deudas_pendientes_detalle = 'Por pagar GPS S/.10.00 (cuotas 1, 2 y 3)',
  fecha_inicio_credito = '2026-02-10',
  plazo_meses = 18,
  fecha_fin_credito = '2027-08-10',
  cuota_base = 1310.83,
  tasa_interes = 10.00,
  monto_interes = 131.080,
  cuota_total_mensual = 1441.91,
  mora_3_dias = 131.080,
  total_con_mora = 1441.910,
  numero_cuota_pagada = NULL
WHERE chasis = 'MDGM1LCHOT4AA0697';

INSERT INTO registro_ventas_vehiculares (
  fecha_venta, dni_cliente, nombre_cliente, aval, dni_aval, direccion,
  modelo, marca, chasis, motor, color, estado_tramite, placa, telefono_1, telefono_2,
  precio_total, pago_inicial, deudas_pendientes, deudas_pendientes_detalle,
  fecha_inicio_credito, plazo_meses, fecha_fin_credito,
  cuota_base, tasa_interes, monto_interes, cuota_total_mensual, mora_3_dias, total_con_mora, numero_cuota_pagada
)
SELECT
  '2026-02-10', '41678324', 'Maria Esther Pachas Castilla', NULL, NULL, 'Calle Jose Anton 179',
  'TVS KING DURAMAX 225 DEPORTIVA GNV', 'TVS', 'MDGM1LCHOT4AA0697', 'AL4AT41A7700', 'Rojo', 'En tramite', NULL, '926 761 714', NULL,
  19299.00, 4000.00, NULL, 'Por pagar GPS S/.10.00 (cuotas 1, 2 y 3)',
  '2026-02-10', 18, '2027-08-10',
  1310.83, 10.00, 131.080, 1441.91, 131.080, 1441.910, NULL
WHERE NOT EXISTS (
  SELECT 1 FROM registro_ventas_vehiculares WHERE chasis = 'MDGM1LCHOT4AA0697'
);

-- =========================
-- 4) JOSELYN FRIAS LOYOLA TUPAC
-- =========================
UPDATE registro_ventas_vehiculares
SET
  fecha_venta = '2026-01-08',
  dni_cliente = '48320560',
  nombre_cliente = 'Joselyn Frias Loyola Tupac',
  aval = NULL,
  dni_aval = NULL,
  direccion = 'Sanchez Cerro 1000',
  modelo = 'RE AUTORIKSHA TORITO 4T',
  marca = 'TORITO BAJAJ',
  chasis = 'MD2A45AX6TWF00048',
  motor = 'AZXWSF13003',
  color = 'Rojo',
  estado_tramite = 'En tramite',
  placa = NULL,
  telefono_1 = '936 394 218',
  telefono_2 = NULL,
  precio_total = 21444.00,
  pago_inicial = 4000.00,
  deudas_pendientes = NULL,
  deudas_pendientes_detalle = 'Por pagar GPS S/.10.00 (cuotas 1, 2 y 3)',
  fecha_inicio_credito = '2026-01-08',
  plazo_meses = 24,
  fecha_fin_credito = '2028-01-08',
  cuota_base = 1266.66,
  tasa_interes = 10.00,
  monto_interes = 126.670,
  cuota_total_mensual = 1393.33,
  mora_3_dias = 126.670,
  total_con_mora = 1393.330,
  numero_cuota_pagada = NULL
WHERE chasis = 'MD2A45AX6TWF00048';

INSERT INTO registro_ventas_vehiculares (
  fecha_venta, dni_cliente, nombre_cliente, aval, dni_aval, direccion,
  modelo, marca, chasis, motor, color, estado_tramite, placa, telefono_1, telefono_2,
  precio_total, pago_inicial, deudas_pendientes, deudas_pendientes_detalle,
  fecha_inicio_credito, plazo_meses, fecha_fin_credito,
  cuota_base, tasa_interes, monto_interes, cuota_total_mensual, mora_3_dias, total_con_mora, numero_cuota_pagada
)
SELECT
  '2026-01-08', '48320560', 'Joselyn Frias Loyola Tupac', NULL, NULL, 'Sanchez Cerro 1000',
  'RE AUTORIKSHA TORITO 4T', 'TORITO BAJAJ', 'MD2A45AX6TWF00048', 'AZXWSF13003', 'Rojo', 'En tramite', NULL, '936 394 218', NULL,
  21444.00, 4000.00, NULL, 'Por pagar GPS S/.10.00 (cuotas 1, 2 y 3)',
  '2026-01-08', 24, '2028-01-08',
  1266.66, 10.00, 126.670, 1393.33, 126.670, 1393.330, NULL
WHERE NOT EXISTS (
  SELECT 1 FROM registro_ventas_vehiculares WHERE chasis = 'MD2A45AX6TWF00048'
);

-- Verificación rápida
SELECT
  id,
  dni_cliente,
  nombre_cliente,
  chasis,
  precio_total,
  pago_inicial,
  fecha_inicio_credito,
  plazo_meses,
  cuota_base,
  monto_interes,
  cuota_total_mensual
FROM registro_ventas_vehiculares
WHERE chasis IN (
  'LZWADAGA3TC802572',
  'LJXCU2BB8THF47958',
  'MDGM1LCHOT4AA0697',
  'MD2A45AX6TWF00048'
)
ORDER BY nombre_cliente;
