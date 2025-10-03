
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

SELECT * FROM personas;
DELETE FROM pagos;



SELECT idcotizacion, precioventa, inicial, numcuotas, valorcuota, moneda 
              FROM cotizaciones
              WHERE idcotizacion = 16;



              SELECT 
              idcotizacion,
              tipocotizacion,
              CONCAT(vehiculo, ' / ', color) AS vehiculo,
              precioventa,
              moneda,
              inicial,
              nombrecliente,
              documento,
              telefono,
              direccion,
              numcuotas,
              estadocotizacion
            FROM vwGetAllCotizacion
            WHERE idcotizacion = :idcotizacion LIMIT 1;
              

SELECT * FROM personas;





SELECT
                nombres,
                apellidos,
                nrodoc AS dni,
                telprimario,
                genero,
                CASE estadocivil
                    WHEN 'SOL' THEN 'Soltero(a)'
                    WHEN 'CAS' THEN 'Casado(a)'
                    WHEN 'VDO' THEN 'Viudo(a)'
                    WHEN 'DVC' THEN 'Divorciado(a)'
                    WHEN 'CNV' THEN 'Conviviente'
                    ELSE 'No especificado'
                END AS estadocivil
              FROM personas 
              WHERE tipodoc = 'DNI' AND nrodoc = :dni;

SELECT * FROM pagos;