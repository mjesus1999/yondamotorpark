/*
-- VISTAS DE USUARIO.PHP 
*/

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
	END AS restriccionhoraria,
    
    col.idlocal AS idlocal,
    --  NULLIF(dep.departamento, ''),
    CONCAT_WS('/', NULLIF(pro.provincia, '')) AS ubicacion
    
	FROM colaboradores col
	INNER JOIN contratoslaborales cl
	  ON col.idcontratolaboral = cl.idcontratolaboral
	INNER JOIN personas p
	  ON cl.idpersona = p.idpersona
	INNER JOIN cargos cg
	  ON cg.idcargo = cl.idcargo
	INNER JOIN areas a
	  ON a.idarea = cg.idarea
	LEFT JOIN locales loc ON col.idlocal = loc.idlocal
	LEFT JOIN distritos dis ON loc.iddistrito = dis.iddistrito
	LEFT JOIN provincias pro ON dis.idprovincia = pro.idprovincia
	LEFT JOIN departamentos dep ON pro.iddepartamento = dep.iddepartamento
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
  p.iddistrito AS persona_iddistrito,
  d.distrito AS persona_nombre_distrito,
  p.direccion,
  p.referencia,
  p.telprimario,
  p.telalternativo,
  DATE_FORMAT(cl.fechainicio, '%Y-%m-%d') AS fechainicio,
  IFNULL(DATE_FORMAT(cl.fechafin, '%Y-%m-%d'), 'Indeterminado') AS fechafin,
  cl.idcargo AS idcargo,
  cg.cargo,
  a.area,
  a.idarea AS idarea,
  cl.idcontratolaboral AS idcontratolaboral,
  p.idpersona AS idpersona,

  col.idlocal AS idlocal,
  l.tienda AS local_tienda,
  disloc.distrito AS local_distrito,
  proloc.provincia AS local_provincia,
  deploc.departamento AS local_departamento,
  
  CONCAT_WS(' / ',
    NULLIF(deploc.departamento, ''),
    NULLIF(proloc.provincia, ''),
    NULLIF(disloc.distrito, '')
  ) AS local_ubicacion,
  
  CONCAT_WS(' - ',
    NULLIF(l.tienda, ''),
    CONCAT_WS(' / ',
      NULLIF(proloc.provincia, ''),
      NULLIF(deploc.departamento, '')
    )
  ) AS local
  
  /*CONCAT_WS(' / ',
    NULLIF(deploc.departamento, ''),
    NULLIF(proloc.provincia, ''),
    NULLIF(disloc.distrito, '')
  ) AS local_ubicacion,
  
  CONCAT_WS(' - ',
    NULLIF(l.tienda, ''),
    CONCAT_WS(' / ',
      NULLIF(proloc.provincia, ''),
      NULLIF(deploc.departamento, '')
    )
  ) AS local*/

FROM colaboradores col
JOIN contratoslaborales cl ON cl.idcontratolaboral = col.idcontratolaboral
JOIN personas p            ON p.idpersona = cl.idpersona
JOIN cargos cg             ON cg.idcargo = cl.idcargo
JOIN areas a               ON a.idarea = cg.idarea
LEFT JOIN distritos d      ON d.iddistrito = p.iddistrito
LEFT JOIN locales l        ON l.idlocal = col.idlocal
LEFT JOIN distritos disloc ON l.iddistrito = disloc.iddistrito
LEFT JOIN provincias proloc ON disloc.idprovincia = proloc.idprovincia
LEFT JOIN departamentos deploc ON proloc.iddepartamento = deploc.iddepartamento
WHERE col.idcolaborador IS NOT NULL;

/*
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
*/

-- VISTA DE BUSCAR POR USERNICK
DROP VIEW IF EXISTS vwSearchUsernick;
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


