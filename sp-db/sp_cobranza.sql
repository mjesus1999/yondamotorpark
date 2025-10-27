
USE MOTORPARK;

-- 1) ESTADÍSTICAS 

DROP PROCEDURE IF EXISTS sp_get_estadisticas_cobranza;
DELIMITER $$
CREATE PROCEDURE sp_get_estadisticas_cobranza()
BEGIN
    SELECT
        COUNT(DISTINCT CASE 
            WHEN cr.estado IN ('Pendiente','Vencido') 
            THEN c.idcontrato 
        END) AS total_deudores,
        
        COUNT(DISTINCT CASE 
            WHEN cr.estado != 'Pagado' 
            AND DATEDIFF(cr.fechapago, CURDATE()) BETWEEN 0 AND 3 
            THEN c.idcontrato 
        END) AS por_vencer_3dias,
        
        COUNT(DISTINCT CASE 
            WHEN cr.estado IN ('Pendiente', 'Vencido')
            AND cr.fechapago < CURDATE()
            THEN c.idcontrato 
        END) AS contratos_con_vencidos,
        
        IFNULL(SUM(CASE 
            WHEN cr.estado IN ('Pendiente','Vencido')
            AND cr.fechapago < CURDATE()
            THEN (cr.abonocapital + cr.interes + IFNULL(cr.penalidad, 0))
            ELSE 0 
        END), 0) AS total_por_cobrar
        
    FROM contratos c
    INNER JOIN cronogramas cr ON cr.idcontrato = c.idcontrato
    WHERE c.estado = 'ACT';
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
            ELSE e.razonsocial
        END AS nombre_cliente,
        
        CASE 
            WHEN cl.tipocliente = 'P' THEN p.telprimario
            ELSE e.telprimario
        END AS telefono,
        
        CONCAT(d.distrito, ' / ', pr.provincia) AS local,
        CONCAT(ma.marca, ' / ', mo.modelo) AS tipo_vehiculo,
        
        CASE
            WHEN DATEDIFF(MIN(cr.fechapago), CURDATE()) < 0 THEN 'VENCIDO'
            WHEN DATEDIFF(MIN(cr.fechapago), CURDATE()) = 0 THEN 'VENCE HOY'
            WHEN DATEDIFF(MIN(cr.fechapago), CURDATE()) = 1 THEN 'Vence en 1 día'
            WHEN DATEDIFF(MIN(cr.fechapago), CURDATE()) = 2 THEN 'Vence en 2 días'
            WHEN DATEDIFF(MIN(cr.fechapago), CURDATE()) = 3 THEN 'Vence en 3 días'
            ELSE 'POR VENCER'
        END AS estado_vencimiento,
        
        DATE_FORMAT(MIN(cr.fechapago), '%d/%m/%Y') AS fecha_vencimiento
        
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
    INNER JOIN cronogramas cr ON cr.idcontrato = c.idcontrato
    
    WHERE c.estado = 'ACT'
      AND cr.estado IN ('Pendiente', 'Vencido')
      AND DATEDIFF(cr.fechapago, CURDATE()) <= 3
    
    GROUP BY c.idcontrato, cl.tipocliente, p.apellidos, p.nombres, p.telprimario,
             e.razonsocial, e.telprimario, d.distrito, pr.provincia, ma.marca, mo.modelo
    
    ORDER BY 
        CASE 
            WHEN DATEDIFF(MIN(cr.fechapago), CURDATE()) < 0 THEN 1
            WHEN DATEDIFF(MIN(cr.fechapago), CURDATE()) = 0 THEN 2
            ELSE 3
        END,
        MIN(cr.fechapago) ASC;
END$$
DELIMITER ;


-- 3) INFORMACION COMPLETA DEL CLIENTE

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


