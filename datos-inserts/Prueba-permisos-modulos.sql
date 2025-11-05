-- ================================================================
-- Script SQL para Configuración de Permisos - MOTORPARK YONDA
-- Adaptado a la estructura real de áreas y cargos
-- ================================================================

-- 1. Agregar restricción UNIQUE para evitar duplicados
ALTER TABLE accesos 
ADD CONSTRAINT uk_cargo_modulo UNIQUE (idcargo, modulo);

-- ================================================================
-- PERMISOS POR ÁREA
-- ================================================================

-- ----------------------------------------------------------------
-- ÁREA 1: SISTEMAS
-- ----------------------------------------------------------------

-- Jefe de Sistemas (idcargo = 1) - ACCESO TOTAL como administrador
INSERT INTO accesos (idcargo, modulo, permisos, creado) VALUES
(1, 'concesionarios', 1, NOW()),
(1, 'locales', 1, NOW()),
(1, 'clientes', 1, NOW()),
(1, 'marcas', 1, NOW()),
(1, 'vehiculos', 1, NOW()),
(1, 'recepcionVehiculos', 1, NOW()),
(1, 'oc', 1, NOW()),
(1, 'compras', 1, NOW()),
(1, 'formatoCotizacion', 1, NOW()),
(1, 'cotizacion', 1, NOW()),
(1, 'contratos', 1, NOW()),
(1, 'vehiculosAlContado', 1, NOW()),
(1, 'usuarios', 1, NOW()),
(1, 'caja', 1, NOW()),
(1, 'creditos', 1, NOW()),
(1, 'egreso', 1, NOW()),
(1, 'arqueoCaja', 1, NOW()),
(1, 'cobranza', 1, NOW()),
(1, 'auth', 1, NOW())
ON DUPLICATE KEY UPDATE permisos = 1, modificado = NOW();

-- Analista Desarrollador (idcargo = 2) - Similar a Jefe de Sistemas
INSERT INTO accesos (idcargo, modulo, permisos, creado) VALUES
(2, 'concesionarios', 1, NOW()),
(2, 'locales', 1, NOW()),
(2, 'clientes', 1, NOW()),
(2, 'marcas', 1, NOW()),
(2, 'vehiculos', 1, NOW()),
(2, 'recepcionVehiculos', 1, NOW()),
(2, 'oc', 1, NOW()),
(2, 'compras', 1, NOW()),
(2, 'formatoCotizacion', 1, NOW()),
(2, 'cotizacion', 1, NOW()),
(2, 'contratos', 1, NOW()),
(2, 'vehiculosAlContado', 1, NOW()),
(2, 'usuarios', 1, NOW()),
(2, 'caja', 1, NOW()),
(2, 'creditos', 1, NOW()),
(2, 'egreso', 1, NOW()),
(2, 'arqueoCaja', 1, NOW()),
(2, 'cobranza', 1, NOW()),
(2, 'auth', 1, NOW())
ON DUPLICATE KEY UPDATE permisos = 1, modificado = NOW();

-- Practicante (idcargo = 3) - Acceso limitado a módulos básicos
INSERT INTO accesos (idcargo, modulo, permisos, creado) VALUES
(3, 'clientes', 1, NOW()),
(3, 'vehiculos', 1, NOW()),
(3, 'marcas', 1, NOW())
ON DUPLICATE KEY UPDATE permisos = 1, modificado = NOW();

-- ----------------------------------------------------------------
-- ÁREA 2: RECURSOS HUMANOS
-- ----------------------------------------------------------------

-- Jefe de Recursos Humanos (idcargo = 10)
INSERT INTO accesos (idcargo, modulo, permisos, creado) VALUES
(10, 'usuarios', 1, NOW()),
(10, 'auth', 1, NOW())
ON DUPLICATE KEY UPDATE permisos = 1, modificado = NOW();

-- Analista de Recursos Humanos (idcargo = 11)
INSERT INTO accesos (idcargo, modulo, permisos, creado) VALUES
(11, 'usuarios', 1, NOW())
ON DUPLICATE KEY UPDATE permisos = 1, modificado = NOW();

