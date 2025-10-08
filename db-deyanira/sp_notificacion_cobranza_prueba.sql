CREATE TABLE marcas (
    idmarca INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(40) NOT NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT uk_marca_mar UNIQUE (marca)
) ENGINE = INNODB;

CREATE TABLE tipovehiculos (
    idtipovehiculo INT AUTO_INCREMENT PRIMARY KEY,
    tipovehiculo VARCHAR(40) NOT NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT uk_tipovehiculo_tve UNIQUE (tipovehiculo)
) ENGINE = INNODB;

-- El unique para modelos debe ser tipovehiculo + marca + año
CREATE TABLE modelos (
    idmodelo INT AUTO_INCREMENT PRIMARY KEY,
    idtipovehiculo INT NOT NULL,
    idmarca INT NOT NULL,
    modelo VARCHAR(40) NOT NULL,
    anio CHAR(4) NOT NULL,
    imagenreferencial VARCHAR(200) NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT fk_idtipovehiculo_mod FOREIGN KEY (idtipovehiculo) REFERENCES tipovehiculos (idtipovehiculo),
    CONSTRAINT fk_idmarca_mod FOREIGN KEY (idmarca) REFERENCES marcas (idmarca),
    CONSTRAINT uk_modelo_mod UNIQUE (idmarca, modelo, anio)
) ENGINE = INNODB;

CREATE TABLE combustibles (
    idcombustible INT AUTO_INCREMENT PRIMARY KEY,
    combustible VARCHAR(40) NOT NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT uk_combustible_cmb UNIQUE (combustible)
) ENGINE = INNODB;

CREATE TABLE motorpark (
    idmotorpark INT AUTO_INCREMENT PRIMARY KEY,
    ruc CHAR(11) NOT NULL,
    razonsocial VARCHAR(300) NOT NULL,
    nombrecomercial VARCHAR(100) NOT NULL,
    representante VARCHAR(100) NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT uk_ruc_mtp UNIQUE (ruc)
) ENGINE = INNODB;

CREATE TABLE locales (
    idlocal INT AUTO_INCREMENT PRIMARY KEY,
    tienda VARCHAR(40) NOT NULL,
    iddistrito INT NOT NULL,
    idmotorpark INT NOT NULL,
    principal ENUM('S', 'N') NOT NULL,
    responsable VARCHAR(100) NOT NULL,
    correo VARCHAR(200) NULL,
    direccion VARCHAR(300) NULL,
    telefono VARCHAR(12) NULL,
    latitud VARCHAR(20) NULL,
    longitud VARCHAR(20) NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    CONSTRAINT fk_iddistrito_loc FOREIGN KEY (iddistrito) REFERENCES distritos (iddistrito),
    CONSTRAINT fk_idmotorpark_loc FOREIGN KEY (idmotorpark) REFERENCES motorpark (idmotorpark)
) ENGINE = INNODB;

