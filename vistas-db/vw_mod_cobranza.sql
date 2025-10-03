-- VISTA DE COBRANZA.INDEX

-- 1) ESTADÍSTICAS + RESUMEN POR CONTRATO 

DROP PROCEDURE IF EXISTS sp_get_estadisticas_cobranza;
DELIMITER $$
CREATE PROCEDURE sp_get_estadisticas_cobranza()
BEGIN
    SELECT
        -- TOTAL DE CONTRATOS
        (SELECT COUNT(DISTINCT c2.idcontrato)
         FROM contratos c2
         JOIN cronogramas cr2 ON cr2.idcontrato = c2.idcontrato
         WHERE c2.estado = 'ACT' AND cr2.estado IN ('Pendiente','Vencido')
        ) AS total_deudores,
        
        -- CUOTA POR VENCER (próximos 3 días)
        (SELECT COUNT(DISTINCT c3.idcontrato)
         FROM contratos c3
         JOIN cronogramas cr3 ON cr3.idcontrato = c3.idcontrato
         WHERE c3.estado = 'ACT'
           AND cr3.estado IN ('Pendiente','Vencido')
           AND DATEDIFF(cr3.fechapago, CURDATE()) BETWEEN 0 AND 3
           AND cr3.fechapago = (
               SELECT MIN(cr_min.fechapago)
               FROM cronogramas cr_min
               WHERE cr_min.idcontrato = c3.idcontrato
                 AND cr_min.estado IN ('Pendiente','Vencido')
           )
        ) AS por_vencer_3dias,
        
        -- CUOTAS VENCIDAS (filtrado por próxima cuota <= 3 días)
        (SELECT COUNT(DISTINCT c4.idcontrato)
         FROM contratos c4
         JOIN cronogramas cr4 ON cr4.idcontrato = c4.idcontrato
         WHERE c4.estado = 'ACT'
           AND cr4.estado = 'Vencido'
           AND DATEDIFF(cr4.fechapago, CURDATE()) <= 3
           AND cr4.fechapago = (
               SELECT MIN(cr_min2.fechapago)
               FROM cronogramas cr_min2
               WHERE cr_min2.idcontrato = c4.idcontrato
                 AND cr_min2.estado IN ('Pendiente','Vencido')
           )
        ) AS contratos_con_vencidos,
        
        -- TOTAL POR COBRAR
        (SELECT IFNULL(SUM(cr5.abonocapital + cr5.interes + IFNULL(cr5.penalidad,0)),0)
         FROM contratos c5
         JOIN cronogramas cr5 ON cr5.idcontrato = c5.idcontrato
         WHERE c5.estado = 'ACT'
           AND cr5.estado IN ('Pendiente','Vencido')
        ) AS total_por_cobrar;
    
END$$
DELIMITER ;

