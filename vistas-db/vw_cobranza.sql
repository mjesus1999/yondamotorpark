-- VISTA DE COBRANZA 
-- ========================================

-- 1) ESTADÍSTICAS 

DROP PROCEDURE IF EXISTS sp_get_estadisticas_cobranza;
DELIMITER $$
CREATE PROCEDURE sp_get_estadisticas_cobranza()
BEGIN
    WITH estadisticas AS (
        SELECT 
            c.idcontrato,
            cr.estado,
            cr.fechapago,
            (cr.abonocapital + cr.interes + IFNULL(cr.penalidad, 0)) AS monto_cuota,
            DATEDIFF(cr.fechapago, CURDATE()) AS dias_diferencia
        FROM contratos c
        JOIN cronogramas cr ON cr.idcontrato = c.idcontrato
        WHERE c.estado = 'ACT'
    )
    SELECT
        COUNT(DISTINCT CASE 
            WHEN estado IN ('Pendiente','Vencido') 
            THEN idcontrato 
        END) AS total_deudores,
        
        COUNT(DISTINCT CASE 
            WHEN estado != 'Pagado' 
            AND dias_diferencia BETWEEN 0 AND 3 
            THEN idcontrato 
        END) AS por_vencer_3dias,
        
        COUNT(DISTINCT CASE 
            WHEN estado IN ('Pendiente', 'Vencido')
            AND fechapago < CURDATE()
            THEN idcontrato 
        END) AS contratos_con_vencidos,
        
        IFNULL(SUM(CASE 
            WHEN estado IN ('Pendiente','Vencido')
            AND fechapago < CURDATE()
            THEN monto_cuota 
            ELSE 0 
        END), 0) AS total_por_cobrar
        /*IFNULL(SUM(CASE 
            WHEN estado IN ('Pendiente','Vencido') 
            THEN monto_cuota 
            ELSE 0 
        END), 0) AS total_por_cobrar*/
    FROM estadisticas;
END$$
DELIMITER ;


-- 2) TARJETAS DE COBRANZA 

DROP PROCEDURE IF EXISTS sp_get_tarjetas_cobranza;
DELIMITER $$
CREATE PROCEDURE sp_get_tarjetas_cobranza()
BEGIN
    SELECT 
        c.idcontrato,
        CASE 
            WHEN cl.tipocliente = 'P' THEN CONCAT(p.apellidos, ' ', p.nombres)
            WHEN cl.tipocliente = 'E' THEN e.razonsocial
        END AS nombre_cliente,
        
        CASE 
            WHEN cl.tipocliente = 'P' THEN p.telprimario
            WHEN cl.tipocliente = 'E' THEN e.telprimario
        END AS telefono,
        
        CONCAT(d.distrito, ' / ', pr.provincia) AS local,
        CONCAT(ma.marca, ' / ', mo.modelo) AS tipo_vehiculo,
        
        CASE
            WHEN DATEDIFF(cr.fechapago, CURDATE()) < 0 THEN 'VENCIDO'
            WHEN DATEDIFF(cr.fechapago, CURDATE()) = 0 THEN 'VENCE HOY'
            WHEN DATEDIFF(cr.fechapago, CURDATE()) = 1 THEN 'Vence en 1 día'
            WHEN DATEDIFF(cr.fechapago, CURDATE()) = 2 THEN 'Vence en 2 días'
            WHEN DATEDIFF(cr.fechapago, CURDATE()) = 3 THEN 'Vence en 3 días'
            ELSE 'POR VENCER'
        END AS estado_vencimiento,
        
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
    JOIN (
        -- Subconsulta ejecutada UNA VEZ por contrato
        SELECT 
            idcontrato,
            MIN(fechapago) AS min_fecha
        FROM cronogramas
        WHERE estado IN ('Pendiente', 'Vencido')
        GROUP BY idcontrato
    ) primera_cuota ON primera_cuota.idcontrato = c.idcontrato
    JOIN cronogramas cr 
        ON cr.idcontrato = c.idcontrato 
        AND cr.fechapago = primera_cuota.min_fecha
        AND cr.estado IN ('Pendiente', 'Vencido')
    
    WHERE c.estado = 'ACT'
      AND DATEDIFF(cr.fechapago, CURDATE()) <= 3
    
    ORDER BY 
        CASE 
            WHEN DATEDIFF(cr.fechapago, CURDATE()) < 0 THEN 1
            WHEN DATEDIFF(cr.fechapago, CURDATE()) = 0 THEN 2
            ELSE 3
        END,
        cr.fechapago ASC;
END$$
DELIMITER ;


-- 3) INFO CLIENTE

