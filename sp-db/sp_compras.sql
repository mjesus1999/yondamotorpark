
USE motorpark;

DROP PROCEDURE  sp_compra_registrar;
DELIMITER //

CREATE PROCEDURE sp_compra_registrar(
IN idorden_  INT,
IN idlogistica_ INT,
IN fechacompra_ DATE,
IN tipodoc_  VARCHAR(20),
IN serie_  VARCHAR(10),
IN numdocumento_ VARCHAR(30),
IN rutadoc_		VARCHAR(200)
)
BEGIN

	INSERT INTO compras(idorden,idlogistica,fechacompra,tipodoc,serie,numdocumento,rutadoc)
				VALUES(idorden_,idlogistica_,fechacompra_,tipodoc_,serie_,numdocumento_,rutadoc_);
                
	  SELECT LAST_INSERT_ID() AS 'last_id';
                
	UPDATE ordenescompra SET facturado='S' WHERE idordencompra = idorden_;
    
    

END ;
 
 USE motorpark;
DROP PROCEDURE IF EXISTS sp_detalle_oc_por_concesionario;

DELIMITER //
CREATE PROCEDURE sp_detalle_oc_por_concesionario(
    IN idconcesionario_ INT
)
BEGIN
    SELECT 
        doc.iddetordencompra,
        doc.idordencompra,
        v.idvehiculo,
        mar.marca,
        m.modelo AS modelo,
        v.version,
        com.combustible,
        m.anio,
		v.chasis,
        v.seriemotor,
        v.placa,
        v.placarotativa,
        v.color,
        v.condicion,
        v.moneda,
        doc.preciocompra,
        oc.serie,
        DATE_FORMAT(oc.emision, '%d-%m-%Y') AS emision
    FROM ordenescompra oc
    INNER JOIN tiendas t ON t.idtienda = oc.idtienda
    INNER JOIN detordencompra doc ON doc.idordencompra = oc.idordencompra
    INNER JOIN vehiculos v ON v.idvehiculo = doc.idvehiculo
    INNER JOIN combustibles com ON v.idcombustible = com.idcombustible
    INNER JOIN modelos m ON m.idmodelo = v.idmodelo
    INNER JOIN marcas mar ON m.idmarca = mar.idmarca 
    WHERE t.idconcesionario = idconcesionario_
      AND oc.estado != 'anulado'
      AND oc.estado != 'emitido'
      AND oc.facturado != 'S'
      AND doc.estado = '1'
    ORDER BY oc.emision DESC;
END //
DELIMITER ;


CALL sp_detalle_oc_por_concesionario(1);
