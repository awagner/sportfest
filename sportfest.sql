-- MySQL dump 10.13  Distrib 5.5.27, for Win32 (x86)
--
-- Host: localhost    Database: sportfest
-- ------------------------------------------------------
-- Server version	5.5.27

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
-- Table structure for table `ergebnisse`
--

DROP TABLE IF EXISTS `ergebnisse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ergebnisse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `station` int(11) NOT NULL,
  `klasse` int(11) NOT NULL,
  `punkte` int(11) NOT NULL,
  `zeit` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ergebnisse`
--

LOCK TABLES `ergebnisse` WRITE;
/*!40000 ALTER TABLE `ergebnisse` DISABLE KEYS */;
INSERT INTO `ergebnisse` VALUES (2,1,2,100,1374561632),(3,2,1,20,1374568612),(5,1,1,45,1374608525),(6,3,3,100,1374569221),(7,4,1,20,1374600931),(8,4,4,23,1374608328),(9,1,4,44,1374608788),(10,1,6,100,1374609543),(11,1,3,10,1374609669),(12,1,5,60,1374615584),(13,1,22,35,1374615660),(14,1,13,70,1374615755);
/*!40000 ALTER TABLE `ergebnisse` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `klassen`
--

DROP TABLE IF EXISTS `klassen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `klassen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `sortorder` int(11) NOT NULL,
  `visible` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `klassen`
--

LOCK TABLES `klassen` WRITE;
/*!40000 ALTER TABLE `klassen` DISABLE KEYS */;
INSERT INTO `klassen` VALUES (1,'5a',1,1),(2,'5b',2,1),(3,'5c',3,1),(4,'5d',4,1),(5,'6a',5,1),(6,'6b',6,1),(7,'6c',7,1),(8,'6d',8,1),(9,'7a',9,1),(10,'7b',10,1),(11,'7c',11,1),(12,'7d',12,1),(13,'8a',13,1),(14,'8b',14,1),(15,'8c',15,1),(16,'8d',16,1),(17,'9a',18,1),(18,'9b',19,1),(19,'9c',20,1),(20,'9d',21,1),(21,'8e',17,1),(22,'10a',22,1),(23,'10b',23,1),(24,'10c',24,1),(25,'10d',25,1);
/*!40000 ALTER TABLE `klassen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stationen`
--

DROP TABLE IF EXISTS `stationen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stationen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `sortorder` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stationen`
--

LOCK TABLES `stationen` WRITE;
/*!40000 ALTER TABLE `stationen` DISABLE KEYS */;
INSERT INTO `stationen` VALUES (1,'Sprint','Die Läufer bestreiten eine Pendelstaffel über 75m.',1),(2,'Eierwurf','Jeder hat zwei Würfe, mit denen er das Ei durch einen Ring werfen muss. ',2),(3,'Hochsprung','Jeder hat einen Sprung über eine selbstgewählte Höhe.',3),(4,'Autoabschleppen','Gemessen wird die Strecke, über welche die ganze Klasse ein dickes Lehrerauto in 5 sec ziehen kann.',4),(5,'Ei kicken','Jeder hat einen Versuch das Ei so weit wie mögliche zu kicken.',5),(6,'Sandkasten-Boccia','Im Beachvolleyballfeld wird mit Boccia-Kugeln auf Fahrradreifen geworfen. ',6),(7,'Golf','Der Ball ist mit maximal 6 Schlägen in ein Loch zu befördern.',7),(8,'Seil springen','Die 8 Seilspringer haben zwei Versuche um ins Langseil zu kommen u. gemeinsam mind. 5x zu springen. ',8),(9,'Seiltanz','Auf einem gespannten Gurt müssen 8 Schüler eine bestimmte Strecke überwinden.',9),(10,'Büchsen schießen','Mit einer Wasserspritze sind innerhalb von 5 Minuten möglichst viele von 16 Büchsen umzuschießen.',10),(11,'Tischtennis','Die 6 Teilnehmer spielen einen Rundlauf.',11),(12,'Klettern','Hintereinander sollen 4 Kletterer die Kletterwand bewältigen.',12);
/*!40000 ALTER TABLE `stationen` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-07-24  8:08:21
