-- =============================================================================
-- EJECUTAR ESTE ARCHIVO COMPLETO EN phpMyAdmin (pestaña SQL)
-- 1) Seleccione su base de datos (motorpark / u322322994_motorpark, etc.)
-- 2) Pegue y ejecute TODO este script
-- 3) Al final debe ver 4 filas en "VERIFICACION_CCI"
-- =============================================================================

-- A) Medio de pago Interbancario en tabla pagos
ALTER TABLE pagos MODIFY COLUMN mediopago ENUM(
    'Yape',
    'Plin',
    'Transferencia Bancaria',
    'Efectivo',
    'Interbancario'
) NOT NULL;

-- B) Columnas en cuentaspago (si sale "Duplicate column", ignore esa línea y siga)
ALTER TABLE cuentaspago
    ADD COLUMN tipo_cuenta ENUM('Cuenta', 'CCI') NOT NULL DEFAULT 'Cuenta' AFTER moneda;

ALTER TABLE cuentaspago
    ADD COLUMN cuenta_corriente VARCHAR(40) NULL AFTER numcuenta;

UPDATE cuentaspago SET tipo_cuenta = 'Cuenta' WHERE tipo_cuenta IS NULL OR tipo_cuenta = '';

-- C) Bancos
INSERT INTO entidadespago (entidad, tipo) VALUES
    ('Interbank', 'Banco'),
    ('Scotiabank', 'Banco'),
    ('BBVA', 'Banco'),
    ('BCP', 'Banco')
ON DUPLICATE KEY UPDATE tipo = VALUES(tipo);

-- D) Cuentas CCI Yonda (imagen enviada)
INSERT INTO cuentaspago (identidadpago, moneda, numcuenta, tipo_cuenta, cuenta_corriente)
SELECT ep.identidadpago, 'Soles', '003-402-003004119355-55', 'CCI', '402-30041193-55'
FROM entidadespago ep WHERE ep.entidad = 'Interbank'
AND NOT EXISTS (SELECT 1 FROM cuentaspago c JOIN entidadespago e ON e.identidadpago = c.identidadpago
    WHERE e.entidad = 'Interbank' AND c.tipo_cuenta = 'CCI' AND c.numcuenta = '003-402-003004119355-55');

INSERT INTO cuentaspago (identidadpago, moneda, numcuenta, tipo_cuenta, cuenta_corriente)
SELECT ep.identidadpago, 'Soles', '00-930100000238199938', 'CCI', '00-2381999'
FROM entidadespago ep WHERE ep.entidad = 'Scotiabank'
AND NOT EXISTS (SELECT 1 FROM cuentaspago c JOIN entidadespago e ON e.identidadpago = c.identidadpago
    WHERE e.entidad = 'Scotiabank' AND c.tipo_cuenta = 'CCI' AND c.numcuenta = '00-930100000238199938');

INSERT INTO cuentaspago (identidadpago, moneda, numcuenta, tipo_cuenta, cuenta_corriente)
SELECT ep.identidadpago, 'Soles', '0-1121500010005711419', 'CCI', '00-1102150100057114'
FROM entidadespago ep WHERE ep.entidad = 'BBVA'
AND NOT EXISTS (SELECT 1 FROM cuentaspago c JOIN entidadespago e ON e.identidadpago = c.identidadpago
    WHERE e.entidad = 'BBVA' AND c.tipo_cuenta = 'CCI' AND c.numcuenta = '0-1121500010005711419');

INSERT INTO cuentaspago (identidadpago, moneda, numcuenta, tipo_cuenta, cuenta_corriente)
SELECT ep.identidadpago, 'Soles', '00219400146787602892', 'CCI', '1941467876028'
FROM entidadespago ep WHERE ep.entidad = 'BCP'
AND NOT EXISTS (SELECT 1 FROM cuentaspago c JOIN entidadespago e ON e.identidadpago = c.identidadpago
    WHERE e.entidad = 'BCP' AND c.tipo_cuenta = 'CCI' AND c.numcuenta = '00219400146787602892');

-- E) VERIFICACIÓN: debe listar 4 bancos
SELECT ep.entidad, cp.numcuenta AS cci, cp.cuenta_corriente, cp.moneda, cp.tipo_cuenta
FROM cuentaspago cp
JOIN entidadespago ep ON ep.identidadpago = cp.identidadpago
WHERE cp.tipo_cuenta = 'CCI'
ORDER BY ep.entidad;
