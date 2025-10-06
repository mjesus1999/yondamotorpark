-- VISTA DE COBRANZA.INDEX

-- 1) ESTADÍSTICAS + RESUMEN POR CONTRATO
 
DROP PROCEDURE IF EXISTS sp_get_estadisticas_cobranza;
DELIMITER $$
CREATE PROCEDURE sp_get_estadisticas_cobranza()
BEGIN
    SELECT
        -- TOTAL DE CONTRATOS CON DEUDAS
        (SELECT COUNT(DISTINCT c2.idcontrato)
         FROM contratos c2
         JOIN cronogramas cr2 ON cr2.idcontrato = c2.idcontrato
         WHERE c2.estado = 'ACT' 
           AND cr2.estado IN ('Pendiente','Vencido')
        ) AS total_deudores,
        
        -- CUOTAS POR VENCER
        (SELECT COUNT(DISTINCT c3.idcontrato)
         FROM contratos c3
         JOIN cronogramas cr3 ON cr3.idcontrato = c3.idcontrato
         WHERE c3.estado = 'ACT'
           AND cr3.estado != 'Pagado'
           -- Cuotas que vencen en 0-3 días
           AND DATEDIFF(cr3.fechapago, CURDATE()) BETWEEN 0 AND 3
           -- Solo la primera cuota no pagada
           AND cr3.fechapago = (
               SELECT MIN(cr_min.fechapago)
               FROM cronogramas cr_min
               WHERE cr_min.idcontrato = c3.idcontrato
                 AND cr_min.estado != 'Pagado'
           )
        ) AS por_vencer_3dias,
        
        -- CUOTAS VENCIDAS
        (SELECT COUNT(DISTINCT c4.idcontrato)
         FROM contratos c4
         JOIN cronogramas cr4 ON cr4.idcontrato = c4.idcontrato
         WHERE c4.estado = 'ACT'
           AND cr4.estado = 'Vencido'
           -- Solo cuotas que YA pasaron su fecha
           AND DATEDIFF(CURDATE(), cr4.fechapago) > 0
           -- Solo la primera cuota vencida
           AND cr4.fechapago = (
               SELECT MIN(cr_min2.fechapago)
               FROM cronogramas cr_min2
               WHERE cr_min2.idcontrato = c4.idcontrato
                 AND cr_min2.estado IN ('Pendiente','Vencido')
           )
        ) AS contratos_con_vencidos,
        
        -- TOTAL POR COBRAR (todas las cuotas pendientes y vencidas)
        (SELECT IFNULL(SUM(cr5.abonocapital + cr5.interes + IFNULL(cr5.penalidad,0)),0)
         FROM contratos c5
         JOIN cronogramas cr5 ON cr5.idcontrato = c5.idcontrato
         WHERE c5.estado = 'ACT'
           AND cr5.estado IN ('Pendiente','Vencido')
        ) AS total_por_cobrar;
    
END$$
DELIMITER ;


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


-- DETALLE DE CONTRATOS (INFORMACION MAS COMPLETA PARA EL MODAL)


-- 3) PROCEMIENTO PARA LA INFORMACION GENERAL DEL CLIENTE CON UN CONTRATO

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
        CONCAT(ma.marca, ' / ', mo.modelo, ' / ', mo.anio) AS vehiculo_descripcion,
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


