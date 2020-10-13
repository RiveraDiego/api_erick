-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-10-2020 a las 02:13:29
-- Versión del servidor: 10.4.14-MariaDB
-- Versión de PHP: 7.2.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bdeventos`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_EventoActivar` (`idEvento` INT)  UPDATE eventos SET ev_Estado = 'A' WHERE ev_id = idEvento$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_EventoCancelar` (`idEvento` INT)  UPDATE eventos SET ev_Estado = 'C' WHERE ev_id = idEvento$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_EventoCrear` (IN `nombreEvento` VARCHAR(100), IN `fechaEvento` DATE, IN `lugarEvento` VARCHAR(500), IN `HoraInicio` TIME, IN `HoraFin` TIME, IN `descripcion` VARCHAR(500), IN `idUsuario` INT)  INSERT INTO eventos(ev_nombreEvento,ev_fechaEvento,ev_lugarEvento,ev_HoraInicio,ev_HoraFin,ev_descripcion,p_id)
VALUES (
        nombreEvento,
        fechaEvento,
        lugarEvento,
        HoraInicio,
        HoraFin,
        descripcion,
        idUsuario)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_EventoDesuscribir` (`idSucripcion` INT)  UPDATE suscripciones SET sc_estado = 'D' WHERE sc_id = idSucripcion$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_EventoEditar` (IN `nombreEvento` VARCHAR(100), IN `fechaEvento` DATE, IN `lugarEvento` VARCHAR(500), IN `HoraInicio` TIME, IN `HoraFin` TIME, IN `descripcion` VARCHAR(500), IN `idEvento` INT)  UPDATE eventos SET 
ev_nombreEvento = nombreEvento,
ev_fechaEvento = fechaEvento,
ev_lugarEvento = lugarEvento,
ev_HoraInicio = HoraInicio,
ev_HoraFin = HoraFin,
ev_descripcion = descripcion
WHERE ev_id = idEvento$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_EventoEliminar` (`idEvento` INT)  DELETE FROM eventos WHERE ev_id = idEvento$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_EventoListarByID` (`idEvento` INT)  SELECT * FROM `eventos` where ev_id = idEvento$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_EventoListarByUsuario` (`idUsuario` INT)  SELECT * FROM `eventos` where p_id = idUsuario$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_EventoListarTodos` ()  SELECT * FROM `eventos`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_EventoSuscribir` (`idPersona` INT, `idEvento` INT)  INSERT INTO suscripciones(p_id, ev_id) 
VALUES (idPersona, idEvento)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_Login` (`usuario` VARCHAR(100), `pass` VARCHAR(100))  SELECT * FROM personas WHERE ((p_usuario = usuario) and (p_pass = pass))$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_UsuarioActivarCuentaByCorreo` (`correo` VARCHAR(50))  UPDATE personas SET p_estado = "A" WHERE p_correo = correo$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_UsuarioActivarCuentaByID` (`id` INT)  UPDATE personas SET p_estado = "A" WHERE p_id  = id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_UsuarioActivarCuentaByTelefono` (IN `telefono` VARCHAR(25))  UPDATE personas SET p_estado = "A" WHERE p_telefono  = telefono$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_UsuarioEditar` (`nombre` VARCHAR(100), `apellido` VARCHAR(100), `fecha_nac` DATE, `genero` CHAR(1), `correo` VARCHAR(50), `telefono` VARCHAR(25), `pass` VARCHAR(100), `id` INT(11))  UPDATE personas SET p_nombre = nombre, p_apellido = apellido, p_fecha_nac = fecha_nac, p_genero = genero, p_correo = correo, p_telefono = telefono, p_pass = pass WHERE p_id = id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_UsuarioEliminar` (`id` INT)  DELETE FROM personas WHERE p_id = id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_UsuarioListarByCorreo` (`correo` VARCHAR(50))  SELECT * FROM personas WHERE p_correo = correo$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_UsuarioListarByID` (`id` INT(11))  SELECT * FROM personas WHERE p_id = id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_UsuarioListarByTelefono` (`telefono` VARCHAR(25))  SELECT * FROM personas WHERE p_telefono = telefono$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_UsuarioListarByUsuario` (`usuario` VARCHAR(100))  SELECT * FROM personas WHERE p_usuario = usuario$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_UsuarioListarTodos` ()  SELECT * FROM personas$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_UsuarioRegistro` (IN `nombre` VARCHAR(100), IN `apellido` VARCHAR(100), IN `fecha_nac` DATE, IN `genero` CHAR(1), IN `correo` VARCHAR(50), IN `telefono` VARCHAR(25), IN `usuario` VARCHAR(100), IN `pass` VARCHAR(100))  INSERT INTO personas (p_nombre, p_apellido, p_fecha_nac, p_genero, p_correo, p_telefono, p_usuario, p_pass) 
VALUES (nombre, apellido, fecha_nac, genero, correo, telefono, usuario, pass)$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `ev_id` int(11) NOT NULL COMMENT 'Es unico y autoincrementable',
  `ev_nombreEvento` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Nombre del Evento',
  `ev_fechaEvento` date NOT NULL COMMENT 'Fecha en la que se llevará a cabo el evento',
  `ev_lugarEvento` varchar(500) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Lugar en donde se realizará el evento',
  `ev_HoraInicio` time DEFAULT NULL COMMENT 'Hora de inicio del evento',
  `ev_HoraFin` time DEFAULT NULL,
  `ev_descripcion` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Descripción del evento',
  `ev_Estado` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A' COMMENT 'Estado del evento.\r\nA=Activo.\r\nC=Cancelado.\r\nR=Realizado.\r\n',
  `ev_FechaCreacion` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha y hora de la creación del evento',
  `ev_FechaModificacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Última fecha y hora de modificación del evento',
  `p_id` int(11) NOT NULL COMMENT 'Llave foránea, id del usuario que creó el evento'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`ev_id`, `ev_nombreEvento`, `ev_fechaEvento`, `ev_lugarEvento`, `ev_HoraInicio`, `ev_HoraFin`, `ev_descripcion`, `ev_Estado`, `ev_FechaCreacion`, `ev_FechaModificacion`, `p_id`) VALUES
