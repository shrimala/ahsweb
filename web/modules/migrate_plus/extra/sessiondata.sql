-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 12, 2016 at 04:17 PM
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
-- Table structure for table `sessiondata`
--

CREATE TABLE IF NOT EXISTS `sessiondata` (
  `Recordings` varchar(250) DEFAULT NULL,
  `Event` int(1) DEFAULT NULL,
  `Old_id` varchar(4) DEFAULT NULL,
  `title` varchar(10) DEFAULT NULL,
  `field_datetime` varchar(20) DEFAULT NULL,
  `type` varchar(17) DEFAULT NULL,
  `body_summary` varchar(8) DEFAULT NULL,
  `body` varchar(22) DEFAULT NULL,
  `field_leader` varchar(37) DEFAULT NULL,
  `field_clip` varchar(1) DEFAULT NULL,
  `field_old_catalog` varchar(10) DEFAULT NULL,
  `field_restricted` varchar(1) DEFAULT NULL,
  `field_admin_tags` varchar(49) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sessiondata`
--

INSERT INTO `sessiondata` (`Recordings`, `Event`, `Old_id`, `title`, `field_datetime`, `type`, `body_summary`, `body`, `field_leader`, `field_clip`, `field_old_catalog`, `field_restricted`, `field_admin_tags`) VALUES
('testfolder2016/bhatiyar_ausschnitt3.mp3,f_41KcRCTvc', 1, '', 'Session1', '2002-12-01T01:00:00', 'Teaching', 'Brief 1', 'Session description 1', 'Five Cram', '1', 'lorem', '', ''),
('Gy7F-VIFdbw', 1, '977', 'Session2', '2002-12-02T02:00:00', 'Q&A', 'Brief 2', 'Session description 2', 'Gabriele Reifenberg', '', '', '1', 'Needs digitisation'),
('IubVyA_n5Do', 1, '1785', 'Session3', '2002-12-03T03:00:00', 'Miscellaneous', 'BRIEF 3', 'SESSION DESCRIPTION 3', 'Heinz Roger', '', 'ipsum', '', ''),
('j1DGS9DyzCs', 2, '', 'Session4', '2002-12-04T04:00:00', 'Meditation', '', '', 'Iegor Reznikoff,Jane Powell,Five Cram', '', '', '', 'Possibly missing tape,Possibly needs digitisation'),
('testfolder2016/Satie-Gymnopedie1-cello-piano.mp3', 2, '1870', 'Session5', '2002-12-05T05:00:00', 'Guided meditation', '', '', 'Jane Powell', '', '', '', ''),
('MidqoMuVhLI', 3, '', 'Session6', '2002-12-06T06:00:00', 'Liturgy', 'Brief 6', '', 'Five Cram', '', '', '1', ''),
('y_UhHeCDwms', 4, '1910', 'Session7', '2002-12-07T07:00:00', 'Singing', 'BRIEF 7', '', '', '', 'dolor amet', '', 'Missing tape,Needs digitisation'),
('jRgYC4AmgPg', 4, '1051', 'Session8', '2002-12-08T08:00:00', 'Story', 'Brief 8', 'Session description 8', 'Jane Powell', '', '', '', ''),
('T-zl6aX2UIE', 4, '1267', 'Session9', '2002-12-09T09:00:00', 'Transmission', '', 'Session description 9', 'Jane Powell,Five Cram', '1', '', '', 'Missing tape'),
('z41PPQUNKx0', 4, '', 'Session10', '2002-12-10T10:00:00', '', '', 'Session description 10', 'Heinz Roger', '', '', '', 'Missing media'),
('MidqoMuVhLI,2016-04/soulsearcher-clip-2-192.mp3', 4, '', 'Session11', '2002-12-11T11:00:00', '', '', '', 'Jane Powell', '', '', '', ''),
('y_UhHeCDwms,testfolder2016/do_you_sing_my_song.mp3', 4, '1872', 'Session12', '2002-12-12T12:00:00', '', 'Brief 12', 'Session description 12', 'Iegor Reznikoff', '', '', '1', ''),
('2-_XfjSjTU8', 4, '', 'Session 13', '2002-12-12T13:00:00', '', '', 'Session description 13', 'Gabriele Reifenberg', '', '', '', 'Possibly missing tape'),
('testfolder2016/Tumi asbe boleNachiketa.mp3,Jr43i_Yjoys', 4, '1861', 'Session14', '2002-12-14T14:00:00', '', 'Brief 14', 'Session description 14', 'Five Cram', '', '', '', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
