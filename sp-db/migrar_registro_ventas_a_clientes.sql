-- Migración: registrar como PERSONA/CLIENTE los DNIs que existen en
-- registro_ventas_vehiculares pero NO existen en personas/clientes.
--
-- IMPORTANTE:
-- - Esto NO crea contratos ni cronogramas reales del sistema (tablas contratos/cronogramas).
-- - Solo crea el "cliente" para que Caja lo encuentre por DNI y puedas cobrar por conceptos.
--
-- Ejecutar en VPS:
--   mysql -u yhondb -p motorpark < /var/www/yondamotorpark/sp-db/migrar_registro_ventas_a_clientes.sql
--
-- Notas de calidad de datos:
-- - personas.telprimario es NOT NULL y exige 9 dígitos. Si el Excel no trae 9 dígitos, el DNI se omite.
-- - personas.genero es NOT NULL: se pone 'M' por defecto (luego se puede editar en UI).
-- - apellidos/nombres: como el Excel viene con "nombre completo" en un solo campo,
--   guardamos todo en nombres y apellidos='-' para cumplir NOT NULL.

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

START TRANSACTION;

-- 1) Insertar PERSONAS faltantes (solo DNIs con teléfono_1 de 9 dígitos).
INSERT INTO personas (
    iddistrito,
    apellidos,
    nombres,
    tipodoc,
    nrodoc,
    genero,
    direccion,
    referencia,
    telprimario,
    telalternativo,
    latitud,
    longitud
)
SELECT
    NULL AS iddistrito,
    '-' AS apellidos,
    LEFT(TRIM(r.nombre_cliente), 70) AS nombres,
    'DNI' AS tipodoc,
    r.dni_cliente AS nrodoc,
    'M' AS genero,
    LEFT(TRIM(r.direccion), 200) AS direccion,
    NULL AS referencia,
    -- limpiar espacios; si no queda en 9 dígitos, se filtra en WHERE
    REPLACE(REPLACE(REPLACE(TRIM(r.telefono_1), ' ', ''), '-', ''), '+', '') AS telprimario,
    NULL AS telalternativo,
    NULL AS latitud,
    NULL AS longitud
FROM registro_ventas_vehiculares r
LEFT JOIN personas p
    ON p.tipodoc = 'DNI' AND p.nrodoc = r.dni_cliente
WHERE p.idpersona IS NULL
  AND r.dni_cliente IS NOT NULL
  AND r.dni_cliente REGEXP '^[0-9]{8}$'
  AND r.telefono_1 IS NOT NULL
  AND REPLACE(REPLACE(REPLACE(TRIM(r.telefono_1), ' ', ''), '-', ''), '+', '') REGEXP '^[0-9]{9}$'
GROUP BY r.dni_cliente;

-- 2) Insertar CLIENTES faltantes para esas personas (tipocliente='P').
INSERT INTO clientes (
    idpersona,
    idempresa,
    idcolregistra,
    idcolactualiza,
    tipocliente,
    estado
)
SELECT
    p.idpersona,
    NULL AS idempresa,
    NULL AS idcolregistra,
    NULL AS idcolactualiza,
    'P' AS tipocliente,
    'ACT' AS estado
FROM registro_ventas_vehiculares r
INNER JOIN personas p
    ON p.tipodoc = 'DNI' AND p.nrodoc = r.dni_cliente
LEFT JOIN clientes c
    ON c.idpersona = p.idpersona AND c.tipocliente = 'P'
WHERE c.idcliente IS NULL
GROUP BY p.idpersona;

-- 3) Reporte rápido (cuántos DNIs del Excel siguen sin existir como cliente).
SELECT
  COUNT(*) AS dnIs_excel_sin_cliente
FROM (
  SELECT r.dni_cliente
  FROM registro_ventas_vehiculares r
  WHERE r.dni_cliente REGEXP '^[0-9]{8}$'
  GROUP BY r.dni_cliente
) x
LEFT JOIN personas p ON p.tipodoc='DNI' AND p.nrodoc=x.dni_cliente
LEFT JOIN clientes c ON c.idpersona=p.idpersona AND c.tipocliente='P'
WHERE c.idcliente IS NULL;

COMMIT;

