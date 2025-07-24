USE motorpak;
-- CALL spu_vehiculos_registrar(43, 'Básico', 'seminuevo', 3, 'GRIS', 'CHASISX7777', 'YYY-777', 'ROT-777', 'SER-777', 'PEN', 30500, 2, 3);

DELIMITER $$

-- REGISTRAR VEHICULO
CREATE PROCEDURE spu_vehiculos_registrar (
	IN _idmodelo INT,
	IN _version VARCHAR(20),
	IN _condicion ENUM('nuevo', 'seminuevo'),
	IN _idcombustible INT,
	IN _color VARCHAR(30),
	IN _chasis VARCHAR(30),
	IN _placa VARCHAR(10),
	IN _placarotativa VARCHAR(10),
	IN _seriemotor VARCHAR(20),
	IN _moneda ENUM('USD', 'PEN'),
	IN _precioventa DECIMAL(9,2),
	IN _idlogistica INT,
	IN _idlocal INT
)
BEGIN
	INSERT INTO vehiculos (
		idmodelo,
		version,
		condicion,
		idcombustible,
		color,
		chasis,
		placa,
		placarotativa,
		seriemotor,
		moneda, 
		precioventa,
		disponibilidad,
		origen,
		idlogistica,
		idlocal
	)
	VALUES (
		_idmodelo,
		_version,
		_condicion,
		_idcombustible,
		_color,
		_chasis,
		_placa,
		_placarotativa,
		_seriemotor,
		_moneda,
		_precioventa,
		'proceso',
		'CTZ',
		_idlogistica,
		_idlocal
	);

	-- Devolver el ID insertado
	SELECT LAST_INSERT_ID() AS idvehiculo;
END $$
