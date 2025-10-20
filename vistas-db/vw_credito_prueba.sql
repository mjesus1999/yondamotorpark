-- =====================================================
-- STORED PROCEDURES MOROSOS OPTIMIZADOS
-- Sin subconsultas ni tablas temporales
-- Solo JOINs directos con agregaciones
-- =====================================================

USE motorpark;

-- =====================================================
-- VISTA: Totales de pagos por cronograma
-- =====================================================
CREATE OR REPLACE VIEW v_pagos_totales AS
SELECT 
    idcronograma,
    SUM(amortizacion) AS total_general,
    SUM(CASE WHEN tipo = 'Cuota' THEN amortizacion ELSE 0 END) AS total_cuota,
    SUM(CASE WHEN tipo = 'Penalidad' THEN amortizacion ELSE 0 END) AS total_penalidad
FROM pagos
GROUP BY idcronograma;


-- =====================================================
-- 1) ESTADÍSTICAS DE MOROSOS
-- =====================================================
DROP PROCEDURE IF EXISTS sp_get_estadisticas_morosos;
DELIMITER $$
CREATE PROCEDURE sp_get_estadisticas_morosos()
BEGIN
    SELECT 
        COUNT(DISTINCT c.idcontrato) AS total_morosos,
        COALESCE(SUM((cot.valorcuota + cro.penalidad) - IFNULL(vp.total_general, 0)), 0) AS deuda_total,
        COUNT(DISTINCT CASE 
            WHEN DATE(sm.fecha_seguimiento) = CURDATE() 
            THEN sm.idseguimiento 
        END) AS seguimientos_hoy,
        COALESCE(AVG(DATEDIFF(CURDATE(), cro.fechapago)), 0) AS dias_promedio
        
    FROM cronogramas cro
    INNER JOIN contratos c ON cro.idcontrato = c.idcontrato
    INNER JOIN cotizaciones cot ON c.idcotizacion = cot.idcotizacion
    LEFT JOIN v_pagos_totales vp ON vp.idcronograma = cro.idcronograma
    LEFT JOIN seguimientos_morosos sm ON sm.idcontrato = c.idcontrato
    
    WHERE c.estado = 'ACT'
      AND c.escredito = 1
      AND cro.estado IN ('Vencido', 'Pendiente')
      AND cro.fechapago < CURDATE()
      AND (cot.valorcuota + cro.penalidad) > IFNULL(vp.total_general, 0);
END$$
DELIMITER ;


-- =====================================================
-- 2) MOROSOS CLASIFICADOS
-- =====================================================
DROP PROCEDURE IF EXISTS sp_get_morosos_clasificados;
DELIMITER $$
CREATE PROCEDURE sp_get_morosos_clasificados()
BEGIN
    -- Actualización de cuotas vencidas
    UPDATE cronogramas cro
    INNER JOIN contratos cont ON cro.idcontrato = cont.idcontrato
    INNER JOIN cotizaciones coti ON cont.idcotizacion = coti.idcotizacion
    SET 
        cro.estado = 'Vencido',
        cro.aplicapenalidad = 'S',
        cro.penalidad = coti.valorcuota * cont.penalidadbase
    WHERE cro.fechapago < CURDATE()
      AND cro.estado != 'Pagado'
      AND cont.estado = 'ACT'
      AND cont.escredito = 1;

    -- Consulta principal usando la vista
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
                    CASE WHEN p.referencia IS NOT NULL AND TRIM(p.referencia) != '' 
                        THEN CONCAT(' - ', p.referencia) ELSE '' END,
                    CASE WHEN dp.distrito IS NOT NULL 
                        THEN CONCAT(' / ', dp.distrito) ELSE '' END,
                    CASE WHEN prop.provincia IS NOT NULL 
                        THEN CONCAT(' / ', prop.provincia) ELSE '' END,
                    CASE WHEN depp.departamento IS NOT NULL 
                        THEN CONCAT(' / ', depp.departamento) ELSE '' END
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
        
        (cot.valorcuota + cro.penalidad - IFNULL(vp.total_general, 0)) AS saldo_pendiente,
        (cot.valorcuota - IFNULL(vp.total_cuota, 0)) AS saldo_cuota,
        (cro.penalidad - IFNULL(vp.total_penalidad, 0)) AS saldo_penalidad,
        
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
    LEFT JOIN v_pagos_totales vp ON vp.idcronograma = cro.idcronograma
    
    WHERE c.estado = 'ACT'
      AND c.escredito = 1
      AND cro.estado IN ('Vencido', 'Pendiente')
      AND cro.fechapago < CURDATE()
      AND (
          cot.valorcuota > IFNULL(vp.total_cuota, 0)
          OR cro.penalidad > IFNULL(vp.total_penalidad, 0)
      )
    ORDER BY dias_atraso DESC, cro.numcuota;
