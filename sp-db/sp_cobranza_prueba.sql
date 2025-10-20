CALL sp_get_datos_reporte_recojo_vehicular_pdf(9);
-- CALL sp_get_datos_reporte_notificacion_pdf(8););

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
        
        -- Detalle de cuotas vencidas con formato especial
        GROUP_CONCAT(
            DISTINCT
            CASE
                -- Solo tiene penalidad pendiente (la cuota ya fue pagada)
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
            SEPARATOR '||'
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
        
        -- Detalle de cuotas vencidas con formato especial
        GROUP_CONCAT(
            DISTINCT
            CASE
                -- Solo tiene penalidad pendiente (la cuota ya fue pagada)
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
            SEPARATOR '||'
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


-- ACTUALIZAR EL ID = 7
UPDATE contratos
SET diapago = DAY(fechainicio)
WHERE idcontrato = 7;

-- ACTUALIZAR TELEFONO

-- 9) ACTUALIZAR TELÉFONO (VERSION ULTRA COMPACTA)
DROP PROCEDURE IF EXISTS sp_actualizar_telefono_cliente;
DELIMITER $$
CREATE PROCEDURE sp_actualizar_telefono_cliente(
    IN p_idcontrato INT,
    IN p_telefono_nuevo VARCHAR(15),
    IN p_telefono_actual VARCHAR(15)
)
BEGIN
    DECLARE v_idpersona INT DEFAULT NULL;
    DECLARE v_idempresa INT DEFAULT NULL;
    DECLARE v_tipocliente CHAR(1) DEFAULT NULL;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 
            'error' AS status,
            'Error en la base de datos al actualizar el teléfono' AS message,
            NULL AS telefono_nuevo;
    END;
    
    START TRANSACTION;
    
    -- Obtener y actualizar en una sola operación
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
    
    -- Validaciones y actualización
    IF v_tipocliente IS NULL THEN
        ROLLBACK;
        SELECT 
            'error' AS status,
            CONCAT('No se encontró el contrato: ', p_idcontrato) AS message,
            NULL AS telefono_nuevo;
    
    ELSEIF v_tipocliente = 'P' THEN
        UPDATE personas 
        SET telprimario = p_telefono_nuevo, modificado = NOW()
        WHERE idpersona = v_idpersona;
        
        IF ROW_COUNT() > 0 THEN
            COMMIT;
            SELECT 'success' AS status, 'Teléfono actualizado correctamente' AS message, p_telefono_nuevo AS telefono_nuevo;
        ELSE
            ROLLBACK;
            SELECT 'error' AS status, 'No se pudo actualizar el teléfono' AS message, NULL AS telefono_nuevo;
        END IF;
    
    ELSEIF v_tipocliente = 'E' THEN
        UPDATE empresas 
        SET telprimario = p_telefono_nuevo
        WHERE idempresa = v_idempresa;
        
        IF ROW_COUNT() > 0 THEN
            COMMIT;
            SELECT 'success' AS status, 'Teléfono actualizado correctamente' AS message, p_telefono_nuevo AS telefono_nuevo;
        ELSE
            ROLLBACK;
            SELECT 'error' AS status, 'No se pudo actualizar el teléfono' AS message, NULL AS telefono_nuevo;
        END IF;
    
    ELSE
        ROLLBACK;
        SELECT 'error' AS status, 'Tipo de cliente no válido' AS message, NULL AS telefono_nuevo;
    END IF;
END$$
DELIMITER ;


-- Actualizacion de dia de pago de 15 -> 16 (contrato N° 9)
-- Se cambio porque queria probar la fecha de contrato con la fecha de pago => si resulto

/*
UPDATE contratos 
SET diapago = 16 
WHERE idcontrato = 9;

-- CORREGIDO: sp_get_datos_reporte_notificacion_pdf
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
        
        -- DATOS DEL ASESOR (quien hizo la cotización original)
        CONCAT(pase.apellidos, ' ', pase.nombres) AS colaborador_nombre,
        UPPER(CONCAT(pase.nombres, ' ', pase.apellidos)) AS colaborador_nombre_completo,
        ar.area AS colaborador_area,
        cg.cargo AS colaborador_cargo,
        pase.telprimario AS colaborador_telefono,
        pase.telalternativo AS colaborador_telefono_alt,
        col.usernick AS colaborador_usuario,
        
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
    
    -- JOIN para obtener el ASESOR y su ÁREA
    LEFT JOIN colaboradores col ON cot.idasesor = col.idcolaborador
    LEFT JOIN contratoslaborales cl_ase ON col.idcontratolaboral = cl_ase.idcontratolaboral
    LEFT JOIN personas pase ON cl_ase.idpersona = pase.idpersona
    LEFT JOIN cargos cg ON cl_ase.idcargo = cg.idcargo
    LEFT JOIN areas ar ON cg.idarea = ar.idarea

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


-- CORREGIDO: sp_get_datos_reporte_recojo_vehicular_pdf
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
        
        -- DATOS DEL ASESOR y su ÁREA
        CONCAT(pase.apellidos, ' ', pase.nombres) AS colaborador_nombre,
        UPPER(CONCAT(pase.nombres, ' ', pase.apellidos)) AS colaborador_nombre_completo,
        ar.area AS colaborador_area,
        cg.cargo AS colaborador_cargo,
        pase.telprimario AS colaborador_telefono,
        pase.telalternativo AS colaborador_telefono_alt,
        col.usernick AS colaborador_usuario,
        
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
    
    -- JOIN para obtener el ASESOR y su ÁREA
    LEFT JOIN colaboradores col ON cot.idasesor = col.idcolaborador
    LEFT JOIN contratoslaborales cl_ase ON col.idcontratolaboral = cl_ase.idcontratolaboral
    LEFT JOIN personas pase ON cl_ase.idpersona = pase.idpersona
    LEFT JOIN cargos cg ON cl_ase.idcargo = cg.idcargo
    LEFT JOIN areas ar ON cg.idarea = ar.idarea

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
DELIMITER ;*/


