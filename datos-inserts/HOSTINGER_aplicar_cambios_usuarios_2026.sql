-- =============================================================================
-- HOSTINGER: aplicar cambios de usuarios, cargos y permisos
-- BD: u322322994_motorpark
-- Ejecutar en phpMyAdmin (pestaña SQL, BD seleccionada en panel izquierdo)
--
-- NO borra usuarios existentes. Solo agrega permisos, 3 usuarios nuevos
-- y renombra 4 usernicks + cargos acordados.
-- =============================================================================

USE u322322994_motorpark;

-- ---------------------------------------------------------------------------
-- 1. Áreas y cargos nuevos
-- ---------------------------------------------------------------------------
INSERT IGNORE INTO areas (idarea, area) VALUES
    (3, 'Contabilidad'),
    (8, 'Legal'),
    (9, 'Logística');

INSERT INTO cargos (idarea, cargo)
SELECT 3, 'Analista de Contabilidad'
WHERE NOT EXISTS (SELECT 1 FROM cargos WHERE cargo = 'Analista de Contabilidad');

INSERT INTO cargos (idarea, cargo)
SELECT 8, 'Asesor Legal'
WHERE NOT EXISTS (SELECT 1 FROM cargos WHERE cargo = 'Asesor Legal');

-- Renombrar cargos (solo si aún tienen el nombre viejo)
UPDATE cargos SET cargo = 'Sistemas' WHERE cargo = 'Jefe de sistemas';
UPDATE cargos SET cargo = 'Asistente de sistemas' WHERE cargo = 'Analista desarrollador';
UPDATE cargos SET cargo = 'Cajera' WHERE cargo = 'Jefe de Caja';
UPDATE cargos SET cargo = 'Administración' WHERE cargo = 'Jefatura';

-- josuepy → cargo Asistente de sistemas
UPDATE contratoslaborales cl
JOIN colaboradores c ON c.idcontratolaboral = cl.idcontratolaboral
SET cl.idcargo = (SELECT idcargo FROM cargos WHERE cargo = 'Asistente de sistemas' LIMIT 1)
WHERE c.usernick = 'josuepy';

-- ---------------------------------------------------------------------------
-- 2. Permisos Contabilidad, Legal, Logística
-- ---------------------------------------------------------------------------
INSERT INTO accesos (idcargo, modulo, permisos)
SELECT c.idcargo, x.modulo, 1
FROM cargos c
JOIN (
    SELECT 'Analista de Contabilidad' AS cargo, 'clientes' AS modulo UNION ALL
    SELECT 'Analista de Contabilidad', 'contratos' UNION ALL
    SELECT 'Analista de Contabilidad', 'creditos' UNION ALL
    SELECT 'Analista de Contabilidad', 'caja' UNION ALL
    SELECT 'Analista de Contabilidad', 'arqueoCaja' UNION ALL
    SELECT 'Analista de Contabilidad', 'egreso' UNION ALL
    SELECT 'Analista de Contabilidad', 'cobranza'
) x ON c.cargo = x.cargo
WHERE NOT EXISTS (
    SELECT 1 FROM accesos a WHERE a.idcargo = c.idcargo AND a.modulo = x.modulo
);

INSERT INTO accesos (idcargo, modulo, permisos)
SELECT c.idcargo, x.modulo, 1
FROM cargos c
JOIN (
    SELECT 'Asesor Legal' AS cargo, 'contratos' AS modulo UNION ALL
    SELECT 'Asesor Legal', 'clientes' UNION ALL
    SELECT 'Asesor Legal', 'cotizacion' UNION ALL
    SELECT 'Asesor Legal', 'creditos'
) x ON c.cargo = x.cargo
WHERE NOT EXISTS (
    SELECT 1 FROM accesos a WHERE a.idcargo = c.idcargo AND a.modulo = x.modulo
);

INSERT INTO accesos (idcargo, modulo, permisos)
SELECT c.idcargo, x.modulo, 1
FROM cargos c
JOIN (
    SELECT 'Jefe de Logística' AS cargo, 'oc' AS modulo UNION ALL
    SELECT 'Jefe de Logística', 'compras' UNION ALL
    SELECT 'Jefe de Logística', 'vehiculos' UNION ALL
    SELECT 'Jefe de Logística', 'recepcionVehiculos' UNION ALL
    SELECT 'Jefe de Logística', 'marcas' UNION ALL
    SELECT 'Jefe de Logística', 'concesionarios' UNION ALL
    SELECT 'Jefe de Logística', 'locales'
) x ON c.cargo = x.cargo
WHERE NOT EXISTS (
    SELECT 1 FROM accesos a WHERE a.idcargo = c.idcargo AND a.modulo = x.modulo
);

