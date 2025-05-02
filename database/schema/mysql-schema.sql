/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mcdrnew`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mcdrnew` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `DateEvent` date DEFAULT NULL,
  `ACtype` varchar(15) DEFAULT NULL,
  `Reg` varchar(15) DEFAULT NULL,
  `FlightNo` varchar(8) DEFAULT NULL,
  `DepSta` varchar(3) DEFAULT NULL,
  `ArivSta` varchar(3) DEFAULT NULL,
  `DCP` varchar(1) DEFAULT NULL,
  `Aog` varchar(3) DEFAULT NULL,
  `HoursTot` int DEFAULT NULL,
  `MinTot` int DEFAULT NULL,
  `FDD` varchar(5) DEFAULT NULL,
  `RtABO` varchar(3) DEFAULT NULL,
  `Iata` varchar(8) DEFAULT NULL,
  `ATAtdm` int DEFAULT NULL,
  `SubATAtdm` varchar(2) DEFAULT NULL,
  `HoursTek` int DEFAULT NULL,
  `MinTek` int DEFAULT NULL,
  `Problem` longtext,
  `Rectification` longtext,
  `LastRectification` longtext,
  `KeyProblem` varchar(100) DEFAULT NULL,
  `Chargeability` varchar(50) DEFAULT NULL,
  `RootCause` varchar(100) DEFAULT NULL,
  `Maintenance_Action` longtext,
  `EventID` int DEFAULT NULL,
  `SDR` varchar(20) DEFAULT NULL,
  `Avoidable_Unavoidable` varchar(100) DEFAULT NULL,
  `ATAdelay` varchar(50) DEFAULT NULL,
  `DateEvent1` varchar(50) DEFAULT NULL,
  `TimeCode` varchar(50) DEFAULT NULL,
  `WorkshopReliability` varchar(100) DEFAULT NULL,
  `UpdateDateTER` date DEFAULT NULL,
  `CreateDateSwift` date DEFAULT NULL,
  `UpdateDateTO` datetime DEFAULT NULL,
  `DateInsertTO` datetime DEFAULT NULL,
  `status_review` varchar(10) DEFAULT NULL,
  `user_review` varchar(100) DEFAULT NULL,
  `Remark` longtext,
  `Contributing_Factor` longtext,
  `Category` longtext,
  PRIMARY KEY (`ID`),
  KEY `mcdrnew_eventid_index` (`EventID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`),
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tbl_alertlevel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_alertlevel` (
  `id` int DEFAULT NULL,
  `actype` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ata` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `startmonth` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endmonth` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alertlevel` decimal(12,10) DEFAULT NULL,
  `NOTE` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tbl_master_ata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_master_ata` (
  `ATA` int NOT NULL,
  `ATA_DESC` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ATA`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tbl_masterac`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_masterac` (
  `IDreg` int NOT NULL,
  `IDType` int DEFAULT NULL,
  `ACType` varchar(50) DEFAULT NULL,
  `ACReg` varchar(50) DEFAULT NULL,
  `Operator` varchar(30) DEFAULT NULL,
  `SerialModule` varchar(50) DEFAULT NULL,
  `VariableNumber` varchar(50) DEFAULT NULL,
  `SerialNumber` int DEFAULT NULL,
  `ManufYear` datetime DEFAULT NULL,
  `DEliveryDate` datetime DEFAULT NULL,
  `EngineType` varchar(50) DEFAULT NULL,
  `Lessor` varchar(50) DEFAULT NULL,
  `Active` int DEFAULT NULL,
  PRIMARY KEY (`IDreg`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tbl_monthlyfhfc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_monthlyfhfc` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `IDReg` int DEFAULT NULL,
  `Reg` varchar(255) DEFAULT NULL,
  `Actype` varchar(255) DEFAULT NULL,
  `RevBHHours` int DEFAULT NULL,
  `RevBHMin` int DEFAULT NULL,
  `RevFHHours` int DEFAULT NULL,
  `RevFHMin` int DEFAULT NULL,
  `RevFC` int DEFAULT NULL,
  `NoRevBHHours` int DEFAULT NULL,
  `NoRevBHMin` int DEFAULT NULL,
  `NoRevFHHours` int DEFAULT NULL,
  `NoRevFHMin` int DEFAULT NULL,
  `NoRevFC` int DEFAULT NULL,
  `MonthEval` date DEFAULT NULL,
  `AvaiDays` int DEFAULT NULL,
  `TSN` int DEFAULT NULL,
  `TSNMin` int DEFAULT NULL,
  `CSN` int DEFAULT NULL,
  `Remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `tbl_monthlyfhfc_actype_montheval_index` (`Actype`,`MonthEval`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tbl_sdr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_sdr` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ACTYPE` varchar(50) DEFAULT NULL,
  `Reg` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `DateOccur` date DEFAULT NULL,
  `FlightNo` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `ATA` int DEFAULT NULL,
  `Remark` varchar(50) DEFAULT NULL,
  `Problem` varchar(50) DEFAULT NULL,
  `Rectification` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tblpirep_swift`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tblpirep_swift` (
  `ID_new` int NOT NULL AUTO_INCREMENT,
  `Notification` varchar(255) DEFAULT NULL,
  `ACTYPE` varchar(255) DEFAULT NULL,
  `REG` varchar(255) DEFAULT NULL,
  `FN` varchar(255) DEFAULT NULL,
  `STADEP` varchar(255) DEFAULT NULL,
  `STAARR` varchar(255) DEFAULT NULL,
  `DATE` date DEFAULT NULL,
  `SEQ` double DEFAULT NULL,
  `DefectCode` varchar(4) DEFAULT NULL,
  `ATA` varchar(2) DEFAULT NULL,
  `SUBATA` varchar(2) DEFAULT NULL,
  `PROBLEM` varchar(1000) DEFAULT NULL,
  `Keyword` varchar(255) DEFAULT NULL,
  `ACTION` varchar(1000) DEFAULT NULL,
  `PirepMarep` varchar(31) DEFAULT NULL,
  `Month` varchar(7) DEFAULT NULL,
  `PN_in` varchar(25) DEFAULT NULL,
  `SN_in` varchar(25) DEFAULT NULL,
  `PN_out` varchar(25) DEFAULT NULL,
  `SN_out` varchar(25) DEFAULT NULL,
  `Created_on` date DEFAULT NULL,
  `Changed_on` date DEFAULT NULL,
  `update_date` date DEFAULT NULL,
  `ETOPSEvent` varchar(255) DEFAULT NULL,
  `GAForm` varchar(255) DEFAULT NULL,
  `ID_mcdrnew` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID_new`),
  KEY `tblpirep_swift_actype_date_pirepmarep_ata_index` (`ACTYPE`,`DATE`,`PirepMarep`,`ATA`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2024_11_07_074414_connect_mcdrnew_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2024_11_07_081057_connect_tbl_masterac_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2024_11_07_081728_connect_tbl_monthlyfhfc_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2024_11_07_085019_connect_tblpirep_swift_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2024_11_19_081016_connect_tbl_sdr_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2024_12_02_011532_connect_tbl_master_ata_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2024_12_06_031205_connet_tbl_alert_level_tabel',1);