-- La moneda y precio de compra están definidos en el proceso de COMPRA
CREATE TABLE vehiculos (
    idvehiculo INT AUTO_INCREMENT PRIMARY KEY,
    idmodelo INT NOT NULL,
    idcombustible INT NOT NULL,
    idlogistica INT NULL,
    idlocal INT NULL,
    version VARCHAR(20) NOT NULL,
    condicion ENUM('nuevo', 'seminuevo') NOT NULL DEFAULT 'nuevo',
    color VARCHAR(30) NULL,
    chasis VARCHAR(30) NULL,
    placa VARCHAR(10) NULL,
    placarotativa VARCHAR(10) NULL,
    seriemotor VARCHAR(20) NULL,
    moneda ENUM('USD', 'PEN') NULL DEFAULT 'USD', -- VENTA
    precioventa DECIMAL(9, 2) NULL,
    disponibilidad ENUM(
        'proceso',
        'libre',
        'separado',
        'vendido',
        'recuperado'
    ) NOT NULL,
    origen ENUM('OCP', 'OLD', 'CTZ') NOT NULL COMMENT 'OCP = Orden de compra (conducto regular), OLD (Contratos anteriores al sistema), CTZ (Cotizado por asesor)',
    estado ENUM('0', '1') NULL DEFAULT '1',
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    eliminado DATETIME NULL,
    CONSTRAINT fk_idmodelo_veh FOREIGN KEY (idmodelo) REFERENCES modelos (idmodelo),
    CONSTRAINT fk_idcombustible_veh FOREIGN KEY (idcombustible) REFERENCES combustibles (idcombustible),
    CONSTRAINT fk_idlocal_veh FOREIGN KEY (idlocal) REFERENCES locales (idlocal),
    CONSTRAINT fk_idlogistica_veh FOREIGN KEY (idlogistica) REFERENCES colaboradores (idcolaborador)
) ENGINE = INNODB;
CREATE TABLE cotizaciones (
    idcotizacion INT AUTO_INCREMENT PRIMARY KEY,
    idformato INT NOT NULL,
    idcliente INT NULL,
    idasesor INT NOT NULL COMMENT 'Colaborador del área de VENTA', -- 
    idvehiculo INT NOT NULL,
    moneda ENUM('PEN', 'USD') NOT NULL,
    precioventa DECIMAL(9, 2) NOT NULL,
    vigenciadias TINYINT NOT NULL COMMENT 'Días válidos de la cotización' DEFAULT 7,
    inicial DECIMAL(9, 2) NOT NULL,
    numcuotas SMALLINT NOT NULL,
    valorcuota DECIMAL(9, 2) NOT NULL, -- Se usara en la tabla de cronogramas
    gastosadministrativos DECIMAL(9,2) NOT NULL DEFAULT 0.00 COMMENT 'Gastos administrativos de la cotización',
    estadocotizacion ENUM('P', 'E', 'A', 'C', 'R') NOT NULL DEFAULT 'P' COMMENT 'Pendiente | Evaluación | Aprobada | Cancelada (cliente) | Rechazada (Analista crédito)',
    comentarios TEXT,
    fechaseguimiento DATETIME NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME NULL,
    fechareactivacion DATETIME NULL,
    CONSTRAINT fk_idformato_cot FOREIGN KEY (idformato) REFERENCES formatocotizacion (idformato),
    CONSTRAINT fk_idcliente_cot FOREIGN KEY (idcliente) REFERENCES clientes (idcliente),
    CONSTRAINT fk_idvehiculo_cot FOREIGN KEY (idvehiculo) REFERENCES vehiculos (idvehiculo),
    CONSTRAINT fk_idcolventa_cot FOREIGN KEY (idasesor) REFERENCES colaboradores (idcolaborador)
) ENGINE = INNODB;
CREATE TABLE contratos (
    idcontrato INT AUTO_INCREMENT PRIMARY KEY,
    idlocal INT NOT NULL,
    idcotizacion INT NOT NULL,
    idlogistica INT NULL,
    fechainicio DATE NOT NULL,
    diapago TINYINT NOT NULL,
    escredito ENUM('S', 'N') NOT NULL DEFAULT 'S',
    fecharevision DATE NULL,
    penalidadbase DECIMAL(10, 2) NOT NULL DEFAULT 0.1,
    observaciones VARCHAR(350) NULL,
    estado ENUM('ACT', 'INACT') DEFAULT 'ACT',
    CONSTRAINT fk_idlocal_contrato FOREIGN KEY (idlocal) REFERENCES locales (idlocal),
    CONSTRAINT fk_idcotizacion_contrato FOREIGN KEY (idcotizacion) REFERENCES cotizaciones (idcotizacion),
    CONSTRAINT fk_idlogistica_contrato FOREIGN KEY (idlogistica) REFERENCES colaboradores (idcolaborador)
) ENGINE = InnoDB;

CREATE TABLE cronogramas (
    idcronograma INT AUTO_INCREMENT PRIMARY KEY,
    idcontrato INT NOT NULL, -- El valor de cuota se encuentra en contrato <---> cotizaciones
    fechapago DATE NOT NULL,
    interes DECIMAL(10, 2) NOT NULL,
    abonocapital DECIMAL(10, 2) NOT NULL,
    numcuota TINYINT NOT NULL,
    penalidad DECIMAL(10, 2) NULL,
    saldocapital DECIMAL(10, 2) NOT NULL,
    aplicapenalidad ENUM('S', 'N') NOT NULL DEFAULT 'N',
    estado ENUM(
        'Pendiente',
        'Pagado',
        'Vencido'
    ) DEFAULT 'Pendiente',
    CONSTRAINT fk_idcont_cronogramas FOREIGN KEY (idcontrato) REFERENCES contratos (idcontrato)
) ENGINE = InnoDB;

