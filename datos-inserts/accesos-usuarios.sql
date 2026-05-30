-- BD seleccionada en phpMyAdmin.
-- Requiere filas en `cargos` para cada nombre usado abajo. Si falla #1452, ejecutar antes:
--   datos-inserts/cargos-areas_semilla_motorpark.sql
--
-- Los INSERT resuelven idcargo por nombre de cargo (no por número), para no depender del AUTO_INCREMENT.

-- Jefe de Caja
INSERT INTO accesos (idcargo, modulo, permisos)
SELECT c.idcargo, x.modulo, 1
FROM cargos c
JOIN (
    SELECT 'Jefe de Caja' AS cargo, 'caja' AS modulo UNION ALL
    SELECT 'Jefe de Caja', 'auth'
) x ON c.cargo = x.cargo
WHERE NOT EXISTS (
    SELECT 1 FROM accesos a WHERE a.idcargo = c.idcargo AND a.modulo = x.modulo
);

-- Jefe de Logística
INSERT INTO accesos (idcargo, modulo, permisos)
SELECT c.idcargo, x.modulo, 1
FROM cargos c
JOIN (
    SELECT 'Jefe de Logística' AS cargo, 'oc' AS modulo UNION ALL
    SELECT 'Jefe de Logística', 'compras' UNION ALL
    SELECT 'Jefe de Logística', 'vehiculos' UNION ALL
    SELECT 'Jefe de Logística', 'recepcionVehiculos' UNION ALL
    SELECT 'Jefe de Logística', 'marcas' UNION ALL
    SELECT 'Jefe de Logística', 'concesionarios'
) x ON c.cargo = x.cargo
WHERE NOT EXISTS (
    SELECT 1 FROM accesos a WHERE a.idcargo = c.idcargo AND a.modulo = x.modulo
);

-- Asistente de Logística
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

-- Jefe de sistemas (nombre histórico en BD motorpark: minúscula en "sistemas")
INSERT INTO accesos (idcargo, modulo, permisos)
SELECT c.idcargo, x.modulo, 1
FROM cargos c
JOIN (
    SELECT 'Jefe de sistemas' AS cargo, 'concesionarios' AS modulo UNION ALL
    SELECT 'Jefe de sistemas', 'locales' UNION ALL
    SELECT 'Jefe de sistemas', 'clientes' UNION ALL
    SELECT 'Jefe de sistemas', 'marcas' UNION ALL
    SELECT 'Jefe de sistemas', 'vehiculos' UNION ALL
    SELECT 'Jefe de sistemas', 'recepcionVehiculos' UNION ALL
    SELECT 'Jefe de sistemas', 'oc' UNION ALL
    SELECT 'Jefe de sistemas', 'compras' UNION ALL
    SELECT 'Jefe de sistemas', 'formatoCotizacion' UNION ALL
    SELECT 'Jefe de sistemas', 'cotizacion' UNION ALL
    SELECT 'Jefe de sistemas', 'contratos' UNION ALL
    SELECT 'Jefe de sistemas', 'vehiculosAlContado' UNION ALL
    SELECT 'Jefe de sistemas', 'usuarios' UNION ALL
    SELECT 'Jefe de sistemas', 'caja' UNION ALL
    SELECT 'Jefe de sistemas', 'creditos' UNION ALL
    SELECT 'Jefe de sistemas', 'egreso' UNION ALL
    SELECT 'Jefe de sistemas', 'arqueoCaja' UNION ALL
    SELECT 'Jefe de sistemas', 'cobranza' UNION ALL
    SELECT 'Jefe de sistemas', 'auth'
) x ON c.cargo = x.cargo
WHERE NOT EXISTS (
    SELECT 1 FROM accesos a WHERE a.idcargo = c.idcargo AND a.modulo = x.modulo
);

-- Jefe de Recursos Humanos
INSERT INTO accesos (idcargo, modulo, permisos)
SELECT c.idcargo, x.modulo, 1
FROM cargos c
JOIN (
    SELECT 'Jefe de Recursos Humanos' AS cargo, 'usuarios' AS modulo UNION ALL
    SELECT 'Jefe de Recursos Humanos', 'auth'
) x ON c.cargo = x.cargo
WHERE NOT EXISTS (
    SELECT 1 FROM accesos a WHERE a.idcargo = c.idcargo AND a.modulo = x.modulo
);

-- Jefe de Ventas
INSERT INTO accesos (idcargo, modulo, permisos)
SELECT c.idcargo, x.modulo, 1
FROM cargos c
JOIN (
    SELECT 'Jefe de Ventas' AS cargo, 'clientes' AS modulo UNION ALL
    SELECT 'Jefe de Ventas', 'vehiculos' UNION ALL
    SELECT 'Jefe de Ventas', 'cotizacion' UNION ALL
    SELECT 'Jefe de Ventas', 'contratos' UNION ALL
    SELECT 'Jefe de Ventas', 'vehiculosAlContado'
) x ON c.cargo = x.cargo
WHERE NOT EXISTS (
    SELECT 1 FROM accesos a WHERE a.idcargo = c.idcargo AND a.modulo = x.modulo
);

-- Asesor de Ventas
INSERT INTO accesos (idcargo, modulo, permisos)
SELECT c.idcargo, x.modulo, 1
FROM cargos c
JOIN (
    SELECT 'Asesor de Ventas' AS cargo, 'clientes' AS modulo UNION ALL
    SELECT 'Asesor de Ventas', 'cotizacion'
) x ON c.cargo = x.cargo
WHERE NOT EXISTS (
    SELECT 1 FROM accesos a WHERE a.idcargo = c.idcargo AND a.modulo = x.modulo
);

-- Jefe de Cobranza
INSERT INTO accesos (idcargo, modulo, permisos)
SELECT c.idcargo, x.modulo, 1
FROM cargos c
JOIN (
    SELECT 'Jefe de Cobranza' AS cargo, 'cobranza' AS modulo UNION ALL
    SELECT 'Jefe de Cobranza', 'contratos' UNION ALL
    SELECT 'Jefe de Cobranza', 'clientes'
) x ON c.cargo = x.cargo
WHERE NOT EXISTS (
    SELECT 1 FROM accesos a WHERE a.idcargo = c.idcargo AND a.modulo = x.modulo
);

-- Analista de Contabilidad (ver patch_accesos_contabilidad_logistica_legal_2026.sql)
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

-- Asesor Legal
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
