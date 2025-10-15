SELECT * FROM PERSONAS;

-- Verificar si Miguel Angel ya es cliente
SELECT c.idcliente, c.tipocliente, p.nombres, p.apellidos 
FROM clientes c
INNER JOIN personas p ON c.idpersona = p.idpersona
WHERE p.idpersona = 19;

-- Consulta corregida para ver la cotización 57
SELECT 
    cot.idcotizacion,
    cot.idcliente,
    cot.idvehiculo,
    cot.moneda,
    cot.precioventa,
    cot.inicial,
    cot.numcuotas,
    cot.valorcuota,
    cot.gastosadministrativos,
    cot.estadocotizacion,
    CONCAT(p.nombres, ' ', p.apellidos) as cliente,
    CONCAT(m.marca, ' ', mo.modelo, ' ', mo.anio) as vehiculo,
    tv.tipovehiculo
FROM cotizaciones cot
INNER JOIN clientes cl ON cot.idcliente = cl.idcliente
INNER JOIN personas p ON cl.idpersona = p.idpersona
INNER JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
INNER JOIN modelos mo ON v.idmodelo = mo.idmodelo
INNER JOIN marcas m ON mo.idmarca = m.idmarca
INNER JOIN tipovehiculos tv ON mo.idtipovehiculo = tv.idtipovehiculo
WHERE cot.idcotizacion = 58;

-- 1. Ver locales disponibles
SELECT idlocal, tienda, responsable FROM locales LIMIT 5;

-- 2. Crear el contrato (ajusta idlocal según el resultado anterior)
INSERT INTO contratos (
    idlocal,
    idcotizacion,
    fechainicio,
    diapago,
    escredito,
    penalidadbase,
    observaciones,
    estado
) VALUES (
    1,                      -- Ajusta este ID según tu local
    58,                     
    '2025-07-16',           
    15,                     
    'S',                    
    0.10,                   
    'Contrato de prueba X4',
    'ACT'
);

-- 3. Ver el ID del contrato creado
SELECT LAST_INSERT_ID() as nuevo_idcontrato;

-- 4. Verificar el contrato creado
SELECT 
    con.*,
    CONCAT(p.nombres, ' ', p.apellidos) as cliente,
    l.tienda,
    cot.precioventa,
    cot.numcuotas,
    cot.valorcuota
FROM contratos con
INNER JOIN cotizaciones cot ON con.idcotizacion = cot.idcotizacion
INNER JOIN clientes cl ON cot.idcliente = cl.idcliente
INNER JOIN personas p ON cl.idpersona = p.idpersona
INNER JOIN locales l ON con.idlocal = l.idlocal
WHERE con.idcotizacion = 58;

SELECT COUNT(*) as total_cuotas 
FROM cronogramas 
WHERE idcontrato = 9;

SELECT 
    numcuota,
    DATE_FORMAT(fechapago, '%d/%m/%Y') as fecha_pago,
    abonocapital,
    interes,
    penalidad,
    (abonocapital + interes + penalidad) as cuota_total,
    saldocapital,
    estado
FROM cronogramas
WHERE idcontrato = 9
ORDER BY numcuota;

-- Probar el SP de cronograma de cobranza con el nuevo contrato
CALL sp_get_cronograma_pagos_cobranza(9);

CALL generar_cronograma(9, 4.263224089);

-- 1. Actualizar la fecha de inicio del contrato
UPDATE contratos 
SET fechainicio = '2025-07-15'
WHERE idcontrato = 9;

-- 2. Eliminar el cronograma actual
DELETE FROM cronogramas WHERE idcontrato = 9;

-- 3. Regenerar el cronograma con la nueva fecha
CALL generar_cronograma(9, 4.263224089);

-- 4. Verificar que ahora comienza en 15/10/2025 (primer mes después del inicio)
SELECT 
    numcuota,
    DATE_FORMAT(fechapago, '%d/%m/%Y') as fecha,
    ROUND(abonocapital, 2) as capital,
    ROUND(interes, 2) as interes,
    ROUND(abonocapital + interes, 2) as cuota,
    ROUND(saldocapital, 2) as saldo
FROM cronogramas
WHERE idcontrato = 9
ORDER BY numcuota;

DELETE FROM pagos 
WHERE idcronograma IN (
    SELECT idcronograma FROM cronogramas WHERE idcontrato = 9
);

-- Luego sí puedes eliminar el cronograma:
DELETE FROM cronogramas WHERE idcontrato = 9;
DELETE FROM pagos
WHERE idpago IN (
    SELECT idpago FROM (
        SELECT p.idpago
        FROM pagos p
        JOIN cronogramas c ON p.idcronograma = c.idcronograma
        WHERE c.idcontrato = 9
    ) AS sub
);

CALL generar_cronograma(9, 4.263224089);

SELECT 
    numcuota,
    DATE_FORMAT(fechapago, '%d/%m/%Y') AS fecha_pago,
    ROUND(abonocapital, 2) AS capital,
    ROUND(interes, 2) AS interes,
    ROUND(abonocapital + interes, 2) AS cuota_total,
    ROUND(saldocapital, 2) AS saldo,
    estado
FROM cronogramas
WHERE idcontrato = 9
ORDER BY numcuota;

SELECT * FROM contratos WHERE idcontrato = 10; -- Debe retornar vacío
SELECT * FROM cronogramas WHERE idcontrato = 10; -- También vacío


DELETE FROM contratos
WHERE idcontrato = 10;


DELETE FROM cronogramas
WHERE idcontrato = 10;


DELETE FROM pagos
WHERE idcronograma IN (
    SELECT idcronograma FROM cronogramas WHERE idcontrato = 10
);

-- **********************************************************************
SELECT 
    idcronograma,
    numcuota,
    fechapago,
    estado,
    aplicapenalidad,
    penalidad,
    abonocapital + interes AS cuota_base,
    penalidad AS penalidad_guardada
FROM cronogramas
WHERE idcontrato = 9
  AND numcuota IN (1, 2);

/*
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
        
        -- Penalidad SOLO si ya pasó la fecha de pago
        CASE
            WHEN cr.estado = 'Pagado' THEN 0
            WHEN DATEDIFF(CURDATE(), cr.fechapago) > 0 AND cr.aplicapenalidad = 'S' THEN IFNULL(cr.penalidad, 0)
            ELSE 0
        END AS penalidad,
        
        -- Monto total de la cuota
        (cr.abonocapital + cr.interes + 
            CASE
                WHEN cr.estado = 'Pagado' THEN 0
                WHEN DATEDIFF(CURDATE(), cr.fechapago) > 0 AND cr.aplicapenalidad = 'S' THEN IFNULL(cr.penalidad, 0)
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
