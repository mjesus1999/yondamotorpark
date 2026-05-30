-- Corrección Karla (76477981) y Juan (45954203) — datos que afectan cobro/cronograma.
-- Ejecutar en producción:
--   mysql -u yhondb -p motorpark < sp-db/corregir_karla_juan_cobro.sql

SET NAMES utf8mb4;

-- ─── KARLA BRIGITTE MARTINEZ MAGALLANES (Feb-002) ───
-- Impacto cobro: fecha_inicio_credito define el día de vencimiento y cuándo aplica mora (+3 días).
-- Motor/chasis/dirección no afectan el monto a cobrar.

UPDATE registro_ventas_vehiculares
SET
    motor = 'AK4AT41B9027',
    direccion = 'CL. Union Puerta 104 Chacarita Sunampe',
    fecha_inicio_credito = '2026-03-26',
    numero_cuota_pagada = 1
WHERE dni_cliente = '76477981'
  AND chasis = 'MD6M14LAXT4AA7164';

UPDATE import_contratos_febrero_2026
SET
    tipo_contrato = 'COMPRAVENTA',
    fecha_comienzo = '2026-03-26',
    fecha_vencimiento = '2028-02-26',
    vendedor = NULL,
    condicion = 'EMPRESA',
    duracion_meses = 24,
    cuota_mensual = 1016.58,
    pct_mora = 10.0,
    mora_monto = 101.66,
    total_con_mora = 1118.24,
    cuota_1 = 'S/.1,017.00',
    fecha_pago_1 = '2026-03-25',
    gps_1 = 'S/.10.00',
    mora_1 = NULL,
    deudas_1 = 'Nº:NULL | Pago mora:S/.10.00'
WHERE dni = '76477981'
  AND id_contrato = 'Feb-002-2026';

-- ─── JUAN MANUEL AVALOS DIAZ (Feb-010) ───
-- Impacto cobro: precio_total erróneo (11500) hacía calcular monto a financiar = 8500 en lugar de 11500.

UPDATE registro_ventas_vehiculares
SET
    chasis = 'MD2A45AX5SWL11791',
    precio_total = 14500.00,
    direccion = 'Francisco felix / PSJE Almeyda Sector 2 Mz P LT 12',
    placa = '2162-AY',
    fecha_inicio_credito = '2026-03-14',
    monto_interes = 130.90,
    cuota_total_mensual = 1429.87,
    total_con_mora = 1429.87,
    numero_cuota_pagada = 1
WHERE dni_cliente = '45954203'
  AND chasis IN ('MD2A45SWL11791', 'MD2A45AX5SWL11791');

UPDATE import_contratos_febrero_2026
SET
    tipo_contrato = 'COMPRAVENTA',
    fecha_comienzo = '2026-03-14',
    fecha_vencimiento = '2027-02-14',
    vendedor = 'MARTHA CORDOVA',
    condicion = 'EMPRESA',
    duracion_meses = 12,
    cuota_mensual = 1298.97,
    pct_mora = 10.0,
    mora_monto = 130.90,
    total_con_mora = 1429.87,
    cuota_1 = 'S/.1,298.97',
    fecha_pago_1 = '2026-03-10',
    gps_1 = 'S/.10.00',
    mora_1 = NULL,
    deudas_1 = 'Nº:NULL | Pago mora:S/.10.00'
WHERE dni = '45954203'
  AND id_contrato = 'Feb-010-2026';
