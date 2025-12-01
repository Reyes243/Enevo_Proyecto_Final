-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3308
-- Tiempo de generación: 01-12-2025 a las 21:35:22
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
(8, 'Pedro Morales', 'pedro.morales@email.com', 1, 150, '2025-12-01 16:45:01', 21),
(9, 'Sofía Gutiérrez', 'sofia.gutierrez@email.com', 4, 800, '2025-12-01 16:45:01', 22),
(10, 'Jorge Mendoza', 'jorge.mendoza@email.com', 5, 1800, '2025-12-01 16:45:01', 23),
(11, 'Valeria Torres', 'valeria.torres@email.com', 6, 4200, '2025-12-01 16:45:01', 24),
(12, 'Ricardo Castillo', 'ricardo.castillo@email.com', 7, 7500, '2025-12-01 16:45:01', 25);

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
(5, 'Oro', 1500, 15, '15% descuento + prioridad'),
(6, 'Platino', 3000, 30, '20% descuento + soporte VIP'),
(7, 'Diamante', 6000, 60, '25% descuento + invitaciones VIP');

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
(3, 'KAROL', '$2y$10$80mKX0rL/ORn969ajln8m.kNBlBCD1rwIU2qDUtPdFtfEQDiHjP0.', 'carolinaabaroa02@outlook.com', 'cliente', '2025-11-10 18:46:23'),
(4, 'Luquin', '$2y$10$j4/NEkxcOnleEwsRlRXk1uXKMy7uFK5r0Rz7knFPys2zLwbxHWAi2', 'demo@ejemplo.com', 'cliente', '2025-11-10 18:48:22'),
(5, 'Reyes', '$2y$10$9GCkUnnHLEtKwOKlHqygxORuybzr1b.rMVhFVU0ddSSJqakGXBm36', 'emilianoabaroa@gmail.com', 'cliente', '2025-11-10 19:07:01'),
(6, 'Reniery', '$2y$10$HwI22k3o06GcymrxItkLM.KtS3FlZ/8dhl6067q5Gfa/7LgBeb7Gi', 'reni@gmail.com', 'admin', '2025-11-12 22:30:29'),
(7, 'Diego', '$2y$10$JcYUuhJrW0apIa4tNryw5OEsRi8BXxJR5MLZC1TDnJj4JPI7kMt4a', 'die@gmail.com', 'cliente', '2025-11-18 00:07:52'),
(8, 'csc', '$2y$10$ACEhp1qxyuP5vMbQl66Mq.CQu/9AiFy9qLRWvF9tBiPftEF/oZ8UG', 'carolinaabaroa02@65.com', 'cliente', '2025-11-18 01:14:09'),
(10, 'Reni', '$2y$10$DOdk89oInq5gD1eQV8s20OBDhQ2Nr7lIiahq3Di4lZtuLlNygeDei', 're@gmail.com', 'admin', '2025-11-19 22:24:59'),
(11, 'Diego1', '$2y$10$rx6KRFvsx3kk2KQBi6PGC.RB8UF7JGztzdf8HS.mHwd4W9qFqfFyS', 'demo@ejemplo.coml', 'admin', '2025-11-20 22:03:02'),
(12, 'qqwqw', '$2y$10$YdmfClCs4gi11LuIcS0FhOraP5oAjAGGBl4XO5ePBm/4X1tVx74Ku', 'addad@ddfd.com', 'cliente', '2025-11-20 22:17:35'),
(13, 'Admin123', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@gmail.com', 'admin', '2025-11-20 22:49:42'),
(15, 'DiegoEmiliano', '$2y$10$vgDfWzZOAXrA3i6osxlZj.Nl9VHpGy7qLWFFyIUEUcX6bNuxDKxRW', 'demi@gmail.com', 'admin', '2025-11-25 19:21:55'),
(16, 'Wenseslao', '$2y$10$epgwZ0cpWbrSjP0kQbeGzeO3.RInQH9C.kZJRnxhk5Pu6gCVhY.dK', 'sistemas@gmail.com', 'cliente', '2025-11-26 18:18:25'),
(17, 'NahomiVildosola', '$2y$10$cumg0UntOvyfQGRxUiQJqORdT9d.gEmXTmfQEVu./xYYxolVggp.q', 'naho@gmail.com', 'cliente', '2025-11-30 20:12:30'),
(21, 'pedro.morales', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pedro.morales@email.com', 'cliente', '2025-12-01 16:45:01'),
(22, 'sofia.gutierrez', '$2y$10$eJ5kL3mN9pQrS1tU5vW7xY.zAbCdEfGhIjKlMnOpQrStUvWxYz012', 'sofia.gutierrez@email.com', 'cliente', '2025-12-01 16:45:01'),
(23, 'jorge.mendoza', '$2y$10$fK6lM4nO0pQsT2uV6wX8yZ.aB1CdEfGhIjKlMnOpQrStUvWxYzA23', 'jorge.mendoza@email.com', 'cliente', '2025-12-01 16:45:01'),
(24, 'valeria.torres', '$2y$10$gL7mN5oP1qRtU3vW7xY9zA.bC2DdEfGhIjKlMnOpQrStUvWxYzB34', 'valeria.torres@email.com', 'cliente', '2025-12-01 16:45:01'),
(25, 'ricardo.castillo', '$2y$10$hM8nO6pQ2rStV4wX8yZ0aB.cD3EdEfGhIjKlMnOpQrStUvWxYzC45', 'ricardo.castillo@email.com', 'cliente', '2025-12-01 16:45:01');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
