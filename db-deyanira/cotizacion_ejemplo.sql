
-- === EJEMPLO: crear cotización "vencida" + 2 opciones de financiamiento ===
-- Ajusta estos IDs según tu base de datos real:
SET @ID_FORMATO = 2;    -- idformato existente
SET @ID_CLIENTE = 11;    -- idcliente existente
SET @ID_ASESOR  = 10;    -- idcolaborador existente (idasesor)
SET @ID_VEHICULO= 1;    -- idvehiculo existente

-- Fecha "creado" antigua para asegurar que esté vencida (creado + vigenciadias < CURDATE())
SET @CREADO = '2025-08-01 10:00:00'; -- ejemplo: 1 de agosto 2025
SET @VIGENCIA_DIAS = 7;               -- vigencia menor que la diferencia con CURDATE()

-- Precios / valores
SET @MONEDA = 'PEN';
SET @PRECIO_VENTA = 50000.00;
SET @INICIAL = 10000.00;
SET @NUM_CUOTAS_RESUMEN = 24;
SET @VALOR_CUOTA_RESUMEN = 1666.67;
SET @GASTOS_ADMIN = 1800.00;

-- Inserta cotización (siempre crea nueva)
INSERT INTO cotizaciones
  (idformato, idcliente, idasesor, idvehiculo, moneda, precioventa, vigenciadias, inicial, numcuotas, valorcuota, gastosadministrativos, creado)
VALUES
  (@ID_FORMATO, @ID_CLIENTE, @ID_ASESOR, @ID_VEHICULO, @MONEDA, @PRECIO_VENTA, @VIGENCIA_DIAS, @INICIAL, @NUM_CUOTAS_RESUMEN, @VALOR_CUOTA_RESUMEN, @GASTOS_ADMIN, @CREADO);

-- Obtener id generado
SET @ID_COT = LAST_INSERT_ID();

-- Insertar dos opciones de financiamiento: 24 y 60 meses
INSERT INTO cotizacion_financiamiento
  (idcotizacion, numcuotas, inicial, valorcuota, moneda, precioventa)
VALUES
  (@ID_COT, 24, @INICIAL, 1666.67, @MONEDA, @PRECIO_VENTA),
  (@ID_COT, 60, @INICIAL, 700.00,  @MONEDA, @PRECIO_VENTA);

-- Mostrar lo que acabamos de crear
SELECT * FROM cotizaciones WHERE idcotizacion = @ID_COT;
SELECT * FROM cotizacion_financiamiento WHERE idcotizacion = @ID_COT;

