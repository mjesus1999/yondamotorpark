-- Migración segura de columnas para registro_ventas_vehiculares (según Excel).
-- Ejecutar en el VPS:
--   mysql -u yhondb -p motorpark < /var/www/yondamotorpark/sp-db/registro_ventas_vehiculares_migracion_excel.sql
--
-- Este script agrega columnas faltantes sin romper datos existentes.

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

SET @db_name := DATABASE();

-- Helper: agrega columna si no existe.
SET @t := 'registro_ventas_vehiculares';

-- placa
SET @has := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db_name AND table_name = @t AND column_name = 'placa'
);
SET @sql := IF(@has = 0,
  'ALTER TABLE registro_ventas_vehiculares ADD COLUMN placa VARCHAR(20) NULL AFTER estado_tramite',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- deudas_pendientes (numérico)
SET @has := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db_name AND table_name = @t AND column_name = 'deudas_pendientes'
);
SET @sql := IF(@has = 0,
  'ALTER TABLE registro_ventas_vehiculares ADD COLUMN deudas_pendientes DECIMAL(10,2) NULL AFTER pago_inicial',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- deudas_pendientes_detalle (texto)
SET @has := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db_name AND table_name = @t AND column_name = 'deudas_pendientes_detalle'
);
SET @sql := IF(@has = 0,
  'ALTER TABLE registro_ventas_vehiculares ADD COLUMN deudas_pendientes_detalle TEXT NULL AFTER deudas_pendientes',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- numero_cuota_pagada
SET @has := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db_name AND table_name = @t AND column_name = 'numero_cuota_pagada'
);
SET @sql := IF(@has = 0,
  'ALTER TABLE registro_ventas_vehiculares ADD COLUMN numero_cuota_pagada INT NULL',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- mora_3_dias / total_con_mora (crear si no existen y dejar en 3 decimales)
SET @has := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db_name AND table_name = @t AND column_name = 'mora_3_dias'
);
SET @sql := IF(@has = 0,
  'ALTER TABLE registro_ventas_vehiculares ADD COLUMN mora_3_dias DECIMAL(10,3) NULL',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db_name AND table_name = @t AND column_name = 'total_con_mora'
);
SET @sql := IF(@has = 0,
  'ALTER TABLE registro_ventas_vehiculares ADD COLUMN total_con_mora DECIMAL(10,3) NULL',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Si ya existían con DECIMAL(10,2), subir precisión a 3.
ALTER TABLE registro_ventas_vehiculares
  MODIFY COLUMN mora_3_dias DECIMAL(10,3) NULL,
  MODIFY COLUMN total_con_mora DECIMAL(10,3) NULL;