-- 5) PROCEDIMIENTO PARA MOSTRAR CRONOGRAMA DE PAGOS / Muestra penalidad SOLO si la cuota ya está vencida o fue pagada con penalidad

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
        
        -- PENALIDAD Solo se muestra si:
        -- 1) La cuota está PAGADA y tenía penalidad aplicada
        -- 2) La cuota está VENCIDA
        CASE
            WHEN cr.estado = 'Pagado' AND cr.aplicapenalidad = 'S' 
                THEN IFNULL(cr.penalidad, 0)
            WHEN cr.estado = 'Vencido' AND DATEDIFF(CURDATE(), cr.fechapago) > 0 
                THEN IFNULL(cr.penalidad, 0)
            ELSE 0
        END AS penalidad,
        
        -- Monto total de la cuota
        (cr.abonocapital + cr.interes + 
            CASE
                WHEN cr.estado = 'Pagado' AND cr.aplicapenalidad = 'S' 
                    THEN IFNULL(cr.penalidad, 0)
                WHEN cr.estado = 'Vencido' AND DATEDIFF(CURDATE(), cr.fechapago) > 0 
                    THEN IFNULL(cr.penalidad, 0)
                ELSE 0
            END
        ) AS monto_total_cuota,
        
        cr.saldocapital,
        cr.estado,
        cr.aplicapenalidad,
        
        -- Estado de vencimiento
        CASE
            WHEN cr.estado = 'Pagado' THEN 'PAGADO'
            WHEN DATEDIFF(cr.fechapago, CURDATE()) < 0 THEN 'VENCIDO'
            WHEN DATEDIFF(cr.fechapago, CURDATE()) = 0 THEN 'VENCE HOY'
            WHEN DATEDIFF(cr.fechapago, CURDATE()) BETWEEN 1 AND 3 THEN 'POR VENCER'
            ELSE 'VIGENTE'
        END AS estado_vencimiento,
        
        -- Dias de atraso o días para vencer
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


-- 6) PROCEDIMIENTO PARA MOSTRAR HISTORIAL DE PAGOS

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


-- 7) OBTENER CUOTAS VENCIDAS

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
        (cron.abonocapital + cron.interes + IFNULL(cron.penalidad,0)) AS monto_primera_vencida,
        
        -- Deuda TOTAL de cuotas vencidas
        (SELECT IFNULL(SUM(cr_total.abonocapital + cr_total.interes + IFNULL(cr_total.penalidad,0)), 0)
         FROM cronogramas cr_total
         WHERE cr_total.idcontrato = c.idcontrato
           AND cr_total.estado IN ('Pendiente', 'Vencido')
           AND cr_total.fechapago < CURDATE()
        ) AS deuda_vencida,
        
        -- Cantidad de cuotas vencidas
        (SELECT COUNT(*)
		 FROM cronogramas cr_count
		 WHERE cr_count.idcontrato = c.idcontrato
		   AND cr_count.estado IN ('Pendiente', 'Vencido')
		   AND cr_count.fechapago < CURDATE()
		) AS cuotas_vencidas,
        
        -- Cuotas pagadas del contrato
        (SELECT COUNT(*)
         FROM cronogramas crp
         WHERE crp.idcontrato = c.idcontrato
           AND crp.estado = 'Pagado'
        ) AS cuotas_pagadas,
        
        -- Estado legible: "X vencidas de Y"
        CONCAT(
			(SELECT COUNT(*) 
			 FROM cronogramas cr_v 
			 WHERE cr_v.idcontrato = c.idcontrato 
			   AND cr_v.estado IN ('Pendiente', 'Vencido')
			   AND cr_v.fechapago < CURDATE()
			),
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
        cron.fechapago AS fecha_vencida_mas_antigua,
        
        -- Días de atraso
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
    LEFT JOIN provincias pro ON d.idprovincia = pro.idprovincia
    JOIN cronogramas cron ON cron.idcontrato = c.idcontrato
    
    WHERE c.estado = 'ACT'
      AND cron.estado = 'Vencido'
      -- Solo cuotas que YA vencieron
      AND DATEDIFF(CURDATE(), cron.fechapago) > 0
      -- Solo la primera cuota vencida de cada contrato
      AND cron.fechapago = (
          SELECT MIN(cr_min.fechapago)
          FROM cronogramas cr_min
          WHERE cr_min.idcontrato = c.idcontrato
            AND cr_min.estado IN ('Pendiente', 'Vencido')
      )
    ORDER BY dias_atraso DESC, deuda_vencida DESC;
    
END$$
DELIMITER ;


-- 8) PROCEDIMIENTO PARA OBTENER CUOTAS PRÓXIMAS A VENCER (3 DÍAS ANTES)

