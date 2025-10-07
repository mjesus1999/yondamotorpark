-- VISTA DE COBRANZA
USE motorpark;
-- 1) ESTADÍSTICAS + RESUMEN POR CONTRATO 

DROP PROCEDURE IF EXISTS sp_get_estadisticas_cobranza;
DELIMITER $$
CREATE PROCEDURE sp_get_estadisticas_cobranza()
BEGIN
    -- 1) ESTADÍSTICAS
    SELECT
        -- TOTAL DE CONTRATOS
        (SELECT COUNT(DISTINCT c2.idcontrato)
         FROM contratos c2
         JOIN cronogramas cr2 ON cr2.idcontrato = c2.idcontrato
         WHERE c2.estado = 'ACT' AND cr2.estado IN ('Pendiente','Vencido')
        ) AS total_deudores,
        
        -- CUOTA POR VENCER
        (SELECT COUNT(DISTINCT c3.idcontrato)
         FROM contratos c3
         JOIN cronogramas cr3 ON cr3.idcontrato = c3.idcontrato
         WHERE c3.estado = 'ACT'
           AND cr3.estado IN ('Pendiente','Vencido')
           AND DATEDIFF(cr3.fechapago, CURDATE()) BETWEEN 0 AND 3
        ) AS por_vencer_3dias,
        
        -- CUOTAS VENCIDAS
        (SELECT COUNT(DISTINCT c4.idcontrato)
         FROM contratos c4
         JOIN cronogramas cr4 ON cr4.idcontrato = c4.idcontrato
         WHERE c4.estado = 'ACT'
           AND cr4.estado = 'Vencido'
        ) AS contratos_con_vencidos,
        
        -- TOTAL POR COBRAR
        (SELECT IFNULL(SUM(cr5.abonocapital + cr5.interes + IFNULL(cr5.penalidad,0)),0)
         FROM contratos c5
         JOIN cronogramas cr5 ON cr5.idcontrato = c5.idcontrato
         WHERE c5.estado = 'ACT'
           AND cr5.estado IN ('Pendiente','Vencido')
        ) AS total_por_cobrar,
        
        -- TOTAL DE CUOTAS PAGADAS
        (SELECT COUNT(*)
         FROM cronogramas crp
         JOIN contratos cpr ON crp.idcontrato = cpr.idcontrato
         WHERE cpr.estado = 'ACT'
           AND crp.estado = 'Pagado'
        ) AS total_cuotas_pagadas;
    
END$$
DELIMITER ;

