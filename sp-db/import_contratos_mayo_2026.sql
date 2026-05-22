SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Mayo 2026: contratos + cuotas 1-5.
-- USE u322322994_motorpark;

DROP TABLE IF EXISTS import_contratos_mayo_2026;
CREATE TABLE import_contratos_mayo_2026 (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_contrato VARCHAR(50) NOT NULL,
  fecha_firma DATE NULL,
  dni VARCHAR(20) NULL,
  cliente_nombre VARCHAR(255) NULL,
  direccion TEXT NULL,
  ciudad VARCHAR(100) NULL,
  celular VARCHAR(50) NULL,
  caracteristicas TEXT NULL,
  color VARCHAR(80) NULL,
  chasis VARCHAR(100) NULL,
  motor VARCHAR(100) NULL,
  placa VARCHAR(50) NULL,
  monto_inicial DECIMAL(15,2) NULL,
  monto_valor DECIMAL(15,2) NULL,
  tipo_contrato VARCHAR(100) NULL,
  fecha_comienzo DATE NULL,
  fecha_vencimiento DATE NULL,
  vendedor VARCHAR(255) NULL,
  condicion VARCHAR(100) NULL,
  duracion_meses INT NULL,
  cuota_mensual DECIMAL(15,2) NULL,
  pct_mora DECIMAL(10,4) NULL,
  mora_monto DECIMAL(15,2) NULL,
  total_con_mora DECIMAL(15,2) NULL,
  cuota_1 VARCHAR(255) NULL, fecha_pago_1 DATE NULL, gps_1 VARCHAR(120) NULL, mora_1 VARCHAR(120) NULL, deudas_1 VARCHAR(255) NULL,
  cuota_2 VARCHAR(255) NULL, fecha_pago_2 DATE NULL, mora_2 VARCHAR(120) NULL, gps_2 VARCHAR(120) NULL, deudas_2 VARCHAR(255) NULL,
  cuota_3 VARCHAR(255) NULL, fecha_pago_3 DATE NULL, mora_3 VARCHAR(120) NULL, gps_3 VARCHAR(120) NULL, deudas_3 VARCHAR(255) NULL,
  cuota_4 VARCHAR(255) NULL, fecha_pago_4 DATE NULL, mora_4 VARCHAR(120) NULL, gps_4 VARCHAR(120) NULL, deudas_4 VARCHAR(255) NULL,
  cuota_5 VARCHAR(255) NULL, fecha_pago_5 DATE NULL, mora_5 VARCHAR(120) NULL, gps_5 VARCHAR(120) NULL,
  mas_deudas_pendientes VARCHAR(500) NULL,
  anio_fuente INT NOT NULL DEFAULT 2026,
  creado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_import_chasis (chasis(20)),
  KEY idx_import_dni (dni),
  KEY idx_import_id_contrato (id_contrato)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO import_contratos_mayo_2026 (
  id_contrato, fecha_firma, dni, cliente_nombre, direccion, ciudad, celular, caracteristicas, color, chasis, motor, placa, monto_inicial, monto_valor, tipo_contrato, fecha_comienzo, fecha_vencimiento, vendedor, condicion, duracion_meses, cuota_mensual, pct_mora, mora_monto, total_con_mora, cuota_1, fecha_pago_1, gps_1, mora_1, deudas_1, cuota_2, fecha_pago_2, mora_2, gps_2, deudas_2, cuota_3, fecha_pago_3, mora_3, gps_3, deudas_3, cuota_4, fecha_pago_4, mora_4, gps_4, deudas_4, cuota_5, fecha_pago_5, mora_5, gps_5, mas_deudas_pendientes, anio_fuente
) VALUES
('May-001-2026','2026-05-05','21421404','HUAMAN TUCTA ZENON','Urb .la palma granda Mzna Puerta N°07 Ica','ICA','956 855 690','BAJAJ AUTORIKSHA 2T LONA GASOLINA','Rojo','MD2A47AX3RWD80021','24XWD17114','EN TRÁMITE',1500.0,9600.0,'COMPRAVENTA','2026-06-05','2027-05-05','KARINA VENTURA','EMPRESA',12,914.93,10.0,92.49,1007.42,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2026),
('May-002-2026','2026-05-02','21881478','JUAN CARLOS SEBASTIAN CARBAJAL','Mina de oro Psje San Martin #397','CHINCHA','926 766 083','BAJAJ PULSAR 200 NS UG','Negro - Rojo','MD2A36FX3SCG05275','JLXCRG73629','EN TRÁMITE',3960.0,13200.0,'COMPRAVENTA','2026-06-02','2027-11-02','MARTHA CORDOVA','EMPRESA',18,791.69,10.0,80.17,871.86,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2026),
('May-003-2026','2026-05-05','42887125','TIPICIANO BIZARRA JORGE LUIS','Upis las margaritas Mz A LT16 San luis - cañete','CHINCHA','980 552 255','HYUNDAI GRAND i10 HB MT COMFORT GLP','Plata','MALB251AATM699944','G3LASM529979','EN TRÁMITE',10000.0,48840.0,'COMPRAVENTA','2026-06-05','2029-05-05','LITMAN QUISPE','EMPRESA',36,2129.1,10.0,213.91,2343.01,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2026);

SELECT COUNT(*) AS filas_importadas FROM import_contratos_mayo_2026;