DROP PROCEDURE IF EXISTS sp_get_info_cliente_cobranza;
DELIMITER $$
CREATE PROCEDURE sp_get_info_cliente_cobranza(IN p_idcontrato INT)
BEGIN
    SELECT 
        cl.idcliente,
        cl.tipocliente,
        
        CASE WHEN cl.tipocliente = 'P' THEN CONCAT(p.apellidos, ' ', p.nombres) 
        ELSE NULL END AS nombre_completo,
        CASE WHEN cl.tipocliente = 'P' THEN p.tipodoc 
        ELSE NULL END AS tipo_documento,
        CASE WHEN cl.tipocliente = 'P' THEN p.nrodoc 
        ELSE NULL END AS numero_documento,
        CASE WHEN cl.tipocliente = 'P' THEN p.email 
        ELSE NULL END AS email_persona,
        CASE WHEN cl.tipocliente = 'P' THEN p.direccion 
        ELSE NULL END AS direccion_persona,
        CASE WHEN cl.tipocliente = 'P' THEN p.telprimario 
        ELSE NULL END AS telefono_primario_persona,
        CASE WHEN cl.tipocliente = 'P' THEN p.telalternativo 
        ELSE NULL END AS telefono_alternativo_persona,
        
        CASE WHEN cl.tipocliente = 'E' THEN e.razonsocial 
        ELSE NULL END AS razon_social,
        CASE WHEN cl.tipocliente = 'E' THEN e.ruc 
        ELSE NULL END AS ruc,
        CASE WHEN cl.tipocliente = 'E' THEN e.representante 
        ELSE NULL END AS representante_legal,
        CASE WHEN cl.tipocliente = 'E' THEN e.email 
        ELSE NULL END AS email_empresa,
        CASE WHEN cl.tipocliente = 'E' THEN e.direccion 
        ELSE NULL END AS direccion_empresa,
        CASE WHEN cl.tipocliente = 'E' THEN e.telprimario 
        ELSE NULL END AS telefono_primario_empresa,
        CASE WHEN cl.tipocliente = 'E' THEN e.telsecundario 
        ELSE NULL END AS telefono_secundario_empresa,
        
        CASE WHEN cl.tipocliente = 'P' THEN dp.distrito WHEN cl.tipocliente = 'E' 
        THEN de.distrito END AS distrito,
        CASE WHEN cl.tipocliente = 'P' THEN pp.provincia WHEN cl.tipocliente = 'E' 
        THEN pe.provincia END AS provincia
        
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


-- 4) DETALLE CONTRATO 

DROP PROCEDURE IF EXISTS sp_get_detalle_contrato_cobranza;
DELIMITER $$
CREATE PROCEDURE sp_get_detalle_contrato_cobranza(IN p_idcontrato INT)
BEGIN
    SELECT 
        cnt.idcontrato,
        cnt.fechainicio,
        cnt.diapago,
        cnt.escredito,
        cnt.penalidadbase,
        cnt.estado AS estado_contrato,
        cnt.observaciones,
        
        CONCAT(ma.marca, ' / ', mo.modelo, ' / ', mo.anio) AS vehiculo_descripcion,
        tv.tipovehiculo,
        v.version,
        v.condicion,
        v.color,
        v.placa,
        v.chasis,
        cmb.combustible,
        
        cot.moneda,
        cot.precioventa,
        cot.inicial,
        cot.numcuotas,
        cot.valorcuota,
        cot.gastosadministrativos,
        
        l.tienda AS local_venta,
        dl.distrito AS distrito_local,
        pl.provincia AS provincia_local,
        
        IFNULL(SUM(CASE 
            WHEN cr.estado IN ('Pendiente', 'Vencido')
            THEN (cr.abonocapital + cr.interes + IFNULL(cr.penalidad, 0))
            ELSE 0
        END), 0) AS deuda_total
        
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
    LEFT JOIN cronogramas cr ON cr.idcontrato = cnt.idcontrato
    
    WHERE cnt.idcontrato = p_idcontrato;
    
    /*
    GROUP BY 
        cnt.idcontrato, cnt.fechainicio, cnt.diapago, cnt.escredito,
        cnt.penalidadbase, cnt.estado, cnt.observaciones,
        ma.marca, mo.modelo, mo.anio, tv.tipovehiculo,
        v.version, v.condicion, v.color, v.placa, v.chasis,
        cmb.combustible, cot.moneda, cot.precioventa, cot.inicial,
        cot.numcuotas, cot.valorcuota, cot.gastosadministrativos,
        l.tienda, dl.distrito, pl.provincia;*/
END$$
DELIMITER ;


-- 5) CRONOGRAMA

