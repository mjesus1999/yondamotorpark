-- =============================================================================
-- PATCH: permisos para Contabilidad, Logística y Legal
-- Fecha: 2026-05
-- BD: motorpark (USE motorpark; antes de ejecutar)
--
-- QUÉ HACE:
--   1. Asegura áreas Contabilidad, Legal y Logística.
--   2. Crea cargos nuevos solo si no existen (por nombre).
--   3. Inserta módulos en `accesos` con WHERE NOT EXISTS.
--
-- QUÉ NO HACE (importante):
--   - NO modifica colaboradores existentes (jhonfm, josuepy, etc.).
--   - NO borra ni actualiza permisos de otros cargos.
--   - NO cambia contratoslaborales ni contraseñas.
--
-- CARGOS PARA ASIGNAR AL CREAR USUARIOS:
--   Contabilidad  → Analista de Contabilidad
--   Logística     → Asistente de Logística  (operativo) o Jefe de Logística (supervisor)
--   Legal         → Asesor Legal
-- =============================================================================

USE motorpark;

-- ---------------------------------------------------------------------------
-- 1. Áreas (idempotente)
-- ---------------------------------------------------------------------------
INSERT IGNORE INTO areas (idarea, area) VALUES
    (3, 'Contabilidad'),
    (8, 'Legal'),
    (9, 'Logística');

-- ---------------------------------------------------------------------------
-- 2. Cargos nuevos (solo si el nombre no existe)
-- ---------------------------------------------------------------------------
INSERT INTO cargos (idarea, cargo)
SELECT a.idarea, 'Analista de Contabilidad'
FROM areas a
WHERE a.area = 'Contabilidad'
  AND NOT EXISTS (SELECT 1 FROM cargos c WHERE c.cargo = 'Analista de Contabilidad');

INSERT INTO cargos (idarea, cargo)
SELECT a.idarea, 'Asesor Legal'
FROM areas a
WHERE a.area = 'Legal'
  AND NOT EXISTS (SELECT 1 FROM cargos c WHERE c.cargo = 'Asesor Legal');

-- Jefe de Contabilidad y Logística pueden existir desde semilla; no se tocan.

-- ---------------------------------------------------------------------------
-- 3. CONTABILIDAD — Analista de Contabilidad
--    Finanzas: caja, arqueo, egresos, contratos, créditos, cobranza, clientes
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

-- Jefe de Contabilidad: completa módulos faltantes sin quitar los que ya tenga
INSERT INTO accesos (idcargo, modulo, permisos)
SELECT c.idcargo, x.modulo, 1
FROM cargos c
JOIN (
    SELECT 'Jefe de Contabilidad' AS cargo, 'clientes' AS modulo UNION ALL
    SELECT 'Jefe de Contabilidad', 'contratos' UNION ALL
    SELECT 'Jefe de Contabilidad', 'creditos' UNION ALL
    SELECT 'Jefe de Contabilidad', 'caja' UNION ALL
    SELECT 'Jefe de Contabilidad', 'arqueoCaja' UNION ALL
    SELECT 'Jefe de Contabilidad', 'egreso' UNION ALL
    SELECT 'Jefe de Contabilidad', 'cobranza' UNION ALL
    SELECT 'Jefe de Contabilidad', 'oc' UNION ALL
    SELECT 'Jefe de Contabilidad', 'compras' UNION ALL
    SELECT 'Jefe de Contabilidad', 'vehiculosAlContado'
) x ON c.cargo = x.cargo
WHERE NOT EXISTS (
    SELECT 1 FROM accesos a WHERE a.idcargo = c.idcargo AND a.modulo = x.modulo
);

-- ---------------------------------------------------------------------------
-- 4. LOGÍSTICA — Jefe y Asistente (solo agrega faltantes)
-- ---------------------------------------------------------------------------
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
-- 5. LEGAL — Asesor Legal
--    Contratos, clientes, cotización y créditos (contexto comercial/legal)
-- ---------------------------------------------------------------------------
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

-- ---------------------------------------------------------------------------
-- 6. Verificación (ejecutar después del patch)
-- ---------------------------------------------------------------------------
-- SELECT c.cargo, GROUP_CONCAT(a.modulo ORDER BY a.modulo SEPARATOR ', ') AS modulos
-- FROM cargos c
-- LEFT JOIN accesos a ON a.idcargo = c.idcargo AND a.permisos = 1
-- WHERE c.cargo IN (
--     'Analista de Contabilidad',
--     'Jefe de Contabilidad',
--     'Jefe de Logística',
--     'Asistente de Logística',
--     'Asesor Legal'
-- )
-- GROUP BY c.idcargo, c.cargo
-- ORDER BY c.cargo;