-- Asistente de Recursos Humanos (idcargo = 12)
INSERT INTO accesos (idcargo, modulo, permisos, creado) VALUES
(12, 'usuarios', 1, NOW())
ON DUPLICATE KEY UPDATE permisos = 1, modificado = NOW();

-- ----------------------------------------------------------------
-- ÁREA 3: CONTABILIDAD
-- ----------------------------------------------------------------

-- Jefe de Contabilidad (idcargo = 13) - Acceso total financiero
INSERT INTO accesos (idcargo, modulo, permisos, creado) VALUES
(13, 'caja', 1, NOW()),
(13, 'creditos', 1, NOW()),
(13, 'egreso', 1, NOW()),
(13, 'arqueoCaja', 1, NOW()),
(13, 'oc', 1, NOW()),
(13, 'compras', 1, NOW()),
(13, 'contratos', 1, NOW()),
(13, 'vehiculosAlContado', 1, NOW()),
(13, 'cobranza', 1, NOW()),
(13, 'clientes', 1, NOW())
ON DUPLICATE KEY UPDATE permisos = 1, modificado = NOW();

-- ----------------------------------------------------------------
-- ÁREA 4: MARKETING
-- ----------------------------------------------------------------

-- Jefe de Marketing (idcargo = 14)
INSERT INTO accesos (idcargo, modulo, permisos, creado) VALUES
(14, 'clientes', 1, NOW()),
(14, 'vehiculos', 1, NOW()),
(14, 'marcas', 1, NOW()),
(14, 'formatoCotizacion', 1, NOW()),
(14, 'cotizacion', 1, NOW())
ON DUPLICATE KEY UPDATE permisos = 1, modificado = NOW();

-- Especialista en Marketing (idcargo = 15)
INSERT INTO accesos (idcargo, modulo, permisos, creado) VALUES
(15, 'clientes', 1, NOW()),
(15, 'vehiculos', 1, NOW()),
(15, 'marcas', 1, NOW())
ON DUPLICATE KEY UPDATE permisos = 1, modificado = NOW();

-- ----------------------------------------------------------------
-- ÁREA 5: VENTAS
-- ----------------------------------------------------------------

-- Jefe de Ventas (idcargo = 16) - Acceso completo a ventas
INSERT INTO accesos (idcargo, modulo, permisos, creado) VALUES
(16, 'clientes', 1, NOW()),
(16, 'vehiculos', 1, NOW()),
(16, 'marcas', 1, NOW()),
(16, 'formatoCotizacion', 1, NOW()),
(16, 'cotizacion', 1, NOW()),
(16, 'contratos', 1, NOW()),
(16, 'vehiculosAlContado', 1, NOW()),
(16, 'creditos', 1, NOW())
ON DUPLICATE KEY UPDATE permisos = 1, modificado = NOW();

-- Asesor de Ventas (idcargo = 18) - Operaciones de venta
INSERT INTO accesos (idcargo, modulo, permisos, creado) VALUES
(18, 'clientes', 1, NOW()),
(18, 'vehiculos', 1, NOW()),
(18, 'formatoCotizacion', 1, NOW()),
(18, 'cotizacion', 1, NOW()),
(18, 'contratos', 1, NOW()),
(18, 'vehiculosAlContado', 1, NOW())
ON DUPLICATE KEY UPDATE permisos = 1, modificado = NOW();

-- ----------------------------------------------------------------
-- ÁREA 6: CAJA
-- ----------------------------------------------------------------

-- Jefe de Caja (idcargo = 17) - Operaciones de caja y finanzas
INSERT INTO accesos (idcargo, modulo, permisos, creado) VALUES
(17, 'caja', 1, NOW()),
(17, 'creditos', 1, NOW()),
(17, 'egreso', 1, NOW()),
(17, 'arqueoCaja', 1, NOW()),
(17, 'contratos', 1, NOW()),
(17, 'clientes', 1, NOW())
ON DUPLICATE KEY UPDATE permisos = 1, modificado = NOW();

