
USE motorpark;
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
    IN observacion_ VARCHAR(300),
    IN moneda_ ENUM('USD', 'PEN'),
    IN montomonedaoriginal_ DECIMAL(10,2),
    IN tipocambioaplicado_ DECIMAL(10,4)
)
BEGIN
    DECLARE v_disponibilidad VARCHAR(20);
    DECLARE v_idpago BIGINT DEFAULT 0;
    DECLARE v_cotizacion_pago INT;
    DECLARE v_vendido_al_contado INT DEFAULT 0;

    START TRANSACTION;

    -- Bloqueo de la fila del vehículo para evitar condiciones de carrera
    SELECT disponibilidad
    INTO v_disponibilidad
    FROM vehiculos
    WHERE idvehiculo = idvehiculo_
    FOR UPDATE;
    SELECT COUNT(*)
    INTO v_vendido_al_contado
    FROM pagos
    WHERE idvehiculo = idvehiculo_ AND idconcepto = 1; 

    IF v_vendido_al_contado > 0 THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'Operación cancelada: El vehículo ya fue vendido al contado.';
    END IF;
    SELECT p.idcotizacion
    INTO v_cotizacion_pago
    FROM pagos p
    JOIN conceptospago cp ON cp.idconcepto = p.idconcepto
    WHERE p.idvehiculo = idvehiculo_
      AND cp.concepto = 'Inicial'
    ORDER BY p.idpago ASC
    LIMIT 1;

    -- Si ya existe un pago y es de otra cotización → error
    IF v_cotizacion_pago IS NOT NULL AND v_cotizacion_pago <> idcotizacion_ THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'El vehículo ya fue separado o vendido por otra cotización.';
    END IF;

    -- Insertar pago 
    INSERT INTO pagos(
        idconcepto, idcotizacion, idvehiculo, idcuentapago, idcolcaja,
        mediopago, numerotransaccion, fechapago, amortizacion, saldorestante,
        comprobante, observacion, tipo,
        moneda, montomonedaoriginal, tipocambioaplicado
    )
    VALUES(
        idconcepto_, idcotizacion_, idvehiculo_, IF(idcuentapago_ = 0, NULL, idcuentapago_), idcolcaja_,
        mediopago_, numerotransaccion_, fechapago_, amortizacion_, saldorestante_,
        comprobante_, observacion_, 'Otro',
        moneda_, montomonedaoriginal_, tipocambioaplicado_
    );

    SET v_idpago = LAST_INSERT_ID();
    
    -- Actualizar estado de la cotización a 'Separado'
    UPDATE cotizaciones
    SET estadocotizacion = 'S'
    WHERE idcotizacion = idcotizacion_ AND estadocotizacion = 'P';

    -- Si el vehículo aún no estaba separado/vendido → marcar como separado
    IF v_disponibilidad <> 'separado' AND v_disponibilidad <> 'vendido' THEN
        UPDATE vehiculos
        SET disponibilidad = 'separado', modificado = NOW()
        WHERE idvehiculo = idvehiculo_;
    END IF;

    COMMIT;

    SELECT v_idpago AS idpago;
END //

DELIMITER ;




DROP PROCEDURE sp_getActaSeparacionByIdCotizacion;



DELIMITER //
CREATE PROCEDURE sp_getActaSeparacionByIdCotizacion(IN idcotizacion_ INT)
BEGIN
	SELECT
		cot.idcotizacion,
		cot.estadocotizacion,
		cl.tipocliente,
		CASE 
			WHEN cl.tipocliente = 'P' THEN CONCAT(per.apellidos, ' ', per.nombres)
			WHEN cl.tipocliente = 'E' THEN emp.razonsocial
		END AS cliente,

		CASE  
			WHEN cl.tipocliente = 'P' THEN per.nrodoc
			WHEN cl.tipocliente = 'E' THEN emp.ruc
		END AS nrodoc,

		CASE 
			WHEN cl.tipocliente = 'P' THEN per.direccion
			WHEN cl.tipocliente = 'E' THEN emp.direccion
		END AS direccion,

		CASE 
			WHEN cl.tipocliente = 'P' THEN dist.distrito
			WHEN cl.tipocliente = 'E' THEN distemp.distrito
		END AS distrito,

		CASE 
			WHEN cl.tipocliente = 'P' THEN pro.provincia
			WHEN cl.tipocliente = 'E' THEN proemp.provincia
		END AS provincia,

		CASE 
			WHEN cl.tipocliente = 'P' THEN dep.departamento
			WHEN cl.tipocliente = 'E' THEN depemp.departamento
		END AS departamento,

		CASE 
			WHEN cl.tipocliente = 'P' THEN per.telprimario
			WHEN cl.tipocliente = 'E' THEN emp.telprimario
		END AS telprimario,

		p.moneda,
		CASE
			WHEN p.moneda = 'USD' THEN p.montomonedaoriginal
			ELSE p.amortizacion
		END AS amortizacion,
		p.numerotransaccion,    
		p.mediopago,
		DATE_FORMAT(p.fechapago, '%d/%m/%Y') AS fechapago,
		ent.entidad,  
		mar.marca,
		mode.modelo,
		v.color,
		mode.anio,
		comb.combustible

	FROM cotizaciones cot
		INNER JOIN pagos p ON p.idcotizacion = cot.idcotizacion
		INNER JOIN vehiculos v ON p.idvehiculo = v.idvehiculo
		INNER JOIN modelos mode ON v.idmodelo = mode.idmodelo
		INNER JOIN marcas mar ON mode.idmarca = mar.idmarca
		LEFT JOIN cuentaspago cup ON p.idcuentapago = cup.idcuentapago  
		LEFT JOIN entidadespago ent ON cup.identidadpago = ent.identidadpago    
		INNER JOIN combustibles comb ON v.idcombustible = comb.idcombustible
		INNER JOIN conceptospago cp ON p.idconcepto = cp.idconcepto
		INNER JOIN clientes cl ON cot.idcliente = cl.idcliente

		-- JOINS PERSONA / EMPRESA
		LEFT JOIN personas per ON cl.idpersona = per.idpersona
		LEFT JOIN empresas emp ON cl.idempresa = emp.idempresa

		-- UBIGEO PERSONA
		LEFT JOIN distritos dist ON per.iddistrito = dist.iddistrito
		LEFT JOIN provincias pro ON dist.idprovincia = pro.idprovincia
		LEFT JOIN departamentos dep ON pro.iddepartamento = dep.iddepartamento

		-- UBIGEO EMPRESA
		LEFT JOIN distritos distemp ON emp.iddistrito = distemp.iddistrito
		LEFT JOIN provincias proemp ON distemp.idprovincia = proemp.idprovincia
		LEFT JOIN departamentos depemp ON proemp.iddepartamento = depemp.iddepartamento

	WHERE 
		cot.idcotizacion = idcotizacion_
		AND cp.concepto = 'Inicial' 
		AND cot.estadocotizacion = 'S'
		AND p.idpago = (
			SELECT MIN(p2.idpago)
			FROM pagos p2
			WHERE p2.idcotizacion = cot.idcotizacion
			  AND p2.idconcepto = p.idconcepto
		)
	ORDER BY p.fecharegistro ASC;
END //

DELIMITER ;


CALL sp_getActaSeparacionByIdCotizacion(92);

select * from cotizaciones;