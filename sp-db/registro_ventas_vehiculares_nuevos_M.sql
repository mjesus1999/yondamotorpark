-- Nuevos registros (M1..M3) desde Excel.
-- Ejecutar en VPS:
--   mysql -u yhondb -p motorpark < /var/www/yondamotorpark/sp-db/registro_ventas_vehiculares_nuevos_M.sql
--
-- Fechas: en este bloque vienen en formato dd/mm/yyyy (ej. 14/3/2026), por eso se convierten a YYYY-MM-DD.
-- "NUMERO DE CUOTA PAGADA" -> numero_cuota_pagada

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

INSERT INTO registro_ventas_vehiculares (
  fecha_venta,
  dni_cliente,
  nombre_cliente,
  aval,
  dni_aval,
  direccion,
  modelo,
  marca,
  chasis,
  motor,
  color,
  estado_tramite,
  placa,
  telefono_1,
  telefono_2,
  precio_total,
  pago_inicial,
  deudas_pendientes,
  deudas_pendientes_detalle,
  fecha_inicio_credito,
  plazo_meses,
  fecha_fin_credito,
  cuota_base,
  tasa_interes,
  monto_interes,
  cuota_total_mensual,
  mora_3_dias,
  total_con_mora,
  numero_cuota_pagada
) VALUES
('2026-03-14','21884676','Teodoro Sandro Jorges Odria',NULL,NULL,'Av. Grocio Prado 1210 - Pueblo Nuevo - Chincha Alta','RE AUTORIKSHA TORITO 4T','BAJAJ','MD2A45AX9TWJ00093','AZXWSJ81948','Azul','En tramite',NULL,'987470209',NULL,21444.00,3000.00,NULL,NULL,'2026-03-14',24,'2028-03-14',1339.28,10.00,133.93,1473.21,133.93,1473.21,NULL),
('2026-03-14','43859734','Jorge Luis Saldaña Carmona',NULL,NULL,'AAH. Señor de los Milagros MZ K1 LT 06- chincha Alta','KINGS LS DELUXE PLUS','TVS','MD6M14LA5T4AA7105','AK4AT47B8295','Negro','En tramite',NULL,'944506540',NULL,17000.00,3000.00,NULL,NULL,'2026-03-14',8,'2026-11-14',2167.87,10.00,216.79,2384.66,216.79,2384.66,2),
('2026-04-12','77416004','Valdez Vellantoy Joselito Jose',NULL,NULL,'Calle San Jose. MZ. 26 LT. 11 - Pisco','CB 190R','HONDA','ME4MC5690TA010210','MC56E5015973','Rojo','En tramite',NULL,'906515307','944613669',11200.00,3360.00,NULL,NULL,'2026-04-12',18,'2028-04-06',671.74,10.00,67.17,738.91,67.17,738.91,1)
;