-- 4) DETALLE DEL CONTRATO POR PERSONA

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
    
    WHERE cnt.idcontrato = p_idcontrato
    
    GROUP BY 
        cnt.idcontrato, cnt.fechainicio, cnt.diapago, cnt.escredito,
        cnt.penalidadbase, cnt.estado, cnt.observaciones,
        ma.marca, mo.modelo, mo.anio, tv.tipovehiculo,
        v.version, v.condicion, v.color, v.placa, v.chasis,
        cmb.combustible, cot.moneda, cot.precioventa, cot.inicial,
        cot.numcuotas, cot.valorcuota, cot.gastosadministrativos,
        l.tienda, dl.distrito, pl.provincia;
END$$
DELIMITER ;


-- 5) DETALLE DEL CRONOGRAMA POR CONTRATO

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


-- 6) HISTORIAL DE PAGOS

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
        CASE 
            WHEN cli.tipocliente = 'P' THEN CONCAT(p.apellidos, ' ', p.nombres)
            ELSE e.razonsocial
        END AS cliente,
        CASE 
            WHEN cli.tipocliente = 'P' THEN p.nrodoc
            ELSE e.ruc
        END AS documento,
        CASE 
            WHEN cli.tipocliente = 'P' THEN p.telprimario
            ELSE e.telprimario
        END AS telefono,
        
        -- Ubicación completa (provincia/distrito/dirección)
        CASE
            WHEN cli.tipocliente = 'P' THEN 
                CONCAT(
                    COALESCE(pp.provincia, 'Sin provincia'), ' / ',
                    COALESCE(dp.distrito, 'Sin distrito'), ' / ', 
                    COALESCE(p.direccion, 'Sin direccion')
                )
            ELSE 
                CONCAT(
                    COALESCE(pe.provincia, 'Sin provincia'), ' / ',
                    COALESCE(de.distrito, 'Sin distrito'), ' / ', 
                    COALESCE(e.direccion, 'Sin direccion')
                )
        END AS ubicacion_cliente,
        
        -- Campos separados para filtrado (CLAVE PARA BÚSQUEDA)
        CASE 
            WHEN cli.tipocliente = 'P' THEN COALESCE(pp.provincia, 'Sin provincia')
            ELSE COALESCE(pe.provincia, 'Sin provincia')
        END AS provincia_cliente,
        
        CASE 
            WHEN cli.tipocliente = 'P' THEN COALESCE(dp.distrito, 'Sin distrito')
            ELSE COALESCE(de.distrito, 'Sin distrito')
        END AS distrito_cliente,
        
        CONCAT(m.marca, ' / ', mo.modelo, ' / ', v.color, ' / ', v.placa) AS vehiculo,
        COALESCE(d.distrito, 'Sin distrito') AS tienda,
        cot.numcuotas AS cuotas_totales,
        
        MIN(CASE 
            WHEN cron.fechapago < CURDATE() AND cron.estado IN ('Pendiente', 'Vencido')
            THEN (cron.abonocapital + cron.interes + IFNULL(cron.penalidad, 0))
        END) AS monto_primera_vencida,
        
        SUM(CASE 
            WHEN cron.fechapago < CURDATE() AND cron.estado IN ('Pendiente', 'Vencido')
            THEN (cron.abonocapital + cron.interes + IFNULL(cron.penalidad, 0))
            ELSE 0
        END) AS deuda_vencida,
        
        SUM(CASE 
            WHEN cron.fechapago < CURDATE() AND cron.estado IN ('Pendiente', 'Vencido')
            THEN 1 
            ELSE 0 
        END) AS cuotas_vencidas,
        
        SUM(CASE 
            WHEN cron.estado = 'Pagado' 
            THEN 1 
            ELSE 0 
        END) AS cuotas_pagadas,
        
        CONCAT(
            SUM(CASE 
                WHEN cron.fechapago < CURDATE() AND cron.estado IN ('Pendiente', 'Vencido')
                THEN 1 ELSE 0 
            END),
            ' vencidas de ',
            GREATEST(cot.numcuotas - SUM(CASE WHEN cron.estado = 'Pagado' THEN 1 ELSE 0 END), 0)
        ) AS estado_pagos,
        
        MIN(CASE 
            WHEN cron.fechapago < CURDATE() AND cron.estado IN ('Pendiente', 'Vencido')
            THEN cron.fechapago
        END) AS fecha_vencida_mas_antigua,
        
        DATEDIFF(CURDATE(), MIN(CASE 
            WHEN cron.fechapago < CURDATE() AND cron.estado IN ('Pendiente', 'Vencido')
            THEN cron.fechapago
        END)) AS dias_atraso
        
    FROM contratos c
    INNER JOIN cotizaciones cot ON c.idcotizacion = cot.idcotizacion
    INNER JOIN clientes cli ON cot.idcliente = cli.idcliente
    LEFT JOIN personas p ON cli.idpersona = p.idpersona
    LEFT JOIN empresas e ON cli.idempresa = e.idempresa
    INNER JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    INNER JOIN modelos mo ON v.idmodelo = mo.idmodelo
    INNER JOIN marcas m ON mo.idmarca = m.idmarca
    LEFT JOIN locales loc ON c.idlocal = loc.idlocal
    
    -- Joins para persona (distrito y provincia)
    LEFT JOIN distritos dp ON p.iddistrito = dp.iddistrito
    LEFT JOIN provincias pp ON dp.idprovincia = pp.idprovincia
    
    -- Joins para empresa (distrito y provincia)
    LEFT JOIN distritos de ON e.iddistrito = de.iddistrito
    LEFT JOIN provincias pe ON de.idprovincia = pe.idprovincia
    
    -- Distrito de tienda
    LEFT JOIN distritos d ON loc.iddistrito = d.iddistrito
    
    INNER JOIN cronogramas cron ON cron.idcontrato = c.idcontrato
    
    WHERE c.estado = 'ACT'
    
    GROUP BY 
        c.idcontrato, cli.tipocliente, p.apellidos, p.nombres, p.nrodoc, p.telprimario,
        e.razonsocial, e.ruc, e.telprimario, m.marca, mo.modelo, d.distrito, cot.numcuotas,
        p.direccion, e.direccion, dp.distrito, de.distrito, pp.provincia, pe.provincia
    
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
            ELSE e.razonsocial
        END AS cliente,
        CASE 
            WHEN cli.tipocliente = 'P' THEN p.telprimario
            ELSE e.telprimario
        END AS telefono,
        COALESCE(d.distrito, 'Sin distrito') AS local,
        CONCAT(m.marca, ' / ', mo.modelo) AS vehiculo,
        
        CASE
            WHEN DATEDIFF(MIN(CASE 
                WHEN cron.estado != 'Pagado' AND DATEDIFF(cron.fechapago, CURDATE()) BETWEEN 0 AND 3
                THEN cron.fechapago 
            END), CURDATE()) = 0 THEN 'VENCE HOY'
            WHEN DATEDIFF(MIN(CASE 
                WHEN cron.estado != 'Pagado' AND DATEDIFF(cron.fechapago, CURDATE()) BETWEEN 0 AND 3
                THEN cron.fechapago 
            END), CURDATE()) = 1 THEN 'Vence mañana'
            WHEN DATEDIFF(MIN(CASE 
                WHEN cron.estado != 'Pagado' AND DATEDIFF(cron.fechapago, CURDATE()) BETWEEN 0 AND 3
                THEN cron.fechapago 
            END), CURDATE()) = 2 THEN 'Vence en 2 días'
            WHEN DATEDIFF(MIN(CASE 
                WHEN cron.estado != 'Pagado' AND DATEDIFF(cron.fechapago, CURDATE()) BETWEEN 0 AND 3
                THEN cron.fechapago 
            END), CURDATE()) = 3 THEN 'Vence en 3 días'
            ELSE 'Próximo a vencer'
        END AS estado_vencimiento,
        
        DATE_FORMAT(MIN(CASE 
            WHEN cron.estado != 'Pagado' AND DATEDIFF(cron.fechapago, CURDATE()) BETWEEN 0 AND 3
            THEN cron.fechapago 
        END), '%d/%m/%Y') AS fecha_vencimiento,
        
        MIN(CASE 
            WHEN cron.estado != 'Pagado' AND DATEDIFF(cron.fechapago, CURDATE()) BETWEEN 0 AND 3
            THEN cron.numcuota 
        END) AS numcuota,
        
        MIN(CASE 
            WHEN cron.estado != 'Pagado' AND DATEDIFF(cron.fechapago, CURDATE()) BETWEEN 0 AND 3
            THEN (cron.abonocapital + cron.interes)
        END) AS monto_cuota,
        
        cot.numcuotas AS cuotas_totales,
        SUM(CASE WHEN cron.estado = 'Pagado' THEN 1 ELSE 0 END) AS cuotas_pagadas,
        
        DATEDIFF(MIN(CASE 
            WHEN cron.estado != 'Pagado' AND DATEDIFF(cron.fechapago, CURDATE()) BETWEEN 0 AND 3
            THEN cron.fechapago 
        END), CURDATE()) AS dias_para_vencer
        
    FROM contratos c
    INNER JOIN cotizaciones cot ON c.idcotizacion = cot.idcotizacion
    INNER JOIN clientes cli ON cot.idcliente = cli.idcliente
    LEFT JOIN personas p ON cli.idpersona = p.idpersona
    LEFT JOIN empresas e ON cli.idempresa = e.idempresa
    INNER JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    INNER JOIN modelos mo ON v.idmodelo = mo.idmodelo
    INNER JOIN marcas m ON mo.idmarca = m.idmarca
    INNER JOIN locales loc ON c.idlocal = loc.idlocal
    INNER JOIN distritos d ON loc.iddistrito = d.iddistrito
    INNER JOIN cronogramas cron ON cron.idcontrato = c.idcontrato
    
    WHERE c.estado = 'ACT'
    
    GROUP BY 
        c.idcontrato, cli.tipocliente, p.apellidos, p.nombres, p.telprimario,
        e.razonsocial, e.telprimario, d.distrito, m.marca, mo.modelo, cot.numcuotas
    
    HAVING dias_para_vencer IS NOT NULL
    ORDER BY dias_para_vencer ASC;
