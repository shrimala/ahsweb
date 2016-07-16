-- MySQL dump 10.13  Distrib 5.5.47, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: tappetyc_ahs
-- ------------------------------------------------------
-- Server version	5.5.47-0ubuntu0.14.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `res_classifications`
--

DROP TABLE IF EXISTS `res_classifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `res_classifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_name` varchar(75) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `PK_AHSClassifications` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `res_classifications`
--

LOCK TABLES `res_classifications` WRITE;
/*!40000 ALTER TABLE `res_classifications` DISABLE KEYS */;
INSERT INTO `res_classifications` VALUES (1,'Restricted - don\'t list publicly'),(2,'Miscellaneous Heart of Awakening'),(3,'Meditation & Daily Life Awareness practice'),(4,'Heart, Mind & Space - DHB book 1'),(5,'Confidence & Heart Wish - DHB Book 2'),(6,'Openness & Clarity - DHB Book 3'),(7,'Sensitivity & Mandala Principle - DHB Book 4'),(8,'Trusting the Heart of Buddhism Book 1'),(9,'Trusting the Heart of Buddhism Book 2'),(10,'Miscellaneous Vaster Vision'),(11,'Loving Kindness'),(12,'Basic liturgy'),(13,'Other liturgy'),(14,'Taking Refuge'),(15,'Taking the Bodhisattva Vow'),(16,'Death & Dying');
/*!40000 ALTER TABLE `res_classifications` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-02-20 11:09:19
