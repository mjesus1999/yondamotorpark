
USE motorpark;

SELECT * FROM fichasolicitud;
SELECT * FROM personas;
SELECT * FROM vehiculos;
SELECT * FROM modelos;
SELECT * FROM marcas;
SELECT * FROM tipovehiculos;
SELECT * FROM ordenescompra;

SELECT * FROM contratos;

select * FROM locales;
SELECT * FROM cotizaciones;

SELECT * FROM clientes;
SELECT * FROM vehiculos;
SELECT * FROM modelos;





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



DROP PROCEDURE sp_contrato_pdf;



DELIMITER //

CREATE PROCEDURE sp_contrato_pdf(IN idcontrato_ INT)
BEGIN
    SELECT 
        con.idcontrato,
    	  CONCAT('N° ', LPAD(con.idcontrato, 5, '0')) AS codigo_contrato,

        -- SEDE (Usando la Vista)
        CONCAT(ulocal.departamento, ' - ', ulocal.provincia) AS sede,

        -- CLIENTE
        CONCAT(pcliente.apellidos, ' ', pcliente.nombres) AS cliente,
        pcliente.nrodoc AS documentoCliente,
        pcliente.direccion AS direccionCliente,
        ucliente.distrito AS distritoCliente,      
        ucliente.provincia AS provinciaCliente,    
        ucliente.departamento AS departamentoCliente, 
        pcliente.email AS emailCliente,
        pcliente.telprimario AS telCliente,

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
    
    -- SEDE (Usando la Vista)
    INNER JOIN locales l ON con.idlocal = l.idlocal
    INNER JOIN v_ubigeo_completo ulocal ON l.iddistrito = ulocal.iddistrito

    -- CLIENTE
    INNER JOIN clientes cli ON cot.idcliente = cli.idcliente
    INNER JOIN personas pcliente ON cli.idpersona = pcliente.idpersona
    LEFT JOIN v_ubigeo_completo ucliente ON pcliente.iddistrito = ucliente.iddistrito -- Simplificado

    -- FICHA SOLICITUD (conyuge, aval, conyuge del aval)
    INNER JOIN fichasolicitud f ON f.idcotizacion = cot.idcotizacion
    LEFT JOIN personas pconyuge ON f.idconyuge = pconyuge.idpersona
    LEFT JOIN personas paval ON f.idaval = paval.idpersona
    LEFT JOIN personas pavalconyuge ON f.idavalconyuge = pavalconyuge.idpersona

    -- UBIGEOS (Usando la Vista)
    LEFT JOIN v_ubigeo_completo uconyuge ON pconyuge.iddistrito = uconyuge.iddistrito     -- Simplificado
    LEFT JOIN v_ubigeo_completo uaval ON paval.iddistrito = uaval.iddistrito               -- Simplificado
    LEFT JOIN v_ubigeo_completo uavalconyuge ON pavalconyuge.iddistrito = uavalconyuge.iddistrito -- Simplificado

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





CALL sp_contrato_pdf(34);







-- CONTRATOS
CREATE INDEX idx_contratos_idcotizacion ON contratos (idcotizacion);
CREATE INDEX idx_contratos_idlocal ON contratos (idlocal);
CREATE INDEX idx_contratos_estado ON contratos (estado);

-- COTIZACIONES 
CREATE INDEX idx_cotizaciones_idcliente ON cotizaciones (idcliente);
CREATE INDEX idx_cotizaciones_idvehiculo ON cotizaciones (idvehiculo);
CREATE INDEX idx_cotizaciones_estadocotizacion ON cotizaciones (estadocotizacion);

-- LOCALES 
CREATE INDEX idx_locales_iddistrito ON locales (iddistrito);


-- FICHA SOLICITUD 
CREATE INDEX idx_fichasolicitud_idcotizacion ON fichasolicitud (idcotizacion);

-- MODELOS
CREATE INDEX idx_modelos_idmarca ON modelos (idmarca);




-- De esa forma, la búsqueda del contrato es directa sin recorrer filas innecesarias.
CREATE UNIQUE INDEX idx_contratos_idcontrato_estado ON contratos (idcontrato, estado);






SHOW COLUMNS FROM ordenescompra;
SHOW COLUMNS FROM vehiculos;
SHOW COLUMNS FROM cotizaciones;
SHOW COLUMNS FROM contratos;


CREATE INDEX idx_cotizacion_estado ON cotizaciones(estadocotizacion);
CREATE INDEX idx_cotizacion_cliente ON cotizaciones(idcliente);
CREATE INDEX idx_pagos_cotizacion ON pagos(idcotizacion);
CREATE INDEX idx_pagos_concepto ON pagos(idconcepto);
CREATE INDEX idx_contratos_cotizacion ON contratos(idcotizacion);
CREATE INDEX idx_vehiculos_id ON vehiculos(idvehiculo);
CREATE INDEX idx_clientes_tipo ON clientes(tipocliente);
CREATE INDEX idx_modelos_marca ON modelos(idmarca);