INSERT INTO accesos (idcargo, modulo, permisos)
SELECT c.idcargo, x.modulo, 1
FROM cargos c
JOIN (
    SELECT 'Asistente de Logística' AS cargo, 'oc' AS modulo UNION ALL
    SELECT 'Asistente de Logística', 'compras' UNION ALL
    SELECT 'Asistente de Logística', 'vehiculos' UNION ALL
    SELECT 'Asistente de Logística', 'recepcionVehiculos' UNION ALL
    SELECT 'Asistente de Logística', 'marcas'
) x ON c.cargo = x.cargo
WHERE NOT EXISTS (
    SELECT 1 FROM accesos a WHERE a.idcargo = c.idcargo AND a.modulo = x.modulo
);
-- ---------------------------------------------------------------------------
-- 3. Crear usuarios nuevos: Contab, Abogado, Ing Log
-- Contraseña temporal: Motorpark2026!
-- ---------------------------------------------------------------------------
SET @pwd := '$2y$10$hQpOivk4CzclgVYxPdNkaeHGaZAAPAoM/fXZmC2NXZ3HG5a69yM7S';

INSERT INTO personas (apellidos, nombres, tipodoc, nrodoc, genero, telprimario)
SELECT 'PENDIENTE', 'Contabilidad', 'CEX', '90000001', 'M', '900000001'
WHERE NOT EXISTS (SELECT 1 FROM personas WHERE tipodoc = 'CEX' AND nrodoc = '90000001');

INSERT INTO personas (apellidos, nombres, tipodoc, nrodoc, genero, telprimario)
SELECT 'PENDIENTE', 'Legal', 'CEX', '90000002', 'M', '900000002'
WHERE NOT EXISTS (SELECT 1 FROM personas WHERE tipodoc = 'CEX' AND nrodoc = '90000002');

INSERT INTO personas (apellidos, nombres, tipodoc, nrodoc, genero, telprimario)
SELECT 'PENDIENTE', 'Logistica', 'CEX', '90000003', 'M', '900000003'
WHERE NOT EXISTS (SELECT 1 FROM personas WHERE tipodoc = 'CEX' AND nrodoc = '90000003');

INSERT INTO contratoslaborales (idpersona, idcargo, fechainicio, fechafin, tipocontrato)
SELECT p.idpersona, cg.idcargo, CURDATE(), NULL, 'P'
FROM personas p
JOIN cargos cg ON cg.cargo = 'Analista de Contabilidad'
WHERE p.tipodoc = 'CEX' AND p.nrodoc = '90000001'
  AND NOT EXISTS (SELECT 1 FROM colaboradores WHERE usernick = 'Contab');

INSERT INTO contratoslaborales (idpersona, idcargo, fechainicio, fechafin, tipocontrato)
SELECT p.idpersona, cg.idcargo, CURDATE(), NULL, 'P'
FROM personas p
JOIN cargos cg ON cg.cargo = 'Asesor Legal'
WHERE p.tipodoc = 'CEX' AND p.nrodoc = '90000002'
  AND NOT EXISTS (SELECT 1 FROM colaboradores WHERE usernick = 'Abogado');

INSERT INTO contratoslaborales (idpersona, idcargo, fechainicio, fechafin, tipocontrato)
SELECT p.idpersona, cg.idcargo, CURDATE(), NULL, 'P'
FROM personas p
JOIN cargos cg ON cg.cargo = 'Asistente de Logística'
WHERE p.tipodoc = 'CEX' AND p.nrodoc = '90000003'
  AND NOT EXISTS (SELECT 1 FROM colaboradores WHERE usernick = 'Ing Log');

INSERT INTO colaboradores (idcontratolaboral, usernick, userpassword, habilitado, restriccionhoraria, estado)
SELECT cl.idcontratolaboral, 'Contab', @pwd, 'S', 'N', '1'
FROM contratoslaborales cl
JOIN personas p ON cl.idpersona = p.idpersona
JOIN cargos cg ON cl.idcargo = cg.idcargo
WHERE p.tipodoc = 'CEX' AND p.nrodoc = '90000001' AND cg.cargo = 'Analista de Contabilidad'
  AND NOT EXISTS (SELECT 1 FROM colaboradores c WHERE c.usernick = 'Contab')
