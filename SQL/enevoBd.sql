-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: sql100.infinityfree.com
-- Tiempo de generación: 08-12-2025 a las 14:35:09
-- Versión del servidor: 10.6.22-MariaDB
-- Versión de PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `if0_40360603_enevo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nivel_id` int(11) DEFAULT NULL,
  `puntos_acumulados` int(11) DEFAULT 0,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `email`, `nivel_id`, `puntos_acumulados`, `fecha_registro`, `usuario_id`) VALUES
(3, 'Pedro Morales', 'pedro.morales@email.com', 1, 461, '2025-12-01 17:18:25', 35),
(4, 'Sofía Gutiérrez', 'sofia.gutierrez@email.com', 4, 800, '2025-12-01 17:18:25', 36),
(5, 'Jorge Mendoza', 'jorge.mendoza@email.com', 5, 1800, '2025-12-01 17:18:25', 37),
(6, 'Valeria Torres', 'valeria.torres@email.com', 6, 4200, '2025-12-01 17:18:25', 38),
(13, 'Luis Ramírez', 'luis.ramirez@email.com', 1, 118, '2025-12-08 19:15:04', 47),
(14, 'Mariana Santos', 'mariana.santos@email.com', 4, 456, '2025-12-08 19:15:16', 48),
(15, 'Carlos Vargas', 'carlos.vargas@email.com', 5, 515, '2025-12-08 19:15:24', 49),
(16, 'Andrea Rojas', 'andrea.rojas@email.com', 6, 644, '2025-12-08 19:15:34', 50),
(17, 'Fernanda López', 'fernanda.lopez@email.com', 5, 347, '2025-12-08 19:15:43', 51),
(18, 'Ramón Cortés', 'ramon.cortes@email.com', 1, 158, '2025-12-08 19:16:02', 53);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `juego_id` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT 1,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `monto` decimal(10,2) NOT NULL,
  `puntos_generados` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id`, `cliente_id`, `juego_id`, `cantidad`, `fecha`, `monto`, `puntos_generados`) VALUES
(5, 3, 2, 1, '2025-12-07 20:32:31', '0.00', 0),
(6, 3, 1, 1, '2025-12-07 20:33:12', '1299.00', 129),
(9, 3, 2, 1, '2025-12-07 21:08:48', '0.00', 0),
(15, 3, 3, 1, '2025-12-08 18:55:48', '999.00', 99),
(16, 13, 5, 1, '2025-12-08 19:15:04', '199.00', 19),
(17, 13, 3, 1, '2025-12-08 19:15:04', '999.00', 99),
(18, 14, 2, 1, '2025-12-08 19:15:16', '1199.00', 119),
(19, 14, 1, 1, '2025-12-08 19:15:16', '1299.00', 129),
(20, 14, 4, 1, '2025-12-08 19:15:16', '1099.00', 109),
(21, 14, 3, 1, '2025-12-08 19:15:16', '999.00', 99),
(22, 15, 1, 1, '2025-12-08 19:15:24', '1299.00', 129),
(23, 15, 2, 1, '2025-12-08 19:15:24', '1199.00', 119),
(24, 15, 3, 1, '2025-12-08 19:15:24', '999.00', 99),
(25, 15, 4, 1, '2025-12-08 19:15:24', '1099.00', 109),
(26, 15, 5, 1, '2025-12-08 19:15:24', '599.00', 59),
(27, 16, 2, 1, '2025-12-08 19:15:34', '1199.00', 119),
(28, 16, 1, 1, '2025-12-08 19:15:34', '1299.00', 129),
(29, 16, 3, 1, '2025-12-08 19:15:34', '999.00', 99),
(30, 16, 4, 1, '2025-12-08 19:15:34', '1099.00', 109),
(31, 16, 5, 1, '2025-12-08 19:15:34', '599.00', 59),
(32, 16, 1, 1, '2025-12-08 19:15:34', '1299.00', 129),
(33, 17, 3, 1, '2025-12-08 19:15:43', '999.00', 99),
(34, 17, 1, 1, '2025-12-08 19:15:43', '1299.00', 129),
(35, 17, 2, 1, '2025-12-08 19:15:43', '1199.00', 119),
(36, 18, 5, 1, '2025-12-08 19:16:02', '599.00', 59),
(37, 18, 3, 1, '2025-12-08 19:16:02', '999.00', 99),
(38, 3, 1, 1, '2025-12-08 19:18:05', '1299.00', 129);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `juegos`
--

CREATE TABLE `juegos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `genero` varchar(50) DEFAULT NULL,
  `plataforma` varchar(50) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `juegos`
--