END$$
DELIMITER ;


-- 9) ACTUALIZAR TELEFONO

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


-- 10) REPORTE PDF NOTIFICACIÓN

DROP PROCEDURE IF EXISTS sp_get_datos_reporte_notificacion_pdf;
DELIMITER $$
CREATE PROCEDURE sp_get_datos_reporte_notificacion_pdf(IN p_idcontrato INT)
BEGIN
    SELECT 
        -- Datos del cliente
        CASE 
            WHEN cl.tipocliente = 'P' THEN CONCAT(pe.apellidos, ' ', pe.nombres)
            ELSE e.razonsocial
        END AS nombre_cliente,
        CASE WHEN cl.tipocliente = 'P' THEN pe.tipodoc ELSE 'RUC' END AS tipo_documento,
        CASE WHEN cl.tipocliente = 'P' THEN pe.nrodoc ELSE e.ruc END AS numero_documento,
        CASE 
            WHEN cl.tipocliente = 'P' THEN CONCAT(pe.direccion, ', ', dp.distrito, ', ', pp.provincia)
            ELSE CONCAT(e.direccion, ', ', de.distrito, ', ', peprov.provincia)
        END AS direccion_completa,
        
        -- Datos del vehículo
        ma.marca,
        mo.modelo,
        mo.anio AS vehiculo_anio,
        v.color,
        v.placa,
        v.chasis AS numero_chasis,
        v.seriemotor AS numero_motor,
        
        -- Datos del contrato
        cnt.idcontrato,
        cnt.fechainicio AS fecha_contrato,
        cnt.penalidadbase AS porcentaje_penalidad,
        cot.moneda,
        cot.valorcuota,
        
        -- Datos del colaborador
        ar.area AS colaborador_area,
        CONCAT(SUBSTRING_INDEX(pcolab.nombres, ' ', 1), ' ', 
               SUBSTRING_INDEX(pcolab.apellidos, ' ', 1)) AS colaborador_nombre,
        pcolab.telprimario AS colaborador_telefono,
        
        -- Detalle de cuotas vencidas
        GROUP_CONCAT(
            DISTINCT
            CASE
                -- Solo tiene penalidad pendiente
                WHEN ROUND(cr.abonocapital + cr.interes, 2) = 0 
                     AND ROUND(IFNULL(cr.penalidad, 0), 2) > 0
                THEN CONCAT('MORA:', MONTH(cr.fechapago), ':', 
                           FORMAT(cr.penalidad, 2))
                
                -- Tiene cuota pendiente (con o sin mora)
                WHEN ROUND(cr.abonocapital + cr.interes, 2) > 0
                THEN CONCAT('CUOTA:', MONTH(cr.fechapago), ':', 
                           FORMAT(cr.abonocapital + cr.interes + IFNULL(cr.penalidad, 0), 2))
                
                ELSE NULL
            END
            ORDER BY cr.fechapago
            SEPARATOR ' || '
        ) AS detalle_cuotas_vencidas,
        
        -- Estadísticas
        COUNT(DISTINCT cr.idcronograma) AS cantidad_cuotas_vencidas,
        ROUND(SUM(cr.abonocapital + cr.interes), 2) AS total_cuotas_vencidas,
        ROUND(SUM(IFNULL(cr.penalidad, 0)), 2) AS total_penalidades_vencidas,
        ROUND(SUM(cr.abonocapital + cr.interes + IFNULL(cr.penalidad, 0)), 2) AS total_deuda_vencida,
        MIN(cr.fechapago) AS fecha_primera_vencida,
        DATEDIFF(CURDATE(), MIN(cr.fechapago)) AS dias_atraso
        
    FROM contratos cnt
    INNER JOIN cotizaciones cot ON cnt.idcotizacion = cot.idcotizacion
    INNER JOIN clientes cl ON cot.idcliente = cl.idcliente
    LEFT JOIN personas pe ON cl.idpersona = pe.idpersona
    LEFT JOIN empresas e ON cl.idempresa = e.idempresa
    LEFT JOIN distritos dp ON pe.iddistrito = dp.iddistrito
    LEFT JOIN provincias pp ON dp.idprovincia = pp.idprovincia
    LEFT JOIN distritos de ON e.iddistrito = de.iddistrito
    LEFT JOIN provincias peprov ON de.idprovincia = peprov.idprovincia
    INNER JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    INNER JOIN modelos mo ON v.idmodelo = mo.idmodelo
    INNER JOIN marcas ma ON mo.idmarca = ma.idmarca
    LEFT JOIN colaboradores colab ON cnt.idlogistica = colab.idcolaborador
    LEFT JOIN contratoslaborales cl_colab ON colab.idcontratolaboral = cl_colab.idcontratolaboral
    LEFT JOIN personas pcolab ON cl_colab.idpersona = pcolab.idpersona
    LEFT JOIN cargos cg ON cl_colab.idcargo = cg.idcargo
    LEFT JOIN areas ar ON cg.idarea = ar.idarea
    INNER JOIN cronogramas cr ON cr.idcontrato = cnt.idcontrato
    
    WHERE cnt.idcontrato = p_idcontrato
      AND cr.estado IN ('Pendiente', 'Vencido')
      AND cr.fechapago < CURDATE()
    
    GROUP BY cnt.idcontrato, cl.tipocliente, pe.nrodoc, e.ruc;
