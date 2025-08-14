USE motorpark;

INSERT INTO formatocotizacion (tipocotizacion, fechainicio, fechafin)
VALUES
  ('Independiente formal',      '2025-01-01', NULL),
  ('Independiente Informal',    '2025-07-01', NULL),
  ('Dependiente',               '2025-03-15', NULL),
  ('Contado Empresas',          '2025-07-21', NULL),
  ('Contado Persona Natural',   '2025-07-21', NULL);

SELECT * FROM formatocotizacion;

INSERT INTO requisitos (requisito) 
VALUES
  ('FOTOCOPIA DNI DEL TITULAR Y CONYUGUE'),
  ('COPIA DEL ÚLTIMO RECIBO PAGADO DE SERVICIOS (LUZ O AGUA)'),
  ('COPIA SIMPLE DE VIVIENDA (TÍTULO DE PROPIEDAD / CERTIFICADO DE POSESIÓN, COPIA LITERAL)'),
  ('DECLARACION JURADA DE INGRESOS'),
  ('LICENCIA DE CONDUCIR'),
  ('RECORD DE PAPELETAS'),
  ('DNI AVAL (DNI CONYUGE DE SER NECESARIO)'),
  ('EVALUACION DE GASTOS FAMILIARES'),
  ('30% DE INICIAL COMO MINIMO (aumenta según precio de la unidad)'),
  ('VERIFICACION DOMICILIARIA Y LABORAL'),
  ('PAGO UNICO POR GASTOS ADMINISTRATIVOS S/1,500.00'),
  ('SEGURO VEHICULAR (bajo evaluación)'),
  ('GPS SATELITAL'),
  ('RECIBO DE SERVICIOS'),
  ('BOLETAS DE PAGO');

SELECT * FROM requisitos;



-- DETALLE DE LOS REQUISITOS PARA EL FORMATO DE COTIZACION
-- Independiente formal (idformato = 1), requisitos según listado (incluye RECIBO DE SERVICIOS = 14)
INSERT INTO detallerequisitos (idformato, idrequisito) VALUES
  (1, 1),
  (1, 2),
  (1, 3),
  (1, 4),
  (1, 5),
  (1, 14),
  (1, 6),
  (1, 7),
  (1, 8),
  (1, 9),
  (1, 10),
  (1, 11),
  (1, 12),
  (1, 13);
  
-- Independiente Informal (idformato = 2), requisitos 1 a 13
INSERT INTO detallerequisitos (idformato, idrequisito) VALUES
  (2, 1),
  (2, 2),
  (2, 3),
  (2, 4),
  (2, 5),
  (2, 6),
  (2, 7),
  (2, 8),
  (2, 9),
  (2, 10),
  (2, 11),
  (2, 12),
  (2, 13);

-- Dependiente (idformato = 3), requisitos según listado (incluye BOLETAS DE PAGO = 15)
INSERT INTO detallerequisitos (idformato, idrequisito) VALUES
  (3, 1),
  (3, 2),
  (3, 3),
  (3, 15),
  (3, 7),
  (3, 8),
  (3, 9),
  (3, 10),
  (3, 11),
  (3, 12),
  (3, 13);



  SELECT * FROM clientes;
  SELECT * FROM vehiculos;

-- INSERTAR EN COTIZACIONES:

INSERT INTO cotizaciones (
    idformato,
    idcliente,
    idasesor,
    idvehiculo,
    moneda,
    precioventa,
    vigenciadias,
    inicial,
    numcuotas,
    valorcuota,
    estadocotizacion
) VALUES (
    1,          -- idformato
    8,          -- idcliente 
    2,          -- idasesor
    47,          -- idvehiculo 
    'PEN',      -- moneda
    60050.00,   -- precioventa
    7,          -- vigenciadias
    10000.00,    -- inicial
    36,         -- numcuotas
    2196.00,    -- valorcuota
    'A'         -- estado inicial 
);

SELECT * FROM cotizaciones;
UPDATE cotizaciones SET estadocotizacion = 'A' WHERE idcotizacion = 1; 

UPDATE cotizaciones SET precioventa = 50050, inicial = 10000 WHERE idcotizacion = 1; 
SELECT * FROM vehiculos;

INSERT INTO vehiculos (
    idmodelo, version, condicion, idcombustible, disponibilidad, idlogistica, idlocal, origen, creado
) VALUES (
    1, 'Versión D', 'nuevo', 1, 'vendido', 2, 1, 'CTZ', NOW()
);

SELECT * FROM locales;
SELECT * FROM cotizaciones;


INSERT INTO contratos (
    idlocal,
    idcotizacion,
    idlogistica,
    fechainicio,
    diapago,
    fecharevision,
    observaciones
) VALUES (
    1,         -- idlocal
    2,         -- idcotizacion 
    2,         -- idlogistica 
    '2025-08-13', -- fechainicio
    12,        -- día de pago
    '2025-09-13', -- fecharevision
    'Contrato inicial para entrega de vehículo.'
);

