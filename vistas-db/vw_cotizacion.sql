/*
-- VISTAS DE COTIZACION.PHP
*/

USE motorpark;

-- OBTENER TODAS LAS COTIZACIONES REALIZADAS / getAll 
/*
-- (solo mostrando los que esten en los dias de vigenicas se mostrara)*/
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
    info_reserva.idcotizacion AS idcotizacion_reserva,
    CASE WHEN info_reserva.idcotizacion IS NOT NULL THEN 1 ELSE 0 END AS existe_reserva,
    info_reserva.nombrecliente AS reserva_cliente_nombre,
    info_contrato.nombrecliente AS contrato_cliente_nombre, 
    CASE WHEN info_contrato.idvehiculo IS NOT NULL THEN 1 ELSE 0 END AS vehiculo_en_contrato,

    info_contado.nombrecliente AS contado_cliente_nombre,
    CASE WHEN info_contado.idvehiculo IS NOT NULL THEN 1 ELSE 0 END AS vehiculo_vendido_contado,
    CASE 
        WHEN (
            SELECT COALESCE(SUM(p_sum.amortizacion), 0)
            FROM pagos p_sum
            WHERE p_sum.idcotizacion = c.idcotizacion AND p_sum.idconcepto = (SELECT idconcepto FROM conceptospago WHERE concepto = 'Inicial' LIMIT 1)
        ) >= c.inicial
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
LEFT JOIN colaboradores col ON c.idasesor = col.idcolaborador
LEFT JOIN contratoslaborales cl_ase ON col.idcontratolaboral = cl_ase.idcontratolaboral
LEFT JOIN personas pase ON cl_ase.idpersona = pase.idpersona
LEFT JOIN cargos cg ON cl_ase.idcargo = cg.idcargo
LEFT JOIN (
    WITH RankedReserva AS (
        SELECT
            c_res.idvehiculo, c_res.idcotizacion,
            COALESCE(CASE WHEN cl_res.tipocliente = 'P' THEN CONCAT(p_res.apellidos, ', ', p_res.nombres) ELSE e_res.razonsocial END) AS nombrecliente,
            ROW_NUMBER() OVER(PARTITION BY c_res.idvehiculo ORDER BY c_res.creado DESC) as rn
        FROM cotizaciones c_res
        JOIN clientes cl_res ON c_res.idcliente = cl_res.idcliente
        LEFT JOIN personas p_res ON cl_res.idpersona = p_res.idpersona
        LEFT JOIN empresas e_res ON cl_res.idempresa = e_res.idempresa
        WHERE c_res.estadocotizacion IN ('S', 'A')
    )
    SELECT idvehiculo, idcotizacion, nombrecliente FROM RankedReserva WHERE rn = 1
) AS info_reserva ON info_reserva.idvehiculo = v.idvehiculo
LEFT JOIN (
    WITH RankedContracts AS (
        SELECT
            c_cont.idvehiculo,
            COALESCE(CASE WHEN cl_cont.tipocliente = 'P' THEN CONCAT(p_cont.apellidos, ', ', p_cont.nombres) ELSE e_cont.razonsocial END) AS nombrecliente,
            ROW_NUMBER() OVER(PARTITION BY c_cont.idvehiculo ORDER BY co.idcontrato DESC) as rn
        FROM contratos co
        JOIN cotizaciones c_cont ON co.idcotizacion = c_cont.idcotizacion
        JOIN clientes cl_cont ON c_cont.idcliente = cl_cont.idcliente
        LEFT JOIN personas p_cont ON cl_cont.idpersona = p_cont.idpersona
        LEFT JOIN empresas e_cont ON cl_cont.idempresa = e_cont.idempresa
    )
    SELECT idvehiculo, nombrecliente FROM RankedContracts WHERE rn = 1
) AS info_contrato ON info_contrato.idvehiculo = v.idvehiculo
LEFT JOIN (
    SELECT idcotizacion, SUM(amortizacion) AS total_pagado
    FROM pagos
    GROUP BY idcotizacion
) AS pagos_sum ON pagos_sum.idcotizacion = c.idcotizacion
LEFT JOIN (
    WITH RankedContado AS (
        SELECT
            p_cont.idvehiculo,
            COALESCE(CASE WHEN cl_cont.tipocliente = 'P' THEN CONCAT(per_cont.apellidos, ', ', per_cont.nombres) ELSE e_cont.razonsocial END) AS nombrecliente,
            ROW_NUMBER() OVER(PARTITION BY p_cont.idvehiculo ORDER BY p_cont.idpago DESC) as rn
        FROM pagos p_cont
        JOIN clientes cl_cont ON p_cont.idcliente = cl_cont.idcliente
        LEFT JOIN personas per_cont ON cl_cont.idpersona = per_cont.idpersona
        LEFT JOIN empresas e_cont ON cl_cont.idempresa = e_cont.idempresa
        WHERE p_cont.idconcepto = 1 -- ID para 'Contado'
    )
    SELECT idvehiculo, nombrecliente FROM RankedContado WHERE rn = 1
) AS info_contado ON info_contado.idvehiculo = v.idvehiculo

WHERE 
(
    c.estadocotizacion NOT IN ('CONT') 
    AND (
        (c.estadocotizacion = 'P' AND COALESCE(pagos_sum.total_pagado, 0) = 0 AND DATE_ADD(IFNULL(c.fechareactivacion, c.creado), INTERVAL c.vigenciadias DAY) >= CURDATE())
        OR c.estadocotizacion IN ('A', 'S')
    )
)
ORDER BY c.creado DESC;


