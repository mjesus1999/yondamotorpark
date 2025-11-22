
USE motorpark;


SELECT * FROM colaboradores;
SELECT * FROM contratoslaborales;
UPDATE contratoslaborales SET idcargo = 1 WHERE idcontratolaboral = 3;
SELECT * FROM cargos;
SELECT * FROM areas;

-- Permisos para Jefe de Caja (idcargo = 17)
INSERT INTO accesos (idcargo, modulo, permisos) VALUES
(17, 'caja', 1),
(17, 'auth', 1);

-- Jefe de Logística (idcargo = 8)
INSERT INTO accesos (idcargo, modulo, permisos) VALUES
(8, 'oc', 1),
(8, 'compras', 1),
(8, 'vehiculos', 1),
(8, 'recepcionVehiculos', 1),
(8, 'marcas', 1),
(8, 'concesionarios', 1);

-- Asistente de Logística (idcargo = 9)
INSERT INTO accesos (idcargo, modulo, permisos) VALUES
(9, 'oc', 1),
(9, 'compras', 1),
(9, 'vehiculos', 1),
(9, 'recepcionVehiculos', 1),
(9, 'marcas', 1);


-- Jefe de Sistemas (idcargo = 1)
INSERT INTO accesos (idcargo, modulo, permisos) VALUES
(1, 'concesionarios', 1),
(1, 'locales', 1),
(1, 'clientes', 1),
(1, 'marcas', 1),
(1, 'vehiculos', 1),
(1, 'recepcionVehiculos', 1),
(1, 'oc', 1),
(1, 'compras', 1),
(1, 'formatoCotizacion', 1),
(1, 'cotizacion', 1),
(1, 'contratos', 1),
(1, 'vehiculosAlContado', 1),
(1, 'usuarios', 1),
(1, 'caja', 1),
(1, 'creditos', 1),
(1, 'egreso', 1),
(1, 'arqueoCaja', 1),
(1, 'cobranza', 1),
(1, 'auth', 1);

-- Jefe de Recursos Humanos (idcargo = 10)
INSERT INTO accesos (idcargo, modulo, permisos) VALUES
(10, 'usuarios', 1),
(10, 'auth', 1);

-- Jefe de Ventas (idcargo = 16)
INSERT INTO accesos (idcargo, modulo, permisos) VALUES
(16, 'clientes', 1),
(16, 'vehiculos', 1),
(16, 'cotizacion', 1),
(16, 'contratos', 1),
(16, 'vehiculosAlContado', 1);

-- Asesor de Ventas (idcargo = 18)
INSERT INTO accesos (idcargo, modulo, permisos) VALUES
(18, 'clientes', 1),
(18, 'cotizacion', 1);

-- Jefe de Cobranza (idcargo = 19)
INSERT INTO accesos (idcargo, modulo, permisos) VALUES
(19, 'cobranza', 1),
(19, 'contratos', 1),
(19, 'clientes', 1);