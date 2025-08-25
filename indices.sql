USE MOTORPARK2;

-- AGREGANDO INDICES
ALTER TABLE pagos ADD INDEX idx_idcronograma (idcronograma);
ALTER TABLE cronogramas ADD INDEX idx_idcontrato (idcontrato);
ALTER TABLE contratos ADD INDEX idx_idocntrato_contratos (idcontrato);
ALTER TABLE cotizaciones ADD INDEX idx_idcotizacion_cotizaciones (idcotizacion);
ALTER TABLE contratos ADD INDEX idx_idcotizacion_contratos (idcotizacion)

-- Indice en la columna 'idlocal' de la tabla contratos
ALTER TABLE contratos ADD INDEX idx_idlocal (idlocal);

SELECT * FROM contratos;


    SHOW INDEX FROM contratos;