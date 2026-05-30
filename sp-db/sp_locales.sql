-- USE motorpark; -- deshabilitado para hosting compartido (seleccionar BD antes en phpMyAdmin)

DROP PROCEDURE IF EXISTS sp_getAll_locales;

DELIMITER $$

CREATE PROCEDURE sp_getAll_locales()
BEGIN
    SELECT 
        loc.idlocal,
        CONCAT(loc.tienda, ' - ', pro.provincia, ' (', dep.departamento, ')') AS local

    FROM locales loc
    INNER JOIN distritos dis ON loc.iddistrito = dis.iddistrito
    INNER JOIN provincias pro ON dis.idprovincia = pro.idprovincia
    INNER JOIN departamentos dep ON pro.iddepartamento = dep.iddepartamento;
    -- locales puede no tener columna estado en algunos dumps; listar todos
END $$

DELIMITER ;

-- Pruebas locales comentadas (evitar errores al importar en hosting)
-- CALL sp_getAll_locales();
-- SELECT * FROM vehiculos;
-- SELECT * FROM compras;
-- SHOW columns FROM compras;
-- ALTER TABLE compras MODIFY COLUMN numdocumento VARCHAR(30) NOT NULL;
-- SELECT * FROM vehiculos WHERE idvehiculo = 98;
