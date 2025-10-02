-- ========================================
-- SCRIPT DE PRUEBA PARA NOTIFICACIONES DE 3 DÍAS
-- ========================================

USE motorpark;

-- ========================================
-- PASO 1: VERIFICAR IDS EXISTENTES
-- ========================================

-- Ver los contratos activos actuales
SELECT 
    c.idcontrato,
    c.idcotizacion,
    CONCAT(p.apellidos, ' ', p.nombres) AS cliente
FROM contratos c
INNER JOIN cotizaciones cot ON c.idcotizacion = cot.idcotizacion
INNER JOIN clientes cli ON cot.idcliente = cli.idcliente
INNER JOIN personas p ON cli.idpersona = p.idpersona
WHERE c.estado = 'ACT'
LIMIT 5;

-- ========================================
-- PASO 2: ACTUALIZAR FECHAS DE CRONOGRAMAS EXISTENTES PARA PRUEBA
-- ========================================

-- IMPORTANTE: Guarda las fechas originales primero para poder restaurarlas después
CREATE TEMPORARY TABLE IF NOT EXISTS temp_fechas_backup AS
SELECT 
    idcronograma,
    fechapago,
    estado
FROM cronogramas
WHERE idcontrato IN (1, 2, 3,4);

SELECT 'Backup realizado' AS mensaje, COUNT(*) AS registros_respaldados
FROM temp_fechas_backup;

SELECT * FROM contratos;
SELECT * FROM cronogramas;

-- ========================================
-- PASO 3: MODIFICAR FECHAS PARA LA PRUEBA
-- ========================================

UPDATE cronogramas 
SET fechapago = DATE_ADD(CURDATE(), INTERVAL 3 DAY), estado = 'Pendiente'
WHERE idcontrato IN (SELECT idcontrato FROM contratos WHERE estado = 'ACT')
  AND numcuota = 1
  AND estado <> 'Pagado';

UPDATE cronogramas 
SET fechapago = DATE_ADD(CURDATE(), INTERVAL 3 DAY), estado = 'Pendiente'
WHERE idcontrato IN (SELECT idcontrato FROM contratos WHERE estado = 'ACT')
  AND numcuota = 2
  AND estado <> 'Pagado';

UPDATE cronogramas 
SET fechapago = DATE_ADD(CURDATE(), INTERVAL 3 DAY), estado = 'Pendiente'
WHERE idcontrato IN (SELECT idcontrato FROM contratos WHERE estado = 'ACT')
  AND numcuota = 3
  AND estado <> 'Pagado';

UPDATE cronogramas 
SET fechapago = DATE_ADD(CURDATE(), INTERVAL 3 DAY), estado = 'Pendiente'
WHERE idcontrato IN (SELECT idcontrato FROM contratos WHERE estado = 'ACT')
  AND numcuota = 4
  AND estado <> 'Pagado';

UPDATE cronogramas 
SET fechapago = DATE_ADD(CURDATE(), INTERVAL 3 DAY), estado = 'Pendiente'
WHERE idcontrato IN (SELECT idcontrato FROM contratos WHERE estado = 'ACT')
  AND numcuota = 5
  AND estado <> 'Pagado';

UPDATE cronogramas 
SET fechapago = DATE_ADD(CURDATE(), INTERVAL 3 DAY), estado = 'Pendiente'
WHERE idcontrato IN (SELECT idcontrato FROM contratos WHERE estado = 'ACT')
  AND numcuota = 6
  AND estado <> 'Pagado';

UPDATE cronogramas 
SET fechapago = DATE_ADD(CURDATE(), INTERVAL 3 DAY), estado = 'Pendiente'
WHERE idcontrato IN (SELECT idcontrato FROM contratos WHERE estado = 'ACT')
  AND numcuota = 7
  AND estado <> 'Pagado';


