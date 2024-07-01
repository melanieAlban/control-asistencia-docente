-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: 34.174.179.172    Database: asistencia-docente
-- ------------------------------------------------------
-- Server version	8.0.31-google

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ 'ce76ae82-251b-11ef-bdd8-42010a400007:1-1091';

--
-- Table structure for table `asistencias`
--

DROP TABLE IF EXISTS `asistencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asistencias` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `total_generado` double(10,2) DEFAULT NULL,
  `descuento` double(10,2) DEFAULT NULL,
  `id_empleado` bigint NOT NULL,
  `estado` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_empleado` (`id_empleado`),
  CONSTRAINT `asistencias_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistencias`
--

LOCK TABLES `asistencias` WRITE;
/*!40000 ALTER TABLE `asistencias` DISABLE KEYS */;
INSERT INTO `asistencias` VALUES (88,'2024-06-06',54.00,18.75,34,'FINALIZADO'),(89,'2024-06-07',40.00,45.00,34,'FINALIZADO'),(91,'2024-06-08',0.00,60.00,34,'FINALIZADO'),(92,'2024-06-09',40.00,45.00,34,'FINALIZADO'),(93,'2024-06-10',45.86,34.00,34,'FINALIZADO'),(94,'2024-06-11',40.00,45.00,34,'FINALIZADO'),(95,'2024-06-12',0.00,60.00,34,'FINALIZADO'),(96,'2024-06-13',0.00,60.00,34,'FINALIZADO'),(97,'2024-06-14',0.00,60.00,34,'FINALIZADO'),(98,'2024-06-15',0.00,60.00,34,'FINALIZADO'),(99,'2024-06-16',0.00,60.00,34,'FINALIZADO'),(100,'2024-06-17',0.00,60.00,34,'FINALIZADO'),(101,'2024-06-18',0.00,60.00,34,'FINALIZADO'),(102,'2024-06-19',40.00,45.00,34,'FINALIZADO'),(103,'2024-06-20',56.54,13.99,34,'FINALIZADO'),(104,'2024-06-20',0.00,60.00,31,'FINALIZADO'),(105,'2024-06-26',16.00,48.99,31,'FINALIZADO'),(106,'2024-06-27',32.00,60.00,31,'FINALIZADO'),(107,'2024-06-28',64.00,0.00,31,'FINALIZADO'),(108,'2024-06-29',56.00,15.00,31,'FINALIZADO'),(109,'2024-06-29',64.00,0.00,34,'FINALIZADO'),(110,'2024-06-30',53.13,20.38,34,'FINALIZADO'),(111,'2024-07-01',4.00,60.00,34,'FINALIZADO'),(112,'2024-06-05',53.06,20.50,34,'FINALIZADO');
/*!40000 ALTER TABLE `asistencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_asistencias`
--

DROP TABLE IF EXISTS `detalle_asistencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_asistencias` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `jornada` varchar(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `hora_ingreso` time DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `horas_trabajadas` time NOT NULL,
  `subtotal_generado` double(10,2) NOT NULL,
  `id_asistencia` bigint NOT NULL,
  `subtotal_descuento` double(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_asistencia` (`id_asistencia`),
  CONSTRAINT `detalle_asistencias_ibfk_1` FOREIGN KEY (`id_asistencia`) REFERENCES `asistencias` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=180 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_asistencias`
--

LOCK TABLES `detalle_asistencias` WRITE;
/*!40000 ALTER TABLE `detalle_asistencias` DISABLE KEYS */;
INSERT INTO `detalle_asistencias` VALUES (130,'MAT','10:00:00','12:45:00','02:45:00',22.00,88,3.75),(131,'VES','15:00:00','19:00:00','04:00:00',32.00,88,15.00),(132,'MAT',NULL,NULL,'00:00:00',0.00,89,45.00),(133,'VES','15:00:00','20:00:00','05:00:00',40.00,89,0.00),(136,'MAT',NULL,NULL,'00:00:00',0.00,91,45.00),(137,'VES',NULL,NULL,'00:00:00',0.00,91,75.00),(138,'MAT','10:00:00','13:00:00','03:00:00',24.00,92,0.00),(139,'VES','18:00:00','20:05:00','02:00:00',16.00,92,45.00),(140,'MAT','12:14:00','13:07:00','00:46:00',6.13,93,33.50),(141,'VES','15:00:00','19:58:00','04:58:00',39.73,93,0.50),(142,'MAT',NULL,NULL,'00:00:00',0.00,94,45.00),(143,'VES','14:55:00','20:01:00','05:00:00',40.00,94,0.00),(144,'MAT',NULL,NULL,'00:00:00',0.00,95,45.00),(145,'VES',NULL,NULL,'00:00:00',0.00,95,75.00),(146,'MAT',NULL,NULL,'00:00:00',0.00,96,45.00),(147,'VES',NULL,NULL,'00:00:00',0.00,96,75.00),(148,'MAT',NULL,NULL,'00:00:00',0.00,97,45.00),(149,'VES',NULL,NULL,'00:00:00',0.00,97,75.00),(150,'MAT',NULL,NULL,'00:00:00',0.00,98,45.00),(151,'VES',NULL,NULL,'00:00:00',0.00,98,75.00),(152,'MAT',NULL,NULL,'00:00:00',0.00,99,45.00),(153,'VES',NULL,NULL,'00:00:00',0.00,99,75.00),(154,'MAT',NULL,NULL,'00:00:00',0.00,100,45.00),(155,'VES',NULL,NULL,'00:00:00',0.00,100,75.00),(156,'MAT',NULL,NULL,'00:00:00',0.00,101,45.00),(157,'VES',NULL,NULL,'00:00:00',0.00,101,75.00),(158,'MAT',NULL,NULL,'00:00:00',0.00,102,45.00),(159,'VES','14:55:00','20:00:00','05:00:00',40.00,102,0.00),(160,'MAT','10:55:00','13:00:00','02:05:00',16.67,103,13.75),(161,'VES','15:00:57','20:00:57','04:59:03',39.87,103,0.24),(162,'MAT',NULL,NULL,'00:00:00',0.00,104,75.00),(163,'VES',NULL,NULL,'00:00:00',0.00,104,45.00),(164,'MAT','10:15:57',NULL,'00:00:00',0.00,105,33.99),(165,'VES','14:00:00','16:00:00','02:00:00',16.00,105,15.00),(166,'MAT','08:00:00','11:00:00','03:00:00',24.00,106,30.00),(167,'VES','15:00:00','16:00:00','01:00:00',8.00,106,30.00),(168,'MAT','08:00:00','13:00:00','05:00:00',40.00,107,0.00),(169,'VES','14:00:00','17:00:00','03:00:00',24.00,107,0.00),(170,'MAT','08:30:00','13:00:00','04:30:00',36.00,108,7.50),(171,'VES','14:30:00','17:00:00','02:30:00',20.00,108,7.50),(172,'MAT','10:00:00','13:00:00','03:00:00',24.00,109,0.00),(173,'VES','15:00:00','20:00:00','05:00:00',40.00,109,0.00),(174,'MAT','10:34:32','12:14:27','01:39:55',13.32,110,20.02),(175,'VES','15:01:27','20:00:01','04:58:33',39.81,110,0.36),(176,'MAT','12:00:01','12:30:01','00:30:00',4.00,111,37.50),(177,'VES','15:03:01',NULL,'00:00:00',0.00,111,0.75),(178,'MAT','10:34:01','13:04:01','02:25:59',19.46,112,8.50),(179,'VES','14:52:01','19:12:01','04:12:01',33.60,112,12.00);
/*!40000 ALTER TABLE `detalle_asistencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleados`
--

DROP TABLE IF EXISTS `empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleados` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `cedula` varchar(10) NOT NULL,
  `password` varchar(100) NOT NULL,
  `estado` varchar(100) NOT NULL,
  `rol` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleados`
