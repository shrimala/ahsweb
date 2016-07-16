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
  `id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `audio`
--

INSERT INTO `audio` (`title`, `id`) VALUES
('Audio1', 'testfolder2016/behagra-t-192.mp3'),
('Audio2', 'testfolder2016/bhatiyar_ausschnitt3.mp3'),
('Audio3', 'testfolder2016/do_you_sing_my_song.mp3'),
('Audio4', 'testfolder2016/pictures-promenade.mp3'),
('Audio5', 'testfolder2016/Mozart-minuet-k2.mp3'),
('Audio6', 'testfolder2016/Satie-Gymnopedie1-cello-piano.mp3'),
('Audio7', 'testfolder2016/soulsearcher-clip-2-192.mp3'),
('Audio8', 'testfolder2016/Tumi asbe boleNachiketa.mp3');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
