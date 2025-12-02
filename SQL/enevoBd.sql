-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3308
-- Tiempo de generación: 02-12-2025 a las 23:18:22
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
-- Base de datos: `enevo`
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
(8, 'Pedro Morales', 'pedro.morales@email.com', 4, 597, '2025-12-01 16:45:01', 21),
(9, 'Sofía Gutiérrez', 'sofia.gutierrez@email.com', 4, 800, '2025-12-01 16:45:01', 22),
(10, 'Jorge Mendoza', 'jorge.mendoza@email.com', 5, 1800, '2025-12-01 16:45:01', 23),
(11, 'Valeria Torres', 'valeria.torres@email.com', 6, 4200, '2025-12-01 16:45:01', 24),
(12, 'Ricardo Castillo', 'ricardo.castillo@email.com', 7, 7500, '2025-12-01 16:45:01', 25),
(13, 'María López', 'maria.lopez@email.com', 1, 250, '2025-12-02 22:17:37', 26),
(14, 'Carlos Ramírez', 'carlos.ramirez@email.com', 4, 750, '2025-12-02 22:17:37', 27),
(15, 'Ana Martínez', 'ana.martinez@email.com', 5, 2000, '2025-12-02 22:17:37', 28),
(16, 'Luis Fernández', 'luis.fernandez@email.com', 6, 4500, '2025-12-02 22:17:37', 29),
(17, 'Elena García', 'elena.garcia@email.com', 7, 8000, '2025-12-02 22:17:37', 30);

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
(1, 8, 3, 1, '2025-12-02 21:58:57', 999.00, 99),
(2, 8, 4, 2, '2025-12-02 22:00:31', 2198.00, 219),
(3, 8, 1, 1, '2025-12-02 22:11:24', 1299.00, 129),
(4, 13, 1, 1, '2025-11-15 17:30:00', 1299.00, 129),
(5, 13, 3, 1, '2025-11-20 21:15:00', 999.00, 99),
(6, 13, 5, 1, '2025-11-25 23:45:00', 599.00, 59),
(7, 14, 1, 2, '2025-11-10 16:00:00', 2598.00, 259),
(8, 14, 2, 2, '2025-11-12 18:30:00', 2398.00, 239),
(9, 14, 4, 1, '2025-11-18 22:20:00', 1099.00, 109),
(10, 14, 3, 1, '2025-11-22 17:45:00', 999.00, 99),
(11, 15, 1, 3, '2025-10-05 19:00:00', 3897.00, 389),
(12, 15, 2, 3, '2025-10-15 20:30:00', 3597.00, 359),
(13, 15, 3, 4, '2025-10-25 23:00:00', 3996.00, 399),
(14, 15, 4, 2, '2025-11-05 17:15:00', 2198.00, 219),
(15, 15, 5, 5, '2025-11-15 21:45:00', 2995.00, 299),
(16, 15, 1, 2, '2025-11-28 18:20:00', 2598.00, 259),
(17, 16, 1, 5, '2025-09-01 17:00:00', 6495.00, 649),
(18, 16, 2, 5, '2025-09-10 18:30:00', 5995.00, 599),
(19, 16, 3, 8, '2025-09-20 21:00:00', 7992.00, 799),
(20, 16, 4, 6, '2025-10-01 16:15:00', 6594.00, 659),
(21, 16, 5, 10, '2025-10-15 23:30:00', 5990.00, 599),
(22, 16, 1, 4, '2025-11-01 19:45:00', 5196.00, 519),
(23, 16, 2, 3, '2025-11-20 17:20:00', 3597.00, 359),
(24, 17, 1, 10, '2025-08-01 16:00:00', 12990.00, 1299),
(25, 17, 2, 8, '2025-08-15 17:30:00', 9592.00, 959),
(26, 17, 3, 10, '2025-09-01 18:00:00', 9990.00, 999),
(27, 17, 4, 10, '2025-09-20 20:45:00', 10990.00, 1099),
(28, 17, 5, 15, '2025-10-05 22:20:00', 8985.00, 898),
(29, 17, 1, 8, '2025-10-25 21:00:00', 10392.00, 1039),
(30, 17, 2, 5, '2025-11-10 23:30:00', 5995.00, 599),
(31, 17, 3, 6, '2025-11-25 19:15:00', 5994.00, 599);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `juegos`
--

