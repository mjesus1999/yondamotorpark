
USE motorpark;


-- VISTA DE EJEMPLO PARA VISUALIZAR CARGOS CON USUARIOS
CREATE VIEW vw_colaboradores_con_cargo AS
SELECT 
    col.idcolaborador,
    p.nombres,
    p.apellidos,
    CONCAT(p.apellidos, ' ', p.nombres) AS nombrecompleto,
    col.usernick,
    c.cargo,
    cl.fechainicio,
    cl.fechafin,
    col.habilitado,
    col.ultimoacceso
FROM colaboradores col
INNER JOIN contratoslaborales cl ON col.idcontratolaboral = cl.idcontratolaboral
INNER JOIN personas p ON cl.idpersona = p.idpersona
INNER JOIN cargos c ON cl.idcargo = c.idcargo;


/*
-- VISTAS DE USUARIO.PHP 
*/


-- VISTA DE OBTENER TODOS LOS USUARIOS Y MOSTRARLOS / getAll
DROP VIEW IF EXISTS vwGetAllUser;
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
	  col.usernick        AS usuario,
      
    CASE
    WHEN UPPER(COALESCE(col.restriccionhoraria, 'N')) IN ('S','1','Y','YES','TRUE') THEN 'S'
    ELSE 'N'
	END AS restriccionhoraria
    
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
CREATE OR REPLACE VIEW vwGetUserDetail AS
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
  a.area,
  a.idarea AS idarea,                       -- <- agregado
  cl.idcontratolaboral AS idcontratolaboral,-- <- agregado
  p.idpersona AS idpersona                  -- <- agregado
FROM colaboradores col
JOIN contratoslaborales cl ON cl.idcontratolaboral = col.idcontratolaboral
JOIN personas p            ON p.idpersona          = cl.idpersona
JOIN cargos cg             ON cg.idcargo           = cl.idcargo
JOIN areas a               ON a.idarea             = cg.idarea
LEFT JOIN distritos d      ON d.iddistrito         = p.iddistrito;


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


-- VISTA PRUEBA (VISTA DE MOSTRAR CONTRATOS / SI SE DESHABILITA UN USUARIO PASA A SER UN CONTRATO SIN CUENTA)
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
-- sólo "unimos" colaboradores que estén habilitados:
LEFT JOIN colaboradores col
  ON col.idcontratolaboral = cl.idcontratolaboral
  AND col.habilitado = 'S'
WHERE col.idcolaborador IS NULL
ORDER BY p.apellidos, p.nombres;


/*
-- VISTAS DE COTIZACION.PHP
*/

-- OBTENER TODAS LAS COTIZACIONES REALIZADAS / getAll 
/*
-- (solo mostrando los que esten en los dias de vigenicas se mostrara)
*/


CREATE OR REPLACE VIEW vwGetAllCotizacion AS
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
  -- calcular vencimiento desde fechareactivacion si existe, si no desde creado
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
WHERE DATE_ADD(IFNULL(c.fechareactivacion, c.creado), INTERVAL c.vigenciadias DAY) >= CURDATE()
ORDER BY COALESCE(c.fechareactivacion, c.creado) ASC, c.creado ASC;

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
