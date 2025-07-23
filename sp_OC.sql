
USE motorpark;

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


SHOW FULL COLUMNS FROM ordenescompra;