-- 2) RESUMEN POR CONTRATO
/*
-- trae con los montos
DROP PROCEDURE IF EXISTS sp_get_tarjetas_cobranza;
DELIMITER $$
CREATE PROCEDURE sp_get_tarjetas_cobranza()
BEGIN
    SELECT 
        c.idcontrato,
        
        -- Datos del cliente
        CASE 
            WHEN cl.tipocliente = 'P' THEN CONCAT(p.apellidos, ' ', p.nombres)
            WHEN cl.tipocliente = 'E' THEN e.razonsocial
        END AS nombre_cliente,
        
        CASE 
            WHEN cl.tipocliente = 'P' THEN p.telprimario
            WHEN cl.tipocliente = 'E' THEN e.telprimario
        END AS telefono,
        
        -- Local
        CONCAT(d.distrito, ' / ', pr.provincia) AS local,
        
        -- Vehículo
        CONCAT(ma.marca, ' ', mo.modelo) AS tipo_vehiculo,
        
        -- Próxima cuota pendiente o vencida
        cr.fechapago AS fecha_vencimiento,
        cr.numcuota,
        
        -- Monto de la cuota (capital + interés + penalidad si aplica)
        (cr.abonocapital + cr.interes + IFNULL(cr.penalidad, 0)) AS monto_cuota,
        
        -- Total deuda pendiente del contrato
        (SELECT SUM(cr2.abonocapital + cr2.interes + IFNULL(cr2.penalidad, 0))
         FROM cronogramas cr2
         WHERE cr2.idcontrato = c.idcontrato
           AND cr2.estado IN ('Pendiente', 'Vencido')
        ) AS deuda_total,
        
        -- Estado basado en días hasta vencimiento
        CASE
            WHEN cr.estado = 'Vencido' THEN 'VENCIDO'
            WHEN DATEDIFF(cr.fechapago, CURDATE()) = 0 THEN 'VENCE HOY'
            WHEN DATEDIFF(cr.fechapago, CURDATE()) BETWEEN 1 AND 3 THEN CONCAT('Vence en ', DATEDIFF(cr.fechapago, CURDATE()), ' días')
            ELSE 'POR VENCER'
        END AS estado_vencimiento,
        
        -- Días para vencimiento (útil para ordenamiento)
        DATEDIFF(cr.fechapago, CURDATE()) AS dias_vencimiento
        
    FROM contratos c
    INNER JOIN cotizaciones cot ON c.idcotizacion = cot.idcotizacion
    INNER JOIN clientes cl ON cot.idcliente = cl.idcliente
    LEFT JOIN personas p ON cl.idpersona = p.idpersona
    LEFT JOIN empresas e ON cl.idempresa = e.idempresa
    INNER JOIN locales l ON c.idlocal = l.idlocal
    INNER JOIN distritos d ON l.iddistrito = d.iddistrito
    INNER JOIN provincias pr ON d.idprovincia = pr.idprovincia
    INNER JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    INNER JOIN modelos mo ON v.idmodelo = mo.idmodelo
    INNER JOIN marcas ma ON mo.idmarca = ma.idmarca
    INNER JOIN (
        -- Subconsulta para obtener la próxima cuota pendiente o vencida de cada contrato
        SELECT cr1.*
        FROM cronogramas cr1
        INNER JOIN (
            SELECT idcontrato, MIN(fechapago) AS min_fecha
            FROM cronogramas
            WHERE estado IN ('Pendiente', 'Vencido')
            GROUP BY idcontrato
        ) cr_min ON cr1.idcontrato = cr_min.idcontrato 
                AND cr1.fechapago = cr_min.min_fecha
        WHERE cr1.estado IN ('Pendiente', 'Vencido')
    ) cr ON c.idcontrato = cr.idcontrato
    
    WHERE c.estado = 'ACT'
      AND cr.estado IN ('Pendiente', 'Vencido')
      AND DATEDIFF(cr.fechapago, CURDATE()) <= 3  -- Solo los que vencen en 3 días o menos, o ya vencidos
    
    ORDER BY 
        CASE 
            WHEN cr.estado = 'Vencido' THEN 1
            WHEN DATEDIFF(cr.fechapago, CURDATE()) = 0 THEN 2
            ELSE 3
        END,
        cr.fechapago DESC;
        
END$$
DELIMITER ;
*/

