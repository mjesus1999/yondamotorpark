use motorpark;

SELECT * FROM cotizaciones;
/*
* ACTUALIZAR LOS REQUISITOS:
*/
-- REQUISITOS
INSERT INTO requisitos (requisito) 
VALUES
  ('FOTOCOPIA DNI DEL TITULAR Y CONYUGUE'),
  ('COPIA DEL ÚLTIMO RECIBO PAGADO DE SERVICIOS (LUZ O AGUA)'),
  ('COPIA SIMPLE DE VIVIENDA (TÍTULO DE PROPIEDAD / CERTIFICADO DE POSESIÓN, COPIA LITERAL)'),
  ('DECLARACION JURADA DE INGRESOS'),
  ('LICENCIA DE CONDUCIR'),
  ('RECORD DE PAPELETAS'),
  ('DNI AVAL (DNI CONYUGE DE SER NECESARIO)'),
  ('EVALUACION DE GASTOS FAMILIARES'),
  ('30% DE INICIAL COMO MINIMO (aumenta según precio de la unidad)'),
  ('VERIFICACION DOMICILIARIA Y LABORAL'),
  -- ('PAGO UNICO POR GASTOS ADMINISTRATIVOS S/1,500.00'),
  ('Pago único por gastos administrativos'),
  ('SEGURO VEHICULAR (bajo evaluación)'),
  ('GPS SATELITAL'),
  ('RECIBO DE SERVICIOS'),
  ('BOLETAS DE PAGO');

SELECT * FROM requisitos;
SELECT idrequisito, requisito FROM requisitos WHERE idrequisito = 11;
UPDATE requisitos
SET requisito = 'Pago único por gastos administrativos'
WHERE idrequisito = 11;

SELECT idrequisito,
       requisito AS original,
       CONCAT(
         UPPER(LEFT(TRIM(requisito), 1)),
         LOWER(SUBSTRING(TRIM(requisito), 2))
       ) AS nuevo_formato
FROM requisitos
WHERE requisito IS NOT NULL AND requisito <> '';

START TRANSACTION;

UPDATE requisitos
SET requisito = CONCAT(
  UPPER(LEFT(TRIM(requisito), 1)),
  LOWER(SUBSTRING(TRIM(requisito), 2))
)
WHERE requisito IS NOT NULL AND requisito <> '';

COMMIT;

-- copia de seguridad rápida (por si acaso)
CREATE TABLE IF NOT EXISTS requisitos_backup AS SELECT * FROM requisitos;

-- ----- DESDE AQUI -------
SET SQL_SAFE_UPDATES = 0;

START TRANSACTION;

UPDATE requisitos
SET requisito = 'Fotocopia DNI del titular y cónyuge'
WHERE idrequisito = 1;

UPDATE requisitos
SET requisito = 'Copia del último recibo pagado de servicios (luz o agua)'
WHERE idrequisito = 2;

UPDATE requisitos
SET requisito = 'Copia simple de vivienda (título de propiedad / certificado de posesión, copia literal)'
WHERE idrequisito = 3;

UPDATE requisitos
SET requisito = 'Declaración jurada de ingresos'
WHERE idrequisito = 4;

UPDATE requisitos
SET requisito = 'Licencia de conducir'
WHERE idrequisito = 5;

UPDATE requisitos
SET requisito = 'Récord de papeletas'
WHERE idrequisito = 6;

UPDATE requisitos
SET requisito = 'DNI aval (DNI cónyuge de ser necesario)'
WHERE idrequisito = 7;

UPDATE requisitos
SET requisito = 'Evaluación de gastos familiares'
WHERE idrequisito = 8;

UPDATE requisitos
SET requisito = '30% de inicial como mínimo (aumenta según precio de la unidad)'
WHERE idrequisito = 9;

UPDATE requisitos
SET requisito = 'Verificación domiciliaria y laboral'
WHERE idrequisito = 10;

UPDATE requisitos
SET requisito = 'Pago único por gastos administrativos S/ 1,500.00'
WHERE idrequisito = 11;

UPDATE requisitos
SET requisito = 'Seguro vehicular (bajo evaluación)'
WHERE idrequisito = 12;

UPDATE requisitos
SET requisito = 'GPS satelital'
WHERE idrequisito = 13;