/*
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

-- 2) PROCEDIMIENTO PARA OBTENER LAS TARJETAS DE COBRANZA VENCIDAS

DROP PROCEDURE IF EXISTS sp_get_tarjetas_cobranza;
DELIMITER $$
CREATE PROCEDURE sp_get_tarjetas_cobranza()
BEGIN
    SELECT 
        c.idcontrato,
        
        -- Nombre del cliente
        CASE 
            WHEN cl.tipocliente = 'P' THEN CONCAT(p.apellidos, ' ', p.nombres)
            WHEN cl.tipocliente = 'E' THEN e.razonsocial
        END AS nombre_cliente,
        
        -- Teléfono
        CASE 
            WHEN cl.tipocliente = 'P' THEN p.telprimario
            WHEN cl.tipocliente = 'E' THEN e.telprimario
        END AS telefono,
        
        -- Local
        CONCAT(d.distrito, ' / ', pr.provincia) AS local,
        
        -- Tipo de vehículo
        CONCAT(ma.marca, ' / ', mo.modelo) AS tipo_vehiculo,
        
        -- Estado de vencimiento
        CASE
			-- Primero revisa los días
			WHEN DATEDIFF(cr.fechapago, CURDATE()) < 0 THEN 'VENCIDO'
			WHEN DATEDIFF(cr.fechapago, CURDATE()) = 0 THEN 'VENCE HOY'
			WHEN DATEDIFF(cr.fechapago, CURDATE()) = 1 THEN 'Vence en 1 día'
			WHEN DATEDIFF(cr.fechapago, CURDATE()) = 2 THEN 'Vence en 2 días'
			WHEN DATEDIFF(cr.fechapago, CURDATE()) = 3 THEN 'Vence en 3 días'
			ELSE 'POR VENCER'
		END AS estado_vencimiento,
        /*
        CASE
            WHEN cr.estado = 'Vencido' OR DATEDIFF(cr.fechapago, CURDATE()) < 0 THEN 'VENCIDO'
            WHEN DATEDIFF(cr.fechapago, CURDATE()) = 0 THEN 'VENCE HOY'
            WHEN DATEDIFF(cr.fechapago, CURDATE()) = 1 THEN 'Vence en 1 día'
            WHEN DATEDIFF(cr.fechapago, CURDATE()) = 2 THEN 'Vence en 2 días'
            WHEN DATEDIFF(cr.fechapago, CURDATE()) = 3 THEN 'Vence en 3 días'
            ELSE 'POR VENCER'
        END AS estado_vencimiento,*/
        
        -- Fecha de vencimiento
        DATE_FORMAT(cr.fechapago, '%d/%m/%Y') AS fecha_vencimiento
        
    FROM contratos c
    JOIN cotizaciones cot ON c.idcotizacion = cot.idcotizacion
    JOIN clientes cl ON cot.idcliente = cl.idcliente
    LEFT JOIN personas p ON cl.idpersona = p.idpersona
    LEFT JOIN empresas e ON cl.idempresa = e.idempresa
    JOIN locales l ON c.idlocal = l.idlocal
    JOIN distritos d ON l.iddistrito = d.iddistrito
    JOIN provincias pr ON d.idprovincia = pr.idprovincia
    JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    JOIN modelos mo ON v.idmodelo = mo.idmodelo
    JOIN marcas ma ON mo.idmarca = ma.idmarca
    JOIN cronogramas cr ON c.idcontrato = cr.idcontrato
    
    WHERE c.estado = 'ACT'
      AND cr.estado IN ('Pendiente', 'Vencido')
      AND DATEDIFF(cr.fechapago, CURDATE()) <= 3
      AND cr.fechapago = (
          SELECT MIN(cr2.fechapago) 
          FROM cronogramas cr2 
          WHERE cr2.idcontrato = c.idcontrato 
            AND cr2.estado IN ('Pendiente', 'Vencido')
      )
    ORDER BY 
        CASE 
            WHEN cr.estado = 'Vencido' OR DATEDIFF(cr.fechapago, CURDATE()) < 0 THEN 1
            WHEN DATEDIFF(cr.fechapago, CURDATE()) = 0 THEN 2
            ELSE 3
        END,
        cr.fechapago ASC;
        
END$$
DELIMITER ;


-- DETALLE:

-- 3) PROCEMIENTO PARA LA INFORMACION GENERAL DEL CONTRATO

DROP PROCEDURE IF EXISTS sp_get_info_cliente_cobranza;
DELIMITER $$
CREATE PROCEDURE sp_get_info_cliente_cobranza(
    IN p_idcontrato INT
)
BEGIN
    SELECT 
        cl.idcliente,
        cl.tipocliente,
        
        -- Datos persona natural
        CASE 
            WHEN cl.tipocliente = 'P' THEN CONCAT(p.apellidos, ' ', p.nombres)
            ELSE NULL
        END AS nombre_completo,
        
        CASE 
            WHEN cl.tipocliente = 'P' THEN p.tipodoc
            ELSE NULL
        END AS tipo_documento,
        
        CASE 
            WHEN cl.tipocliente = 'P' THEN p.nrodoc
            ELSE NULL
        END AS numero_documento,
        
        CASE 
            WHEN cl.tipocliente = 'P' THEN p.email
            ELSE NULL
        END AS email_persona,
        
        CASE 
            WHEN cl.tipocliente = 'P' THEN p.direccion
            ELSE NULL
        END AS direccion_persona,
        
        CASE 
            WHEN cl.tipocliente = 'P' THEN p.telprimario
            ELSE NULL
        END AS telefono_primario_persona,
        
        CASE 
            WHEN cl.tipocliente = 'P' THEN p.telalternativo
            ELSE NULL
        END AS telefono_alternativo_persona,
        
        -- Datos empresa
        CASE 
            WHEN cl.tipocliente = 'E' THEN e.razonsocial
            ELSE NULL
        END AS razon_social,
        
        CASE 
            WHEN cl.tipocliente = 'E' THEN e.ruc
            ELSE NULL
        END AS ruc,
        
        CASE 
            WHEN cl.tipocliente = 'E' THEN e.representante
            ELSE NULL
        END AS representante_legal,
        
        CASE 
            WHEN cl.tipocliente = 'E' THEN e.email
            ELSE NULL
        END AS email_empresa,
        
        CASE 
            WHEN cl.tipocliente = 'E' THEN e.direccion
            ELSE NULL
        END AS direccion_empresa,
        
        CASE 
            WHEN cl.tipocliente = 'E' THEN e.telprimario
            ELSE NULL
        END AS telefono_primario_empresa,
        
        CASE 
            WHEN cl.tipocliente = 'E' THEN e.telsecundario
            ELSE NULL
        END AS telefono_secundario_empresa,
        
        -- Distrito
        CASE 
            WHEN cl.tipocliente = 'P' THEN dp.distrito
            WHEN cl.tipocliente = 'E' THEN de.distrito
        END AS distrito,
        
        CASE 
            WHEN cl.tipocliente = 'P' THEN pp.provincia
            WHEN cl.tipocliente = 'E' THEN pe.provincia
        END AS provincia
        
    FROM contratos cnt
    JOIN cotizaciones cot ON cnt.idcotizacion = cot.idcotizacion
    JOIN clientes cl ON cot.idcliente = cl.idcliente
    LEFT JOIN personas p ON cl.idpersona = p.idpersona
    LEFT JOIN empresas e ON cl.idempresa = e.idempresa
    LEFT JOIN distritos dp ON p.iddistrito = dp.iddistrito
    LEFT JOIN provincias pp ON dp.idprovincia = pp.idprovincia
    LEFT JOIN distritos de ON e.iddistrito = de.iddistrito
    LEFT JOIN provincias pe ON de.idprovincia = pe.idprovincia
    
    WHERE cnt.idcontrato = p_idcontrato;
    