select * from cotizaciones;
select * from cronogramas;
CALL sp_get_datos_reporte_notificacion(8);
CALL sp_get_datos_reporte_notificacion_pdf(7);


USE motorpark;

-- SP DE MOSTRAR PENALIDAD 
/*
DROP PROCEDURE IF EXISTS sp_get_datos_reporte_notificacion_pdf;
DELIMITER $$
CREATE PROCEDURE sp_get_datos_reporte_notificacion_pdf(IN p_idcontrato INT)
BEGIN

    SELECT 
        -- Datos del cliente
        CASE 
            WHEN cl.tipocliente = 'P' THEN CONCAT(pe.apellidos, ' ', pe.nombres)
            WHEN cl.tipocliente = 'E' THEN e.razonsocial
        END AS nombre_cliente,

        CASE 
            WHEN cl.tipocliente = 'P' THEN pe.tipodoc
            ELSE 'RUC'
        END AS tipo_documento,

        CASE 
            WHEN cl.tipocliente = 'P' THEN pe.nrodoc
            WHEN cl.tipocliente = 'E' THEN e.ruc
        END AS numero_documento,

        CASE 
            WHEN cl.tipocliente = 'P' THEN CONCAT(pe.direccion, ', ', dp.distrito, ', ', pp.provincia)
            WHEN cl.tipocliente = 'E' THEN CONCAT(e.direccion, ', ', de.distrito, ', ', peprov.provincia)
        END AS direccion_completa,

        -- Datos del vehículo
        ma.marca,
        mo.modelo,
        mo.anio AS vehiculo_anio,
        v.color,
        v.placa,
        v.chasis AS numero_chasis,
        v.seriemotor AS numero_motor,

        -- Datos del contrato
        cnt.idcontrato,
        cnt.fechainicio AS fecha_contrato,
        cnt.penalidadbase AS porcentaje_penalidad,

        -- Datos financieros
        cot.moneda,
        cot.valorcuota,

        -- Detalle: texto por mes (solo donde existan saldos pendientes)
        GROUP_CONCAT(
            DISTINCT
            (CASE
                -- cuota pagada, penal pendiente -> mostrar solo la mora (penalidad)
                WHEN GREATEST((cot.valorcuota * cnt.penalidadbase) - 
                    COALESCE((SELECT SUM(pg2.amortizacion) FROM pagos pg2 WHERE pg2.idcronograma = cr.idcronograma AND pg2.tipo = 'Penalidad'),0),0) > 0
                     AND GREATEST(cot.valorcuota - COALESCE((SELECT SUM(pg1.amortizacion) FROM pagos pg1 WHERE pg1.idcronograma = cr.idcronograma AND pg1.tipo = 'Cuota'),0),0) = 0
                THEN CONCAT('MORA DE ', UPPER(
                    ELT(MONTH(cr.fechapago),
                        'enero','febrero','marzo','abril','mayo','junio','julio','agosto','setiembre','octubre','noviembre','diciembre'
                    )
                ), ' DE S/ ', FORMAT(
                    GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE((SELECT SUM(pg2a.amortizacion) FROM pagos pg2a WHERE pg2a.idcronograma = cr.idcronograma AND pg2a.tipo = 'Penalidad'),0),0)
                ,2))
                -- cuota pendiente (con/sin penal) -> mostrar "CUOTA DE {MES} CON MORA DE S/ {cuota+penal}"
                WHEN GREATEST(cot.valorcuota - COALESCE((SELECT SUM(pg3.amortizacion) FROM pagos pg3 WHERE pg3.idcronograma = cr.idcronograma AND pg3.tipo = 'Cuota'),0),0) > 0
                THEN CONCAT('CUOTA DE ', UPPER(
                    ELT(MONTH(cr.fechapago),
                        'enero','febrero','marzo','abril','mayo','junio','julio','agosto','setiembre','octubre','noviembre','diciembre'
                    )
                ), ' CON MORA DE S/ ', FORMAT(
                    GREATEST(cot.valorcuota - COALESCE((SELECT SUM(pg4.amortizacion) FROM pagos pg4 WHERE pg4.idcronograma = cr.idcronograma AND pg4.tipo = 'Cuota'),0),0)
                    +
                    GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE((SELECT SUM(pg5.amortizacion) FROM pagos pg5 WHERE pg5.idcronograma = cr.idcronograma AND pg5.tipo = 'Penalidad'),0),0)
                ,2))
                -- (caso raro: solo cuota pendiente sin penal pendiente)
                WHEN GREATEST(cot.valorcuota - COALESCE((SELECT SUM(pg6.amortizacion) FROM pagos pg6 WHERE pg6.idcronograma = cr.idcronograma AND pg6.tipo = 'Cuota'),0),0) > 0
                THEN CONCAT('CUOTA DE ', UPPER(
                    ELT(MONTH(cr.fechapago),
                        'enero','febrero','marzo','abril','mayo','junio','julio','agosto','setiembre','octubre','noviembre','diciembre'
                    )
                ), ' ADEUDA S/ ', FORMAT(
                    GREATEST(cot.valorcuota - COALESCE((SELECT SUM(pg7.amortizacion) FROM pagos pg7 WHERE pg7.idcronograma = cr.idcronograma AND pg7.tipo = 'Cuota'),0),0)
                ,2))
                ELSE NULL
            END)
            ORDER BY cr.fechapago
            SEPARATOR ' || '
        ) AS detalle_cuotas_vencidas,

        -- Cantidad de cuotas con saldo pendiente
        SUM(
            CASE
                WHEN
                    (GREATEST(cot.valorcuota - COALESCE((SELECT SUM(pg8.amortizacion) FROM pagos pg8 WHERE pg8.idcronograma = cr.idcronograma AND pg8.tipo = 'Cuota'),0),0) > 0)
                    OR
                    (GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE((SELECT SUM(pg9.amortizacion) FROM pagos pg9 WHERE pg9.idcronograma = cr.idcronograma AND pg9.tipo = 'Penalidad'),0),0) > 0)
                THEN 1 ELSE 0
            END
        ) AS cantidad_cuotas_vencidas,

        -- Totales calculados: sumando solo los saldos pendientes
        SUM(
            GREATEST(cot.valorcuota - COALESCE((SELECT SUM(pg10.amortizacion) FROM pagos pg10 WHERE pg10.idcronograma = cr.idcronograma AND pg10.tipo = 'Cuota'),0),0)
        ) AS total_cuotas_vencidas,

        SUM(
            GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE((SELECT SUM(pg11.amortizacion) FROM pagos pg11 WHERE pg11.idcronograma = cr.idcronograma AND pg11.tipo = 'Penalidad'),0),0)
        ) AS total_penalidades_vencidas,

        SUM(
            GREATEST(cot.valorcuota - COALESCE((SELECT SUM(pg12.amortizacion) FROM pagos pg12 WHERE pg12.idcronograma = cr.idcronograma AND pg12.tipo = 'Cuota'),0),0)
            +
            GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE((SELECT SUM(pg13.amortizacion) FROM pagos pg13 WHERE pg13.idcronograma = cr.idcronograma AND pg13.tipo = 'Penalidad'),0),0)
        ) AS total_deuda_vencida,

        MIN(cr.fechapago) AS fecha_primera_vencida,
        DATEDIFF(CURDATE(), MIN(cr.fechapago)) AS dias_atraso

    FROM contratos cnt
    JOIN cotizaciones cot ON cnt.idcotizacion = cot.idcotizacion
    JOIN clientes cl ON cot.idcliente = cl.idcliente
    LEFT JOIN personas pe ON cl.idpersona = pe.idpersona
    LEFT JOIN empresas e ON cl.idempresa = e.idempresa
    LEFT JOIN distritos dp ON pe.iddistrito = dp.iddistrito
    LEFT JOIN provincias pp ON dp.idprovincia = pp.idprovincia
    LEFT JOIN distritos de ON e.iddistrito = de.iddistrito
    LEFT JOIN provincias peprov ON de.idprovincia = peprov.idprovincia
    JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    JOIN modelos mo ON v.idmodelo = mo.idmodelo
    JOIN marcas ma ON mo.idmarca = ma.idmarca

    LEFT JOIN cronogramas cr ON cr.idcontrato = cnt.idcontrato
        AND cr.estado IN ('Pendiente', 'Vencido')
        AND cr.fechapago < CURDATE()
        AND (
            (cot.valorcuota - COALESCE((SELECT SUM(ppg1.amortizacion) FROM pagos ppg1 WHERE ppg1.idcronograma = cr.idcronograma AND ppg1.tipo = 'Cuota'),0)) > 0
            OR
            ((cot.valorcuota * cnt.penalidadbase) - COALESCE((SELECT SUM(ppg2.amortizacion) FROM pagos ppg2 WHERE ppg2.idcronograma = cr.idcronograma AND ppg2.tipo = 'Penalidad'),0)) > 0
        )

    WHERE cnt.idcontrato = p_idcontrato
    GROUP BY cnt.idcontrato;
END$$
DELIMITER ;
*/

