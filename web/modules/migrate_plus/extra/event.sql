-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 12, 2016 at 05:33 PM
-- Server version: 5.5.47-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `update_migration`
--

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE IF NOT EXISTS `event` (
  `Id` int(1) DEFAULT NULL,
  `title` varchar(6) DEFAULT NULL,
  `field_datetime` varchar(20) DEFAULT NULL,
  `body` varchar(16) DEFAULT NULL,
  `field_leader` varchar(21) DEFAULT NULL,
  `field_venue` varchar(7) DEFAULT NULL,
  `field_event_tags` varchar(24) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`Id`, `title`, `field_datetime`, `body`, `field_leader`, `field_venue`, `field_event_tags`) VALUES
(1, 'Event1', '2001-11-01T01:00:00', 'Body for Event', 'Jane Powell,Five Cram', 'Venue 1', 'Day,Meditation'),
(2, 'Event2', '2001-11-02T02:00:00', 'Body for Event', 'Five Cram', 'Venue 2', 'Week,Retreat'),
(3, 'Event3', '2001-11-03T03:20:00', '', '', 'Venue 3', ''),
(4, 'Event4', '2001-11-04T04:00:03', 'Body for Event 4', 'Iegor Reznikoff', 'Venue 1', 'Week,Teachings,Gathering'),
(5, 'Event5', '2001-11-05T05:00:00', 'Body for Event 5', 'Heinz Roger', '', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