DROP PROCEDURE IF EXISTS sp_get_cronograma_pagos_cobranza;
DELIMITER $$
CREATE PROCEDURE sp_get_cronograma_pagos_cobranza(IN p_idcontrato INT)
BEGIN
    SELECT 
        cr.idcronograma,
        cr.numcuota,
        DATE_FORMAT(cr.fechapago, '%d/%m/%Y') AS fecha_pago_formateada,
        cr.fechapago,
        cr.abonocapital,
        cr.interes,
        
        CASE
            WHEN cr.estado = 'Pagado' AND cr.aplicapenalidad = 'S' THEN IFNULL(cr.penalidad, 0)
            WHEN cr.estado = 'Vencido' AND DATEDIFF(CURDATE(), cr.fechapago) > 0 THEN IFNULL(cr.penalidad, 0)
            ELSE 0
        END AS penalidad,
        
        (cr.abonocapital + cr.interes + 
            CASE
                WHEN cr.estado = 'Pagado' AND cr.aplicapenalidad = 'S' THEN IFNULL(cr.penalidad, 0)
                WHEN cr.estado = 'Vencido' AND DATEDIFF(CURDATE(), cr.fechapago) > 0 THEN IFNULL(cr.penalidad, 0)
                ELSE 0
            END
        ) AS monto_total_cuota,
        
        cr.saldocapital,
        cr.estado,
        cr.aplicapenalidad,
        
        CASE
            WHEN cr.estado = 'Pagado' THEN 'PAGADO'
            WHEN DATEDIFF(cr.fechapago, CURDATE()) < 0 THEN 'VENCIDO'
            WHEN DATEDIFF(cr.fechapago, CURDATE()) = 0 THEN 'VENCE HOY'
            WHEN DATEDIFF(cr.fechapago, CURDATE()) BETWEEN 1 AND 3 THEN 'POR VENCER'
            ELSE 'VIGENTE'
        END AS estado_vencimiento,
        
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


-- 6) HISTORIAL 

DROP PROCEDURE IF EXISTS sp_get_historial_pagos_cobranza;
DELIMITER $$
CREATE PROCEDURE sp_get_historial_pagos_cobranza(IN p_idcontrato INT, IN p_limite INT)
BEGIN
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


-- 7) CUOTAS VENCIDAS

DROP PROCEDURE IF EXISTS sp_get_cuotas_vencidas;
DELIMITER $$
CREATE PROCEDURE sp_get_cuotas_vencidas()
BEGIN
    SELECT
        c.idcontrato,
        CONCAT(p.apellidos, ' ', p.nombres) AS cliente,
        p.telprimario AS telefono,
        CONCAT(m.marca, ' ', mo.modelo) AS vehiculo,
        COALESCE(d.distrito, 'Sin distrito') AS tienda,
        cot.numcuotas AS cuotas_totales,
        
        -- Monto primera vencida 
        (cron.abonocapital + cron.interes + IFNULL(cron.penalidad, 0)) AS monto_primera_vencida,
        
        -- SUM/COUNT 
        IFNULL(SUM(CASE 
            WHEN cron_all.estado IN ('Pendiente', 'Vencido')
            AND cron_all.fechapago < CURDATE()
            THEN (cron_all.abonocapital + cron_all.interes + IFNULL(cron_all.penalidad, 0))
            ELSE 0
        END), 0) AS deuda_vencida,
        
        SUM(CASE 
            WHEN cron_all.estado IN ('Pendiente', 'Vencido')
            AND cron_all.fechapago < CURDATE()
            THEN 1 
            ELSE 0 
        END) AS cuotas_vencidas,
        
        SUM(CASE 
            WHEN cron_all.estado = 'Pagado' 
            THEN 1 
            ELSE 0 
        END) AS cuotas_pagadas,
        
        CONCAT(
            SUM(CASE 
                WHEN cron_all.estado IN ('Pendiente', 'Vencido')
                AND cron_all.fechapago < CURDATE()
                THEN 1 
                ELSE 0 
            END),
            ' vencidas de ',
            GREATEST(cot.numcuotas - SUM(CASE 
                WHEN cron_all.estado = 'Pagado' 
                THEN 1 
                ELSE 0 
            END), 0)
        ) AS estado_pagos,
        
        cron.fechapago AS fecha_vencida_mas_antigua,
        DATEDIFF(CURDATE(), cron.fechapago) AS dias_atraso
        
    FROM contratos c
    JOIN cotizaciones cot ON c.idcotizacion = cot.idcotizacion
    JOIN clientes cli ON cot.idcliente = cli.idcliente
    JOIN personas p ON cli.idpersona = p.idpersona
    JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    JOIN modelos mo ON v.idmodelo = mo.idmodelo
    JOIN marcas m ON mo.idmarca = m.idmarca
    LEFT JOIN locales loc ON c.idlocal = loc.idlocal
    LEFT JOIN distritos d ON loc.iddistrito = d.iddistrito
    
    -- JOIN para TODAS las cuotas 
    JOIN cronogramas cron_all ON cron_all.idcontrato = c.idcontrato
    
    -- JOIN para la PRIMERA cuota vencida
    JOIN (
        SELECT 
            idcontrato,
            MIN(fechapago) AS min_fecha
        FROM cronogramas
        WHERE estado IN ('Pendiente', 'Vencido')
        AND fechapago < CURDATE()
        GROUP BY idcontrato
    ) primera_vencida ON primera_vencida.idcontrato = c.idcontrato
    
    JOIN cronogramas cron 
        ON cron.idcontrato = c.idcontrato 
        AND cron.fechapago = primera_vencida.min_fecha
        AND cron.estado IN ('Pendiente', 'Vencido')
    
    WHERE c.estado = 'ACT'
    
    GROUP BY 
        c.idcontrato, cot.numcuotas, p.apellidos, p.nombres, 
        p.telprimario, m.marca, mo.modelo, d.distrito,
        cron.fechapago, cron.abonocapital, cron.interes, cron.penalidad
    
    HAVING cuotas_vencidas > 0
    ORDER BY dias_atraso DESC, deuda_vencida DESC;