DROP PROCEDURE IF EXISTS sp_get_datos_reporte_notificacion_pdf;
DELIMITER $$
CREATE PROCEDURE sp_get_datos_reporte_notificacion_pdf(IN p_idcontrato INT)
BEGIN
    /*
      OBJETIVO:
      Retornar los datos necesarios para el PDF de notificación de cobranza,
      mostrando cuotas vencidas, penalidades calculadas (por ejemplo 10%), y total adeudado.
      Solo devuelve cuotas cuya fecha de pago ya pasó y que tengan saldo pendiente
      (ya sea de la cuota y/o de la penalidad).
      Optimizado para no repetir subconsultas por fila.
    */

    /* Agregamos pagos por idcronograma una sola vez */
    SELECT 
        -- Datos del cliente
        CASE 
            WHEN cl.tipocliente = 'P' THEN CONCAT(pe.apellidos, ' ', pe.nombres)
            WHEN cl.tipocliente = 'E' THEN e.razonsocial
        END AS nombre_cliente,

        CASE WHEN cl.tipocliente = 'P' THEN pe.tipodoc ELSE 'RUC' END AS tipo_documento,
        CASE WHEN cl.tipocliente = 'P' THEN pe.nrodoc WHEN cl.tipocliente = 'E' THEN e.ruc END AS numero_documento,
        CASE WHEN cl.tipocliente = 'P' THEN CONCAT(pe.direccion, ', ', dp.distrito, ', ', pp.provincia)
             WHEN cl.tipocliente = 'E' THEN CONCAT(e.direccion, ', ', de.distrito, ', ', peprov.provincia)
        END AS direccion_completa,

        -- Vehículo / contrato / financieros
        ma.marca,
        mo.modelo,
        mo.anio AS vehiculo_anio,
        v.color,
        v.placa,
        v.chasis AS numero_chasis,
        v.seriemotor AS numero_motor,

        cnt.idcontrato,
        cnt.fechainicio AS fecha_contrato,
        cnt.penalidadbase AS porcentaje_penalidad,

        cot.moneda,
        cot.valorcuota,

        -- Detalle legible (texto). Usamos REPLACE(FORMAT(...),',','') para quitar separador de miles
        GROUP_CONCAT(
            DISTINCT
            (CASE
                WHEN (ROUND(GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0),2) > 0)
                     AND (ROUND(GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0),2) = 0)
                THEN CONCAT(
                    'MORA DE ', UPPER(ELT(MONTH(cr.fechapago),
                        'enero','febrero','marzo','abril','mayo','junio','julio','agosto','setiembre','octubre','noviembre','diciembre'
                    )),
                    ' DE S/ ',
                    REPLACE(FORMAT(ROUND(GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0),2),2),',','')
                )

                WHEN ROUND(GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0),2) > 0
                THEN CONCAT(
                    'CUOTA DE ', UPPER(ELT(MONTH(cr.fechapago),
                        'enero','febrero','marzo','abril','mayo','junio','julio','agosto','setiembre','octubre','noviembre','diciembre'
                    )),
                    ' CON MORA DE S/ ',
                    REPLACE(FORMAT(
                        ROUND(GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0),2)
                        +
                        ROUND(GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0),2)
                    ,2),',','')
                )

                ELSE NULL
            END)
            ORDER BY cr.fechapago
            SEPARATOR ' || '
        ) AS detalle_cuotas_vencidas,

        -- Cantidad de cuotas con saldo pendiente
        SUM(
            CASE
                WHEN (GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0) > 0)
                  OR (GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0) > 0)
                THEN 1 ELSE 0
            END
        ) AS cantidad_cuotas_vencidas,

        -- Totales: sumando por cronograma los saldos pendientes y redondeando a 2 decimales
        ROUND(SUM( GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0) ),2) AS total_cuotas_vencidas,

        ROUND(SUM( GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0) ),2) AS total_penalidades_vencidas,

        -- total_deuda_vencida = SUM( cuota_rest + penal_rest ) redondeado a 2 decimales
        ROUND(SUM(
            GREATEST(cot.valorcuota - COALESCE(pgs.pagado_cuota,0),0)
            +
            GREATEST((cot.valorcuota * cnt.penalidadbase) - COALESCE(pgs.pagado_penal,0),0)
        ),2) AS total_deuda_vencida,

        MIN(cr.fechapago) AS fecha_primera_vencida,
        DATEDIFF(CURDATE(), MIN(cr.fechapago)) AS dias_atraso

    FROM contratos cnt
    JOIN cotizaciones cot ON cnt.idcotizacion = cot.idcotizacion
    JOIN clientes cl ON cot.idcliente = cl.idcliente
    LEFT JOIN personas pe ON cl.idpersona = pe.idpersona
    LEFT JOIN empresas e ON cl.idempresa = e.idempresa
    LEFT JOIN distritos dp ON pe.iddistrito = dp.iddistrito
    LEFT JOIN provincias pp ON dp.idprovincia = pp.idprovincia
    LEFT JOIN distritos de ON e.iddistrito = de.iddistrito
    LEFT JOIN provincias peprov ON de.idprovincia = peprov.idprovincia
    JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    JOIN modelos mo ON v.idmodelo = mo.idmodelo
    JOIN marcas ma ON mo.idmarca = ma.idmarca

    /* cronogramas vencidos */
    LEFT JOIN cronogramas cr ON cr.idcontrato = cnt.idcontrato
        AND cr.estado IN ('Pendiente', 'Vencido')
        AND cr.fechapago < CURDATE()

    /* agregación de pagos por idcronograma (evita subconsultas repetidas) */
    LEFT JOIN (
        SELECT 
            idcronograma,
            SUM(CASE WHEN tipo = 'Cuota' THEN amortizacion ELSE 0 END) AS pagado_cuota,
            SUM(CASE WHEN tipo = 'Penalidad' THEN amortizacion ELSE 0 END) AS pagado_penal
        FROM pagos
        GROUP BY idcronograma
    ) pgs ON pgs.idcronograma = cr.idcronograma

    WHERE cnt.idcontrato = p_idcontrato
      /* filtrar: solo cronogramas vencidos con saldo pendiente (si se requiere) */
      /* La agregación ya contempla cronogramas NULL (no hay cr) pero la SUM y GROUP_CONCAT ignorarán */
    GROUP BY cnt.idcontrato;
