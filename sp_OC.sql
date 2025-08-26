
USE motorpark2;

DROP procedure sp_oc_por_estado


DELIMITER $$

CREATE PROCEDURE sp_oc_por_estado(IN p_estado VARCHAR(50))
BEGIN
    SELECT 
        oc.idordencompra,
        oc.serie,
        oc.emision,
        oc.moneda,
        con.razonsocial,

        (
            SELECT IFNULL(SUM(preciocompra * 1.18),0) 
            FROM detordencompra 
            WHERE idordencompra = oc.idordencompra
        ) AS totalOC,

        (
            SELECT IFNULL(SUM(amortizacion),0) 
            FROM pagosOC 
            WHERE idorden = oc.idordencompra
        ) AS totalPagado,

        (
            (SELECT IFNULL(SUM(preciocompra * 1.18),0) 
             FROM detordencompra 
             WHERE idordencompra = oc.idordencompra)
            -
            (SELECT IFNULL(SUM(amortizacion),0) 
             FROM pagosOC 
             WHERE idorden = oc.idordencompra)
        ) AS saldoRestante

    FROM ordenescompra oc
    JOIN tiendas t ON oc.idtienda = t.idtienda
    JOIN concesionarios con ON t.idconcesionario = con.idconcesionario
    WHERE oc.estado = CONVERT(p_estado USING utf8mb4) COLLATE utf8mb4_general_ci
    ORDER BY oc.idordencompra DESC;
END $$

DELIMITER ;


DROP PROCEDURE sp_anular_OC

DROP PROCEDURE sp_anular_OC;

DELIMITER //

CREATE PROCEDURE sp_anular_OC(
    IN estado_ VARCHAR(20),
    IN observaciones_ VARCHAR(400),
    IN idordencompra_ INT
)
BEGIN
    -- 1. Actualizar la orden
    UPDATE ordenescompra 
    SET estado = estado_,
        observaciones = observaciones_,
        fechanulado = NOW(),
        anulacion = CURDATE()
    WHERE idordencompra = idordencompra_;

    -- 2. Actualizar vehículos vinculados a la orden
    UPDATE vehiculos v
    INNER JOIN detordencompra d ON v.idvehiculo = d.idvehiculo
    SET v.estado = '0',
        v.eliminado = NOW()
    WHERE d.idordencompra = idordencompra_;

    -- 3. Actualizar detordencompra 
    UPDATE detordencompra
    SET estado = '0'
    WHERE idordencompra = idordencompra_;
END //

DELIMITER ;

SHOW FULL COLUMNS FROM ordenescompra;


SELECT COUNT(*) 
FROM information_schema.tables 
WHERE table_schema = 'motorpark2';

SELECT