DROP PROCEDURE IF EXISTS sp_get_resumen_cobranza;
DELIMITER $$
CREATE PROCEDURE sp_get_resumen_cobranza()
BEGIN
    SELECT
        c.idcontrato,
        CONCAT(p.apellidos, ' ', p.nombres) AS cliente,
        p.telprimario AS telefono,
        CONCAT(m.marca, ' ', mo.modelo) AS vehiculo,
        CONCAT(COALESCE(d.distrito,'Sin distrito'), ' / ', COALESCE(pro.provincia,'Sin provincia')) AS tienda,
        cot.numcuotas AS cuotas_totales,
        
        /* DEUDA TOTAL: suma de cuotas Pendiente/Vencido para el contrato */
        IFNULL(SUM(CASE WHEN cro.estado IN ('Pendiente','Vencido') THEN (cro.abonocapital + cro.interes + IFNULL(cro.penalidad,0)) ELSE 0 END), 0) AS deuda_total,
        
        /* NUMERO DE CUOTAS VENCIDAS */
        SUM(cro.estado = 'Vencido') AS cuotas_vencidas,
        
        /* NUMERO DE CUOTAS PAGADAS */
        (SELECT COUNT(*)
         FROM cronogramas crp
         WHERE crp.idcontrato = c.idcontrato
           AND crp.estado = 'Pagado'
        ) AS cuotas_pagadas,
        
        /* monto de la próxima cuota pendiente/vencida */
        (SELECT (crn.abonocapital + crn.interes + IFNULL(crn.penalidad,0))
         FROM cronogramas crn
         WHERE crn.idcontrato = c.idcontrato
           AND crn.estado IN ('Pendiente','Vencido')
         ORDER BY crn.fechapago
         LIMIT 1
        ) AS monto_cuota,
        
        /* fecha próxima de las cuotas pendientes/vencidas */
        (SELECT MIN(crn2.fechapago)
         FROM cronogramas crn2
         WHERE crn2.idcontrato = c.idcontrato
           AND crn2.estado IN ('Pendiente','Vencido')
        ) AS prox_fechapago,
        
        /* estado legible según diferencia de días con la fecha próxima */
        CASE
            WHEN (SELECT DATEDIFF(MIN(crn3.fechapago), CURDATE())
                  FROM cronogramas crn3
                  WHERE crn3.idcontrato = c.idcontrato
                    AND crn3.estado IN ('Pendiente','Vencido')
                 ) = 3 THEN 'Vence en 3 días'
            WHEN (SELECT DATEDIFF(MIN(crn3.fechapago), CURDATE())
                  FROM cronogramas crn3
                  WHERE crn3.idcontrato = c.idcontrato
                    AND crn3.estado IN ('Pendiente','Vencido')
                 ) = 2 THEN 'Vence en 2 días'
            WHEN (SELECT DATEDIFF(MIN(crn3.fechapago), CURDATE())
                  FROM cronogramas crn3
                  WHERE crn3.idcontrato = c.idcontrato
                    AND crn3.estado IN ('Pendiente','Vencido')
                 ) = 1 THEN 'Vence mañana'
            WHEN (SELECT DATEDIFF(MIN(crn3.fechapago), CURDATE())
                  FROM cronogramas crn3
                  WHERE crn3.idcontrato = c.idcontrato
                    AND crn3.estado IN ('Pendiente','Vencido')
                 ) = 0 THEN 'Vence HOY'
            WHEN (SELECT DATEDIFF(MIN(crn3.fechapago), CURDATE())
                  FROM cronogramas crn3
                  WHERE crn3.idcontrato = c.idcontrato
                    AND crn3.estado IN ('Pendiente','Vencido')
                 ) < 0 THEN CONCAT('Vencido hace ', ABS(
                 (SELECT DATEDIFF(MIN(crn3.fechapago), CURDATE())
				   FROM cronogramas crn3
				   WHERE crn3.idcontrato = c.idcontrato
					 AND crn3.estado IN ('Pendiente','Vencido')
				  )), ' días')
            ELSE CONCAT('Vence en ', 
            (SELECT DATEDIFF(MIN(crn3.fechapago), CURDATE())
			 FROM cronogramas crn3
			 WHERE crn3.idcontrato = c.idcontrato
			   AND crn3.estado IN ('Pendiente','Vencido')
			), ' días')
        END AS estado_vencimiento
        
    FROM contratos c
    JOIN cotizaciones cot ON c.idcotizacion = cot.idcotizacion
    JOIN clientes cli ON cot.idcliente = cli.idcliente
    JOIN personas p ON cli.idpersona = p.idpersona
    JOIN cronogramas cro ON cro.idcontrato = c.idcontrato
    JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    JOIN modelos mo ON v.idmodelo = mo.idmodelo
    JOIN marcas m ON mo.idmarca = m.idmarca
    LEFT JOIN locales loc ON c.idlocal = loc.idlocal
    LEFT JOIN distritos d ON loc.iddistrito = d.iddistrito
    LEFT JOIN provincias pro ON d.idprovincia = pro.idprovincia
    WHERE c.estado = 'ACT'
      AND cro.estado IN ('Pendiente','Vencido')
    GROUP BY c.idcontrato
    ORDER BY prox_fechapago ASC;
END$$
DELIMITER ;


/*
SELECT COUNT(*) AS cuotas_pag
FROM cronogramas
WHERE idcontrato = 1 AND estado = 'pagado';
*/


-- 3) VENCIDOS: lista detallada de contratos con cuotas vencidas
-- VENCIDOS - DEUDA_VENCIDA - CUOTAS_PAGADAS

