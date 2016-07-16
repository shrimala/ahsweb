-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 19, 2016 at 01:54 PM
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
-- Table structure for table `audio`
--

CREATE TABLE IF NOT EXISTS `audio` (
  `title` varchar(6) DEFAULT NULL,
  `id` varchar(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `audio`
--

INSERT INTO `audio` (`title`, `id`) VALUES
('Audio1', 'aaa/2637890.txt'),
('Audio2', 'aaa/2637891.txt'),
('Audio3', 'bbb/2637892.txt'),
('Audio4', 'bbb/2637893.txt'),
('Audio5', 'ccc/2637894.txt'),
('Audio6', 'ddd/4847475.txt'),
('Audio7', 'eee/38378374.txt'),
('Audio8', 'fff/5848737.txt');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
