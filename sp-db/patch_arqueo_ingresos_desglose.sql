-- Desglose de ingresos por medio de pago en arqueo (Efectivo, Yape, Plin, Transferencia, Interbancario).
-- Ejecutar en phpMyAdmin con la BD motorpark seleccionada.

DROP PROCEDURE IF EXISTS sp_obtener_ingresos_desde;

DELIMITER $$

CREATE PROCEDURE sp_obtener_ingresos_desde(
    IN fecha_ref DATE,
    IN hora_ref TIME
)
BEGIN
    DECLARE limite DATETIME;
    SET limite = TIMESTAMP(fecha_ref, hora_ref);

    SELECT
        COALESCE(SUM(CASE WHEN mediopago = 'Efectivo' THEN amortizacion ELSE 0 END), 0) AS ingresos_efectivo_nuevos,
        COALESCE(SUM(CASE WHEN mediopago = 'Yape' THEN amortizacion ELSE 0 END), 0) AS ingresos_yape,
        COALESCE(SUM(CASE WHEN mediopago = 'Plin' THEN amortizacion ELSE 0 END), 0) AS ingresos_plin,
        COALESCE(SUM(CASE WHEN mediopago = 'Transferencia Bancaria' THEN amortizacion ELSE 0 END), 0) AS ingresos_transferencia,
        COALESCE(SUM(CASE WHEN mediopago = 'Interbancario' THEN amortizacion ELSE 0 END), 0) AS ingresos_interbancario,
        COALESCE(SUM(CASE WHEN mediopago NOT IN ('Efectivo') THEN amortizacion ELSE 0 END), 0) AS ingresos_digital_nuevos
    FROM pagos
    WHERE fecharegistro > limite
      AND amortizacion > 0;
END$$

DELIMITER ;