DROP PROCEDURE IF EXISTS sp_get_cuotas_vencidas;
DELIMITER $$
CREATE PROCEDURE sp_get_cuotas_vencidas()
BEGIN
    SELECT
        c.idcontrato,
        CONCAT(p.apellidos, ' ', p.nombres) AS cliente,
        p.telprimario AS telefono,
        CONCAT(m.marca, ' ', mo.modelo) AS vehiculo,
        CONCAT(COALESCE(d.distrito,'Sin distrito')) AS tienda,
        cot.numcuotas AS cuotas_totales,
        
        -- Primera cuota VENCIDA
        (SELECT (crn.abonocapital + crn.interes + IFNULL(crn.penalidad,0))
         FROM cronogramas crn
         WHERE crn.idcontrato = c.idcontrato
           AND crn.estado = 'Vencido'
         ORDER BY crn.fechapago
         LIMIT 1
        ) AS monto_primera_vencida,
        
        -- Deuda cuotas VENCIDAS
        IFNULL(SUM(cron.abonocapital + cron.interes + IFNULL(cron.penalidad,0)), 0) AS deuda_vencida,
        
        -- Cantidad numérica de cuotas vencidas
        SUM(cron.estado = 'Vencido') AS cuotas_vencidas,
        
        -- Cuotas pagadas del contrato
        (SELECT COUNT(*)
         FROM cronogramas crp
         WHERE crp.idcontrato = c.idcontrato
           AND crp.estado = 'Pagado'
        ) AS cuotas_pagadas,
        
        -- Estado legible: "X vencidas de Y" donde Y = total - pagadas
        CONCAT(
            SUM(cron.estado = 'Vencido'),
            ' vencidas de ',
            GREATEST(
                cot.numcuotas - (
                    SELECT COUNT(*)
                    FROM cronogramas crp2
                    WHERE crp2.idcontrato = c.idcontrato
                      AND crp2.estado = 'Pagado'
                ),
                0
            )
        ) AS estado_pagos,
        
        -- Fecha de la cuota vencida más antigua
        MIN(cron.fechapago) AS fecha_vencida_mas_antigua
        
    FROM contratos c
    JOIN cotizaciones cot ON c.idcotizacion = cot.idcotizacion
    JOIN clientes cli ON cot.idcliente = cli.idcliente
    JOIN personas p ON cli.idpersona = p.idpersona
    JOIN cronogramas cron ON cron.idcontrato = c.idcontrato
    JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    JOIN modelos mo ON v.idmodelo = mo.idmodelo
    JOIN marcas m ON mo.idmarca = m.idmarca
    LEFT JOIN locales loc ON c.idlocal = loc.idlocal
    LEFT JOIN distritos d ON loc.iddistrito = d.iddistrito
    LEFT JOIN provincias pro ON d.idprovincia = pro.idprovincia
    WHERE c.estado = 'ACT'
      AND cron.estado = 'Vencido'
    GROUP BY c.idcontrato
    ORDER BY deuda_vencida DESC;
    
END$$
DELIMITER ;


-- 4) PRÓXIMAS A VENCER - primera cuota pendiente en el rango - CUOTAS_PAGADAS 
-- Vence en 3-Dias