UPDATE requisitos
SET requisito = 'Recibo de servicios'
WHERE idrequisito = 14;

UPDATE requisitos
SET requisito = 'Boletas de pago'
WHERE idrequisito = 15;

-- revisar resultados
SELECT idrequisito, requisito FROM requisitos ORDER BY idrequisito;

COMMIT;

-- reactivar safe-updates si quieres
SET SQL_SAFE_UPDATES = 1;

-- ----- HASTA AQUI ------

/*
*	AGREGARE INSERTS PARA VEHICULO (MOTOLINEALES) : 09/09/25
*/
-- (idtipovehiculo = 9) :  HONDA (idmarca = 11)
INSERT INTO modelos (idtipovehiculo, idmarca, modelo, anio, imagenreferencial)
VALUES
(9, 11, 'CB125F', '2025', NULL),
(9, 11, 'XR150L', '2025', NULL),
(9, 11, 'CBR250R', '2025', NULL);

/*
* PARA MOTOTAXIS / SE AGREGARA BAJAJ EJEMPLO : 09/09/25
*/
INSERT INTO marcas (marca) VALUES ('BAJAJ');

-- Ahora agregamos modelos de mototaxi
INSERT INTO modelos (idtipovehiculo, idmarca, modelo, anio, imagenreferencial)
VALUES
(11, (SELECT idmarca FROM marcas WHERE marca = 'BAJAJ'), 'RE 205', '2025', NULL),
(11, (SELECT idmarca FROM marcas WHERE marca = 'BAJAJ'), 'Maxima CNG', '2025', NULL);



SELECT * FROM personas limit 200;
SELECT * FROM contratoslaborales;
SELECT * FROM colaboradores;
SELECT * FROM clientes;
SELECT * FROM vehiculos;
SELECT * FROM cotizaciones;
SELECT * FROM cotizacion_financiamiento WHERE idcotizacion = 48;

SELECT idcotizacion, moneda, precioventa FROM cotizaciones WHERE idcotizacion = 53;

SELECT idfinanciamiento, idcotizacion, numcuotas, inicial, valorcuota, moneda, precioventa, creado
FROM cotizacion_financiamiento
WHERE idcotizacion = 2
ORDER BY numcuotas;


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

INSERT INTO cargos (idarea, cargo) VALUES (5, 'Asesor de Ventas');

INSERT INTO personas (apellidos, nombres, tipodoc, nrodoc, genero, fechanac, estadocivil, telprimario)
VALUES 
('Ríos Castillo', 'Carlos Eduardo', 'DNI', '70000001', 'M', '1990-01-01', 'SOL', '987111111'),
('López Sánchez', 'Ana María', 'DNI', '70000002', 'F', '1992-02-02', 'SOL', '987222222'),
('Torres Vega', 'Luis Alberto', 'DNI', '70000003', 'M', '1989-03-03', 'CAS', '987333333');

INSERT INTO contratoslaborales (idpersona, idcargo, fechainicio, tipocontrato)
VALUES 
(15, 18, '2025-09-01', 'P'),
(16, 18, '2025-09-01', 'P'),
(17, 18, '2025-09-01', 'P');

/*
INSERT INTO colaboradores (idcontratolaboral, usernick, userpassword)
VALUES 
(25, 'carlosr', '$2y$10$ejemploHash1'), -- contraseñas de ejemplo
(26, 'analopez', '$2y$10$ejemploHash2'),
(27, 'luistorres', '$2y$10$ejemploHash3');
*/

INSERT INTO cotizaciones (
    idformato, idcliente, idasesor, idvehiculo,
    moneda, precioventa, inicial, numcuotas, valorcuota,
    comentarios, fechaseguimiento
)
VALUES 
-- Cotización 1 - Carlos Eduardo (colaborador 8) para cliente 1
(1, 1, 9, 1, 'PEN', 45000.00, 9000.00, 24, 1700.00, 'Primera cotización de Carlos', '2025-09-05'),

-- Cotización 2 - Ana María (colaborador 9) para cliente 2 (empresa)
(1, 2, 10, 2, 'USD', 32000.00, 8000.00, 12, 2400.00, 'Primera cotización de Ana', '2025-09-06'),

