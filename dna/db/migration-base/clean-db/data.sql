-- MySQL dump 10.13  Distrib 5.5.44, for debian-linux-gnu (x86_64)
--
-- Host: localdb    Database: db_clean_db
-- ------------------------------------------------------
-- Server version	5.6.27

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
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` (`id`, `title`) VALUES (1,'Developer');
INSERT INTO `role` (`id`, `title`) VALUES (2,'SuperAdministrator');
INSERT INTO `role` (`id`, `title`) VALUES (3,'Authenticated');
INSERT INTO `role` (`id`, `title`) VALUES (4,'Guest');
INSERT INTO `role` (`id`, `title`) VALUES (5,'GroupAdministrator');
INSERT INTO `role` (`id`, `title`) VALUES (6,'GroupPublisher');
INSERT INTO `role` (`id`, `title`) VALUES (7,'GroupEditor');
INSERT INTO `role` (`id`, `title`) VALUES (8,'GroupApprover');
INSERT INTO `role` (`id`, `title`) VALUES (9,'GroupModerator');
INSERT INTO `role` (`id`, `title`) VALUES (10,'GroupContributor');
INSERT INTO `role` (`id`, `title`) VALUES (11,'GroupReviewer');
INSERT INTO `role` (`id`, `title`) VALUES (12,'GroupTranslator');
INSERT INTO `role` (`id`, `title`) VALUES (13,'GroupMember');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

