-- =============================================================================
-- Crear usuarios: Contab, Abogado, Ing Log
-- BD: motorpark
-- Fecha: 2026-05
--
-- NO modifica usuarios existentes (jhonfm, josuepy, alexandrah, aportilla).
-- Contraseña temporal inicial: Motorpark2026!
--   (cambiar al primer ingreso desde Usuarios → Editar)
--
-- Ejecutar en phpMyAdmin: USE motorpark; luego todo este archivo.
-- =============================================================================

USE motorpark;

SET @pwd := '$2y$10$hQpOivk4CzclgVYxPdNkaeHGaZAAPAoM/fXZmC2NXZ3HG5a69yM7S';

-- ---------------------------------------------------------------------------
-- 1. Personas (solo si no existen por DNI temporal)
--    Actualizar nombres/DNI reales después desde el módulo Usuarios.
-- ---------------------------------------------------------------------------
INSERT INTO personas (apellidos, nombres, tipodoc, nrodoc, genero, telprimario)
SELECT 'PENDIENTE', 'Contabilidad', 'CEX', '90000001', 'M', '900000001'
WHERE NOT EXISTS (SELECT 1 FROM personas WHERE tipodoc = 'CEX' AND nrodoc = '90000001');

INSERT INTO personas (apellidos, nombres, tipodoc, nrodoc, genero, telprimario)
SELECT 'PENDIENTE', 'Legal', 'CEX', '90000002', 'M', '900000002'
WHERE NOT EXISTS (SELECT 1 FROM personas WHERE tipodoc = 'CEX' AND nrodoc = '90000002');

INSERT INTO personas (apellidos, nombres, tipodoc, nrodoc, genero, telprimario)
SELECT 'PENDIENTE', 'Logistica', 'CEX', '90000003', 'M', '900000003'
WHERE NOT EXISTS (SELECT 1 FROM personas WHERE tipodoc = 'CEX' AND nrodoc = '90000003');

-- ---------------------------------------------------------------------------
-- 2. Contratos laborales (solo si el usernick aún no existe)
-- ---------------------------------------------------------------------------
INSERT INTO contratoslaborales (idpersona, idcargo, fechainicio, fechafin, tipocontrato)
SELECT p.idpersona, 20, CURDATE(), NULL, 'P'
FROM personas p
WHERE p.tipodoc = 'CEX' AND p.nrodoc = '90000001'
  AND NOT EXISTS (SELECT 1 FROM colaboradores WHERE usernick = 'Contab');

INSERT INTO contratoslaborales (idpersona, idcargo, fechainicio, fechafin, tipocontrato)
SELECT p.idpersona, 21, CURDATE(), NULL, 'P'
FROM personas p
WHERE p.tipodoc = 'CEX' AND p.nrodoc = '90000002'
  AND NOT EXISTS (SELECT 1 FROM colaboradores WHERE usernick = 'Abogado');

INSERT INTO contratoslaborales (idpersona, idcargo, fechainicio, fechafin, tipocontrato)
SELECT p.idpersona, 9, CURDATE(), NULL, 'P'
FROM personas p
WHERE p.tipodoc = 'CEX' AND p.nrodoc = '90000003'
  AND NOT EXISTS (SELECT 1 FROM colaboradores WHERE usernick = 'Ing Log');

-- ---------------------------------------------------------------------------
-- 3. Colaboradores (cuentas de acceso)
-- ---------------------------------------------------------------------------
INSERT INTO colaboradores (idcontratolaboral, usernick, userpassword, habilitado, restriccionhoraria, estado)
SELECT cl.idcontratolaboral, 'Contab', @pwd, 'S', 'N', '1'
FROM contratoslaborales cl
JOIN personas p ON cl.idpersona = p.idpersona
JOIN cargos cg ON cl.idcargo = cg.idcargo
WHERE p.tipodoc = 'CEX' AND p.nrodoc = '90000001'
  AND cg.cargo = 'Analista de Contabilidad'
  AND NOT EXISTS (SELECT 1 FROM colaboradores c WHERE c.usernick = 'Contab')
ORDER BY cl.idcontratolaboral DESC
LIMIT 1;

INSERT INTO colaboradores (idcontratolaboral, usernick, userpassword, habilitado, restriccionhoraria, estado)
SELECT cl.idcontratolaboral, 'Abogado', @pwd, 'S', 'N', '1'
FROM contratoslaborales cl
JOIN personas p ON cl.idpersona = p.idpersona
JOIN cargos cg ON cl.idcargo = cg.idcargo
WHERE p.tipodoc = 'CEX' AND p.nrodoc = '90000002'
  AND cg.cargo = 'Asesor Legal'
  AND NOT EXISTS (SELECT 1 FROM colaboradores c WHERE c.usernick = 'Abogado')
ORDER BY cl.idcontratolaboral DESC
LIMIT 1;

INSERT INTO colaboradores (idcontratolaboral, usernick, userpassword, habilitado, restriccionhoraria, estado)
SELECT cl.idcontratolaboral, 'Ing Log', @pwd, 'S', 'N', '1'
FROM contratoslaborales cl
JOIN personas p ON cl.idpersona = p.idpersona
JOIN cargos cg ON cl.idcargo = cg.idcargo
WHERE p.tipodoc = 'CEX' AND p.nrodoc = '90000003'
  AND cg.cargo = 'Asistente de Logística'
  AND NOT EXISTS (SELECT 1 FROM colaboradores c WHERE c.usernick = 'Ing Log')
ORDER BY cl.idcontratolaboral DESC
LIMIT 1;

-- ---------------------------------------------------------------------------
-- 4. Verificación
-- ---------------------------------------------------------------------------
SELECT 
    c.idcolaborador,
    c.usernick,
    CONCAT(p.nombres, ' ', p.apellidos) AS nombre,
    cg.cargo,
    a.area,
    c.habilitado
FROM colaboradores c
JOIN contratoslaborales cl ON c.idcontratolaboral = cl.idcontratolaboral
JOIN personas p ON cl.idpersona = p.idpersona
JOIN cargos cg ON cl.idcargo = cg.idcargo
JOIN areas a ON cg.idarea = a.idarea
WHERE c.usernick IN ('Contab', 'Abogado', 'Ing Log', 'jhonfm', 'josuepy', 'alexandrah', 'aportilla')
ORDER BY c.idcolaborador;
