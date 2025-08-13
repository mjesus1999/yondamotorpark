USE motorpark;

-- CONTRASEÑA ACTUALIZADA PARA PODER ENTRAR EN EL LOGIN
-- anonimo$$123%
UPDATE colaboradores
SET userpassword = '$2y$10$cYt7.yHXNdzUaYGw0xktxuD9MJem51XAfTSClw7FZnL6a/XNeEOeS'
WHERE usernick = 'leticiall';

-- COMBUSTIBLE
INSERT INTO combustibles (idcombustible, combustible) VALUES
  (1, 'Gasolina'),
  (2, 'Diésel'),
  (3, 'GLP'),
  (4, 'GNV'),
  (5, 'Dual: Gasolina, GLP');
   
-- MOTORPARK
INSERT INTO motorpark (ruc, razonsocial, nombrecomercial)
VALUES
  ('20500011122', 'Motorpark S.A.C.', 'Motorpark');

-- LOCALES
INSERT INTO locales (
  tienda, iddistrito, idmotorpark, principal,
  responsable, correo, direccion, telefono, latitud, longitud
) VALUES
  (
    'Sucursal Lima Centro', 10, 1, 'S',
    'Juan Pérez', 'juan.perez@motorpark.com',
    'Av. Ejemplo 123, Lima', '012345678',
    '-12.046374', '-77.042793'
  ),
  (
    'Sucursal Arequipa', 20, 1, 'N',
    'María López', 'maria.lopez@motorpark.com',
    'Calle Ficticia 456, Arequipa', '054123456',
    '-16.409047', '-71.537451'
  ),
  (
    'Sucursal Chincha', 1010 , 1, 'S',
    'Leticia', 'leticia.llana@motorpark.com',
    'Av. Ejemplo 24, Chincha', '987654321',
    '-14.046374', '-70.042793'
  );

-- FORMATO COTIZACION
INSERT INTO formatocotizacion (tipocotizacion, fechainicio, fechafin)
VALUES
  ('Independiente formal',      '2025-01-01', NULL),
  ('Independiente Informal',    '2025-07-01', NULL),
  ('Dependiente',               '2025-03-15', NULL),
  ('Contado Empresas',          '2025-07-21', NULL),
  ('Contado Persona Natural',   '2025-07-21', NULL);

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
  ('PAGO UNICO POR GASTOS ADMINISTRATIVOS S/1,500.00'),
  ('SEGURO VEHICULAR (bajo evaluación)'),
  ('GPS SATELITAL'),
  ('RECIBO DE SERVICIOS'),
  ('BOLETAS DE PAGO');

-- DETALLE DE LOS REQUISITOS PARA EL FORMATO DE COTIZACION
-- Independiente formal (idformato = 1), requisitos según listado (incluye RECIBO DE SERVICIOS = 14)
INSERT INTO detallerequisitos (idformato, idrequisito) VALUES
  (1, 1),
  (1, 2),
  (1, 3),
  (1, 4),
  (1, 5),
  (1, 14),
  (1, 6),
  (1, 7),
  (1, 8),
  (1, 9),
  (1, 10),
  (1, 11),
  (1, 12),
  (1, 13);
  
-- Independiente Informal (idformato = 2), requisitos 1 a 13
INSERT INTO detallerequisitos (idformato, idrequisito) VALUES
  (2, 1),
  (2, 2),
  (2, 3),
  (2, 4),
  (2, 5),
  (2, 6),
  (2, 7),
  (2, 8),
  (2, 9),
  (2, 10),
  (2, 11),
  (2, 12),
  (2, 13);

-- Dependiente (idformato = 3), requisitos según listado (incluye BOLETAS DE PAGO = 15)
INSERT INTO detallerequisitos (idformato, idrequisito) VALUES
  (3, 1),
  (3, 2),
  (3, 3),
  (3, 15),
  (3, 7),
  (3, 8),
  (3, 9),
  (3, 10),
  (3, 11),
  (3, 12),
  (3, 13);


-- VEHICULOS INSERT (disponibilidad)
-- PROCESO
INSERT INTO vehiculos (
    idmodelo, version, condicion, idcombustible, disponibilidad, idlogistica, idlocal, origen, creado
) VALUES (
    1, 'Versión A', 'nuevo', 1, 'proceso', 2, 3, 'CTZ', NOW()
);