END$$
DELIMITER ;


/*
DROP PROCEDURE IF EXISTS sp_get_datos_reporte_notificacion_pdf;
DELIMITER $$

CREATE PROCEDURE sp_get_datos_reporte_notificacion_pdf(IN p_idcontrato INT)
BEGIN

    SELECT 
        -- Datos del cliente
        CASE 
            WHEN cl.tipocliente = 'P' THEN CONCAT(p.apellidos, ' ', p.nombres)
            WHEN cl.tipocliente = 'E' THEN e.razonsocial
        END AS nombre_cliente,

        CASE 
            WHEN cl.tipocliente = 'P' THEN p.tipodoc
            ELSE 'RUC'
        END AS tipo_documento,

        CASE 
            WHEN cl.tipocliente = 'P' THEN p.nrodoc
            WHEN cl.tipocliente = 'E' THEN e.ruc
        END AS numero_documento,

        CASE 
            WHEN cl.tipocliente = 'P' THEN CONCAT(p.direccion, ', ', dp.distrito, ', ', pp.provincia)
            WHEN cl.tipocliente = 'E' THEN CONCAT(e.direccion, ', ', de.distrito, ', ', pe.provincia)
        END AS direccion_completa,

        -- Datos del vehículo
        ma.marca,
        mo.modelo,
        mo.anio AS vehiculo_anio,
        v.color,
        v.placa,
        v.chasis AS numero_chasis,
        v.seriemotor AS numero_motor,

        -- Datos del contrato
        cnt.idcontrato,
        cnt.fechainicio AS fecha_contrato,
        cnt.penalidadbase AS porcentaje_penalidad,

        -- Datos financieros
        cot.moneda,
        cot.valorcuota,

        -- Detalle de cuotas vencidas
        GROUP_CONCAT(
            CONCAT(
                'Cuota ', cr.numcuota, 
                ' (', DATE_FORMAT(cr.fechapago, '%d/%m/%Y'), ')',
                ' | Cuota: ', FORMAT(cot.valorcuota, 2),
                ' | Penalidad: ', FORMAT(
                    IF(
                        -- Si ya se pagó la penalidad completamente, no se cobra de nuevo
                        (SELECT COALESCE(SUM(p.amortizacion), 0)
                         FROM pagos p
                         WHERE p.idcronograma = cr.idcronograma
                         AND p.tipo = 'Penalidad') >= (cot.valorcuota * cnt.penalidadbase),
                        0,
                        (cot.valorcuota * cnt.penalidadbase)
                    ), 2),
                ' | Total: ',
                FORMAT(
                    cot.valorcuota +
                    IF(
                        (SELECT COALESCE(SUM(p.amortizacion), 0)
                         FROM pagos p
                         WHERE p.idcronograma = cr.idcronograma
                         AND p.tipo = 'Penalidad') >= (cot.valorcuota * cnt.penalidadbase),
                        0,
                        (cot.valorcuota * cnt.penalidadbase)
                    ),
                    2
                )
            )
            ORDER BY cr.fechapago
            SEPARATOR ' || '
        ) AS detalle_cuotas_vencidas,

        COUNT(cr.idcronograma) AS cantidad_cuotas_vencidas,

        -- Totales calculados
        SUM(cot.valorcuota) AS total_cuotas_vencidas,
        SUM(
            IF(
                (SELECT COALESCE(SUM(p.amortizacion), 0)
                 FROM pagos p
                 WHERE p.idcronograma = cr.idcronograma
                 AND p.tipo = 'Penalidad') >= (cot.valorcuota * cnt.penalidadbase),
                0,
                (cot.valorcuota * cnt.penalidadbase)
            )
        ) AS total_penalidades_vencidas,

        SUM(
            cot.valorcuota +
            IF(
                (SELECT COALESCE(SUM(p.amortizacion), 0)
                 FROM pagos p
                 WHERE p.idcronograma = cr.idcronograma
                 AND p.tipo = 'Penalidad') >= (cot.valorcuota * cnt.penalidadbase),
                0,
                (cot.valorcuota * cnt.penalidadbase)
            )
        ) AS total_deuda_vencida,

        MIN(cr.fechapago) AS fecha_primera_vencida,
        DATEDIFF(CURDATE(), MIN(cr.fechapago)) AS dias_atraso

    FROM contratos cnt
    JOIN cotizaciones cot ON cnt.idcotizacion = cot.idcotizacion
    JOIN clientes cl ON cot.idcliente = cl.idcliente
    LEFT JOIN personas p ON cl.idpersona = p.idpersona
    LEFT JOIN empresas e ON cl.idempresa = e.idempresa
    LEFT JOIN distritos dp ON p.iddistrito = dp.iddistrito
    LEFT JOIN provincias pp ON dp.idprovincia = pp.idprovincia
    LEFT JOIN distritos de ON e.iddistrito = de.iddistrito
    LEFT JOIN provincias pe ON de.idprovincia = pe.idprovincia
    JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    JOIN modelos mo ON v.idmodelo = mo.idmodelo
    JOIN marcas ma ON mo.idmarca = ma.idmarca
    LEFT JOIN cronogramas cr ON cr.idcontrato = cnt.idcontrato
        AND cr.estado IN ('Pendiente', 'Vencido')
        AND cr.fechapago < CURDATE()

    WHERE cnt.idcontrato = p_idcontrato
    GROUP BY cnt.idcontrato;
END$$
DELIMITER ;
*/