/*
-- SOLO PRUEBA
DELIMITER $$

CREATE PROCEDURE sp_get_cuotas_proximas_vencer()
BEGIN
    SELECT 
        con.idcontrato,
        -- Obtener teléfono según tipo de cliente
        CASE 
            WHEN cli.tipocliente = 'P' THEN per.telprimario
            WHEN cli.tipocliente = 'E' THEN emp.telprimario
            ELSE NULL
        END AS telefono,
        
        -- Obtener nombre según tipo de cliente
        CASE 
            WHEN cli.tipocliente = 'P' THEN CONCAT(per.apellidos, ', ', per.nombres)
            WHEN cli.tipocliente = 'E' THEN emp.nombrecomercial
            ELSE 'Cliente desconocido'
        END AS cliente,
        
        veh.modelo AS vehiculo,
        suc.nombre AS local,
        COUNT(cp.idcuota) AS cuotas_pagadas,
        con.cuotas AS cuotas_totales,
        cro.monto AS monto_cuota,
        cro.fechavencimiento AS fecha_vencimiento,
        DATEDIFF(cro.fechavencimiento, CURDATE()) AS dias_para_vencer
    FROM 
        contratos con
    INNER JOIN clientes cli ON con.idcliente = cli.idcliente
    LEFT JOIN personas per ON cli.idpersona = per.idpersona AND cli.tipocliente = 'P'
    LEFT JOIN empresas emp ON cli.idempresa = emp.idempresa AND cli.tipocliente = 'E'
    INNER JOIN vehiculos veh ON con.idvehiculo = veh.idvehiculo
    INNER JOIN sucursales suc ON con.idsucursal = suc.idsucursal
    INNER JOIN cronogramas cro ON con.idcontrato = cro.idcontrato
    LEFT JOIN cuotaspagadas cp ON cro.idcronograma = cp.idcronograma
    WHERE 
        con.estado = 'FIR'
        AND cro.estado = 'PEN'
        AND cro.fechavencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)
    GROUP BY 
        con.idcontrato, cro.idcronograma
    ORDER BY 
        cro.fechavencimiento ASC;
END$$

DELIMITER ;
*/

-- REAL
DROP PROCEDURE IF EXISTS sp_get_cuotas_proximas_vencer;
DELIMITER $$
CREATE PROCEDURE sp_get_cuotas_proximas_vencer()
BEGIN
    SELECT
        c.idcontrato,
        
        -- Información del cliente
        CASE 
            WHEN cli.tipocliente = 'P' THEN CONCAT(p.apellidos, ' ', p.nombres)
            WHEN cli.tipocliente = 'E' THEN e.razonsocial
        END AS cliente,
        
        -- Teléfono
        CASE 
            WHEN cli.tipocliente = 'P' THEN p.telprimario
            WHEN cli.tipocliente = 'E' THEN e.telprimario
        END AS telefono,
        
        -- Local
        CONCAT(d.distrito, ' / ', pro.provincia) AS local,
        
        -- Información del vehículo
        CONCAT(m.marca, ' / ', mo.modelo) AS vehiculo,
        
        -- Estado de vencimiento descriptivo
        CASE
            WHEN DATEDIFF(cron.fechapago, CURDATE()) = 0 THEN 'VENCE HOY'
            WHEN DATEDIFF(cron.fechapago, CURDATE()) = 1 THEN 'Vence mañana'
            WHEN DATEDIFF(cron.fechapago, CURDATE()) = 2 THEN 'Vence en 2 días'
            WHEN DATEDIFF(cron.fechapago, CURDATE()) = 3 THEN 'Vence en 3 días'
            ELSE 'Próximo a vencer'
        END AS estado_vencimiento,
        
        -- Fecha de vencimiento formateada
        DATE_FORMAT(cron.fechapago, '%d/%m/%Y') AS fecha_vencimiento,
        
        -- Número de cuota
        cron.numcuota,
        
        -- Monto de la cuota (sin penalidad porque aún no vencio)
        (cron.abonocapital + cron.interes) AS monto_cuota,
        
        -- Total de cuotas del contrato
        cot.numcuotas AS cuotas_totales,
        
        -- Cuotas pagadas
        (SELECT COUNT(*)
         FROM cronogramas crp
         WHERE crp.idcontrato = c.idcontrato
           AND crp.estado = 'Pagado'
        ) AS cuotas_pagadas,
        
        -- Días para vencer (0, 1, 2 o 3)
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
    JOIN cronogramas cron ON cron.idcontrato = c.idcontrato
    
    WHERE c.estado = 'ACT'
      AND cron.estado != 'Pagado'
      AND DATEDIFF(cron.fechapago, CURDATE()) BETWEEN 0 AND 3
      AND cron.fechapago = (
          SELECT MIN(cr_min.fechapago)
          FROM cronogramas cr_min
          WHERE cr_min.idcontrato = c.idcontrato
            AND cr_min.estado != 'Pagado'
      )
    ORDER BY 
        dias_para_vencer ASC,
        cron.fechapago ASC;
    