-- LIBRE
INSERT INTO vehiculos (
    idmodelo, version, condicion, idcombustible, disponibilidad, idlogistica, idlocal, origen, creado
) VALUES (
    1, 'Full', 'nuevo', 1, 'libre', 2, 3, 'CTZ', NOW()
);

-- SEPARADO
INSERT INTO vehiculos (
    idmodelo, version, condicion, idcombustible, disponibilidad, idlogistica, idlocal, origen, creado
) VALUES (
    1, 'Versión C', 'seminuevo', 1, 'separado', 2, 3, 'CTZ', NOW()
);

-- PAGADO
INSERT INTO vehiculos (
    idmodelo, version, condicion, idcombustible, disponibilidad, idlogistica, idlocal, origen, creado
) VALUES (
    1, 'Versión D', 'seminuevo', 1, 'vendido', 2, 3, 'CTZ', NOW()
);

-- SELECT * FROM vehiculos;

/*
UPDATE vehiculos
SET
	color = 'AZUL',
	chasis = 'CHS6543210911',
	placa = 'XYZ-711',
	placarotativa = 'ROT-711',
	seriemotor = 'SM987654311',
	precioventa = 32500.00,
	moneda = 'USD',
	disponibilidad = 'proceso',
	modificado = NOW()
WHERE idvehiculo = 1;

UPDATE vehiculos
SET
	color = 'ROJO',
	chasis = 'CHS6543210922',
	placa = 'XYZ-722',
	placarotativa = 'ROT-722',
	seriemotor = 'SM987654322',
	precioventa = 22500.00,
	moneda = 'PEN',
	disponibilidad = 'libre',
	modificado = NOW()
WHERE idvehiculo = 2;

UPDATE vehiculos
SET
	color = 'VERDE',
	chasis = 'CHS6543210933',
	placa = 'XYZ-733',
	placarotativa = 'ROT-733',
	seriemotor = 'SM98765433',
	precioventa = 33500.00,
	moneda = 'USD',
	disponibilidad = 'separado',
	modificado = NOW()
WHERE idvehiculo = 3;

UPDATE vehiculos
SET
	color = 'MORADO',
	chasis = 'CHS6543210944',
	placa = 'XYZ-744',
	placarotativa = 'ROT-744',
	seriemotor = 'SM98765444',
	precioventa = 24500.00,
	moneda = 'USD',
	disponibilidad = 'vendido',
	modificado = NOW()
WHERE idvehiculo = 4;
*/

-- EMPRESAS
INSERT INTO empresas (razonsocial, nombrecomercial, ruc, representante, email, telprimario)
VALUES
  ('Servicios Alpha S.A.C.', 'Alpha', '20512345678', 'Carlos Ruiz', 'ventas@alpha.com', '999888777');

-- CLIENTES X4
INSERT INTO clientes (tipocliente, idpersona, idempresa, idcolregistra)
VALUES 	('P', 1, NULL,  2), 
		('P', 2, NULL,  2), 
        ('P', 3, NULL,  2),
        ('P', NULL, 1,  2);

/*
-- CLIENTES X3
INSERT INTO clientes (tipocliente, idpersona, idempresa, idcolregistra)
VALUES
  ('P', 1, NULL,  3),  -- Cliente Persona
  ('P', 2, NULL,  3),
  ('E', NULL, 1,  3);  
*/

-- INSERT DE PERSONAS 

-- Persona 1 (ejemplo)
INSERT INTO personas (
  iddistrito, apellidos, nombres, tipodoc, nrodoc, genero, fechanac,
  estadocivil, email, direccion, referencia, telprimario
) VALUES (
  2,
  'Gómez', 'María Luisa', 'DNI', '87654321', 'F', '1992-11-20',
  'CAS', 'maria.gomez@ejemplo.com', 'Jr. Los Olivos 45', 'Depto. 3', '976543210'
);
SET @idPersona2 = LAST_INSERT_ID();
INSERT INTO contratoslaborales (
  idpersona, idcargo, fechainicio, fechafin, tipocontrato
) VALUES (
  @idPersona2,
  8,              -- idcargo existente
  '2023-07-01',
  '2024-09-01',   -- contrato con fecha de fin
  'P'             -- tipo recibos/temporal
);