-- ----------------------------------------------------------------
-- ÁREA 7: COBRANZA
-- ----------------------------------------------------------------

-- Jefe de Cobranza (idcargo = 19) - Gestión de cobranzas
INSERT INTO accesos (idcargo, modulo, permisos, creado) VALUES
(19, 'cobranza', 1, NOW()),
(19, 'creditos', 1, NOW()),
(19, 'clientes', 1, NOW()),
(19, 'contratos', 1, NOW())
ON DUPLICATE KEY UPDATE permisos = 1, modificado = NOW();

-- ----------------------------------------------------------------
-- ÁREA 8: LEGAL
-- ----------------------------------------------------------------

-- ----------------------------------------------------------------
-- ÁREA 9: LOGÍSTICA
-- ----------------------------------------------------------------

-- Jefe de Logística (idcargo = 8)
INSERT INTO accesos (idcargo, modulo, permisos, creado) VALUES
(8, 'marcas', 1, NOW()),
(8, 'vehiculos', 1, NOW()),
(8, 'recepcionVehiculos', 1, NOW()),
(8, 'oc', 1, NOW()),
(8, 'compras', 1, NOW()),
(8, 'concesionarios', 1, NOW()),
(8, 'locales', 1, NOW())
ON DUPLICATE KEY UPDATE permisos = 1, modificado = NOW();

-- Asistente de Logística (idcargo = 9)
INSERT INTO accesos (idcargo, modulo, permisos, creado) VALUES
(9, 'vehiculos', 1, NOW()),
(9, 'recepcionVehiculos', 1, NOW()),
(9, 'marcas', 1, NOW()),
(9, 'compras', 1, NOW())
ON DUPLICATE KEY UPDATE permisos = 1, modificado = NOW();

-- ================================================================
-- VISTAS ÚTILES PARA CONSULTAS
-- ================================================================

-- Vista para ver permisos por cargo con información completa
-- DROP VIEW vista_permisos_cargo
CREATE OR REPLACE VIEW vista_permisos_cargo AS
SELECT 
    c.idcargo,
    c.cargo,
    a.idarea,
    a.area,
    acc.modulo,
    acc.permisos,
    acc.creado,
    acc.modificado
FROM cargos c
INNER JOIN areas a ON c.idarea = a.idarea
LEFT JOIN accesos acc ON c.idcargo = acc.idcargo
ORDER BY a.area, c.cargo, acc.modulo;

-- Vista para ver usuarios con sus permisos activos
CREATE OR REPLACE VIEW vista_usuarios_permisos AS
SELECT 
    col.idcolaborador,
    col.usernick,
    col.habilitado,
    p.nombres,
    p.apellidos,
    car.idcargo,
    car.cargo,
    ar.idarea,
    ar.area,
    acc.modulo,
    acc.permisos,
    col.ultimoacceso
FROM colaboradores col
INNER JOIN contratoslaborales cl ON col.idcontratolaboral = cl.idcontratolaboral
INNER JOIN personas p ON cl.idpersona = p.idpersona
INNER JOIN cargos car ON cl.idcargo = car.idcargo
INNER JOIN areas ar ON car.idarea = ar.idarea
LEFT JOIN accesos acc ON car.idcargo = acc.idcargo
WHERE col.habilitado = 'S' AND col.estado = '1'
ORDER BY ar.area, car.cargo, col.usernick, acc.modulo;

-- Vista resumen de permisos por área
CREATE OR REPLACE VIEW vista_resumen_permisos_area AS
SELECT 
    a.idarea,
    a.area,
    c.idcargo,
    c.cargo,
    COUNT(acc.modulo) as total_modulos_acceso
FROM areas a
INNER JOIN cargos c ON a.idarea = c.idarea
LEFT JOIN accesos acc ON c.idcargo = acc.idcargo AND acc.permisos = 1
GROUP BY a.idarea, a.area, c.idcargo, c.cargo
ORDER BY a.area, c.cargo;

