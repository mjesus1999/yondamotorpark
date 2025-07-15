USE motorpark;
SELECT * FROM marcas;
SELECT * FROM combustibles;
SELECT * FROM ordenescompra;
SELECT * FROM locales;																			
SELECT * FROM detordencompra;

SELECT * FROM modelos;
SELECT * FROM tipovehiculos ;

DELIMITER //

CREATE PROCEDURE sp_vehiculo_OC_registrar(
IN idmodelo_ INT,
IN idcombustible_ INT,
IN idlogistica_ INT,
IN idlocal_ 	INT,
IN version_ VARCHAR(20),
IN condicion_ VARCHAR(20),
IN color_ VARCHAR(30), -- NULL
IN chasis_ VARCHAR(30), -- NULL
IN placa_ VARCHAR(10), -- NULL
IN placarotativa_ VARCHAR(10), -- NULL
IN seriemotor_ VARCHAR(20),-- NULL
IN precioventa_ DECIMAL(9,2) 
)
BEGIN

	INSERT INTO vehiculos(idmodelo,idcombustible, idlogistica,idlocal,version,condicion,color,chasis,placa,placarotativa,seriemotor,precioventa,disponibilidad,origen)
		VALUES(
        idmodelo_,
        idcombustible_,
        idlogistica_,
        idlocal_,
        version_,
        condicion_,
        NULLIF(color_,''),
        NULLIF(chasis_,''),
        NULLIF(placa_,''),
        NULLIF(placarotativa_,''),
        NULLIF(seriemotor_,''),
        precioventa_,
        'proceso',
        'OCP'
        );
        
        SELECT LAST_INSERT_ID() AS 'last_id';

END //
















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






DELIMITER //

CREATE PROCEDURE sp_registrarVehiculoDesdeOC(
  IN idmodelo_ INT,
  IN idcombustible_ INT,
  IN idlogistica_ INT,
  IN idlocal_ INT,
  IN version_ VARCHAR(20),
  IN condicion_ ENUM('nuevo','seminuevo'),
  IN color_ VARCHAR(30),
  IN chasis_ VARCHAR(30),
  IN placa_ VARCHAR(10),
  IN placarotativa_ VARCHAR(10),
  IN seriemotor_ VARCHAR(20),
  IN precioventa_ DECIMAL(9,2),
  IN idordencompra_ INT,
  IN preciocompra_ DECIMAL(9,2)
)
BEGIN
  -- Insertar en la tabla vehiculos
  INSERT INTO vehiculos (
    idmodelo, idcombustible, idlogistica, idlocal,
    version, condicion, color, chasis,
    placa, placarotativa, seriemotor, precioventa,
    disponibilidad, origen
  ) VALUES (
    idmodelo_, idcombustible_, idlogistica_, idlocal_,
    version_, condicion_, NULLIF(color_, ''), NULLIF(chasis_, ''),
    NULLIF(placa_, ''), NULLIF(placarotativa_, ''), NULLIF(seriemotor_, ''), precioventa_,
    'proceso', 
    'OCP'      
  );

  -- Capturar el ID del vehículo recién insertado
  SET @last_idvehiculo := LAST_INSERT_ID();

  -- Insertar en la tabla detordencompra
  INSERT INTO detordencompra (
    idordencompra, idvehiculo, preciocompra
  ) VALUES (
    idordencompra_, @last_idvehiculo, preciocompra_
  );

  -- Devolver el ID del vehículo creado
  SELECT @last_idvehiculo AS idvehiculo_creado;
END //

DELIMITER ;