/*
-- Escenario 1: Vence en 3 días
UPDATE cronogramas 
SET fechapago = DATE_ADD(CURDATE(), INTERVAL 3 DAY), estado = 'Pendiente'
WHERE idcontrato IN (SELECT idcontrato FROM contratos WHERE estado = 'ACT')
  AND numcuota = 1;

-- Escenario 2: Vence en 2 días
UPDATE cronogramas 
SET fechapago = DATE_ADD(CURDATE(), INTERVAL 2 DAY), estado = 'Pendiente'
WHERE idcontrato IN (SELECT idcontrato FROM contratos WHERE estado = 'ACT')
  AND numcuota = 2;

-- Escenario 3: Vence mañana
UPDATE cronogramas 
SET fechapago = DATE_ADD(CURDATE(), INTERVAL 1 DAY), estado = 'Pendiente'
WHERE idcontrato IN (SELECT idcontrato FROM contratos WHERE estado = 'ACT')
  AND numcuota = 3;

-- Escenario 4: Vence HOY
UPDATE cronogramas 
SET fechapago = CURDATE(), estado = 'Pendiente'
WHERE idcontrato IN (SELECT idcontrato FROM contratos WHERE estado = 'ACT')
  AND numcuota = 4;

-- Escenario 5: Vencido hace 1 día
UPDATE cronogramas 
SET fechapago = DATE_SUB(CURDATE(), INTERVAL 1 DAY), estado = 'Vencido'
WHERE idcontrato IN (SELECT idcontrato FROM contratos WHERE estado = 'ACT')
  AND numcuota = 5;

-- Escenario 6: Vencido hace 2 días
UPDATE cronogramas 
SET fechapago = DATE_SUB(CURDATE(), INTERVAL 2 DAY), estado = 'Vencido'
WHERE idcontrato IN (SELECT idcontrato FROM contratos WHERE estado = 'ACT')
  AND numcuota = 6;

-- Escenario 7: Vencido hace 3 días
UPDATE cronogramas 
SET fechapago = DATE_SUB(CURDATE(), INTERVAL 3 DAY), estado = 'Vencido'
WHERE idcontrato IN (SELECT idcontrato FROM contratos WHERE estado = 'ACT')
  AND numcuota = 7;

SELECT 'Datos de prueba creados' AS resultado;
*/

-- ========================================
-- VERIFICACIÓN
-- ========================================
SELECT 
    DATEDIFF(CURDATE(), cro.fechapago) AS dias_diferencia,
    COUNT(*) AS cantidad,
    GROUP_CONCAT(cro.numcuota ORDER BY cro.numcuota) AS cuotas
FROM cronogramas cro
INNER JOIN contratos c ON cro.idcontrato = c.idcontrato
WHERE c.estado = 'ACT'
  AND DATEDIFF(CURDATE(), cro.fechapago) BETWEEN -3 AND 3
GROUP BY DATEDIFF(CURDATE(), cro.fechapago)
ORDER BY dias_diferencia;

-- ========================================
-- PASO 4: VERIFICAR LOS DATOS DE PRUEBA
-- ========================================

SELECT 
    'Verificación de datos de prueba' AS info,
    DATEDIFF(CURDATE(), cro.fechapago) AS dias_diferencia,
    COUNT(*) AS cantidad
FROM cronogramas cro
INNER JOIN contratos c ON cro.idcontrato = c.idcontrato
WHERE c.estado = 'ACT'
  AND cro.estado IN ('Pendiente', 'Vencido')
  AND DATEDIFF(CURDATE(), cro.fechapago) BETWEEN -3 AND 3
GROUP BY DATEDIFF(CURDATE(), cro.fechapago)
ORDER BY dias_diferencia;

-- ========================================
-- PASO 5: EJECUTAR LA CONSULTA DE NOTIFICACIONES
-- ========================================

