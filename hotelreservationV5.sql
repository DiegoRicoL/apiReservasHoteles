-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-12-2023 a las 17:35:02
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
-- Base de datos: `hotelreservation`
--
CREATE DATABASE IF NOT EXISTS `hotelreservation` DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci;
USE `hotelreservation`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `Habitacion` int(11) DEFAULT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Apellidos` varchar(255) NOT NULL,
  `numTLF` int(11) NOT NULL,
  `mail` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `habitaciones`
--

DROP TABLE IF EXISTS `habitaciones`;
CREATE TABLE `habitaciones` (
  `id` int(11) NOT NULL,
  `Hotel` int(11) NOT NULL,
  `Tipo` enum('Individual','Doble','Triple','Quad','Queen','King','Duplex','Doble-doble','Estudio','Suite') NOT NULL,
  `Camas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `habitaciones`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hoteles`
--

DROP TABLE IF EXISTS `hoteles`;
CREATE TABLE `hoteles` (
  `id` int(11) NOT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Valoracion` float NOT NULL DEFAULT 0,
  `Ubicacion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
ALTER TABLE `hoteles` ADD UNIQUE(`Nombre`);

--
-- Volcado de datos para la tabla `hoteles`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opinion`
--

DROP TABLE IF EXISTS `opinion`;
CREATE TABLE `opinion` (
  `id` int(11) NOT NULL,
  `Cliente` int(11) NOT NULL,
  `Habitacion` int(11) NOT NULL,
  `Nota` float NOT NULL,
  `Texto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

DROP TABLE IF EXISTS `reservas`;
CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `Cliente` int(11) NOT NULL,
  `Habitacion` int(11) NOT NULL,
  `FDesde` date NOT NULL,
  `FHasta` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `Nombre` varchar(255) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `Admin` tinyint(1) NOT NULL DEFAULT 0,
  `Cliente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
ALTER TABLE `usuarios` ADD UNIQUE(`Nombre`);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `HabitacionFK` (`Habitacion`);

--
-- Indices de la tabla `habitaciones`
--
ALTER TABLE `habitaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `HotelFK` (`Hotel`);

--
-- Indices de la tabla `hoteles`
--
ALTER TABLE `hoteles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opinion`
--
ALTER TABLE `opinion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ClienteFK` (`Cliente`),
  ADD KEY `HabitacionOpFK` (`Habitacion`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ClienteReservaFK` (`Cliente`),
  ADD KEY `HabitacionReservaFK` (`Habitacion`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UsuariosFK` (`Cliente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `habitaciones`
--
ALTER TABLE `habitaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `hoteles`
--
ALTER TABLE `hoteles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opinion`
--
ALTER TABLE `opinion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `HabitacionFK` FOREIGN KEY (`Habitacion`) REFERENCES `habitaciones` (`id`);

--
-- Filtros para la tabla `habitaciones`
--
ALTER TABLE `habitaciones`
  ADD CONSTRAINT `HotelFK` FOREIGN KEY (`Hotel`) REFERENCES `hoteles` (`id`);

--
-- Filtros para la tabla `opinion`
--
ALTER TABLE `opinion`
  ADD CONSTRAINT `ClienteFK` FOREIGN KEY (`Cliente`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `HabitacionOpFK` FOREIGN KEY (`Habitacion`) REFERENCES `habitaciones` (`id`);

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `ClienteReservaFK` FOREIGN KEY (`Cliente`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `HabitacionReservaFK` FOREIGN KEY (`Habitacion`) REFERENCES `habitaciones` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `UsuariosFK` FOREIGN KEY (`Cliente`) REFERENCES `clientes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

INSERT INTO `hoteles` (`id`, `Nombre`, `Valoracion`, `Ubicacion`) VALUES (NULL, 'OnlyYou', '7', 'Valencia');
INSERT INTO `habitaciones` (`id`, `Hotel`, `Tipo`, `Camas`) VALUES (NULL, '1', 'Individual', '1');
INSERT INTO `habitaciones` (`id`, `Hotel`, `Tipo`, `Camas`) VALUES (NULL, '1', 'Individual', '1');
INSERT INTO `clientes` (`id`, `Habitacion`, `Nombre`, `Apellidos`, `numTLF`, `mail`) VALUES (NULL, '1', 'AdminPacoSi', 'paquito', '53534543', 'paquito@gmail.com');
INSERT INTO `usuarios` (`id`, `Nombre`, `contrasena`, `Admin`, `Cliente`) VALUES (NULL, 'PaquitoAdmin', 'paquito', '1', '1');
INSERT INTO `clientes` (`id`, `Habitacion`, `Nombre`, `Apellidos`, `numTLF`, `mail`) VALUES (NULL, '2', 'NoAdminJose', 'Josete', '645646', 'jose@gmail.com');
INSERT INTO `usuarios` (`id`, `Nombre`, `contrasena`, `Admin`, `Cliente`) VALUES (NULL, 'JoseNoAdmin', 'josete', '0', '2');
INSERT INTO `reservas` (`id`, `Cliente`, `Habitacion`, `FDesde`, `FHasta`) VALUES (NULL, '2', '2', '2023-12-20', '2023-12-21');
INSERT INTO `opinion` (`id`, `Cliente`, `Habitacion`, `Nota`, `Texto`) VALUES (NULL, '2', '2', '6', 'No me gusta muchisimo, vaya caca');
