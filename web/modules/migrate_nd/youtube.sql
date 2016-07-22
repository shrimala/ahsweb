-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 20, 2016 at 08:01 PM
-- Server version: 5.5.47-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `xyz`
--

-- --------------------------------------------------------

--
-- Table structure for table `youtube`
--

CREATE TABLE IF NOT EXISTS `youtube` (
  `title` varchar(9) DEFAULT NULL,
  `length` varchar(2) DEFAULT NULL,
  `id` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `youtube`
--

INSERT INTO `youtube` (`title`, `length`, `id`) VALUES
('Youtube1', '30', 'Gy7F-VIFdbw'),
('Youtube2', '25', '2-_XfjSjTU8'),
('Youtube3', '34', 'IUbVyA_n5Do'),
('Youtube4', '59', 'j1DGS9DyzCs'),
('Youtube5', '93', 'jRgYC4AmgPg'),
('Youtube6', '89', 'T-zl6aX2UIE'),
('Youtube7', '93', 'f_41KcRCTvc'),
('Youtube8', '39', 'z41PPQUNKx0'),
('Youtube9', '3', 'Jr43i_YJoys'),
('Youtube10', '83', 'MIdqoMuVhLI'),
('Youtube11', '', 'y_UhHeCDwms');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
