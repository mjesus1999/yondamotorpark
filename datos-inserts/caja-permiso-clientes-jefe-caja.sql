-- Opcional: que "Jefe de Caja" (idcargo = 17) pueda usar el módulo Clientes.
-- Esto hace que en el menú aparezca "Clientes" y puedan registrar/buscar desde ese módulo.
-- Ejecutar SOLO si tu política permite que caja cree/edite clientes.

USE motorpark;

INSERT INTO accesos (idcargo, modulo, permisos)
VALUES (17, ''clientes'', 1)
ON DUPLICATE KEY UPDATE permisos = VALUES(permisos);
