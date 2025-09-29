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


