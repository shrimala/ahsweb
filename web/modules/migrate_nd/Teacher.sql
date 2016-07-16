-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 19, 2016 at 01:56 PM
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
-- Table structure for table `Teacher`
--

CREATE TABLE IF NOT EXISTS `Teacher` (
  `teacher` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Teacher`
--

INSERT INTO `Teacher` (`teacher`) VALUES
('Five Cram'),
('Gabriele Reifenberg'),
('Heinz Roger'),
('Iegor Reznikoff'),
('Jane Powell'),
('Katie Morrow'),
('Khenpo Namgyal'),
('Khenpo Tsultrim Gyamtso Rinpoche'),
('Kristina Bischoff'),
('Lama Lodro Palmo'),
('Lama Phuntsok'),
('Lama Rigdzin Shikpo'),
('Lama Shenpen'),
('Jane Powell'),
('Patrycja Bielak'),
('Pema Oser'),
('Ponlop Rinpoche'),
('Katie Morrow'),
('Aled Jones-Williams'),
('Katie'),
('Stephanie Hair'),
('Tashi Mannox'),
('Vajrapriya'),
('Tara Dew');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
