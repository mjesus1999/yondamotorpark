USE motorpark;


SELECT * FROM cotizaciones;


DROP PROCEDURE  sp_reporte_cotizaciones_general;

CREATE PROCEDURE sp_reporte_cotizaciones_general()
BEGIN
    SELECT
        c.idcotizacion AS '#',
        CONCAT(
            p.apellidos, ' ', p.nombres
        ) AS cliente,
        p.nrodoc AS documento,
        CONCAT(
            mar.marca, ' / ', 
            modl.modelo, ' / ', 
            modl.anio
        ) AS vehiculo,
        c.moneda, 
        CONCAT(
            CASE c.moneda WHEN 'USD' THEN '$ ' ELSE 'S/ ' END, 
            FORMAT(c.inicial, 2)
        ) AS inicial,
        c.numcuotas,
        CONCAT(
            CASE c.moneda WHEN 'USD' THEN '$ ' ELSE 'S/ ' END, 
            FORMAT(c.valorcuota, 2)
        ) AS valorcuota,
        CONCAT(per_ase.apellidos, ' ', per_ase.nombres) AS asesor,
        CASE c.estadocotizacion
            WHEN 'P' THEN 'Pendiente'
            WHEN 'S' THEN 'Separada'
            WHEN 'A' THEN 'Aprobada'
            ELSE c.estadocotizacion
        END AS estado
        
    FROM 
        cotizaciones c
    INNER JOIN clientes cli ON c.idcliente = cli.idcliente
    INNER JOIN personas p ON cli.idpersona = p.idpersona
    INNER JOIN vehiculos v ON c.idvehiculo = v.idvehiculo
    INNER JOIN modelos modl ON v.idmodelo = modl.idmodelo
    INNER JOIN marcas mar ON modl.idmarca = mar.idmarca
    INNER JOIN colaboradores col_ase ON c.idasesor = col_ase.idcolaborador
    INNER JOIN contratoslaborales cl_ase ON col_ase.idcontratolaboral = cl_ase.idcontratolaboral
    INNER JOIN personas per_ase ON cl_ase.idpersona = per_ase.idpersona
    WHERE c.estadocotizacion IN ('P', 'S', 'A') 
    ORDER BY c.idcotizacion DESC;
END 

CALL sp_reporte_cotizaciones_general();

 UPDATE cotizaciones SET estadocotizacion = 'R' WHERE idcotizacion= 104;