--

LOCK TABLES `empleados` WRITE;
/*!40000 ALTER TABLE `empleados` DISABLE KEYS */;
INSERT INTO `empleados` VALUES (1,'Ismael','Sailema','123','123','ACT','administrador'),(14,'Alison','Salas','3423456789','3423456789','ACT','empleado'),(15,'jose','perez','1111114411','1111111111','INA','empleado'),(16,'maria','sanchez','1111122221','1111122221','INA','empleado'),(17,'maria','sanchez','1111111111','1111111111','ACT','empleado'),(21,'Pedro','Acosta','3245678788','3245678788','ACT','empleado'),(22,'jose','perez','8765435678','8765435678','ACT','empleado'),(23,'Ismael','Sailema','1804904058','1804904058','ACT','empleado'),(24,'Fernando','Torres','1801234568','1801234568','ACT','empleado'),(25,'Lucia','Ferrerira','1425361235','1425361235','ACT','empleado'),(26,'Ismael','Sailema','1804904059','1804904059','ACT','empleado'),(27,'Francisco','Rojas','1801234569','1801234569','ACT','empleado'),(28,'Ismael','Gavilanez','1804904055','1804904055','ACT','empleado'),(29,'Xavier','Linares','1234567895','1234567895','ACT','empleado'),(30,'Alison','Salas','1234567896','1234567896','ACT','empleado'),(31,'Rafael','Soriano','12344321','12344321','ACT','empleado'),(32,'Freddy','Alvarez','7777777777','7777777777','ACT','empleado'),(33,'sadf','asdf','8787878787','8787878787','ACT','empleado'),(34,'Julio','Perez','0123654987','0123654987','ACT','empleado');
/*!40000 ALTER TABLE `empleados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horarios`
--

DROP TABLE IF EXISTS `horarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `horarios` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `entrada` time NOT NULL,
  `salida` time NOT NULL,
  `jornada` varchar(3) NOT NULL,
  `id_empleado` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_empleado` (`id_empleado`),
  CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horarios`
--

LOCK TABLES `horarios` WRITE;
/*!40000 ALTER TABLE `horarios` DISABLE KEYS */;
INSERT INTO `horarios` VALUES (11,'08:00:00','13:00:00','MAT',14),(12,'14:00:00','17:00:00','VES',14),(13,'07:00:00','12:00:00','MAT',15),(14,'14:00:00','18:00:00','VES',15),(25,'08:00:00','13:00:00','MAT',21),(26,'14:00:00','17:00:00','VES',21),(27,'07:00:00','10:00:00','MAT',22),(28,'14:00:00','19:00:00','VES',22),(29,'07:00:00','11:00:00','MAT',23),(30,'15:00:00','19:00:00','VES',23),(31,'08:00:00','13:00:00','MAT',24),(32,'14:00:00','17:00:00','VES',24),(33,'07:00:00','13:00:00','MAT',25),(34,'14:00:00','16:00:00','VES',25),(35,'08:00:00','13:00:00','MAT',26),(36,'14:00:00','17:00:00','VES',26),(37,'08:00:00','13:00:00','MAT',27),(38,'14:00:00','18:00:00','VES',27),(39,'07:00:00','12:00:00','MAT',28),(40,'14:00:00','17:00:00','VES',28),(41,'07:00:00','12:00:00','MAT',29),(42,'14:00:00','17:00:00','VES',29),(43,'08:00:00','13:00:00','MAT',30),(44,'14:00:00','17:00:00','VES',30),(45,'08:00:00','13:00:00','MAT',31),(46,'14:00:00','17:00:00','VES',31),(47,'08:00:00','13:00:00','MAT',32),(48,'14:00:00','17:00:00','VES',32),(49,'07:00:00','12:00:00','MAT',33),(50,'14:00:00','17:00:00','VES',33),(51,'10:00:00','13:00:00','MAT',34),(52,'15:00:00','20:00:00','VES',34);
/*!40000 ALTER TABLE `horarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'asistencia-docente'
--
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-07-01 11:31:48