END$$
DELIMITER ;

-- 4) PROCEDIMIENTO PARA MOSTRAR EL DETALLE DEL CONTRATO

DROP PROCEDURE IF EXISTS sp_get_detalle_contrato_cobranza;
DELIMITER $$
CREATE PROCEDURE sp_get_detalle_contrato_cobranza(
    IN p_idcontrato INT
)
BEGIN
    SELECT 
        cnt.idcontrato,
        cnt.fechainicio,
        cnt.diapago,
        cnt.escredito,
        cnt.penalidadbase,
        cnt.estado AS estado_contrato,
        cnt.observaciones,
        
        -- Información del vehículo
        CONCAT(ma.marca, ' ', mo.modelo, ' ', mo.anio) AS vehiculo_descripcion,
        tv.tipovehiculo,
        v.version,
        v.condicion,
        v.color,
        v.placa,
        v.chasis,
        cmb.combustible,
        
        -- Información financiera
        cot.moneda,
        cot.precioventa,
        cot.inicial,
        cot.numcuotas,
        cot.valorcuota,
        cot.gastosadministrativos,
        
        -- Local
        l.tienda AS local_venta,
        dl.distrito AS distrito_local,
        pl.provincia AS provincia_local,
        
        (SELECT IFNULL(SUM(cr.abonocapital + cr.interes + IFNULL(cr.penalidad, 0)), 0)
         FROM cronogramas cr
         WHERE cr.idcontrato = cnt.idcontrato
           AND cr.estado IN ('Pendiente', 'Vencido')
        ) AS deuda_total
        
    FROM contratos cnt
    JOIN cotizaciones cot ON cnt.idcotizacion = cot.idcotizacion
    JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    JOIN modelos mo ON v.idmodelo = mo.idmodelo
    JOIN marcas ma ON mo.idmarca = ma.idmarca
    JOIN tipovehiculos tv ON mo.idtipovehiculo = tv.idtipovehiculo
    JOIN combustibles cmb ON v.idcombustible = cmb.idcombustible
    JOIN locales l ON cnt.idlocal = l.idlocal
    JOIN distritos dl ON l.iddistrito = dl.iddistrito
    JOIN provincias pl ON dl.idprovincia = pl.idprovincia
    
    WHERE cnt.idcontrato = p_idcontrato;
    
END$$
DELIMITER ;

-- 5) PROCEDIMIENTO PARA MOSTRAR CRONOGRAMA DE PAGOS

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
        
        -- Estado de vencimiento
        CASE
            WHEN cr.estado = 'Pagado' THEN 'PAGADO'
            WHEN cr.estado = 'Vencido' OR DATEDIFF(cr.fechapago, CURDATE()) < 0 THEN 'VENCIDO'
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

