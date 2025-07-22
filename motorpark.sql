-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-06-2025 a las 16:20:08
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `motorpark`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_concesionarios_eliminar_todo` (IN `_idconcesionario` INT)   BEGIN
	DELETE FROM tiendas WHERE idconcesionario = _idconcesionario;
    DELETE FROM concesionarios WHERE idconcesionario = _idconcesionario;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_concesionarios_obtener_oc` (IN `_idconcesionario` INT, OUT `_registros` INT)   BEGIN
	SET _registros = 
		(
        SELECT COUNT(*) 
		FROM ordenescompra 
        WHERE idtienda IN 
			(SELECT idtienda FROM tiendas WHERE idconcesionario = _idconcesionario)
		);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_modelos_obtener_por_marca` (IN `_idmarca` INT)   BEGIN
	SELECT
		MD.idmodelo,
		TV.tipovehiculo,
		MD.modelo, MD.anio, MD.imagenreferencial
		FROM modelos MD
		INNER JOIN tipovehiculos TV ON TV.idtipovehiculo = MD.idtipovehiculo
		WHERE MD.idmarca = _idmarca
        ORDER BY TV.tipovehiculo, MD.modelo;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_oc_registrar` (IN `_idtienda` INT, IN `_idlogistica` INT, IN `_moneda` CHAR(3), IN `_serie` CHAR(4), IN `_numstock` VARCHAR(20), IN `_observaciones` VARCHAR(400))   BEGIN
    INSERT INTO ordenescompra (idtienda, idlogistica, moneda, serie, emision, numstock, observaciones, estado) 
		VALUES (
			_idtienda,
            _idlogistica, 
            _moneda,
            _serie,
            NOW(),
            NULLIF(_numstock, ''),
            NULLIF(_observaciones, ''),
            'emitido'
        );
	SELECT last_insert_id() AS 'last_id';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_tiendas_obtener` (IN `_idtienda` INT)   BEGIN
SELECT
	TD.idtienda,
	DP.iddepartamento, PR.idprovincia, DS.iddistrito,
	TD.direccion,
	TD.email,
	TD.telefono,
	TD.contacto
	FROM tiendas TD
	INNER JOIN distritos DS ON DS.iddistrito = TD.iddistrito
	INNER JOIN provincias PR ON PR.idprovincia = DS.idprovincia
	INNER JOIN departamentos DP ON DP.iddepartamento = PR.iddepartamento
	WHERE TD.idtienda = _idtienda;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_tiendas_por_concesionario` (IN `_idconcesionario` INT)   BEGIN
    SELECT
		TD.idtienda,
        CONCAT(DP.departamento, ', ', PR.provincia, ', ', DS.distrito) AS 'ubigeo',
        TD.direccion,
        TD.email,
        TD.telefono,
        TD.contacto
		FROM tiendas TD
        INNER JOIN distritos DS ON DS.iddistrito = TD.iddistrito
        INNER JOIN provincias PR ON PR.idprovincia = DS.idprovincia
        INNER JOIN departamentos DP ON DP.iddepartamento = PR.iddepartamento
        WHERE TD.idconcesionario = _idconcesionario;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `amortizacionesoc`
--