INSERT INTO `juegos` (`id`, `nombre`, `descripcion`, `precio`, `genero`, `plataforma`, `fecha_creacion`) VALUES
(1, 'The Legend of Zelda: Tears of the Kingdom', 'Aventura épica en el reino de Hyrule', '1299.00', 'Aventura', 'Nintendo Switch', '2025-12-02 04:35:08'),
(2, 'Elden Ring', 'RPG de acción en un mundo abierto oscuro', '1199.00', 'RPG', 'PC/PS5/Xbox', '2025-12-02 04:35:08'),
(3, 'FIFA 24', 'Simulador de fútbol', '999.00', 'Deportes', 'Multi-plataforma', '2025-12-02 04:35:08'),
(4, 'Resident Evil 4 Remake', 'Survival horror reimaginado', '1099.00', 'Terror', 'PC/PS5/Xbox', '2025-12-02 04:35:08'),
(5, 'Minecraft', 'Construcción y aventura sandbox', '599.00', 'Sandbox', 'Multi-plataforma', '2025-12-02 04:35:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `niveles`
--

CREATE TABLE `niveles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `puntos_minimos` int(11) NOT NULL,
  `compras_necesarias` int(11) NOT NULL DEFAULT 0,
  `beneficios` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `niveles`
--

INSERT INTO `niveles` (`id`, `nombre`, `puntos_minimos`, `compras_necesarias`, `beneficios`) VALUES
(1, 'Bronce', 100, 1, 'El mejor nivel para iniciar en la plataforma'),
(4, 'Plata', 500, 5, 'A un paso del Oro'),
(5, 'Oro', 1500, 10, 'Prioridad'),
(6, 'Platino', 3000, 15, 'Soporte VIP');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recompensas`
--

CREATE TABLE `recompensas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `costo_puntos` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recompensas_canjeadas`
--

CREATE TABLE `recompensas_canjeadas` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `recompensa_id` int(11) NOT NULL,
  `fecha_canjeo` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `rol` varchar(50) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password_hash`, `email`, `rol`, `fecha_creacion`) VALUES
(30, 'Admin1', '$2y$10$7wFY2ZVhcrswVEfjhewwBuqewoWo3nm96qf9cry.bTDSKbSweQhDS', 'admin@gmail.com', 'admin', '2025-11-26 18:43:35'),
(35, 'pedro.morales', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pedro.morales@email.com', 'cliente', '2025-12-01 17:18:25'),
(36, 'sofia.gutierrez', '$2y$10$eJ5kL3mN9pQrS1tU5vW7xY.zAbCdEfGhIjKlMnOpQrStUvWxYz012', 'sofia.gutierrez@email.com', 'cliente', '2025-12-01 17:18:25'),
(37, 'jorge.mendoza', '$2y$10$fK6lM4nO0pQsT2uV6wX8yZ.aB1CdEfGhIjKlMnOpQrStUvWxYzA23', 'jorge.mendoza@email.com', 'cliente', '2025-12-01 17:18:25'),
(38, 'valeria.torres', '$2y$10$gL7mN5oP1qRtU3vW7xY9zA.bC2DdEfGhIjKlMnOpQrStUvWxYzB34', 'valeria.torres@email.com', 'cliente', '2025-12-01 17:18:25'),
(47, 'luis.ramirez', '$2y$10$abcdefghijklmnopqrstuv1234567890hash', 'luis.ramirez@email.com', 'cliente', '2025-12-08 19:15:04'),
(48, 'mariana.santos', '$2y$10$abcdefgh1234567890mnopqrstuvHASH2', 'mariana.santos@email.com', 'cliente', '2025-12-08 19:15:16'),
(49, 'carlos.vargas', '$2y$10$ABCDEFGH901234567890MNOPQRSTUVhash3', 'carlos.vargas@email.com', 'cliente', '2025-12-08 19:15:24'),
(50, 'andrea.rojas', '$2y$10$HASH12345ABCDEFGHIJKLMNOpqrstuvwxy4', 'andrea.rojas@email.com', 'cliente', '2025-12-08 19:15:34'),
(51, 'fernanda.lopez', '$2y$10$HASHABCDE1234567890QRSTUVhash5', 'fernanda.lopez@email.com', 'cliente', '2025-12-08 19:15:43'),
(53, 'ramon.cortes', '$2y$10$HASH123901283ASDASDASDasd6', 'ramon.cortes@email.com', 'cliente', '2025-12-08 19:16:02');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`),
  ADD KEY `fk_clientes_niveles` (`nivel_id`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_compras_clientes` (`cliente_id`),
  ADD KEY `fk_compras_juegos` (`juego_id`);

--
-- Indices de la tabla `juegos`
--
ALTER TABLE `juegos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_genero` (`genero`),
  ADD KEY `idx_plataforma` (`plataforma`),
  ADD KEY `idx_precio` (`precio`);

--
-- Indices de la tabla `niveles`
--
ALTER TABLE `niveles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `recompensas`
--
ALTER TABLE `recompensas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `recompensas_canjeadas`
--
ALTER TABLE `recompensas_canjeadas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_canjeo_cliente` (`cliente_id`),
  ADD KEY `fk_canjeo_recompensa` (`recompensa_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre_usuario` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `juegos`
--
ALTER TABLE `juegos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `niveles`
--
ALTER TABLE `niveles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `recompensas`
--
ALTER TABLE `recompensas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recompensas_canjeadas`
--
ALTER TABLE `recompensas_canjeadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `fk_clientes_niveles` FOREIGN KEY (`nivel_id`) REFERENCES `niveles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_clientes_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `fk_compras_clientes` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_compras_juegos` FOREIGN KEY (`juego_id`) REFERENCES `juegos` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `recompensas_canjeadas`
--
ALTER TABLE `recompensas_canjeadas`
  ADD CONSTRAINT `fk_canjeo_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_canjeo_recompensa` FOREIGN KEY (`recompensa_id`) REFERENCES `recompensas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
