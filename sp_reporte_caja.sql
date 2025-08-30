USE motorpark;

DELIMITER $$
CREATE PROCEDURE spu_caja_reporte_completo_hoy()
BEGIN
    SELECT
        p.idpago,
        p.fechapago AS fecha,
        p.mediopago AS metodo_pago,
        p.numerotransaccion AS numero_operacion,
        p.amortizacion AS monto,
       
        ep.entidad AS entidad_bancaria,
        cp.numcuenta AS numero_cuenta
    FROM
        pagos p
    LEFT JOIN
        cuentaspago cp ON p.idcuentapago = cp.idcuentapago
    LEFT JOIN
        entidadespago ep ON cp.identidadpago = ep.identidadpago
    WHERE
        DATE(p.fechapago) = CURDATE() AND p.amortizacion > 0
    ORDER BY
        p.mediopago,
        p.fechapago;
END$$
DELIMITER ;

CALL spu_caja_reporte_completo_hoy();



-- SP QUE TRAE LOS PAGOS SOLO CON LOS DIAS DE PAGOS
-- DROP PROCEDURE ObtenerReportePagosPorFechas;
-- DELIMITER $$

-- CREATE PROCEDURE ObtenerReportePagosPorFechas(
--     IN fecha_inicio DATE,
--     IN fecha_fin DATE
-- )
-- BEGIN
--     SELECT
--         DATE(fechapago) AS dia,
--         SUM(CASE WHEN mediopago = 'Efectivo' THEN amortizacion ELSE 0 END) AS total_efectivo,
--         SUM(CASE WHEN mediopago = 'Yape' THEN amortizacion ELSE 0 END) AS total_yape,
--         SUM(CASE WHEN mediopago = 'Plin' THEN amortizacion ELSE 0 END) AS total_plin,
--         SUM(CASE WHEN mediopago = 'Transferencia Bancaria' THEN amortizacion ELSE 0 END) AS total_transferencia,
        
--         SUM(amortizacion) AS total_diario
--     FROM
--         pagos
--     WHERE
--         fechapago BETWEEN fecha_inicio AND fecha_fin
--     GROUP BY
--         dia
--     ORDER BY
--         dia ASC;
-- END$$

-- DELIMITER ;



-- SP QUE TRAE LOS PAGOS , INCLUSO LOS DIAS QUE NO HAY PAGOS.
DELIMITER $$
CREATE PROCEDURE ObtenerReportePagosPorFechas(
IN fecha_inicio DATE,
IN fecha_fin DATE
)
BEGIN
-- Crear una tabla temporal para almacenar todas las fechas en el rango
CREATE TEMPORARY TABLE IF NOT EXISTS fechaintervalo (
dia DATE
);

-- Variables para el bucle
SET @current_date = fecha_inicio;

-- Llenar la tabla temporal con todas las fechas entre la fecha de inicio y la fecha de fin
WHILE @current_date <= fecha_fin DO
    INSERT INTO fechaintervalo (dia) VALUES (@current_date);
    SET @current_date = DATE_ADD(@current_date, INTERVAL 1 DAY);
END WHILE;

-- Seleccionar datos y hacer un LEFT JOIN para incluir todos los días del rango
SELECT
    f.dia,
    -- Usar COALESCE para mostrar 0 en lugar de NULL para los días sin pagos
    COALESCE(SUM(CASE WHEN p.mediopago = 'Efectivo' THEN p.amortizacion ELSE 0 END), 0) AS total_efectivo,
    COALESCE(SUM(CASE WHEN p.mediopago = 'Yape' THEN p.amortizacion ELSE 0 END), 0) AS total_yape,
    COALESCE(SUM(CASE WHEN p.mediopago = 'Plin' THEN p.amortizacion ELSE 0 END), 0) AS total_plin,
    COALESCE(SUM(CASE WHEN p.mediopago = 'Transferencia Bancaria' THEN p.amortizacion ELSE 0 END), 0) AS total_transferencia,

    COALESCE(SUM(p.amortizacion), 0) AS total_diario
FROM
    fechaintervalo f
LEFT JOIN
    pagos p ON f.dia = DATE(p.fechapago)
GROUP BY
    f.dia
ORDER BY
    f.dia ASC;
DROP TEMPORARY TABLE IF EXISTS fechaintervalo;

END$$
DELIMITER ;

CALL ObtenerReportePagosPorFechas('2025-08-22', '2025-08-29');