END$$
DELIMITER ;


-- 8) PRÓXIMAS A VENCER 

DROP PROCEDURE IF EXISTS sp_get_cuotas_proximas_vencer;
DELIMITER $$
CREATE PROCEDURE sp_get_cuotas_proximas_vencer()
BEGIN
    SELECT
        c.idcontrato,
        
        CASE 
            WHEN cli.tipocliente = 'P' THEN CONCAT(p.apellidos, ' ', p.nombres)
            WHEN cli.tipocliente = 'E' THEN e.razonsocial
        END AS cliente,
        
        CASE 
            WHEN cli.tipocliente = 'P' THEN p.telprimario
            WHEN cli.tipocliente = 'E' THEN e.telprimario
        END AS telefono,
        
        CONCAT(d.distrito, ' / ', pro.provincia) AS local,
        CONCAT(m.marca, ' / ', mo.modelo) AS vehiculo,
        
        CASE
            WHEN DATEDIFF(cron.fechapago, CURDATE()) = 0 THEN 'VENCE HOY'
            WHEN DATEDIFF(cron.fechapago, CURDATE()) = 1 THEN 'Vence mañana'
            WHEN DATEDIFF(cron.fechapago, CURDATE()) = 2 THEN 'Vence en 2 días'
            WHEN DATEDIFF(cron.fechapago, CURDATE()) = 3 THEN 'Vence en 3 días'
            ELSE 'Próximo a vencer'
        END AS estado_vencimiento,
        
        DATE_FORMAT(cron.fechapago, '%d/%m/%Y') AS fecha_vencimiento,
        cron.numcuota,
        (cron.abonocapital + cron.interes) AS monto_cuota,
        cot.numcuotas AS cuotas_totales,
        
        SUM(CASE 
            WHEN cron_all.estado = 'Pagado' 
            THEN 1 
            ELSE 0 
        END) AS cuotas_pagadas,
        
        DATEDIFF(cron.fechapago, CURDATE()) AS dias_para_vencer
        
    FROM contratos c
    JOIN cotizaciones cot ON c.idcotizacion = cot.idcotizacion
    JOIN clientes cli ON cot.idcliente = cli.idcliente
    LEFT JOIN personas p ON cli.idpersona = p.idpersona
    LEFT JOIN empresas e ON cli.idempresa = e.idempresa
    JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    JOIN modelos mo ON v.idmodelo = mo.idmodelo
    JOIN marcas m ON mo.idmarca = m.idmarca
    JOIN locales loc ON c.idlocal = loc.idlocal
    JOIN distritos d ON loc.iddistrito = d.iddistrito
    JOIN provincias pro ON d.idprovincia = pro.idprovincia
    
    -- JOIN para TODAS las cuotas
    JOIN cronogramas cron_all ON cron_all.idcontrato = c.idcontrato
    
    -- JOIN para primera cuota por vencer
    JOIN (
        SELECT 
            idcontrato,
            MIN(fechapago) AS min_fecha
        FROM cronogramas
        WHERE estado != 'Pagado'
        AND DATEDIFF(fechapago, CURDATE()) BETWEEN 0 AND 3
        GROUP BY idcontrato
    ) proxima ON proxima.idcontrato = c.idcontrato
    
    JOIN cronogramas cron 
        ON cron.idcontrato = c.idcontrato 
        AND cron.fechapago = proxima.min_fecha
        AND cron.estado != 'Pagado'
    
    WHERE c.estado = 'ACT'
    
    /*
    GROUP BY 
        c.idcontrato, cli.tipocliente, p.apellidos, p.nombres, 
        p.telprimario, e.razonsocial, e.telprimario,
        d.distrito, pro.provincia, m.marca, mo.modelo,
        cron.fechapago, cron.numcuota, cron.abonocapital, 
        cron.interes, cot.numcuotas*/
    
    ORDER BY dias_para_vencer ASC, cron.fechapago ASC;
