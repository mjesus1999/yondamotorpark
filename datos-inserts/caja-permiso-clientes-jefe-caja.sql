-- Opcional: que "Jefe de Caja" pueda usar el módulo Clientes.
-- Ejecutar SOLO si tu política permite que caja cree/edite clientes.
-- Requiere la fila de cargo 'Jefe de Caja' en `cargos` (ver cargos-areas_semilla_motorpark.sql si hace falta).

-- USE motorpark; -- hosting: seleccionar BD en phpMyAdmin

INSERT INTO accesos (idcargo, modulo, permisos)
SELECT c.idcargo, 'clientes', 1
FROM cargos c
WHERE c.cargo = 'Jefe de Caja'
  AND NOT EXISTS (
      SELECT 1 FROM accesos a WHERE a.idcargo = c.idcargo AND a.modulo = 'clientes'
  )
LIMIT 1;
