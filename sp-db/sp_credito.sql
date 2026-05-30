-- REFACTORIZACION MODULO DE CREDITO

-- USE motorpark; -- hosting: seleccionar BD en phpMyAdmin

-- 1) ESTADÍSTICAS DE MOROSOS - SEPARADO EN MÚLTIPLES SPs

-- 1.1) Total de morosos y deuda
DROP PROCEDURE IF EXISTS sp_get_estadisticas_morosos_base;
DELIMITER $$
CREATE PROCEDURE sp_get_estadisticas_morosos_base()
BEGIN
    SELECT 
        COUNT(DISTINCT c.idcontrato) AS total_morosos,
        IFNULL(SUM(cot.valorcuota + IFNULL(cro.penalidad, 0)), 0) AS deuda_total,
        IFNULL(AVG(DATEDIFF(CURDATE(), cro.fechapago)), 0) AS dias_promedio
    FROM cronogramas cro
    INNER JOIN contratos c ON cro.idcontrato = c.idcontrato
    INNER JOIN cotizaciones cot ON c.idcotizacion = cot.idcotizacion
    WHERE c.estado = 'ACT'
      AND cro.estado IN ('Vencido', 'Pendiente')
      AND cro.fechapago < CURDATE();
END$$
DELIMITER ;

-- 1.2) Seguimientos de hoy
DROP PROCEDURE IF EXISTS sp_get_seguimientos_hoy;
DELIMITER $$
CREATE PROCEDURE sp_get_seguimientos_hoy()
BEGIN
    SELECT COUNT(*) AS seguimientos_hoy
    FROM seguimientos_morosos 
    WHERE DATE(fecha_seguimiento) = CURDATE();
END$$
DELIMITER ;

-- 2) MOROSOS CLASIFICADOS 

DROP PROCEDURE IF EXISTS sp_actualizar_cuotas_vencidas;
DELIMITER $$
CREATE PROCEDURE sp_actualizar_cuotas_vencidas()
BEGIN
    UPDATE cronogramas cro
    INNER JOIN contratos cont ON cro.idcontrato = cont.idcontrato
    INNER JOIN cotizaciones coti ON cont.idcotizacion = coti.idcotizacion
    SET 
        cro.estado = 'Vencido',
        cro.aplicapenalidad = 'S',
        cro.penalidad = coti.valorcuota * cont.penalidadbase
    WHERE cro.fechapago < CURDATE()
      AND cro.estado != 'Pagado'
      AND cont.estado = 'ACT';
      
    SELECT ROW_COUNT() AS filas_actualizadas;
END$$
DELIMITER ;


--

