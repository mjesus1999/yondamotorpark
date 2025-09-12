
USE motorpark2;

SELECT * FROM cuentaspago;
SELECT * FROM entidadespago;

-- Podria ser idnumcuenta agregar , en ves de entidad o como se podria manejar 
CREATE TABLE pagosOC (
    idpagooc INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    idorden INT NOT NULL, -- ID OC
    idlogistica INT NOT NULL, -- Persona que registro el pago
    identidadpago INT NOT NULL,
    fecharealpago DATETIME NOT NULL,
    numtransaccion VARCHAR(20) NOT NULL,
    moneda ENUM('USD', 'PEN') NOT NULL, -- Moneda en que se realizo el pago
    tipocambio DECIMAL(5,2) NULL, -- Tipo de cambio si es  que se paga en SOLES 
    valorUSD DECIMAL(10,2) NULL,
    amortizacion DECIMAL(10, 2) NOT NULL, -- Lo que se ha adelantado
    saldo DECIMAL(10, 2) NOT NULL, -- El saldo a pagar o lo que falta pagar si es que se ha hecho amortización
    comprobante VARCHAR(300) NOT NULL, -- Ruta del comprobante
    observaciones VARCHAR(400) NULL,
    modificado DATETIME NULL,   
    fecha DATETIME NOT NULL DEFAULT NOW(), -- Fecha y hora de que se regsitro el pago
    CONSTRAINT fk_idlo_pagoOC FOREIGN KEY (idlogistica) REFERENCES colaboradores (idcolaborador),
    CONSTRAINT fk_idorde_pagosOC FOREIGN KEY (idorden) REFERENCES ordenescompra (idordencompra),
    CONSTRAINT fk_identidadpago_pagosOC FOREIGN KEY (identidadpago) REFERENCES entidadespago (identidadpago);
) ENGINE = InnoDB;

-- ALTER TABLE pagosOC CHANGE COLUMN valorSoles valorUSD DECIMAL(10,2) NULL AFTER tipocambio;
-- UPDATE pagosOC SET identidadpago = 1;
-- ALTER TABLE pagosOC ADD COLUMN identidadpago INT NOT NULL AFTER idlogistica;
-- 
-- ALTER TABLE pagoSoc ADD COLUMN numtransaccion VARCHAR(20) NOT NULL AFTER fecharealpago;
-- ALTER TABLE pagosOC ADD COLUMN moneda ENUM('USD', 'PEN') NOT NULL AFTER numtransaccion;
-- ALTER TABLE pagosOC ADD COLUMN tipocambio DECIMAL(5,2) NULL AFTER moneda;

-- ALTER TABLE pagosOC ADD COLUMN observaciones VARCHAR(400) NULL AFTER comprobante;





CALL sp_oc_por_estado_reporte('proceso');



USE motorpark2;


DROP PROCEDURE IF EXISTS sp_reporte_general_oc_proceso;