END$$
DELIMITER ;


-- 9) ACTUALIZAR EL TELEFONO DESDE COBRANZA
DELIMITER $$

DROP PROCEDURE IF EXISTS sp_actualizar_telefono_cliente$$

CREATE PROCEDURE sp_actualizar_telefono_cliente(
    IN p_idcontrato INT,
    IN p_telefono_nuevo VARCHAR(15),
    IN p_telefono_actual VARCHAR(15)
)
BEGIN
    DECLARE v_idpersona INT DEFAULT NULL;
    DECLARE v_idempresa INT DEFAULT NULL;
    DECLARE v_tipocliente CHAR(1) DEFAULT NULL;
    DECLARE v_filas_afectadas INT DEFAULT 0;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 
            'error' as status,
            'Error en la base de datos al actualizar el teléfono' as message,
            NULL as telefono_nuevo;
    END;
    
    START TRANSACTION;
    
    SELECT 
        cli.tipocliente,
        cli.idpersona,
        cli.idempresa
    INTO 
        v_tipocliente,
        v_idpersona,
        v_idempresa
    FROM contratos cnt
    INNER JOIN cotizaciones cot ON cnt.idcotizacion = cot.idcotizacion
    INNER JOIN clientes cli ON cot.idcliente = cli.idcliente
    WHERE cnt.idcontrato = p_idcontrato;
    
    -- Verificar si se encontró el contrato
    IF v_tipocliente IS NULL THEN
        SELECT 
            'error' as status,
            CONCAT('No se encontró el contrato: ', p_idcontrato) as message,
            NULL as telefono_nuevo;
        ROLLBACK;
    ELSE
        -- Actualizar según el tipo de cliente
        IF v_tipocliente = 'P' THEN
            -- Actualizar teléfono de persona
            UPDATE personas 
            SET telprimario = p_telefono_nuevo,
                modificado = NOW()
            WHERE idpersona = v_idpersona;
            
            SET v_filas_afectadas = ROW_COUNT();
            
            IF v_filas_afectadas > 0 THEN
                COMMIT;
                SELECT 
                    'success' as status,
                    'Teléfono actualizado correctamente' as message,
                    p_telefono_nuevo as telefono_nuevo;
            ELSE
                ROLLBACK;
                SELECT 
                    'error' as status,
                    'No se pudo actualizar el teléfono de la persona' as message,
                    NULL as telefono_nuevo;
            END IF;
            
        ELSEIF v_tipocliente = 'E' THEN
            -- Actualizar teléfono de empresa
            UPDATE empresas 
            SET telprimario = p_telefono_nuevo
            WHERE idempresa = v_idempresa;
            
            SET v_filas_afectadas = ROW_COUNT();
            
            IF v_filas_afectadas > 0 THEN
                COMMIT;
                SELECT 
                    'success' as status,
                    'Teléfono actualizado correctamente' as message,
                    p_telefono_nuevo as telefono_nuevo;
            ELSE
                ROLLBACK;
                SELECT 
                    'error' as status,
                    'No se pudo actualizar el teléfono de la empresa' as message,
                    NULL as telefono_nuevo;
            END IF;
        ELSE
            ROLLBACK;
            SELECT 
                'error' as status,
                'Tipo de cliente no válido' as message,
                NULL as telefono_nuevo;
        END IF;
    END IF;
END$$

DELIMITER ;


-- 10) CONSULTA PARA EL PDF DE NOTIFICACIÓN DE COBRANZA - PRUEBA