DROP PROCEDURE IF EXISTS sp_get_morosos_clasificados;
DELIMITER $$
CREATE PROCEDURE sp_get_morosos_clasificados()
BEGIN
    SELECT 
        c.idcontrato,
        CONCAT(p.apellidos, ' ', p.nombres) AS cliente,
        p.tipodoc AS documento,
        p.nrodoc AS ndocumento,
        COALESCE(p.telprimario, p.telalternativo, '') AS telefono,
        
        CASE 
            WHEN p.direccion IS NOT NULL AND TRIM(p.direccion) != '' THEN
                CONCAT(
                    p.direccion,
                    CASE 
                        WHEN p.referencia IS NOT NULL AND TRIM(p.referencia) != '' 
                        THEN CONCAT(' - ', p.referencia)
                        ELSE ''
                    END,
                    CASE 
                        WHEN dp.distrito IS NOT NULL 
                        THEN CONCAT(' / ', dp.distrito)
                        ELSE ''
                    END,
                    CASE 
                        WHEN prop.provincia IS NOT NULL 
                        THEN CONCAT(' / ', prop.provincia)
                        ELSE ''
                    END,
                    CASE 
                        WHEN depp.departamento IS NOT NULL 
                        THEN CONCAT(' / ', depp.departamento)
                        ELSE ''
                    END
                )
            ELSE 'Sin dirección registrada'
        END AS direccion_persona,
        
        CONCAT(
            COALESCE(d.distrito, 'Sin distrito'), ' / ',
            COALESCE(pro.provincia, 'Sin provincia')
        ) AS direccion_local,
        
        CONCAT(
            COALESCE(mar.marca, 'Sin marca'), ' / ',
            COALESCE(model.modelo, 'Sin modelo'), ' / ',
            COALESCE(cb.combustible, 'Sin combustible'), ' / ',
            COALESCE(v.color, 'Sin color')
        ) AS vehiculo,
        
        cro.numcuota,
        DATE_FORMAT(cro.fechapago, '%d/%m/%Y') AS fecha_vencimiento,
        DATEDIFF(CURDATE(), cro.fechapago) AS dias_atraso,
        
        cot.valorcuota + IFNULL(cro.penalidad, 0) AS saldo_pendiente,
        cot.valorcuota AS saldo_cuota,
        IFNULL(cro.penalidad, 0) AS saldo_penalidad,
        
        cro.estado,
        cro.idcronograma
        
    FROM cronogramas cro
    INNER JOIN contratos c ON cro.idcontrato = c.idcontrato
    INNER JOIN cotizaciones cot ON c.idcotizacion = cot.idcotizacion
    INNER JOIN clientes cli ON cot.idcliente = cli.idcliente
    INNER JOIN personas p ON cli.idpersona = p.idpersona
    INNER JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    INNER JOIN modelos model ON v.idmodelo = model.idmodelo
    INNER JOIN marcas mar ON model.idmarca = mar.idmarca
    INNER JOIN combustibles cb ON v.idcombustible = cb.idcombustible
    LEFT JOIN locales l ON c.idlocal = l.idlocal
    LEFT JOIN distritos d ON l.iddistrito = d.iddistrito
    LEFT JOIN provincias pro ON d.idprovincia = pro.idprovincia
    LEFT JOIN departamentos dep ON pro.iddepartamento = dep.iddepartamento
    LEFT JOIN distritos dp ON p.iddistrito = dp.iddistrito
    LEFT JOIN provincias prop ON dp.idprovincia = prop.idprovincia
    LEFT JOIN departamentos depp ON prop.iddepartamento = depp.iddepartamento
    
    WHERE c.estado = 'ACT'
      AND cro.estado IN ('Vencido', 'Pendiente')
      AND cro.fechapago < CURDATE()
      
    ORDER BY dias_atraso DESC, cro.numcuota;
END$$
DELIMITER ;

-- 3) REGISTRAR SEGUIMIENTO

DROP PROCEDURE IF EXISTS sp_registrar_seguimiento_moroso;
DELIMITER $$
CREATE PROCEDURE sp_registrar_seguimiento_moroso(
    IN p_idcontrato INT,
    IN p_tipo ENUM('documento', 'evidencia'),
    IN p_observaciones TEXT,
    IN p_evidencia VARCHAR(255),
    IN p_fecha_seguimiento DATETIME,
    IN p_usuario_registro INT
)
BEGIN
    INSERT INTO seguimientos_morosos (
        idcontrato,
        tipo,
        observaciones,
        evidencia,
        fecha_seguimiento,
        usuario_registro
    ) VALUES (
        p_idcontrato,
        p_tipo,
        p_observaciones,
        p_evidencia,
        p_fecha_seguimiento,
        p_usuario_registro
    );
    
    SELECT LAST_INSERT_ID() AS last_insert_id;
END$$
DELIMITER ;


-- 4) HISTORIAL DE SEGUIMIENTOS

