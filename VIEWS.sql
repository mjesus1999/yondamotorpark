/*
-- VISTAS DE USUARIO.PHP 
*/

-- VISTA DE OBTENER TODOS LOS USUARIOS Y MOSTRARLOS / getAll
CREATE VIEW vwGetAllUser AS
	SELECT
	  col.idcolaborador   AS idcolaborador,
	  p.apellidos         AS apellidos,
	  p.nombres           AS nombres,
	  a.area              AS area,
	  cg.cargo            AS cargo,
	  DATE_FORMAT(cl.fechainicio, '%Y-%m-%d') AS fecha_inicio,
	  IFNULL(
		DATE_FORMAT(cl.fechafin, '%Y-%m-%d'),
		'Indeterminado'
	  )                   AS fecha_fin,
	  col.usernick        AS usuario
	FROM colaboradores col
	INNER JOIN contratoslaborales cl
	  ON col.idcontratolaboral = cl.idcontratolaboral
	INNER JOIN personas p
	  ON cl.idpersona = p.idpersona
	INNER JOIN cargos cg
	  ON cg.idcargo = cl.idcargo
	INNER JOIN areas a
	  ON a.idarea = cg.idarea
	WHERE col.habilitado = 'S';


-- VISTA DE OBTENER EL USUARIO POR ID Y MOSTRARLO / GET BY ID
CREATE VIEW vwGetUserDetail AS
SELECT
  col.idcolaborador,
  col.usernick,
  col.avatar,
  col.restriccionhoraria,
  p.apellidos,
  p.nombres,
  p.tipodoc,
  p.nrodoc,
  p.genero,
  DATE_FORMAT(p.fechanac, '%Y-%m-%d') AS fechanac,
  p.estadocivil,
  p.email,
  p.iddistrito,
  d.distrito AS nombre_distrito,
  p.direccion,
  p.referencia,
  p.telprimario,
  p.telalternativo,
  DATE_FORMAT(cl.fechainicio, '%Y-%m-%d') AS fechainicio,
  IFNULL(DATE_FORMAT(cl.fechafin, '%Y-%m-%d'), 'Indeterminado') AS fechafin,
  cl.idcargo AS idcargo,
  cg.cargo,
  a.area
FROM colaboradores col
JOIN contratoslaborales cl ON cl.idcontratolaboral = col.idcontratolaboral
JOIN personas p            ON p.idpersona          = cl.idpersona
JOIN cargos cg             ON cg.idcargo           = cl.idcargo
JOIN areas a               ON a.idarea             = cg.idarea
LEFT JOIN distritos d      ON d.iddistrito         = p.iddistrito;


-- SELECT * FROM vwGetUserDetail WHERE idcolaborador = 1;


-- VISTA DE BUSCAR POR USERNICK
CREATE VIEW vwSearchUsernick AS
SELECT
  col.idcolaborador,
  col.usernick,
  col.userpassword,
  col.habilitado,
  col.avatar,
  col.restriccionhoraria,
  p.nombres,
  p.apellidos,
  cl.idcargo,
  cg.cargo
FROM colaboradores col
JOIN contratoslaborales cl ON cl.idcontratolaboral = col.idcontratolaboral
JOIN personas p ON p.idpersona = cl.idpersona
JOIN cargos cg ON cg.idcargo = cl.idcargo;


-- VISTA DE MOSTRAR CONTRATOS / QUE NO ESTAN REGISTRADOS COMO COLABORADORES
CREATE VIEW vwContractsWithoutColaborador AS
SELECT
  cl.idcontratolaboral,
  cl.idpersona,
  p.apellidos,
  p.nombres,
  a.area,
  cg.cargo,
  DATE_FORMAT(cl.fechainicio, '%Y-%m-%d') AS fechainicio
FROM contratoslaborales cl
JOIN personas p ON p.idpersona = cl.idpersona
JOIN cargos cg ON cg.idcargo = cl.idcargo
JOIN areas a ON a.idarea = cg.idarea
LEFT JOIN colaboradores col ON col.idcontratolaboral = cl.idcontratolaboral
WHERE col.idcolaborador IS NULL
ORDER BY p.apellidos, p.nombres;


/*
-- VISTAS DE COTIZACION.PHP
*/

-- OBTENER TODAS LAS COTIZACIONES REALIZADAS / getAll
CREATE OR REPLACE VIEW vwGetAllCotizacion AS
SELECT
    c.idcotizacion,
    c.idformato,
    fc.tipocotizacion,
    COALESCE(
      CASE WHEN cl.tipocliente = 'P' THEN CONCAT(p.nombres, ' ', p.apellidos) END,
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
    v.color,
    c.creado AS fechaRegistro
FROM cotizaciones c
JOIN clientes cl ON c.idcliente = cl.idcliente
LEFT JOIN personas p ON cl.idpersona = p.idpersona
LEFT JOIN empresas e ON cl.idempresa = e.idempresa
JOIN vehiculos v ON c.idvehiculo = v.idvehiculo
JOIN modelos mo ON v.idmodelo = mo.idmodelo
JOIN marcas ma ON mo.idmarca = ma.idmarca
JOIN formatocotizacion fc ON c.idformato = fc.idformato;


-- OBETENER EL DETALLE DE LA COTIZACION POR ID / getById
CREATE OR REPLACE VIEW vwGetCotizacionDetail AS
SELECT
  c.idcotizacion,
  c.idformato,
  c.idcliente,
  c.idvehiculo,
  c.moneda,
  c.precioventa,
  c.vigenciadias,
  c.inicial,
  c.numcuotas,
  c.valorcuota,
  c.idasesor,
  c.creado AS fechaRegistro,

  -- Datos del cliente (normalizado: persona o empresa)
  CASE
    WHEN cl.tipocliente = 'P' THEN CONCAT(p.nombres, ' ', p.apellidos)
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

  -- Vehículo
  ma.marca      AS vehiculo_marca,
  mo.modelo     AS vehiculo_modelo,
  mo.anio       AS vehiculo_anio,
  v.color       AS vehiculo_color

FROM cotizaciones c
JOIN clientes cl    ON c.idcliente = cl.idcliente
LEFT JOIN personas p ON cl.idpersona = p.idpersona
LEFT JOIN empresas e ON cl.idempresa = e.idempresa
JOIN vehiculos v    ON c.idvehiculo = v.idvehiculo
JOIN modelos mo     ON v.idmodelo = mo.idmodelo
JOIN marcas ma      ON mo.idmarca = ma.idmarca;


