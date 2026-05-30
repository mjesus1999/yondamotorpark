-- Ejecutar en phpMyAdmin DESPUÉS de sp-db/patch_pagos_unificado_si_falta.sql
-- (tabla pagos con idcotizacion, idconcepto, idcliente, idvehiculo, etc.).
-- Orden: DROP la vista que depende de las otras, luego recrear cadenas.

DROP VIEW IF EXISTS vwGetAllCotizacion;

CREATE OR REPLACE VIEW vwPagosInicialCalculados AS
SELECT
    c.idcotizacion,
    COALESCE(SUM(p.amortizacion), 0) AS totalpagado_calculado
FROM cotizaciones c
LEFT JOIN pagos p
    ON p.idcotizacion = c.idcotizacion
    AND p.idconcepto = (SELECT idconcepto FROM conceptospago WHERE concepto = 'Inicial' LIMIT 1)
GROUP BY c.idcotizacion;

CREATE OR REPLACE VIEW vwVentaContadoPorVehiculo AS
SELECT idvehiculo, nombrecliente
FROM (
    SELECT
        p_cont.idvehiculo,
        COALESCE(
            CASE WHEN cl_cont.tipocliente = 'P'
                 THEN CONCAT(per_cont.apellidos, ', ', per_cont.nombres)
                 ELSE e_cont.razonsocial END
        ) AS nombrecliente,
        ROW_NUMBER() OVER(PARTITION BY p_cont.idvehiculo ORDER BY p_cont.idpago DESC) AS rn
    FROM pagos p_cont
    JOIN clientes cl_cont ON p_cont.idcliente = cl_cont.idcliente
    LEFT JOIN personas per_cont ON cl_cont.idpersona = per_cont.idpersona
    LEFT JOIN empresas e_cont ON cl_cont.idempresa = e_cont.idempresa
    WHERE p_cont.idconcepto = 1
) ranked
WHERE rn = 1;

CREATE OR REPLACE VIEW vwGetAllCotizacion AS
SELECT
    c.idcotizacion,
    c.idformato,
    c.idvehiculo,
    c.idasesor,
    c.numcuotas,
    c.estadocotizacion,
    fc.tipocotizacion,
    COALESCE(
        CASE WHEN cl.tipocliente = 'P' THEN CONCAT(p.apellidos, ', ', p.nombres) ELSE e.razonsocial END,
        'Cliente no definido'
    ) AS nombrecliente,
    p.direccion,
    COALESCE(
        CASE WHEN cl.tipocliente = 'P' THEN p.nrodoc ELSE e.ruc END, ''
    ) AS documento,
    COALESCE(
        CASE WHEN cl.tipocliente = 'P' THEN p.telprimario ELSE e.telprimario END, ''
    ) AS telefono,
    p.email,
    ma.marca AS marcaVehiculo,
    mo.modelo AS modeloVehiculo,
    mo.anio,
    CONCAT_WS(' / ', ma.marca, mo.modelo, mo.anio) AS vehiculo,
    v.color,
    c.creado AS fechaRegistro,
    c.vigenciadias,
    c.moneda,
    c.inicial,
    c.precioventa,
    c.valorcuota,
    DATE_ADD(IFNULL(c.fechareactivacion, c.creado), INTERVAL c.vigenciadias DAY) AS fecha_vencimiento,
    c.fechareactivacion,
    CONCAT(pase.apellidos, ' ', pase.nombres) AS asesor_nombre,
    col.usernick AS asesor_usuario,
    cg.cargo AS asesor_cargo,
    r.idcotizacion AS idcotizacion_reserva,
    CASE WHEN r.idcotizacion IS NOT NULL THEN 1 ELSE 0 END AS existe_reserva,
    r.nombrecliente AS reserva_cliente_nombre,
    ct.nombrecliente AS contrato_cliente_nombre,
    CASE WHEN ct.idvehiculo IS NOT NULL THEN 1 ELSE 0 END AS vehiculo_en_contrato,
    cc.nombrecliente AS contado_cliente_nombre,
    CASE WHEN cc.idvehiculo IS NOT NULL THEN 1 ELSE 0 END AS vehiculo_vendido_contado,
    CASE
        WHEN c.estadocotizacion IN ('A', 'S')
             AND pc.totalpagado_calculado >= c.inicial
        THEN 1 ELSE 0
    END AS habilitar_contrato
FROM cotizaciones c
JOIN clientes cl ON c.idcliente = cl.idcliente
LEFT JOIN personas p ON cl.idpersona = p.idpersona
LEFT JOIN empresas e ON cl.idempresa = e.idempresa
JOIN vehiculos v ON c.idvehiculo = v.idvehiculo
JOIN modelos mo ON v.idmodelo = mo.idmodelo
JOIN marcas ma ON mo.idmarca = ma.idmarca
JOIN formatocotizacion fc ON c.idformato = fc.idformato
LEFT JOIN vwPagosInicialCalculados pc ON c.idcotizacion = pc.idcotizacion
LEFT JOIN colaboradores col ON c.idasesor = col.idcolaborador
LEFT JOIN contratoslaborales cl_ase ON col.idcontratolaboral = cl_ase.idcontratolaboral
LEFT JOIN personas pase ON cl_ase.idpersona = pase.idpersona
LEFT JOIN cargos cg ON cl_ase.idcargo = cg.idcargo
LEFT JOIN vwReservaPorVehiculo r ON r.idvehiculo = v.idvehiculo
LEFT JOIN vwContratoPorVehiculo ct ON ct.idvehiculo = v.idvehiculo
LEFT JOIN vwVentaContadoPorVehiculo cc ON cc.idvehiculo = v.idvehiculo
WHERE
    c.estadocotizacion NOT IN ('CONT')
    AND (
        (c.estadocotizacion = 'P' AND DATE_ADD(IFNULL(c.fechareactivacion, c.creado), INTERVAL c.vigenciadias DAY) >= CURDATE())
        OR c.estadocotizacion IN ('A', 'S')
    )
ORDER BY c.creado DESC;