DROP PROCEDURE IF EXISTS sp_get_datos_reporte_notificacion_pdf;
DELIMITER $$
CREATE PROCEDURE sp_get_datos_reporte_notificacion_pdf(IN p_idcontrato INT)
BEGIN

    SELECT 
        -- Datos del cliente
        CASE 
            WHEN cl.tipocliente = 'P' THEN CONCAT(pe.apellidos, ' ', pe.nombres)
            WHEN cl.tipocliente = 'E' THEN e.razonsocial
        END AS nombre_cliente,

        CASE WHEN cl.tipocliente = 'P' THEN pe.tipodoc ELSE 'RUC' END AS tipo_documento,
        CASE WHEN cl.tipocliente = 'P' THEN pe.nrodoc WHEN cl.tipocliente = 'E' THEN e.ruc END AS numero_documento,
        CASE WHEN cl.tipocliente = 'P' THEN CONCAT(pe.direccion, ', ', dp.distrito, ', ', pp.provincia)
             WHEN cl.tipocliente = 'E' THEN CONCAT(e.direccion, ', ', de.distrito, ', ', peprov.provincia)
        END AS direccion_completa,

        ma.marca,
        mo.modelo,
        mo.anio AS vehiculo_anio,
        v.color,
        v.placa,
        v.chasis AS numero_chasis,
        v.seriemotor AS numero_motor,

        cnt.idcontrato,
        cnt.fechainicio AS fecha_contrato,
        cnt.penalidadbase AS porcentaje_penalidad,

        cot.moneda,
        cot.valorcuota,
        
        GROUP_CONCAT(
            DISTINCT
            (CASE
                WHEN (ROUND(GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0),2) > 0)
                     AND (ROUND(GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0),2) = 0)
				THEN CONCAT(
					'MORA DE MES:', MONTH(cr.fechapago), 
					' MONTO:', 
					REPLACE(FORMAT(ROUND(GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0),2),2),',','')
				)
                WHEN ROUND(GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0),2) > 0
                
                THEN CONCAT(
					'CUOTA DE MES:', MONTH(cr.fechapago), 
					' CON MORA MONTO:', 
					REPLACE(FORMAT(
						ROUND(GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0),2)
						+
						ROUND(GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0),2)
					,2),',','')
				)
                ELSE NULL
            END)
            ORDER BY cr.fechapago
            SEPARATOR ' || '
        ) AS detalle_cuotas_vencidas,

        SUM(
            CASE
                WHEN (GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0) > 0)
                  OR (GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0) > 0)
                THEN 1 ELSE 0
            END
        ) AS cantidad_cuotas_vencidas,

        ROUND(SUM( GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0) ),2) AS total_cuotas_vencidas,

        ROUND(SUM( GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0) ),2) AS total_penalidades_vencidas,

        ROUND(SUM(
            GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0)
            +
            GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0)
        ),2) AS total_deuda_vencida,

        MIN(cr.fechapago) AS fecha_primera_vencida,
        DATEDIFF(CURDATE(), MIN(cr.fechapago)) AS dias_atraso

    FROM contratos cnt
    JOIN cotizaciones cot ON cnt.idcotizacion = cot.idcotizacion
    JOIN clientes cl ON cot.idcliente = cl.idcliente
    LEFT JOIN personas pe ON cl.idpersona = pe.idpersona
    LEFT JOIN empresas e ON cl.idempresa = e.idempresa
    LEFT JOIN distritos dp ON pe.iddistrito = dp.iddistrito
    LEFT JOIN provincias pp ON dp.idprovincia = pp.idprovincia
    LEFT JOIN distritos de ON e.iddistrito = de.iddistrito
    LEFT JOIN provincias peprov ON de.idprovincia = peprov.idprovincia
    JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    JOIN modelos mo ON v.idmodelo = mo.idmodelo
    JOIN marcas ma ON mo.idmarca = ma.idmarca

    LEFT JOIN cronogramas cr ON cr.idcontrato = cnt.idcontrato
        AND cr.estado IN ('Pendiente', 'Vencido')
        AND cr.fechapago < CURDATE()

    LEFT JOIN (
        SELECT 
            idcronograma,
            SUM(CASE WHEN tipo = 'Cuota' THEN amortizacion ELSE 0 END) AS pagado_cuota,
            SUM(CASE WHEN tipo = 'Penalidad' THEN amortizacion ELSE 0 END) AS pagado_penal
        FROM pagos
        GROUP BY idcronograma
    ) pgs ON pgs.idcronograma = cr.idcronograma

    WHERE cnt.idcontrato = p_idcontrato
    GROUP BY cnt.idcontrato;
END$$
DELIMITER ;


-- 11) CONSULTA PARA EL PDF DE RECOJO VEHICULAR DE COBRANZA

