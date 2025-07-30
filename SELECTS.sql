use motorpark;

SELECT * FROM personas;
SELECT * FROM empresas;
select * from areas;
select * from cargos;

-- COTIZACION
SELECT
	idpersona,
	apellidos, 
    nombres, 
    telprimario, 
    telalternativo, 
    email 
FROM personas 
WHERE tipodoc = 'DNI'
AND nrodoc  = '71689010'
LIMIT 1;

SELECT idempresa,
	razonsocial AS apellidos, nombrecomercial AS nombres,
	telprimario, telalternativo, email
FROM empresas
WHERE ruc = '20512345678'
LIMIT 1;

-- PERMISOS
SELECT moduloapp
	FROM permisos
	WHERE idcargo = 1;
    
SELECT COUNT(*) 
            FROM permisos 
            WHERE idcargo = 3 AND moduloapp = 'marcas';
            
-- IDLOGISTICA
SELECT a.idarea
	FROM colaboradores c
	INNER JOIN contratoslaborales cl ON c.idcontratolaboral = cl.idcontratolaboral
	INNER JOIN cargos ca ON cl.idcargo = ca.idcargo
	INNER JOIN areas a ON ca.idarea = a.idarea
	WHERE c.idcolaborador = 2
	LIMIT 1;
            
-- MOSTRAR LOS VEHICULOS (MODULO)
SELECT * FROM modelos;
SELECT * FROM vehiculos LIMIT 100;

SELECT * FROM colaboradores;

SELECT 
	v.idvehiculo,
    mc.marca,
    tv.tipovehiculo,
    m.modelo,
    v.version,
    v.condicion,
    v.color,
    v.disponibilidad,
    v.placa,
    v.placarotativa,
    c.idcombustible,
    c.combustible,
    v.moneda,
    v.precioventa
    FROM vehiculos v
    INNER JOIN modelos m ON m.idmodelo = v.idmodelo
    INNER JOIN marcas mc ON mc.idmarca = m.idmarca
    INNER JOIN tipovehiculos tv ON tv.idtipovehiculo = m.idtipovehiculo
    INNER JOIN combustibles c ON c.idcombustible = v.idcombustible
    ORDER BY v.idvehiculo DESC
    LIMIT 100 ;

SELECT 
    v.idvehiculo,
    mc.marca,
    tv.tipovehiculo,
    m.modelo,
    v.version,
    v.condicion,
    v.color,
    v.disponibilidad
FROM vehiculos v
INNER JOIN modelos m ON m.idmodelo = v.idmodelo
INNER JOIN marcas mc ON mc.idmarca = m.idmarca
INNER JOIN tipovehiculos tv ON tv.idtipovehiculo = m.idtipovehiculo
WHERE v.disponibilidad = 'proceso'
ORDER BY v.idvehiculo DESC;

SELECT * FROM vehiculos WHERE idvehiculo = '5';
/*
-- PRUEBA
SELECT 
	v.idvehiculo,
	mc.marca,
	tv.tipovehiculo,
	m.modelo,
	v.version,
	v.condicion,
	v.color,
	v.disponibilidad
FROM vehiculos v
INNER JOIN modelos m ON m.idmodelo = v.idmodelo
INNER JOIN marcas mc ON mc.idmarca = m.idmarca
INNER JOIN tipovehiculos tv ON tv.idtipovehiculo = m.idtipovehiculo
WHERE v.disponibilidad = 'proceso';
*/

/*
SELECT
  v.idvehiculo,
  m.modelo,
  m.anio,
  v.version,
  v.color,
  v.placa
FROM vehiculos v
JOIN modelos m ON v.idmodelo = m.idmodelo
WHERE m.idmarca = :idmarca
  AND m.idtipovehiculo = :idtipovehiculo
  AND m.modelo = :modelo
  AND m.anio = :anio
  AND v.disponibilidad = 'libre'
ORDER BY v.version, v.placa;*/

-- VEHICULOS (MARCA - TIPO - MODELO- AÑO)
SELECT
    DISTINCT(TV.tipovehiculo), MD.idtipovehiculo 
    FROM modelos MD
      INNER JOIN tipovehiculos TV ON MD.idtipovehiculo = TV.idtipovehiculo
      WHERE MD.idmarca = 12
      ORDER BY TV. tipovehiculo;
      
SELECT idtipovehiculo, tipovehiculo
	FROM tipovehiculos
	ORDER BY tipovehiculo;

-- FORMATO DE COTIZACION & (REQUISITOS - DETALLE REQUISITOS)
SELECT idrequisito, requisito FROM requisitos ORDER BY idrequisito;

SELECT
  r.idrequisito,
  r.requisito
FROM detallerequisitos dr
JOIN requisitos r USING (idrequisito)
WHERE dr.idformato = 5
ORDER BY r.idrequisito;

-- CLIENTE (PERSONA NATURAL / EMPRESA)
SELECT
  c.idcliente,
  -- etiqueta para mostrar en el <option>
  CASE
    WHEN c.tipocliente = 'P'
    THEN CONCAT(p.nombres, ' ', p.apellidos)
    ELSE e.nombrecomercial
  END AS label,
  -- campos separados
  CASE
    WHEN c.tipocliente = 'P' THEN p.nrodoc
    ELSE e.ruc
  END AS nrodoc,
  CASE
    WHEN c.tipocliente = 'P' THEN p.telprimario
    ELSE e.telprimario
  END AS telprimario
FROM clientes c
LEFT JOIN personas p ON c.idpersona = p.idpersona
LEFT JOIN empresas e ON c.idempresa = e.idempresa
ORDER BY label;

SELECT * FROM combustibles;