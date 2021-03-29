-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Хост: db
-- Час створення: Бер 24 2021 р., 14:43
-- Версія сервера: 10.5.9-MariaDB-1:10.5.9+maria~focal
-- Версія PHP: 7.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База даних: `project`
--

-- --------------------------------------------------------

--
-- Структура таблиці `filters`
--

CREATE TABLE `filters` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `visible` smallint(6) DEFAULT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `menu_position` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `filters`
--

INSERT INTO `filters` (`id`, `name`, `content`, `visible`, `parent_id`, `menu_position`) VALUES
(3, 'red', 'content - 2', NULL, 51, 1),
(4, 'green', 'content - 3', NULL, 51, 2),
(5, 'black', 'content - 4', NULL, 51, 3),
(6, '200ml', 'content - 5', NULL, 52, 1),
(7, '300ml', 'content - 6', NULL, 52, 2),
(51, 'color', NULL, NULL, NULL, 1),
(52, 'size', NULL, NULL, NULL, 3),
(54, 'Lenght', '1', 1, NULL, 2),
(55, '1 m', '1', 1, 54, 1);

--
-- Індекси збережених таблиць
--

--
-- Індекси таблиці `filters`
--
ALTER TABLE `filters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `filters_filters_id_fk` (`parent_id`);

--
-- AUTO_INCREMENT для збережених таблиць
--

--
-- AUTO_INCREMENT для таблиці `filters`
--
ALTER TABLE `filters`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- Обмеження зовнішнього ключа збережених таблиць
--

--
-- Обмеження зовнішнього ключа таблиці `filters`
--
ALTER TABLE `filters`
  ADD CONSTRAINT `filters_filters_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `filters` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