DROP PROCEDURE IF EXISTS sp_get_cuotas_proximas_vencer;
DELIMITER $$
CREATE PROCEDURE sp_get_cuotas_proximas_vencer()
BEGIN
    SELECT 
        cro.idcronograma,
        c.idcontrato,
        CONCAT(p.apellidos, ' ', p.nombres) AS cliente,
        p.telprimario AS telefono,
        CONCAT(m.marca, ' ', mo.modelo) AS vehiculo,
        CONCAT(
            COALESCE(d.distrito, 'Sin distrito'), ' / ',
            COALESCE(pro.provincia, 'Sin provincia')
        ) AS direccion_local,
        cot.numcuotas AS cuotas_totales,
        cro.numcuota,
        CONCAT(cro.numcuota, ' de ', cot.numcuotas) AS cuota_formato,
        (cro.abonocapital + cro.interes + IFNULL(cro.penalidad, 0)) AS montocuota,
        cro.fechapago,
        cro.estado,
        DATEDIFF(cro.fechapago, CURDATE()) AS dias_para_vencer,
        CASE 
            WHEN DATEDIFF(cro.fechapago, CURDATE()) = 3 THEN 'Vence en 3 días'
            WHEN DATEDIFF(cro.fechapago, CURDATE()) = 2 THEN 'Vence en 2 días'
            WHEN DATEDIFF(cro.fechapago, CURDATE()) = 1 THEN 'Vence mañana'
            WHEN DATEDIFF(cro.fechapago, CURDATE()) = 0 THEN 'Vence HOY'
            WHEN DATEDIFF(cro.fechapago, CURDATE()) < 0 THEN CONCAT('Vencido hace ', ABS(DATEDIFF(cro.fechapago, CURDATE())), ' días')
            ELSE CONCAT('Vence en ', DATEDIFF(cro.fechapago, CURDATE()), ' días')
        END AS estado_vencimiento,
        (
            SELECT SUM(cr2.abonocapital + cr2.interes + IFNULL(cr2.penalidad, 0))
            FROM cronogramas cr2 
            WHERE cr2.idcontrato = c.idcontrato 
              AND cr2.estado IN ('Pendiente', 'Vencido')
        ) AS deuda_total,
        (
            SELECT COUNT(*)
            FROM cronogramas cr2 
            WHERE cr2.idcontrato = c.idcontrato 
              AND cr2.estado = 'Vencido'
        ) AS cuotas_vencidas,
        
        -- Cuotas pagadas del contrato
        (SELECT COUNT(*)
         FROM cronogramas crp
         WHERE crp.idcontrato = c.idcontrato
           AND crp.estado = 'Pagado'
        ) AS cuotas_pagadas
        
    FROM cronogramas cro
    INNER JOIN contratos c ON cro.idcontrato = c.idcontrato
    INNER JOIN cotizaciones cot ON c.idcotizacion = cot.idcotizacion
    INNER JOIN clientes cli ON cot.idcliente = cli.idcliente
    INNER JOIN personas p ON cli.idpersona = p.idpersona
    INNER JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    INNER JOIN modelos mo ON v.idmodelo = mo.idmodelo
    INNER JOIN marcas m ON mo.idmarca = m.idmarca
    LEFT JOIN locales loc ON c.idlocal = loc.idlocal  
    LEFT JOIN distritos d ON loc.iddistrito = d.iddistrito
    LEFT JOIN provincias pro ON d.idprovincia = pro.idprovincia
    WHERE c.estado = 'ACT'
      AND cro.estado = 'Pendiente'
      AND DATEDIFF(cro.fechapago, CURDATE()) BETWEEN 0 AND 3
      
      -- Solo la primera cuota pendiente por contrato en este rango
      AND cro.idcronograma = (
          SELECT cr3.idcronograma
          FROM cronogramas cr3
          WHERE cr3.idcontrato = c.idcontrato
            AND cr3.estado = 'Pendiente'
            AND DATEDIFF(cr3.fechapago, CURDATE()) BETWEEN 0 AND 3
          ORDER BY cr3.fechapago ASC, cr3.numcuota ASC
          LIMIT 1
      )
    ORDER BY cro.fechapago ASC, cro.numcuota ASC;
    
END$$
DELIMITER ;


-- CALL sp_get_estadisticas_cobranza();
-- CALL sp_get_cuotas_vencidas();
-- CALL sp_get_cuotas_proximas_vencer();


