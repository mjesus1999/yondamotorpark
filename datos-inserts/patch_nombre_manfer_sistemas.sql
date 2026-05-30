-- Nombre visible: "Manfer" (usuario stmas / antes jhonfm)
-- Ejecutar en Hostinger → cerrar sesión y volver a entrar.

USE u322322994_motorpark;

UPDATE personas p
JOIN contratoslaborales cl ON cl.idpersona = p.idpersona
JOIN colaboradores c ON c.idcontratolaboral = cl.idcontratolaboral
SET p.nombres = 'Manfer', p.apellidos = ''
WHERE c.usernick IN ('stmas', 'jhonfm');

SELECT c.usernick, p.nombres, p.apellidos
FROM colaboradores c
JOIN contratoslaborales cl ON c.idcontratolaboral = cl.idcontratolaboral
JOIN personas p ON cl.idpersona = p.idpersona
WHERE c.usernick IN ('stmas', 'jhonfm');