DROP PROCEDURE IF EXISTS sp_get_datos_reporte_recojo_vehicular_pdf;
DELIMITER $$
CREATE PROCEDURE sp_get_datos_reporte_recojo_vehicular_pdf(IN p_idcontrato INT)
BEGIN
    SELECT 
        -- Datos del cliente
        CASE 
            WHEN cl.tipocliente = 'P' THEN CONCAT(pe.apellidos, ' ', pe.nombres)
            WHEN cl.tipocliente = 'E' THEN e.razonsocial
        END AS nombre_cliente,

        CASE WHEN cl.tipocliente = 'P' THEN pe.tipodoc ELSE 'RUC' END AS tipo_documento,
        CASE WHEN cl.tipocliente = 'P' THEN pe.nrodoc WHEN cl.tipocliente = 'E' THEN e.ruc END AS numero_documento,
        CASE WHEN cl.tipocliente = 'P' THEN CONCAT(pe.direccion, ', ', dp.distrito, ', ', pp.provincia)
             WHEN cl.tipocliente = 'E' THEN CONCAT(e.direccion, ', ', de.distrito, ', ', peprov.provincia)
        END AS direccion_completa,
        
        CASE 
            WHEN cl.tipocliente = 'P' THEN pe.telprimario
            WHEN cl.tipocliente = 'E' THEN e.telprimario
        END AS telefono,

        ma.marca,
        mo.modelo,
        mo.anio AS vehiculo_anio,
        v.color,
        v.placa,
        v.chasis AS numero_chasis,
        v.seriemotor AS numero_motor,

        cnt.idcontrato,
        cnt.fechainicio AS fecha_contrato,
        cnt.diapago AS dia_pago_mensual,
        cnt.penalidadbase AS porcentaje_penalidad,

        cot.moneda,
        cot.valorcuota,
        
        GROUP_CONCAT(
            DISTINCT
            (CASE
                WHEN (ROUND(GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0),2) > 0)
                     AND (ROUND(GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0),2) = 0)
                THEN CONCAT(
                    'MORA DE MES:', MONTH(cr.fechapago), 
                    ' MONTO:', 
                    REPLACE(FORMAT(ROUND(GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0),2),2),',','')
                )
                
                WHEN ROUND(GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0),2) > 0
                THEN CONCAT(
                    'CUOTA DE MES:', MONTH(cr.fechapago), 
                    ' CON MORA MONTO:', 
                    REPLACE(FORMAT(
                        ROUND(GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0),2)
                        +
                        ROUND(GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0),2)
                    ,2),',','')
                )

                ELSE NULL
            END)
            ORDER BY cr.fechapago
            SEPARATOR ' || '
        ) AS detalle_cuotas_vencidas,

        SUM(
            CASE
                WHEN (GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0) > 0)
                  OR (GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0) > 0)
                THEN 1 ELSE 0
            END
        ) AS cantidad_cuotas_vencidas,

        ROUND(SUM( GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0) ),2) AS total_cuotas_vencidas,

        ROUND(SUM( GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0) ),2) AS total_penalidades_vencidas,

        ROUND(SUM(
            GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0)
            +
            GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0)
        ),2) AS total_deuda_vencida,

        MIN(cr.fechapago) AS fecha_primera_vencida,
        DATEDIFF(CURDATE(), MIN(cr.fechapago)) AS dias_atraso

    FROM contratos cnt
    JOIN cotizaciones cot ON cnt.idcotizacion = cot.idcotizacion
    JOIN clientes cl ON cot.idcliente = cl.idcliente
    LEFT JOIN personas pe ON cl.idpersona = pe.idpersona
    LEFT JOIN empresas e ON cl.idempresa = e.idempresa
    LEFT JOIN distritos dp ON pe.iddistrito = dp.iddistrito
    LEFT JOIN provincias pp ON dp.idprovincia = pp.idprovincia
    LEFT JOIN distritos de ON e.iddistrito = de.iddistrito
    LEFT JOIN provincias peprov ON de.idprovincia = peprov.idprovincia
    JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    JOIN modelos mo ON v.idmodelo = mo.idmodelo
    JOIN marcas ma ON mo.idmarca = ma.idmarca

    LEFT JOIN cronogramas cr ON cr.idcontrato = cnt.idcontrato
        AND cr.estado IN ('Pendiente', 'Vencido')
        AND cr.fechapago < CURDATE()

    LEFT JOIN (
        SELECT 
            idcronograma,
            SUM(CASE WHEN tipo = 'Cuota' THEN amortizacion ELSE 0 END) AS pagado_cuota,
            SUM(CASE WHEN tipo = 'Penalidad' THEN amortizacion ELSE 0 END) AS pagado_penal
        FROM pagos
        GROUP BY idcronograma
    ) pgs ON pgs.idcronograma = cr.idcronograma

    WHERE cnt.idcontrato = p_idcontrato
    GROUP BY cnt.idcontrato;
    
END$$
DELIMITER ;



