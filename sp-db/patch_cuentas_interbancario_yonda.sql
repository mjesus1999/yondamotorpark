-- Cuentas CCI Yonda + soporte Interbancario en pagos y arqueo.
-- Ejecutar en phpMyAdmin con la BD motorpark seleccionada (una sola vez).

-- 1) Medio de pago Interbancario
ALTER TABLE pagos MODIFY COLUMN mediopago ENUM(
    'Yape',
    'Plin',
    'Transferencia Bancaria',
    'Efectivo',
    'Interbancario'
) NOT NULL;

-- 2) Tipo de cuenta: corriente (transferencia) vs CCI (interbancario)
-- Si la columna ya existe, omitir la línea que falle y continuar.
ALTER TABLE cuentaspago
    ADD COLUMN tipo_cuenta ENUM('Cuenta', 'CCI') NOT NULL DEFAULT 'Cuenta' AFTER moneda;

ALTER TABLE cuentaspago
    ADD COLUMN cuenta_corriente VARCHAR(40) NULL
        COMMENT 'N° cuenta en el mismo banco (referencia cuando tipo_cuenta=CCI)' AFTER numcuenta;

-- 3) Bancos (entidades)
INSERT INTO entidadespago (entidad, tipo) VALUES
    ('Interbank', 'Banco'),
    ('Scotiabank', 'Banco'),
    ('BBVA', 'Banco'),
    ('BCP', 'Banco')
ON DUPLICATE KEY UPDATE tipo = VALUES(tipo);

-- 4) CCI soles YONDA Y GRUPO HUARACA (RUC 20609396866)
INSERT INTO cuentaspago (identidadpago, moneda, numcuenta, tipo_cuenta, cuenta_corriente)
SELECT ep.identidadpago, 'Soles', '003-402-003004119355-55', 'CCI', '402-30041193-55'
FROM entidadespago ep
WHERE ep.entidad = 'Interbank'
  AND NOT EXISTS (
      SELECT 1 FROM cuentaspago c
      INNER JOIN entidadespago e ON e.identidadpago = c.identidadpago
      WHERE e.entidad = 'Interbank' AND c.tipo_cuenta = 'CCI'
  );

INSERT INTO cuentaspago (identidadpago, moneda, numcuenta, tipo_cuenta, cuenta_corriente)
SELECT ep.identidadpago, 'Soles', '00-930100000238199938', 'CCI', '00-2381999'
FROM entidadespago ep
WHERE ep.entidad = 'Scotiabank'
  AND NOT EXISTS (
      SELECT 1 FROM cuentaspago c
      INNER JOIN entidadespago e ON e.identidadpago = c.identidadpago
      WHERE e.entidad = 'Scotiabank' AND c.tipo_cuenta = 'CCI'
  );

INSERT INTO cuentaspago (identidadpago, moneda, numcuenta, tipo_cuenta, cuenta_corriente)
SELECT ep.identidadpago, 'Soles', '0-1121500010005711419', 'CCI', '00-1102150100057114'
FROM entidadespago ep
WHERE ep.entidad = 'BBVA'
  AND NOT EXISTS (
      SELECT 1 FROM cuentaspago c
      INNER JOIN entidadespago e ON e.identidadpago = c.identidadpago
      WHERE e.entidad = 'BBVA' AND c.tipo_cuenta = 'CCI'
  );

INSERT INTO cuentaspago (identidadpago, moneda, numcuenta, tipo_cuenta, cuenta_corriente)
SELECT ep.identidadpago, 'Soles', '00219400146787602892', 'CCI', '1941467876028'
FROM entidadespago ep
WHERE ep.entidad = 'BCP'
  AND NOT EXISTS (
      SELECT 1 FROM cuentaspago c
      INNER JOIN entidadespago e ON e.identidadpago = c.identidadpago
      WHERE e.entidad = 'BCP' AND c.tipo_cuenta = 'CCI'
  );
