
-- USE motorpark; -- hosting: seleccionar BD en phpMyAdmin

CREATE VIEW v_ubigeo_completo AS
SELECT 
    d.iddistrito,
    d.distrito,
    p.idprovincia,
    p.provincia,
    dep.iddepartamento,
    dep.departamento
FROM distritos d
JOIN provincias p ON d.idprovincia = p.idprovincia
JOIN departamentos dep ON p.iddepartamento = dep.iddepartamento;



DROP PROCEDURE IF EXISTS sp_contrato_pdf;


DELIMITER //

CREATE PROCEDURE sp_contrato_pdf(IN idcontrato_ INT)
BEGIN
    
SELECT 
    con.idcontrato,
    cli.tipocliente,
    CONCAT('N° ', LPAD(con.idcontrato, 5, '0')) AS codigo_contrato,

    -- SEDE
    CONCAT(ulocal.departamento, ' - ', ulocal.provincia) AS sede,

    -- CLIENTE (Persona o Empresa)
    CASE 
        WHEN cli.tipocliente = 'P' THEN CONCAT(pcliente.apellidos, ' ', pcliente.nombres)
        WHEN cli.tipocliente = 'E' THEN ecliente.razonsocial
    END AS cliente,

    CASE 
        WHEN cli.tipocliente = 'P' THEN pcliente.nrodoc
        WHEN cli.tipocliente = 'E' THEN ecliente.ruc
    END AS documentoCliente,

    CASE 
        WHEN cli.tipocliente = 'P' THEN pcliente.direccion
        WHEN cli.tipocliente = 'E' THEN ecliente.direccion
    END AS direccionCliente,

    CASE 
        WHEN cli.tipocliente = 'P' THEN ucliente.distrito
        WHEN cli.tipocliente = 'E' THEN uempresa.distrito
    END AS distritoCliente,

    CASE 
        WHEN cli.tipocliente = 'P' THEN ucliente.provincia
        WHEN cli.tipocliente = 'E' THEN uempresa.provincia
    END AS provinciaCliente,

    CASE 
        WHEN cli.tipocliente = 'P' THEN ucliente.departamento
        WHEN cli.tipocliente = 'E' THEN uempresa.departamento
    END AS departamentoCliente,

    CASE 
        WHEN cli.tipocliente = 'P' THEN pcliente.email
        WHEN cli.tipocliente = 'E' THEN ecliente.email
    END AS emailCliente,

    CASE 
        WHEN cli.tipocliente = 'P' THEN pcliente.telprimario
        WHEN cli.tipocliente = 'E' THEN ecliente.telprimario
    END AS telCliente,


    -- CÓNYUGE
    CONCAT(pconyuge.apellidos, ' ', pconyuge.nombres) AS conyuge,
    pconyuge.nrodoc AS documentoConyuge,
    pconyuge.direccion AS direccionConyuge,
    uconyuge.distrito AS distritoConyuge,     
    uconyuge.provincia AS provinciaConyuge,   
    uconyuge.departamento AS departamentoConyuge,
    pconyuge.email AS emailConyuge,
    pconyuge.telprimario AS telConyuge,

    -- AVAL
    CONCAT(paval.apellidos, ' ', paval.nombres) AS aval,
    paval.nrodoc AS documentoAval,
    paval.direccion AS direccionAval,
    uaval.distrito AS distritoAval,            
    uaval.provincia AS provinciaAval,          
    uaval.departamento AS departamentoAval,      
    paval.email AS emailAval,
    paval.telprimario AS telAval,

    -- CÓNYUGE DEL AVAL
    CONCAT(pavalconyuge.apellidos, ' ', pavalconyuge.nombres) AS avalConyuge,
    pavalconyuge.nrodoc AS documentoAvalConyuge,
    pavalconyuge.direccion AS direccionAvalConyuge,
    uavalconyuge.distrito AS distritoAvalConyuge,    
    uavalconyuge.provincia AS provinciaAvalConyuge,  
    uavalconyuge.departamento AS departamentoAvalConyuge, 
    pavalconyuge.email AS emailAvalConyuge,
    pavalconyuge.telprimario AS telAvalConyuge,

    -- VEHÍCULO
    mar.marca,
    mode.modelo,
    v.color,
    v.placa,
    v.seriemotor,
    v.chasis,
    mode.anio,

    -- DETALLES DEL CONTRATO
    (cot.precioventa - cot.inicial) AS financiado,
    cot.moneda,
    con.diapago,
    (cot.valorcuota * cot.numcuotas) AS totalPagar,
    con.penalidadbase,
    cot.inicial AS cuotainicial,
    cot.numcuotas,
    cot.valorcuota,
    cot.moneda,
    cot.tasaanual,
    cot.tasamensual

