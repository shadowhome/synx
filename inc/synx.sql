-- MySQL dump 10.13  Distrib 5.6.25, for osx10.10 (x86_64)
--
-- Host: localhost    Database: synx
-- ------------------------------------------------------
-- Server version	5.6.25

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
-- Table structure for table `Packages`
--

DROP TABLE IF EXISTS `Packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Packages` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `package` varchar(255) DEFAULT NULL,
  `OS` varchar(255) DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL,
  `security` varchar(255) DEFAULT '0',
  `upgrade` int(11) DEFAULT '0',
  `servers` int(10) unsigned DEFAULT NULL,
  `servername` varchar(120) DEFAULT NULL,
  `changelog` text,
  `date` datetime DEFAULT NULL,
  `md5` varchar(255) DEFAULT NULL,
  `nversion` varchar(60) DEFAULT NULL,
  `ii` int(2) DEFAULT NULL,
  `rc` int(2) DEFAULT NULL,
  `sshp` int(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `packages1` (`servers`,`package`,`md5`)
) ENGINE=InnoDB AUTO_INCREMENT=23297 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Packages`
--

LOCK TABLES `Packages` WRITE;
/*!40000 ALTER TABLE `Packages` DISABLE KEYS */;
/*!40000 ALTER TABLE `Packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `packagesHist`
--

DROP TABLE IF EXISTS `packagesHist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `packagesHist` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `package` varchar(255) DEFAULT NULL,
  `OS` varchar(255) DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL,
  `security` varchar(255) DEFAULT '0',
  `upgrade` int(11) DEFAULT '0',
  `servers` varchar(120) DEFAULT NULL,
  `servername` varchar(120) DEFAULT NULL,
  `changelog` text,
  `upgraded` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packagesHist`
--

LOCK TABLES `packagesHist` WRITE;
/*!40000 ALTER TABLE `packagesHist` DISABLE KEYS */;
/*!40000 ALTER TABLE `packagesHist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `servers`
--

DROP TABLE IF EXISTS `servers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `servers` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `servername` varchar(30) NOT NULL,
  `ip` varchar(30) NOT NULL,
  `company` varchar(60) DEFAULT NULL,
  `OS` varchar(50) DEFAULT NULL,
  `version` varchar(50) DEFAULT NULL,
  `description` varchar(120) DEFAULT NULL,
  `releasever` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `serverips` (`ip`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servers`
--

LOCK TABLES `servers` WRITE;
/*!40000 ALTER TABLE `servers` DISABLE KEYS */;
INSERT INTO `servers` VALUES (21,'home.ur-sltn.com','192.168.2.1','Ur-Sltn LTD','Debian','jessie','description','8.1');
/*!40000 ALTER TABLE `servers` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-08-11 18:15:59