ORDER BY cl.idcontratolaboral DESC LIMIT 1;

INSERT INTO colaboradores (idcontratolaboral, usernick, userpassword, habilitado, restriccionhoraria, estado)
SELECT cl.idcontratolaboral, 'Abogado', @pwd, 'S', 'N', '1'
FROM contratoslaborales cl
JOIN personas p ON cl.idpersona = p.idpersona
JOIN cargos cg ON cl.idcargo = cg.idcargo
WHERE p.tipodoc = 'CEX' AND p.nrodoc = '90000002' AND cg.cargo = 'Asesor Legal'
  AND NOT EXISTS (SELECT 1 FROM colaboradores c WHERE c.usernick = 'Abogado')
ORDER BY cl.idcontratolaboral DESC LIMIT 1;

INSERT INTO colaboradores (idcontratolaboral, usernick, userpassword, habilitado, restriccionhoraria, estado)
SELECT cl.idcontratolaboral, 'Ing Log', @pwd, 'S', 'N', '1'
FROM contratoslaborales cl
JOIN personas p ON cl.idpersona = p.idpersona
JOIN cargos cg ON cl.idcargo = cg.idcargo
WHERE p.tipodoc = 'CEX' AND p.nrodoc = '90000003' AND cg.cargo = 'Asistente de Logística'
  AND NOT EXISTS (SELECT 1 FROM colaboradores c WHERE c.usernick = 'Ing Log')
ORDER BY cl.idcontratolaboral DESC LIMIT 1;

-- ---------------------------------------------------------------------------
-- 4. Renombrar usernicks (login)
-- ---------------------------------------------------------------------------
UPDATE colaboradores SET usernick = 'stmas'       WHERE usernick = 'jhonfm';
UPDATE colaboradores SET usernick = 'asissistemas' WHERE usernick = 'josuepy';
UPDATE colaboradores SET usernick = 'maizha'      WHERE usernick = 'alexandrah';
UPDATE colaboradores SET usernick = 'Admin'       WHERE usernick = 'aportilla';

-- ---------------------------------------------------------------------------
-- 5. Nombres visibles en menú y panel (personas)
--    Tras ejecutar: cerrar sesión y volver a entrar.
-- ---------------------------------------------------------------------------
UPDATE personas p
JOIN contratoslaborales cl ON cl.idpersona = p.idpersona
JOIN colaboradores c ON c.idcontratolaboral = cl.idcontratolaboral
SET p.nombres = 'Manfer', p.apellidos = ''
WHERE c.usernick IN ('stmas', 'jhonfm');

UPDATE personas p
JOIN contratoslaborales cl ON cl.idpersona = p.idpersona
JOIN colaboradores c ON c.idcontratolaboral = cl.idcontratolaboral
SET p.nombres = 'Asistente', p.apellidos = ''
WHERE c.usernick IN ('asissistemas', 'josuepy');

UPDATE personas p
JOIN contratoslaborales cl ON cl.idpersona = p.idpersona
JOIN colaboradores c ON c.idcontratolaboral = cl.idcontratolaboral
SET p.nombres = 'Maiza', p.apellidos = ''
WHERE c.usernick IN ('maizha', 'alexandrah');

UPDATE personas p
JOIN contratoslaborales cl ON cl.idpersona = p.idpersona
JOIN colaboradores c ON c.idcontratolaboral = cl.idcontratolaboral
SET p.nombres = 'Ariana', p.apellidos = ''
WHERE c.usernick IN ('Admin', 'aportilla');

UPDATE personas p
JOIN contratoslaborales cl ON cl.idpersona = p.idpersona
JOIN colaboradores c ON c.idcontratolaboral = cl.idcontratolaboral
SET p.nombres = 'Leticia', p.apellidos = ''
WHERE c.usernick = 'Ing Log';

-- ---------------------------------------------------------------------------
-- 6. Verificación
-- ---------------------------------------------------------------------------
SELECT COUNT(*) AS total_usuarios FROM colaboradores;

SELECT usernick, habilitado FROM colaboradores ORDER BY idcolaborador;