END$$
DELIMITER ;


-- 11) REPORTE PDF RECOJO VEHICULAR

DROP PROCEDURE IF EXISTS sp_get_datos_reporte_recojo_vehicular_pdf;
DELIMITER $$
CREATE PROCEDURE sp_get_datos_reporte_recojo_vehicular_pdf(IN p_idcontrato INT)
BEGIN
    SELECT 
        -- Datos del cliente
        CASE 
            WHEN cl.tipocliente = 'P' THEN CONCAT(pe.apellidos, ' ', pe.nombres)
            ELSE e.razonsocial
        END AS nombre_cliente,
        CASE WHEN cl.tipocliente = 'P' THEN pe.tipodoc ELSE 'RUC' END AS tipo_documento,
        CASE WHEN cl.tipocliente = 'P' THEN pe.nrodoc ELSE e.ruc END AS numero_documento,
        CASE 
            WHEN cl.tipocliente = 'P' THEN CONCAT(pe.direccion, ', ', dp.distrito, ', ', pp.provincia)
            ELSE CONCAT(e.direccion, ', ', de.distrito, ', ', peprov.provincia)
        END AS direccion_completa,
        CASE 
            WHEN cl.tipocliente = 'P' THEN pe.telprimario
            ELSE e.telprimario
        END AS telefono,
        
        -- Datos del vehículo
        ma.marca,
        mo.modelo,
        mo.anio AS vehiculo_anio,
        v.color,
        v.placa,
        v.chasis AS numero_chasis,
        v.seriemotor AS numero_motor,
        
        -- Datos del contrato
        cnt.idcontrato,
        cnt.fechainicio AS fecha_contrato,
        cnt.diapago AS dia_pago_mensual,
        cnt.penalidadbase AS porcentaje_penalidad,
        cot.moneda,
        cot.valorcuota,
        
        -- Datos del colaborador
        ar.area AS colaborador_area,
        CONCAT(SUBSTRING_INDEX(pcolab.nombres, ' ', 1), ' ', 
               SUBSTRING_INDEX(pcolab.apellidos, ' ', 1)) AS colaborador_nombre,
        pcolab.telprimario AS colaborador_telefono,
        
        -- Detalle de cuotas vencidas
        GROUP_CONCAT(
            DISTINCT
            CASE
                -- Solo tiene penalidad pendiente
                WHEN ROUND(cr.abonocapital + cr.interes, 2) = 0 
                     AND ROUND(IFNULL(cr.penalidad, 0), 2) > 0
                THEN CONCAT('MORA:', MONTH(cr.fechapago), ':', 
                           FORMAT(cr.penalidad, 2))
                
                -- Tiene cuota pendiente (con o sin mora)
                WHEN ROUND(cr.abonocapital + cr.interes, 2) > 0
                THEN CONCAT('CUOTA:', MONTH(cr.fechapago), ':', 
                           FORMAT(cr.abonocapital + cr.interes + IFNULL(cr.penalidad, 0), 2))
				
                ELSE NULL
            END
            ORDER BY cr.fechapago
            SEPARATOR ' || '
        ) AS detalle_cuotas_vencidas,
        
        -- Estadísticas
        COUNT(DISTINCT cr.idcronograma) AS cantidad_cuotas_vencidas,
        ROUND(SUM(cr.abonocapital + cr.interes), 2) AS total_cuotas_vencidas,
        ROUND(SUM(IFNULL(cr.penalidad, 0)), 2) AS total_penalidades_vencidas,
        ROUND(SUM(cr.abonocapital + cr.interes + IFNULL(cr.penalidad, 0)), 2) AS total_deuda_vencida,
        MIN(cr.fechapago) AS fecha_primera_vencida,
        DATEDIFF(CURDATE(), MIN(cr.fechapago)) AS dias_atraso
        
    FROM contratos cnt
    INNER JOIN cotizaciones cot ON cnt.idcotizacion = cot.idcotizacion
    INNER JOIN clientes cl ON cot.idcliente = cl.idcliente
    LEFT JOIN personas pe ON cl.idpersona = pe.idpersona
    LEFT JOIN empresas e ON cl.idempresa = e.idempresa
    LEFT JOIN distritos dp ON pe.iddistrito = dp.iddistrito
    LEFT JOIN provincias pp ON dp.idprovincia = pp.idprovincia
    LEFT JOIN distritos de ON e.iddistrito = de.iddistrito
    LEFT JOIN provincias peprov ON de.idprovincia = peprov.idprovincia
    INNER JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    INNER JOIN modelos mo ON v.idmodelo = mo.idmodelo
    INNER JOIN marcas ma ON mo.idmarca = ma.idmarca
    LEFT JOIN colaboradores colab ON cnt.idlogistica = colab.idcolaborador
    LEFT JOIN contratoslaborales cl_colab ON colab.idcontratolaboral = cl_colab.idcontratolaboral
    LEFT JOIN personas pcolab ON cl_colab.idpersona = pcolab.idpersona
    LEFT JOIN cargos cg ON cl_colab.idcargo = cg.idcargo
    LEFT JOIN areas ar ON cg.idarea = ar.idarea
    INNER JOIN cronogramas cr ON cr.idcontrato = cnt.idcontrato
    
    WHERE cnt.idcontrato = p_idcontrato
      AND cr.estado IN ('Pendiente', 'Vencido')
      AND cr.fechapago < CURDATE()
    
    GROUP BY cnt.idcontrato, cl.tipocliente, pe.nrodoc, e.ruc;
