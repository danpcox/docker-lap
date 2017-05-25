CREATE DATABASE IF NOT EXISTS employees;
use employees;

DROP TABLE IF EXISTS `EMPLOYEE_COUNT`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `EMPLOYEE_COUNT` (
  `THE_DATE` date DEFAULT NULL,
  `SVP` varchar(30) DEFAULT NULL,
  `CONTRACTORS` int(11) DEFAULT NULL,
  `FULL_TIME` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
