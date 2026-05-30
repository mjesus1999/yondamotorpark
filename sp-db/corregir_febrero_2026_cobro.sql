-- Correcciones febrero 2026 en registro_ventas_vehiculares (clientes revisados manualmente).
-- Ejecutar DESPUÉS de import_contratos_febrero_2026.sql en producción:
--   mysql -u yhondb -p motorpark < sp-db/import_contratos_febrero_2026.sql
--   mysql -u yhondb -p motorpark < sp-db/corregir_febrero_2026_cobro.sql

SET NAMES utf8mb4;

-- Feb-002 Karla (76477981)
UPDATE registro_ventas_vehiculares SET motor = 'AK4AT41B9027', direccion = 'CL. Union Puerta 104 Chacarita Sunampe', fecha_inicio_credito = '2026-03-26', cuota_base = 1016.58, monto_interes = 101.66, cuota_total_mensual = 1118.24, total_con_mora = 1118.24, numero_cuota_pagada = 1 WHERE dni_cliente = '76477981' AND chasis = 'MD6M14LAXT4AA7164';

-- Feb-005 Abraham (46126929)
UPDATE registro_ventas_vehiculares SET fecha_inicio_credito = '2026-03-19', cuota_base = 1495.00, monto_interes = 149.50, cuota_total_mensual = 1645.08, total_con_mora = 1645.08, numero_cuota_pagada = 2 WHERE dni_cliente = '46126929' AND chasis = 'MD2A45AX3TWG00530';

-- Feb-007 Aburto (21871258) — ya correcto en registro; sin cambios

-- Feb-008 César (40712273)
UPDATE registro_ventas_vehiculares SET motor = '24XWSE26502', pago_inicial = 3000.00, fecha_inicio_credito = '2026-03-12', cuota_base = 805.30, numero_cuota_pagada = 1 WHERE dni_cliente = '40712273' AND chasis = 'MD2A49AX8TWE00153';

-- Feb-009 José Manuel (74999342)
UPDATE registro_ventas_vehiculares SET motor = '24XWSD25876', fecha_inicio_credito = '2026-03-18' WHERE dni_cliente = '74999342' AND chasis = 'MD2A49AX2TWD60037';

-- Feb-010 Juan (45954203)
UPDATE registro_ventas_vehiculares SET chasis = 'MD2A45AX5SWL11791', precio_total = 14500.00, direccion = 'Francisco felix / PSJE Almeyda Sector 2 Mz P LT 12', placa = '2162-AY', fecha_inicio_credito = '2026-03-14', cuota_base = 1298.97, monto_interes = 130.90, cuota_total_mensual = 1429.87, total_con_mora = 1429.87, numero_cuota_pagada = 1 WHERE dni_cliente = '45954203' AND chasis IN ('MD2A45SWL11791', 'MD2A45AX5SWL11791');

-- Feb-011 Gladys (43090872)
UPDATE registro_ventas_vehiculares SET nombre_cliente = 'Gladys Betel Barrientos Quispe', fecha_inicio_credito = '2026-03-09', cuota_base = 2617.00, monto_interes = 261.70, cuota_total_mensual = 2878.63, total_con_mora = 2878.63, numero_cuota_pagada = 3 WHERE dni_cliente = '43090872' AND chasis = 'MALB251AATM676127';

-- Feb-015 Carbajal (77690811)
UPDATE registro_ventas_vehiculares SET chasis = 'LF3PCJ5L1RB000381', motor = '157FMI4R1025271', fecha_inicio_credito = '2026-03-04', cuota_base = 317.02, monto_interes = 31.70, cuota_total_mensual = 348.72, total_con_mora = 348.72 WHERE dni_cliente = '77690811' AND chasis IN ('157FMI4R1025272', 'LF3PCJ5L1RB000381');

-- Feb-016 Renzo (43489179)
UPDATE registro_ventas_vehiculares SET chasis = 'MD2A49AX4TWE00165', precio_total = 14090.00, fecha_inicio_credito = '2026-03-17', plazo_meses = 12, fecha_fin_credito = '2027-02-17', cuota_base = 1253.00, monto_interes = 125.30, cuota_total_mensual = 1378.93, total_con_mora = 1378.93, numero_cuota_pagada = 1 WHERE dni_cliente = '43489179' AND chasis IN ('MD2A49AXTWE00165', 'MD2A49AX4TWE00165');

-- Feb-018 Miguel (42349901)
UPDATE registro_ventas_vehiculares SET chasis = 'MD6M1LLH8T4AA4030', fecha_inicio_credito = '2026-03-07', cuota_base = 1405.20, cuota_total_mensual = 1546.69, total_con_mora = 1546.69, numero_cuota_pagada = 3 WHERE dni_cliente = '42349901' AND chasis IN ('MD6M1LLH8T4A4030', 'MD6M1LLH8T4AA4030');

-- Feb-019 Roxana (47485795) — DNI corregido en producción si quedó 47485195
UPDATE registro_ventas_vehiculares SET dni_cliente = '47485795', motor = '24XWG26985', color = 'Negro', fecha_inicio_credito = '2026-03-27', cuota_base = 1253.00, monto_interes = 125.30, cuota_total_mensual = 1378.93, total_con_mora = 1378.93 WHERE chasis = 'MD2A49AX2TWG60029' AND dni_cliente IN ('47485195', '47485795');

-- Feb-038 Marion (40257923)
UPDATE registro_ventas_vehiculares SET fecha_inicio_credito = '2026-03-20', cuota_base = 746.50, monto_interes = 74.65, cuota_total_mensual = 821.04, total_con_mora = 821.04, numero_cuota_pagada = 2 WHERE dni_cliente = '40257923' AND chasis = 'MD2A49AX4SWM10321';
