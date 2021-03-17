-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Mar 17, 2021 at 05:57 PM
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
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `visible` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `filters`
--

INSERT INTO `filters` (`id`, `name`, `content`, `parent_id`, `visible`) VALUES
(1, 'color', '', NULL, 1),
(2, 'size', '', NULL, 1),
(3, 'red', 'content - 2', 1, NULL),
(4, 'green', 'content - 3', 1, NULL),
(5, 'black', 'content - 4', 1, NULL),
(6, '200ml', 'content - 5', 2, NULL),
(7, '300ml', 'content - 6', 2, NULL),
(49, 'lightred', NULL, 3, 1),
(50, 'tttt', '2', 1, 1);

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
  `parent_id` int(11) DEFAULT NULL,
  `visible` int(11) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `date` date NOT NULL,
  `datetime` datetime NOT NULL,
  `alias` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `goods`
--

INSERT INTO `goods` (`id`, `name`, `img`, `gallery_img`, `menu_position`, `parent_id`, `visible`, `content`, `date`, `datetime`, `alias`) VALUES
(63, '1', '1.jpg', NULL, NULL, NULL, NULL, NULL, '0000-00-00', '0000-00-00 00:00:00', ''),
(64, '2', '2.jpg', NULL, NULL, NULL, NULL, NULL, '0000-00-00', '0000-00-00 00:00:00', ''),
(65, '3', '3.jpg', NULL, NULL, NULL, NULL, NULL, '0000-00-00', '0000-00-00 00:00:00', ''),
(66, '1', '1.jpg', NULL, 1, NULL, NULL, NULL, '0000-00-00', '0000-00-00 00:00:00', ''),
(67, '2', '2.jpg', NULL, NULL, NULL, NULL, NULL, '0000-00-00', '0000-00-00 00:00:00', ''),
(68, '3', '3.jpg', NULL, NULL, NULL, NULL, NULL, '0000-00-00', '0000-00-00 00:00:00', ''),
(69, '33', NULL, NULL, 1, NULL, NULL, NULL, '0000-00-00', '0000-00-00 00:00:00', ''),
(70, '33', NULL, NULL, 1, NULL, NULL, NULL, '0000-00-00', '0000-00-00 00:00:00', ''),
(71, '33', NULL, NULL, 1, NULL, NULL, NULL, '0000-00-00', '0000-00-00 00:00:00', ''),
(72, '33', '33.jpg', NULL, 1, NULL, NULL, NULL, '0000-00-00', '0000-00-00 00:00:00', ''),
(73, '33!!!', '[\"33.jpg\",\"33.jpg\",\"33.jpg\"]', NULL, 1, NULL, NULL, NULL, '0000-00-00', '0000-00-00 00:00:00', ''),
(74, '33!!!', '[\"33.jpg\",\"33.jpg\",\"33.jpg\"]', NULL, 1, NULL, NULL, NULL, '0000-00-00', '0000-00-00 00:00:00', ''),
(75, '321421', NULL, NULL, 13, 74, 1, '421421421421', '2020-10-16', '2020-10-16 15:45:18', ''),
(76, 'fffffffffffffffffffffff', NULL, NULL, 1, 65, 1, 'fffffffff', '2020-10-16', '2020-10-16 15:49:47', ''),
(77, '', NULL, NULL, 1, 0, 1, '', '2020-10-16', '2020-10-16 19:41:04', ''),
(78, '', NULL, NULL, 1, 0, 1, '', '2020-10-16', '2020-10-16 19:41:21', ''),
(79, '', NULL, NULL, 1, 0, 1, '', '2020-10-16', '2020-10-16 19:41:23', ''),
(80, '', NULL, NULL, 1, 0, 1, '', '2020-10-16', '2020-10-16 19:41:26', ''),
(81, 'w12341241', NULL, NULL, 1, 0, 1, '241421421', '2020-10-16', '2020-10-16 19:41:45', ''),
(82, 'rweewerrrrrrr', NULL, NULL, 1, 0, 1, 'rewwwwwww', '2020-10-16', '2020-10-16 19:50:14', 'rweewerrrrrrr'),
(83, '', NULL, NULL, 1, 0, 1, '', '2020-10-16', '2020-10-16 19:50:17', ''),
(84, '', NULL, NULL, 1, 0, 1, '', '2020-10-16', '2020-10-16 19:50:50', ''),
(85, 'bvcbcbc', NULL, NULL, 21, 84, 1, 'rwqqwrqw', '2020-10-16', '2020-10-16 19:51:12', 'bvcbcbc'),
(86, 'привет мир 43235345 ;\"№%\"№\";\"! _-', NULL, NULL, 1, 0, 1, '', '2020-10-16', '2020-10-16 19:51:35', 'privet-mir-43235345-_-'),
(87, 'ewqewqe', NULL, NULL, 1, 0, 1, '', '2020-10-16', '2020-10-16 19:53:52', 'ewqewqe'),
(88, 'цвуцйкуцкцук4123лдо 1дьбьцбюььуцдйукжлцйкожуйц', NULL, NULL, 1, 0, 1, 'fddsfsd', '2020-10-16', '2020-10-16 20:09:25', 'tsvutsykutsktsuk4123ldo-1dbtsbyuyutsdyukzhltsykozhuyts');

-- --------------------------------------------------------

--
-- Table structure for table `goods_filters`
--

CREATE TABLE `goods_filters` (
  `student` int(10) UNSIGNED NOT NULL,
  `teacher` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `goods_filters`
--

INSERT INTO `goods_filters` (`student`, `teacher`) VALUES
(1, 63),
(1, 64),
(1, 65),
(1, 66),
(2, 63),
(2, 64),
(2, 65),
(2, 66),
(3, 63),
(3, 68),
(3, 69);

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
  ADD KEY `11` (`parent_id`);

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
  ADD PRIMARY KEY (`student`,`teacher`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `goods`
--
ALTER TABLE `goods`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

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
  ADD CONSTRAINT `11` FOREIGN KEY (`parent_id`) REFERENCES `filters` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
