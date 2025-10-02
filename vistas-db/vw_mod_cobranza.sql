-- VISTA DE COBRANZA

-- 1) ESTADÍSTICAS + RESUMEN POR CONTRATO 

DROP PROCEDURE IF EXISTS sp_get_estadisticas_cobranza;
DELIMITER $$
CREATE PROCEDURE sp_get_estadisticas_cobranza()
BEGIN
    -- 1) ESTADÍSTICAS
    SELECT
        -- TOTAL DE CONTRATOS
        (SELECT COUNT(DISTINCT c2.idcontrato)
         FROM contratos c2
         JOIN cronogramas cr2 ON cr2.idcontrato = c2.idcontrato
         WHERE c2.estado = 'ACT' AND cr2.estado IN ('Pendiente','Vencido')
        ) AS total_deudores,
        
        -- CUOTA POR VENCER
        (SELECT COUNT(DISTINCT c3.idcontrato)
         FROM contratos c3
         JOIN cronogramas cr3 ON cr3.idcontrato = c3.idcontrato
         WHERE c3.estado = 'ACT'
           AND cr3.estado IN ('Pendiente','Vencido')
           AND DATEDIFF(cr3.fechapago, CURDATE()) BETWEEN 0 AND 3
        ) AS por_vencer_3dias,
        
        -- CUOTAS VENCIDAS
        (SELECT COUNT(DISTINCT c4.idcontrato)
         FROM contratos c4
         JOIN cronogramas cr4 ON cr4.idcontrato = c4.idcontrato
         WHERE c4.estado = 'ACT'
           AND cr4.estado = 'Vencido'
        ) AS contratos_con_vencidos,
        
        -- TOTAL POR COBRAR
        (SELECT IFNULL(SUM(cr5.abonocapital + cr5.interes + IFNULL(cr5.penalidad,0)),0)
         FROM contratos c5
         JOIN cronogramas cr5 ON cr5.idcontrato = c5.idcontrato
         WHERE c5.estado = 'ACT'
           AND cr5.estado IN ('Pendiente','Vencido')
        ) AS total_por_cobrar,
        
        -- TOTAL DE CUOTAS PAGADAS
        (SELECT COUNT(*)
         FROM cronogramas crp
         JOIN contratos cpr ON crp.idcontrato = cpr.idcontrato
         WHERE cpr.estado = 'ACT'
           AND crp.estado = 'Pagado'
        ) AS total_cuotas_pagadas;
    
END$$
DELIMITER ;


-- 2) PROCEDIMIENTO PARA OBTENER LAS TARJETAS DE COBRANZA

DROP PROCEDURE IF EXISTS sp_get_tarjetas_cobranza;
DELIMITER $$
CREATE PROCEDURE sp_get_tarjetas_cobranza()
BEGIN
    SELECT 
        c.idcontrato,
        
        -- Nombre del cliente
        CASE 
            WHEN cl.tipocliente = 'P' THEN CONCAT(p.apellidos, ' ', p.nombres)
            WHEN cl.tipocliente = 'E' THEN e.razonsocial
        END AS nombre_cliente,
        
        -- Teléfono
        CASE 
            WHEN cl.tipocliente = 'P' THEN p.telprimario
            WHEN cl.tipocliente = 'E' THEN e.telprimario
        END AS telefono,
        
        -- Local
        CONCAT(d.distrito, ' / ', pr.provincia) AS local,
        
        -- Tipo de vehículo
        CONCAT(ma.marca, ' - ', mo.modelo) AS tipo_vehiculo,
        
        -- Estado de vencimiento
        CASE
            WHEN cr.estado = 'Vencido' THEN 'VENCIDO'
            WHEN DATEDIFF(cr.fechapago, CURDATE()) = 0 THEN 'VENCE HOY'
            WHEN DATEDIFF(cr.fechapago, CURDATE()) BETWEEN 1 AND 3 THEN CONCAT('Vence en ', DATEDIFF(cr.fechapago, CURDATE()), ' días')
        END AS estado_vencimiento,
        
        -- Fecha de vencimiento
        DATE_FORMAT(cr.fechapago, '%d/%m/%Y') AS fecha_vencimiento
        
    FROM contratos c
    JOIN cotizaciones cot ON c.idcotizacion = cot.idcotizacion
    JOIN clientes cl ON cot.idcliente = cl.idcliente
    LEFT JOIN personas p ON cl.idpersona = p.idpersona
    LEFT JOIN empresas e ON cl.idempresa = e.idempresa
    JOIN locales l ON c.idlocal = l.idlocal
    JOIN distritos d ON l.iddistrito = d.iddistrito
    JOIN provincias pr ON d.idprovincia = pr.idprovincia
    JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    JOIN modelos mo ON v.idmodelo = mo.idmodelo
    JOIN marcas ma ON mo.idmarca = ma.idmarca
    JOIN cronogramas cr ON c.idcontrato = cr.idcontrato
    
    WHERE c.estado = 'ACT'
      AND cr.estado IN ('Pendiente', 'Vencido')
      AND DATEDIFF(cr.fechapago, CURDATE()) <= 3
      AND cr.fechapago = (
          SELECT MIN(cr2.fechapago) 
          FROM cronogramas cr2 
          WHERE cr2.idcontrato = c.idcontrato 
            AND cr2.estado IN ('Pendiente', 'Vencido')
      )
    ORDER BY 
        FIELD(cr.estado, 'Vencido', 'Pendiente'),
        cr.fechapago ASC;
        
END$$
DELIMITER ;