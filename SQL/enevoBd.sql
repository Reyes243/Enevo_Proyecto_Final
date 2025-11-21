-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3308
-- Tiempo de generación: 21-11-2025 a las 20:11:11
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `monto` decimal(10,2) NOT NULL,
  `puntos_generados` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `niveles`
--

CREATE TABLE `niveles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `puntos_minimos` int(11) NOT NULL,
  `beneficios` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(14, 'Wesley', '$2y$10$qyNUTFc7wpxnsDvLaVAdlOq.g.ZoALC0Cu/FxYE27N59QhG41S9S2', 'we@gmail.com', 'cliente', '2025-11-20 23:55:34');

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
  ADD KEY `fk_compras_clientes` (`cliente_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `niveles`
--
ALTER TABLE `niveles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
  ADD CONSTRAINT `fk_compras_clientes` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;

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
