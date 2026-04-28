-- Nuevos registros (E1..E34) desde Excel.
-- Ejecutar en VPS:
--   mysql -u yhondb -p motorpark < /var/www/yondamotorpark/sp-db/registro_ventas_vehiculares_nuevos_E.sql
--
-- Notas:
-- - Fechas del Excel tipo 1/9/2026 se interpretan como dd/mm/yyyy (Perú): 2026-09-01 si fuese así,
--   pero en tu excel anterior venía 2/27/2026 (formato mm/dd/yyyy). Para mantener consistencia con lo anterior,
--   aquí se interpreta como mm/dd/yyyy => 2026-01-09. Ajusta si tu Excel era dd/mm.
-- - "N° DE CUOTA ACTUAL" se guarda en numero_cuota_pagada (misma columna que venías usando).
-- - "RESTA" (texto tipo "S/.10 GPS") se guarda en deudas_pendientes_detalle.

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Insertar nuevas filas (no borra las existentes).
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
('2026-01-09','70230899','Jhon Leonardo Lizarbe Leon','Maribel Leon Remon','42611474','Urbanización Quijano Mendivil Mz. H-Lote-13','Captiva Prime Mt 3F','CHEVROLET','LZWADAGA4TB013725','LJ018S71620046','Gris Tungsteno','En tramite',NULL,'954 646 063','914 146 280',61532.92,46300.00,NULL,NULL,'2026-01-09',12,'2027-01-09',5127.74,10.00,512.77,5640.51,512.77,5640.51,2),
('2026-01-10','21871286','Yesenia Giovanna Pariona Yache',NULL,NULL,'Jr Colon n-501','N400','CHEVROLET','LZWADAGA3TC802572','LARICS72310004','Plata Twinkling','En tramite',NULL,'917 793 934',NULL,61142.00,22100.00,NULL,NULL,'2026-01-10',60,'2031-01-10',1811.90,10.00,181.19,1993.09,181.19,1993.09,NULL),
('2026-01-10','48165679','Robert Olmos Gonzales','Wendy Geraldine Rojas Levano','76598844','Distrito de sudtanjalla- Ica departamento de Ica','FRONTIER XE 4X4 MT','NISSAN','3N6CD33B8SK804040','YD25791685P','Blanco','En tramite','Y2J-906','933 986 375',NULL,127800.00,80000.00,NULL,NULL,'2026-01-10',60,'2031-01-10',2218.35,10.00,221.84,2440.19,221.84,2440.19,1),
('2026-01-12','44310275','Jimy Palomino Perez','Karla Patricia Vargas Jurado','72887159','AA.HH 21 de noviembre MZ.G lote- 27','TERROTORY TREND AT','FROD','LJXCU2BB8THF47958','S9G097266','Azul pantera','En tramite',NULL,'917 491 170','900 617 498',95004.00,30000.00,NULL,NULL,'2026-01-12',36,'2029-01-12',3783.34,10.00,378.33,4161.67,378.33,4161.67,2),
('2026-02-10','41678324','Maria Esther Pachas Castilla','Cesar Agusto de la cruz Salhuana','43650438','Calle Jose Anton 179','TVS KING DURAMAX 225 DEPORTIVA GNV','TVS','MDGM1LCHOT4AA0697','AL4AT41A7700','Rojo','En tramite',NULL,'926 761 714','936 749 439',19299.00,4000.00,NULL,NULL,'2026-02-10',18,'2027-08-10',1310.83,10.00,131.08,1441.91,131.08,1441.91,3),
('2026-01-08','48320560','Joselyn Frisa Loyola Tupac','Jose Luis Anton Lobaton','47871606','Sanchez Cerro 1000','RE AUTORIKSHA TORITO 4T','TORITO BAJAJ','MD2A45AX6TWF00048','AZXWSF13003','Rojo','En tramite',NULL,'936 394 218',NULL,21444.00,4000.00,NULL,'S/.10 GPS','2026-01-08',24,'2028-01-08',1266.66,10.00,126.67,1393.33,126.67,1393.33,3),
('2026-01-08','45696669','Jose Alfredo Diaz Castilla','Jaqueleen Soledad Aguilar Huaman','47904607','AA.HH 15 de agosto 2da etapa Mz E Lt. 05','RE AUTORIKSHA TORITO 4T','TORITO BAJAJ','MD2A45AX0TWF00191','AZXWSF17460','Azul','En tramite',NULL,'975 728 352','998 868 291',21444.00,4000.00,NULL,NULL,'2026-01-08',24,'2028-01-08',1266.66,10.00,126.67,1393.33,126.67,1393.33,3),
('2027-07-13','71592481','Carmen Luisa Falcon Pachas','Lrotentino Zapata Guerra','41300819','Pasaje Guadalupe Mz G lote-13','RE AUTORIKSHA TORITO 4T','TORITO BAJAJ','MD2A45AX1TWF00328','AZXWSF18566','Azul','En tramite',NULL,'989 810 395','929 603 584',21444.00,3000.00,NULL,NULL,'2027-07-13',18,'2029-01-13',1580.30,10.00,158.03,1738.33,158.03,1738.33,2),
('2026-01-06','22089206','Seferina Valdivia Sueros',NULL,NULL,'AMP Villa Hermosa- Asoc. Los Angeles mz, F Lt.09','RIK','ATUL','MCG523LR3TADC0049','RKR2509699','Azul','En tramite',NULL,'995 294 937',NULL,17500.00,3000.00,NULL,NULL,'2026-01-06',12,'2027-01-06',1637.83,10.00,163.78,1801.61,163.78,1801.61,3),
('2026-01-05','42328536','Yimy Alberto Tasayco Munayco','Lisseth Mariela Olivia Mesias','45279825','Av. Union S/N- Chincha. Grocio Prado','RE AUTORIKSHA TORITO 4T','TORITO BAJAJ','MD2A45AX2TW000641','AZXWSD20931','Azul','En tramite',NULL,'961 405 347','915 216 617',21444.00,4000.00,NULL,NULL,'2026-01-05',24,'2028-01-05',1266.66,10.00,126.67,1393.33,126.67,1393.33,2),
('2026-01-05','42743730','Adolfo Javier Torres Nonones','Karen Soraya Canelo Camacho','44246459','C.P. Canyar MZ. H Lt.01','GRAND I10','HYUNDAI',NULL,'G3LASM334074','Gris','En tramite','CSS-279','926 852 044','960 970 969',48840.00,10700.00,NULL,NULL,'2026-01-05',48,'2030-01-05',1878.79,10.00,187.88,2066.67,187.88,2066.67,1),
('2026-01-05','47155561','Miguel Angel Canchari Guerra','Oswaldo Francisco Canchari Guerra','21844431','AA.HH. Los Alamos- los Laureles MZ. 28 Lt-03','RE AUTORIKSHA TORITO 4T','TORITO BAJAJ','MD2A35AX7SWL12439','AZXWSL87812','Rojo','En tramite','2878-AY','912 015 580','953 358 925',14000.00,4000.00,NULL,'S/.10 GPS','2026-01-05',12,'2027-01-05',1129.54,10.00,112.95,1242.49,112.95,1242.49,1),
('2026-01-03','47444796','Javier Pachas Flores','Angelica Saravia Marcelo','434737791','Calle Ligura 818 Interior 02','TVS KING LS+ DP IT','TVS','MD6M14LAXT4AA6595','AK4AT985567','Azul','En tramite','3138-GD','936 209 695','932 380 026',17300.00,3000.00,NULL,NULL,'2026-01-03',18,'2027-07-03',1225.24,10.00,122.52,1347.76,122.52,1347.76,3),
('2026-01-31','70683042','David Alexander Castilla Zegarra','Leslie Jhoane Galvez Carrasco','77688669','Upis San Alfredo MZ B Lote 12-  Distrito del Carmen','GX3 PRO MT/GLP','GEELY','LLV2C3A24T0201406','JLY4G15S7UB010151','Rojo','En tramite','Y2L-161','962954300',NULL,53243.00,16000.00,NULL,'S/.10 GPS','2026-01-31',36,'2029-01-31',2041.56,10.00,204.16,2245.72,204.16,2245.72,2),
('2026-01-30','73020835','Miakol Giordi Alvarez Pachas','Carmen Lus Pachas Castilla De Alvarez','21828723','C.P. HUIRACOCHA #100- Distrito del Carmen','NEW ACCENT COMFORT PLUS MT/GLP','HYUNDAI','MALGT41DATM196603','G4FLSQ544028','Gris Metalico','En tramite',NULL,'956 822 574','964 193 189',66563.00,20000.00,NULL,NULL,'2026-01-30',36,'2029-01-30',2552.45,10.00,255.25,2807.70,255.25,2807.70,2),
('2026-01-30','43145535','Julio Enrique Yataco Calderon','Alicia Janet Sanchez Meneses','44456788','Av. Simon bolivar 1282- Dist. Pueblo Nuevo','TORITO 4T/GLP','BAJAJ','MD2A45AX0TWF00031','AZXWSF10809','Rojo','En tramite',NULL,'907 443 675',NULL,21444.00,3000.00,NULL,NULL,'2026-01-30',18,'2027-07-30',1580.30,10.00,158.03,1738.33,158.03,1738.33,2),
('2026-01-27','42332517','Ronald Gonzales de la cruz','Mabel Rivas Guitierrez','43521966','El huarangal lote 1-2 Calle Ricardo Palma Caniche','RE AUTORIKSHA TORITO 2T LPGR','BAJAJ','MD2A49AX8TWE60031','24XWSE2606','Azul','En tramite',NULL,'948 683 639','903 227 081',14090.00,3000.00,NULL,NULL,'2026-01-27',24,'2028-01-27',805.28,10.00,80.53,885.81,80.53,885.81,2),
('2026-02-27','48999270','Morrys Hudguerys Chiquinquira Advincula Espinoza','Lady Janne Arones Quispe','76534295','Fundo Wanqui s/n espaldas de la municipalidad de tambo de mora','TVS KING LS','TVS','MD6M14LAGT4AA6559','AK4AT4DB5518','Azul','En tramite',NULL,'923 851 185','933 022 093',17000.00,3000.00,NULL,NULL,'2026-02-27',24,'2028-02-27',1016.58,10.00,101.66,1118.24,101.66,1118.24,2),
('2026-01-27','22308497','Vilma Rosario Espinoza Vega','Vilma Evelyn Camazca Espinoza','46765668','ah. Alto del Molino MZ. 61 Lt-21','GRAND I10 HB CONFOT MT 1.0 GLPT','HYUNDAI','MALB251AATM689570','G3LASM503848','Blanco','En tramite',NULL,'986 479 834','932 814 891',48840.00,10000.00,NULL,NULL,'2026-01-27',36,'2029-01-27',2129.10,10.00,212.91,2342.01,212.91,2342.01,NULL),
('2026-01-26','42937021','Mirian Yolanda Catilla Almeyda',NULL,NULL,'URB. Los Viledos Mz. H1 Lt 5',NULL,'BAJAJ','MD2A45AX6TWF00082','AZXWSF12984','Rojo','En tramite',NULL,'912 125 449',NULL,18444.00,3000.00,NULL,'S/.10 GPS Y S/.134.93 MORA','2026-01-26',24,'2028-01-26',1339.28,10.00,133.93,1473.21,133.93,1473.21,1),
('2026-01-26','43359676','Maria Estela Torres Mendoza',NULL,'43359676','Av. Nueva esperanza Mz. B Lt- 03','X7 PLUS LIMITED GLP','CHANCHAG','LS4ASE3EXTA997870','JL473ZQA*SETABA09054','Gris Universo','En tramite',NULL,'933 844 951','981 603 701',72483.00,21744.90,NULL,NULL,'2026-01-26',18,'2027-07-26',4093.60,10.00,409.36,4502.96,409.36,4502.96,2),
('2026-01-23','76368930','Diego Alejandro Manrique Saldaña',NULL,NULL,'Urbanización Simon Bolivar Mz I Lt 06','KINGL-DMX- DP-GLP','TVS','MD6M1LLHH4T4AA4042','AL4AT47A84412','Negro','En tramite',NULL,'960 410 533',NULL,19400.00,3000.00,NULL,NULL,'2026-01-23',18,'2027-07-23',1405.17,10.00,140.52,1545.69,140.52,1545.69,NULL),
('2026-02-22','25575025','Alberto Antonio Lopez Smith','Eva Fermina Perez Huachua','21490655','UPIS SAN ANTONIO MZ A A.Lt 11','TVS KING LS DP','TVS','MD6M141T4AA7022','AK4AT4GB7702','Azul','En tramite',NULL,'926 666 797','938 902 369',17000.00,3000.00,NULL,NULL,'2026-02-22',24,'2028-02-22',1016.58,10.00,101.66,1118.24,101.66,1118.24,2),
('2026-01-21','41093244','Uber Yoel Huarcaya Luna','Judith Sonal Sonaly Huarca Quispe','71130967','Pueblo Joven San Clemente','GRAND I10 HB COMFORT','HYUNDAI','MALB251AATM649125','G3LASM387899','Azul','En tramite','CWH-037','939 906 419',NULL,48840.00,10000.00,NULL,NULL,'2026-01-21',36,'2029-01-21',2129.10,10.00,212.91,2342.01,212.91,2342.01,2),
('2026-01-23','61263588','Dan Tananta Lozano','Cristina Del castillo Montes','62943158','Parcela 12 sector Villacurí en Salas Guadalupe','RIK 4T GLP FIBRA DE VIDRIO','ATUL','MCG523LR1TADC0051','RKR25D9711','Azul','En tramite',NULL,'917 739 175','926 407 052',17700.00,3000.00,NULL,NULL,'2026-01-23',24,'2028-01-23',1067.41,10.00,106.74,1174.15,106.74,1174.15,3),
('2026-01-17','74538534','Jordy Boniet Limache Bonifaz','Esteban Limache Flores','21539063','Calle Jose Galvez 118 y 182','RE AUTORIKSHA TORITO 2T','TORITO BAJAJ','MD2A4BAX2TWE60025','24XWSE26356','Azul','En tramite',NULL,'901 021 625','960 896 896',14090.00,3000.00,NULL,NULL,'2026-01-17',18,'2027-07-17',950.20,10.00,95.02,1045.22,95.02,1045.22,1),
('2026-01-17','80632679','Hebert Alejandro Frafab Espino','Marisela lisset Hernandez Perez','46051746','URB. Villa El Gauyabo 4ta etapa Mz 10 Lt-17','RE AUTORIKSHA TORITO 2T','TORITO BAJAJ','MD2A49AX8TWE00119','24XWSE26477','Azul','En tramite',NULL,'913 368 677','907 538 953',14090.00,3000.00,NULL,NULL,'2026-01-17',24,'2028-01-17',805.28,10.00,80.53,885.81,80.53,885.81,2),
('2026-01-16','44519201','Ramos Levano Fiorella Patricia','Quincho Levano Ronald Oswaldo','42501547','San Pedro Mz 01A Lt- 08','GX3 PRO MT/GLP','GEELY','LLV2C3A23T0200974','JLY4G15S1UB0101976','Azul','En tramite','Y2L-255','969 192 813','924 114 863',53243.00,16000.00,NULL,NULL,'2026-01-16',36,'2029-01-16',2041.56,10.00,204.16,2245.72,204.16,2245.72,3),
('2026-02-13','41738788','Luis Alberto Yataco Catilla','Jorge Luis Yataco Ramos','21832062','Av. Melchorita 1090','TORITO 4T','TORITO BAJAJ','MD2A45AX5TWF00140','AZWSF15123','Azul','En tramite',NULL,'912 490 206',NULL,21444.00,3000.00,NULL,NULL,'2026-02-13',24,'2028-02-13',1339.28,10.00,133.93,1473.21,133.93,1473.21,3),
('2026-01-13','71951994','Kennedy Frankensteng Carrero Delgado','Mirian Carolina Bohorquez Aguilar','70454255','San Jose Mz.D Lt-18','DURO CFR 250','SSENDA','LRPRCM2B1SA000342','SS166FMM225000376','Negro-Amarillo','En tramite',NULL,'956 691 363','955 473 033',7990.00,2398.00,NULL,'S/.10 GPS','2026-01-13',24,'2028-01-13',406.05,10.00,40.61,446.66,40.61,446.66,1),
('2026-01-13','72537927','Arturo Silva Espino',NULL,NULL,'URB. Juan xxiii Mz D1 Lt-09','GX3 PRO','GEELY','LLV2C3A27T0201710','JLY4G15S7UB0102123','Azul','En tramite','CVS-090','977 683 557',NULL,53983.00,16195.00,NULL,'S/.10 GPS','2026-01-13',36,'2029-01-13',2071.43,10.00,207.14,2278.57,207.14,2278.57,2),
('2026-01-13','70053789','Alcina Xcarpio Carbajal','Paula Rosa Carbajal De Diaz','21822274','Av. Progreso Pasaje los rosales 106','RE AUTORIKSHA TORITO 4T','TORITO BAJAJ','MD2A45AX4TWF00307','AZXWSF18536','Azul','En tramite',NULL,'942 533 593','924 199 575',21444.00,4000.00,NULL,'S/.10 GPS','2026-01-13',18,'2027-07-13',1494.62,10.00,149.46,1644.08,149.46,1644.08,1),
('2026-01-17','21869801','Jose Luis Luyo Huarote',NULL,NULL,'AA.HH El salvador Mz Q Lt-12','GX3 PRO','GEELY','LLV2C3A2XR0200616','JLY4G15RCUB0104541','Dorado','En tramite','Y2K-699','934 300 171',NULL,37783.00,16200.00,NULL,NULL,'2026-01-17',36,'2029-01-17',2071.16,10.00,207.12,2278.28,207.12,2278.28,3),
('2026-01-10','40892569','Norma Ruth valenzuela Llancari',NULL,NULL,'UPIS SAL LUIS Mz D Lt-08','GRAND I10 GLP','HYUNDAI','MALB251AATM648143','G3LASM388096','Verde','En tramite',NULL,'943 971 902',NULL,48840.00,10000.00,NULL,'S/.10 GPS','2026-01-10',60,'2031-01-10',1802.53,10.00,180.25,1982.78,180.25,1982.78,3)
;