END$$
DELIMITER ;


-- 12) SP PARA VISUALIZAR LAS FECHAS DE LAS CUOTAS VENCIDAS

DROP PROCEDURE IF EXISTS sp_get_detalle_cuotas_vencidas;
DELIMITER $$
CREATE PROCEDURE sp_get_detalle_cuotas_vencidas(IN p_idcontrato INT)
BEGIN
    SELECT 
        cr.numcuota,
        DATE_FORMAT(cr.fechapago, '%d/%m/%Y') AS fecha_formateada,
        cr.fechapago,
        (cr.abonocapital + cr.interes + IFNULL(cr.penalidad, 0)) AS monto,
        DATEDIFF(CURDATE(), cr.fechapago) AS dias_vencido
    FROM cronogramas cr
    WHERE cr.idcontrato = p_idcontrato
      AND cr.estado IN ('Pendiente', 'Vencido')
      AND cr.fechapago < CURDATE()
    ORDER BY cr.fechapago ASC;
END$$
DELIMITER ;


/*
-- ÍNDICES NECESARIOS (ejecutar primero)

USE MOTORPARK;

-- Índices básicos
ALTER TABLE cronogramas ADD INDEX idx_estado_fecha (estado, fechapago);
ALTER TABLE cronogramas ADD INDEX idx_contrato_estado (idcontrato, estado);
ALTER TABLE clientes ADD INDEX idx_idpersona (idpersona);
ALTER TABLE clientes ADD INDEX idx_idempresa (idempresa);
ALTER TABLE vehiculos ADD INDEX idx_idmodelo (idmodelo);
ALTER TABLE modelos ADD INDEX idx_idmarca (idmarca);
ALTER TABLE locales ADD INDEX idx_iddistrito (iddistrito);
ALTER TABLE contratos ADD INDEX idx_estado_fecha (estado, fechainicio);
*/

-- PRUEBAS

-- CALL sp_get_estadisticas_cobranza();
-- CALL sp_get_tarjetas_cobranza();
-- CALL sp_get_cuotas_vencidas();
-- CALL sp_get_cuotas_proximas_vencer();
-- CALL sp_get_datos_reporte_notificacion_pdf(9);
-- CALL sp_get_datos_reporte_recojo_vehicular_pdf(9);

/*
SET profiling = 1;
CALL sp_get_cuotas_vencidas();
SHOW PROFILES;
*/