DROP PROCEDURE IF EXISTS sp_get_historial_seguimientos;
DELIMITER $$
CREATE PROCEDURE sp_get_historial_seguimientos(IN p_idcontrato INT)
BEGIN
    SELECT 
        sm.idseguimiento,
        sm.tipo,
        sm.observaciones,
        sm.evidencia,
        DATE_FORMAT(sm.fecha_seguimiento, '%d/%m/%Y %H:%i') AS fecha_seguimiento,
        COALESCE(CONCAT(p.nombres, ' ', p.apellidos), co.usernick) AS usuario_registro,
        sm.created_at
    FROM seguimientos_morosos sm
    LEFT JOIN colaboradores co ON sm.usuario_registro = co.idcolaborador
    LEFT JOIN contratoslaborales cl ON co.idcontratolaboral = cl.idcontratolaboral
    LEFT JOIN personas p ON cl.idpersona = p.idpersona
    WHERE sm.idcontrato = p_idcontrato
    ORDER BY sm.fecha_seguimiento DESC, sm.idseguimiento DESC;
END$$
DELIMITER ;


-- 5) RESUMEN DE MOROSOS

DROP PROCEDURE IF EXISTS getMorososResumen;
DELIMITER $$
CREATE PROCEDURE getMorososResumen()
BEGIN
    SELECT
        cont.idcontrato,
        CONCAT(p.apellidos, ' ', p.nombres) AS cliente,
        p.nrodoc AS ndocumento,
        
        IFNULL(SUM(
            CASE 
                WHEN cro.fechapago < CURDATE() AND cro.estado != 'Pagado'
                THEN coti.valorcuota + IFNULL(cro.penalidad, 0)
                ELSE 0
            END
        ), 0) AS deuda_total,
        
        IFNULL(MAX(
            CASE
                WHEN cro.fechapago < CURDATE() AND cro.estado != 'Pagado' 
                THEN DATEDIFF(CURDATE(), cro.fechapago)
                ELSE 0
            END
        ), 0) AS dias_max_vencido,
        
        IFNULL(SUM(
            CASE
                WHEN cro.fechapago < CURDATE() AND cro.estado != 'Pagado' 
                THEN 1
                ELSE 0
            END
        ), 0) AS cuotas_vencidas,
        
        cont.idlocal
        
    FROM contratos cont
    INNER JOIN cotizaciones coti ON cont.idcotizacion = coti.idcotizacion
    INNER JOIN cronogramas cro ON cro.idcontrato = cont.idcontrato
    INNER JOIN clientes cli ON coti.idcliente = cli.idcliente
    INNER JOIN personas p ON cli.idpersona = p.idpersona
    
    WHERE cont.estado = 'ACT'
    
    GROUP BY cont.idcontrato, p.apellidos, p.nombres, p.nrodoc, cont.idlocal
    
    HAVING deuda_total > 0 OR cuotas_vencidas > 0
    
    ORDER BY dias_max_vencido DESC, deuda_total DESC;
END$$
DELIMITER ;


-- =====================================================
-- PRUEBAS
-- =====================================================

-- CALL sp_get_estadisticas_morosos_base();
-- CALL sp_get_seguimientos_hoy();
-- CALL sp_actualizar_cuotas_vencidas();
-- CALL sp_get_morosos_clasificados();
-- CALL sp_get_historial_seguimientos(1);
-- CALL getMorososResumen();



/*
-- USE motorpark; -- hosting: seleccionar BD en phpMyAdmin

-- Índice compuesto para pagos (MUY IMPORTANTE)
ALTER TABLE pagos ADD INDEX idx_cronograma_tipo (idcronograma, tipo);
ALTER TABLE pagos ADD INDEX idx_cronograma (idcronograma);

-- Índice para contratos
ALTER TABLE contratos ADD INDEX idx_estado (estado);

-- Índices para cronogramas
ALTER TABLE cronogramas ADD INDEX idx_contrato_estado (idcontrato, estado);
ALTER TABLE cronogramas ADD INDEX idx_fechapago (fechapago);

-- Índices para seguimientos_morosos
ALTER TABLE seguimientos_morosos ADD INDEX idx_contrato_fecha (idcontrato, fecha_seguimiento);
ALTER TABLE seguimientos_morosos ADD INDEX idx_fecha_seguimiento (fecha_seguimiento);
*/





-- USE motorpark; -- hosting: seleccionar BD en phpMyAdmin


SELECT * FROM vehiculos WHERE idvehiculo = 75;