INSERT INTO `juegos` (`id`, `nombre`, `descripcion`, `precio`, `genero`, `plataforma`, `fecha_creacion`) VALUES
(1, 'The Legend of Zelda: Tears of the Kingdom', 'Aventura épica en el reino de Hyrule', 1299.00, 'Aventura', 'Nintendo Switch', '2025-12-01 20:35:08'),
(2, 'Elden Ring', 'RPG de acción en un mundo abierto oscuro', 1199.00, 'RPG', 'PC/PS5/Xbox', '2025-12-01 20:35:08'),
(3, 'FIFA 24', 'Simulador de fútbol', 999.00, 'Deportes', 'Multi-plataforma', '2025-12-01 20:35:08'),
(4, 'Resident Evil 4 Remake', 'Survival horror reimaginado', 1099.00, 'Terror', 'PC/PS5/Xbox', '2025-12-01 20:35:08'),
(5, 'Minecraft', 'Construcción y aventura sandbox', 599.00, 'Sandbox', 'Multi-plataforma', '2025-12-01 20:35:08');

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
(4, 'Plata', 500, 5, '10% descuento'),
(5, 'Oro', 1500, 10, '15% descuento + prioridad'),
(6, 'Platino', 3000, 15, '20% descuento + soporte VIP'),
(7, 'Diamante', 6000, 20, '25% descuento + invitaciones VIP');

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
(15, 'DiegoEmiliano', '$2y$10$vgDfWzZOAXrA3i6osxlZj.Nl9VHpGy7qLWFFyIUEUcX6bNuxDKxRW', 'demi@gmail.com', 'admin', '2025-11-25 19:21:55'),
(21, 'pedro.morales', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pedro.morales@email.com', 'cliente', '2025-12-01 16:45:01'),
(22, 'sofia.gutierrez', '$2y$10$eJ5kL3mN9pQrS1tU5vW7xY.zAbCdEfGhIjKlMnOpQrStUvWxYz012', 'sofia.gutierrez@email.com', 'cliente', '2025-12-01 16:45:01'),
(23, 'jorge.mendoza', '$2y$10$fK6lM4nO0pQsT2uV6wX8yZ.aB1CdEfGhIjKlMnOpQrStUvWxYzA23', 'jorge.mendoza@email.com', 'cliente', '2025-12-01 16:45:01'),
(24, 'valeria.torres', '$2y$10$gL7mN5oP1qRtU3vW7xY9zA.bC2DdEfGhIjKlMnOpQrStUvWxYzB34', 'valeria.torres@email.com', 'cliente', '2025-12-01 16:45:01'),
(25, 'ricardo.castillo', '$2y$10$hM8nO6pQ2rStV4wX8yZ0aB.cD3EdEfGhIjKlMnOpQrStUvWxYzC45', 'ricardo.castillo@email.com', 'cliente', '2025-12-01 16:45:01'),
(26, 'maria.lopez', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'maria.lopez@email.com', 'cliente', '2025-12-02 22:17:37'),
(27, 'carlos.ramirez', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'carlos.ramirez@email.com', 'cliente', '2025-12-02 22:17:37'),
(28, 'ana.martinez', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ana.martinez@email.com', 'cliente', '2025-12-02 22:17:37'),
(29, 'luis.fernandez', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'luis.fernandez@email.com', 'cliente', '2025-12-02 22:17:37'),
(30, 'elena.garcia', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'elena.garcia@email.com', 'cliente', '2025-12-02 22:17:37');

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
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `juegos`
--
ALTER TABLE `juegos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `niveles`
--
ALTER TABLE `niveles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

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
