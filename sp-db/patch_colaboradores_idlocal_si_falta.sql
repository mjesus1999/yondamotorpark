-- Ejecutar en phpMyAdmin con la BD seleccionada si vw_usuarios falla con:
--   #1054 - No se reconoce la columna 'col.idlocal' en SELECT
-- (instalaciones que importaron IMPORTAR_OK antes de incluir idlocal en colaboradores).
-- Si la columna o la FK ya existen, ignorar el error correspondiente y seguir.

ALTER TABLE colaboradores
    ADD COLUMN idlocal INT NULL AFTER idcontratolaboral;

ALTER TABLE colaboradores
    ADD CONSTRAINT fk_idlocal_col FOREIGN KEY (idlocal) REFERENCES locales (idlocal);