SELECT 
    c.idcontrato,
    CONCAT(p.apellidos, ' ', p.nombres) AS cliente,
    p.tipodoc AS documento,
    p.nrodoc AS ndocumento,
    COALESCE(p.telprimario, p.telalternativo, '') AS telefono,
    
    cro.numcuota,
    DATE_FORMAT(cro.fechapago, '%d/%m/%Y') AS fecha_vencimiento,
    DATEDIFF(CURDATE(), cro.fechapago) AS dias_diferencia,
    
    -- Tipo de notificación
    CASE 
        WHEN DATEDIFF(CURDATE(), cro.fechapago) BETWEEN -3 AND -1 THEN 'RECORDATORIO DE PAGO'
        WHEN DATEDIFF(CURDATE(), cro.fechapago) = 0 THEN 'VENCE HOY'
        WHEN DATEDIFF(CURDATE(), cro.fechapago) BETWEEN 1 AND 3 THEN 'NOTIFICACIÓN DE VENCIMIENTO'
        ELSE 'FUERA DE RANGO'
    END AS tipo_notificacion,
    
    cot.valorcuota AS valor_cuota,
    
    -- Saldo pendiente
    cot.valorcuota - COALESCE((
        SELECT SUM(pag.amortizacion)
        FROM pagos pag
        WHERE pag.idcronograma = cro.idcronograma
        AND pag.tipo = 'Cuota'
    ), 0) AS saldo_pendiente_cuota,
    
    cro.estado,
    cro.idcronograma,
    
    -- Prioridad
    CASE 
        WHEN DATEDIFF(CURDATE(), cro.fechapago) = 0 THEN 'ALTA - Vence Hoy'
        WHEN DATEDIFF(CURDATE(), cro.fechapago) BETWEEN 1 AND 3 THEN 'ALTA - Vencido sin penalidad'
        WHEN DATEDIFF(CURDATE(), cro.fechapago) = -1 THEN 'MEDIA - Vence mañana'
        WHEN DATEDIFF(CURDATE(), cro.fechapago) BETWEEN -3 AND -2 THEN 'BAJA - Recordatorio'
        ELSE 'SIN PRIORIDAD'
    END AS prioridad
    
FROM cronogramas cro
INNER JOIN contratos c ON cro.idcontrato = c.idcontrato
INNER JOIN cotizaciones cot ON c.idcotizacion = cot.idcotizacion
INNER JOIN clientes cli ON cot.idcliente = cli.idcliente
INNER JOIN personas p ON cli.idpersona = p.idpersona

WHERE c.estado = 'ACT'
  AND cro.estado IN ('Pendiente', 'Vencido')
  AND DATEDIFF(CURDATE(), cro.fechapago) BETWEEN -3 AND 3
  
  -- Solo cuotas con saldo pendiente
  AND cot.valorcuota > COALESCE((
      SELECT SUM(pag.amortizacion)
      FROM pagos pag
      WHERE pag.idcronograma = cro.idcronograma
      AND pag.tipo = 'Cuota'
  ), 0)

ORDER BY 
    CASE WHEN DATEDIFF(CURDATE(), cro.fechapago) >= 0 THEN 0 ELSE 1 END,
    ABS(DATEDIFF(CURDATE(), cro.fechapago)),
    cro.numcuota;

-- ========================================
-- PASO 6: RESTAURAR LOS DATOS ORIGINALES (EJECUTAR DESPUÉS DE LA PRUEBA)
-- ========================================

-- IMPORTANTE: Ejecuta esto cuando termines las pruebas para volver a los datos originales

/*
UPDATE cronogramas cro
INNER JOIN temp_fechas_backup tfb ON cro.idcronograma = tfb.idcronograma
SET cro.fechapago = tfb.fechapago,
    cro.estado = tfb.estado;

SELECT 'Datos restaurados' AS mensaje, COUNT(*) AS registros_restaurados
FROM temp_fechas_backup;

DROP TEMPORARY TABLE IF EXISTS temp_fechas_backup;
*/

-- ========================================
-- RESUMEN DE LA PRUEBA
-- ========================================

SELECT 
    '=== RESUMEN DE PRUEBA ===' AS resumen,
    '' AS detalle
UNION ALL
SELECT 
    'Escenarios creados' AS resumen,
    'Cuotas en rango de ±3 días' AS detalle
UNION ALL
SELECT 
    'Fecha actual' AS resumen,
    DATE_FORMAT(CURDATE(), '%d/%m/%Y %H:%i:%s') AS detalle
UNION ALL
SELECT 
    'Registros de prueba' AS resumen,
    CAST(COUNT(*) AS CHAR) AS detalle
FROM cronogramas cro
INNER JOIN contratos c ON cro.idcontrato = c.idcontrato
WHERE c.estado = 'ACT'
  AND DATEDIFF(CURDATE(), cro.fechapago) BETWEEN -3 AND 3;
  
  
/*SCRIPT CORRECTO*/
/*
-- DROP VIEW vw_cuotas_proximas_vencer;
CREATE VIEW vw_cuotas_proximas_vencer AS
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
*/