END$$
DELIMITER ;


-- 9) ACTUALIZAR TELEFONO
DELIMITER $$

DROP PROCEDURE IF EXISTS sp_actualizar_telefono_cliente$$

CREATE PROCEDURE sp_actualizar_telefono_cliente(
    IN p_idcontrato INT,
    IN p_telefono_nuevo VARCHAR(15),
    IN p_telefono_actual VARCHAR(15)  -- Añadimos el teléfono actual como parámetro
)
BEGIN
    DECLARE v_idpersona INT DEFAULT NULL;
    DECLARE v_idempresa INT DEFAULT NULL;
    DECLARE v_filas_afectadas INT DEFAULT 0;
    DECLARE v_mensaje VARCHAR(255);
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SELECT 
            'error' as status,
            'Error en la base de datos al actualizar el teléfono' as message,
            NULL as telefono_nuevo;
    END;
    
    -- Intentar encontrar la persona por teléfono actual
    SELECT idpersona INTO v_idpersona
    FROM personas
    WHERE telprimario = p_telefono_actual
       OR telalternativo = p_telefono_actual
    LIMIT 1;
    
    IF v_idpersona IS NOT NULL THEN
        -- Actualizar teléfono de persona
        UPDATE personas 
        SET telprimario = p_telefono_nuevo,
            modificado = NOW()
        WHERE idpersona = v_idpersona;
        
        SET v_filas_afectadas = ROW_COUNT();
        
        IF v_filas_afectadas > 0 THEN
            SELECT 
                'success' as status,
                'Teléfono de persona actualizado correctamente' as message,
                p_telefono_nuevo as telefono_nuevo;
        ELSE
            SELECT 
                'error' as status,
                'No se pudo actualizar el teléfono de la persona' as message,
                NULL as telefono_nuevo;
        END IF;
    ELSE
        -- Si no es persona, buscar en empresas
        SELECT idempresa INTO v_idempresa
        FROM empresas
        WHERE telprimario = p_telefono_actual
           OR telsecundario = p_telefono_actual
        LIMIT 1;
        
        IF v_idempresa IS NOT NULL THEN
            -- Actualizar teléfono de empresa
            UPDATE empresas 
            SET telprimario = p_telefono_nuevo
            WHERE idempresa = v_idempresa;
            
            SET v_filas_afectadas = ROW_COUNT();
            
            IF v_filas_afectadas > 0 THEN
                SELECT 
                    'success' as status,
                    'Teléfono de empresa actualizado correctamente' as message,
                    p_telefono_nuevo as telefono_nuevo;
            ELSE
                SELECT 
                    'error' as status,
                    'No se pudo actualizar el teléfono de la empresa' as message,
                    NULL as telefono_nuevo;
            END IF;
        ELSE
            -- No se encontró ni persona ni empresa con ese teléfono
            SELECT 
                'error' as status,
                CONCAT('No se encontró ningún cliente con el teléfono: ', p_telefono_actual) as message,
                NULL as telefono_nuevo;
        END IF;
    END IF;
END$$

DELIMITER ;

-- LLAMADAS DE SP

-- CALL sp_get_estadisticas_cobranza();
-- CALL sp_get_tarjetas_cobranza();
-- CALL sp_get_info_cliente_cobranza(9);
-- CALL sp_get_detalle_contrato_cobranza(9);
-- CALL sp_get_cronograma_pagos_cobranza(9);

-- CALL sp_get_historial_pagos_cobranza(9, 5);

-- CALL sp_get_cuotas_vencidas();
-- CALL sp_get_cuotas_proximas_vencer();