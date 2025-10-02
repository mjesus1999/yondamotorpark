/*
-- VISTAS DE COTIZACION.PHP
*/

USE motorpark;

-- OBTENER TODAS LAS COTIZACIONES REALIZADAS / getAll 
/*
-- (solo mostrando los que esten en los dias de vigenicas se mostrara)
*/

CREATE OR REPLACE VIEW vwGetAllCotizacion AS
SELECT
    c.idcotizacion,
    c.idformato,
    c.idasesor,
    c.numcuotas,
    c.estadocotizacion,
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
    -- Calcular vencimiento
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
WHERE 
(
  
    c.estadocotizacion NOT IN ('CONT') 
    AND
    (
        -- Regla de vigencia: Vigente O Aprobada
        DATE_ADD(IFNULL(c.fechareactivacion, c.creado), INTERVAL c.vigenciadias DAY) >= CURDATE()
        OR c.estadocotizacion = 'A'
    )
)
ORDER BY COALESCE(c.fechareactivacion, c.creado) ASC, c.creado DESC;

SELECT * FROM vwGetAllCotizacion WHERE estadocotizacion = 'P';


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



