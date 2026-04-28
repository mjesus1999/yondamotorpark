-- Tabla e importación de registro de ventas vehiculares.
-- Base de datos: motorpark
--
-- En el VPS (ajusta usuario si no es yhondb):
--   mysql -u yhondb -p motorpark < /var/www/yondamotorpark/sp-db/registro_ventas_vehiculares.sql
-- O en phpMyAdmin: Importar este archivo o pegar y ejecutar.
--
-- Cada ejecución vacía la tabla y vuelve a insertar (evita filas duplicadas).

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE IF NOT EXISTS registro_ventas_vehiculares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_venta DATE,
    dni_cliente VARCHAR(15),
    nombre_cliente VARCHAR(150),
    aval VARCHAR(150),
    dni_aval VARCHAR(15),
    direccion VARCHAR(255),
    modelo VARCHAR(100),
    marca VARCHAR(50),
    chasis VARCHAR(100),
    motor VARCHAR(100),
    color VARCHAR(50),
    estado_tramite VARCHAR(50),
    telefono_1 VARCHAR(20),
    telefono_2 VARCHAR(20),
    precio_total DECIMAL(10,2),
    pago_inicial DECIMAL(10,2),
    fecha_inicio_credito DATE,
    plazo_meses INT,
    fecha_fin_credito DATE,
    cuota_base DECIMAL(10,2),
    tasa_interes DECIMAL(5,2),
    monto_interes DECIMAL(10,2),
    cuota_total_mensual DECIMAL(10,2)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELETE FROM registro_ventas_vehiculares;

