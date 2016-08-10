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
-- Table structure for table `res_classifications`
--

CREATE TABLE IF NOT EXISTS `res_classifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_name` varchar(75) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `PK_AHSClassifications` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `res_classifications`
--

INSERT INTO `res_classifications` (`id`, `class_name`) VALUES
(1, 'Restricted - don''t list publicly'),
(2, 'Miscellaneous Heart of Awakening'),
(3, 'Meditation & Daily Life Awareness practice'),
(4, 'Heart, Mind & Space - DHB book 1'),
(5, 'Confidence & Heart Wish - DHB Book 2'),
(6, 'Openness & Clarity - DHB Book 3'),
(7, 'Sensitivity & Mandala Principle - DHB Book 4'),
(8, 'Trusting the Heart of Buddhism Book 1'),
(9, 'Trusting the Heart of Buddhism Book 2'),
(10, 'Miscellaneous Vaster Vision'),
(11, 'Loving Kindness'),
(12, 'Basic liturgy'),
(13, 'Other liturgy'),
(14, 'Taking Refuge'),
(15, 'Taking the Bodhisattva Vow'),
(16, 'Death & Dying');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