/*
DROP PROCEDURE IF EXISTS sp_get_cuotas_vencidas;
DELIMITER $$
CREATE PROCEDURE sp_get_cuotas_vencidas()
BEGIN
    SELECT
        c.idcontrato,
        CONCAT(p.apellidos, ' ', p.nombres) AS cliente,
        p.telprimario AS telefono,
        CONCAT(m.marca, ' ', mo.modelo) AS vehiculo,
        CONCAT(
			COALESCE(d.distrito,'Sin distrito')) AS tienda,
        cot.numcuotas AS cuotas_totales,
        -- Primera cuota VENCIDA (monto)
        (SELECT (crn.abonocapital + crn.interes + IFNULL(crn.penalidad,0))
         FROM cronogramas crn
         WHERE crn.idcontrato = c.idcontrato
           AND crn.estado = 'Vencido'
         ORDER BY crn.fechapago
         LIMIT 1
        ) AS monto_primera_vencida,
        -- Deuda solo por cuotas VENCIDAS
        IFNULL(SUM(cron.abonocapital + cron.interes + IFNULL(cron.penalidad,0)), 0) AS deuda_vencida,
        -- Cantidad de cuotas vencidas y formato
        CONCAT(SUM(cron.estado = 'Vencido'), ' vencidas de ', cot.numcuotas) AS estado_pagos,
        -- Fecha de la cuota vencida más antigua
        MIN(cron.fechapago) AS fecha_vencida_mas_antigua,
        -- Cuotas pagadas del contrato (para referencia)
        (SELECT COUNT(*)
         FROM cronogramas crp
         WHERE crp.idcontrato = c.idcontrato
           AND crp.estado = 'Pagado'
        ) AS cuotas_pagadas
    FROM contratos c
    JOIN cotizaciones cot ON c.idcotizacion = cot.idcotizacion
    JOIN clientes cli ON cot.idcliente = cli.idcliente
    JOIN personas p ON cli.idpersona = p.idpersona
    JOIN cronogramas cron ON cron.idcontrato = c.idcontrato
    JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    JOIN modelos mo ON v.idmodelo = mo.idmodelo
    JOIN marcas m ON mo.idmarca = m.idmarca
    LEFT JOIN locales loc ON c.idlocal = loc.idlocal
    LEFT JOIN distritos d ON loc.iddistrito = d.iddistrito
    LEFT JOIN provincias pro ON d.idprovincia = pro.idprovincia
    WHERE c.estado = 'ACT'
      AND cron.estado = 'Vencido'
    GROUP BY c.idcontrato
    ORDER BY deuda_vencida DESC;
END$$
DELIMITER ;
*/


/*
* PRUEBAS PARA COBRANZA: S2
*/

/*
-- OBETIENE TODOS LOS CONTRATOS ALMENOS CON UNA CUOTA VENCIDA (EJEMPLO SALIA 4 Y ERAN 2)
DROP PROCEDURE IF EXISTS sp_get_estadisticas_cobranza;
DELIMITER $$
CREATE PROCEDURE sp_get_estadisticas_cobranza()
BEGIN
    -- 1) ESTADÍSTICAS
    SELECT
        -- TOTAL DE CONTRATOS
        (SELECT COUNT(DISTINCT c2.idcontrato)
         FROM contratos c2
         JOIN cronogramas cr2 ON cr2.idcontrato = c2.idcontrato
         WHERE c2.estado = 'ACT' AND cr2.estado IN ('Pendiente','Vencido')
        ) AS total_deudores,
        -- CUOTA POR VENCER
        (SELECT COUNT(DISTINCT c3.idcontrato)
         FROM contratos c3
         JOIN cronogramas cr3 ON cr3.idcontrato = c3.idcontrato
         WHERE c3.estado = 'ACT'
           AND cr3.estado IN ('Pendiente','Vencido')
           AND DATEDIFF(cr3.fechapago, CURDATE()) BETWEEN 0 AND 3
        ) AS por_vencer_3dias,
        -- CUOTAS VENCIDAS
        (SELECT COUNT(DISTINCT c4.idcontrato)
         FROM contratos c4
         JOIN cronogramas cr4 ON cr4.idcontrato = c4.idcontrato
         WHERE c4.estado = 'ACT'
           AND cr4.estado = 'Vencido'
        ) AS contratos_con_vencidos,
        -- TOTAL POR COBRAR
        (SELECT IFNULL(SUM(cr5.abonocapital + cr5.interes + IFNULL(cr5.penalidad,0)),0)
         FROM contratos c5
         JOIN cronogramas cr5 ON cr5.idcontrato = c5.idcontrato
         WHERE c5.estado = 'ACT'
           AND cr5.estado IN ('Pendiente','Vencido')
        ) AS total_por_cobrar;
        -- TOTAL DE CUOTAS PAGADAS
        (SELECT COUNT(*)
         FROM cronogramas crp
         JOIN contratos cpr ON crp.idcontrato = cpr.idcontrato
         WHERE cpr.estado = 'ACT'
           AND crp.estado = 'Pagado'
        ) AS total_cuotas_pagadas;
END$$
DELIMITER ;
*/

