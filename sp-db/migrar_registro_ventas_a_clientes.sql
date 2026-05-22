-- Migración masiva: crear/activar clientes desde registro_ventas_vehiculares (DNI).
-- Esto evita registrar cliente manualmente uno por uno en Caja.
--
-- Ejecutar en phpMyAdmin sobre BD motorpark.

START TRANSACTION;

-- 1) Personas faltantes por DNI (toma la venta más reciente por DNI).
INSERT INTO personas (
    iddistrito, apellidos, nombres, tipodoc, nrodoc, genero,
    fechanac, estadocivil, email, direccion, referencia, latitud, longitud,
    telprimario, telalternativo
)
SELECT
    NULL,
    '-',
    LEFT(TRIM(r.nombre_cliente), 70),
    'DNI',
    r.dni_cliente,
    'M',
    NULL, NULL, NULL,
    LEFT(TRIM(COALESCE(r.direccion, 'SIN DIRECCION')), 200),
    NULL, NULL, NULL,
    CASE
      WHEN LENGTH(REGEXP_REPLACE(COALESCE(r.telefono_1, ''), '[^0-9]', '')) >= 9
        THEN RIGHT(REGEXP_REPLACE(COALESCE(r.telefono_1, ''), '[^0-9]', ''), 9)
      ELSE '999999999'
    END,
    NULL
FROM registro_ventas_vehiculares r
LEFT JOIN personas p
  ON p.tipodoc = 'DNI'
 AND p.nrodoc = r.dni_cliente
WHERE r.id = (
    SELECT x.id
    FROM registro_ventas_vehiculares x
    WHERE x.dni_cliente = r.dni_cliente
    ORDER BY x.fecha_venta DESC, x.id DESC
    LIMIT 1
)
AND r.dni_cliente REGEXP '^[0-9]{8}$'
AND p.idpersona IS NULL;

-- 2) Clientes faltantes.
INSERT INTO clientes (idpersona, idempresa, idcolregistra, idcolactualiza, tipocliente, estado)
SELECT p.idpersona, NULL, NULL, NULL, 'P', 'ACT'
FROM personas p
LEFT JOIN clientes c ON c.idpersona = p.idpersona
WHERE p.tipodoc = 'DNI'
  AND EXISTS (SELECT 1 FROM registro_ventas_vehiculares r WHERE r.dni_cliente = p.nrodoc)
  AND c.idcliente IS NULL;

-- 3) Activar clientes existentes de esos DNIs.
UPDATE clientes c
JOIN personas p ON p.idpersona = c.idpersona
SET c.estado = 'ACT',
    c.tipocliente = 'P',
    c.idempresa = NULL
WHERE p.tipodoc = 'DNI'
  AND EXISTS (SELECT 1 FROM registro_ventas_vehiculares r WHERE r.dni_cliente = p.nrodoc);

COMMIT;

-- Verificación: DNIs del registro que todavía no tienen cliente ACT.
SELECT COUNT(*) AS dnis_sin_cliente_activo
FROM (
    SELECT DISTINCT r.dni_cliente
    FROM registro_ventas_vehiculares r
    WHERE r.dni_cliente REGEXP '^[0-9]{8}$'
) x
LEFT JOIN personas p ON p.tipodoc = 'DNI' AND p.nrodoc = x.dni_cliente
LEFT JOIN clientes c ON c.idpersona = p.idpersona AND c.estado = 'ACT'
WHERE c.idcliente IS NULL;