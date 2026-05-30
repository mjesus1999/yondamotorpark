-- USE motorpark; -- hosting

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


UPDATE colaboradores
SET restriccionhoraria = 'N',
    modificado = NOW()
WHERE idcolaborador = 5;

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
    'USD'),			-- moneda
    -- LIBRE
	(1, 			-- idmodelo
    'Full',       	-- version
    'nuevo',     	-- condicion
    1, 				-- idcombustible
    'libre',    	-- disponibilidad
    2, 				-- idlogistica
    3, 				-- idlocal
    'CTZ', 			-- origen
    NOW(), 			-- creado
	'ROJO',  		-- color
    'CHS6543210922', -- chasis
    'XYZ-722', 		-- placa
    'ROT-722', 		-- placa rotativa
    'SM987654322', 	-- serie motor
    22500.00, 		-- precio venta
    'PEN'),
    -- SEPARADO
	(1, 			-- idmodelo
    'Versión C',  	-- versión
    'seminuevo', 	-- condición
    1, 				-- idcombustible
    'separado', 	-- disponibilidad
    2, 				-- idlogistica
    3, 				-- idlocal
    'CTZ', 			-- origen
    NOW(), 			-- creado
	'VERDE', 		-- color
    'CHS6543210933', -- chasis
    'XYZ-733', 		-- placa
    'ROT-733', 		-- placa rotativa
    'SM98765433',  	-- serie motor
    33500.00, 		-- precio venta
    'USD'),			-- moneda
    -- PAGADO
	(1, 			-- idmodelo
    'Versión D',  	-- versión
    'seminuevo', 	-- condición
    1, 				-- idcombustible
    'vendido',  	-- disponibilidad
    2, 				-- idlogistica
    3, 				-- idlocal
    'CTZ', 			-- origen
    NOW(),			-- creado
	'MORADO',		-- color
    'CHS6543210944', -- chasis
    'XYZ-744', 		-- placa
    'ROT-744', 		-- placa rotativa
    'SM98765444',  	-- serie motor
    24500.00, 		-- precio venta
    'USD');			-- moneda


-- EMPRESAS
INSERT INTO empresas (razonsocial, nombrecomercial, ruc, representante, email, telprimario)
VALUES
  ('Servicios Alpha S.A.C.', 'Alpha', '20512345678', 'Carlos Ruiz', 'ventas@alpha.com', '999888777');

-- CLIENTES X4
INSERT INTO clientes (tipocliente, idpersona, idempresa, idcolregistra)
VALUES 	('P', 4, NULL,  2);

-- CONTRATO
INSERT INTO contratoslaborales (idpersona, idcargo, fechainicio, fechafin, tipocontrato) VALUES
(6, 1, now(), null, 1);
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

-- CARGOS (OTROS) / PARA PRUEBAS
INSERT INTO cargos (idarea, cargo) VALUES
(2, 'Jefe de Recursos Humanos'),
(2, 'Analista de Recursos Humanos'),
(2, 'Asistente de Recursos Humanos'),
(3, 'Jefe de Contabilidad'),
(4, 'Jefe de Marketing'),
(4, 'Especialista en Marketing'),
(5, 'Jefe de Ventas'),
(6, 'Jefe de Caja');

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
(16, 'cotizacion', 1);


