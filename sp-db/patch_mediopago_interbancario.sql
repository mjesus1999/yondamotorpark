-- OBSOLETO: usar patch_cuentas_interbancario_yonda.sql (incluye ENUM + cuentas CCI).
-- Agrega "Interbancario" al ENUM de mediopago en pagos (caja / pagos compuestos).
-- Ejecutar en phpMyAdmin con la BD motorpark seleccionada.

ALTER TABLE pagos MODIFY COLUMN mediopago ENUM(
    'Yape',
    'Plin',
    'Transferencia Bancaria',
    'Efectivo',
    'Interbancario'
) NOT NULL;