-- Cotización 3 - Luis Alberto (colaborador 10) para cliente 8
(1, 8, 11, 3, 'PEN', 50000.00, 10000.00, 36, 1300.00, 'Primera cotización de Luis', '2025-09-07');




CREATE OR REPLACE VIEW vwGetAllCotizacion AS
SELECT
  c.idcotizacion,
  c.idformato,
  c.idasesor,
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
  c.creado AS fechaRegistro,
  c.vigenciadias,
  DATE_ADD(c.creado, INTERVAL c.vigenciadias DAY) AS fecha_vencimiento
FROM cotizaciones c
JOIN clientes cl ON c.idcliente = cl.idcliente
LEFT JOIN personas p ON cl.idpersona = p.idpersona
LEFT JOIN empresas e ON cl.idempresa = e.idempresa
JOIN vehiculos v ON c.idvehiculo = v.idvehiculo
JOIN modelos mo ON v.idmodelo = mo.idmodelo
JOIN marcas ma ON mo.idmarca = ma.idmarca
JOIN formatocotizacion fc ON c.idformato = fc.idformato
-- Filtramos para devolver solo las cotizaciones cuya fecha de vencimiento aún NO pasó
WHERE DATE_ADD(c.creado, INTERVAL c.vigenciadias DAY) >= NOW();


SELECT * FROM areas;
SELECT * FROM cargos;
SELECT * FROM contratoslaborales;
SELECT * FROM colaboradores;
SELECT * FROM personas;

SELECT * FROM locales;
SHOW  COLUMNS from personas;
USE MOTORPARK2;
SELECT * FROM contratoslaborales;
SELECT * FROM detordencompra;

SELECT * FROM cotizaciones;
SHOW COLUMNS FROM cotizaciones;
UPDATE colaboradores SET idcontratolaboral = 2 WHERE usernick = 'leticiall';
SELECT * FROM colaboradores;
SELECT * FROM personas;

-- CONTRASEÑA ACTUALIZADA PARA PODER ENTRAR EN EL LOGIN
-- anonimo$$123%
UPDATE colaboradores
SET userpassword = '$2y$10$cYt7.yHXNdzUaYGw0xktxuD9MJem51XAfTSClw7FZnL6a/XNeEOeS'
WHERE usernick = 'leticiall';

-- PASAR DE SI A NO EN RESTRICCION HORARIA a leticiall
UPDATE colaboradores
SET restriccionhoraria = 'N',
    modificado = NOW()
WHERE idcolaborador = 2;
-- CARGOS (OTROS) / PARA PRUEBAS

SELECT * FROM areas;
INSERT INTO cargos (idarea, cargo) VALUES
(2, 'Jefe de Recursos Humanos'),
(2, 'Analista de Recursos Humanos'),
(2, 'Asistente de Recursos Humanos'),
(3, 'Jefe de Contabilidad'),
(4, 'Jefe de Marketing'),
(4, 'Especialista en Marketing'),
(5, 'Jefe de Ventas'),
(6, 'Jefe de Caja');

SELECT * FROM cargos;
-- PERMISOS (ACCESOS A LOS MODULOS)

-- Jefe de logistica (ID = 8) Acceso a todos los modulos
INSERT INTO accesos (idcargo, modulo, permisos) VALUES
(8, 'marcas', 1),
(8, 'usuarios', 1),
(8, 'vehiculos', 1),
(8, 'formatoCotizacion', 1),
(8, 'auth', 1),
(8, 'cotizacion', 1);

-- Jefe de Sistemas (ID = 1) - Acceso a todos los módulos menos registro de cuentas
INSERT INTO accesos (idcargo, modulo, permisos) VALUES
(1, 'marcas', 1),
(1, 'usuarios', 1),
(1, 'vehiculos', 1),
(1, 'formatoCotizacion', 1),
(1, 'cotizacion', 1);

-- Practicante (ID = 3) - Solo puede ver vehículos
INSERT INTO accesos (idcargo, modulo, permisos) VALUES
(3, 'vehiculos', 1);