-- Persona 2 (ejemplo adicional)
INSERT INTO personas (
  iddistrito, apellidos, nombres, tipodoc, nrodoc, genero, fechanac,
  estadocivil, email, direccion, referencia, telprimario
) VALUES (
  5,
  'Ramírez', 'Carlos Alberto', 'DNI', '71234567', 'M', '1985-03-15',
  'SOL', 'carlos.ramirez@ejemplo.com', 'Av. Siempre Viva 123', 'Casa', '987654321'
);
SET @idPersona3 = LAST_INSERT_ID();
INSERT INTO contratoslaborales (
  idpersona, idcargo, fechainicio, fechafin, tipocontrato
) VALUES (
  @idPersona3,
  9,              -- idcargo existente
  '2024-01-15',
  NULL,           -- contrato indefinido / sin fecha de fin
  'P'             -- tipo: Indefinido (ajusta según tus valores)
);

-- Persona 3 (otro ejemplo adicional)
INSERT INTO personas (
  iddistrito, apellidos, nombres, tipodoc, nrodoc, genero, fechanac,
  estadocivil, email, direccion, referencia, telprimario
) VALUES (
  3,
  'Pérez', 'Ana María', 'DNI', '71223344', 'F', '1990-06-30',
  'CAS', 'ana.perez@ejemplo.com', 'Calle Falsa 100', 'Piso 2', '965432178'
);
SET @idPersona4 = LAST_INSERT_ID();
INSERT INTO contratoslaborales (
  idpersona, idcargo, fechainicio, fechafin, tipocontrato
) VALUES (
  @idPersona4,
  2,             -- idcargo existente
  '2022-05-01',
  '2023-05-01',   -- contrato con fecha de fin
  'P'             -- tipo: temporal (ajusta según tus valores)
);

-- PERMISOS

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

/*
-- Jefe de Logística (ID = 8) - Acceso total a todos los módulos
INSERT INTO permisos (idcargo, moduloapp) VALUES
(8, 'marcas'),
(8, 'usuarios'),
(8, 'vehiculos'),
(8, 'formatoCotizacion'),
(8, 'auth'),
(8, 'cotizacion');

-- Jefe de Sistemas (ID = 1) - Acceso a todos los módulos también
INSERT INTO permisos (idcargo, moduloapp) VALUES
(1, 'marcas'),
(1, 'usuarios'),
(1, 'vehiculos'),
(1, 'formatoCotizacion'),
(1, 'auth'),
(1, 'cotizacion');;

-- Practicante (ID = 3) - Solo puede ver vehículos
INSERT INTO permisos (idcargo, moduloapp) VALUES
(3, 'vehiculos');
*/

/*
-- Supongamos que la persona con DNI 71689010 tiene idpersona = 3
INSERT INTO clientes (tipocliente, idpersona, idempresa, idcolregistra)
VALUES ('P', 3, NULL,  3);

INSERT INTO clientes (tipocliente, idpersona, idempresa, idcolregistra)
VALUES 	('P', 1, NULL,  2), 
		('P', 2, NULL,  2), 
        ('P', 3, NULL,  2),
        ('P', NULL, 1,  2);
*/

/*
INSERT INTO vehiculos
  (idmodelo, version, condicion, idcombustible, color, chasis, placa, placarotativa, seriemotor,
   moneda, precioventa, disponibilidad, idlogistica, idlocal, origen)
VALUES
  -- Vehículo 1
  (1, 'Standard',      'nuevo', 1, 'Rojo',    'CHASIS123ABC', 'ABC-123', 'ROT-001', 'ENG-0001',
   'USD', 25000.00, 'libre', 2, 3, 'CTZ'),
  -- Vehículo 2
  (2, 'Deluxe',        'seminuevo', 2, 'Azul',  'CHASIS456DEF', 'DEF-456', 'ROT-002', 'ENG-0002',
   'PEN', 85000.50, 'separado', 2, 4, 'OCP');

SELECT idlocal, tienda FROM locales;
*/