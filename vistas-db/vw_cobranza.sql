
-- estadísticas + resumen por contrato

DROP PROCEDURE IF EXISTS sp_get_estadisticas_cobranza;
DELIMITER $$
CREATE PROCEDURE sp_get_estadisticas_cobranza()
BEGIN
    -- 1) ESTADISTICAS
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
        
        -- CUOTA VENCIDA
        (SELECT COUNT(DISTINCT c4.idcontrato)
         FROM contratos c4
         JOIN cronogramas cr4 ON cr4.idcontrato = c4.idcontrato
         WHERE c4.estado = 'ACT'
           AND cr4.estado = 'Vencido'
        ) AS vencidos,
        
        -- TOTAL DE CUOTAS
        (SELECT IFNULL(SUM(cr5.abonocapital + cr5.interes + IFNULL(cr5.penalidad,0)),0)
         FROM contratos c5
         JOIN cronogramas cr5 ON cr5.idcontrato = c5.idcontrato
         WHERE c5.estado = 'ACT'
           AND cr5.estado IN ('Pendiente','Vencido')
        ) AS total_por_cobrar;

    -- 2) RESUMEN POR CONTRATO
    SELECT
        c.idcontrato,
        CONCAT(p.apellidos, ' ', p.nombres) AS cliente,
        p.telprimario AS telefono,
        CONCAT(m.marca, ' ', mo.modelo) AS vehiculo,
        CONCAT(COALESCE(d.distrito,'Sin distrito'), ' / ', COALESCE(pro.provincia,'Sin provincia')) AS tienda,
        cot.numcuotas AS cuotas_totales,
        
        /* DEDUDA TOTAL: suma de cuotas Pendiente/Vencido para el contrato */
        IFNULL(SUM(CASE WHEN cro.estado IN ('Pendiente','Vencido') THEN (cro.abonocapital + cro.interes + IFNULL(cro.penalidad,0)) ELSE 0 END), 0) AS deuda_total,
        
        /* NUMERO DE CUOTAS VENCIDAS */
        SUM(cro.estado = 'Vencido') AS cuotas_vencidas,
        
        /* monto de la próxima cuota pendiente/vencida */
        (SELECT (crn.abonocapital + crn.interes + IFNULL(crn.penalidad,0))
         FROM cronogramas crn
         WHERE crn.idcontrato = c.idcontrato
           AND crn.estado IN ('Pendiente','Vencido')
         ORDER BY crn.fechapago
         LIMIT 1
        ) AS monto_cuota,
        
        /* fecha próxima (mínima) de las cuotas pendientes/vencidas */
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


-- NOTIFICAR: lista de los que estan a 3 dias de vencer
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
        cro.abonocapital,
        cro.interes,
        IFNULL(cro.penalidad, 0) AS penalidad,
        (cro.abonocapital + cro.interes + IFNULL(cro.penalidad, 0)) AS montocuota,
        cro.fechapago,
        cro.estado,
        DATEDIFF(cro.fechapago, CURDATE()) AS dias_para_vencer,
        CASE 
            WHEN DATEDIFF(cro.fechapago, CURDATE()) = 3 THEN 'Vence en 3 días'
            WHEN DATEDIFF(cro.fechapago, CURDATE()) = 2 THEN 'Vence en 2 días'
            WHEN DATEDIFF(cro.fechapago, CURDATE()) = 1 THEN 'Vence mañana'
            WHEN DATEDIFF(cro.fechapago, CURDATE()) = 0 THEN 'Vence HOY'
            WHEN DATEDIFF(cro.fechapago, CURDATE()) = -1 THEN 'Vencido hace 1 día'
            WHEN DATEDIFF(cro.fechapago, CURDATE()) = -2 THEN 'Vencido hace 2 días'
            WHEN DATEDIFF(cro.fechapago, CURDATE()) = -3 THEN 'Vencido hace 3 días'
            WHEN DATEDIFF(cro.fechapago, CURDATE()) < -3 THEN CONCAT('Vencido hace ', ABS(DATEDIFF(cro.fechapago, CURDATE())), ' días')
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
        ) AS cuotas_vencidas
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
      AND cro.estado IN ('Pendiente', 'Vencido')
      AND DATEDIFF(cro.fechapago, CURDATE()) = 3;
END$$

DELIMITER ;

-- VENCIDOS: lista detallada de contratos con cuotas vencidas
DROP PROCEDURE IF EXISTS sp_get_cuotas_vencidas;
DELIMITER $$
CREATE PROCEDURE sp_get_cuotas_vencidas()
BEGIN
    SELECT
        c.idcontrato,
        CONCAT(p.apellidos, ' ', p.nombres) AS cliente,
        p.telprimario AS telefono,
        CONCAT(m.marca, ' ', mo.modelo) AS vehiculo,
        CONCAT(COALESCE(d.distrito,'Sin distrito'), ' / ', COALESCE(pro.provincia,'Sin provincia')) AS tienda,
        cot.numcuotas AS cuotas_totales,
        (SELECT (crn.abonocapital + crn.interes + IFNULL(crn.penalidad,0))
         FROM cronogramas crn
         WHERE crn.idcontrato = c.idcontrato
           AND crn.estado IN ('Pendiente','Vencido')
         ORDER BY crn.fechapago
         LIMIT 1
        ) AS monto_cuota,
        IFNULL(SUM(CASE WHEN cron.estado IN ('Pendiente','Vencido') THEN (cron.abonocapital + cron.interes + IFNULL(cron.penalidad,0)) ELSE 0 END), 0) AS deuda_total,
        SUM(cron.estado = 'Vencido') AS cuotas_vencidas
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
    ORDER BY deuda_total DESC;
END$$
DELIMITER ;

