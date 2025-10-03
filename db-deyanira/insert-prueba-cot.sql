-- Verificar si Miguel Angel ya es cliente
SELECT c.idcliente, c.tipocliente, p.nombres, p.apellidos 
FROM clientes c
INNER JOIN personas p ON c.idpersona = p.idpersona
WHERE p.idpersona = 21;

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
WHERE cot.idcotizacion = 57;

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
    57,                     
    '2025-10-01',           
    15,                     
    'S',                    
    0.10,                   
    'Contrato de prueba - Miguel Angel Arias Anton',
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
WHERE con.idcotizacion = 57;

SELECT COUNT(*) as total_cuotas 
FROM cronogramas 
WHERE idcontrato = 8;

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
WHERE idcontrato = 8
ORDER BY numcuota;

-- Probar el SP de cronograma de cobranza con el nuevo contrato
CALL sp_get_cronograma_pagos_cobranza(8);



-- 1. Actualizar la fecha de inicio del contrato
UPDATE contratos 
SET fechainicio = '2025-08-15'
WHERE idcontrato = 8;

-- 2. Eliminar el cronograma actual
DELETE FROM cronogramas WHERE idcontrato = 8;

-- 3. Regenerar el cronograma con la nueva fecha
CALL generar_cronograma(8, 4.263224089);

-- 4. Verificar que ahora comienza en 15/10/2025 (primer mes después del inicio)
SELECT 
    numcuota,
    DATE_FORMAT(fechapago, '%d/%m/%Y') as fecha,
    ROUND(abonocapital, 2) as capital,
    ROUND(interes, 2) as interes,
    ROUND(abonocapital + interes, 2) as cuota,
    ROUND(saldocapital, 2) as saldo
FROM cronogramas
WHERE idcontrato = 8
ORDER BY numcuota;