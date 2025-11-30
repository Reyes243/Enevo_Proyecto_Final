-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3308
-- Tiempo de generación: 30-11-2025 a las 21:25:48
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

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `procesar_compra` (IN `p_cliente_id` INT, IN `p_monto` DECIMAL(10,2))   BEGIN
  DECLARE v_puntos INT;
  DECLARE v_total_puntos INT;
  DECLARE v_nivel_id INT;

  -- regla de negocio: 1 punto por cada unidad monetaria (ajusta según tu política)
  SET v_puntos = FLOOR(p_monto);

  -- insertar la compra
  INSERT INTO compras (cliente_id, fecha, monto, puntos_generados)
  VALUES (p_cliente_id, CURRENT_TIMESTAMP(), p_monto, v_puntos);

  -- sumar puntos al cliente
  UPDATE clientes
    SET puntos_acumulados = COALESCE(puntos_acumulados,0) + v_puntos
    WHERE id = p_cliente_id;

  -- obtener puntos totales actualizados
  SELECT puntos_acumulados INTO v_total_puntos FROM clientes WHERE id = p_cliente_id;

  -- determinar el nivel correspondiente: el nivel con mayor puntos_minimos <= puntos_totales
  SELECT id INTO v_nivel_id
    FROM niveles
    WHERE puntos_minimos <= v_total_puntos
    ORDER BY puntos_minimos DESC
    LIMIT 1;

  -- actualizar el nivel si corresponde
  IF v_nivel_id IS NOT NULL THEN
    UPDATE clientes SET nivel_id = v_nivel_id WHERE id = p_cliente_id;
  END IF;
END$$

DELIMITER ;

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

--
-- Disparadores `compras`
--
DELIMITER $$
CREATE TRIGGER `trg_after_insert_compra` AFTER INSERT ON `compras` FOR EACH ROW BEGIN
  DECLARE v_total_puntos INT;
  DECLARE v_nivel_id INT;

  -- sumar puntos generados por la compra al cliente
  UPDATE clientes
    SET puntos_acumulados = COALESCE(puntos_acumulados,0) + COALESCE(NEW.puntos_generados,0)
    WHERE id = NEW.cliente_id;

  -- obtener puntos totales
  SELECT puntos_acumulados INTO v_total_puntos FROM clientes WHERE id = NEW.cliente_id;

  -- determinar nivel correcto
  SELECT id INTO v_nivel_id
    FROM niveles
    WHERE puntos_minimos <= v_total_puntos
    ORDER BY puntos_minimos DESC
    LIMIT 1;

  IF v_nivel_id IS NOT NULL THEN
    UPDATE clientes SET nivel_id = v_nivel_id WHERE id = NEW.cliente_id;
  END IF;
END
$$
DELIMITER ;

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

--
-- Disparadores `recompensas_canjeadas`
--
DELIMITER $$
CREATE TRIGGER `trg_before_recompensa_canjeada` BEFORE INSERT ON `recompensas_canjeadas` FOR EACH ROW BEGIN
  DECLARE v_costo INT;
  DECLARE v_puntos INT;

  SELECT costo_puntos INTO v_costo FROM recompensas WHERE id = NEW.recompensa_id;
  SELECT COALESCE(puntos_acumulados,0) INTO v_puntos FROM clientes WHERE id = NEW.cliente_id;

  IF v_puntos < v_costo THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No tiene puntos suficientes para este canje.';
  ELSE
    -- resta puntos (se realiza antes del INSERT para asegurar atomicidad)
    UPDATE clientes
      SET puntos_acumulados = puntos_acumulados - v_costo
      WHERE id = NEW.cliente_id;
  END IF;
END
$$
DELIMITER ;

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
(14, 'Wesley', '$2y$10$qyNUTFc7wpxnsDvLaVAdlOq.g.ZoALC0Cu/FxYE27N59QhG41S9S2', 'we@gmail.com', 'cliente', '2025-11-20 23:55:34'),
(15, 'DiegoEmiliano', '$2y$10$vgDfWzZOAXrA3i6osxlZj.Nl9VHpGy7qLWFFyIUEUcX6bNuxDKxRW', 'demi@gmail.com', 'admin', '2025-11-25 19:21:55'),
(16, 'Wenseslao', '$2y$10$epgwZ0cpWbrSjP0kQbeGzeO3.RInQH9C.kZJRnxhk5Pu6gCVhY.dK', 'sistemas@gmail.com', 'cliente', '2025-11-26 18:18:25'),
(17, 'NahomiVildosola', '$2y$10$cumg0UntOvyfQGRxUiQJqORdT9d.gEmXTmfQEVu./xYYxolVggp.q', 'naho@gmail.com', 'cliente', '2025-11-30 20:12:30');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_resumen_niveles`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_resumen_niveles` (
`nivel_id` int(11)
,`nivel_nombre` varchar(50)
,`clientes_count` bigint(21)
,`puntos_totales` decimal(32,0)
,`monto_total_compras` decimal(54,2)
,`promedio_monto_por_cliente` decimal(33,2)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `v_resumen_niveles`
--
DROP TABLE IF EXISTS `v_resumen_niveles`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_resumen_niveles`  AS SELECT `n`.`id` AS `nivel_id`, `n`.`nombre` AS `nivel_nombre`, count(`c`.`id`) AS `clientes_count`, coalesce(sum(`c`.`puntos_acumulados`),0) AS `puntos_totales`, coalesce(sum(`comp`.`total_monto`),0) AS `monto_total_compras`, coalesce(round(`comp`.`total_monto` / nullif(count(`c`.`id`),0),2),0) AS `promedio_monto_por_cliente` FROM ((`niveles` `n` left join `clientes` `c` on(`c`.`nivel_id` = `n`.`id`)) left join (select `compras`.`cliente_id` AS `cliente_id`,sum(`compras`.`monto`) AS `total_monto` from `compras` group by `compras`.`cliente_id`) `comp` on(`comp`.`cliente_id` = `c`.`id`)) GROUP BY `n`.`id`, `n`.`nombre` ;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
