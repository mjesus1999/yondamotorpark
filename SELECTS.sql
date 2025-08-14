use motorpark;

SELECT * FROM cotizaciones;
SELECT * FROM empresas;
SELECT * FROM personas LIMIT 200;
SELECT * FROM Colaboradores;
select * from contratoslaborales;
select * from areas;
select * from cargos;
select * from clientes;
select * from accesos;
SELECT idcolaborador, usernick, restriccionhoraria
FROM colaboradores
WHERE usernick = 'Deyanira' OR idcolaborador = 3;


-- DELETE FROM Colaboradores WHERE idcolaborador = 39;
-- DELETE FROM cotizaciones WHERE idasesor = 26;
-- DELETE FROM vehiculos WHERE idlogistica = 26;

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
      ORDER BY p.apellidos, p.nombres
      LIMIT 0,1000;

SELECT modulo
            FROM accesos
            WHERE idcargo = 8 AND permisos = 1;
            
SELECT 1 FROM accesos
	WHERE idcargo = 1 AND modulo = 'usuarios' AND permisos = 1
	LIMIT 1;
/*
-- ENCONTRAR EL USERNICK Y EMAIL
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
      ORDER BY p.apellidos, p.nombres
      LIMIT 0,1000;
*/

-- REPORTE DE COTIZACION
SELECT
    c.idcliente,
    CASE 
        WHEN c.tipocliente = 'P' THEN CONCAT(p.nombres, ' ', p.apellidos)
        WHEN c.tipocliente = 'E' THEN e.razonsocial
        ELSE 'Sin nombre'
    END AS nombre_cliente,
    c.tipocliente,
    c.nrodoc
FROM clientes c
LEFT JOIN personas p ON c.idpersona = p.idpersona
LEFT JOIN empresas e ON c.idempresa = e.idempresa;

SELECT
    CONCAT(p.nombres, ' ', p.apellidos) AS nombre_cliente,
    p.nrodoc,
    p.telprimario,
    ma.marca AS marcaVehiculo,
    mo.modelo AS modeloVehiculo,
    mo.anio,
    v.color,
	c.creado AS fechaRegistro
FROM cotizaciones c
JOIN clientes cl ON c.idcliente = cl.idcliente
JOIN personas p ON cl.idpersona = p.idpersona AND cl.tipocliente = 'P'
JOIN vehiculos v ON c.idvehiculo = v.idvehiculo
JOIN modelos mo ON v.idmodelo = mo.idmodelo
JOIN marcas ma ON mo.idmarca = ma.idmarca
WHERE c.idcotizacion = 1;

/*
c.moneda,
c.precioventa,
c.inicial,
c.numcuotas,
c.valorcuota,
c.estadocotizacion,
*/

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
JOIN formatocotizacion fc ON c.idformato = fc.idformato
ORDER BY c.creado DESC
LIMIT 10;


SELECT
    cl.idcliente,
    cl.tipocliente,
    cl.idpersona,
    cl.idempresa,
    p.nombres, p.apellidos, p.nrodoc, p.telprimario,
    e.razonsocial, e.ruc, e.telprimario
FROM clientes cl
LEFT JOIN personas p ON cl.idpersona = p.idpersona
LEFT JOIN empresas e ON cl.idempresa = e.idempresa
WHERE cl.idcliente IN (SELECT idcliente FROM cotizaciones);


SELECT cl.idcliente, cl.tipocliente, p.nrodoc, e.ruc
FROM clientes cl
LEFT JOIN personas p ON cl.idpersona = p.idpersona
LEFT JOIN empresas e ON cl.idempresa = e.idempresa
WHERE p.nrodoc = '71689010' OR e.ruc = '71689010';


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