/*
-- PROCEDIMIENTO PARA OBTENER LOS PAGOS DEL CRONOGRAMA (CAMBIO EN LA FECHA DE VENCIDOS EN LAS CUOTAS Y SI PASA DE LA FECHA RECIEN APLICA PENALIDAD)
DROP PROCEDURE IF EXISTS sp_get_cronograma_pagos_cobranza;
DELIMITER $$
CREATE PROCEDURE sp_get_cronograma_pagos_cobranza(
    IN p_idcontrato INT
)
BEGIN
    SELECT 
        cr.idcronograma,
        cr.numcuota,
        DATE_FORMAT(cr.fechapago, '%d/%m/%Y') AS fecha_pago_formateada,
        cr.fechapago,
        cr.abonocapital,
        cr.interes,
        IFNULL(cr.penalidad, 0) AS penalidad,
        (cr.abonocapital + cr.interes + IFNULL(cr.penalidad, 0)) AS monto_total_cuota,
        cr.saldocapital,
        cr.estado,
        cr.aplicapenalidad,
        
		-- Estado de vencimiento (PRIORIZA LA FECHA REAL)
        CASE
            WHEN cr.estado = 'Pagado' THEN 'PAGADO'
            WHEN DATEDIFF(cr.fechapago, CURDATE()) < 0 THEN 'VENCIDO'
            WHEN DATEDIFF(cr.fechapago, CURDATE()) = 0 THEN 'VENCE HOY'
            WHEN DATEDIFF(cr.fechapago, CURDATE()) BETWEEN 1 AND 3 THEN 'POR VENCER'
            ELSE 'VIGENTE'
        END AS estado_vencimiento,
        
        -- Días de atraso o días para vencer
        CASE 
            WHEN cr.estado = 'Pagado' THEN 0
            WHEN DATEDIFF(CURDATE(), cr.fechapago) > 0 THEN DATEDIFF(CURDATE(), cr.fechapago)
            ELSE DATEDIFF(cr.fechapago, CURDATE())
        END AS dias_diferencia
        
    FROM cronogramas cr
    WHERE cr.idcontrato = p_idcontrato
    ORDER BY cr.numcuota ASC;
    
END$$
DELIMITER ;
*/

-- 6) PROCEDIMIENTO PARA MOSTRAR EL RESUMEN FINANCIERO (por ahora no)
/*
DROP PROCEDURE IF EXISTS sp_get_resumen_financiero_cobranza;
DELIMITER $$
CREATE PROCEDURE sp_get_resumen_financiero_cobranza(
    IN p_idcontrato INT
)
BEGIN
    SELECT 
        -- Total del contrato
        (SELECT precioventa 
         FROM cotizaciones cot 
         JOIN contratos cnt ON cnt.idcotizacion = cot.idcotizacion
         WHERE cnt.idcontrato = p_idcontrato
        ) AS precio_venta_total,
        
        (SELECT inicial 
         FROM cotizaciones cot 
         JOIN contratos cnt ON cnt.idcotizacion = cot.idcotizacion
         WHERE cnt.idcontrato = p_idcontrato
        ) AS inicial_pagado,
        
        -- Total pagado (cuotas)
        IFNULL(SUM(
            CASE 
                WHEN cr.estado = 'Pagado' 
                THEN cr.abonocapital + cr.interes + IFNULL(cr.penalidad, 0)
                ELSE 0
            END
        ), 0) AS total_pagado_cuotas,
        
        -- Total pendiente
        IFNULL(SUM(
            CASE 
                WHEN cr.estado IN ('Pendiente', 'Vencido')
                THEN cr.abonocapital + cr.interes + IFNULL(cr.penalidad, 0)
                ELSE 0
            END
        ), 0) AS total_pendiente,
        
        -- Total vencido
        IFNULL(SUM(
            CASE 
                WHEN cr.estado = 'Vencido'
                THEN cr.abonocapital + cr.interes + IFNULL(cr.penalidad, 0)
                ELSE 0
            END
        ), 0) AS total_vencido,
        
        -- Cuotas pagadas
        COUNT(CASE WHEN cr.estado = 'Pagado' THEN 1 END) AS cuotas_pagadas,
        
        -- Cuotas pendientes
        COUNT(CASE WHEN cr.estado IN ('Pendiente', 'Vencido') THEN 1 END) AS cuotas_pendientes,
        
        -- Total de cuotas
        COUNT(*) AS total_cuotas,
        
        -- Penalidades acumuladas
        IFNULL(SUM(IFNULL(cr.penalidad, 0)), 0) AS total_penalidades
        
    FROM cronogramas cr
    WHERE cr.idcontrato = p_idcontrato;
    
END$$
DELIMITER ;
*/