-- 6) PROCEDIMIENTO PARA MOSTRAR EL RESUMEN FINANCIERO
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

-- 7) PROCEDIMIENTO PARA MOSTRAR HISTORIAL DE PAGOS

DROP PROCEDURE IF EXISTS sp_get_historial_pagos_cobranza;
DELIMITER $$
CREATE PROCEDURE sp_get_historial_pagos_cobranza(
    IN p_idcontrato INT,
    IN p_limite INT
)
BEGIN
    -- Obtener los últimos pagos realizados
    SELECT 
        cr.idcronograma,
        cr.numcuota,
        DATE_FORMAT(cr.fechapago, '%d/%m/%Y') AS fecha_pago_programada,
        (cr.abonocapital + cr.interes + IFNULL(cr.penalidad, 0)) AS monto_pagado,
        cr.abonocapital,
        cr.interes,
        IFNULL(cr.penalidad, 0) AS penalidad,
        cr.saldocapital AS saldo_capital_restante
        
    FROM cronogramas cr
    WHERE cr.idcontrato = p_idcontrato
      AND cr.estado = 'Pagado'
    ORDER BY cr.numcuota DESC
    LIMIT p_limite;
    
END$$
DELIMITER ;

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

-- CALL sp_get_estadisticas_cobranza();
-- CALL sp_get_tarjetas_cobranza();
-- CALL sp_get_info_cliente_cobranza(8);
-- CALL sp_get_detalle_contrato_cobranza(8);
-- CALL sp_get_cronograma_pagos_cobranza(8);
-- CALL sp_get_historial_pagos_cobranza(8, 5);
-- CALL sp_get_cuotas_vencidas();

-- CALL sp_get_resumen_financiero_cobranza(1);


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
        
        -- Primera cuota VENCIDA (la próxima a pagar)
        (cron.abonocapital + cron.interes + IFNULL(cron.penalidad,0)) AS monto_primera_vencida,
        
        -- Deuda TOTAL de cuotas vencidas y pendientes
        (SELECT IFNULL(SUM(cr_total.abonocapital + cr_total.interes + IFNULL(cr_total.penalidad,0)), 0)
         FROM cronogramas cr_total
         WHERE cr_total.idcontrato = c.idcontrato
           AND cr_total.estado = 'Vencido'
        ) AS deuda_vencida,
        
        -- Cantidad de cuotas vencidas
        (SELECT COUNT(*)
         FROM cronogramas cr_count
         WHERE cr_count.idcontrato = c.idcontrato
           AND cr_count.estado = 'Vencido'
        ) AS cuotas_vencidas,
        
        -- Cuotas pagadas del contrato
        (SELECT COUNT(*)
         FROM cronogramas crp
         WHERE crp.idcontrato = c.idcontrato
           AND crp.estado = 'Pagado'
        ) AS cuotas_pagadas,
        
        -- Estado legible: "X vencidas de Y"
        CONCAT(
            (SELECT COUNT(*) FROM cronogramas cr_v WHERE cr_v.idcontrato = c.idcontrato AND cr_v.estado = 'Vencido'),
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
        cron.fechapago AS fecha_vencida_mas_antigua
        
    FROM contratos c
    JOIN cotizaciones cot ON c.idcotizacion = cot.idcotizacion
    JOIN clientes cli ON cot.idcliente = cli.idcliente
    JOIN personas p ON cli.idpersona = p.idpersona
    JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    JOIN modelos mo ON v.idmodelo = mo.idmodelo
    JOIN marcas m ON mo.idmarca = m.idmarca
    LEFT JOIN locales loc ON c.idlocal = loc.idlocal
    LEFT JOIN distritos d ON loc.iddistrito = d.iddistrito
    LEFT JOIN provincias pro ON d.idprovincia = pro.idprovincia
    -- JOIN con la próxima cuota a pagar
    JOIN cronogramas cron ON cron.idcontrato = c.idcontrato
    
    WHERE c.estado = 'ACT'
      AND cron.estado = 'Vencido'
      AND DATEDIFF(cron.fechapago, CURDATE()) <= 3  -- ← Filtro de 3 días
      AND cron.fechapago = (  -- ← Solo la próxima cuota vencida
          SELECT MIN(cr_min.fechapago)
          FROM cronogramas cr_min
          WHERE cr_min.idcontrato = c.idcontrato
            AND cr_min.estado IN ('Pendiente', 'Vencido')
      )
    ORDER BY deuda_vencida DESC;
    
END$$
DELIMITER ;