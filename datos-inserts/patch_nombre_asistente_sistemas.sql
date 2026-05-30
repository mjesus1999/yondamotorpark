-- Nombre visible: "Asistente" (usuario asissistemas / antes josuepy)
-- Ejecutar en Hostinger: phpMyAdmin → SQL → USE u322322994_motorpark;
-- Luego: cerrar sesión en la web y volver a entrar.

USE u322322994_motorpark;

UPDATE personas p
JOIN contratoslaborales cl ON cl.idpersona = p.idpersona
JOIN colaboradores c ON c.idcontratolaboral = cl.idcontratolaboral
SET p.nombres = 'Asistente', p.apellidos = ''
WHERE c.usernick IN ('asissistemas', 'josuepy');

SELECT c.usernick, p.nombres, p.apellidos
FROM colaboradores c
JOIN contratoslaborales cl ON c.idcontratolaboral = cl.idcontratolaboral
JOIN personas p ON cl.idpersona = p.idpersona
WHERE c.usernick IN ('asissistemas', 'josuepy');