/*
-- MOSTRAR TODOS LOS CONTRATOS VENCIDOS - (EJEMPLO SALIAN 4 Y SOLO HAY 2 VENCIDOS LOS OTROS 2 AUN FALTAN)

DROP PROCEDURE IF EXISTS sp_get_cuotas_vencidas;
DELIMITER $$
CREATE PROCEDURE sp_get_cuotas_vencidas()
BEGIN
    SELECT
        c.idcontrato,
        CONCAT(p.apellidos, ' ', p.nombres) AS cliente,
        p.telprimario AS telefono,
        CONCAT(m.marca, ' ', mo.modelo) AS vehiculo,
        CONCAT(COALESCE(d.distrito,'Sin distrito')) AS tienda,
        cot.numcuotas AS cuotas_totales,
        
        -- Primera cuota VENCIDA
        (SELECT (crn.abonocapital + crn.interes + IFNULL(crn.penalidad,0))
         FROM cronogramas crn
         WHERE crn.idcontrato = c.idcontrato
           AND crn.estado = 'Vencido'
         ORDER BY crn.fechapago
         LIMIT 1
        ) AS monto_primera_vencida,
        
        -- Deuda cuotas VENCIDAS
        IFNULL(SUM(cron.abonocapital + cron.interes + IFNULL(cron.penalidad,0)), 0) AS deuda_vencida,
        
        -- Cantidad numérica de cuotas vencidas
        SUM(cron.estado = 'Vencido') AS cuotas_vencidas,
        
        -- Cuotas pagadas del contrato
        (SELECT COUNT(*)
         FROM cronogramas crp
         WHERE crp.idcontrato = c.idcontrato
           AND crp.estado = 'Pagado'
        ) AS cuotas_pagadas,
        
        -- Estado legible: "X vencidas de Y" donde Y = total - pagadas
        CONCAT(
            SUM(cron.estado = 'Vencido'),
            ' vencidas de ',
            GREATEST(
                cot.numcuotas - (
                    SELECT COUNT(*)
                    FROM cronogramas crp2
                    WHERE crp2.idcontrato = c.idcontrato
                      AND crp2.estado = 'Pagado'
                ),
                0
            )
        ) AS estado_pagos,
        
        -- Fecha de la cuota vencida más antigua
        MIN(cron.fechapago) AS fecha_vencida_mas_antigua
        
    FROM contratos c
    JOIN cotizaciones cot ON c.idcotizacion = cot.idcotizacion
    JOIN clientes cli ON cot.idcliente = cli.idcliente
    JOIN personas p ON cli.idpersona = p.idpersona
    JOIN cronogramas cron ON cron.idcontrato = c.idcontrato
    JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    JOIN modelos mo ON v.idmodelo = mo.idmodelo
    JOIN marcas m ON mo.idmarca = m.idmarca
    LEFT JOIN locales loc ON c.idlocal = loc.idlocal
    LEFT JOIN distritos d ON loc.iddistrito = d.iddistrito
    LEFT JOIN provincias pro ON d.idprovincia = pro.idprovincia
    WHERE c.estado = 'ACT'
      AND cron.estado = 'Vencido'
    GROUP BY c.idcontrato
    ORDER BY deuda_vencida DESC;
    
END$$
DELIMITER ;
*/


-- CALL sp_get_resumen_financiero_cobranza(1);