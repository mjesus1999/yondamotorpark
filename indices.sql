
USE MOTORPARK;

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

SELECT * FROM ordenescompra;
SELECT * FROM pagos;
SELECT * FROM pagosOC;
SELECT * FROM compras;
SELECT * FROM clientes;

SELECT * FROM cotizaciones;

SELECT * FROM cronogramas;
SHOW COLUMNS FROM  cotizaciones;

SELECT * FROM locales;


SELECT * FROM marcas;
SELECT * FROM modelos;



SELECT * FROM distritos;
SELECT * FROM provincias;

SELECT *  FROM vehiculos;
SELECT * FROM modelos;

SELECT 
    v.idvehiculo,
    mc.marca,
    tv.tipovehiculo,
    m.modelo,
    m.anio,
    v.version,
    v.condicion,
    v.color,
    v.disponibilidad,
    v.placa,
    v.placarotativa,
    c.idcombustible,
    c.combustible,
    v.moneda,
    v.precioventa
FROM vehiculos v
INNER JOIN modelos m ON m.idmodelo = v.idmodelo
INNER JOIN marcas mc ON mc.idmarca = m.idmarca
INNER JOIN tipovehiculos tv ON tv.idtipovehiculo = m.idtipovehiculo
INNER JOIN combustibles c ON c.idcombustible = v.idcombustible;