/*
DROP PROCEDURE IF EXISTS sp_get_datos_reporte_recojo_vehicular_pdf;
DELIMITER $$
CREATE PROCEDURE sp_get_datos_reporte_recojo_vehicular_pdf(IN p_idcontrato INT)
BEGIN
	SELECT 
        -- Datos del cliente
        CASE 
            WHEN cl.tipocliente = 'P' THEN CONCAT(pe.apellidos, ' ', pe.nombres)
            WHEN cl.tipocliente = 'E' THEN e.razonsocial
        END AS nombre_cliente,

        CASE WHEN cl.tipocliente = 'P' THEN pe.tipodoc ELSE 'RUC' END AS tipo_documento,
        CASE WHEN cl.tipocliente = 'P' THEN pe.nrodoc WHEN cl.tipocliente = 'E' THEN e.ruc END AS numero_documento,
        CASE WHEN cl.tipocliente = 'P' THEN CONCAT(pe.direccion, ', ', dp.distrito, ', ', pp.provincia)
             WHEN cl.tipocliente = 'E' THEN CONCAT(e.direccion, ', ', de.distrito, ', ', peprov.provincia)
        END AS direccion_completa,
        
        CASE 
			WHEN cl.tipocliente = 'P' THEN pe.telprimario
			WHEN cl.tipocliente = 'E' THEN e.telprimario
		END AS telefono,

        ma.marca,
        mo.modelo,
        mo.anio AS vehiculo_anio,
        v.color,
        v.placa,
        v.chasis AS numero_chasis,
        v.seriemotor AS numero_motor,

        cnt.idcontrato,
        cnt.fechainicio AS fecha_contrato,
        cnt.penalidadbase AS porcentaje_penalidad,

        cot.moneda,
        cot.valorcuota,
        
        GROUP_CONCAT(
            DISTINCT
            (CASE
                WHEN (ROUND(GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0),2) > 0)
                     AND (ROUND(GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0),2) = 0)
				THEN CONCAT(
					'MORA DE MES:', MONTH(cr.fechapago), 
					' MONTO:', 
					REPLACE(FORMAT(ROUND(GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0),2),2),',','')
				)
                
                WHEN ROUND(GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0),2) > 0
                
                THEN CONCAT(
					'CUOTA DE MES:', MONTH(cr.fechapago), 
					' CON MORA MONTO:', 
					REPLACE(FORMAT(
						ROUND(GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0),2)
						+
						ROUND(GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0),2)
					,2),',','')
				)

                ELSE NULL
            END)
            ORDER BY cr.fechapago
            SEPARATOR ' || '
        ) AS detalle_cuotas_vencidas,

        SUM(
            CASE
                WHEN (GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0) > 0)
                  OR (GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0) > 0)
                THEN 1 ELSE 0
            END
        ) AS cantidad_cuotas_vencidas,

        ROUND(SUM( GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0) ),2) AS total_cuotas_vencidas,

        ROUND(SUM( GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0) ),2) AS total_penalidades_vencidas,

        ROUND(SUM(
            GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0)
            +
            GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0)
        ),2) AS total_deuda_vencida,

        MIN(cr.fechapago) AS fecha_primera_vencida,
        DATEDIFF(CURDATE(), MIN(cr.fechapago)) AS dias_atraso

    FROM contratos cnt
    JOIN cotizaciones cot ON cnt.idcotizacion = cot.idcotizacion
    JOIN clientes cl ON cot.idcliente = cl.idcliente
    LEFT JOIN personas pe ON cl.idpersona = pe.idpersona
    LEFT JOIN empresas e ON cl.idempresa = e.idempresa
    LEFT JOIN distritos dp ON pe.iddistrito = dp.iddistrito
    LEFT JOIN provincias pp ON dp.idprovincia = pp.idprovincia
    LEFT JOIN distritos de ON e.iddistrito = de.iddistrito
    LEFT JOIN provincias peprov ON de.idprovincia = peprov.idprovincia
    JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    JOIN modelos mo ON v.idmodelo = mo.idmodelo
    JOIN marcas ma ON mo.idmarca = ma.idmarca

    LEFT JOIN cronogramas cr ON cr.idcontrato = cnt.idcontrato
        AND cr.estado IN ('Pendiente', 'Vencido')
        AND cr.fechapago < CURDATE()

    LEFT JOIN (
        SELECT 
            idcronograma,
            SUM(CASE WHEN tipo = 'Cuota' THEN amortizacion ELSE 0 END) AS pagado_cuota,
            SUM(CASE WHEN tipo = 'Penalidad' THEN amortizacion ELSE 0 END) AS pagado_penal
        FROM pagos
        GROUP BY idcronograma
    ) pgs ON pgs.idcronograma = cr.idcronograma

    WHERE cnt.idcontrato = p_idcontrato
    GROUP BY cnt.idcontrato;
	
END$$
DELIMITER ;
*/    
        
-- PRUEBAS
-- CALL sp_get_datos_reporte_recojo_vehicular_pdf(7);
-- CALL sp_get_datos_reporte_notificacion_pdf(7);
-- CALL sp_get_estadisticas_cobranza();
-- CALL sp_get_tarjetas_cobranza();
-- CALL sp_get_info_cliente_cobranza(7);
-- CALL sp_get_detalle_contrato_cobranza(7);
-- CALL sp_get_cronograma_pagos_cobranza(7);
-- CALL sp_get_historial_pagos_cobranza(7, 5);
-- CALL sp_get_cuotas_vencidas();
-- CALL sp_get_cuotas_proximas_vencer();