-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 01, 2016 at 07:12 PM
-- Server version: 5.5.47-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `parthatemp`
--

-- --------------------------------------------------------

--
-- Table structure for table `event_tags`
--

CREATE TABLE IF NOT EXISTS `event_tags` (
  `title` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `event_tags`
--

INSERT INTO `event_tags` (`title`) VALUES
('Collection'),
('Day'),
('Five days'),
('Gathering'),
('Informal'),
('Month'),
('Online course'),
('Retreat'),
('Series'),
('Single session'),
('Talk'),
('Teaching'),
('Ten days'),
('Two weeks'),
('Week'),
('Weekend'),
('Weekend retreat'),
('Workshop');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