-- Analista desarrollador (ID = 2) - solo puede ver marcas, vehiculos, formato cotizacion y cotizacion
INSERT INTO accesos (idcargo, modulo, permisos) VALUES
(2, 'marcas', 1),
(2, 'vehiculos', 1),
(2, 'formatoCotizacion', 1),
(2, 'cotizacion', 1);

-- Asistente de logistica (ID = 9) - solo puede ver vehiculos y cotizaciones
INSERT INTO accesos (idcargo, modulo, permisos) VALUES
(9, 'vehiculos', 1),
(9, 'cotizacion', 1);

-- Jefe de recursos humanos (ID = 10) - solo puede ver usuarios y crear cuentas
INSERT INTO accesos (idcargo, modulo, permisos) VALUES
(10, 'usuarios', 1),
(10, 'auth', 1);

-- Analista de recursos humanos (ID = 11) - solo puede ver los usuarios
INSERT INTO accesos (idcargo, modulo, permisos) VALUES
(11, 'usuarios', 1);

-- Asistente de recursos humanos (ID = 12) - solo puede ver los usuarios
INSERT INTO accesos (idcargo, modulo, permisos) VALUES
(12, 'usuarios', 1);

-- Jefe de contactabilidad (ID = 13) - solo puede ver las cotizaciones y formato cotizacion
INSERT INTO accesos (idcargo, modulo, permisos) VALUES
(13, 'cotizacion', 1),
(13, 'formatoCotizacion', 1);

-- Jefe de marketing (ID = 14) - solo puede ver marcas. formato cotizacion y cotizaciones
INSERT INTO accesos (idcargo, modulo, permisos) VALUES
(14, 'marcas', 1),
(14, 'formatoCotizacion', 1),
(14, 'cotizacion', 1);

-- Jefe de ventas (ID = 16) - solo puede ver marcas, vehiculos, formato cotizacion y cotizaciones
INSERT INTO accesos (idcargo, modulo, permisos) VALUES
(16, 'marcas', 1),
(16, 'vehiculos', 1),
(16, 'formatoCotizacion', 1),
(16, 'cotizacion', 1),
(16, 'usuarios', 1),
(16, 'auth', 1);


-- VEHICULOS INSERT (disponibilidad)
INSERT INTO vehiculos (
    idmodelo, 
    version, 
    condicion, 
    idcombustible, 
    disponibilidad, 
    idlogistica, 
    idlocal, 
    origen, 
    creado, 
    color, 
    chasis, 
    placa, 
    placarotativa, 
    seriemotor, 
    precioventa, 
    moneda
) VALUES
	-- PROCESO
	(1, 			-- idmodelo
    'Versión A',  	-- version
    'nuevo',     	-- condicion
    1, 				-- idcombustible
    'proceso',  	-- disponibilidad
    2, 				-- idlogistica
    3, 				-- idlocal
    'CTZ', 			-- origen
    NOW(), 			-- creado
	'AZUL',  		-- color
    'CHS6543210911', -- chasis
    'XYZ-711', 		-- placa
    'ROT-711', 		-- placa rotativa
    'SM987654311', 	-- serie motor
    32500.00, 		-- precio venta
    'USD');			-- moneda


    select * from vehiculos;    


    -- Persona 1 (ejemplo)
INSERT INTO personas (
  iddistrito, apellidos, nombres, tipodoc, nrodoc, genero, fechanac,
  estadocivil, email, direccion, referencia, telprimario
) VALUES (
  2,
  'Gómez', 'Julio', 'DNI', '52585858', 'M', '1992-11-20',
  'CAS', 'julio34@ejemplo.com', 'Jr. Los Olivos 45', 'Depto. 3', '976545510'
);

SELECT * FROM personas;
SET @idPersona2 = LAST_INSERT_ID();

INSERT INTO contratoslaborales (
  idpersona, idcargo, fechainicio, fechafin, tipocontrato
) VALUES (
  10,
  2,              -- idcargo existente
  '2023-07-01',
  '2024-09-01',   -- contrato con fecha de fin
  'P'             -- tipo recibos/temporal
);

SELECT * FROM colaboradores;
SELECT * FROM cargos;

UPDATE colaboradores SET habilitado = 'S';


SELECT * FROM colaboradores;
SELECT * FROM contratoslaborales;
DELETE FROM contratoslaborales WHERE idpersona = 10;