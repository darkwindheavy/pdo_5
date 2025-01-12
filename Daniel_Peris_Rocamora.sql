-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-12-2024 a las 23:51:21
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
-- Base de datos: `clientes_pdo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articulos`
--

CREATE TABLE `articulos` (
  `id` int(11) NOT NULL,
  `codigo` varchar(8) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `articulos`
--

INSERT INTO `articulos` (`id`, `codigo`, `nombre`, `descripcion`, `categoria`, `precio`, `imagen`) VALUES
(2, 'CCC45233', 'Pantalon', 'Termicos para nieve', 'Ropa técnica', 120.50, '67536595433be_67524ea2cc4aa_Foto_perfil.jpg'),
(3, 'HHH12345', 'bermuda', 'playa estampado', 'verano', 10.00, '67537fafe5fd6_67524ea2cc4aa_Foto_perfil.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `dni` varchar(9) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('usuario','administrador','editor') NOT NULL DEFAULT 'usuario',
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `localidad` varchar(100) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `dni`, `nombre`, `correo`, `contrasena`, `rol`, `telefono`, `direccion`, `localidad`, `provincia`, `creado_en`) VALUES
(39, '94818448M', 'Victor', 'vi@vi.com', '$2y$10$rCY53nj4xd2B9fA6woXfe.qKgHK9uNGwKiI4Pyv.Je0pJNSVVWzba', 'editor', '777898989', 'calle calvario 14', 'santa pola', 'Alicante', '2024-12-05 21:04:49'),
(40, '62442129G', 'Alis', 'alis@lais.com', '$2y$10$.a9DwzybvNQ7rL7rhe05jOeTZCPbYNk72nPLDlkrQcn91XlLg1cAC', 'editor', '123123123', 'tresmall 16', 'Altet', 'Alicante', '2024-12-05 21:55:43'),
(41, '23060568D', 'Alison', 'alison@lais.com', '$2y$10$Sddde0uF13ru.rD8w8LEm.kgHk9Z3EjMFMBVzAwdXdP4x/RFZB.ae', 'usuario', '123123321', 'tresmall 16', 'Altet', 'Alicante', '2024-12-05 21:56:58'),
(45, '74224456Y', 'Daniel', 'ddd@d.com', '$2y$10$DwBkGsim5tekQjl8qpMLFe1ATTVS8B/txf98f76AFWlIZON8jKLzS', 'administrador', '444565657', 'calle calvario 55', 'alicante', 'Alicante', '2024-12-06 00:30:55'),
(46, '87219784N', 'antonio', 'ant@ant.com', '$2y$10$.8tOdebX2yvUjVLsjMcB7eMSQNMTwFCjfYwoBBgfYsGwzhDaxpJ4K', 'administrador', '444565656', 'tresmall 16', 'caudete', 'albacete', '2024-12-06 00:33:22'),
(47, '02389427A', 'alfi', 'al@al.com', '$2y$10$YN0dZBWKw5UmyF4BsL.Pk.Jq6kc.MsZwQywhBjAWB1W5tWTLOIIZ.', 'editor', '444777888', 'calle calvario 55', 'caudete', 'albacete', '2024-12-06 22:14:25');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `articulos`
--
ALTER TABLE `articulos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `articulos`
--
ALTER TABLE `articulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
