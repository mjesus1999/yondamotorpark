-- Nombre visible: "Leticia" (usuario Ing Log)
-- Ejecutar en Hostinger → cerrar sesión y volver a entrar.

USE u322322994_motorpark;

UPDATE personas p
JOIN contratoslaborales cl ON cl.idpersona = p.idpersona
JOIN colaboradores c ON c.idcontratolaboral = cl.idcontratolaboral
SET p.nombres = 'Leticia', p.apellidos = ''
WHERE c.usernick = 'Ing Log';

SELECT c.usernick, p.nombres, p.apellidos
FROM colaboradores c
JOIN contratoslaborales cl ON c.idcontratolaboral = cl.idcontratolaboral
JOIN personas p ON cl.idpersona = p.idpersona
WHERE c.usernick = 'Ing Log';