END$$
DELIMITER ;


-- =====================================================
-- 3) REGISTRAR SEGUIMIENTO
-- =====================================================
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
        idcontrato, tipo, observaciones, evidencia, 
        fecha_seguimiento, usuario_registro
    ) VALUES (
        p_idcontrato, p_tipo, p_observaciones, p_evidencia,
        p_fecha_seguimiento, p_usuario_registro
    );
    
    SELECT LAST_INSERT_ID() AS last_insert_id;
END$$
DELIMITER ;


-- =====================================================
-- 4) HISTORIAL DE SEGUIMIENTOS
-- =====================================================
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


-- =====================================================
-- 5) RESUMEN DE MOROSOS
-- =====================================================
DROP PROCEDURE IF EXISTS getMorososResumen;
DELIMITER $$
CREATE PROCEDURE getMorososResumen()
BEGIN
    SELECT
        cont.idcontrato,
        CONCAT(p.apellidos, ' ', p.nombres) AS cliente,
        p.nrodoc AS ndocumento,
        COALESCE(SUM((coti.valorcuota + cro.penalidad) - IFNULL(vp.total_general, 0)), 0) AS deuda_total,
        COALESCE(MAX(CASE
            WHEN cro.fechapago < CURDATE() AND cro.estado != 'Pagado' 
            THEN DATEDIFF(CURDATE(), cro.fechapago)
            ELSE 0
        END), 0) AS dias_max_vencido,
        COALESCE(SUM(CASE
            WHEN cro.fechapago < CURDATE() AND cro.estado != 'Pagado' 
            THEN 1 ELSE 0
        END), 0) AS cuotas_vencidas,
        cont.idlocal
        
    FROM contratos cont
    INNER JOIN cotizaciones coti ON cont.idcotizacion = coti.idcotizacion
    INNER JOIN cronogramas cro ON cro.idcontrato = cont.idcontrato
    INNER JOIN clientes cli ON coti.idcliente = cli.idcliente
    INNER JOIN personas p ON cli.idpersona = p.idpersona
    LEFT JOIN v_pagos_totales vp ON vp.idcronograma = cro.idcronograma
    
    WHERE cont.estado = 'ACT'
      AND cont.escredito = 1
    GROUP BY cont.idcontrato, p.apellidos, p.nombres, p.nrodoc, cont.idlocal
    HAVING deuda_total > 0 OR cuotas_vencidas > 0
    ORDER BY dias_max_vencido DESC, deuda_total DESC;
END$$
DELIMITER ;


-- =====================================================
-- ÍNDICES RECOMENDADOS
-- =====================================================
/*
ALTER TABLE cronogramas ADD INDEX idx_estado_fecha (estado, fechapago);
ALTER TABLE cronogramas ADD INDEX idx_contrato_estado (idcontrato, estado);
ALTER TABLE pagos ADD INDEX idx_cronograma_tipo (idcronograma, tipo);
ALTER TABLE contratos ADD INDEX idx_estado_credito (estado, escredito);
ALTER TABLE seguimientos_morosos ADD INDEX idx_contrato_fecha (idcontrato, fecha_seguimiento);
*/

-- =====================================================
-- PRUEBAS
-- =====================================================
-- CALL sp_get_estadisticas_morosos();
-- CALL sp_get_morosos_clasificados();
-- CALL getMorososResumen();
-- CALL sp_get_historial_seguimientos(1);