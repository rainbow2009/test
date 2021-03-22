-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Mar 21, 2021 at 09:30 PM
-- Server version: 10.5.5-MariaDB-1:10.5.5+maria~focal
-- PHP Version: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `menu_position` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `name`, `parent_id`, `menu_position`) VALUES
(1, '1st art', 1, NULL),
(2, '2nd art', NULL, NULL),
(3, '3 art', NULL, NULL),
(5, '4t art', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `filters`
--

CREATE TABLE `filters` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `visible` smallint(6) DEFAULT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `filters`
--

INSERT INTO `filters` (`id`, `name`, `content`, `visible`, `parent_id`) VALUES
(3, 'red', 'content - 2', NULL, 51),
(4, 'green', 'content - 3', NULL, 51),
(5, 'black', 'content - 4', NULL, 51),
(6, '200ml', 'content - 5', NULL, 52),
(7, '300ml', 'content - 6', NULL, 52),
(51, 'color', NULL, NULL, NULL),
(52, 'size', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `filters_categories`
--

CREATE TABLE `filters_categories` (
  `id` int(10) NOT NULL,
  `name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `filters_categories`
--

INSERT INTO `filters_categories` (`id`, `name`) VALUES
(1, 'color'),
(2, 'size');

-- --------------------------------------------------------

--
-- Table structure for table `goods`
--

CREATE TABLE `goods` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `gallery_img` text DEFAULT NULL,
  `menu_position` int(10) UNSIGNED DEFAULT NULL,
  `visible` int(11) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `date` date NOT NULL,
  `datetime` datetime NOT NULL,
  `alias` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `goods`
--

INSERT INTO `goods` (`id`, `name`, `img`, `gallery_img`, `menu_position`, `visible`, `content`, `date`, `datetime`, `alias`) VALUES
(93, 'test1', NULL, NULL, 1, 1, '1', '2021-03-21', '2021-03-21 21:14:48', 'test1'),
(94, 'test2', NULL, NULL, 1, 1, '1', '2021-03-21', '2021-03-21 21:17:09', 'test2'),
(95, '1', NULL, NULL, 1, 1, '1', '2021-03-21', '2021-03-21 21:22:34', '1'),
(96, '1', NULL, NULL, 1, 1, '1', '2021-03-21', '2021-03-21 21:23:31', ''),
(97, '1', NULL, NULL, 1, 1, '1', '2021-03-21', '2021-03-21 21:23:41', ''),
(98, '24124', NULL, NULL, 1, 1, '1', '2021-03-21', '2021-03-21 21:25:36', '24124'),
(99, 'wdfwerfwe', NULL, NULL, 1, 1, '321wrq', '2021-03-21', '2021-03-21 21:26:06', 'wdfwerfwe');

-- --------------------------------------------------------

--
-- Table structure for table `goods_filters`
--

CREATE TABLE `goods_filters` (
  `goods_id` int(10) UNSIGNED NOT NULL,
  `filters_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `goods_filters`
--

INSERT INTO `goods_filters` (`goods_id`, `filters_id`) VALUES
(93, 51),
(98, 3),
(98, 4),
(98, 6),
(98, 7),
(99, 4),
(99, 5);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `name`) VALUES
(1, '1st page'),
(2, '2nd page');

-- --------------------------------------------------------

--
-- Table structure for table `parsing_table`
--

CREATE TABLE `parsing_table` (
  `id` int(11) NOT NULL,
  `all_links` longtext DEFAULT NULL,
  `temp_links` longtext DEFAULT NULL,
  `bad_link` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `parsing_table`
--

INSERT INTO `parsing_table` (`id`, `all_links`, `temp_links`, `bad_link`) VALUES
(1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `1-2` (`parent_id`);

--
-- Indexes for table `filters`
--
ALTER TABLE `filters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `filters_filters_id_fk` (`parent_id`);

--
-- Indexes for table `filters_categories`
--
ALTER TABLE `filters_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `goods`
--
ALTER TABLE `goods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `goods_filters`
--
ALTER TABLE `goods_filters`
  ADD PRIMARY KEY (`goods_id`,`filters_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parsing_table`
--
ALTER TABLE `parsing_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `filters`
--
ALTER TABLE `filters`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `filters_categories`
--
ALTER TABLE `filters_categories`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `goods`
--
ALTER TABLE `goods`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `parsing_table`
--
ALTER TABLE `parsing_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `1-2` FOREIGN KEY (`parent_id`) REFERENCES `pages` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `filters`
--
ALTER TABLE `filters`
  ADD CONSTRAINT `filters_filters_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `filters` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
