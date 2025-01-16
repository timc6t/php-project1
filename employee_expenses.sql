-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-01-2025 a las 22:34:10
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `employee_expenses`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `expenses`
--

CREATE TABLE `expenses` (
  `report_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `report_date` date DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','denied') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `expenses`
--

INSERT INTO `expenses` (`report_id`, `user_id`, `category`, `description`, `report_date`, `amount`, `created_at`, `status`) VALUES
(1, 2, 'test', 'testing', '0000-00-00', 9000.00, '0000-00-00 00:00:00', 'denied'),
(2, 3, 'test2', 'testicle', '2024-12-09', 4783.36, '2024-12-04 20:33:34', 'denied'),
(4, 1, 'Technology', 'TV', '2024-12-10', 4563.00, '2024-12-28 12:25:57', 'approved'),
(5, 4, 'Public transport card', 'Top-up for the public transport card.', '2024-12-29', 26.56, '2025-01-03 11:22:01', 'pending'),
(6, 6, 'Phone charger', 'Previously lost, new charger for the company phone.', '2025-01-24', 53.00, '2025-01-03 11:22:01', 'pending'),
(7, 2, 'Stationary', 'A pack of 100 sheets of paper for the printer', '2024-12-20', 4.00, '2025-01-03 11:26:36', 'pending'),
(8, 1, 'Tech', '1 optic fiber cable', '2025-01-06', 10.62, '2025-01-03 11:26:36', 'pending'),
(9, 6, 'Food', 'Breakfast at VIPS.', '2024-12-27', 7.56, '2025-01-03 11:29:14', 'pending'),
(10, 6, 'Entertainment', 'A 1 month subscription to Spotify', '2025-01-01', 10.00, '2025-01-03 11:29:14', 'pending'),
(11, 4, 'Cosmetics', 'Masculine perfume', '2024-12-24', 105.00, '2025-01-03 11:37:41', 'pending'),
(12, 4, 'Stationary', '4 Bic pens (blue, red, black, and green)', '2025-01-02', 1.82, '2025-01-03 11:37:41', 'approved'),
(13, 6, 'Company phone', 'iPhone 15 Pro Max 256 GB', '2024-08-02', 1452.95, '2024-12-28 12:41:09', 'approved'),
(14, 6, 'Public Transport Card', 'Recharge', '2024-11-29', 26.56, '2024-12-28 12:42:06', 'approved'),
(15, 3, 'Food', 'Fruits (Apples and bananas)', '2024-12-11', 11.55, '2024-12-28 12:45:15', 'pending'),
(16, 3, 'Electronics', 'TV/Monitor', '2025-01-09', 4000.00, '2025-01-03 10:41:18', 'pending');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_role` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`user_id`, `email`, `name`, `user_password`, `user_role`) VALUES
(1, 'it.support@company.com', 'Admin', '$2y$10$WgbWLRt8nAc120FHJ7qeXeFvmjwAI2h0iYGy6fuJVL0VTaWFtnKrG', 2),
(2, 'default_employee@company.com', 'John Doe', '$2y$10$q.qq//M2aKO.S0aGCFxqpOjqjf1BqC2iyIYdFglXNiKuWMsYZbiP6', 0),
(3, 'default_manager@company.com', 'Jane Doe', '$2y$10$0ABHX8a7ES.Xam5JLQjfwe5uKJXDSkRiE/XAyng5.JQ2KgTUqNA4u', 1),
(4, 'jose.hierro@company.com', 'José del Hierro', '$2y$10$dAgqDhCJwS0tBJa34GRAFOnZeKeulD131s/JxObsFadWG1Jpxrywi', 0),
(5, 'test2@it.company.com', 'Tester Dos', '$2y$10$yMXUB0oGqVUdnGZye.sdBOxHRHf/Sj/BjC7FH/96vRwNkbaLGWaea', 1),
(6, 'tim@company.com', 'Tim', '$2y$10$4BcXEJ91KiTWTrikSgSSy.HvHcGlqMF7outEReXtg46PJTDC.7DKK', 2),
(7, 'post_test@company.com', 'Poster', '$2y$10$PJrPBo3qoGjDQtHydxkojuE08oAkQ5IGtp5U/pHrFTi9ea3W0KLu.', 0),
(8, 'another_one@company.com', 'Another', '$2y$10$38jsGcjRe3G7P7Em.vPxT.ZH7KMzlqdFF.IyJy/i3lIARMGBp4OaG', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `expenses`
--
ALTER TABLE `expenses`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
