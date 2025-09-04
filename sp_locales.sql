USE motorpark;

DROP PROCEDURE IF EXISTS sp_getAll_locales;

DELIMITER / /

CREATE PROCEDURE sp_getAll_locales()
BEGIN
    SELECT 
        loc.idlocal,
        CONCAT(loc.tienda, ' - ', pro.provincia, ' (', dep.departamento, ')') AS local

    FROM locales loc
    INNER JOIN distritos dis ON loc.iddistrito = dis.iddistrito
    INNER JOIN provincias pro ON dis.idprovincia = pro.idprovincia
    INNER JOIN departamentos dep ON pro.iddepartamento = dep.iddepartamento
    WHERE loc.estado = 'ACT';
END //

DELIMITER;

CALL sp_getAll_locales ()

SELECT * FROM vehiculos;

SELECT * FROM compras;

SHOW columns FROM compras;

ALTER TABLE compras MODIFY COLUMN numdocumento VARCHAR(30) NOT NULL

SELECT * FROM vehiculos WHERE idvehiculo = 98;