/*
DROP PROCEDURE IF EXISTS sp_get_datos_reporte_notificacion;
DELIMITER $$
CREATE PROCEDURE sp_get_datos_reporte_notificacion(IN p_idcontrato INT)
BEGIN
    SELECT 
        -- Datos del cliente
        CASE 
            WHEN cl.tipocliente = 'P' THEN CONCAT(p.apellidos, ' ', p.nombres)
            WHEN cl.tipocliente = 'E' THEN e.razonsocial
        END AS nombre_cliente,
        
        CASE 
            WHEN cl.tipocliente = 'P' THEN p.tipodoc
            ELSE 'RUC'
        END AS tipo_documento,
        
        CASE 
            WHEN cl.tipocliente = 'P' THEN p.nrodoc
            WHEN cl.tipocliente = 'E' THEN e.ruc
        END AS numero_documento,
        
        CASE 
			WHEN cl.tipocliente = 'P' THEN CONCAT(p.direccion, ', ', dp.distrito, ', ', pp.provincia)
			WHEN cl.tipocliente = 'E' THEN CONCAT(e.direccion, ', ', de.distrito, ', ', pe.provincia)
		END AS direccion_completa,

        ma.marca,
        mo.modelo,
        mo.anio AS vehiculo_anio,
        v.placa,
        v.chasis AS numero_chasis,
        v.seriemotor AS numero_motor,
        v.color,
        
        -- Datos financieros
        cot.moneda,
        cot.valorcuota AS monto_cuota,
        
        -- Cuotas vencidas
        GROUP_CONCAT(
            CONCAT(
                DATE_FORMAT(cr.fechapago, '%d/%m/%Y'),
                ' - S/ ',
                FORMAT((cr.abonocapital + cr.interes + IFNULL(cr.penalidad, 0)), 2)
            )
            ORDER BY cr.fechapago
            SEPARATOR ' | '
        ) AS detalle_cuotas_vencidas,
        
        COUNT(cr.idcronograma) AS cantidad_cuotas_vencidas,
        
        SUM(cr.abonocapital + cr.interes + IFNULL(cr.penalidad, 0)) AS deuda_total_vencida,
        
        MIN(cr.fechapago) AS fecha_primera_vencida,
        
        -- Fecha del contrato
        cnt.fechainicio AS fecha_contrato,
        
        -- Datos adicionales
        DATEDIFF(CURDATE(), MIN(cr.fechapago)) AS dias_atraso
        
    FROM contratos cnt
    JOIN cotizaciones cot ON cnt.idcotizacion = cot.idcotizacion
    JOIN clientes cl ON cot.idcliente = cl.idcliente
    LEFT JOIN personas p ON cl.idpersona = p.idpersona
    LEFT JOIN empresas e ON cl.idempresa = e.idempresa
    LEFT JOIN distritos dp ON p.iddistrito = dp.iddistrito
    LEFT JOIN provincias pp ON dp.idprovincia = pp.idprovincia
    LEFT JOIN distritos de ON e.iddistrito = de.iddistrito
    LEFT JOIN provincias pe ON de.idprovincia = pe.idprovincia
    JOIN vehiculos v ON cot.idvehiculo = v.idvehiculo
    JOIN modelos mo ON v.idmodelo = mo.idmodelo
    JOIN marcas ma ON mo.idmarca = ma.idmarca
    LEFT JOIN cronogramas cr ON cr.idcontrato = cnt.idcontrato
        AND cr.estado IN ('Pendiente', 'Vencido')
        AND cr.fechapago < CURDATE()
    
    WHERE cnt.idcontrato = p_idcontrato
    
    GROUP BY 
        cnt.idcontrato, cl.tipocliente, p.apellidos, p.nombres, 
        p.tipodoc, p.nrodoc, e.razonsocial, e.ruc,
        dp.distrito, pp.provincia, de.distrito, pe.provincia,
        ma.marca, mo.modelo, mo.anio, v.placa, v.chasis, v.seriemotor, v.color,
        cot.moneda, cot.valorcuota, cnt.fechainicio;
END$$
DELIMITER ;*/

-- CALL sp_get_datos_reporte_notificacion(7);