DELIMITER //
CREATE PROCEDURE sp_reporte_general_oc_proceso()
BEGIN
    -- Declaración de variables para el Resumen Ejecutivo
    DECLARE total_oc_proceso INT;
    DECLARE total_vehiculos_pendientes INT;
    DECLARE deuda_global DECIMAL(15, 2);
    DECLARE pagos_recibidos DECIMAL(15, 2);
    DECLARE saldo_pendiente DECIMAL(15, 2);
    DECLARE avance_general DECIMAL(5, 2);

    -- 1. Consulta para el Resumen Ejecutivo (Primer conjunto de resultados)
    -- Contar Órdenes de Compra en estado 'proceso'
    SELECT COUNT(*)
    INTO total_oc_proceso
    FROM ordenescompra
    WHERE estado = 'proceso';

    -- Sumar los precios de compra de todos los vehículos de las OCs en proceso, aplicando el IGV (1.18)
    SELECT COALESCE(SUM(totalOC), 0)
    INTO deuda_global
    FROM (
        SELECT SUM(doc.preciocompra * 1.18) AS totalOC
        FROM detordencompra doc
        INNER JOIN ordenescompra oc ON doc.idordencompra = oc.idordencompra
        WHERE oc.estado = 'proceso'
        GROUP BY oc.idordencompra
    ) AS subquery;

    -- Sumar la amortización (pagos) de todas las OCs en proceso, convirtiendo PEN a USD
    SELECT COALESCE(SUM(totalPagado), 0)
    INTO pagos_recibidos
    FROM (
        SELECT SUM(
            CASE
                WHEN poc.moneda = 'PEN' AND poc.tipocambio > 0 THEN poc.amortizacion / poc.tipocambio
                ELSE poc.amortizacion
            END
        ) AS totalPagado
        FROM pagosOC poc
        INNER JOIN ordenescompra oc ON poc.idorden = oc.idordencompra
        WHERE oc.estado = 'proceso'
        GROUP BY oc.idordencompra
    ) AS subquery;

    -- Contar el total de vehículos en OCs en proceso
    SELECT COALESCE(COUNT(doc.idvehiculo), 0)
    INTO total_vehiculos_pendientes
    FROM detordencompra doc
    INNER JOIN ordenescompra oc ON doc.idordencompra = oc.idordencompra
    WHERE oc.estado = 'proceso';
    
    -- Calcular el saldo pendiente y el avance general
    SET saldo_pendiente = deuda_global - pagos_recibidos;
    SET avance_general = IF(deuda_global > 0, (pagos_recibidos / deuda_global) * 100, 0);
    
    -- Primer conjunto de resultados: Resumen Ejecutivo
    SELECT
        total_oc_proceso AS 'totalOCProceso',
        total_vehiculos_pendientes AS 'totalVehiculos',
        ROUND(deuda_global, 2) AS 'Deudaglobal',
        ROUND(pagos_recibidos, 2) AS 'totalPagosGlobal',
        ROUND(saldo_pendiente, 2) AS 'saldoPendienteGlobal',
        ROUND(avance_general, 2) AS 'porcentajeAvanceGlobal';

    -- Consulta para el Detalle de Órdenes de Compra y Vehículos (Segundo conjunto de resultados)
    SELECT
        CONCAT(oc.serie, '-', LPAD(oc.idordencompra, 5, '0')) AS 'OCIdentificador',
        con.nombrecomercial AS 'Concesionario',
        CONCAT_WS(', ', dep.departamento, p.provincia, d.distrito) AS 'ubicacionConcesionario',
        ROUND((
            SELECT IFNULL(SUM(preciocompra * 1.18), 0)
            FROM detordencompra
            WHERE idordencompra = oc.idordencompra
        ), 2) AS 'totalOC',
        ROUND((
            SELECT IFNULL(SUM(
                CASE 
                    WHEN moneda = 'PEN' AND tipocambio > 0 THEN amortizacion / tipocambio
                    ELSE amortizacion
                END
            ), 0)
            FROM pagosOC
            WHERE idorden = oc.idordencompra
        ), 2) AS 'pagado',
        ROUND((
            (SELECT IFNULL(SUM(preciocompra * 1.18), 0) FROM detordencompra WHERE idordencompra = oc.idordencompra) -
            (SELECT IFNULL(SUM(
                CASE 
                    WHEN moneda = 'PEN' AND tipocambio > 0 THEN amortizacion / tipocambio
                    ELSE amortizacion
                END
            ), 0) FROM pagosOC WHERE idorden = oc.idordencompra)
        ), 2) AS 'saldo',
        IF(
            (SELECT IFNULL(SUM(preciocompra * 1.18), 0) FROM detordencompra WHERE idordencompra = oc.idordencompra) > 0,
            ROUND(
                (
                    (SELECT IFNULL(SUM(
                        CASE 
                            WHEN moneda = 'PEN' AND tipocambio > 0 THEN amortizacion / tipocambio
                            ELSE amortizacion
                        END
                    ), 0) FROM pagosOC WHERE idorden = oc.idordencompra)
                    /
                    (SELECT IFNULL(SUM(preciocompra * 1.18), 0) FROM detordencompra WHERE idordencompra = oc.idordencompra)
                ) * 100, 2),
            0
        ) AS 'avancePorcentaje',
        (
            SELECT COUNT(d.idvehiculo) 
            FROM detordencompra d 
            WHERE d.idordencompra = oc.idordencompra
        ) AS 'totalVehiculos',
        oc.emision,
        GROUP_CONCAT(CONCAT(mar.marca, ' ', modl.modelo, ' / ', v.chasis) SEPARATOR ', ') AS 'Detalle de Vehículos'
    FROM
        ordenescompra oc
    LEFT JOIN
        detordencompra doc ON oc.idordencompra = doc.idordencompra
    LEFT JOIN
        tiendas t ON oc.idtienda = t.idtienda
    LEFT JOIN
        concesionarios con ON t.idconcesionario = con.idconcesionario
    LEFT JOIN
        vehiculos v ON doc.idvehiculo = v.idvehiculo
    LEFT JOIN
        modelos modl ON v.idmodelo = modl.idmodelo
    LEFT JOIN
        marcas mar ON modl.idmarca = mar.idmarca
    LEFT JOIN
        distritos d ON t.iddistrito = d.iddistrito
    LEFT JOIN
        provincias p ON d.idprovincia = p.idprovincia
    LEFT JOIN
        departamentos dep ON p.iddepartamento = dep.iddepartamento
    WHERE
        oc.estado = 'proceso'
    GROUP BY
        oc.idordencompra, ubicacionConcesionario
    ORDER BY
        oc.idordencompra DESC;

END 

DELIMITER ;
CALL sp_reporte_general_oc_proceso();