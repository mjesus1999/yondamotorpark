USE motorpark;
SELECT * FROM marcas;
SELECT * FROM combustibles;
SELECT * FROM ordenescompra;
SELECT * FROM locales;																			
SELECT * FROM detordencompra;

SELECT * FROM modelos;
SELECT * FROM tipovehiculos ;



DROP PROCEDURE sp_vehiculo_OC_registrar;
DELIMITER //

CREATE PROCEDURE sp_vehiculo_OC_registrar(
IN idmodelo_ INT,
IN idcombustible_ INT,
IN version_ VARCHAR(20),
IN color_ VARCHAR(30), -- NULL
IN chasis_ VARCHAR(30), -- NULL
IN placa_ VARCHAR(10), -- NULL
IN placarotativa_ VARCHAR(10), -- NULL
IN seriemotor_ VARCHAR(20) -- NULL
)
BEGIN

	INSERT INTO vehiculos(idmodelo,idcombustible,version,color,chasis,placa,placarotativa,seriemotor,disponibilidad,origen)
		VALUES(
        idmodelo_,
        idcombustible_,
        version_,
        NULLIF(color_,''),
        NULLIF(chasis_,''),
        NULLIF(placa_,''),
        NULLIF(placarotativa_,''),
        NULLIF(seriemotor_,''),
        'proceso',
        'OCP'
        );
        
        SELECT LAST_INSERT_ID() AS 'last_id';

END //

CALL sp_vehiculo_OC_registrar(13,1,'FULL','Rojo','','','','');
















INSERT INTO combustibles(combustible)
		VALUES ('Gasolina'),
			   ('Diesel'),
               ('GLP'),
               ('GNV'),
               ('Dual: Gasolina, GLP');
               
SELECT * FROM combustibles;



CALL sp_vehiculo_OC_registrar(
  13,             -- idmodelo_ 
  1,             -- idcombustible_ 
  2,             -- idlogistica_ 
  NULL,          -- idlocal_ (es NULL)
  'Full',        -- version_ 
  'nuevo',       -- condicion_ 
  NULL,          -- color_
  NULL,          -- chasis_
  NULL,          -- placa_
  NULL,          -- placarotativa_
  NULL,          -- seriemotor_
  18000.00       -- precioventa_
);
	
SELECT * FROM vehiculos;