(2, 'Ver las estrellas', '2020-10-09', 'San Salvador', '14:00:00', '17:00:00', 'Ojala no llueva', 'A', '2020-10-01 01:32:28', '2020-10-12 17:25:44', 74),
(3, 'Liberacion Tortugas', '2020-10-25', 'Playa', '10:00:00', '15:00:00', 'traigan Palas', 'A', '2020-10-01 01:56:40', '2020-10-01 01:56:40', 4),
(4, 'ver las estrellas', '2020-12-31', 'Res Libertad', '00:00:00', '02:00:00', 'Ojala no llueva', 'A', '2020-10-01 01:57:37', '2020-10-01 02:11:15', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personas`
--

CREATE TABLE `personas` (
  `p_id` int(11) NOT NULL COMMENT 'Id de la persona',
  `p_nombre` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Nombre de la pesona',
  `p_apellido` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Apellido de la persona',
  `p_fecha_nac` date NOT NULL COMMENT 'Fecha de nacimiento de la persona',
  `p_genero` char(1) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Género de la persona. M=Masculino, F=Femenino',
  `p_correo` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Correo del usuario',
  `p_telefono` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Número de telefono del usuario',
  `p_usuario` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Usuario para iniciar sesión',
  `p_pass` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Contraseña para iniciar sesión',
  `p_fechaCreacion` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de creación de la cuenta. ',
  `p_fechaModificacion` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Ultima fecha de modificación del perfil',
  `p_estado` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'P' COMMENT 'Estado de la cuenta.\r\nA=Activo\r\nI=Inactivo\r\nP=Pendiente de Verificacion'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `personas`
--

INSERT INTO `personas` (`p_id`, `p_nombre`, `p_apellido`, `p_fecha_nac`, `p_genero`, `p_correo`, `p_telefono`, `p_usuario`, `p_pass`, `p_fechaCreacion`, `p_fechaModificacion`, `p_estado`) VALUES
(2, 'Erick', 'Cruz', '1995-01-01', 'M', 'Correomodificado@gmailcom', '12345678', 'susy', 'minuevaPass', '2020-09-29 01:02:14', '2020-09-29 02:04:26', 'A'),
(3, 'Susy', 'Hernandez', '1995-05-22', 'M', 'erick.cruz.dev@gmail.com', '70013052', 'Test', 'susy123', '2020-09-29 01:02:14', '2020-09-29 02:04:26', 'A'),
(4, 'Diego', 'Menendez', '1995-05-09', 'M', 'diego@gmail.com', '75848524', 'Diegouser', 'DiegoPass', '2020-09-29 01:26:54', '2020-09-29 02:04:26', 'A'),
(26, 'Diego 2', 'Rivera 2', '1995-07-09', 'M', 'dmenendez3075@gmail.com', '76037413', 'Diegouser', '416824e0bc19c81a6b0ed96ac45846cb', '2020-10-08 00:24:38', '2020-10-08 00:24:38', 'P'),
(33, 'Erick from Postman', 'Cruz', '1995-05-22', 'M', 'die.menen@gmail.com', '50370013052', 'ecruz', 'f7752d71d859b8b207b66a908d037fdf', '2020-10-12 18:09:52', '2020-10-12 18:09:52', 'P'),
(74, 'Erick from Postman', 'Cruz', '1995-05-22', 'M', 'ddsdie.menen@gmail.com', '50370013052', 'ecruz', 'f7752d71d859b8b207b66a908d037fdf', '2020-10-12 16:34:39', '2020-10-12 16:34:39', 'P');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suscripciones`
--

CREATE TABLE `suscripciones` (
  `sc_id` int(11) NOT NULL COMMENT 'Id de la suscripción al evento',
  `p_id` int(11) NOT NULL COMMENT 'ID de la persona que se suscribe al evento',
  `ev_id` int(11) NOT NULL COMMENT 'ID del evento al cual se suscriben',
  `sc_fechaCreacion` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha en el cual se suscribieron al evento',
  `sc_fechaModificacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha en la cual se modifica la suscripción',
  `sc_estado` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'S' COMMENT 'Estado de la suscripcion.\r\nS=Suscrito.\r\nD=Desuscrito'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `suscripciones`
--

INSERT INTO `suscripciones` (`sc_id`, `p_id`, `ev_id`, `sc_fechaCreacion`, `sc_fechaModificacion`, `sc_estado`) VALUES
(1, 2, 2, '2020-10-01 01:44:24', '2020-10-12 17:47:42', 'D'),
(2, 4, 2, '2020-10-01 01:47:48', '2020-10-01 01:51:05', 'D'),
(3, 2, 4, '2020-10-12 18:10:35', '2020-10-12 18:10:35', 'S'),
(4, 2, 4, '2020-10-12 18:10:55', '2020-10-12 18:10:55', 'S'),
(5, 2, 4, '2020-10-12 18:11:54', '2020-10-12 18:11:54', 'S');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_tokens`
--

CREATE TABLE `user_tokens` (
  `id` int(11) NOT NULL,
  `token` longtext NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `end_date` datetime NOT NULL DEFAULT current_timestamp(),
  `p_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `user_tokens`
--

INSERT INTO `user_tokens` (`id`, `token`, `created_date`, `end_date`, `p_id`) VALUES
(1, 'adasdasdasdasdasdasdasd', '2020-10-07 13:25:42', '2020-10-12 12:12:31', 4),
(11, '541d7f02306f41b50d8853a0bb2add26', '2020-10-12 22:20:19', '2020-10-15 22:20:19', 33),
(12, '20b59d6d32d50f1a6d486a00a03ebb68', '2020-10-13 00:34:40', '2020-10-16 00:34:40', 74);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`ev_id`),
  ADD KEY `p_id` (`p_id`);

--
-- Indices de la tabla `personas`
--
ALTER TABLE `personas`
  ADD PRIMARY KEY (`p_id`),
  ADD UNIQUE KEY `p_correo` (`p_correo`);

--
-- Indices de la tabla `suscripciones`
--
ALTER TABLE `suscripciones`
  ADD PRIMARY KEY (`sc_id`),
  ADD KEY `p_id` (`p_id`),
  ADD KEY `ev_id` (`ev_id`);

--
-- Indices de la tabla `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `p_id` (`p_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `ev_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Es unico y autoincrementable', AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `personas`
--
ALTER TABLE `personas`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id de la persona', AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT de la tabla `suscripciones`
--
ALTER TABLE `suscripciones`
  MODIFY `sc_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id de la suscripción al evento', AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `user_tokens`
--
ALTER TABLE `user_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `eventos_ibfk_1` FOREIGN KEY (`p_id`) REFERENCES `personas` (`p_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `suscripciones`
--
ALTER TABLE `suscripciones`
  ADD CONSTRAINT `suscripciones_ibfk_1` FOREIGN KEY (`ev_id`) REFERENCES `eventos` (`ev_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `suscripciones_ibfk_2` FOREIGN KEY (`p_id`) REFERENCES `personas` (`p_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`p_id`) REFERENCES `personas` (`p_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