FROM contratos con
INNER JOIN cotizaciones cot ON con.idcotizacion = cot.idcotizacion

-- SEDE
INNER JOIN locales l ON con.idlocal = l.idlocal
INNER JOIN v_ubigeo_completo ulocal ON l.iddistrito = ulocal.iddistrito

-- CLIENTE
INNER JOIN clientes cli ON cot.idcliente = cli.idcliente
LEFT JOIN personas pcliente ON cli.idpersona = pcliente.idpersona
LEFT JOIN empresas ecliente ON cli.idempresa = ecliente.idempresa
LEFT JOIN v_ubigeo_completo ucliente ON pcliente.iddistrito = ucliente.iddistrito
LEFT JOIN v_ubigeo_completo uempresa ON ecliente.iddistrito = uempresa.iddistrito

-- FICHA SOLICITUD
INNER JOIN fichasolicitud f ON f.idcotizacion = cot.idcotizacion
LEFT JOIN personas pconyuge ON f.idconyuge = pconyuge.idpersona
LEFT JOIN personas paval ON f.idaval = paval.idpersona
LEFT JOIN personas pavalconyuge ON f.idavalconyuge = pavalconyuge.idpersona

-- UBIGEOS
LEFT JOIN v_ubigeo_completo uconyuge ON pconyuge.iddistrito = uconyuge.iddistrito
LEFT JOIN v_ubigeo_completo uaval ON paval.iddistrito = uaval.iddistrito
LEFT JOIN v_ubigeo_completo uavalconyuge ON pavalconyuge.iddistrito = uavalconyuge.iddistrito

-- VEHÍCULO
INNER JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
INNER JOIN modelos mode ON v.idmodelo = mode.idmodelo
INNER JOIN marcas mar ON mode.idmarca = mar.idmarca

WHERE con.idcontrato = idcontrato_
  AND con.estado = 'ACT'
  AND cot.estadocotizacion = 'CONT'
LIMIT 1;

END //
DELIMITER ;









-- CALL sp_contrato_pdf(37); -- prueba local







-- Índices opcionales (comentados si indices.sql ya los cubre)
-- CREATE INDEX idx_contratos_idcotizacion ON contratos (idcotizacion);
-- CREATE INDEX idx_contratos_idlocal ON contratos (idlocal);
-- CREATE INDEX idx_contratos_estado ON contratos (estado);

-- COTIZACIONES 
-- CREATE INDEX idx_cotizaciones_idcliente ON cotizaciones (idcliente);
-- CREATE INDEX idx_cotizaciones_idvehiculo ON cotizaciones (idvehiculo);
-- CREATE INDEX idx_cotizaciones_estadocotizacion ON cotizaciones (estadocotizacion);

-- LOCALES 
-- CREATE INDEX idx_locales_iddistrito ON locales (iddistrito);


-- FICHA SOLICITUD 
-- CREATE INDEX idx_fichasolicitud_idcotizacion ON fichasolicitud (idcotizacion);

-- MODELOS
-- CREATE INDEX idx_modelos_idmarca ON modelos (idmarca);




-- De esa forma, la búsqueda del contrato es directa sin recorrer filas innecesarias.
-- CREATE UNIQUE INDEX idx_contratos_idcontrato_estado ON contratos (idcontrato, estado);






-- SHOW / CREATE INDEX / SELECT comentados para hosting seguro
-- (descomenta solo si necesitas recrear índices manualmente)