UPDATE contratos SET diapago = 13 WHERE idcontrato = 2;
SELECT * FROM contratos;

USE motorpark;
DROP PROCEDURE generar_cronograma;

update cronogramas SET fechapago = '2025-08-07', penalidad = 300.00 WHERE numcuota = 1;




DELIMITER $
CREATE PROCEDURE generar_cronograma(IN p_idcontrato INT, IN p_tasaMensual DECIMAL(15,9))
BEGIN
    DECLARE v_precioVenta DECIMAL(10,2);
    DECLARE v_inicial DECIMAL(10,2);
    DECLARE v_cuotas INT;
    DECLARE v_valorCuota DECIMAL(10,2);
    DECLARE v_diapago INT;
    DECLARE v_fechaInicio DATE;
   
    DECLARE v_saldoCapital DECIMAL(15,4);
    DECLARE v_interes DECIMAL(10,2);
    DECLARE v_abonoCapital DECIMAL(10,2);
    DECLARE v_fechaPago DATE;
    DECLARE v_numCuota INT DEFAULT 1;
   
    -- Variables para control de redondeos
    DECLARE v_montoFinanciado DECIMAL(10,2);
    DECLARE v_totalInteresReal DECIMAL(15,4) DEFAULT 0.0000;
    DECLARE v_totalAbonoReal DECIMAL(15,4) DEFAULT 0.0000;
    DECLARE v_interesExacto DECIMAL(15,4);
   
    -- 1️ Limpiar cronogramas existentes
    DELETE FROM cronogramas WHERE idcontrato = p_idcontrato;
   
    -- 2️ Obtener datos del contrato y cotización
    SELECT ctz.precioventa, ctz.inicial, ctz.numcuotas, ctz.valorcuota, ctr.diapago, ctr.fechainicio
    INTO v_precioVenta, v_inicial, v_cuotas, v_valorCuota, v_diapago, v_fechaInicio
    FROM contratos ctr
    INNER JOIN cotizaciones ctz ON ctr.idcotizacion = ctz.idcotizacion
    WHERE ctr.idcontrato = p_idcontrato;
   
    -- 3️ Calcular valores base
    SET v_montoFinanciado = v_precioVenta - v_inicial;
    SET v_saldoCapital = v_montoFinanciado;
    SET v_fechaPago = v_fechaInicio;
   
    -- 4️ Generar cuotas con control de redondeo
    WHILE v_numCuota <= v_cuotas DO
        -- Avanzar fecha de pago
        SET v_fechaPago = DATE_ADD(v_fechaPago, INTERVAL 1 MONTH);
        SET v_fechaPago = MAKEDATE(YEAR(v_fechaPago), 1)
                          + INTERVAL (MONTH(v_fechaPago)-1) MONTH
                          + INTERVAL (v_diapago-1) DAY;
       
        -- Calcular interés exacto para esta cuota
        SET v_interesExacto = v_saldoCapital * (p_tasaMensual / 100);
       
        IF v_numCuota < v_cuotas THEN
            -- Cuotas normales: redondear interés
            SET v_interes = ROUND(v_interesExacto, 2);
            SET v_abonoCapital = v_valorCuota - v_interes;
           
            -- Actualizar acumuladores con valores exactos
            SET v_totalInteresReal = v_totalInteresReal + v_interes;
            SET v_totalAbonoReal = v_totalAbonoReal + v_abonoCapital;
           
        ELSE
            -- Última cuota: ajustar para cerrar exacto
            SET v_abonoCapital = ROUND(v_saldoCapital, 2);
            SET v_interes = v_valorCuota - v_abonoCapital;
           
            -- Verificar que el interés no sea negativo
            IF v_interes < 0 THEN
                SET v_abonoCapital = v_valorCuota;
                SET v_interes = 0.00;
            END IF;
        END IF;
       
        -- Insertar cronograma
        INSERT INTO cronogramas (
            idcontrato, fechapago, interes, abonocapital, numcuota, penalidad, saldocapital
        ) VALUES (
            p_idcontrato,
            v_fechaPago,
            v_interes,
            v_abonoCapital,
            v_numCuota,
            0.00,
            CASE
                WHEN v_numCuota = v_cuotas THEN 0.00
                ELSE ROUND(v_saldoCapital - v_abonoCapital, 2)
            END
        );
       
        -- Actualizar saldo capital
        SET v_saldoCapital = v_saldoCapital - v_abonoCapital;
        SET v_numCuota = v_numCuota + 1;
    END WHILE;
END$
DELIMITER ;
CALL generar_cronograma(2, 4.263224089);
SELECT * FROM cronogramas;


SELECT SUM(interes) AS TotalInteres, SUM(abonocapital) AS TotalAbonos, COUNT(*) AS Filas
FROM cronogramas WHERE idcontrato = 1;

SELECT SUM(interes)+SUM(abonocapital) AS TotalPagado
FROM cronogramas WHERE idcontrato = 1;