CREATE TABLE `amortizacionesoc` (
  `idamortizacion` int(11) NOT NULL,
  `idorden` int(11) NOT NULL,
  `idlogistica` int(11) NOT NULL,
  `identidadpago` int(11) NOT NULL,
  `fechapago` date NOT NULL,
  `numtransaccion` varchar(20) NOT NULL,
  `moneda` enum('USD','PEN') NOT NULL,
  `tipocambio` decimal(5,2) DEFAULT NULL,
  `amortizacion` decimal(9,2) NOT NULL,
  `saldo` decimal(9,2) NOT NULL,
  `comprobante` varchar(200) DEFAULT NULL,
  `observaciones` varchar(400) DEFAULT NULL,
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `modificado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `areas`
--

CREATE TABLE `areas` (
  `idarea` int(11) NOT NULL,
  `area` varchar(40) NOT NULL,
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `modificado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `areas`
--

INSERT INTO `areas` (`idarea`, `area`, `creado`, `modificado`) VALUES
(1, 'Sistemas', '2025-05-16 16:58:52', NULL),
(2, 'Recursos Humanos', '2025-05-16 16:58:52', NULL),
(3, 'Contabilidad', '2025-05-16 16:58:52', NULL),
(4, 'Marketing', '2025-05-16 16:58:52', NULL),
(5, 'Ventas', '2025-05-16 16:58:52', NULL),
(6, 'Caja', '2025-05-16 16:58:52', NULL),
(7, 'Cobranza', '2025-05-16 16:58:52', NULL),
(8, 'Legal', '2025-05-16 16:58:52', NULL),
(9, 'Logística', '2025-06-21 11:42:55', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargos`
--

CREATE TABLE `cargos` (
  `idcargo` int(11) NOT NULL,
  `idarea` int(11) NOT NULL,
  `cargo` varchar(40) NOT NULL,
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `modificado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cargos`
--

INSERT INTO `cargos` (`idcargo`, `idarea`, `cargo`, `creado`, `modificado`) VALUES
(1, 1, 'Jefe de sistemas', '2025-05-16 17:00:07', NULL),
(2, 1, 'Analista desarrollador', '2025-05-16 17:00:07', NULL),
(3, 1, 'Practicante', '2025-05-16 17:00:07', NULL),
(8, 9, 'Jefe de Logística', '2025-06-21 11:44:41', NULL),
(9, 9, 'Asistente de Logística', '2025-06-21 11:44:41', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `colaboradores`
--

CREATE TABLE `colaboradores` (
  `idcolaborador` int(11) NOT NULL,
  `idcontratolaboral` int(11) NOT NULL,
  `usernick` varchar(40) NOT NULL,
  `userpassword` varchar(70) NOT NULL,
  `avatar` varchar(150) DEFAULT NULL,
  `ultimoacceso` datetime DEFAULT NULL,
  `habilitado` enum('S','N') NOT NULL DEFAULT 'S',
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `modificado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `colaboradores`
--

INSERT INTO `colaboradores` (`idcolaborador`, `idcontratolaboral`, `usernick`, `userpassword`, `avatar`, `ultimoacceso`, `habilitado`, `creado`, `modificado`) VALUES
(1, 1, 'jhonfm', '$2y$10$GpQTuV8A8UPRul2E1E1OeOrhAb7842wa1cfB3bNieXncYTk2S1NTC', NULL, NULL, 'S', '2025-05-17 09:09:41', NULL),
(2, 2, 'leticiall', '$2y$10$GpQTuV8A8UPRul2E1E1OeOrhAb7842wa1cfB3bNieXncYTk2S1NTC', NULL, NULL, 'S', '2025-06-21 12:02:34', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `combustibles`
--

CREATE TABLE `combustibles` (
  `idcombustible` int(11) NOT NULL,
  `combustible` varchar(40) NOT NULL,
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `modificado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `idcompra` int(11) NOT NULL,
  `idorden` int(11) NOT NULL COMMENT 'De esta clave se obtendrán las datos de los vehículos',
  `idlogistica` int(11) NOT NULL COMMENT 'Colaborador que realiza el registro',
  `fechacompra` date NOT NULL,
  `fecharecepcion` date DEFAULT NULL,
  `tipodoc` enum('B','F') NOT NULL DEFAULT 'F' COMMENT 'Boleta o Factura',
  `serie` varchar(10) NOT NULL,
  `numdocumento` int(11) NOT NULL,
  `pathxml` varchar(200) DEFAULT NULL,
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `modificado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `concesionarios`
--

CREATE TABLE `concesionarios` (
  `idconcesionario` int(11) NOT NULL,
  `ruc` char(11) NOT NULL,
  `razonsocial` varchar(350) NOT NULL,
  `nombrecomercial` varchar(150) NOT NULL,
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `modificado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `concesionarios`
--

INSERT INTO `concesionarios` (`idconcesionario`, `ruc`, `razonsocial`, `nombrecomercial`, `creado`, `modificado`) VALUES
(2, '20602274277', 'DELATEL NETWORK TELECOMUNICACIONES PERU S.A.C.', 'YONDA', '2025-05-05 11:37:59', '2025-05-17 12:53:07'),
(6, '20602458491', 'NISSAN PERU S.A.C.', 'NISSAN PERU', '2025-05-15 16:52:45', '2025-05-17 12:52:48'),
(7, '20605661522', 'HYUNDAI ENGINEERING & CONSTRUCTION CO., LTD-SUCURSAL DEL PERU', 'HYUNDAI', '2025-05-17 11:05:39', '2025-05-17 12:50:23'),
(9, '20503758114', 'GAS NATURAL DE LIMA Y CALLAO S.A.', 'TOYOTA PERU', '2025-06-05 16:09:58', NULL),
(10, '20267575411', 'HAVAL SRLTDA', 'HAVAL', '2025-06-10 16:05:58', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contratoslaborales`
--

CREATE TABLE `contratoslaborales` (
  `idcontratolaboral` int(11) NOT NULL,
  `idpersona` int(11) NOT NULL,
  `idcargo` int(11) NOT NULL,
  `fechainicio` date NOT NULL,
  `fechafin` date DEFAULT NULL,
  `tipocontrato` enum('P','R') NOT NULL COMMENT 'Planilla - Recibos',
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `modificado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contratoslaborales`
--

INSERT INTO `contratoslaborales` (`idcontratolaboral`, `idpersona`, `idcargo`, `fechainicio`, `fechafin`, `tipocontrato`, `creado`, `modificado`) VALUES
(1, 1, 1, '2024-06-01', NULL, 'R', '2025-05-16 17:01:47', NULL),
(2, 2, 8, '2024-01-01', NULL, 'P', '2025-06-21 11:58:57', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos`
--

CREATE TABLE `departamentos` (
  `iddepartamento` int(11) NOT NULL,
  `departamento` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `departamentos`
--

INSERT INTO `departamentos` (`iddepartamento`, `departamento`) VALUES
(1, 'Amazonas'),
(2, 'Ancash'),
(3, 'Apurimac'),
(4, 'Arequipa'),
(5, 'Ayacucho'),
(6, 'Cajamarca'),
(7, 'Callao'),
(8, 'Cusco'),
(9, 'Huancavelica'),
(10, 'Huanuco'),
(11, 'Ica'),
(12, 'Junin'),
(13, 'La Libertad'),
(14, 'Lambayeque'),
(15, 'Lima'),
(16, 'Loreto'),
(17, 'Madre de Dios'),
(18, 'Moquegua'),
(19, 'Pasco'),
(20, 'Piura'),
(21, 'Puno'),
(22, 'San Martin'),
(23, 'Tacna'),
(24, 'Tumbes'),
(25, 'Ucayali');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detordencompra`
--

CREATE TABLE `detordencompra` (
  `iddetordencompra` int(11) NOT NULL,
  `idordencompra` int(11) NOT NULL,
  `idvehiculo` int(11) NOT NULL,
  `preciocompra` decimal(9,2) NOT NULL,
  `escorrecto` enum('S','N') DEFAULT NULL COMMENT 'Define si el vehículo llego de acuerdo a los datos de la factura'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `distritos`
--

CREATE TABLE `distritos` (
  `iddistrito` int(11) NOT NULL,
  `idprovincia` int(11) NOT NULL,
  `distrito` varchar(150) NOT NULL,
  `ubigeoinei` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `distritos`
--

INSERT INTO `distritos` (`iddistrito`, `idprovincia`, `distrito`, `ubigeoinei`) VALUES
(1, 1, 'Chachapoyas', '010101'),
(2, 1, 'Asuncion', '010102'),
(3, 1, 'Balsas', '010103'),
(4, 1, 'Cheto', '010104'),
(5, 1, 'Chiliquin', '010105'),
(6, 1, 'Chuquibamba', '010106'),
(7, 1, 'Granada', '010107'),
(8, 1, 'Huancas', '010108'),
(9, 1, 'La Jalca', '010109'),
(10, 1, 'Leimebamba', '010110'),
(11, 1, 'Levanto', '010111'),
(12, 1, 'Magdalena', '010112'),
(13, 1, 'Mariscal Castilla', '010113'),
(14, 1, 'Molinopampa', '010114'),
(15, 1, 'Montevideo', '010115'),
(16, 1, 'Olleros', '010116'),
(17, 1, 'Quinjalca', '010117'),
(18, 1, 'San Francisco de Daguas', '010118'),
(19, 1, 'San Isidro de Maino', '010119'),
(20, 1, 'Soloco', '010120'),
(21, 1, 'Sonche', '010121'),
(22, 2, 'Bagua', '010201'),
(23, 2, 'Aramango', '010202'),
(24, 2, 'Copallin', '010203'),
(25, 2, 'El Parco', '010204'),
(26, 2, 'Imaza', '010205'),
(27, 2, 'La Peca', '010206'),
(28, 3, 'Jumbilla', '010301'),
(29, 3, 'Chisquilla', '010302'),
(30, 3, 'Churuja', '010303'),
(31, 3, 'Corosha', '010304'),
(32, 3, 'Cuispes', '010305'),
(33, 3, 'Florida', '010306'),
(34, 3, 'Jazan', '010307'),
(35, 3, 'Recta', '010308'),
(36, 3, 'San Carlos', '010309'),
(37, 3, 'Shipasbamba', '010310'),
(38, 3, 'Valera', '010311'),
(39, 3, 'Yambrasbamba', '010312'),
(40, 4, 'Nieva', '010401'),
(41, 4, 'El Cenepa', '010402'),
(42, 4, 'Rio Santiago', '010403'),
(43, 5, 'Lamud', '010501'),
(44, 5, 'Camporredondo', '010502'),
(45, 5, 'Cocabamba', '010503'),
(46, 5, 'Colcamar', '010504'),
(47, 5, 'Conila', '010505'),
(48, 5, 'Inguilpata', '010506'),
(49, 5, 'Longuita', '010507'),
(50, 5, 'Lonya Chico', '010508'),
(51, 5, 'Luya', '010509'),
(52, 5, 'Luya Viejo', '010510'),
(53, 5, 'Maria', '010511'),
(54, 5, 'Ocalli', '010512'),
(55, 5, 'Ocumal', '010513'),
(56, 5, 'Pisuquia', '010514'),
(57, 5, 'Providencia', '010515'),
(58, 5, 'San Cristobal', '010516'),
(59, 5, 'San Francisco del Yeso', '010517'),
(60, 5, 'San Jeronimo', '010518'),
(61, 5, 'San Juan de Lopecancha', '010519'),
(62, 5, 'Santa Catalina', '010520'),
(63, 5, 'Santo Tomas', '010521'),
(64, 5, 'Tingo', '010522'),
(65, 5, 'Trita', '010523'),
(66, 6, 'San Nicolas', '010601'),
(67, 6, 'Chirimoto', '010602'),
(68, 6, 'Cochamal', '010603'),
(69, 6, 'Huambo', '010604'),
(70, 6, 'Limabamba', '010605'),
(71, 6, 'Longar', '010606'),
(72, 6, 'Mariscal Benavides', '010607'),
(73, 6, 'Milpuc', '010608'),
(74, 6, 'Omia', '010609'),
(75, 6, 'Santa Rosa', '010610'),
(76, 6, 'Totora', '010611'),
(77, 6, 'Vista Alegre', '010612'),
(78, 7, 'Bagua Grande', '010701'),
(79, 7, 'Cajaruro', '010702'),
(80, 7, 'Cumba', '010703'),
(81, 7, 'El Milagro', '010704'),
(82, 7, 'Jamalca', '010705'),
(83, 7, 'Lonya Grande', '010706'),
(84, 7, 'Yamon', '010707'),
(85, 8, 'Huaraz', '020101'),
(86, 8, 'Cochabamba', '020102'),
(87, 8, 'Colcabamba', '020103'),
(88, 8, 'Huanchay', '020104'),
(89, 8, 'Independencia', '020105'),
(90, 8, 'Jangas', '020106'),
(91, 8, 'La Libertad', '020107'),
(92, 8, 'Olleros', '020108'),
(93, 8, 'Pampas', '020109'),
(94, 8, 'Pariacoto', '020110'),
(95, 8, 'Pira', '020111'),
(96, 8, 'Tarica', '020112'),
(97, 9, 'Aija', '020201'),
(98, 9, 'Coris', '020202'),
(99, 9, 'Huacllan', '020203'),
(100, 9, 'La Merced', '020204'),
(101, 9, 'Succha', '020205'),
(102, 10, 'Llamellin', '020301'),
(103, 10, 'Aczo', '020302'),
(104, 10, 'Chaccho', '020303'),
(105, 10, 'Chingas', '020304'),
(106, 10, 'Mirgas', '020305'),
(107, 10, 'San Juan de Rontoy', '020306'),
(108, 11, 'Chacas', '020401'),
(109, 11, 'Acochaca', '020402'),
(110, 12, 'Chiquian', '020501'),
(111, 12, 'Abelardo Pardo Lezameta', '020502'),
(112, 12, 'Antonio Raymondi', '020503'),
(113, 12, 'Aquia', '020504'),
(114, 12, 'Cajacay', '020505'),
(115, 12, 'Canis', '020506'),
(116, 12, 'Colquioc', '020507'),
(117, 12, 'Huallanca', '020508'),
(118, 12, 'Huasta', '020509'),
(119, 12, 'Huayllacayan', '020510'),
(120, 12, 'La Primavera', '020511'),
(121, 12, 'Mangas', '020512'),
(122, 12, 'Pacllon', '020513'),
(123, 12, 'San Miguel de Corpanqui', '020514'),
(124, 12, 'Ticllos', '020515'),
(125, 13, 'Carhuaz', '020601'),
(126, 13, 'Acopampa', '020602'),
(127, 13, 'Amashca', '020603'),
(128, 13, 'Anta', '020604'),
(129, 13, 'Ataquero', '020605'),
(130, 13, 'Marcara', '020606'),
(131, 13, 'Pariahuanca', '020607'),
(132, 13, 'San Miguel de Aco', '020608'),
(133, 13, 'Shilla', '020609'),
(134, 13, 'Tinco', '020610'),
(135, 13, 'Yungar', '020611'),
(136, 14, 'San Luis', '020701'),
(137, 14, 'San Nicolas', '020702'),
(138, 14, 'Yauya', '020703'),
(139, 15, 'Casma', '020801'),
(140, 15, 'Buena Vista Alta', '020802'),
(141, 15, 'Comandante Noel', '020803'),
(142, 15, 'Yautan', '020804'),
(143, 16, 'Corongo', '020901'),
(144, 16, 'Aco', '020902'),
(145, 16, 'Bambas', '020903'),
(146, 16, 'Cusca', '020904'),
(147, 16, 'La Pampa', '020905'),
(148, 16, 'Yanac', '020906'),
(149, 16, 'Yupan', '020907'),
(150, 17, 'Huari', '021001'),
(151, 17, 'Anra', '021002'),
(152, 17, 'Cajay', '021003'),
(153, 17, 'Chavin de Huantar', '021004'),
(154, 17, 'Huacachi', '021005'),
(155, 17, 'Huacchis', '021006'),
(156, 17, 'Huachis', '021007'),
(157, 17, 'Huantar', '021008'),
(158, 17, 'Masin', '021009'),
(159, 17, 'Paucas', '021010'),
(160, 17, 'Ponto', '021011'),
(161, 17, 'Rahuapampa', '021012'),
(162, 17, 'Rapayan', '021013'),
(163, 17, 'San Marcos', '021014'),
(164, 17, 'San Pedro de Chana', '021015'),
(165, 17, 'Uco', '021016'),
(166, 18, 'Huarmey', '021101'),
(167, 18, 'Cochapeti', '021102'),
(168, 18, 'Culebras', '021103'),
(169, 18, 'Huayan', '021104'),
(170, 18, 'Malvas', '021105'),
(171, 19, 'Caraz', '021201'),
(172, 19, 'Huallanca', '021202'),
(173, 19, 'Huata', '021203'),
(174, 19, 'Huaylas', '021204'),
(175, 19, 'Mato', '021205'),
(176, 19, 'Pamparomas', '021206'),
(177, 19, 'Pueblo Libre', '021207'),
(178, 19, 'Santa Cruz', '021208'),
(179, 19, 'Santo Toribio', '021209'),
(180, 19, 'Yuracmarca', '021210'),
(181, 20, 'Piscobamba', '021301'),
(182, 20, 'Casca', '021302'),
(183, 20, 'Eleazar Guzman Barron', '021303'),
(184, 20, 'Fidel Olivas Escudero', '021304'),
(185, 20, 'Llama', '021305'),
(186, 20, 'Llumpa', '021306'),
(187, 20, 'Lucma', '021307'),
(188, 20, 'Musga', '021308'),
(189, 21, 'Ocros', '021401'),
(190, 21, 'Acas', '021402'),
(191, 21, 'Cajamarquilla', '021403'),
(192, 21, 'Carhuapampa', '021404'),
(193, 21, 'Cochas', '021405'),
(194, 21, 'Congas', '021406'),
(195, 21, 'Llipa', '021407'),
(196, 21, 'San Cristobal de Rajan', '021408'),
(197, 21, 'San Pedro', '021409'),
(198, 21, 'Santiago de Chilcas', '021410'),
(199, 22, 'Cabana', '021501'),
(200, 22, 'Bolognesi', '021502'),
(201, 22, 'Conchucos', '021503'),
(202, 22, 'Huacaschuque', '021504'),
(203, 22, 'Huandoval', '021505'),
(204, 22, 'Lacabamba', '021506'),
(205, 22, 'Llapo', '021507'),
(206, 22, 'Pallasca', '021508'),
(207, 22, 'Pampas', '021509'),
(208, 22, 'Santa Rosa', '021510'),
(209, 22, 'Tauca', '021511'),
(210, 23, 'Pomabamba', '021601'),
(211, 23, 'Huayllan', '021602'),
(212, 23, 'Parobamba', '021603'),
(213, 23, 'Quinuabamba', '021604'),
(214, 24, 'Recuay', '021701'),
(215, 24, 'Catac', '021702'),
(216, 24, 'Cotaparaco', '021703'),
(217, 24, 'Huayllapampa', '021704'),
(218, 24, 'Llacllin', '021705'),
(219, 24, 'Marca', '021706'),
(220, 24, 'Pampas Chico', '021707'),
(221, 24, 'Pararin', '021708'),
(222, 24, 'Tapacocha', '021709'),
(223, 24, 'Ticapampa', '021710'),
(224, 25, 'Chimbote', '021801'),
(225, 25, 'Caceres del Peru', '021802'),
(226, 25, 'Coishco', '021803'),
(227, 25, 'Macate', '021804'),
(228, 25, 'Moro', '021805'),
(229, 25, 'Nepeña', '021806'),
(230, 25, 'Samanco', '021807'),
(231, 25, 'Santa', '021808'),
(232, 25, 'Nuevo Chimbote', '021809'),
(233, 26, 'Sihuas', '021901'),
(234, 26, 'Acobamba', '021902'),
(235, 26, 'Alfonso Ugarte', '021903'),
(236, 26, 'Cashapampa', '021904'),
(237, 26, 'Chingalpo', '021905'),
(238, 26, 'Huayllabamba', '021906'),
(239, 26, 'Quiches', '021907'),
(240, 26, 'Ragash', '021908'),
(241, 26, 'San Juan', '021909'),
(242, 26, 'Sicsibamba', '021910'),
(243, 27, 'Yungay', '022001'),
(244, 27, 'Cascapara', '022002'),
(245, 27, 'Mancos', '022003'),
(246, 27, 'Matacoto', '022004'),
(247, 27, 'Quillo', '022005'),
(248, 27, 'Ranrahirca', '022006'),
(249, 27, 'Shupluy', '022007'),
(250, 27, 'Yanama', '022008'),
(251, 28, 'Abancay', '030101'),
(252, 28, 'Chacoche', '030102'),
(253, 28, 'Circa', '030103'),
(254, 28, 'Curahuasi', '030104'),
(255, 28, 'Huanipaca', '030105'),
(256, 28, 'Lambrama', '030106'),
(257, 28, 'Pichirhua', '030107'),
(258, 28, 'San Pedro de Cachora', '030108'),
(259, 28, 'Tamburco', '030109'),
(260, 29, 'Andahuaylas', '030201'),
(261, 29, 'Andarapa', '030202'),
(262, 29, 'Chiara', '030203'),
(263, 29, 'Huancarama', '030204'),
(264, 29, 'Huancaray', '030205'),
(265, 29, 'Huayana', '030206'),
(266, 29, 'Kishuara', '030207'),
(267, 29, 'Pacobamba', '030208'),
(268, 29, 'Pacucha', '030209'),
(269, 29, 'Pampachiri', '030210'),
(270, 29, 'Pomacocha', '030211'),
(271, 29, 'San Antonio de Cachi', '030212'),
(272, 29, 'San Jeronimo', '030213'),
(273, 29, 'San Miguel de Chaccrampa', '030214'),
(274, 29, 'Santa Maria de Chicmo', '030215'),
(275, 29, 'Talavera', '030216'),
(276, 29, 'Tumay Huaraca', '030217'),
(277, 29, 'Turpo', '030218'),
(278, 29, 'Kaquiabamba', '030219'),
(279, 29, 'José María Arguedas', '030220'),
(280, 30, 'Antabamba', '030301'),
(281, 30, 'El Oro', '030302'),
(282, 30, 'Huaquirca', '030303'),
(283, 30, 'Juan Espinoza Medrano', '030304'),
(284, 30, 'Oropesa', '030305'),
(285, 30, 'Pachaconas', '030306'),
(286, 30, 'Sabaino', '030307'),
(287, 31, 'Chalhuanca', '030401'),
(288, 31, 'Capaya', '030402'),
(289, 31, 'Caraybamba', '030403'),
(290, 31, 'Chapimarca', '030404'),
(291, 31, 'Colcabamba', '030405'),
(292, 31, 'Cotaruse', '030406'),
(293, 31, 'Huayllo', '030407'),
(294, 31, 'Justo Apu Sahuaraura', '030408'),
(295, 31, 'Lucre', '030409'),
(296, 31, 'Pocohuanca', '030410'),
(297, 31, 'San Juan de Chacña', '030411'),
(298, 31, 'Sañayca', '030412'),
(299, 31, 'Soraya', '030413'),
(300, 31, 'Tapairihua', '030414'),
(301, 31, 'Tintay', '030415'),
(302, 31, 'Toraya', '030416'),
(303, 31, 'Yanaca', '030417'),
(304, 32, 'Tambobamba', '030501'),
(305, 32, 'Cotabambas', '030502'),
(306, 32, 'Coyllurqui', '030503'),
(307, 32, 'Haquira', '030504'),
(308, 32, 'Mara', '030505'),
(309, 32, 'Challhuahuacho', '030506'),
(310, 33, 'Chincheros', '030601'),
(311, 33, 'Anco_Huallo', '030602'),
(312, 33, 'Cocharcas', '030603'),
(313, 33, 'Huaccana', '030604'),
(314, 33, 'Ocobamba', '030605'),
(315, 33, 'Ongoy', '030606'),
(316, 33, 'Uranmarca', '030607'),
(317, 33, 'Ranracancha', '030608'),
(318, 33, 'Rocchacc', '030609'),
(319, 33, 'El Porvenir', '030610'),
(320, 33, 'Los Chankas', '030611'),
(321, 34, 'Chuquibambilla', '030701'),
(322, 34, 'Curpahuasi', '030702'),
(323, 34, 'Gamarra', '030703'),
(324, 34, 'Huayllati', '030704'),
(325, 34, 'Mamara', '030705'),
(326, 34, 'Micaela Bastidas', '030706'),
(327, 34, 'Pataypampa', '030707'),
(328, 34, 'Progreso', '030708'),
(329, 34, 'San Antonio', '030709'),
(330, 34, 'Santa Rosa', '030710'),
(331, 34, 'Turpay', '030711'),
(332, 34, 'Vilcabamba', '030712'),
(333, 34, 'Virundo', '030713'),
(334, 34, 'Curasco', '030714'),
(335, 35, 'Arequipa', '040101'),
(336, 35, 'Alto Selva Alegre', '040102'),
(337, 35, 'Cayma', '040103'),
(338, 35, 'Cerro Colorado', '040104'),
(339, 35, 'Characato', '040105'),
(340, 35, 'Chiguata', '040106'),
(341, 35, 'Jacobo Hunter', '040107'),
(342, 35, 'La Joya', '040108'),
(343, 35, 'Mariano Melgar', '040109'),
(344, 35, 'Miraflores', '040110'),
(345, 35, 'Mollebaya', '040111'),
(346, 35, 'Paucarpata', '040112'),
(347, 35, 'Pocsi', '040113'),
(348, 35, 'Polobaya', '040114'),
(349, 35, 'Quequeña', '040115'),
(350, 35, 'Sabandia', '040116'),
(351, 35, 'Sachaca', '040117'),
(352, 35, 'San Juan de Siguas', '040118'),
(353, 35, 'San Juan de Tarucani', '040119'),
(354, 35, 'Santa Isabel de Siguas', '040120'),
(355, 35, 'Santa Rita de Siguas', '040121'),
(356, 35, 'Socabaya', '040122'),
(357, 35, 'Tiabaya', '040123'),
(358, 35, 'Uchumayo', '040124'),
(359, 35, 'Vitor', '040125'),
(360, 35, 'Yanahuara', '040126'),
(361, 35, 'Yarabamba', '040127'),
(362, 35, 'Yura', '040128'),
(363, 35, 'Jose Luis Bustamante y Rivero', '040129'),
(364, 36, 'Camana', '040201'),
(365, 36, 'Jose Maria Quimper', '040202'),
(366, 36, 'Mariano Nicolas Valcarcel', '040203'),
(367, 36, 'Mariscal Caceres', '040204'),
(368, 36, 'Nicolas de Pierola', '040205'),
(369, 36, 'Ocoña', '040206'),
(370, 36, 'Quilca', '040207'),
(371, 36, 'Samuel Pastor', '040208'),
(372, 37, 'Caraveli', '040301'),
(373, 37, 'Acari', '040302'),
(374, 37, 'Atico', '040303'),
(375, 37, 'Atiquipa', '040304'),
(376, 37, 'Bella Union', '040305'),
(377, 37, 'Cahuacho', '040306'),
(378, 37, 'Chala', '040307'),
(379, 37, 'Chaparra', '040308'),
(380, 37, 'Huanuhuanu', '040309'),
(381, 37, 'Jaqui', '040310'),
(382, 37, 'Lomas', '040311'),
(383, 37, 'Quicacha', '040312'),
(384, 37, 'Yauca', '040313'),
(385, 38, 'Aplao', '040401'),
(386, 38, 'Andagua', '040402'),
(387, 38, 'Ayo', '040403'),
(388, 38, 'Chachas', '040404'),
(389, 38, 'Chilcaymarca', '040405'),
(390, 38, 'Choco', '040406'),
(391, 38, 'Huancarqui', '040407'),
(392, 38, 'Machaguay', '040408'),
(393, 38, 'Orcopampa', '040409'),
(394, 38, 'Pampacolca', '040410'),
(395, 38, 'Tipan', '040411'),
(396, 38, 'Uñon', '040412'),
(397, 38, 'Uraca', '040413'),
(398, 38, 'Viraco', '040414'),
(399, 39, 'Chivay', '040501'),
(400, 39, 'Achoma', '040502'),
(401, 39, 'Cabanaconde', '040503'),
(402, 39, 'Callalli', '040504'),
(403, 39, 'Caylloma', '040505'),
(404, 39, 'Coporaque', '040506'),
(405, 39, 'Huambo', '040507'),
(406, 39, 'Huanca', '040508'),
(407, 39, 'Ichupampa', '040509'),
(408, 39, 'Lari', '040510'),
(409, 39, 'Lluta', '040511'),
(410, 39, 'Maca', '040512'),
(411, 39, 'Madrigal', '040513'),
(412, 39, 'San Antonio de Chuca', '040514'),
(413, 39, 'Sibayo', '040515'),
(414, 39, 'Tapay', '040516'),
(415, 39, 'Tisco', '040517'),
(416, 39, 'Tuti', '040518'),
(417, 39, 'Yanque', '040519'),
(418, 39, 'Majes', '040520'),
(419, 40, 'Chuquibamba', '040601'),
(420, 40, 'Andaray', '040602'),
(421, 40, 'Cayarani', '040603'),
(422, 40, 'Chichas', '040604'),
(423, 40, 'Iray', '040605'),
(424, 40, 'Rio Grande', '040606'),
(425, 40, 'Salamanca', '040607'),
(426, 40, 'Yanaquihua', '040608'),
(427, 41, 'Mollendo', '040701'),
(428, 41, 'Cocachacra', '040702'),
(429, 41, 'Dean Valdivia', '040703'),
(430, 41, 'Islay', '040704'),
(431, 41, 'Mejia', '040705'),
(432, 41, 'Punta de Bombon', '040706'),
(433, 42, 'Cotahuasi', '040801'),
(434, 42, 'Alca', '040802'),
(435, 42, 'Charcana', '040803'),
(436, 42, 'Huaynacotas', '040804'),
(437, 42, 'Pampamarca', '040805'),
(438, 42, 'Puyca', '040806'),
(439, 42, 'Quechualla', '040807'),
(440, 42, 'Sayla', '040808'),
(441, 42, 'Tauria', '040809'),
(442, 42, 'Tomepampa', '040810'),
(443, 42, 'Toro', '040811'),
(444, 43, 'Ayacucho', '050101'),
(445, 43, 'Acocro', '050102'),
(446, 43, 'Acos Vinchos', '050103'),
(447, 43, 'Carmen Alto', '050104'),
(448, 43, 'Chiara', '050105'),
(449, 43, 'Ocros', '050106'),
(450, 43, 'Pacaycasa', '050107'),
(451, 43, 'Quinua', '050108'),
(452, 43, 'San Jose de Ticllas', '050109'),
(453, 43, 'San Juan Bautista', '050110'),
(454, 43, 'Santiago de Pischa', '050111'),
(455, 43, 'Socos', '050112'),
(456, 43, 'Tambillo', '050113'),
(457, 43, 'Vinchos', '050114'),
(458, 43, 'Jesus Nazareno', '050115'),
(459, 43, 'Andrés Avelino Cáceres Dorregaray', '050116'),
(460, 44, 'Cangallo', '050201'),
(461, 44, 'Chuschi', '050202'),
(462, 44, 'Los Morochucos', '050203'),
(463, 44, 'Maria Parado de Bellido', '050204'),
(464, 44, 'Paras', '050205'),
(465, 44, 'Totos', '050206'),
(466, 45, 'Sancos', '050301'),
(467, 45, 'Carapo', '050302'),
(468, 45, 'Sacsamarca', '050303'),
(469, 45, 'Santiago de Lucanamarca', '050304'),
(470, 46, 'Huanta', '050401'),
(471, 46, 'Ayahuanco', '050402'),
(472, 46, 'Huamanguilla', '050403'),
(473, 46, 'Iguain', '050404'),
(474, 46, 'Luricocha', '050405'),
(475, 46, 'Santillana', '050406'),
(476, 46, 'Sivia', '050407'),
(477, 46, 'Llochegua', '050408'),
(478, 46, 'Canayre', '050409'),
(479, 46, 'Uchuraccay', '050410'),
(480, 46, 'Pucacolpa', '050411'),
(481, 46, 'Chaca', '050412'),
(482, 47, 'San Miguel', '050501'),
(483, 47, 'Anco', '050502'),
(484, 47, 'Ayna', '050503'),
(485, 47, 'Chilcas', '050504'),
(486, 47, 'Chungui', '050505'),
(487, 47, 'Luis Carranza', '050506'),
(488, 47, 'Santa Rosa', '050507'),
(489, 47, 'Tambo', '050508'),
(490, 47, 'Samugari', '050509'),
(491, 47, 'Anchihuay', '050510'),
(492, 47, 'Oronccoy', '050511'),
(493, 48, 'Puquio', '050601'),
(494, 48, 'Aucara', '050602'),
(495, 48, 'Cabana', '050603'),
(496, 48, 'Carmen Salcedo', '050604'),
(497, 48, 'Chaviña', '050605'),
(498, 48, 'Chipao', '050606'),
(499, 48, 'Huac-Huas', '050607'),
(500, 48, 'Laramate', '050608'),
(501, 48, 'Leoncio Prado', '050609'),
(502, 48, 'Llauta', '050610'),
(503, 48, 'Lucanas', '050611'),
(504, 48, 'Ocaña', '050612'),
(505, 48, 'Otoca', '050613'),
(506, 48, 'Saisa', '050614'),
(507, 48, 'San Cristobal', '050615'),
(508, 48, 'San Juan', '050616'),
(509, 48, 'San Pedro', '050617'),
(510, 48, 'San Pedro de Palco', '050618'),
(511, 48, 'Sancos', '050619'),
(512, 48, 'Santa Ana de Huaycahuacho', '050620'),
(513, 48, 'Santa Lucia', '050621'),
(514, 49, 'Coracora', '050701'),
(515, 49, 'Chumpi', '050702'),
(516, 49, 'Coronel Castañeda', '050703'),
(517, 49, 'Pacapausa', '050704'),
(518, 49, 'Pullo', '050705'),
(519, 49, 'Puyusca', '050706'),
(520, 49, 'San Francisco de Ravacayco', '050707'),
(521, 49, 'Upahuacho', '050708'),
(522, 50, 'Pausa', '050801'),
(523, 50, 'Colta', '050802'),
(524, 50, 'Corculla', '050803'),
(525, 50, 'Lampa', '050804'),
(526, 50, 'Marcabamba', '050805'),
(527, 50, 'Oyolo', '050806'),
(528, 50, 'Pararca', '050807'),
(529, 50, 'San Javier de Alpabamba', '050808'),
(530, 50, 'San Jose de Ushua', '050809'),
(531, 50, 'Sara Sara', '050810'),
(532, 51, 'Querobamba', '050901'),
(533, 51, 'Belen', '050902'),
(534, 51, 'Chalcos', '050903'),
(535, 51, 'Chilcayoc', '050904'),
(536, 51, 'Huacaña', '050905'),
(537, 51, 'Morcolla', '050906'),
(538, 51, 'Paico', '050907'),
(539, 51, 'San Pedro de Larcay', '050908'),
(540, 51, 'San Salvador de Quije', '050909'),
(541, 51, 'Santiago de Paucaray', '050910'),
(542, 51, 'Soras', '050911'),
(543, 52, 'Huancapi', '051001'),
(544, 52, 'Alcamenca', '051002'),
(545, 52, 'Apongo', '051003'),
(546, 52, 'Asquipata', '051004'),
(547, 52, 'Canaria', '051005'),
(548, 52, 'Cayara', '051006'),
(549, 52, 'Colca', '051007'),
(550, 52, 'Huamanquiquia', '051008'),
(551, 52, 'Huancaraylla', '051009'),
(552, 52, 'Huaya', '051010'),
(553, 52, 'Sarhua', '051011'),
(554, 52, 'Vilcanchos', '051012'),
(555, 53, 'Vilcas Huaman', '051101'),
(556, 53, 'Accomarca', '051102'),
(557, 53, 'Carhuanca', '051103'),
(558, 53, 'Concepcion', '051104'),
(559, 53, 'Huambalpa', '051105'),
(560, 53, 'Independencia', '051106'),
(561, 53, 'Saurama', '051107'),
(562, 53, 'Vischongo', '051108'),
(563, 54, 'Cajamarca', '060101'),
(564, 54, 'Asuncion', '060102'),
(565, 54, 'Chetilla', '060103'),
(566, 54, 'Cospan', '060104'),
(567, 54, 'Encañada', '060105'),
(568, 54, 'Jesus', '060106'),
(569, 54, 'Llacanora', '060107'),
(570, 54, 'Los Baños del Inca', '060108'),
(571, 54, 'Magdalena', '060109'),
(572, 54, 'Matara', '060110'),
(573, 54, 'Namora', '060111'),
(574, 54, 'San Juan', '060112'),
(575, 55, 'Cajabamba', '060201'),
(576, 55, 'Cachachi', '060202'),
(577, 55, 'Condebamba', '060203'),
(578, 55, 'Sitacocha', '060204'),
(579, 56, 'Celendin', '060301'),
(580, 56, 'Chumuch', '060302'),
(581, 56, 'Cortegana', '060303'),
(582, 56, 'Huasmin', '060304'),
(583, 56, 'Jorge Chavez', '060305'),
(584, 56, 'Jose Galvez', '060306'),
(585, 56, 'Miguel Iglesias', '060307'),
(586, 56, 'Oxamarca', '060308'),
(587, 56, 'Sorochuco', '060309'),
(588, 56, 'Sucre', '060310'),
(589, 56, 'Utco', '060311'),
(590, 56, 'La Libertad de Pallan', '060312'),
(591, 57, 'Chota', '060401'),
(592, 57, 'Anguia', '060402'),
(593, 57, 'Chadin', '060403'),
(594, 57, 'Chiguirip', '060404'),
(595, 57, 'Chimban', '060405'),
(596, 57, 'Choropampa', '060406'),
(597, 57, 'Cochabamba', '060407'),
(598, 57, 'Conchan', '060408'),
(599, 57, 'Huambos', '060409'),
(600, 57, 'Lajas', '060410'),
(601, 57, 'Llama', '060411'),
(602, 57, 'Miracosta', '060412'),
(603, 57, 'Paccha', '060413'),
(604, 57, 'Pion', '060414'),
(605, 57, 'Querocoto', '060415'),
(606, 57, 'San Juan de Licupis', '060416'),
(607, 57, 'Tacabamba', '060417'),
(608, 57, 'Tocmoche', '060418'),
(609, 57, 'Chalamarca', '060419'),
(610, 58, 'Contumaza', '060501'),
(611, 58, 'Chilete', '060502'),
(612, 58, 'Cupisnique', '060503'),
(613, 58, 'Guzmango', '060504'),
(614, 58, 'San Benito', '060505'),
(615, 58, 'Santa Cruz de Toled', '060506'),
(616, 58, 'Tantarica', '060507'),
(617, 58, 'Yonan', '060508'),
(618, 59, 'Cutervo', '060601'),
(619, 59, 'Callayuc', '060602'),
(620, 59, 'Choros', '060603'),
(621, 59, 'Cujillo', '060604'),
(622, 59, 'La Ramada', '060605'),
(623, 59, 'Pimpingos', '060606'),
(624, 59, 'Querocotillo', '060607'),
(625, 59, 'San Andres de Cutervo', '060608'),
(626, 59, 'San Juan de Cutervo', '060609'),
(627, 59, 'San Luis de Lucma', '060610'),
(628, 59, 'Santa Cruz', '060611'),
(629, 59, 'Santo Domingo de La Capilla', '060612'),
(630, 59, 'Santo Tomas', '060613'),
(631, 59, 'Socota', '060614'),
(632, 59, 'Toribio Casanova', '060615'),
(633, 60, 'Bambamarca', '060701'),
(634, 60, 'Chugur', '060702'),
(635, 60, 'Hualgayoc', '060703'),
(636, 61, 'Jaen', '060801'),
(637, 61, 'Bellavista', '060802'),
(638, 61, 'Chontali', '060803'),
(639, 61, 'Colasay', '060804'),
(640, 61, 'Huabal', '060805'),
(641, 61, 'Las Pirias', '060806'),
(642, 61, 'Pomahuaca', '060807'),
(643, 61, 'Pucara', '060808'),
(644, 61, 'Sallique', '060809'),
(645, 61, 'San Felipe', '060810'),
(646, 61, 'San Jose del Alto', '060811'),
(647, 61, 'Santa Rosa', '060812'),
(648, 62, 'San Ignacio', '060901'),
(649, 62, 'Chirinos', '060902'),
(650, 62, 'Huarango', '060903'),
(651, 62, 'La Coipa', '060904'),
(652, 62, 'Namballe', '060905'),
(653, 62, 'San Jose de Lourdes', '060906'),
(654, 62, 'Tabaconas', '060907'),
(655, 63, 'Pedro Galvez', '061001'),
(656, 63, 'Chancay', '061002'),
(657, 63, 'Eduardo Villanueva', '061003'),
(658, 63, 'Gregorio Pita', '061004'),
(659, 63, 'Ichocan', '061005'),
(660, 63, 'Jose Manuel Quiroz', '061006'),
(661, 63, 'Jose Sabogal', '061007'),
(662, 64, 'San Miguel', '061101'),
(663, 64, 'Bolivar', '061102'),
(664, 64, 'Calquis', '061103'),
(665, 64, 'Catilluc', '061104'),
(666, 64, 'El Prado', '061105'),
(667, 64, 'La Florida', '061106'),
(668, 64, 'Llapa', '061107'),
(669, 64, 'Nanchoc', '061108'),
(670, 64, 'Niepos', '061109'),
(671, 64, 'San Gregorio', '061110'),
(672, 64, 'San Silvestre de Cochan', '061111'),
(673, 64, 'Tongod', '061112'),
(674, 64, 'Union Agua Blanca', '061113'),
(675, 65, 'San Pablo', '061201'),
(676, 65, 'San Bernardino', '061202'),
(677, 65, 'San Luis', '061203'),
(678, 65, 'Tumbaden', '061204'),
(679, 66, 'Santa Cruz', '061301'),
(680, 66, 'Andabamba', '061302'),
(681, 66, 'Catache', '061303'),
(682, 66, 'Chancaybaños', '061304'),
(683, 66, 'La Esperanza', '061305'),
(684, 66, 'Ninabamba', '061306'),
(685, 66, 'Pulan', '061307'),
(686, 66, 'Saucepampa', '061308'),
(687, 66, 'Sexi', '061309'),
(688, 66, 'Uticyacu', '061310'),
(689, 66, 'Yauyucan', '061311'),
(690, 67, 'Callao', '070101'),
(691, 67, 'Bellavista', '070102'),
(692, 67, 'Carmen de La Legua', '070103'),
(693, 67, 'La Perla', '070104'),
(694, 67, 'La Punta', '070105'),
(695, 67, 'Ventanilla', '070106'),
(696, 67, 'Mi Peru', '070107'),
(697, 68, 'Cusco', '080101'),
(698, 68, 'Ccorca', '080102'),
(699, 68, 'Poroy', '080103'),
(700, 68, 'San Jeronimo', '080104'),
(701, 68, 'San Sebastian', '080105'),
(702, 68, 'Santiago', '080106'),
(703, 68, 'Saylla', '080107'),
(704, 68, 'Wanchaq', '080108'),
(705, 69, 'Acomayo', '080201'),
(706, 69, 'Acopia', '080202'),
(707, 69, 'Acos', '080203'),
(708, 69, 'Mosoc Llacta', '080204'),
(709, 69, 'Pomacanchi', '080205'),
(710, 69, 'Rondocan', '080206'),
(711, 69, 'Sangarara', '080207'),
(712, 70, 'Anta', '080301'),
(713, 70, 'Ancahuasi', '080302'),
(714, 70, 'Cachimayo', '080303'),
(715, 70, 'Chinchaypujio', '080304'),
(716, 70, 'Huarocondo', '080305'),
(717, 70, 'Limatambo', '080306'),
(718, 70, 'Mollepata', '080307'),
(719, 70, 'Pucyura', '080308'),
(720, 70, 'Zurite', '080309'),
(721, 71, 'Calca', '080401'),
(722, 71, 'Coya', '080402'),
(723, 71, 'Lamay', '080403'),
(724, 71, 'Lares', '080404'),
(725, 71, 'Pisac', '080405'),
(726, 71, 'San Salvador', '080406'),
(727, 71, 'Taray', '080407'),
(728, 71, 'Yanatile', '080408'),
(729, 72, 'Yanaoca', '080501'),
(730, 72, 'Checca', '080502'),
(731, 72, 'Kunturkanki', '080503'),
(732, 72, 'Langui', '080504'),
(733, 72, 'Layo', '080505'),
(734, 72, 'Pampamarca', '080506'),
(735, 72, 'Quehue', '080507'),
(736, 72, 'Tupac Amaru', '080508'),
(737, 73, 'Sicuani', '080601'),
(738, 73, 'Checacupe', '080602'),
(739, 73, 'Combapata', '080603'),
(740, 73, 'Marangani', '080604'),
(741, 73, 'Pitumarca', '080605'),
(742, 73, 'San Pablo', '080606'),
(743, 73, 'San Pedro', '080607'),
(744, 73, 'Tinta', '080608'),
(745, 74, 'Santo Tomas', '080701'),
(746, 74, 'Capacmarca', '080702'),
(747, 74, 'Chamaca', '080703'),
(748, 74, 'Colquemarca', '080704'),
(749, 74, 'Livitaca', '080705'),
(750, 74, 'Llusco', '080706'),
(751, 74, 'Quiñota', '080707'),
(752, 74, 'Velille', '080708'),
(753, 75, 'Espinar', '080801'),
(754, 75, 'Condoroma', '080802'),
(755, 75, 'Coporaque', '080803'),
(756, 75, 'Ocoruro', '080804'),
(757, 75, 'Pallpata', '080805'),
(758, 75, 'Pichigua', '080806'),
(759, 75, 'Suyckutambo', '080807'),
(760, 75, 'Alto Pichigua', '080808'),
(761, 76, 'Santa Ana', '080901'),
(762, 76, 'Echarate', '080902'),
(763, 76, 'Huayopata', '080903'),
(764, 76, 'Maranura', '080904'),
(765, 76, 'Ocobamba', '080905'),
(766, 76, 'Quellouno', '080906'),
(767, 76, 'Kimbiri', '080907'),
(768, 76, 'Santa Teresa', '080908'),
(769, 76, 'Vilcabamba', '080909'),
(770, 76, 'Pichari', '080910'),
(771, 76, 'Inkawasi', '080911'),
(772, 76, 'Villa Virgen', '080912'),
(773, 76, 'Villa Kintiarina', '080913'),
(774, 76, 'Megantoni', '080914'),
(775, 77, 'Paruro', '081001'),
(776, 77, 'Accha', '081002'),
(777, 77, 'Ccapi', '081003'),
(778, 77, 'Colcha', '081004'),
(779, 77, 'Huanoquite', '081005'),
(780, 77, 'Omacha', '081006'),
(781, 77, 'Paccaritambo', '081007'),
(782, 77, 'Pillpinto', '081008'),
(783, 77, 'Yaurisque', '081009'),
(784, 78, 'Paucartambo', '081101'),
(785, 78, 'Caicay', '081102'),
(786, 78, 'Challabamba', '081103'),
(787, 78, 'Colquepata', '081104'),
(788, 78, 'Huancarani', '081105'),
(789, 78, 'Kosñipata', '081106'),
(790, 79, 'Urcos', '081201'),
(791, 79, 'Andahuaylillas', '081202'),
(792, 79, 'Camanti', '081203'),
(793, 79, 'Ccarhuayo', '081204'),
(794, 79, 'Ccatca', '081205'),
(795, 79, 'Cusipata', '081206'),
(796, 79, 'Huaro', '081207'),
(797, 79, 'Lucre', '081208'),
(798, 79, 'Marcapata', '081209'),
(799, 79, 'Ocongate', '081210'),
(800, 79, 'Oropesa', '081211'),
(801, 79, 'Quiquijana', '081212'),
(802, 80, 'Urubamba', '081301'),
(803, 80, 'Chinchero', '081302'),
(804, 80, 'Huayllabamba', '081303'),
(805, 80, 'Machupicchu', '081304'),
(806, 80, 'Maras', '081305'),
(807, 80, 'Ollantaytambo', '081306'),
(808, 80, 'Yucay', '081307'),
(809, 81, 'Huancavelica', '090101'),
(810, 81, 'Acobambilla', '090102'),
(811, 81, 'Acoria', '090103'),
(812, 81, 'Conayca', '090104'),
(813, 81, 'Cuenca', '090105'),
(814, 81, 'Huachocolpa', '090106'),
(815, 81, 'Huayllahuara', '090107'),
(816, 81, 'Izcuchaca', '090108'),
(817, 81, 'Laria', '090109'),
(818, 81, 'Manta', '090110'),
(819, 81, 'Mariscal Caceres', '090111'),
(820, 81, 'Moya', '090112'),
(821, 81, 'Nuevo Occoro', '090113'),
(822, 81, 'Palca', '090114'),
(823, 81, 'Pilchaca', '090115'),
(824, 81, 'Vilca', '090116'),
(825, 81, 'Yauli', '090117'),
(826, 81, 'Ascension', '090118'),
(827, 81, 'Huando', '090119'),
(828, 82, 'Acobamba', '090201'),
(829, 82, 'Andabamba', '090202'),
(830, 82, 'Anta', '090203'),
(831, 82, 'Caja', '090204'),
(832, 82, 'Marcas', '090205'),
(833, 82, 'Paucara', '090206'),
(834, 82, 'Pomacocha', '090207'),
(835, 82, 'Rosario', '090208'),
(836, 83, 'Lircay', '090301'),
(837, 83, 'Anchonga', '090302'),
(838, 83, 'Callanmarca', '090303'),
(839, 83, 'Ccochaccasa', '090304'),
(840, 83, 'Chincho', '090305'),
(841, 83, 'Congalla', '090306'),
(842, 83, 'Huanca-Huanca', '090307'),
(843, 83, 'Huayllay Grande', '090308'),
(844, 83, 'Julcamarca', '090309'),
(845, 83, 'San Antonio de Antaparco', '090310'),
(846, 83, 'Santo Tomas de Pata', '090311'),
(847, 83, 'Secclla', '090312'),
(848, 84, 'Castrovirreyna', '090401'),
(849, 84, 'Arma', '090402'),
(850, 84, 'Aurahua', '090403'),
(851, 84, 'Capillas', '090404'),
(852, 84, 'Chupamarca', '090405'),
(853, 84, 'Cocas', '090406'),
(854, 84, 'Huachos', '090407'),
(855, 84, 'Huamatambo', '090408'),
(856, 84, 'Mollepampa', '090409'),
(857, 84, 'San Juan', '090410'),
(858, 84, 'Santa Ana', '090411'),
(859, 84, 'Tantara', '090412'),
(860, 84, 'Ticrapo', '090413'),
(861, 85, 'Churcampa', '090501'),
(862, 85, 'Anco', '090502'),
(863, 85, 'Chinchihuasi', '090503'),
(864, 85, 'El Carmen', '090504'),
(865, 85, 'La Merced', '090505'),
(866, 85, 'Locroja', '090506'),
(867, 85, 'Paucarbamba', '090507'),
(868, 85, 'San Miguel de Mayocc', '090508'),
(869, 85, 'San Pedro de Coris', '090509'),
(870, 85, 'Pachamarca', '090510'),
(871, 85, 'Cosme', '090511'),
(872, 86, 'Huaytara', '090601'),
(873, 86, 'Ayavi', '090602'),
(874, 86, 'Cordova', '090603'),
(875, 86, 'Huayacundo Arma', '090604'),
(876, 86, 'Laramarca', '090605'),
(877, 86, 'Ocoyo', '090606'),
(878, 86, 'Pilpichaca', '090607'),
(879, 86, 'Querco', '090608'),
(880, 86, 'Quito-Arma', '090609'),
(881, 86, 'San Antonio de Cusicancha', '090610'),
(882, 86, 'San Francisco de Sangayaico', '090611'),
(883, 86, 'San Isidro', '090612'),
(884, 86, 'Santiago de Chocorvos', '090613'),
(885, 86, 'Santiago de Quirahuara', '090614'),
(886, 86, 'Santo Domingo de Capillas', '090615'),
(887, 86, 'Tambo', '090616'),
(888, 87, 'Pampas', '090701'),
(889, 87, 'Acostambo', '090702'),
(890, 87, 'Acraquia', '090703'),
(891, 87, 'Ahuaycha', '090704'),
(892, 87, 'Colcabamba', '090705'),
(893, 87, 'Daniel Hernandez', '090706'),
(894, 87, 'Huachocolpa', '090707'),
(895, 87, 'Huaribamba', '090709'),
(896, 87, 'Ñahuimpuquio', '090710'),
(897, 87, 'Pazos', '090711'),
(898, 87, 'Quishuar', '090713'),
(899, 87, 'Salcabamba', '090714'),
(900, 87, 'Salcahuasi', '090715'),
(901, 87, 'San Marcos de Rocchac', '090716'),
(902, 87, 'Surcubamba', '090717'),
(903, 87, 'Tintay Puncu', '090718'),
(904, 87, 'Quichuas', '090719'),
(905, 87, 'Andaymarca', '090720'),
(906, 87, 'Roble', '090721'),
(907, 87, 'Pichos', '090722'),
(908, 87, 'Santiago de Túcuma', '090723'),
(909, 88, 'Huanuco', '100101'),
(910, 88, 'Amarilis', '100102'),
(911, 88, 'Chinchao', '100103'),
(912, 88, 'Churubamba', '100104'),
(913, 88, 'Margos', '100105'),
(914, 88, 'Quisqui', '100106'),
(915, 88, 'San Francisco de Cayran', '100107'),
(916, 88, 'San Pedro de Chaulan', '100108'),
(917, 88, 'Santa Maria del Valle', '100109'),
(918, 88, 'Yarumayo', '100110'),
(919, 88, 'Pillco Marca', '100111'),
(920, 88, 'Yacus', '100112'),
(921, 88, 'San Pablo de Pillao', '100113'),
(922, 89, 'Ambo', '100201'),
(923, 89, 'Cayna', '100202'),
(924, 89, 'Colpas', '100203'),
(925, 89, 'Conchamarca', '100204'),
(926, 89, 'Huacar', '100205'),
(927, 89, 'San Francisco', '100206'),
(928, 89, 'San Rafael', '100207'),
(929, 89, 'Tomay Kichwa', '100208'),
(930, 90, 'La Union', '100301'),
(931, 90, 'Chuquis', '100307'),
(932, 90, 'Marias', '100311'),
(933, 90, 'Pachas', '100313'),
(934, 90, 'Quivilla', '100316'),
(935, 90, 'Ripan', '100317'),
(936, 90, 'Shunqui', '100321'),
(937, 90, 'Sillapata', '100322'),
(938, 90, 'Yanas', '100323'),
(939, 91, 'Huacaybamba', '100401'),
(940, 91, 'Canchabamba', '100402'),
(941, 91, 'Cochabamba', '100403'),
(942, 91, 'Pinra', '100404'),
(943, 92, 'Llata', '100501'),
(944, 92, 'Arancay', '100502'),
(945, 92, 'Chavin de Pariarca', '100503'),
(946, 92, 'Jacas Grande', '100504'),
(947, 92, 'Jircan', '100505'),
(948, 92, 'Miraflores', '100506'),
(949, 92, 'Monzon', '100507'),
(950, 92, 'Punchao', '100508'),
(951, 92, 'Puños', '100509'),
(952, 92, 'Singa', '100510'),
(953, 92, 'Tantamayo', '100511'),
(954, 93, 'Rupa-Rupa', '100601'),
(955, 93, 'Daniel Alomias Robles', '100602'),
(956, 93, 'Hermilio Valdizan', '100603'),
(957, 93, 'Jose Crespo y Castillo', '100604'),
(958, 93, 'Luyando', '100605'),
(959, 93, 'Mariano Damaso Beraun', '100606'),
(960, 93, 'Pucayacu', '100607'),
(961, 93, 'Castillo Grande', '100608'),
(962, 93, 'Pueblo Nuevo', '100609'),
(963, 93, 'Santo Domingo de Anda', '100610'),
(964, 94, 'Huacrachuco', '100701'),
(965, 94, 'Cholon', '100702'),
(966, 94, 'San Buenaventura', '100703'),
(967, 94, 'La Morada', '100704'),
(968, 94, 'Santa Rosa de Alto Yanajanca', '100705'),
(969, 95, 'Panao', '100801'),
(970, 95, 'Chaglla', '100802'),
(971, 95, 'Molino', '100803'),
(972, 95, 'Umari', '100804'),
(973, 96, 'Puerto Inca', '100901'),
(974, 96, 'Codo del Pozuzo', '100902'),
(975, 96, 'Honoria', '100903'),
(976, 96, 'Tournavista', '100904'),
(977, 96, 'Yuyapichis', '100905'),
(978, 97, 'Jesus', '101001'),
(979, 97, 'Baños', '101002'),
(980, 97, 'Jivia', '101003'),
(981, 97, 'Queropalca', '101004'),
(982, 97, 'Rondos', '101005'),
(983, 97, 'San Francisco de Asis', '101006'),
(984, 97, 'San Miguel de Cauri', '101007'),
(985, 98, 'Chavinillo', '101101'),
(986, 98, 'Cahuac', '101102'),
(987, 98, 'Chacabamba', '101103'),
(988, 98, 'Aparicio Pomares', '101104'),
(989, 98, 'Jacas Chico', '101105'),
(990, 98, 'Obas', '101106'),
(991, 98, 'Pampamarca', '101107'),
(992, 98, 'Choras', '101108'),
(993, 99, 'Ica', '110101'),
(994, 99, 'La Tinguiña', '110102'),
(995, 99, 'Los Aquijes', '110103'),
(996, 99, 'Ocucaje', '110104'),
(997, 99, 'Pachacutec', '110105'),
(998, 99, 'Parcona', '110106'),
(999, 99, 'Pueblo Nuevo', '110107'),
(1000, 99, 'Salas', '110108'),
(1001, 99, 'San Jose de los Molinos', '110109'),
(1002, 99, 'San Juan Bautista', '110110'),
(1003, 99, 'Santiago', '110111'),
(1004, 99, 'Subtanjalla', '110112'),
(1005, 99, 'Tate', '110113'),
(1006, 99, 'Yauca del Rosario', '110114'),
(1007, 100, 'Chincha Alta', '110201'),
(1008, 100, 'Alto Laran', '110202'),
(1009, 100, 'Chavin', '110203'),
(1010, 100, 'Chincha Baja', '110204'),
(1011, 100, 'El Carmen', '110205'),
(1012, 100, 'Grocio Prado', '110206'),
(1013, 100, 'Pueblo Nuevo', '110207'),
(1014, 100, 'San Juan de Yanac', '110208'),
(1015, 100, 'San Pedro de Huacarpana', '110209'),
(1016, 100, 'Sunampe', '110210'),
(1017, 100, 'Tambo de Mora', '110211'),
(1018, 101, 'Nazca', '110301'),
(1019, 101, 'Changuillo', '110302'),
(1020, 101, 'El Ingenio', '110303'),
(1021, 101, 'Marcona', '110304'),
(1022, 101, 'Vista Alegre', '110305'),
(1023, 102, 'Palpa', '110401'),
(1024, 102, 'Llipata', '110402'),
(1025, 102, 'Rio Grande', '110403'),
(1026, 102, 'Santa Cruz', '110404'),
(1027, 102, 'Tibillo', '110405'),
(1028, 103, 'Pisco', '110501'),
(1029, 103, 'Huancano', '110502'),
(1030, 103, 'Humay', '110503'),
(1031, 103, 'Independencia', '110504'),
(1032, 103, 'Paracas', '110505'),
(1033, 103, 'San Andres', '110506'),
(1034, 103, 'San Clemente', '110507'),
(1035, 103, 'Tupac Amaru Inca', '110508'),
(1036, 104, 'Huancayo', '120101'),
(1037, 104, 'Carhuacallanga', '120104'),
(1038, 104, 'Chacapampa', '120105'),
(1039, 104, 'Chicche', '120106'),
(1040, 104, 'Chilca', '120107'),
(1041, 104, 'Chongos Alto', '120108'),
(1042, 104, 'Chupuro', '120111'),
(1043, 104, 'Colca', '120112'),
(1044, 104, 'Cullhuas', '120113'),
(1045, 104, 'El Tambo', '120114'),
(1046, 104, 'Huacrapuquio', '120116'),
(1047, 104, 'Hualhuas', '120117'),
(1048, 104, 'Huancan', '120119'),
(1049, 104, 'Huasicancha', '120120'),
(1050, 104, 'Huayucachi', '120121'),
(1051, 104, 'Ingenio', '120122'),
(1052, 104, 'Pariahuanca', '120124'),
(1053, 104, 'Pilcomayo', '120125'),
(1054, 104, 'Pucara', '120126'),
(1055, 104, 'Quichuay', '120127'),
(1056, 104, 'Quilcas', '120128'),
(1057, 104, 'San Agustin', '120129'),
(1058, 104, 'San Jeronimo de Tunan', '120130'),
(1059, 104, 'Saño', '120132'),
(1060, 104, 'Sapallanga', '120133'),
(1061, 104, 'Sicaya', '120134'),
(1062, 104, 'Santo Domingo de Acobamba', '120135'),
(1063, 104, 'Viques', '120136'),
(1064, 105, 'Concepcion', '120201'),
(1065, 105, 'Aco', '120202'),
(1066, 105, 'Andamarca', '120203'),
(1067, 105, 'Chambara', '120204'),
(1068, 105, 'Cochas', '120205'),
(1069, 105, 'Comas', '120206'),
(1070, 105, 'Heroinas Toledo', '120207'),
(1071, 105, 'Manzanares', '120208'),
(1072, 105, 'Mariscal Castilla', '120209'),
(1073, 105, 'Matahuasi', '120210'),
(1074, 105, 'Mito', '120211'),
(1075, 105, 'Nueve de Julio', '120212'),
(1076, 105, 'Orcotuna', '120213'),
(1077, 105, 'San Jose de Quero', '120214'),
(1078, 105, 'Santa Rosa de Ocopa', '120215'),
(1079, 106, 'Chanchamayo', '120301'),
(1080, 106, 'Perene', '120302'),
(1081, 106, 'Pichanaqui', '120303'),
(1082, 106, 'San Luis de Shuaro', '120304'),
(1083, 106, 'San Ramon', '120305'),
(1084, 106, 'Vitoc', '120306'),
(1085, 107, 'Jauja', '120401'),
(1086, 107, 'Acolla', '120402'),
(1087, 107, 'Apata', '120403'),
(1088, 107, 'Ataura', '120404'),
(1089, 107, 'Canchayllo', '120405'),
(1090, 107, 'Curicaca', '120406'),
(1091, 107, 'El Mantaro', '120407'),
(1092, 107, 'Huamali', '120408'),
(1093, 107, 'Huaripampa', '120409'),
(1094, 107, 'Huertas', '120410'),
(1095, 107, 'Janjaillo', '120411'),
(1096, 107, 'Julcan', '120412'),
(1097, 107, 'Leonor Ordoñez', '120413'),
(1098, 107, 'Llocllapampa', '120414'),
(1099, 107, 'Marco', '120415'),
(1100, 107, 'Masma', '120416'),
(1101, 107, 'Masma Chicche', '120417'),
(1102, 107, 'Molinos', '120418'),
(1103, 107, 'Monobamba', '120419'),
(1104, 107, 'Muqui', '120420'),
(1105, 107, 'Muquiyauyo', '120421'),
(1106, 107, 'Paca', '120422'),
(1107, 107, 'Paccha', '120423'),
(1108, 107, 'Pancan', '120424'),
(1109, 107, 'Parco', '120425'),
(1110, 107, 'Pomacancha', '120426'),
(1111, 107, 'Ricran', '120427'),
(1112, 107, 'San Lorenzo', '120428'),
(1113, 107, 'San Pedro de Chunan', '120429'),
(1114, 107, 'Sausa', '120430'),
(1115, 107, 'Sincos', '120431'),
(1116, 107, 'Tunan Marca', '120432'),
(1117, 107, 'Yauli', '120433'),
(1118, 107, 'Yauyos', '120434'),
(1119, 108, 'Junin', '120501'),
(1120, 108, 'Carhuamayo', '120502'),
(1121, 108, 'Ondores', '120503'),
(1122, 108, 'Ulcumayo', '120504'),
(1123, 109, 'Satipo', '120601'),
(1124, 109, 'Coviriali', '120602'),
(1125, 109, 'Llaylla', '120603'),
(1126, 109, 'Mazamari', '120604'),
(1127, 109, 'Pampa Hermosa', '120605'),
(1128, 109, 'Pangoa', '120606'),
(1129, 109, 'Rio Negro', '120607'),
(1130, 109, 'Rio Tambo', '120608'),
(1131, 109, 'Vizcatán del Ene', '120609'),
(1132, 110, 'Tarma', '120701'),
(1133, 110, 'Acobamba', '120702'),
(1134, 110, 'Huaricolca', '120703'),
(1135, 110, 'Huasahuasi', '120704'),
(1136, 110, 'La Union', '120705'),
(1137, 110, 'Palca', '120706'),
(1138, 110, 'Palcamayo', '120707'),
(1139, 110, 'San Pedro de Cajas', '120708'),
(1140, 110, 'Tapo', '120709'),
(1141, 111, 'La Oroya', '120801'),
(1142, 111, 'Chacapalpa', '120802'),
(1143, 111, 'Huay-Huay', '120803'),
(1144, 111, 'Marcapomacocha', '120804'),
(1145, 111, 'Morococha', '120805'),
(1146, 111, 'Paccha', '120806'),
(1147, 111, 'Santa Barbara de Carhuacayan', '120807'),
(1148, 111, 'Santa Rosa de Sacco', '120808'),
(1149, 111, 'Suitucancha', '120809'),
(1150, 111, 'Yauli', '120810'),
(1151, 112, 'Chupaca', '120901'),
(1152, 112, 'Ahuac', '120902'),
(1153, 112, 'Chongos Bajo', '120903'),
(1154, 112, 'Huachac', '120904'),
(1155, 112, 'Huamancaca Chico', '120905'),
(1156, 112, 'San Juan de Yscos', '120906'),
(1157, 112, 'San Juan de Jarpa', '120907'),
(1158, 112, 'Tres de Diciembre', '120908'),
(1159, 112, 'Yanacancha', '120909'),
(1160, 113, 'Trujillo', '130101'),
(1161, 113, 'El Porvenir', '130102'),
(1162, 113, 'Florencia de Mora', '130103'),
(1163, 113, 'Huanchaco', '130104'),
(1164, 113, 'La Esperanza', '130105'),
(1165, 113, 'Laredo', '130106'),
(1166, 113, 'Moche', '130107'),
(1167, 113, 'Poroto', '130108'),
(1168, 113, 'Salaverry', '130109'),
(1169, 113, 'Simbal', '130110'),
(1170, 113, 'Victor Larco Herrera', '130111'),
(1171, 114, 'Ascope', '130201'),
(1172, 114, 'Chicama', '130202'),
(1173, 114, 'Chocope', '130203'),
(1174, 114, 'Magdalena de Cao', '130204'),
(1175, 114, 'Paijan', '130205'),
(1176, 114, 'Razuri', '130206'),
(1177, 114, 'Santiago de Cao', '130207'),
(1178, 114, 'Casa Grande', '130208'),
(1179, 115, 'Bolivar', '130301'),
(1180, 115, 'Bambamarca', '130302'),
(1181, 115, 'Condormarca', '130303'),
(1182, 115, 'Longotea', '130304'),
(1183, 115, 'Uchumarca', '130305'),
(1184, 115, 'Ucuncha', '130306'),
(1185, 116, 'Chepen', '130401'),
(1186, 116, 'Pacanga', '130402'),
(1187, 116, 'Pueblo Nuevo', '130403'),
(1188, 117, 'Julcan', '130501'),
(1189, 117, 'Calamarca', '130502'),
(1190, 117, 'Carabamba', '130503'),
(1191, 117, 'Huaso', '130504'),
(1192, 118, 'Otuzco', '130601'),
(1193, 118, 'Agallpampa', '130602'),
(1194, 118, 'Charat', '130604'),
(1195, 118, 'Huaranchal', '130605'),
(1196, 118, 'La Cuesta', '130606'),
(1197, 118, 'Mache', '130608'),
(1198, 118, 'Paranday', '130610'),
(1199, 118, 'Salpo', '130611'),
(1200, 118, 'Sinsicap', '130613'),
(1201, 118, 'Usquil', '130614'),
(1202, 119, 'San Pedro de Lloc', '130701'),
(1203, 119, 'Guadalupe', '130702'),
(1204, 119, 'Jequetepeque', '130703'),
(1205, 119, 'Pacasmayo', '130704'),
(1206, 119, 'San Jose', '130705'),
(1207, 120, 'Tayabamba', '130801'),
(1208, 120, 'Buldibuyo', '130802'),
(1209, 120, 'Chillia', '130803'),
(1210, 120, 'Huancaspata', '130804'),
(1211, 120, 'Huaylillas', '130805'),
(1212, 120, 'Huayo', '130806'),
(1213, 120, 'Ongon', '130807'),
(1214, 120, 'Parcoy', '130808'),
(1215, 120, 'Pataz', '130809'),
(1216, 120, 'Pias', '130810'),
(1217, 120, 'Santiago de Challas', '130811'),
(1218, 120, 'Taurija', '130812'),
(1219, 120, 'Urpay', '130813'),
(1220, 121, 'Huamachuco', '130901'),
(1221, 121, 'Chugay', '130902'),
(1222, 121, 'Cochorco', '130903'),
(1223, 121, 'Curgos', '130904'),
(1224, 121, 'Marcabal', '130905'),
(1225, 121, 'Sanagoran', '130906'),
(1226, 121, 'Sarin', '130907'),
(1227, 121, 'Sartimbamba', '130908'),
(1228, 122, 'Santiago de Chuco', '131001'),
(1229, 122, 'Angasmarca', '131002'),
(1230, 122, 'Cachicadan', '131003'),
(1231, 122, 'Mollebamba', '131004'),
(1232, 122, 'Mollepata', '131005'),
(1233, 122, 'Quiruvilca', '131006'),
(1234, 122, 'Santa Cruz de Chuca', '131007'),
(1235, 122, 'Sitabamba', '131008'),
(1236, 123, 'Cascas', '131101'),
(1237, 123, 'Lucma', '131102'),
(1238, 123, 'Compin', '131103'),
(1239, 123, 'Sayapullo', '131104'),
(1240, 124, 'Viru', '131201'),
(1241, 124, 'Chao', '131202'),
(1242, 124, 'Guadalupito', '131203'),
(1243, 125, 'Chiclayo', '140101'),
(1244, 125, 'Chongoyape', '140102'),
(1245, 125, 'Eten', '140103'),
(1246, 125, 'Eten Puerto', '140104'),
(1247, 125, 'Jose Leonardo Ortiz', '140105'),
(1248, 125, 'La Victoria', '140106'),
(1249, 125, 'Lagunas', '140107'),
(1250, 125, 'Monsefu', '140108'),
(1251, 125, 'Nueva Arica', '140109'),
(1252, 125, 'Oyotun', '140110'),
(1253, 125, 'Picsi', '140111'),
(1254, 125, 'Pimentel', '140112'),
(1255, 125, 'Reque', '140113'),
(1256, 125, 'Santa Rosa', '140114'),
(1257, 125, 'Saña', '140115'),
(1258, 125, 'Cayalti', '140116'),
(1259, 125, 'Patapo', '140117'),
(1260, 125, 'Pomalca', '140118'),
(1261, 125, 'Pucala', '140119'),
(1262, 125, 'Tuman', '140120'),
(1263, 126, 'Ferreñafe', '140201'),
(1264, 126, 'Cañaris', '140202'),
(1265, 126, 'Incahuasi', '140203'),
(1266, 126, 'Manuel Antonio Mesones Muro', '140204'),
(1267, 126, 'Pitipo', '140205'),
(1268, 126, 'Pueblo Nuevo', '140206'),
(1269, 127, 'Lambayeque', '140301'),
(1270, 127, 'Chochope', '140302'),
(1271, 127, 'Illimo', '140303'),
(1272, 127, 'Jayanca', '140304'),
(1273, 127, 'Mochumi', '140305'),
(1274, 127, 'Morrope', '140306'),
(1275, 127, 'Motupe', '140307'),
(1276, 127, 'Olmos', '140308'),
(1277, 127, 'Pacora', '140309'),
(1278, 127, 'Salas', '140310'),
(1279, 127, 'San Jose', '140311'),
(1280, 127, 'Tucume', '140312'),
(1281, 128, 'Lima', '150101'),
(1282, 128, 'Ancon', '150102'),
(1283, 128, 'Ate', '150103'),
(1284, 128, 'Barranco', '150104'),
(1285, 128, 'Breña', '150105'),
(1286, 128, 'Carabayllo', '150106'),
(1287, 128, 'Chaclacayo', '150107'),
(1288, 128, 'Chorrillos', '150108'),
(1289, 128, 'Cieneguilla', '150109'),
(1290, 128, 'Comas', '150110'),
(1291, 128, 'El Agustino', '150111'),
(1292, 128, 'Independencia', '150112'),
(1293, 128, 'Jesus Maria', '150113'),
(1294, 128, 'La Molina', '150114'),
(1295, 128, 'La Victoria', '150115'),
(1296, 128, 'Lince', '150116'),
(1297, 128, 'Los Olivos', '150117'),
(1298, 128, 'Lurigancho', '150118'),
(1299, 128, 'Lurin', '150119'),
(1300, 128, 'Magdalena del Mar', '150120'),
(1301, 128, 'Pueblo Libre', '150121'),
(1302, 128, 'Miraflores', '150122'),
(1303, 128, 'Pachacamac', '150123'),
(1304, 128, 'Pucusana', '150124'),
(1305, 128, 'Puente Piedra', '150125'),
(1306, 128, 'Punta Hermosa', '150126'),
(1307, 128, 'Punta Negra', '150127'),
(1308, 128, 'Rimac', '150128'),
(1309, 128, 'San Bartolo', '150129'),
(1310, 128, 'San Borja', '150130'),
(1311, 128, 'San Isidro', '150131'),
(1312, 128, 'San Juan de Lurigancho', '150132'),
(1313, 128, 'San Juan de Miraflores', '150133'),
(1314, 128, 'San Luis', '150134'),
(1315, 128, 'San Martin de Porres', '150135'),
(1316, 128, 'San Miguel', '150136'),
(1317, 128, 'Santa Anita', '150137'),
(1318, 128, 'Santa Maria del Mar', '150138'),
(1319, 128, 'Santa Rosa', '150139'),
(1320, 128, 'Santiago de Surco', '150140'),
(1321, 128, 'Surquillo', '150141'),
(1322, 128, 'Villa El Salvador', '150142'),
(1323, 128, 'Villa Maria del Triunfo', '150143'),
(1324, 129, 'Barranca', '150201'),
(1325, 129, 'Paramonga', '150202'),
(1326, 129, 'Pativilca', '150203'),
(1327, 129, 'Supe', '150204'),
(1328, 129, 'Supe Puerto', '150205'),
(1329, 130, 'Cajatambo', '150301'),
(1330, 130, 'Copa', '150302'),
(1331, 130, 'Gorgor', '150303'),
(1332, 130, 'Huancapon', '150304'),
(1333, 130, 'Manas', '150305'),
(1334, 131, 'Canta', '150401'),
(1335, 131, 'Arahuay', '150402'),
(1336, 131, 'Huamantanga', '150403'),
(1337, 131, 'Huaros', '150404'),
(1338, 131, 'Lachaqui', '150405'),
(1339, 131, 'San Buenaventura', '150406'),
(1340, 131, 'Santa Rosa de Quives', '150407'),
(1341, 132, 'San Vicente de Cañete', '150501'),
(1342, 132, 'Asia', '150502'),
(1343, 132, 'Calango', '150503'),
(1344, 132, 'Cerro Azul', '150504'),
(1345, 132, 'Chilca', '150505'),
(1346, 132, 'Coayllo', '150506'),
(1347, 132, 'Imperial', '150507'),
(1348, 132, 'Lunahuana', '150508'),
(1349, 132, 'Mala', '150509'),
(1350, 132, 'Nuevo Imperial', '150510'),
(1351, 132, 'Pacaran', '150511'),
(1352, 132, 'Quilmana', '150512'),
(1353, 132, 'San Antonio', '150513'),
(1354, 132, 'San Luis', '150514'),
(1355, 132, 'Santa Cruz de Flores', '150515'),
(1356, 132, 'Zuñiga', '150516'),
(1357, 133, 'Huaral', '150601'),
(1358, 133, 'Atavillos Alto', '150602'),
(1359, 133, 'Atavillos Bajo', '150603'),
(1360, 133, 'Aucallama', '150604'),
(1361, 133, 'Chancay', '150605'),
(1362, 133, 'Ihuari', '150606'),
(1363, 133, 'Lampian', '150607'),
(1364, 133, 'Pacaraos', '150608'),
(1365, 133, 'San Miguel de Acos', '150609'),
(1366, 133, 'Santa Cruz de Andamarca', '150610'),
(1367, 133, 'Sumbilca', '150611'),
(1368, 133, 'Veintisiete de Noviembre', '150612'),
(1369, 134, 'Matucana', '150701'),
(1370, 134, 'Antioquia', '150702'),
(1371, 134, 'Callahuanca', '150703'),
(1372, 134, 'Carampoma', '150704'),
(1373, 134, 'Chicla', '150705'),
(1374, 134, 'Cuenca', '150706'),
(1375, 134, 'Huachupampa', '150707'),
(1376, 134, 'Huanza', '150708'),
(1377, 134, 'Huarochiri', '150709'),
(1378, 134, 'Lahuaytambo', '150710'),
(1379, 134, 'Langa', '150711'),
(1380, 134, 'Laraos', '150712'),
(1381, 134, 'Mariatana', '150713'),
(1382, 134, 'Ricardo Palma', '150714'),
(1383, 134, 'San Andres de Tupicocha', '150715'),
(1384, 134, 'San Antonio', '150716'),
(1385, 134, 'San Bartolome', '150717'),
(1386, 134, 'San Damian', '150718'),
(1387, 134, 'San Juan de Iris', '150719'),
(1388, 134, 'San Juan de Tantaranche', '150720'),
(1389, 134, 'San Lorenzo de Quinti', '150721'),
(1390, 134, 'San Mateo', '150722'),
(1391, 134, 'San Mateo de Otao', '150723'),
(1392, 134, 'San Pedro de Casta', '150724'),
(1393, 134, 'San Pedro de Huancayre', '150725'),
(1394, 134, 'Sangallaya', '150726'),
(1395, 134, 'Santa Cruz de Cocachacra', '150727'),
(1396, 134, 'Santa Eulalia', '150728'),
(1397, 134, 'Santiago de Anchucaya', '150729'),
(1398, 134, 'Santiago de Tuna', '150730'),
(1399, 134, 'Santo Domingo de los Olleros', '150731'),
(1400, 134, 'Surco', '150732'),
(1401, 135, 'Huacho', '150801'),
(1402, 135, 'Ambar', '150802'),
(1403, 135, 'Caleta de Carquin', '150803'),
(1404, 135, 'Checras', '150804'),
(1405, 135, 'Hualmay', '150805'),
(1406, 135, 'Huaura', '150806'),
(1407, 135, 'Leoncio Prado', '150807'),
(1408, 135, 'Paccho', '150808'),
(1409, 135, 'Santa Leonor', '150809'),
(1410, 135, 'Santa Maria', '150810'),
(1411, 135, 'Sayan', '150811'),
(1412, 135, 'Vegueta', '150812'),
(1413, 136, 'Oyon', '150901'),
(1414, 136, 'Andajes', '150902'),
(1415, 136, 'Caujul', '150903'),
(1416, 136, 'Cochamarca', '150904'),
(1417, 136, 'Navan', '150905'),
(1418, 136, 'Pachangara', '150906'),
(1419, 137, 'Yauyos', '151001'),
(1420, 137, 'Alis', '151002'),
(1421, 137, 'Ayauca', '151003'),
(1422, 137, 'Ayaviri', '151004'),
(1423, 137, 'Azangaro', '151005'),
(1424, 137, 'Cacra', '151006'),
(1425, 137, 'Carania', '151007'),
(1426, 137, 'Catahuasi', '151008'),
(1427, 137, 'Chocos', '151009'),
(1428, 137, 'Cochas', '151010'),
(1429, 137, 'Colonia', '151011'),
(1430, 137, 'Hongos', '151012'),
(1431, 137, 'Huampara', '151013'),
(1432, 137, 'Huancaya', '151014'),
(1433, 137, 'Huangascar', '151015'),
(1434, 137, 'Huantan', '151016'),
(1435, 137, 'Huañec', '151017'),
(1436, 137, 'Laraos', '151018'),
(1437, 137, 'Lincha', '151019'),
(1438, 137, 'Madean', '151020'),
(1439, 137, 'Miraflores', '151021'),
(1440, 137, 'Omas', '151022'),
(1441, 137, 'Putinza', '151023'),
(1442, 137, 'Quinches', '151024'),
(1443, 137, 'Quinocay', '151025'),
(1444, 137, 'San Joaquin', '151026'),
(1445, 137, 'San Pedro de Pilas', '151027'),
(1446, 137, 'Tanta', '151028'),
(1447, 137, 'Tauripampa', '151029'),
(1448, 137, 'Tomas', '151030'),
(1449, 137, 'Tupe', '151031'),
(1450, 137, 'Viñac', '151032'),
(1451, 137, 'Vitis', '151033'),
(1452, 138, 'Iquitos', '160101'),
(1453, 138, 'Alto Nanay', '160102'),
(1454, 138, 'Fernando Lores', '160103'),
(1455, 138, 'Indiana', '160104'),
(1456, 138, 'Las Amazonas', '160105'),
(1457, 138, 'Mazan', '160106'),
(1458, 138, 'Napo', '160107'),
(1459, 138, 'Punchana', '160108'),
(1460, 138, 'Torres Causana', '160110'),
(1461, 138, 'Belen', '160112'),
(1462, 138, 'San Juan Bautista', '160113'),
(1463, 139, 'Yurimaguas', '160201'),
(1464, 139, 'Balsapuerto', '160202'),
(1465, 139, 'Jeberos', '160205'),
(1466, 139, 'Lagunas', '160206'),
(1467, 139, 'Santa Cruz', '160210'),
(1468, 139, 'Teniente Cesar Lopez Rojas', '160211'),
(1469, 140, 'Nauta', '160301'),
(1470, 140, 'Parinari', '160302'),
(1471, 140, 'Tigre', '160303'),
(1472, 140, 'Trompeteros', '160304'),
(1473, 140, 'Urarinas', '160305'),
(1474, 141, 'Ramon Castilla', '160401'),
(1475, 141, 'Pebas', '160402'),
(1476, 141, 'Yavari', '160403'),
(1477, 141, 'San Pablo', '160404'),
(1478, 142, 'Requena', '160501'),
(1479, 142, 'Alto Tapiche', '160502'),
(1480, 142, 'Capelo', '160503'),
(1481, 142, 'Emilio San Martin', '160504'),
(1482, 142, 'Maquia', '160505'),
(1483, 142, 'Puinahua', '160506'),
(1484, 142, 'Saquena', '160507'),
(1485, 142, 'Soplin', '160508'),
(1486, 142, 'Tapiche', '160509'),
(1487, 142, 'Jenaro Herrera', '160510'),
(1488, 142, 'Yaquerana', '160511'),
(1489, 143, 'Contamana', '160601'),
(1490, 143, 'Inahuaya', '160602'),
(1491, 143, 'Padre Marquez', '160603'),
(1492, 143, 'Pampa Hermosa', '160604'),
(1493, 143, 'Sarayacu', '160605'),
(1494, 143, 'Vargas Guerra', '160606'),
(1495, 144, 'Barranca', '160701'),
(1496, 144, 'Cahuapanas', '160702'),
(1497, 144, 'Manseriche', '160703'),
(1498, 144, 'Morona', '160704'),
(1499, 144, 'Pastaza', '160705'),
(1500, 144, 'Andoas', '160706'),
(1501, 138, 'Putumayo', '160801'),
(1502, 138, 'Rosa Panduro', '160802'),
(1503, 138, 'Teniente Manuel Clavero', '160803'),
(1504, 138, 'Yaguas', '160804'),
(1505, 145, 'Tambopata', '170101'),
(1506, 145, 'Inambari', '170102'),
(1507, 145, 'Las Piedras', '170103'),
(1508, 145, 'Laberinto', '170104'),
(1509, 146, 'Manu', '170201'),
(1510, 146, 'Fitzcarrald', '170202'),
(1511, 146, 'Madre de Dios', '170203'),
(1512, 146, 'Huepetuhe', '170204'),
(1513, 147, 'Iñapari', '170301'),
(1514, 147, 'Iberia', '170302'),
(1515, 147, 'Tahuamanu', '170303'),
(1516, 148, 'Moquegua', '180101'),
(1517, 148, 'Carumas', '180102'),
(1518, 148, 'Cuchumbaya', '180103'),
(1519, 148, 'Samegua', '180104'),
(1520, 148, 'San Cristobal', '180105'),
(1521, 148, 'Torata', '180106');
INSERT INTO `distritos` (`iddistrito`, `idprovincia`, `distrito`, `ubigeoinei`) VALUES
(1522, 149, 'Omate', '180201'),
(1523, 149, 'Chojata', '180202'),
(1524, 149, 'Coalaque', '180203'),
(1525, 149, 'Ichuña', '180204'),
(1526, 149, 'La Capilla', '180205'),
(1527, 149, 'Lloque', '180206'),
(1528, 149, 'Matalaque', '180207'),
(1529, 149, 'Puquina', '180208'),
(1530, 149, 'Quinistaquillas', '180209'),
(1531, 149, 'Ubinas', '180210'),
(1532, 149, 'Yunga', '180211'),
(1533, 150, 'Ilo', '180301'),
(1534, 150, 'El Algarrobal', '180302'),
(1535, 150, 'Pacocha', '180303'),
(1536, 151, 'Chaupimarca', '190101'),
(1537, 151, 'Huachon', '190102'),
(1538, 151, 'Huariaca', '190103'),
(1539, 151, 'Huayllay', '190104'),
(1540, 151, 'Ninacaca', '190105'),
(1541, 151, 'Pallanchacra', '190106'),
(1542, 151, 'Paucartambo', '190107'),
(1543, 151, 'San Francisco de Asis de Yarusyacan', '190108'),
(1544, 151, 'Simon Bolivar', '190109'),
(1545, 151, 'Ticlacayan', '190110'),
(1546, 151, 'Tinyahuarco', '190111'),
(1547, 151, 'Vicco', '190112'),
(1548, 151, 'Yanacancha', '190113'),
(1549, 152, 'Yanahuanca', '190201'),
(1550, 152, 'Chacayan', '190202'),
(1551, 152, 'Goyllarisquizga', '190203'),
(1552, 152, 'Paucar', '190204'),
(1553, 152, 'San Pedro de Pillao', '190205'),
(1554, 152, 'Santa Ana de Tusi', '190206'),
(1555, 152, 'Tapuc', '190207'),
(1556, 152, 'Vilcabamba', '190208'),
(1557, 153, 'Oxapampa', '190301'),
(1558, 153, 'Chontabamba', '190302'),
(1559, 153, 'Huancabamba', '190303'),
(1560, 153, 'Palcazu', '190304'),
(1561, 153, 'Pozuzo', '190305'),
(1562, 153, 'Puerto Bermudez', '190306'),
(1563, 153, 'Villa Rica', '190307'),
(1564, 153, 'Constitución', '190308'),
(1565, 154, 'Piura', '200101'),
(1566, 154, 'Castilla', '200104'),
(1567, 154, 'Catacaos', '200105'),
(1568, 154, 'Cura Mori', '200107'),
(1569, 154, 'El Tallan', '200108'),
(1570, 154, 'La Arena', '200109'),
(1571, 154, 'La Union', '200110'),
(1572, 154, 'Las Lomas', '200111'),
(1573, 154, 'Tambo Grande', '200114'),
(1574, 154, '26 de Octubre', '200115'),
(1575, 155, 'Ayabaca', '200201'),
(1576, 155, 'Frias', '200202'),
(1577, 155, 'Jilili', '200203'),
(1578, 155, 'Lagunas', '200204'),
(1579, 155, 'Montero', '200205'),
(1580, 155, 'Pacaipampa', '200206'),
(1581, 155, 'Paimas', '200207'),
(1582, 155, 'Sapillica', '200208'),
(1583, 155, 'Sicchez', '200209'),
(1584, 155, 'Suyo', '200210'),
(1585, 156, 'Huancabamba', '200301'),
(1586, 156, 'Canchaque', '200302'),
(1587, 156, 'El Carmen de La Frontera', '200303'),
(1588, 156, 'Huarmaca', '200304'),
(1589, 156, 'Lalaquiz', '200305'),
(1590, 156, 'San Miguel de El Faique', '200306'),
(1591, 156, 'Sondor', '200307'),
(1592, 156, 'Sondorillo', '200308'),
(1593, 157, 'Chulucanas', '200401'),
(1594, 157, 'Buenos Aires', '200402'),
(1595, 157, 'Chalaco', '200403'),
(1596, 157, 'La Matanza', '200404'),
(1597, 157, 'Morropon', '200405'),
(1598, 157, 'Salitral', '200406'),
(1599, 157, 'San Juan de Bigote', '200407'),
(1600, 157, 'Santa Catalina de Mossa', '200408'),
(1601, 157, 'Santo Domingo', '200409'),
(1602, 157, 'Yamango', '200410'),
(1603, 158, 'Paita', '200501'),
(1604, 158, 'Amotape', '200502'),
(1605, 158, 'Arenal', '200503'),
(1606, 158, 'Colan', '200504'),
(1607, 158, 'La Huaca', '200505'),
(1608, 158, 'Tamarindo', '200506'),
(1609, 158, 'Vichayal', '200507'),
(1610, 159, 'Sullana', '200601'),
(1611, 159, 'Bellavista', '200602'),
(1612, 159, 'Ignacio Escudero', '200603'),
(1613, 159, 'Lancones', '200604'),
(1614, 159, 'Marcavelica', '200605'),
(1615, 159, 'Miguel Checa', '200606'),
(1616, 159, 'Querecotillo', '200607'),
(1617, 159, 'Salitral', '200608'),
(1618, 160, 'Pariñas', '200701'),
(1619, 160, 'El Alto', '200702'),
(1620, 160, 'La Brea', '200703'),
(1621, 160, 'Lobitos', '200704'),
(1622, 160, 'Los Organos', '200705'),
(1623, 160, 'Mancora', '200706'),
(1624, 161, 'Sechura', '200801'),
(1625, 161, 'Bellavista de La Union', '200802'),
(1626, 161, 'Bernal', '200803'),
(1627, 161, 'Cristo Nos Valga', '200804'),
(1628, 161, 'Vice', '200805'),
(1629, 161, 'Rinconada Llicuar', '200806'),
(1630, 162, 'Puno', '210101'),
(1631, 162, 'Acora', '210102'),
(1632, 162, 'Amantani', '210103'),
(1633, 162, 'Atuncolla', '210104'),
(1634, 162, 'Capachica', '210105'),
(1635, 162, 'Chucuito', '210106'),
(1636, 162, 'Coata', '210107'),
(1637, 162, 'Huata', '210108'),
(1638, 162, 'Mañazo', '210109'),
(1639, 162, 'Paucarcolla', '210110'),
(1640, 162, 'Pichacani', '210111'),
(1641, 162, 'Plateria', '210112'),
(1642, 162, 'San Antonio', '210113'),
(1643, 162, 'Tiquillaca', '210114'),
(1644, 162, 'Vilque', '210115'),
(1645, 163, 'Azangaro', '210201'),
(1646, 163, 'Achaya', '210202'),
(1647, 163, 'Arapa', '210203'),
(1648, 163, 'Asillo', '210204'),
(1649, 163, 'Caminaca', '210205'),
(1650, 163, 'Chupa', '210206'),
(1651, 163, 'Jose Domingo Choquehuanca', '210207'),
(1652, 163, 'Muñani', '210208'),
(1653, 163, 'Potoni', '210209'),
(1654, 163, 'Saman', '210210'),
(1655, 163, 'San Anton', '210211'),
(1656, 163, 'San Jose', '210212'),
(1657, 163, 'San Juan de Salinas', '210213'),
(1658, 163, 'Santiago de Pupuja', '210214'),
(1659, 163, 'Tirapata', '210215'),
(1660, 164, 'Macusani', '210301'),
(1661, 164, 'Ajoyani', '210302'),
(1662, 164, 'Ayapata', '210303'),
(1663, 164, 'Coasa', '210304'),
(1664, 164, 'Corani', '210305'),
(1665, 164, 'Crucero', '210306'),
(1666, 164, 'Ituata', '210307'),
(1667, 164, 'Ollachea', '210308'),
(1668, 164, 'San Gaban', '210309'),
(1669, 164, 'Usicayos', '210310'),
(1670, 165, 'Juli', '210401'),
(1671, 165, 'Desaguadero', '210402'),
(1672, 165, 'Huacullani', '210403'),
(1673, 165, 'Kelluyo', '210404'),
(1674, 165, 'Pisacoma', '210405'),
(1675, 165, 'Pomata', '210406'),
(1676, 165, 'Zepita', '210407'),
(1677, 166, 'Ilave', '210501'),
(1678, 166, 'Capazo', '210502'),
(1679, 166, 'Pilcuyo', '210503'),
(1680, 166, 'Santa Rosa', '210504'),
(1681, 166, 'Conduriri', '210505'),
(1682, 167, 'Huancane', '210601'),
(1683, 167, 'Cojata', '210602'),
(1684, 167, 'Huatasani', '210603'),
(1685, 167, 'Inchupalla', '210604'),
(1686, 167, 'Pusi', '210605'),
(1687, 167, 'Rosaspata', '210606'),
(1688, 167, 'Taraco', '210607'),
(1689, 167, 'Vilque Chico', '210608'),
(1690, 168, 'Lampa', '210701'),
(1691, 168, 'Cabanilla', '210702'),
(1692, 168, 'Calapuja', '210703'),
(1693, 168, 'Nicasio', '210704'),
(1694, 168, 'Ocuviri', '210705'),
(1695, 168, 'Palca', '210706'),
(1696, 168, 'Paratia', '210707'),
(1697, 168, 'Pucara', '210708'),
(1698, 168, 'Santa Lucia', '210709'),
(1699, 168, 'Vilavila', '210710'),
(1700, 169, 'Ayaviri', '210801'),
(1701, 169, 'Antauta', '210802'),
(1702, 169, 'Cupi', '210803'),
(1703, 169, 'Llalli', '210804'),
(1704, 169, 'Macari', '210805'),
(1705, 169, 'Nuñoa', '210806'),
(1706, 169, 'Orurillo', '210807'),
(1707, 169, 'Santa Rosa', '210808'),
(1708, 169, 'Umachiri', '210809'),
(1709, 170, 'Moho', '210901'),
(1710, 170, 'Conima', '210902'),
(1711, 170, 'Huayrapata', '210903'),
(1712, 170, 'Tilali', '210904'),
(1713, 171, 'Putina', '211001'),
(1714, 171, 'Ananea', '211002'),
(1715, 171, 'Pedro Vilca Apaza', '211003'),
(1716, 171, 'Quilcapuncu', '211004'),
(1717, 171, 'Sina', '211005'),
(1718, 172, 'Juliaca', '211101'),
(1719, 172, 'Cabana', '211102'),
(1720, 172, 'Cabanillas', '211103'),
(1721, 172, 'Caracoto', '211104'),
(1722, 172, 'San Miguel', '211105'),
(1723, 173, 'Sandia', '211201'),
(1724, 173, 'Cuyocuyo', '211202'),
(1725, 173, 'Limbani', '211203'),
(1726, 173, 'Patambuco', '211204'),
(1727, 173, 'Phara', '211205'),
(1728, 173, 'Quiaca', '211206'),
(1729, 173, 'San Juan del Oro', '211207'),
(1730, 173, 'Yanahuaya', '211208'),
(1731, 173, 'Alto Inambari', '211209'),
(1732, 173, 'San Pedro de Putina Punco', '211210'),
(1733, 174, 'Yunguyo', '211301'),
(1734, 174, 'Anapia', '211302'),
(1735, 174, 'Copani', '211303'),
(1736, 174, 'Cuturapi', '211304'),
(1737, 174, 'Ollaraya', '211305'),
(1738, 174, 'Tinicachi', '211306'),
(1739, 174, 'Unicachi', '211307'),
(1740, 175, 'Moyobamba', '220101'),
(1741, 175, 'Calzada', '220102'),
(1742, 175, 'Habana', '220103'),
(1743, 175, 'Jepelacio', '220104'),
(1744, 175, 'Soritor', '220105'),
(1745, 175, 'Yantalo', '220106'),
(1746, 176, 'Bellavista', '220201'),
(1747, 176, 'Alto Biavo', '220202'),
(1748, 176, 'Bajo Biavo', '220203'),
(1749, 176, 'Huallaga', '220204'),
(1750, 176, 'San Pablo', '220205'),
(1751, 176, 'San Rafael', '220206'),
(1752, 177, 'San Jose de Sisa', '220301'),
(1753, 177, 'Agua Blanca', '220302'),
(1754, 177, 'San Martin', '220303'),
(1755, 177, 'Santa Rosa', '220304'),
(1756, 177, 'Shatoja', '220305'),
(1757, 178, 'Saposoa', '220401'),
(1758, 178, 'Alto Saposoa', '220402'),
(1759, 178, 'El Eslabon', '220403'),
(1760, 178, 'Piscoyacu', '220404'),
(1761, 178, 'Sacanche', '220405'),
(1762, 178, 'Tingo de Saposoa', '220406'),
(1763, 179, 'Lamas', '220501'),
(1764, 179, 'Alonso de Alvarado', '220502'),
(1765, 179, 'Barranquita', '220503'),
(1766, 179, 'Caynarachi', '220504'),
(1767, 179, 'Cuñumbuqui', '220505'),
(1768, 179, 'Pinto Recodo', '220506'),
(1769, 179, 'Rumisapa', '220507'),
(1770, 179, 'San Roque de Cumbaza', '220508'),
(1771, 179, 'Shanao', '220509'),
(1772, 179, 'Tabalosos', '220510'),
(1773, 179, 'Zapatero', '220511'),
(1774, 180, 'Juanjui', '220601'),
(1775, 180, 'Campanilla', '220602'),
(1776, 180, 'Huicungo', '220603'),
(1777, 180, 'Pachiza', '220604'),
(1778, 180, 'Pajarillo', '220605'),
(1779, 181, 'Picota', '220701'),
(1780, 181, 'Buenos Aires', '220702'),
(1781, 181, 'Caspisapa', '220703'),
(1782, 181, 'Pilluana', '220704'),
(1783, 181, 'Pucacaca', '220705'),
(1784, 181, 'San Cristobal', '220706'),
(1785, 181, 'San Hilarion', '220707'),
(1786, 181, 'Shamboyacu', '220708'),
(1787, 181, 'Tingo de Ponasa', '220709'),
(1788, 181, 'Tres Unidos', '220710'),
(1789, 182, 'Rioja', '220801'),
(1790, 182, 'Awajun', '220802'),
(1791, 182, 'Elias Soplin Vargas', '220803'),
(1792, 182, 'Nueva Cajamarca', '220804'),
(1793, 182, 'Pardo Miguel', '220805'),
(1794, 182, 'Posic', '220806'),
(1795, 182, 'San Fernando', '220807'),
(1796, 182, 'Yorongos', '220808'),
(1797, 182, 'Yuracyacu', '220809'),
(1798, 183, 'Tarapoto', '220901'),
(1799, 183, 'Alberto Leveau', '220902'),
(1800, 183, 'Cacatachi', '220903'),
(1801, 183, 'Chazuta', '220904'),
(1802, 183, 'Chipurana', '220905'),
(1803, 183, 'El Porvenir', '220906'),
(1804, 183, 'Huimbayoc', '220907'),
(1805, 183, 'Juan Guerra', '220908'),
(1806, 183, 'La Banda de Shilcayo', '220909'),
(1807, 183, 'Morales', '220910'),
(1808, 183, 'Papaplaya', '220911'),
(1809, 183, 'San Antonio', '220912'),
(1810, 183, 'Sauce', '220913'),
(1811, 183, 'Shapaja', '220914'),
(1812, 184, 'Tocache', '221001'),
(1813, 184, 'Nuevo Progreso', '221002'),
(1814, 184, 'Polvora', '221003'),
(1815, 184, 'Shunte', '221004'),
(1816, 184, 'Uchiza', '221005'),
(1817, 185, 'Tacna', '230101'),
(1818, 185, 'Alto de La Alianza', '230102'),
(1819, 185, 'Calana', '230103'),
(1820, 185, 'Ciudad Nueva', '230104'),
(1821, 185, 'Inclan', '230105'),
(1822, 185, 'Pachia', '230106'),
(1823, 185, 'Palca', '230107'),
(1824, 185, 'Pocollay', '230108'),
(1825, 185, 'Sama', '230109'),
(1826, 185, 'Coronel Gregorio Albarracin Lanchipa', '230110'),
(1827, 185, 'La Yarada-Los Palos', '230111'),
(1828, 186, 'Candarave', '230201'),
(1829, 186, 'Cairani', '230202'),
(1830, 186, 'Camilaca', '230203'),
(1831, 186, 'Curibaya', '230204'),
(1832, 186, 'Huanuara', '230205'),
(1833, 186, 'Quilahuani', '230206'),
(1834, 187, 'Locumba', '230301'),
(1835, 187, 'Ilabaya', '230302'),
(1836, 187, 'Ite', '230303'),
(1837, 188, 'Tarata', '230401'),
(1838, 188, 'Heroes Albarracin', '230402'),
(1839, 188, 'Estique', '230403'),
(1840, 188, 'Estique-Pampa', '230404'),
(1841, 188, 'Sitajara', '230405'),
(1842, 188, 'Susapaya', '230406'),
(1843, 188, 'Tarucachi', '230407'),
(1844, 188, 'Ticaco', '230408'),
(1845, 189, 'Tumbes', '240101'),
(1846, 189, 'Corrales', '240102'),
(1847, 189, 'La Cruz', '240103'),
(1848, 189, 'Pampas de Hospital', '240104'),
(1849, 189, 'San Jacinto', '240105'),
(1850, 189, 'San Juan de La Virgen', '240106'),
(1851, 190, 'Zorritos', '240201'),
(1852, 190, 'Casitas', '240202'),
(1853, 190, 'Canoas de Punta Sal', '240203'),
(1854, 191, 'Zarumilla', '240301'),
(1855, 191, 'Aguas Verdes', '240302'),
(1856, 191, 'Matapalo', '240303'),
(1857, 191, 'Papayal', '240304'),
(1858, 192, 'Calleria', '250101'),
(1859, 192, 'Campoverde', '250102'),
(1860, 192, 'Iparia', '250103'),
(1861, 192, 'Masisea', '250104'),
(1862, 192, 'Yarinacocha', '250105'),
(1863, 192, 'Nueva Requena', '250106'),
(1864, 192, 'Manantay', '250107'),
(1865, 193, 'Raymondi', '250201'),
(1866, 193, 'Sepahua', '250202'),
(1867, 193, 'Tahuania', '250203'),
(1868, 193, 'Yurua', '250204'),
(1869, 194, 'Padre Abad', '250301'),
(1870, 194, 'Irazola', '250302'),
(1871, 194, 'Curimana', '250303'),
(1872, 194, 'Neshuya', '250304'),
(1873, 194, 'Alexander von Humboldt', '250305'),
(1874, 195, 'Purus', '250401');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entidadespago`
--

CREATE TABLE `entidadespago` (
  `identidadpago` int(11) NOT NULL,
  `entidad` varchar(20) NOT NULL,
  `tipo` enum('Banco','Caja','Financiera') NOT NULL DEFAULT 'Banco',
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `modificado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

CREATE TABLE `gastos` (
  `idgasto` int(11) NOT NULL,
  `idvehiculo` int(11) NOT NULL,
  `descripcion` varchar(300) NOT NULL,
  `importe` decimal(9,2) NOT NULL,
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `modificado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `locales`
--

CREATE TABLE `locales` (
  `idlocal` int(11) NOT NULL,
  `tienda` varchar(40) NOT NULL,
  `iddistrito` int(11) NOT NULL,
  `idmotorpark` int(11) NOT NULL,
  `principal` enum('S','N') NOT NULL,
  `responsable` varchar(100) NOT NULL,
  `correo` varchar(200) DEFAULT NULL,
  `direccion` varchar(300) DEFAULT NULL,
  `telefono` varchar(12) DEFAULT NULL,
  `latitud` varchar(20) DEFAULT NULL,
  `longitud` varchar(20) DEFAULT NULL,
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `modificado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

CREATE TABLE `marcas` (
  `idmarca` int(11) NOT NULL,
  `marca` varchar(40) NOT NULL,
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `modificado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `marcas`
--

INSERT INTO `marcas` (`idmarca`, `marca`, `creado`, `modificado`) VALUES
(1, 'BMW', '2025-05-02 16:46:40', NULL),
(2, 'CHANGAN', '2025-05-02 16:46:40', NULL),
(3, 'CHERY', '2025-05-02 16:46:40', NULL),
(4, 'CHEVROLET', '2025-05-02 16:46:40', NULL),
(5, 'DFSK', '2025-05-02 16:46:40', NULL),
(6, 'DONGFENG', '2025-05-02 16:46:40', NULL),
(7, 'FORD', '2025-05-02 16:46:40', NULL),
(8, 'GEELY', '2025-05-02 16:46:40', NULL),
(9, 'GREAT WALL', '2025-05-02 16:46:40', NULL),
(10, 'HAVAL', '2025-05-02 16:46:40', NULL),
(11, 'HONDA', '2025-05-02 16:46:40', NULL),
(12, 'HYUNDAI', '2025-05-02 16:46:40', NULL),
(13, 'JAC', '2025-05-02 16:46:40', NULL),
(14, 'JETOUR', '2025-05-02 16:46:40', NULL),
(15, 'KIA', '2025-05-02 16:46:40', NULL),
(16, 'MAZDA', '2025-05-02 16:46:40', NULL),
(17, 'MERCEDES BENZ', '2025-05-02 16:46:40', NULL),
(18, 'NISSAN', '2025-05-02 16:46:40', NULL),
(19, 'RENAULT', '2025-05-02 16:46:40', NULL),
(20, 'SSANGYONG', '2025-05-02 16:46:40', NULL),
(21, 'TOYOTA', '2025-05-02 16:46:40', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modelos`
--

CREATE TABLE `modelos` (
  `idmodelo` int(11) NOT NULL,
  `idtipovehiculo` int(11) NOT NULL,
  `idmarca` int(11) NOT NULL,
  `modelo` varchar(40) NOT NULL,
  `anio` char(4) NOT NULL,
  `imagenreferencial` varchar(200) DEFAULT NULL,
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `modificado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modelos`
--

INSERT INTO `modelos` (`idmodelo`, `idtipovehiculo`, `idmarca`, `modelo`, `anio`, `imagenreferencial`, `creado`, `modificado`) VALUES
(1, 1, 8, 'Emgrand', '2025', NULL, '2025-05-02 17:31:47', NULL),
(2, 2, 8, 'Cityray', '2025', NULL, '2025-05-02 17:31:47', NULL),
(3, 2, 8, 'CX3 Pro', '2025', NULL, '2025-05-02 17:31:47', NULL),
(4, 2, 8, 'Coolray', '2025', NULL, '2025-05-02 17:31:47', NULL),
(5, 2, 8, 'New Starray', '2025', NULL, '2025-05-02 17:31:47', NULL),
(6, 2, 10, 'New Jolion', '2025', NULL, '2025-05-02 17:37:38', NULL),
(7, 2, 10, 'H6', '2025', NULL, '2025-05-02 17:37:38', NULL),
(8, 2, 10, 'Jolion pro', '2025', NULL, '2025-05-02 17:37:38', NULL),
(9, 2, 10, 'Dargo', '2025', NULL, '2025-05-02 17:37:38', NULL),
(10, 2, 10, 'H6 GT', '2025', NULL, '2025-05-02 17:37:38', NULL),
(11, 2, 10, 'Jolion Híbrido', '2025', NULL, '2025-05-02 17:37:38', NULL),
(12, 2, 10, 'H6 Híbrido', '2025', NULL, '2025-05-02 17:37:38', NULL),
(13, 3, 12, 'Grand i10', '2025', NULL, '2025-05-02 17:42:47', NULL),
(14, 3, 12, 'i20 Hatch', '2025', NULL, '2025-05-02 17:42:47', NULL),
(15, 1, 12, 'Grand i10 Sedan', '2025', NULL, '2025-05-02 17:42:47', NULL),
(16, 1, 12, 'Accent', '2025', NULL, '2025-05-02 17:42:47', NULL),
(17, 1, 12, 'Elantra', '2025', NULL, '2025-05-02 17:42:47', NULL),
(18, 2, 12, 'Venue', '2025', NULL, '2025-05-02 17:42:47', NULL),
(19, 2, 12, 'Creta', '2025', NULL, '2025-05-02 17:42:47', NULL),
(20, 2, 12, 'Creta Grand', '2025', NULL, '2025-05-02 17:42:47', NULL),
(21, 2, 12, 'Kona', '2025', NULL, '2025-05-02 17:42:47', NULL),
(22, 2, 12, 'Tucson', '2025', NULL, '2025-05-02 17:42:47', NULL),
(23, 2, 12, 'Santa Fe', '2025', NULL, '2025-05-02 17:42:47', NULL),
(24, 2, 12, 'Palisade', '2025', NULL, '2025-05-02 17:42:47', NULL),
(25, 1, 15, 'All-new K3', '2025', NULL, '2025-05-02 17:54:26', NULL),
(26, 1, 15, 'Soluto', '2025', NULL, '2025-05-02 17:54:26', NULL),
(27, 3, 15, 'Picanto', '2025', NULL, '2025-05-02 17:54:26', NULL),
(28, 2, 15, 'Sorento', '2025', NULL, '2025-05-02 17:54:26', NULL),
(29, 2, 15, 'K3 Cross', '2025', NULL, '2025-05-02 17:54:26', NULL),
(30, 2, 15, 'Carnival', '2025', NULL, '2025-05-02 17:54:26', NULL),
(31, 2, 15, 'Seltos', '2025', NULL, '2025-05-02 17:54:26', NULL),
(32, 2, 15, 'Sonet', '2025', NULL, '2025-05-02 17:54:26', NULL),
(33, 2, 15, 'Carens', '2025', NULL, '2025-05-02 17:54:26', NULL),
(34, 2, 15, 'Sportage', '2025', NULL, '2025-05-02 17:54:26', NULL),
(35, 2, 2, 'NEW CS15', '2025', NULL, '2025-06-07 11:01:06', NULL),
(36, 2, 2, 'NEW CS35 Plus', '2025', NULL, '2025-06-07 11:04:09', NULL),
(37, 2, 2, 'NEW CS55 Plus', '2025', NULL, '2025-06-07 11:05:44', NULL),
(38, 2, 2, 'X7 Plus', '2025', NULL, '2025-06-07 11:09:49', NULL),
(39, 2, 2, 'UNIT-T', '2025', NULL, '2025-06-07 11:10:17', NULL),
(40, 1, 3, 'ARRIZO 5 Pro', '2025', NULL, '2025-06-07 11:11:43', NULL),
(41, 2, 13, 'JS2', '2025', NULL, '2025-06-07 11:12:26', NULL),
(42, 2, 13, 'JS3', '2025', NULL, '2025-06-07 11:13:01', NULL),
(43, 5, 13, 'T8', '2025', NULL, '2025-06-07 11:13:24', NULL),
(44, 8, 12, 'H-100', '2025', NULL, '2025-06-14 12:16:11', NULL),
(49, 2, 12, 'Tucson', '2023', NULL, '2025-06-16 16:44:59', NULL),
(50, 2, 12, 'Tucson', '2024', NULL, '2025-06-16 16:45:06', NULL),
(52, 1, 12, 'Elantra', '2024', NULL, '2025-06-16 16:45:39', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `motorpark`
--

CREATE TABLE `motorpark` (
  `idmotorpark` int(11) NOT NULL,
  `ruc` char(11) NOT NULL,
  `razonsocial` varchar(300) NOT NULL,
  `nombrecomercial` varchar(100) NOT NULL,
  `representante` varchar(100) DEFAULT NULL,
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `modificado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenescompra`
--

CREATE TABLE `ordenescompra` (
  `idordencompra` int(11) NOT NULL COMMENT 'Será el número de orden de compra',
  `idtienda` int(11) NOT NULL,
  `idlogistica` int(11) NOT NULL,
  `moneda` enum('USD','PEN') NOT NULL,
  `serie` char(4) NOT NULL COMMENT 'Será el año',
  `emision` date NOT NULL COMMENT 'Se creó la orden de compra',
  `aprobacion` date DEFAULT NULL COMMENT 'Gerencia aprueba la orden',
  `presentacion` date DEFAULT NULL COMMENT 'Logística envía la orden al concesionario',
  `anulacion` date DEFAULT NULL COMMENT 'Logística anula la orden de compra',
  `numstock` varchar(20) DEFAULT NULL COMMENT 'Este dato será provisto por el concesionario - opcional',
  `observaciones` varchar(400) DEFAULT NULL,
  `estado` enum('emitido','aprobado','presentado','anulado','pagado') NOT NULL DEFAULT 'emitido'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ordenescompra`
--

INSERT INTO `ordenescompra` (`idordencompra`, `idtienda`, `idlogistica`, `moneda`, `serie`, `emision`, `aprobacion`, `presentacion`, `anulacion`, `numstock`, `observaciones`, `estado`) VALUES
(1, 27, 2, 'USD', '2025', '2025-06-25', NULL, NULL, NULL, NULL, NULL, 'emitido');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personas`
--

CREATE TABLE `personas` (
  `idpersona` int(11) NOT NULL,
  `apellidos` varchar(70) NOT NULL,
  `nombres` varchar(70) NOT NULL,
  `tipodoc` enum('DNI','CEX','PAS') NOT NULL DEFAULT 'DNI',
  `nrodoc` varchar(12) NOT NULL,
  `genero` enum('M','F') NOT NULL,
  `fechanac` date DEFAULT NULL,
  `estadocivil` enum('SOL','CAS','VDO','DVC','CNV') DEFAULT NULL COMMENT 'Soltero, casado, viudo, divorciado y conviviente',
  `email` varchar(150) DEFAULT NULL,
  `iddistrito` int(11) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `referencia` varchar(200) DEFAULT NULL,
  `telprimario` char(9) NOT NULL,
  `telalternativo` char(9) DEFAULT NULL,
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `modificado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `personas`
--

INSERT INTO `personas` (`idpersona`, `apellidos`, `nombres`, `tipodoc`, `nrodoc`, `genero`, `fechanac`, `estadocivil`, `email`, `iddistrito`, `direccion`, `referencia`, `telprimario`, `telalternativo`, `creado`, `modificado`) VALUES
(1, 'Francia Minaya', 'Jhon Edward', 'DNI', '45406071', 'M', '1984-09-20', 'CAS', 'sistemas@yondaperu.com', 1012, 'Upis Felix Amoretti Mz G Lote 19', 'Cerca loza deportiva', '956834915', NULL, '2025-05-16 16:17:30', NULL),
(2, 'Llana Lozano', 'Keiko Leticia', 'DNI', '73476525', 'F', '1995-01-10', 'SOL', 'asistentecontable@yondaperu.com', 1012, 'Jiron Alva Maurtua N° 100', NULL, '926743607', NULL, '2025-06-21 11:57:52', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `provincias`
--

CREATE TABLE `provincias` (
  `idprovincia` int(11) NOT NULL,
  `iddepartamento` int(11) NOT NULL,
  `provincia` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `provincias`
--

INSERT INTO `provincias` (`idprovincia`, `iddepartamento`, `provincia`) VALUES
(1, 1, 'Chachapoyas'),
(2, 1, 'Bagua'),
(3, 1, 'Bongara'),
(4, 1, 'Condorcanqui'),
(5, 1, 'Luya'),
(6, 1, 'Rodriguez de Mendoza'),
(7, 1, 'Utcubamba'),
(8, 2, 'Huaraz'),
(9, 2, 'Aija'),
(10, 2, 'Antonio Raymondi'),
(11, 2, 'Asuncion'),
(12, 2, 'Bolognesi'),
(13, 2, 'Carhuaz'),
(14, 2, 'Carlos Fermin Fitzca'),
(15, 2, 'Casma'),
(16, 2, 'Corongo'),
(17, 2, 'Huari'),
(18, 2, 'Huarmey'),
(19, 2, 'Huaylas'),
(20, 2, 'Mariscal Luzuriaga'),
(21, 2, 'Ocros'),
(22, 2, 'Pallasca'),
(23, 2, 'Pomabamba'),
(24, 2, 'Recuay'),
(25, 2, 'Santa'),
(26, 2, 'Sihuas'),
(27, 2, 'Yungay'),
(28, 3, 'Abancay'),
(29, 3, 'Andahuaylas'),
(30, 3, 'Antabamba'),
(31, 3, 'Aymaraes'),
(32, 3, 'Cotabambas'),
(33, 3, 'Chincheros'),
(34, 3, 'Grau'),
(35, 4, 'Arequipa'),
(36, 4, 'Camana'),
(37, 4, 'Caraveli'),
(38, 4, 'Castilla'),
(39, 4, 'Caylloma'),
(40, 4, 'Condesuyos'),
(41, 4, 'Islay'),
(42, 4, 'La Union'),
(43, 5, 'Huamanga'),
(44, 5, 'Cangallo'),
(45, 5, 'Huanca Sancos'),
(46, 5, 'Huanta'),
(47, 5, 'La Mar'),
(48, 5, 'Lucanas'),
(49, 5, 'Parinacochas'),
(50, 5, 'Paucar del Sara Sara'),
(51, 5, 'Sucre'),
(52, 5, 'Victor Fajardo'),
(53, 5, 'Vilcas Huaman'),
(54, 6, 'Cajamarca'),
(55, 6, 'Cajabamba'),
(56, 6, 'Celendin'),
(57, 6, 'Chota'),
(58, 6, 'Contumaza'),
(59, 6, 'Cutervo'),
(60, 6, 'Hualgayoc'),
(61, 6, 'Jaen'),
(62, 6, 'San Ignacio'),
(63, 6, 'San Marcos'),
(64, 6, 'San Miguel'),
(65, 6, 'San Pablo'),
(66, 6, 'Santa Cruz'),
(67, 7, 'Callao'),
(68, 8, 'Cusco'),
(69, 8, 'Acomayo'),
(70, 8, 'Anta'),
(71, 8, 'Calca'),
(72, 8, 'Canas'),
(73, 8, 'Canchis'),
(74, 8, 'Chumbivilcas'),
(75, 8, 'Espinar'),
(76, 8, 'La Convencion'),
(77, 8, 'Paruro'),
(78, 8, 'Paucartambo'),
(79, 8, 'Quispicanchi'),
(80, 8, 'Urubamba'),
(81, 9, 'Huancavelica'),
(82, 9, 'Acobamba'),
(83, 9, 'Angaraes'),
(84, 9, 'Castrovirreyna'),
(85, 9, 'Churcampa'),
(86, 9, 'Huaytara'),
(87, 9, 'Tayacaja'),
(88, 10, 'Huanuco'),
(89, 10, 'Ambo'),
(90, 10, 'Dos de Mayo'),
(91, 10, 'Huacaybamba'),
(92, 10, 'Huamalies'),
(93, 10, 'Leoncio Prado'),
(94, 10, 'Marañon'),
(95, 10, 'Pachitea'),
(96, 10, 'Puerto Inca'),
(97, 10, 'Lauricocha'),
(98, 10, 'Yarowilca'),
(99, 11, 'Ica'),
(100, 11, 'Chincha'),
(101, 11, 'Nazca'),
(102, 11, 'Palpa'),
(103, 11, 'Pisco'),
(104, 12, 'Huancayo'),
(105, 12, 'Concepcion'),
(106, 12, 'Chanchamayo'),
(107, 12, 'Jauja'),
(108, 12, 'Junin'),
(109, 12, 'Satipo'),
(110, 12, 'Tarma'),
(111, 12, 'Yauli'),
(112, 12, 'Chupaca'),
(113, 13, 'Trujillo'),
(114, 13, 'Ascope'),
(115, 13, 'Bolivar'),
(116, 13, 'Chepen'),
(117, 13, 'Julcan'),
(118, 13, 'Otuzco'),
(119, 13, 'Pacasmayo'),
(120, 13, 'Pataz'),
(121, 13, 'Sanchez Carrion'),
(122, 13, 'Santiago de Chuco'),
(123, 13, 'Gran Chimu'),
(124, 13, 'Viru'),
(125, 14, 'Chiclayo'),
(126, 14, 'Ferreñafe'),
(127, 14, 'Lambayeque'),
(128, 15, 'Lima'),
(129, 15, 'Barranca'),
(130, 15, 'Cajatambo'),
(131, 15, 'Canta'),
(132, 15, 'Cañete'),
(133, 15, 'Huaral'),
(134, 15, 'Huarochiri'),
(135, 15, 'Huaura'),
(136, 15, 'Oyon'),
(137, 15, 'Yauyos'),
(138, 16, 'Maynas'),
(139, 16, 'Alto Amazonas'),
(140, 16, 'Loreto'),
(141, 16, 'Mariscal Ramon Castilla'),
(142, 16, 'Requena'),
(143, 16, 'Ucayali'),
(144, 16, 'Datem del Marañon'),
(145, 17, 'Tambopata'),
(146, 17, 'Manu'),
(147, 17, 'Tahuamanu'),
(148, 18, 'Mariscal Nieto'),
(149, 18, 'General Sanchez Cerr'),
(150, 18, 'Ilo'),
(151, 19, 'Pasco'),
(152, 19, 'Daniel Alcides Carri'),
(153, 19, 'Oxapampa'),
(154, 20, 'Piura'),
(155, 20, 'Ayabaca'),
(156, 20, 'Huancabamba'),
(157, 20, 'Morropon'),
(158, 20, 'Paita'),
(159, 20, 'Sullana'),
(160, 20, 'Talara'),
(161, 20, 'Sechura'),
(162, 21, 'Puno'),
(163, 21, 'Azangaro'),
(164, 21, 'Carabaya'),
(165, 21, 'Chucuito'),
(166, 21, 'El Collao'),
(167, 21, 'Huancane'),
(168, 21, 'Lampa'),
(169, 21, 'Melgar'),
(170, 21, 'Moho'),
(171, 21, 'San Antonio de Putin'),
(172, 21, 'San Roman'),
(173, 21, 'Sandia'),
(174, 21, 'Yunguyo'),
(175, 22, 'Moyobamba'),
(176, 22, 'Bellavista'),
(177, 22, 'El Dorado'),
(178, 22, 'Huallaga'),
(179, 22, 'Lamas'),
(180, 22, 'Mariscal Caceres'),
(181, 22, 'Picota'),
(182, 22, 'Rioja'),
(183, 22, 'San Martin'),
(184, 22, 'Tocache'),
(185, 23, 'Tacna'),
(186, 23, 'Candarave'),
(187, 23, 'Jorge Basadre'),
(188, 23, 'Tarata'),
(189, 24, 'Tumbes'),
(190, 24, 'Contralmirante Villa'),
(191, 24, 'Zarumilla'),
(192, 25, 'Coronel Portillo'),
(193, 25, 'Atalaya'),
(194, 25, 'Padre Abad'),
(195, 25, 'Purus');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiendas`
--

CREATE TABLE `tiendas` (
  `idtienda` int(11) NOT NULL,
  `iddistrito` int(11) NOT NULL,
  `idconcesionario` int(11) NOT NULL,
  `direccion` varchar(300) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `telefono` varchar(12) NOT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `modificado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tiendas`
--

INSERT INTO `tiendas` (`idtienda`, `iddistrito`, `idconcesionario`, `direccion`, `email`, `telefono`, `contacto`, `creado`, `modificado`) VALUES
(15, 367, 2, 'Calle San Martin 778', 'miguel@gmail.com', '956888777', 'Pacheco', '2025-05-10 09:13:19', '2025-05-10 12:03:57'),
(16, 913, 2, 'Calle Mendoza 778', 'luisa@gmail.com', '985622233', 'Luisa Magallanes', '2025-05-10 09:13:45', '2025-05-10 12:06:24'),
(19, 364, 2, 'Calle Principal 212', 'jorgito@gmail.com', '9855445555', 'Jorge Peña Prietos', '2025-05-10 11:00:11', '2025-05-10 12:02:44'),
(24, 338, 2, 'Calle A', 'luchito@gmail.com', '9567778888', 'Luis', '2025-05-10 12:29:34', NULL),
(26, 995, 6, 'Calle San Francisco', 'luis@gmail.com', '956000122', 'Luis Magallanes', '2025-05-15 16:53:06', NULL),
(27, 1285, 7, 'Jiron San Martin 123', 'arturo@gmail.com', '956888999', 'Arturo Magallanes Castro', '2025-05-17 11:06:14', NULL),
(29, 993, 9, 'Calle San Martin', 'jorge@toyota.com', '95681454554', 'Jorge', '2025-06-05 16:12:48', NULL),
(30, 387, 7, 'Urb. Rosales Mz G lote 19', 'carlos@HYUNDAI.com', '956444555', 'Carlos Prada Tasayco', '2025-06-10 15:30:21', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipovehiculos`
--

CREATE TABLE `tipovehiculos` (
  `idtipovehiculo` int(11) NOT NULL,
  `tipovehiculo` varchar(40) NOT NULL,
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `modificado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipovehiculos`
--

INSERT INTO `tipovehiculos` (`idtipovehiculo`, `tipovehiculo`, `creado`, `modificado`) VALUES
(1, 'Sedan', '2025-05-02 16:55:12', NULL),
(2, 'SUV', '2025-05-02 16:55:12', NULL),
(3, 'Hatchback', '2025-05-02 16:55:12', NULL),
(4, 'Station wagon', '2025-05-02 16:55:12', NULL),
(5, 'Pickup', '2025-05-02 16:55:12', NULL),
(6, 'Coupé', '2025-05-02 16:55:12', NULL),
(7, 'Van', '2025-05-02 16:55:12', NULL),
(8, 'Camión ligero', '2025-05-02 16:55:12', NULL),
(9, 'Motolineal', '2025-05-02 17:23:14', NULL),
(10, 'Motocarga', '2025-05-02 17:23:14', NULL),
(11, 'Mototaxi', '2025-06-07 10:26:14', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

CREATE TABLE `vehiculos` (
  `idvehiculo` int(11) NOT NULL,
  `idmodelo` int(11) NOT NULL,
  `version` varchar(20) NOT NULL,
  `condicion` enum('nuevo','seminuevo') NOT NULL DEFAULT 'nuevo',
  `idcombustible` int(11) NOT NULL,
  `color` varchar(30) DEFAULT NULL,
  `chasis` varchar(30) DEFAULT NULL,
  `placa` varchar(10) DEFAULT NULL,
  `placarotativa` varchar(10) DEFAULT NULL,
  `seriemotor` varchar(20) DEFAULT NULL,
  `moneda` enum('USD','PEN') DEFAULT 'USD',
  `precioventa` decimal(9,2) DEFAULT NULL,
  `disponibilidad` enum('proceso','libre','separado','vendido','recuperado') NOT NULL,
  `idlogistica` int(11) NOT NULL,
  `idlocal` int(11) DEFAULT NULL,
  `origen` enum('OCP','OLD','CTZ') NOT NULL COMMENT 'OCP = Orden de compra (conducto regular), OLD (Contratos anteriores al sistema), CTZ (Cotizado por asesor)',
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `modificado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `amortizacionesoc`
--
ALTER TABLE `amortizacionesoc`
  ADD PRIMARY KEY (`idamortizacion`),
  ADD KEY `fk_idorden_aoc` (`idorden`),
  ADD KEY `fk_idlogistica_aoc` (`idlogistica`),
  ADD KEY `fk_identidadpago_aoc` (`identidadpago`);

--
-- Indices de la tabla `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`idarea`),
  ADD UNIQUE KEY `uk_area_are` (`area`);

--
-- Indices de la tabla `cargos`
--
ALTER TABLE `cargos`
  ADD PRIMARY KEY (`idcargo`),
  ADD KEY `fk_idarea_car` (`idarea`);

--
-- Indices de la tabla `colaboradores`
--
ALTER TABLE `colaboradores`
  ADD PRIMARY KEY (`idcolaborador`),
  ADD UNIQUE KEY `uk_usernick_col` (`usernick`),
  ADD KEY `fk_idcontratolaboral_col` (`idcontratolaboral`);

--
-- Indices de la tabla `combustibles`
--
ALTER TABLE `combustibles`
  ADD PRIMARY KEY (`idcombustible`),
  ADD UNIQUE KEY `uk_combustible_cmb` (`combustible`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`idcompra`),
  ADD KEY `fk_idorden_cmp` (`idorden`),
  ADD KEY `fk_idlogistica_cmp` (`idlogistica`);

--
-- Indices de la tabla `concesionarios`
--
ALTER TABLE `concesionarios`
  ADD PRIMARY KEY (`idconcesionario`),
  ADD UNIQUE KEY `uk_ruc_con` (`ruc`);

--
-- Indices de la tabla `contratoslaborales`
--
ALTER TABLE `contratoslaborales`
  ADD PRIMARY KEY (`idcontratolaboral`),
  ADD KEY `fk_idpersona_cla` (`idpersona`),
  ADD KEY `fk_idcargocla` (`idcargo`);

--
-- Indices de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  ADD PRIMARY KEY (`iddepartamento`);

--
-- Indices de la tabla `detordencompra`
--
ALTER TABLE `detordencompra`
  ADD PRIMARY KEY (`iddetordencompra`),
  ADD UNIQUE KEY `uk_idvehiculo_doc` (`idvehiculo`),
  ADD KEY `fk_idordencompra_doc` (`idordencompra`);

--
-- Indices de la tabla `distritos`
--
ALTER TABLE `distritos`
  ADD PRIMARY KEY (`iddistrito`),
  ADD KEY `fk_idprovincia_dis` (`idprovincia`);

--
-- Indices de la tabla `entidadespago`
--
ALTER TABLE `entidadespago`
  ADD PRIMARY KEY (`identidadpago`),
  ADD UNIQUE KEY `uk_entidad_epg` (`entidad`);

--
-- Indices de la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`idgasto`),
  ADD KEY `fk_idvehiculo_gst` (`idvehiculo`);

--
-- Indices de la tabla `locales`
--
ALTER TABLE `locales`
  ADD PRIMARY KEY (`idlocal`),
  ADD KEY `fk_iddistrito_loc` (`iddistrito`),
  ADD KEY `fk_idmotorpark_loc` (`idmotorpark`);

--
-- Indices de la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`idmarca`),
  ADD UNIQUE KEY `uk_marca_mar` (`marca`);

--
-- Indices de la tabla `modelos`
--
ALTER TABLE `modelos`
  ADD PRIMARY KEY (`idmodelo`),
  ADD UNIQUE KEY `uk_modelo_mod` (`idmarca`,`modelo`,`anio`),
  ADD KEY `fk_idtipovehiculo_mod` (`idtipovehiculo`);

--
-- Indices de la tabla `motorpark`
--
ALTER TABLE `motorpark`
  ADD PRIMARY KEY (`idmotorpark`),
  ADD UNIQUE KEY `uk_ruc_mtp` (`ruc`);

--
-- Indices de la tabla `ordenescompra`
--
ALTER TABLE `ordenescompra`
  ADD PRIMARY KEY (`idordencompra`),
  ADD KEY `fk_idtienda_ocp` (`idtienda`),
  ADD KEY `fk_idlogistica_ocp` (`idlogistica`);

--
-- Indices de la tabla `personas`
--
ALTER TABLE `personas`
  ADD PRIMARY KEY (`idpersona`),
  ADD UNIQUE KEY `uk_nrodoc` (`tipodoc`,`nrodoc`),
  ADD KEY `fk_iddistrito_per` (`iddistrito`);

--
-- Indices de la tabla `provincias`
--
ALTER TABLE `provincias`
  ADD PRIMARY KEY (`idprovincia`),
  ADD KEY `uk_iddepartamento_pro` (`iddepartamento`);

--
-- Indices de la tabla `tiendas`
--
ALTER TABLE `tiendas`
  ADD PRIMARY KEY (`idtienda`),
  ADD KEY `fk_iddistrito_tnd` (`iddistrito`),
  ADD KEY `fk_idconcesionario_tnd` (`idconcesionario`);

--
-- Indices de la tabla `tipovehiculos`
--
ALTER TABLE `tipovehiculos`
  ADD PRIMARY KEY (`idtipovehiculo`),
  ADD UNIQUE KEY `uk_tipovehiculo_tve` (`tipovehiculo`);

--
-- Indices de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD PRIMARY KEY (`idvehiculo`),
  ADD KEY `fk_idcombustible_veh` (`idcombustible`),
  ADD KEY `fk_idlocal_veh` (`idlocal`),
  ADD KEY `fk_idlogistica_veh` (`idlogistica`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `amortizacionesoc`
--
ALTER TABLE `amortizacionesoc`
  MODIFY `idamortizacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `areas`
--
ALTER TABLE `areas`
  MODIFY `idarea` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `cargos`
--
ALTER TABLE `cargos`
  MODIFY `idcargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `colaboradores`
--
ALTER TABLE `colaboradores`
  MODIFY `idcolaborador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `combustibles`
--
ALTER TABLE `combustibles`
  MODIFY `idcombustible` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `idcompra` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `concesionarios`
--
ALTER TABLE `concesionarios`
  MODIFY `idconcesionario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `contratoslaborales`
--
ALTER TABLE `contratoslaborales`
  MODIFY `idcontratolaboral` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  MODIFY `iddepartamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `detordencompra`
--
ALTER TABLE `detordencompra`
  MODIFY `iddetordencompra` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `distritos`
--
ALTER TABLE `distritos`
  MODIFY `iddistrito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1875;

--
-- AUTO_INCREMENT de la tabla `entidadespago`
--
ALTER TABLE `entidadespago`
  MODIFY `identidadpago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `idgasto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `locales`
--
ALTER TABLE `locales`
  MODIFY `idlocal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `marcas`
--
ALTER TABLE `marcas`
  MODIFY `idmarca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `modelos`
--
ALTER TABLE `modelos`
  MODIFY `idmodelo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `motorpark`
--
ALTER TABLE `motorpark`
  MODIFY `idmotorpark` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ordenescompra`
--
ALTER TABLE `ordenescompra`
  MODIFY `idordencompra` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Será el número de orden de compra', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `personas`
--
ALTER TABLE `personas`
  MODIFY `idpersona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `provincias`
--
ALTER TABLE `provincias`
  MODIFY `idprovincia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=196;

--
-- AUTO_INCREMENT de la tabla `tiendas`
--
ALTER TABLE `tiendas`
  MODIFY `idtienda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `tipovehiculos`
--
ALTER TABLE `tipovehiculos`
  MODIFY `idtipovehiculo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  MODIFY `idvehiculo` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `amortizacionesoc`
--
ALTER TABLE `amortizacionesoc`
  ADD CONSTRAINT `fk_identidadpago_aoc` FOREIGN KEY (`identidadpago`) REFERENCES `entidadespago` (`identidadpago`),
  ADD CONSTRAINT `fk_idlogistica_aoc` FOREIGN KEY (`idlogistica`) REFERENCES `colaboradores` (`idcolaborador`),
  ADD CONSTRAINT `fk_idorden_aoc` FOREIGN KEY (`idorden`) REFERENCES `ordenescompra` (`idordencompra`);

--
-- Filtros para la tabla `cargos`
--
ALTER TABLE `cargos`
  ADD CONSTRAINT `fk_idarea_car` FOREIGN KEY (`idarea`) REFERENCES `areas` (`idarea`);

--
-- Filtros para la tabla `colaboradores`
--
ALTER TABLE `colaboradores`
  ADD CONSTRAINT `fk_idcontratolaboral_col` FOREIGN KEY (`idcontratolaboral`) REFERENCES `contratoslaborales` (`idcontratolaboral`);

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `fk_idlogistica_cmp` FOREIGN KEY (`idlogistica`) REFERENCES `colaboradores` (`idcolaborador`),
  ADD CONSTRAINT `fk_idorden_cmp` FOREIGN KEY (`idorden`) REFERENCES `ordenescompra` (`idordencompra`);

--
-- Filtros para la tabla `contratoslaborales`
--
ALTER TABLE `contratoslaborales`
  ADD CONSTRAINT `fk_idcargocla` FOREIGN KEY (`idcargo`) REFERENCES `cargos` (`idcargo`),
  ADD CONSTRAINT `fk_idpersona_cla` FOREIGN KEY (`idpersona`) REFERENCES `personas` (`idpersona`);

--
-- Filtros para la tabla `detordencompra`
--
ALTER TABLE `detordencompra`
  ADD CONSTRAINT `fk_idordencompra_doc` FOREIGN KEY (`idordencompra`) REFERENCES `ordenescompra` (`idordencompra`),
  ADD CONSTRAINT `fk_idvehiculo_doc` FOREIGN KEY (`idvehiculo`) REFERENCES `vehiculos` (`idvehiculo`);

--
-- Filtros para la tabla `distritos`
--
ALTER TABLE `distritos`
  ADD CONSTRAINT `fk_idprovincia_dis` FOREIGN KEY (`idprovincia`) REFERENCES `provincias` (`idprovincia`);

--
-- Filtros para la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD CONSTRAINT `fk_idvehiculo_gst` FOREIGN KEY (`idvehiculo`) REFERENCES `vehiculos` (`idvehiculo`);

--
-- Filtros para la tabla `locales`
--
ALTER TABLE `locales`
  ADD CONSTRAINT `fk_iddistrito_loc` FOREIGN KEY (`iddistrito`) REFERENCES `distritos` (`iddistrito`),
  ADD CONSTRAINT `fk_idmotorpark_loc` FOREIGN KEY (`idmotorpark`) REFERENCES `motorpark` (`idmotorpark`);

--
-- Filtros para la tabla `modelos`
--
ALTER TABLE `modelos`
  ADD CONSTRAINT `fk_idmarca_mod` FOREIGN KEY (`idmarca`) REFERENCES `marcas` (`idmarca`),
  ADD CONSTRAINT `fk_idtipovehiculo_mod` FOREIGN KEY (`idtipovehiculo`) REFERENCES `tipovehiculos` (`idtipovehiculo`);

--
-- Filtros para la tabla `ordenescompra`
--
ALTER TABLE `ordenescompra`
  ADD CONSTRAINT `fk_idlogistica_ocp` FOREIGN KEY (`idlogistica`) REFERENCES `colaboradores` (`idcolaborador`),
  ADD CONSTRAINT `fk_idtienda_ocp` FOREIGN KEY (`idtienda`) REFERENCES `tiendas` (`idtienda`);

--
-- Filtros para la tabla `personas`
--
ALTER TABLE `personas`
  ADD CONSTRAINT `fk_iddistrito_per` FOREIGN KEY (`iddistrito`) REFERENCES `distritos` (`iddistrito`);

--
-- Filtros para la tabla `provincias`
--
ALTER TABLE `provincias`
  ADD CONSTRAINT `uk_iddepartamento_pro` FOREIGN KEY (`iddepartamento`) REFERENCES `departamentos` (`iddepartamento`);

--
-- Filtros para la tabla `tiendas`
--
ALTER TABLE `tiendas`
  ADD CONSTRAINT `fk_idconcesionario_tnd` FOREIGN KEY (`idconcesionario`) REFERENCES `concesionarios` (`idconcesionario`),
  ADD CONSTRAINT `fk_iddistrito_tnd` FOREIGN KEY (`iddistrito`) REFERENCES `distritos` (`iddistrito`);

--
-- Filtros para la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD CONSTRAINT `fk_idcombustible_veh` FOREIGN KEY (`idcombustible`) REFERENCES `combustibles` (`idcombustible`),
  ADD CONSTRAINT `fk_idlocal_veh` FOREIGN KEY (`idlocal`) REFERENCES `locales` (`idlocal`),
  ADD CONSTRAINT `fk_idlogistica_veh` FOREIGN KEY (`idlogistica`) REFERENCES `colaboradores` (`idcolaborador`),
  ADD CONSTRAINT `fk_idmodelo_veh` FOREIGN KEY (`idvehiculo`) REFERENCES `modelos` (`idmodelo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
