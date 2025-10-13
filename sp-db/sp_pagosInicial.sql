USE motorpark2;
DROP PROCEDURE sp_pagoInicial;DROP PROCEDURE IF EXISTS sp_pagoInicial;

DELIMITER //

CREATE PROCEDURE sp_pagoInicial(
    IN idconcepto_ INT,
    IN idcotizacion_ INT,
    IN idvehiculo_ INT,
    IN idcuentapago_ INT,
    IN idcolcaja_ INT,
    IN mediopago_ VARCHAR(80),
    IN numerotransaccion_ VARCHAR(30),
    IN fechapago_ DATE,
    IN amortizacion_ DECIMAL(10,2),
    IN saldorestante_ DECIMAL(10,2),
    IN comprobante_ VARCHAR(200),
    IN observacion_ VARCHAR(300)
)
BEGIN
    DECLARE v_disponibilidad VARCHAR(20);
    DECLARE v_idpago BIGINT DEFAULT 0;
    DECLARE v_cotizacion_pago INT;
    DECLARE v_current_status CHAR(1);

    START TRANSACTION;

    -- 1) Bloqueo de la fila del vehículo para evitar condiciones de carrera
    SELECT disponibilidad
    INTO v_disponibilidad
    FROM vehiculos
    WHERE idvehiculo = idvehiculo_
    FOR UPDATE;

    -- 2) Buscar si ya existe un pago inicial para este vehículo
    SELECT p.idcotizacion
    INTO v_cotizacion_pago
    FROM pagos p
    JOIN conceptospago cp ON cp.idconcepto = p.idconcepto
    WHERE p.idvehiculo = idvehiculo_
      AND cp.concepto = 'Inicial'
    ORDER BY p.idpago ASC
    LIMIT 1;

    -- 3) Si ya existe un pago y es de otra cotización → error
    IF v_cotizacion_pago IS NOT NULL AND v_cotizacion_pago <> idcotizacion_ THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'El vehículo ya fue separado por otra cotización';
    END IF;

    -- 4) Insertar pago (ligado a idcotizacion)
    INSERT INTO pagos(
        idconcepto, idcotizacion, idvehiculo, idcuentapago, idcolcaja,
        mediopago, numerotransaccion, fechapago, amortizacion, saldorestante,
        comprobante, observacion, tipo
    )
    VALUES(
        idconcepto_, idcotizacion_, idvehiculo_, IF(idcuentapago_ = 0, NULL, idcuentapago_), idcolcaja_,
        mediopago_, numerotransaccion_, fechapago_, amortizacion_, saldorestante_,
        comprobante_, observacion_, 'Otro'
    );

    SET v_idpago = LAST_INSERT_ID();
    UPDATE cotizaciones
    SET estadocotizacion = 'S' -- 'S' de Separado
    WHERE idcotizacion = idcotizacion_ AND estadocotizacion = 'P';
  

    -- 6) Si el vehículo aún no estaba separado/vendido → marcar como separado
    IF v_disponibilidad <> 'separado' AND v_disponibilidad <> 'vendido' THEN
        UPDATE vehiculos
        SET disponibilidad = 'separado', modificado = NOW()
        WHERE idvehiculo = idvehiculo_;
    END IF;

    COMMIT;

    SELECT v_idpago AS idpago;
END //

DELIMITER ;