SELECT * FROM  vwGetAllCotizacion WHERE estadocotizacion = 'P';

SELECT * FROM cotizaciones;

/*
ORDER BY COALESCE (fechaRegistro, fechareactivacion) DESC
ORDER BY COALESCE(c.fechareactivacion, c.creado) DESC, c.creado DESC
*/


/*
USE motorpark;
DROP VIEW vwGetAllCotizacion;
SELECT * FROM cotizaciones;
*/


-- OBETENER EL DETALLE DE LA COTIZACION POR ID / getById
CREATE OR REPLACE VIEW vwGetCotizacionDetail AS
SELECT
  c.idcotizacion,
  c.idformato,
  c.idcliente,
  c.estadocotizacion,
  c.idvehiculo,
  c.moneda,
  c.precioventa,
  c.vigenciadias,
  c.inicial,
  c.numcuotas,
  c.valorcuota,
  c.idasesor,
  c.creado AS fechaRegistro,
  c.fechareactivacion,
  c.gastosadministrativos,
  -- Datos del cliente (normalizado: persona o empresa)
  CASE
    WHEN cl.tipocliente = 'P' THEN CONCAT(p.apellidos, ', ', p.nombres)
    ELSE e.razonsocial
  END AS cliente_nombre,

  CASE
    WHEN cl.tipocliente = 'P' THEN p.nrodoc
    ELSE e.ruc
  END AS cliente_documento,

  CASE
    WHEN cl.tipocliente = 'P' THEN p.telprimario
    ELSE e.telprimario
  END AS cliente_telefono,
  
  ma.marca      AS vehiculo_marca,
  mo.modelo     AS vehiculo_modelo,
  mo.anio       AS vehiculo_anio,
  v.color       AS vehiculo_color,
  CONCAT(pase.apellidos, ' ', pase.nombres) AS asesor_nombre,
  UPPER(CONCAT(pase.nombres, ' ', pase.apellidos)) AS asesor_nombre_completo,
  cg.cargo AS asesor_cargo,
  pase.telprimario AS asesor_telefono,
  pase.telalternativo AS asesor_telefono_alt,
  col.usernick AS asesor_usuario

FROM cotizaciones c
JOIN clientes cl    ON c.idcliente = cl.idcliente
LEFT JOIN personas p ON cl.idpersona = p.idpersona
LEFT JOIN empresas e ON cl.idempresa = e.idempresa
JOIN vehiculos v    ON c.idvehiculo = v.idvehiculo
-- JOIN completo para obtener información del asesor
LEFT JOIN colaboradores col ON c.idasesor = col.idcolaborador
LEFT JOIN contratoslaborales cl_ase ON col.idcontratolaboral = cl_ase.idcontratolaboral
LEFT JOIN personas pase ON cl_ase.idpersona = pase.idpersona
LEFT JOIN cargos cg ON cl_ase.idcargo = cg.idcargo
-- Datos del vehículo
JOIN modelos mo     ON v.idmodelo = mo.idmodelo
JOIN marcas ma      ON mo.idmarca = ma.idmarca;


-- VISTA PARA OBTENER COTIZACIONES VENCIDAS / getAllVencidas
CREATE OR REPLACE VIEW vwGetAllCotizacionVencidas AS
SELECT
  c.idcotizacion,
  c.idformato,
  c.idasesor,
  fc.tipocotizacion,
  COALESCE(
    CASE WHEN cl.tipocliente = 'P' THEN CONCAT(p.apellidos, ', ', p.nombres) END,
    e.razonsocial,
    'Cliente no definido'
  ) AS nombrecliente,
  COALESCE(
    CASE WHEN cl.tipocliente = 'P' THEN p.nrodoc END,
    e.ruc,
    ''
  ) AS documento,
  COALESCE(
    CASE WHEN cl.tipocliente = 'P' THEN p.telprimario END,
    e.telprimario,
    ''
  ) AS telefono,
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
  DATE_ADD(IFNULL(c.fechareactivacion, c.creado), INTERVAL c.vigenciadias DAY) AS fecha_vencimiento,
  c.fechareactivacion,
  CONCAT(pase.apellidos, ' ', pase.nombres) AS asesor_nombre,
  col.usernick AS asesor_usuario,
  cg.cargo AS asesor_cargo
FROM cotizaciones c
JOIN clientes cl ON c.idcliente = cl.idcliente
LEFT JOIN personas p ON cl.idpersona = p.idpersona
LEFT JOIN empresas e ON cl.idempresa = e.idempresa
JOIN vehiculos v ON c.idvehiculo = v.idvehiculo
JOIN modelos mo ON v.idmodelo = mo.idmodelo
JOIN marcas ma ON mo.idmarca = ma.idmarca
JOIN formatocotizacion fc ON c.idformato = fc.idformato
LEFT JOIN colaboradores col ON c.idasesor = col.idcolaborador
LEFT JOIN contratoslaborales cl_ase ON col.idcontratolaboral = cl_ase.idcontratolaboral
LEFT JOIN personas pase ON cl_ase.idpersona = pase.idpersona
LEFT JOIN cargos cg ON cl_ase.idcargo = cg.idcargo
WHERE DATE_ADD(IFNULL(c.fechareactivacion, c.creado), INTERVAL c.vigenciadias DAY) < CURDATE();


/*ORDER BY COALESCE (fechaRegistro, fechareactivacion) DESC;*/