INSERT INTO registro_ventas_vehiculares (
    fecha_venta, dni_cliente, nombre_cliente, aval, dni_aval, direccion,
    modelo, marca, chasis, motor, color, estado_tramite, telefono_1, telefono_2,
    precio_total, pago_inicial, fecha_inicio_credito, plazo_meses, fecha_fin_credito,
    cuota_base, tasa_interes, monto_interes, cuota_total_mensual
) VALUES
('2026-02-28', '70856736', 'Graam Antony Muñoz Huaman', NULL, NULL, 'Comunidad CCANIS BAJA S/N', 'RE AUTORIKSHA TORITO 4T R', 'BAJAJ', 'MD2A25AX0TWC00351', NULL, 'Azul', NULL, '955 283 341', NULL, 16700.00, 0.00, NULL, 27, NULL, 1182.11, 10.00, 118.21, 1300.32),
('2026-02-27', '76309859', 'Cristofer Alexander Siguas Sanabria', NULL, NULL, 'CP. Camino a los resyes nro. Puerta 294', 'RC200', 'KTM', 'VBKJYC409RN14986', 'R93649098', 'Negra', 'En tramite', '963 889 086', '944 298 142', 19900.00, 5970.00, '2026-02-27', 12, '2027-02-27', 1573.45, 10.00, 157.35, 1730.80),
('2026-02-28', '47485195', 'Fernandez Vega Roxana', NULL, NULL, 'El hurango Tierra Prometida Mz G Lt 05', 'AUTORIKSHA', 'BAJAJ', 'MD2A49AX2TWG60029', '24XWSG26985', 'Rojo', 'En tramite', '985 226 840', NULL, 14090.00, 3000.00, '2026-02-27', 12, '2027-02-27', 1252.66, 10.00, 125.27, 1377.93),
('2026-02-28', '71551495', 'Luis Angel Yauricasa Torres', NULL, NULL, 'Mzna D Lt- 30 Santa Rosa', 'AUTORIKSHA', 'BAJAJ', 'MD2A49AX3SWH00178', 'S4XWRH23493', 'Rojo', 'En tramite', '913 808 142', NULL, 9600.00, 3000.00, '2026-02-28', 18, '2027-08-28', 565.50, 10.00, 56.55, 622.05),
('2026-02-27', '42275874', 'Omar Oscar Gutierrez Peña', NULL, NULL, 'Mzna D. 11 A Sin barrio C.P Nustra Señora', 'PICANTO LX PLUS MT 1.0', 'KIA', 'KNAB2511AVT460468', 'G3LASP241367', 'Negro', 'En tramite', '902 771 125', NULL, 50004.00, 13500.00, '2026-02-27', 60, '2031-02-27', 1869.11, 10.00, 186.91, 2056.02),
('2026-02-21', '21861713', 'Gloria Isabel Garcia Canto', NULL, NULL, 'Calle El Carmen 117', 'TVS KING LS', 'TVS', 'MD6M14LA7T4AA7218', 'AK4AT46B9105', 'Azul', 'En tramite', '942 130 236', NULL, 17000.00, 3000.00, '2026-03-21', 24, '2028-02-21', 1016.58, 10.00, 101.66, 1118.24),
('2026-02-20', '40257923', 'Yllescas Cordova Marion Yanet', NULL, NULL, 'URB Los medanos mzna Q puerta 29', 'RE AUTORIKSHA TORITO 2T LPG', 'BAJAJ', 'MD2A49AX4SWM10321', '24XWSM24439', 'Verde', 'En tramite', '915 173 600', NULL, 9600.00, 3000.00, '2026-02-20', 12, '2027-02-20', 745.49, 10.00, 74.55, 820.04),
('2026-02-18', '74999342', 'Jose Manuel Artunduagüa Rebaza', NULL, NULL, 'Urb San Joaquin Mzna x2 Lt 20', 'RE AUTORIKSHA TORITO 2T LPG', 'BAJAJ', 'MD2A49AX2TWD60037', '2AXWSD24876', 'Rojo', 'En tramite', '976 103 055', NULL, 14090.00, 3000.00, '2026-02-18', 24, '2028-02-18', 805.28, 10.00, 80.53, 885.81),
('2026-02-18', '75843735', 'luis Alejandro Nolazco Napa', 'Maria Del Carmen Napa Arcos', '21875213', 'Av. 28 de julio 204', 'GRAND I10 CONFORT GLP', 'HYUNDAI', 'MALB251AATM696463', 'G3LASM523395', 'Azul', 'En tramite', '990 133 225', NULL, 48840.00, 10000.00, '2026-02-18', 36, '2029-02-18', 2129.10, 10.00, 212.91, 2342.01),
('2026-02-17', '43489179', 'Renzo Mariano Caro Zapata', NULL, NULL, 'Av. Miguel Grau 157', 'AUTORIKSHA 2T LPG DUAL', 'BAJAJ', 'MD2A49AXTWE00165', '24XWSE26420', 'Rojo', 'En tramite', '912 508 426', NULL, 15034.00, 3000.00, '2026-02-13', 24, '2028-02-13', 873.83, 10.00, 87.38, 961.21),
('2026-02-11', '43051064', 'Hernandez Yauri Neftali Carlos', NULL, NULL, 'AA.HH STA ROSA DE LIMA F-2', 'RE AUTORISKA TORITO 2T LPG', 'BAJAJ', 'MD2A49AX9TWE00114', '24XWSE26488', 'Azul', 'En tramite', '927 876 977', NULL, 14090.00, 3000.00, '2026-02-10', 24, '2028-02-10', 805.28, 10.00, 80.53, 885.81),
('2026-02-27', '74929915', 'Flavio Augusto Calua Bautista', 'Andrea Sofia Talledo Rojas', '76477775', 'Calle Francisco Moreno 628', 'GRAND I10 CONFORT GLP', 'HYUNDAI', 'MALB251AATM698291', 'G3LASM525803', 'Plata', 'En tramite', '968 656 837', '924 000 581', 48840.00, 10000.00, '2026-03-27', 36, '2029-02-27', 2129.10, 10.00, 212.91, 2342.01),
('2026-02-26', '76477981', 'Karla Brigitte Martinez Magallanes', 'Pablo Ernesto Magallanes Barraza', '21792483', 'Pasaje Martinez 101', 'DELUXE KING CLASICA', 'TVS', 'MD6M14LAXT4AA7164', 'AL4AT1B9027', 'Rojo', 'En tramite', '958 120 436', NULL, 17000.00, 3000.00, '2026-02-26', 24, '2028-02-26', 1016.58, 10.00, 101.66, 1118.24),
('2026-02-26', '43809425', 'Mauro Arias Sosa', 'Sandra Denisse Ormeño Martinez', '43805391', 'Constanza mendoza mz 1 Lt- D', 'DURO 150', 'SSENDA', 'LPPFCKLS9T2000021', 'SSD162FMJT2000021', 'Negro-Rojo', 'En tramite', '907 655 084', '919 478 869', 5700.00, 1710.00, '2026-02-26', 12, '2027-02-26', 450.69, 10.00, 45.07, 495.76),
('2026-02-23', '41605673', 'Rosa Ciprian Cuadros', NULL, NULL, 'URB. Los parques mz B Lote 24', 'COOLRAY EXCLUSIVE 1,5 CTV/GLP', 'GEELY', 'LB3SX2036TX802757', 'BHE15AFDS7GA0713688', 'Gris', 'En tramite', '972 456 806', NULL, 68043.00, 20412.00, '2026-02-23', 36, '2029-02-23', 2610.95, 10.00, 261.10, 2872.05),
('2026-02-19', '46126929', 'Abraham coaquira Basaldua', NULL, NULL, 'Av. Los libertadores 1011', 'RE AUTORIKSHA TORITO 4T R', 'BAJAJ', 'MD2A45AX3TWG00530', 'AZXWSG73419', 'Verde', 'En tramite', '926 047 589', NULL, 21444.00, 4000.00, '2026-02-19', 18, '2027-08-19', 1494.62, 10.00, 149.46, 1644.08),
('2026-02-12', '77391973', 'Jordano Ali Mac Guirre Ormeño', NULL, NULL, 'Urb. Virgen del Carmen', 'STARRAY', 'GEELY', 'LB3FX1S71TB007401', 'BHE15EFZS5B00018433', 'Azul', 'En tramite', '989 022 244', NULL, 105043.00, 31512.90, '2026-02-18', 48, '2030-02-18', 3622.11, 10.00, 362.21, 3984.32),
('2026-02-17', '71110618', 'Carmen Rocio Caceres Armacanqui', 'Willian Candelario Quispe Barrientos', '48532063', 'Pasaje Santa Fe 15', 'GRAND I10', 'HYUNDAI', 'M4LB25AATM681551', 'G3LASM481359', 'Blanco', 'En tramite', '917 077 909', '928 745 285', 47520.00, 10000.00, '2026-02-17', 48, '2030-02-17', 1999.25, 10.00, 199.93, 2199.18),
('2026-02-14', '21871258', 'Aburto Santiago Edwin Walditradis', 'Yoselin Felipa Aaravia Sulca', '21829418', 'AA.HH Miguel Grau Pueblo Nuevo', 'PICANTO LX ONE GLP', 'KIA', 'KNAB2511ATT355970', 'G3LASP021711', 'Negro', 'Y2J-576', '935 616 900', NULL, 43993.00, 10000.00, '2026-03-14', 3, '2026-05-14', 12310.15, 10.00, 1231.02, 13541.17),
('2026-02-14', '45954203', 'Juan Manuel Avalos Diaz', 'Yacquelin Jesus Felipa Cusipuma', '43563341', 'Rancisco Felix Psaje Almeyda', 'RE AUTORIKSHA TORITO 4T R', 'BAJAJ', 'MD2A45SWL11791', 'AZXWSL77090', 'Rojo', 'En tramite', '960 683 026', NULL, 11500.00, 3000.00, '2026-02-14', 12, '2027-02-14', 1298.97, 10.00, 129.90, 1428.87),
('2026-02-13', '44933545', 'Sihuas Saravia Enrique Javier', 'Sihuas Martinez Enrique Alezander', '60752646', 'Av. Centenario Psj Portuguez', 'TORITO 4T', 'BAJAJ', 'MD2A45AX9TWG00208', 'AZXWSG67452', 'Rojo', 'En tramite', '981 389 176', '936 210 563', 21444.00, 3000.00, '2026-02-13', 24, '2028-02-13', 1339.28, 10.00, 133.93, 1473.21),
('2026-02-12', '40712273', 'Cesar Alberto Alarcon Zavaleta', NULL, NULL, 'Urb. Los medanos Subtanjalla', 'AUTORIKSHA TORITO 2T LPG', 'BAJAJ', 'MD2A49AX8TWE00153', 'S4XWSE26502', 'Rojo', 'En tramite', '961 316 730', NULL, 14090.00, 46065.00, '2028-02-12', 24, '2028-02-12', 805.28, 10.00, 80.53, 885.81),
('2026-02-10', '47332564', 'Magda Patricia Tsayco De la cruz', 'Claudia Doris Tsayco De la cruz', '70750512', 'AV. Progreso Psaje Tasayco Balcon', 'WIX 330-8K', 'WANXIN', 'LW8HDNZ19SW001076', 'WX17MN2S024222', 'Azul', 'En tramite', '961 536 940', '903 281 403', 14600.00, 4380.00, '2026-02-10', 18, '2027-08-10', 875.66, 10.00, 87.57, 963.23),
('2026-02-09', '74708310', 'Antony Humberto Sebastian Cabrera', 'Janeth Selene Perea Felipa', '21858712', 'Leon De Vivero M 02', 'RE AUTORIKSHA TORITO 4T', 'BAJAJ', 'MD2A45AX1TWG00414', 'AZWSG71539', 'Verde', 'En tramite', '997 014 179', '913 749 053', 21444.00, 3000.00, '2026-09-02', 24, '2028-02-09', 1339.28, 10.00, 133.93, 1473.21),
('2026-02-09', '43090872', 'Gladys Betel Barrientos Espino', NULL, NULL, 'Calle iquitos 101 San clemente', 'GRAND I10', 'HYUNDAI', 'MALB251AATM676127', 'G3LASM464914', 'Rojo', 'En tramite', '968 830 275', NULL, 48840.00, 10000.00, '2026-02-09', 24, '2028-02-09', 2616.03, 10.00, 261.60, 2877.63),
('2026-02-07', '42349901', 'Miguel Angel Fernandez Herrera', 'Carmen Rosa Herrera Quispe', '21855316', 'Jr. Lima 449 Pueblo Nuevo', 'DMX DP IT- GLPT', 'TVS', 'MD6M1LLH8T4A4030', 'AL4AT41A8370', 'Azul', 'En tramite', '902 584 335', '951 056 233', 19400.00, 3000.00, '2026-02-07', 18, '2027-08-07', 1405.17, 10.00, 140.52, 1545.69),
('2026-02-06', '70156816', 'Levano De La Cruz Juan Carlos', 'Evelyn Lazaro Lizama', '70138125', 'UPIS 08 de ocutubre C-37', 'RE AUTORIKSHA TORITO 4T', 'TORITO BAJA', 'MD2A45AX7TWG00532', 'AZXWSG71587', 'Verde', 'En tramite', '934 574 787', '931 563 690', 21444.00, 3000.00, '2026-02-06', 18, '2027-08-09', 1580.30, 10.00, 158.03, 1738.33),
('2026-02-04', '77690811', 'Carbajal Garay Luis Jose', 'Jorge Carbajal Chacaltana', '21874786', 'C.P. San pedro L- 04', 'LF125-5L', 'LIFAN', '157FMI4R1025272', 'LF3PCJ5L1RB000381', 'Rojo', '4999-AY', '970 725 581', NULL, 5300.00, 1600.00, '2026-02-04', 18, '2027-08-04', 3417.02, 10.00, 341.70, 3758.72),
('2026-02-04', '2189250', 'Cesar Eduardo Pachas Magallanes', 'Nulbia Del Pilar Llanos Melo', '21864508', 'Prolongación rosRIO 795', 'GRAND I10', 'HYUNDAI', 'MALB251AATMG75828', 'G3LASM462979', 'Gris', 'En tramite', '940 873 801', '933 600 465', 48840.00, 10000.00, '2026-03-04', 24, '2028-02-04', 2616.03, 10.00, 261.60, 2877.63),
('2026-02-04', '43642628', 'Ines Reralta Carhuapoma', NULL, NULL, 'AA.HH Las colonias sudtanjalla', 'AUTORIKSHA TORITO 2T LPG', 'BAJAJ', 'MD2A9AX0TWD60098', '24XWSC25531', 'Rojo', 'En tramite', '903 273 124', NULL, 14090.00, 3000.00, '2026-02-04', 24, '2028-02-04', 805.28, 10.00, 80.53, 885.81),
('2026-02-03', '21541058', 'Vega Avalos Lelys Rosario', NULL, NULL, 'Urb. Las Palmeras MZNA I', 'GRAND I10', 'HIUNDAI', 'MALB251AATM678008', 'G3LASM469544', 'Rojo', 'En tramite', '946 302 366', NULL, 47520.00, 10000.00, '2026-02-03', 36, '2029-03-02', 2217.00, 10.00, 221.70, 2438.70),
('2026-02-03', '40354615', 'Luis Alberto Quispe Rojas', 'Yuli Ester Guerrero Hernandez', '40536116', 'Ubr. Santa Maria MZ F Lote 02 B - Pisco', 'COOLRAY EXCLUSIVE MT', 'GEELY', 'LB3SX2042TX800268', 'BHE15AFDS1GA0837055', 'Plata', 'Y2K-698', '924 940 530', NULL, 65083.00, 20000.00, '2026-02-03', 18, '2027-08-03', 3637.34, 10.00, 363.73, 4001.07),
('2026-02-02', '70910087', 'Nohemi Huaman Escobar', NULL, NULL, 'A.H Divino Niño Mz B LT 08 - Ica', 'RE AUTORIKSHA TORITO 2T LPG', 'BAJAJ', 'MD2A49AXOTWD60019', '24XWSC25505', 'Rojo', 'En tramite', '947 597 788', NULL, 14090.00, 6900.00, '2026-02-02', 12, '2027-02-02', 812.14, 10.00, 81.21, 893.35),
('2026-02-02', '47043652', 'Valenzuela Hernandes Pedro Martin', NULL, NULL, 'Fundo Santa Rita MZ B LT 12 - San Andres Pisco', 'DELUXE FULL 4T GLP', 'TVS', 'MD6M14LA3T4AA7197', 'AK4AT4XB9061', 'Azul', 'En tramite', '941 692 274', NULL, 19600.00, 4000.00, '2026-02-02', 18, '2027-08-02', 1336.62, 10.00, 133.66, 1470.28),
('2026-02-02', '76188623', 'Nunes Claux Sebastian Jesus', 'Jose Alfredo De La Cruz  Revilla', '21537972', 'Calle Callao 672/MZ m1 LT 16 - Ica', 'TERA TREND 160', 'VOLKSWAGEN', '9BWBL6DF3TT359420', 'CWS196679', 'Azul Malibu', 'En tramite', '996 300 410', NULL, 66204.00, 39000.00, '2026-02-02', 60, '2031-02-02', 1422.51, 10.00, 142.25, 1564.76);
