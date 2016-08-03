-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 03, 2016 at 03:43 PM
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
-- Table structure for table `Venue`
--

CREATE TABLE IF NOT EXISTS `Venue` (
  `title` varchar(44) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Venue`
--

INSERT INTO `Venue` (`title`) VALUES
('Atlow Mill Centre, Derbyshire'),
('Bardsey Island, North Wales'),
('Berlin, Germany'),
('Bilberry Hill Centre, Birmingham'),
('Bodhi Tree, Hampshire'),
('Bodhicharya Buddhist group, Hampshire'),
('Bremen, Germany'),
('Brighton'),
('Cambridge'),
('Chichester, Sussex'),
('Friends Meeting House, Worcester'),
('Hermitage of the Awakened Heart, North Wales'),
('Ladywell Centre, Surrey'),
('St James Boarding School, Worcestershire'),
('Morlan Centre, Aberystwyth, Wales'),
('Kathmandu, Nepal'),
('Online'),
('Othona Community, Essex'),
('Park Place, Hampshire'),
('Prinknash Abbey, Gloucestershire'),
('Quaker Meeting House, Leigh on Sea, Essex'),
('Ranjung Yeshe Institute, Nepal'),
('Rigpa, London'),
('Roots and Wings, Birmingham'),
('Shambhala, London'),
('St Columba''s Centre, Woking, Surrey'),
('St Marys Convent, London'),
('The Abbey, Sutton Courtenay, Oxfordshire'),
('The Beeches, Birmingham'),
('Carmelite Priory, Oxford'),
('Cherwell Centre, Oxford'),
('The Priory, Sayers Common, Sussex'),
('Trigonos, North Wales'),
('Twymyn Valley, Wales'),
('Tyn y Gors, North Wales'),
('Unknown');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
