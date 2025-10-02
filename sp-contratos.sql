USE MOTORPARK;


SELECT * FROM cotizaciones;
SELECT * FROM contratos;

SHOW COLUMNS FROM  cotizaciones;
SELECT * FROM cotizaciones LIMIT 1;
SELECT * FROM contratos;
SELECT * FROM cronogramas LIMIT 5;

UPDATE cotizaciones SET estadocotizacion = 'CONT';

SELECT * FROM cronogramas;
DELETE FROM cotizaciones;
DELETE FROM contratos;
DELETE FROM cronogramas;


DELETE FROM pagos;



SELECT idcotizacion, precioventa, inicial, numcuotas, valorcuota, moneda 
              FROM cotizaciones
              WHERE idcotizacion = 16;