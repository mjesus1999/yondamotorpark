
-- USE motorpark; -- hosting: seleccionar BD en phpMyAdmin


DROP PROCEDURE IF EXISTS sp_vehiculo_update_recepcion_OC;
DELIMITER //
CREATE PROCEDURE sp_vehiculo_update_recepcion_OC( 
  IN idvehiculo_ INT,
  IN idlogistica_ INT,
  IN idlocal_ INT,
  IN chasis_ VARCHAR(30),
  IN placa_ VARCHAR(10),
  IN placarotativa_ VARCHAR(10),
  IN seriemotor_ VARCHAR(20),
  IN color_ VARCHAR(30),
  IN disponibilidad_ VARCHAR(25)
)
BEGIN
  UPDATE vehiculos 
  SET 
    idlogistica = idlogistica_,
    idlocal = idlocal_,
    chasis = chasis_,
    placa = NULLIF(placa_,''),
    placarotativa = NULLIF(placarotativa_, ''),
    seriemotor = seriemotor_,
    color = color_,
    disponibilidad = disponibilidad_,
    modificado = NOW()
  WHERE idvehiculo = idvehiculo_;

  SELECT ROW_COUNT() AS filas_afectadas;
END //

DELIMITER ;

DELIMITER //

CREATE PROCEDURE sp_vehiculo_OC_registrar(
IN idmodelo_ INT,
IN idcombustible_ INT,
IN version_ VARCHAR(20),
IN condicion_ VARCHAR(30),
IN color_ VARCHAR(30), -- NULL
IN chasis_ VARCHAR(30), -- NULL
IN placa_ VARCHAR(10), -- NULL
IN placarotativa_ VARCHAR(10), -- NULL
IN seriemotor_ VARCHAR(20) -- NULL
)
BEGIN

	INSERT INTO vehiculos(idmodelo,idcombustible,version,condicion,color,chasis,placa,placarotativa,seriemotor,disponibilidad,origen)
		VALUES(
        idmodelo_,
        idcombustible_,
        version_,
        condicion_,
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

DELIMITER ;






