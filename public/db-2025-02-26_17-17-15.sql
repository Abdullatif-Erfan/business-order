-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: business
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `business`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `business` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `business`;

--
-- Table structure for table `access_metrics`
--

DROP TABLE IF EXISTS `access_metrics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `access_metrics` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `access` text NOT NULL,
  `roleId` int(11) NOT NULL,
  `isDeleted` int(11) NOT NULL DEFAULT 0,
  `createdBy` int(11) NOT NULL,
  `createdDtm` datetime NOT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `access_metrics`
--

LOCK TABLES `access_metrics` WRITE;
/*!40000 ALTER TABLE `access_metrics` DISABLE KEYS */;
INSERT INTO `access_metrics` VALUES (1,'[{\"module\":\"dashboard\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"settings\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"journal\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"gen_buy\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"transfer\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"gudam\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"sales\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"reports\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"users\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0}]',1,0,1,'2025-02-23 18:22:07',1,'2025-02-23 18:22:07',NULL,'2025-02-23 13:52:07'),(7,'[{\"module\":\"dashboard\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"settings\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":1,\"edit_records\":0,\"delete_records\":0},{\"module\":\"journal\",\"label\":0,\"total_access\":0,\"list\":0,\"create_records\":1,\"edit_records\":0,\"delete_records\":0},{\"module\":\"gen_buy\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"transfer\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":1},{\"module\":\"gudam\",\"label\":0,\"total_access\":0,\"list\":0,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"sales\",\"label\":0,\"total_access\":0,\"list\":0,\"create_records\":1,\"edit_records\":0,\"delete_records\":0},{\"module\":\"reports\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"users\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0}]',2,0,1,'2025-02-23 13:01:23',1,'2025-02-23 13:01:23','2025-02-22 13:41:23','2025-02-23 08:31:23'),(8,'[{\"module\":\"dashboard\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"settings\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"journal\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"gen_buy\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"transfer\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"gudam\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"sales\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"reports\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"users\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0}]',3,0,1,'2025-02-22 18:16:59',1,'2025-02-22 18:16:59','2025-02-22 13:44:21','2025-02-22 13:46:59');
/*!40000 ALTER TABLE `access_metrics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `account_types`
--

DROP TABLE IF EXISTS `account_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_types`
--

LOCK TABLES `account_types` WRITE;
/*!40000 ALTER TABLE `account_types` DISABLE KEYS */;
INSERT INTO `account_types` VALUES (1,'1000','حساب جاری شرکت',NULL,NULL),(2,'2000','کارمندان',NULL,NULL),(3,'3000','مشتریان',NULL,NULL),(4,'4000','فروشنده گان',NULL,NULL),(5,'5000','سهم داران',NULL,NULL);
/*!40000 ALTER TABLE `account_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_type_id` bigint(20) unsigned NOT NULL,
  `branch_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_pre_select` int(11) NOT NULL DEFAULT 0,
  `percent` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `accounts_name_index` (`name`),
  KEY `accounts_account_type_id_index` (`account_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES (21,1,46,'خزانه مرکزی',NULL,NULL,NULL,0,NULL,'2025-02-04 10:24:28','2025-02-04 10:24:28'),(22,1,2,'خزانه ولایتی',NULL,NULL,NULL,0,NULL,'2025-02-04 10:26:11','2025-02-04 10:26:11'),(23,3,46,'احمدی','۰۹۰۹۰۹۰۰۹۰۰۹۹','کابل',NULL,0,NULL,'2025-02-04 11:34:37','2025-02-04 11:34:37'),(24,4,2,'کریم','۰۹۰۹۳۸۸۸۸۸۸','هرات',NULL,0,NULL,'2025-02-04 11:35:22','2025-02-04 11:35:22'),(25,4,2,'احمد احمدی',NULL,NULL,NULL,0,NULL,'2025-02-04 13:38:46','2025-02-04 13:38:46'),(26,5,46,'Abdul Latif Erfan','0708088185','Barchi, Kabul, Afghanistan',NULL,0,NULL,'2025-02-05 11:46:10','2025-02-05 11:46:10'),(27,4,46,'بشیر احمد',NULL,NULL,NULL,0,NULL,'2025-02-06 12:20:10','2025-02-06 12:20:10'),(28,1,46,'بانک مرکزی',NULL,NULL,NULL,1,NULL,'2025-02-07 12:27:06','2025-02-07 12:27:06'),(29,1,2,'ABDUL LATIF',NULL,NULL,NULL,0,NULL,'2025-02-24 07:00:17','2025-02-24 07:17:17'),(30,5,2,'ahmad',NULL,NULL,NULL,0,30,'2025-02-24 07:10:26','2025-02-24 07:10:35'),(31,2,2,'فواد قاسمی','0708088185','کابل',NULL,0,NULL,'2025-02-25 07:48:55','2025-02-25 07:48:55'),(32,2,2,'جواد عزیزی',NULL,NULL,NULL,0,NULL,'2025-02-25 07:49:21','2025-02-25 07:49:21');
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `backups`
--

DROP TABLE IF EXISTS `backups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `backups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `times` int(11) NOT NULL,
  `dates` varchar(100) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backups`
--

LOCK TABLES `backups` WRITE;
/*!40000 ALTER TABLE `backups` DISABLE KEYS */;
INSERT INTO `backups` VALUES (9,'بک اپ بعد از ختم','db-2025-02-26_16-21-23.sql','D:\\installed\\xamp2\\htdocs\\Business\\business\\storage\\app/backups/db-2025-02-26_16-21-23.sql',1740586883,'1403-12-08 16:21:23','Abdul Latif','2025-02-26 11:51:23','2025-02-26 11:51:23'),(10,'بک اپ دیتابیس','db-2025-02-26_16-52-36.sql','/storage/backups/db-2025-02-26_16-52-36.sql',1740588756,'1403-12-08 16:52:36','Abdul Latif','2025-02-26 12:22:36','2025-02-26 12:22:36'),(11,'بک اپ دیتابیس','db-2025-02-26_16-56-01.sql','/storage/backups/db-2025-02-26_16-56-01.sql',1740588961,'1403-12-08 16:56:01','Abdul Latif','2025-02-26 12:26:01','2025-02-26 12:26:01'),(12,'بک اپ بعد از ختم','db-2025-02-26_16-57-46.sql','/storage/backups/db-2025-02-26_16-57-46.sql',1740589066,'1403-12-08 16:57:46','Abdul Latif','2025-02-26 12:27:46','2025-02-26 12:27:46'),(13,'بک اپ دیتابیس','db-2025-02-26_16-59-47.sql','D:\\installed\\xamp2\\htdocs\\Business\\business\\storage\\app/backups/db-2025-02-26_16-59-47.sql',1740589187,'1403-12-08 16:59:47','Abdul Latif','2025-02-26 12:29:47','2025-02-26 12:29:47'),(14,'بک اپ دیتابیس','db-2025-02-26_17-11-01.sql','/storage/backups/db-2025-02-26_17-11-01.sql',1740589861,'1403-12-08 17:11:01','Abdul Latif','2025-02-26 12:41:01','2025-02-26 12:41:01'),(15,'بک اپ بعد از ختم','db-2025-02-26_17-11-52.sql','/storage/backups/db-2025-02-26_17-11-52.sql',1740589912,'1403-12-08 17:11:52','Abdul Latif','2025-02-26 12:41:52','2025-02-26 12:41:52'),(16,'بک اپ دیتابیس','db-2025-02-26_17-17-15.sql','/storage/backups/db-2025-02-26_17-17-15.sql',1740590235,'1403-12-08 17:17:15','Abdul Latif','2025-02-26 12:47:15','2025-02-26 12:47:15');
/*!40000 ALTER TABLE `backups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bought_item_details`
--

DROP TABLE IF EXISTS `bought_item_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bought_item_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `billno` int(11) DEFAULT NULL,
  `bought_item_id` bigint(20) unsigned NOT NULL,
  `pre_list_id` bigint(20) unsigned DEFAULT NULL,
  `customer_account_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `bought_up` decimal(10,2) DEFAULT NULL,
  `sell_up` decimal(10,2) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `transport` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `is_moved` int(11) NOT NULL,
  `expire_date` varchar(255) DEFAULT NULL,
  `times` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=177 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bought_item_details`
--

LOCK TABLES `bought_item_details` WRITE;
/*!40000 ALTER TABLE `bought_item_details` DISABLE KEYS */;
INSERT INTO `bought_item_details` VALUES (171,1,158,8,23,2.00,22.00,NULL,6,0.00,0.00,44.00,1,NULL,'1740334541','2025-02-23 13:46:02','2025-02-23 13:46:02'),(172,2,159,8,27,40.00,80.00,NULL,7,47.00,37.00,3200.00,1,'1403-12-23','1740364043','2025-02-23 21:58:25','2025-02-23 21:58:25'),(173,3,160,7,24,20.00,1000.00,NULL,6,0.00,0.00,20000.00,1,NULL,'1740477502','2025-02-25 05:29:00','2025-02-25 05:29:00'),(174,4,161,8,24,20.00,300.00,NULL,7,0.00,0.00,6000.00,1,NULL,'1740477560','2025-02-25 05:30:04','2025-02-25 05:30:04'),(175,5,162,9,24,10.00,100.00,NULL,7,0.00,0.00,1000.00,1,NULL,'1740477645','2025-02-25 05:31:24','2025-02-25 05:31:24'),(176,5,162,10,24,5.00,500.00,NULL,8,0.00,0.00,2500.00,1,NULL,'1740477645','2025-02-25 05:32:00','2025-02-25 05:32:00');
/*!40000 ALTER TABLE `bought_item_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bought_item_pre_lists`
--

DROP TABLE IF EXISTS `bought_item_pre_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bought_item_pre_lists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bought_item_pre_lists`
--

LOCK TABLES `bought_item_pre_lists` WRITE;
/*!40000 ALTER TABLE `bought_item_pre_lists` DISABLE KEYS */;
INSERT INTO `bought_item_pre_lists` VALUES (7,46,'برنج','2025-02-07 07:41:11','2025-02-07 07:41:11'),(8,46,'چای','2025-02-07 07:41:22','2025-02-07 07:41:22'),(9,46,'چهارمغز','2025-02-07 07:41:40','2025-02-07 07:41:40'),(10,46,'کشمش','2025-02-07 07:42:03','2025-02-07 07:42:03'),(11,2,'پنادول','2025-02-07 07:42:19','2025-02-07 07:42:19'),(12,2,'فلاجیل','2025-02-07 07:42:33','2025-02-07 07:42:33'),(13,2,'امپی سلین','2025-02-07 07:44:11','2025-02-07 07:44:11'),(14,2,'مسکین','2025-02-07 07:49:14','2025-02-07 07:49:14'),(15,2,'دوای درد دندان','2025-02-07 07:49:42','2025-02-07 07:49:42');
/*!40000 ALTER TABLE `bought_item_pre_lists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bought_items`
--

DROP TABLE IF EXISTS `bought_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bought_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `billno` int(11) DEFAULT NULL,
  `factor` varchar(100) DEFAULT NULL,
  `journal_code` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL DEFAULT 0,
  `total_price` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `payable` decimal(10,2) DEFAULT NULL,
  `cur_pay` decimal(10,2) DEFAULT NULL,
  `remained` decimal(10,2) DEFAULT NULL,
  `account_id` bigint(20) unsigned NOT NULL,
  `customer_account_id` bigint(20) NOT NULL,
  `currency_id` bigint(20) unsigned NOT NULL,
  `trans_spend` decimal(10,2) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `idate` varchar(255) DEFAULT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `iby` varchar(255) DEFAULT NULL,
  `times` varchar(255) DEFAULT NULL,
  `is_cleared` int(2) NOT NULL COMMENT '0:not cleared, 1:cleared',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bought_items`
--

LOCK TABLES `bought_items` WRITE;
/*!40000 ALTER TABLE `bought_items` DISABLE KEYS */;
INSERT INTO `bought_items` VALUES (158,1,'0',0,46,44.00,0.00,44.00,44.00,0.00,22,23,1,0.00,'تصفیه گردید','1403-12-05',1403,12,5,'Abdul Latif','1740334541',1,'2025-02-23 13:46:02','2025-02-25 11:31:27'),(159,2,'fac-01',0,46,3200.00,47.00,3153.00,100.00,3053.00,28,27,1,37.00,'Total: 3153, Paid: 100, Remained: 3053.00','1403-12-06',1403,12,6,'Abdul Latif','1740364043',0,'2025-02-23 21:58:25','2025-02-23 21:58:39'),(160,3,'0',0,46,20000.00,0.00,20000.00,20000.00,0.00,28,24,1,0.00,'تصفیه گردید','1403-12-07',1403,12,7,'Abdul Latif','1740477502',1,'2025-02-25 05:29:00','2025-02-25 11:27:26'),(161,4,'0',0,46,6000.00,0.00,6000.00,6000.00,0.00,28,24,1,0.00,'تصفیه گردید','1403-12-07',1403,12,7,'Abdul Latif','1740477560',1,'2025-02-25 05:30:04','2025-02-25 11:27:26'),(162,5,'0',0,46,3500.00,0.00,3500.00,3500.00,0.00,28,24,1,0.00,'تصفیه گردید','1403-12-07',1403,12,7,'Abdul Latif','1740477645',1,'2025-02-25 05:31:24','2025-02-25 11:29:45');
/*!40000 ALTER TABLE `bought_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `branches_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES (2,'شعبه ولایت','2025-01-31 00:45:42','2025-01-31 00:45:42'),(46,'شعبه مرکزی','2025-02-04 10:07:34','2025-02-04 10:07:34');
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `business_types`
--

DROP TABLE IF EXISTS `business_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `business_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business_types`
--

LOCK TABLES `business_types` WRITE;
/*!40000 ALTER TABLE `business_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clearances`
--

DROP TABLE IF EXISTS `clearances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clearances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('buy','sell') NOT NULL DEFAULT 'buy',
  `from_account_id` bigint(20) unsigned NOT NULL,
  `to_account_id` bigint(20) unsigned NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `currency_id` bigint(20) unsigned NOT NULL,
  `details` varchar(255) NOT NULL,
  `bill_numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`bill_numbers`)),
  `dates` varchar(255) NOT NULL,
  `clearedBy` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clearances`
--

LOCK TABLES `clearances` WRITE;
/*!40000 ALTER TABLE `clearances` DISABLE KEYS */;
INSERT INTO `clearances` VALUES (5,'buy',24,24,11000.00,1,'','\"[3,4]\"','1403-12-07','Abdul Latif','2025-02-25 11:27:26','2025-02-25 11:27:26'),(6,'buy',24,24,70000.00,1,'','\"[5]\"','1403-12-07','Abdul Latif','2025-02-25 11:29:45','2025-02-25 11:29:45'),(7,'buy',23,23,24.00,1,'','\"[1]\"','1403-12-07','Abdul Latif','2025-02-25 11:31:27','2025-02-25 11:31:27'),(9,'sell',25,25,100.00,1,'','\"[1]\"','1403-12-07','Abdul Latif','2025-02-25 12:36:44','2025-02-25 12:36:44');
/*!40000 ALTER TABLE `clearances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currencies`
--

DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `currencies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `symbols` varchar(255) NOT NULL,
  `is_base` enum('yes','no') NOT NULL DEFAULT 'no',
  `color` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currencies`
--

LOCK TABLES `currencies` WRITE;
/*!40000 ALTER TABLE `currencies` DISABLE KEYS */;
INSERT INTO `currencies` VALUES (1,'افغانی','AFN','yes','#000000',NULL,'2025-02-01 11:26:28'),(2,'دالر','USD','no','#ad5605','2025-02-01 10:27:54','2025-02-01 11:39:19');
/*!40000 ALTER TABLE `currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) unsigned NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `customer_type` enum('employee','seller','provider','customer','partner','other') NOT NULL DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expense_types`
--

DROP TABLE IF EXISTS `expense_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expense_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_types`
--

LOCK TABLES `expense_types` WRITE;
/*!40000 ALTER TABLE `expense_types` DISABLE KEYS */;
INSERT INTO `expense_types` VALUES (1,'کرایه دوکان','2025-02-24 08:53:26','2025-02-24 08:53:26'),(2,'معاشات کارمندان','2025-02-24 08:53:36','2025-02-24 08:53:36'),(3,'بل برق','2025-02-24 08:53:47','2025-02-24 08:53:47');
/*!40000 ALTER TABLE `expense_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `income_types`
--

DROP TABLE IF EXISTS `income_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `income_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `income_types`
--

LOCK TABLES `income_types` WRITE;
/*!40000 ALTER TABLE `income_types` DISABLE KEYS */;
INSERT INTO `income_types` VALUES (1,'عواید فروشات','2025-02-24 08:35:28','2025-02-24 08:35:28'),(2,'عواید پروژه ها','2025-02-24 08:35:37','2025-02-24 08:35:37'),(3,'عواید متفرقه','2025-02-24 08:35:44','2025-02-24 08:35:44'),(4,'عواید کرایه','2025-02-24 08:35:51','2025-02-24 08:35:51');
/*!40000 ALTER TABLE `income_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `journals`
--

DROP TABLE IF EXISTS `journals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `journals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` int(11) NOT NULL DEFAULT 0,
  `account_id` bigint(20) unsigned NOT NULL,
  `bill_no` int(11) DEFAULT 0,
  `amount` decimal(20,2) NOT NULL,
  `currency_id` bigint(20) unsigned NOT NULL,
  `transaction_type` int(11) NOT NULL COMMENT '1:recieved/income/increase/talab\r\n2:paid/outcome/decrease/baqi',
  `payment_type` int(11) NOT NULL COMMENT '1: cache, 2: loan',
  `inserted_full_date` varchar(30) DEFAULT NULL,
  `inserted_short_date` varchar(30) DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `updated_full_date` varchar(30) DEFAULT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `doc` varchar(255) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL COMMENT '1: old journal, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:other',
  `branch_id` bigint(20) unsigned DEFAULT 0,
  `dynamic_type` int(11) DEFAULT NULL COMMENT 'has relation with income_type, expense_type, salary, ....',
  `rate` decimal(10,2) DEFAULT NULL,
  `profit` decimal(10,2) DEFAULT NULL,
  `is_cleared` int(11) DEFAULT 0 COMMENT '0: not cleared, 1:cleared',
  `cleared_round` int(11) DEFAULT 0,
  `times` varchar(255) NOT NULL DEFAULT '0',
  `is_single_record` int(11) NOT NULL DEFAULT 0 COMMENT '0:single, 1:pair',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journals`
--

LOCK TABLES `journals` WRITE;
/*!40000 ALTER TABLE `journals` DISABLE KEYS */;
INSERT INTO `journals` VALUES (49,4,26,0,666.00,1,1,1,NULL,'1403-11-17',1,NULL,1403,11,17,NULL,'رسید حساب سابقه',1,46,0,NULL,NULL,0,0,'1738772170',0,'2025-02-05 11:50:56','2025-02-05 11:50:56'),(50,3,25,0,8000.00,1,2,1,NULL,'1403-11-17',1,NULL,1403,11,17,NULL,'رسید حساب سابقه',1,2,0,NULL,NULL,0,0,'1738692526',0,'2025-02-05 11:51:01','2025-02-05 11:51:01'),(51,3,25,0,555.00,1,1,1,NULL,'1403-11-17',1,NULL,1403,11,17,NULL,'رسید حساب سابقه',1,2,0,NULL,NULL,0,0,'1738692526',0,'2025-02-05 11:51:01','2025-02-05 11:51:01'),(52,2,24,0,5000.00,1,2,1,NULL,'1403-11-17',1,NULL,1403,11,17,NULL,'رسید حساب سابقه',1,2,0,NULL,NULL,0,0,'1738685122',0,'2025-02-05 11:51:20','2025-02-05 11:51:20'),(53,2,24,0,1000.00,2,1,1,NULL,'1403-11-17',1,NULL,1403,11,17,NULL,'رسید حساب سابقه',1,2,0,NULL,NULL,0,0,'1738685122',0,'2025-02-05 11:51:20','2025-02-05 11:51:20'),(54,2,22,0,50000.00,1,1,1,NULL,'1403-11-17',1,NULL,1403,11,17,NULL,'رسید حساب سابقه',1,2,0,NULL,NULL,0,0,'1738680971',0,'2025-02-05 11:51:28','2025-02-05 11:51:28'),(55,1,21,0,100000.00,1,1,1,NULL,'1403-11-17',1,NULL,1403,11,17,NULL,'رسید حساب سابقه',1,46,0,NULL,NULL,0,0,'1738680869',0,'2025-02-05 11:51:32','2025-02-05 11:51:32'),(56,1,21,0,2000.00,2,1,1,NULL,'1403-11-17',1,NULL,1403,11,17,NULL,'رسید حساب سابقه',1,46,0,NULL,NULL,0,0,'1738680869',0,'2025-02-05 11:51:32','2025-02-05 11:51:32'),(69,10,28,0,25000.00,1,1,1,NULL,'1403-11-19',1,NULL,1403,11,19,NULL,'رسید حساب سابقه',1,46,0,NULL,NULL,0,0,'1738947426',0,'2025-02-07 12:27:06','2025-02-07 12:27:06'),(86,5,27,0,8000.00,1,1,1,NULL,'1403-11-27',1,NULL,1403,11,27,NULL,'رسید حساب سابقه',1,46,0,NULL,NULL,0,0,'1738860610',0,'2025-02-15 06:40:55','2025-02-15 06:40:55'),(87,5,27,0,200.00,2,2,1,NULL,'1403-11-27',1,NULL,1403,11,27,NULL,'رسید حساب سابقه',1,46,0,NULL,NULL,0,0,'1738860610',0,'2025-02-15 06:40:55','2025-02-15 06:40:55'),(115,11,21,0,5000.00,1,2,1,'1403-12-06 03:31:31 AM','1403-12-06',1,NULL,1403,12,6,NULL,'پرداخت کننده قرض',2,46,0,NULL,NULL,0,0,'1740367891',1,'2025-02-23 23:01:31','2025-02-23 23:01:31'),(116,11,24,NULL,5000.00,1,1,1,'1403-12-06 03:31:31 AM','1403-12-06',1,NULL,1403,12,6,NULL,'دریافت کننده قرض',2,46,0,NULL,NULL,0,0,'1740367891',1,'2025-02-23 23:01:31','2025-02-23 23:01:31'),(120,12,29,0,2222.00,1,1,1,NULL,'1403-12-6',1,NULL,1403,12,6,NULL,'رسید حساب سابقه',1,2,0,NULL,NULL,0,0,'1740396617',0,'2025-02-24 07:17:17','2025-02-24 07:17:17'),(125,14,22,0,4000.00,1,2,1,'1403-12-07 05:44:32 PM','1403-12-07',1,NULL,1403,12,7,NULL,'kkkkkkk',2,2,0,NULL,NULL,0,0,'1740460875',1,'2025-02-25 00:51:15','2025-02-25 13:14:32'),(126,14,24,NULL,4000.57,1,1,1,'1403-12-07 05:44:32 PM','1403-12-07',1,NULL,1403,12,7,NULL,'uuuuuuuuuuuaaa',2,2,0,NULL,NULL,0,0,'1740460875',1,'2025-02-25 00:51:15','2025-02-25 13:14:32'),(128,15,28,0,555.00,1,1,1,'1403-12-08 05:52:41 AM','1403-12-08',1,NULL,1403,12,8,'documents/rTIpNU5Vuf3VWTanKkoBziQhz28UT9zrB435uUjT.jpg','ملاحظات ثبت شد',3,2,1,NULL,NULL,0,0,'1740549161',0,'2025-02-26 01:22:41','2025-02-26 01:22:41'),(131,16,28,0,5555.00,1,2,1,'1403-12-08 06:29:47 AM','1403-12-08',1,NULL,1403,12,8,NULL,'ملاحظات ثبت شد',4,2,3,NULL,NULL,0,0,'1740551387',0,'2025-02-26 01:59:47','2025-02-26 01:59:47');
/*!40000 ALTER TABLE `journals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `last_logins`
--

DROP TABLE IF EXISTS `last_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `last_logins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userId` bigint(20) NOT NULL,
  `sessionData` varchar(255) NOT NULL,
  `machineIp` varchar(255) NOT NULL,
  `userAgent` varchar(255) NOT NULL,
  `agentString` varchar(255) NOT NULL,
  `platform` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `last_logins`
--

LOCK TABLES `last_logins` WRITE;
/*!40000 ALTER TABLE `last_logins` DISABLE KEYS */;
/*!40000 ALTER TABLE `last_logins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(5,'2025_01_24_063831_create_account_types_table',1),(6,'2025_01_24_063844_create_backups_table',1),(7,'2025_01_24_063910_create_bought_items_table',1),(8,'2025_01_24_063930_create_bought_item_details_table',1),(9,'2025_01_24_063944_create_bought_item_pre_lists_table',1),(10,'2025_01_24_063955_create_branches_table',1),(11,'2025_01_24_064013_create_business_types_table',1),(12,'2025_01_24_064036_create_currencies_table',1),(14,'2025_01_24_064101_create_journals_table',1),(15,'2025_01_24_064117_create_org_bios_table',1),(16,'2025_01_24_064127_create_packages_table',1),(17,'2025_01_24_064137_create_rates_table',1),(18,'2025_01_24_064147_create_salaries_table',1),(19,'2025_01_24_064209_create_table_access_metrics_table',1),(20,'2025_01_24_064222_create_table_last_logins_table',1),(21,'2025_01_24_064244_create_table_reset_passwords_table',1),(22,'2025_01_24_064256_create_table_roles_table',1),(23,'2025_01_24_064308_create_table_users_table',1),(24,'2025_01_24_064317_create_units_table',1),(25,'2025_01_24_064347_create_warehouses_table',1),(26,'2025_01_24_064354_create_warehouse_items_table',1),(27,'2025_01_24_064402_create_warehouse_sales_table',1),(28,'2025_01_24_105520_create_personal_access_tokens_table',2),(29,'2025_01_24_063753_create_accounts_table',3),(30,'2025_01_24_131340_create_personal_access_tokens_table',4),(31,'2025_02_21_031315_create_sales_details',5),(32,'2025_02_24_115524_income_type',6),(33,'2025_02_24_115533_expense_type',6),(34,'2025_02_25_062744_create_clearances_table',7);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `org_bios`
--

DROP TABLE IF EXISTS `org_bios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `org_bios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `header` varchar(255) NOT NULL,
  `logos` varchar(255) NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1 COMMENT '0: not active, 1: active',
  `note_for_print` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `org_bios`
--

LOCK TABLES `org_bios` WRITE;
/*!40000 ALTER TABLE `org_bios` DISABLE KEYS */;
INSERT INTO `org_bios` VALUES (2,'خدمات نرم افزار کاوشگران','مرکز تجارتی داودزی - کابل - افغانستان','0729010123','headers/1740452014_header.jpg','logos/1740452014_sm-logo.png',1,'اجناس فروخته شده پس گرفته نمیشود. و قرض هم داده نمیشود',NULL,'2025-02-24 22:23:34');
/*!40000 ALTER TABLE `org_bios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `packages`
--

DROP TABLE IF EXISTS `packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `packages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `type` int(11) NOT NULL DEFAULT 1 COMMENT '1:lite, 2:business, 3:business+',
  `activated_by` varchar(255) DEFAULT NULL,
  `activated_date` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0: not active, 1:active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packages`
--

LOCK TABLES `packages` WRITE;
/*!40000 ALTER TABLE `packages` DISABLE KEYS */;
INSERT INTO `packages` VALUES (2,'lite',2,NULL,NULL,1,NULL,NULL);
/*!40000 ALTER TABLE `packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rates`
--

DROP TABLE IF EXISTS `rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_currency_id` bigint(20) unsigned NOT NULL,
  `from_currency_amount` decimal(10,2) NOT NULL DEFAULT 1.00,
  `to_currency_id` bigint(20) unsigned NOT NULL,
  `to_currency_amount` decimal(10,2) NOT NULL,
  `reverse_amount` decimal(10,2) NOT NULL,
  `greater_account_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rates`
--

LOCK TABLES `rates` WRITE;
/*!40000 ALTER TABLE `rates` DISABLE KEYS */;
/*!40000 ALTER TABLE `rates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `roleId` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `role` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `isDeleted` tinyint(4) NOT NULL DEFAULT 0,
  `createdBy` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`roleId`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Editor',1,0,0,NULL,'2025-02-22 13:47:26'),(2,'ادمین',1,0,1,'2025-02-22 08:21:30','2025-02-22 13:46:22'),(3,'سوپر ادمین',0,0,1,'2025-02-22 08:25:06','2025-02-22 09:25:53');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salaries`
--

DROP TABLE IF EXISTS `salaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salaries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `currency_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `inserted_by` varchar(255) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salaries`
--

LOCK TABLES `salaries` WRITE;
/*!40000 ALTER TABLE `salaries` DISABLE KEYS */;
/*!40000 ALTER TABLE `salaries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_details`
--

DROP TABLE IF EXISTS `sales_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sales_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `billno` int(11) DEFAULT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `warehouse_sales_id` bigint(20) unsigned NOT NULL,
  `pre_list_id` bigint(20) unsigned DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `avg_up` decimal(10,2) DEFAULT NULL,
  `sell_up` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `profit` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `is_returned` int(11) NOT NULL DEFAULT 0 COMMENT '0:not returned, 1:returned',
  `todays_date` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_details`
--

LOCK TABLES `sales_details` WRITE;
/*!40000 ALTER TABLE `sales_details` DISABLE KEYS */;
INSERT INTO `sales_details` VALUES (12,1,46,14,28,8,7,2.00,80.00,120.00,0.00,80.00,240.00,0,'1403-12-06','2025-02-23 21:59:41','2025-02-23 21:59:41'),(13,1,46,15,28,8,7,3.00,80.00,130.00,10.00,150.00,390.00,0,'1403-12-06','2025-02-23 21:59:41','2025-02-23 21:59:41');
/*!40000 ALTER TABLE `sales_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('Fznu8tfs0TvQfi1kE5SuVCLZ2HIYUa2sjBZgP8Ma',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36','YToxMjp7czo2OiJfdG9rZW4iO3M6NDA6InB0NklOZzZSeTNaWTBoNjdqZ2tvb25iUnpDQ3RMdVUzeVVnNEZ5Q1IiO3M6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9iYWNrdXBzIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9iYWNrdXBzL2Rvd25sb2FkLzE1Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo2OiJ1c2VySWQiO2k6MTtzOjQ6InJvbGUiO2k6MTtzOjg6InJvbGVUZXh0IjtzOjY6IkVkaXRvciI7czo0OiJuYW1lIjtzOjExOiJBYmR1bCBMYXRpZiI7czo3OiJpc0FkbWluIjtpOjE7czoxMDoiYWNjZXNzSW5mbyI7YTo5OntzOjk6ImRhc2hib2FyZCI7YTo3OntzOjY6Im1vZHVsZSI7czo5OiJkYXNoYm9hcmQiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MDtzOjQ6Imxpc3QiO2k6MTtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aToxO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6ODoic2V0dGluZ3MiO2E6Nzp7czo2OiJtb2R1bGUiO3M6ODoic2V0dGluZ3MiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MDtzOjQ6Imxpc3QiO2k6MTtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aToxO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6Nzoiam91cm5hbCI7YTo3OntzOjY6Im1vZHVsZSI7czo3OiJqb3VybmFsIjtzOjU6ImxhYmVsIjtpOjA7czoxMjoidG90YWxfYWNjZXNzIjtpOjA7czo0OiJsaXN0IjtpOjE7czoxNDoiY3JlYXRlX3JlY29yZHMiO2k6MDtzOjEyOiJlZGl0X3JlY29yZHMiO2k6MTtzOjE0OiJkZWxldGVfcmVjb3JkcyI7aTowO31zOjc6Imdlbl9idXkiO2E6Nzp7czo2OiJtb2R1bGUiO3M6NzoiZ2VuX2J1eSI7czo1OiJsYWJlbCI7aTowO3M6MTI6InRvdGFsX2FjY2VzcyI7aTowO3M6NDoibGlzdCI7aToxO3M6MTQ6ImNyZWF0ZV9yZWNvcmRzIjtpOjA7czoxMjoiZWRpdF9yZWNvcmRzIjtpOjE7czoxNDoiZGVsZXRlX3JlY29yZHMiO2k6MDt9czo4OiJ0cmFuc2ZlciI7YTo3OntzOjY6Im1vZHVsZSI7czo4OiJ0cmFuc2ZlciI7czo1OiJsYWJlbCI7aTowO3M6MTI6InRvdGFsX2FjY2VzcyI7aTowO3M6NDoibGlzdCI7aToxO3M6MTQ6ImNyZWF0ZV9yZWNvcmRzIjtpOjA7czoxMjoiZWRpdF9yZWNvcmRzIjtpOjE7czoxNDoiZGVsZXRlX3JlY29yZHMiO2k6MDt9czo1OiJndWRhbSI7YTo3OntzOjY6Im1vZHVsZSI7czo1OiJndWRhbSI7czo1OiJsYWJlbCI7aTowO3M6MTI6InRvdGFsX2FjY2VzcyI7aTowO3M6NDoibGlzdCI7aToxO3M6MTQ6ImNyZWF0ZV9yZWNvcmRzIjtpOjA7czoxMjoiZWRpdF9yZWNvcmRzIjtpOjE7czoxNDoiZGVsZXRlX3JlY29yZHMiO2k6MDt9czo1OiJzYWxlcyI7YTo3OntzOjY6Im1vZHVsZSI7czo1OiJzYWxlcyI7czo1OiJsYWJlbCI7aTowO3M6MTI6InRvdGFsX2FjY2VzcyI7aTowO3M6NDoibGlzdCI7aToxO3M6MTQ6ImNyZWF0ZV9yZWNvcmRzIjtpOjA7czoxMjoiZWRpdF9yZWNvcmRzIjtpOjE7czoxNDoiZGVsZXRlX3JlY29yZHMiO2k6MDt9czo3OiJyZXBvcnRzIjthOjc6e3M6NjoibW9kdWxlIjtzOjc6InJlcG9ydHMiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MDtzOjQ6Imxpc3QiO2k6MTtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aToxO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6NToidXNlcnMiO2E6Nzp7czo2OiJtb2R1bGUiO3M6NToidXNlcnMiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MDtzOjQ6Imxpc3QiO2k6MTtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aToxO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fX1zOjEwOiJpc0xvZ2dlZEluIjtiOjE7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9',1740590040);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `table_reset_passwords`
--

DROP TABLE IF EXISTS `table_reset_passwords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `table_reset_passwords` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `activation_id` varchar(255) NOT NULL,
  `agent` varchar(255) NOT NULL,
  `client_ip` varchar(255) NOT NULL,
  `isDeleted` tinyint(4) NOT NULL DEFAULT 0,
  `createdBy` bigint(20) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `table_reset_passwords`
--

LOCK TABLES `table_reset_passwords` WRITE;
/*!40000 ALTER TABLE `table_reset_passwords` DISABLE KEYS */;
/*!40000 ALTER TABLE `table_reset_passwords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `table_users`
--

DROP TABLE IF EXISTS `table_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `table_users` (
  `userId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `name` varchar(128) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `roleId` tinyint(4) NOT NULL,
  `isAdmin` tinyint(4) NOT NULL DEFAULT 2,
  `isDeleted` tinyint(4) NOT NULL DEFAULT 0,
  `isHidden` int(11) NOT NULL DEFAULT 0,
  `photo` varchar(255) DEFAULT NULL,
  `createdBy` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `table_users`
--

LOCK TABLES `table_users` WRITE;
/*!40000 ALTER TABLE `table_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `table_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `units` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `units`
--

LOCK TABLES `units` WRITE;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;
INSERT INTO `units` VALUES (3,'دانه','2025-01-31 12:34:42','2025-01-31 12:34:42'),(4,'عدد','2025-01-31 12:38:33','2025-01-31 13:00:55'),(6,'بوجی','2025-02-01 11:22:22','2025-02-01 11:22:33'),(7,'کیلو','2025-02-19 06:50:04','2025-02-19 06:50:04'),(8,'سیر','2025-02-19 06:50:09','2025-02-19 06:50:09'),(9,'چارک','2025-02-19 06:50:16','2025-02-24 02:23:07');
/*!40000 ALTER TABLE `units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `roleId` tinyint(4) NOT NULL,
  `isAdmin` tinyint(4) NOT NULL DEFAULT 2,
  `isDeleted` tinyint(4) NOT NULL DEFAULT 0,
  `isHidden` int(11) NOT NULL DEFAULT 0,
  `photo` varchar(255) DEFAULT NULL,
  `createdBy` int(11) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Abdul Latif','erfan','erfan@gmail.com',NULL,'$2y$12$fbZdLVT5uWkGd603xet2.eqocp/.vY8X12FYUyEJudwByj.mW/bqy',1,1,0,0,'upload/users/kaaweshgaraan_2024-Dec-Tue.jpg',1,NULL,'2025-01-24 09:25:13','2025-02-23 13:55:24'),(2,'Honorato Stafford','bygokexo','nyhopucacu@mailinator.com',NULL,'$2y$12$Ggrz4eOMfhelQtS5zMitAO9X1WrY8iMpd5SF.7I8Lzg4o5B7SFBIy',2,1,0,0,NULL,1,NULL,'2025-02-23 00:03:44','2025-02-23 00:03:44'),(3,'Alfreda Hampton','dybepyzexy','wynyh@mailinator.com',NULL,'$2y$12$3OHIwJVjw2SPEq7kOB1Nn.3uAMOw9TQj3EUAj07OKhqogLoOClF8.',1,1,0,0,NULL,1,NULL,'2025-02-23 00:04:18','2025-02-23 00:04:18'),(5,'Scarlet Velez','rovuc','lidap@mailinator.com',NULL,'$2y$12$um.YWwzDUm8/fPMIhxbOqOIYiVx.Rm0A.sa9Nbx.j1nWL/AxHHcSe',1,0,0,0,'Z3VhlwJ4hjMr4Sra0Mojcq4EUC50wzJIPCGOgjAU.jpg',1,NULL,'2025-02-23 00:55:51','2025-02-23 02:48:55'),(7,'Breanna Fulton2','rasanefum','caken@mailinator.com',NULL,'$2y$12$1hmCZ0QVYM8tcdLYNjoAburZtV3..K8Paq.wFib8oI3biVJjx4ccK',2,0,0,0,'user_photos/b7dR153Q8uaf7PFLCAI0BxdHpxT4BDSX3WOMYdgV.jpg',1,NULL,'2025-02-23 00:57:25','2025-02-23 02:01:29');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouse_items`
--

DROP TABLE IF EXISTS `warehouse_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warehouse_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` bigint(20) unsigned NOT NULL,
  `buy_pre_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `in_amount` decimal(10,2) DEFAULT NULL,
  `out_amount` double(10,2) DEFAULT NULL,
  `available_amount` decimal(10,2) NOT NULL,
  `wastage_amount` decimal(10,2) DEFAULT NULL,
  `wastage_total` decimal(10,2) NOT NULL,
  `unit_id` bigint(20) unsigned NOT NULL,
  `bought_up` decimal(10,2) DEFAULT NULL COMMENT 'buy unit price',
  `avg_up` decimal(10,2) NOT NULL COMMENT 'averate unit price',
  `sell_up` decimal(10,2) DEFAULT NULL COMMENT 'selling unit price',
  `total` decimal(10,2) NOT NULL,
  `available_total` decimal(10,2) NOT NULL,
  `currency_id` bigint(20) unsigned NOT NULL,
  `notification_amount` int(11) DEFAULT NULL,
  `inserted_by` varchar(100) DEFAULT NULL COMMENT 'user_id',
  `expire_date` varchar(100) DEFAULT NULL,
  `inserted_short_date` varchar(30) NOT NULL,
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `day` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `times` int(11) DEFAULT NULL,
  `is_cleared` int(2) NOT NULL COMMENT '0: not cleared, 1:cleared',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=190 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouse_items`
--

LOCK TABLES `warehouse_items` WRITE;
/*!40000 ALTER TABLE `warehouse_items` DISABLE KEYS */;
INSERT INTO `warehouse_items` VALUES (183,14,7,'Unknown Item',30.00,0.00,30.00,0.00,0.00,6,1000.00,666.67,1200.00,21000.00,20000.00,1,0,'Abdul Latif',NULL,'1403-12-04',1403,12,4,NULL,'2025-02-25 05:29:01',1740477502,0),(184,15,7,'Unknown Item',21.00,11.00,10.00,0.00,0.00,6,100.00,100.00,1200.00,1000.00,1200.00,1,0,'Abdul Latif',NULL,'1403-12-04',1403,12,4,NULL,'2025-02-22 07:07:25',1740199254,0),(185,14,8,'Unknown Item',2.00,0.00,2.00,0.00,0.00,6,22.00,22.00,22.00,44.00,44.00,1,0,'Abdul Latif',NULL,'1403-12-05',1403,12,5,NULL,NULL,1740334541,0),(186,14,8,'Unknown Item',40.00,2.00,38.00,0.00,0.00,7,300.00,200.00,400.00,7600.00,7600.00,1,0,'Abdul Latif',NULL,'1403-12-06',1403,12,6,NULL,'2025-02-25 05:30:04',1740477560,0),(187,15,8,'Unknown Item',20.00,3.00,17.00,0.00,0.00,7,80.00,80.00,130.00,1600.00,1600.00,1,52,'Abdul Latif','1403-12-23','1403-12-06',1403,12,6,NULL,'2025-02-23 21:59:41',1740364043,0),(188,15,9,'Unknown Item',10.00,0.00,10.00,0.00,0.00,7,100.00,100.00,120.00,1000.00,1000.00,1,0,'Abdul Latif',NULL,'1403-12-07',1403,12,7,NULL,NULL,1740477645,0),(189,14,10,'Unknown Item',5.00,0.00,5.00,0.00,0.00,8,500.00,500.00,600.00,2500.00,2500.00,1,0,'Abdul Latif',NULL,'1403-12-07',1403,12,7,NULL,NULL,1740477645,0);
/*!40000 ALTER TABLE `warehouse_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouse_sales`
--

DROP TABLE IF EXISTS `warehouse_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warehouse_sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `billno` int(11) DEFAULT NULL,
  `factor` varchar(100) DEFAULT NULL,
  `account_id` bigint(20) unsigned NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `customer_account_id` bigint(20) unsigned NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `total_discount` decimal(10,2) DEFAULT NULL,
  `payable` decimal(10,2) NOT NULL,
  `cur_pay` decimal(10,2) NOT NULL,
  `remained` decimal(10,2) NOT NULL,
  `currency_id` bigint(20) unsigned NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `short_date` varchar(100) NOT NULL,
  `ifull_date` varchar(100) DEFAULT NULL,
  `iby` varchar(100) DEFAULT NULL,
  `uby` varchar(100) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `day` int(11) DEFAULT NULL,
  `is_cleared` int(6) NOT NULL COMMENT '0: not cleared, 1:cleared',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouse_sales`
--

LOCK TABLES `warehouse_sales` WRITE;
/*!40000 ALTER TABLE `warehouse_sales` DISABLE KEYS */;
INSERT INTO `warehouse_sales` VALUES (28,1,NULL,28,46,25,630.00,10.00,620.00,620.00,0.00,1,'تصفیه گردید','1403-12-06','1403-12-06 02:29:41 AM','Abdul Latif','',1403,12,6,1,'2025-02-23 21:59:41','2025-02-25 12:36:44');
/*!40000 ALTER TABLE `warehouse_sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouses`
--

DROP TABLE IF EXISTS `warehouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warehouses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `responsible` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouses`
--

LOCK TABLES `warehouses` WRITE;
/*!40000 ALTER TABLE `warehouses` DISABLE KEYS */;
INSERT INTO `warehouses` VALUES (14,'گدام مرکزی',2,'احمد','هرات','2025-02-09 12:15:56','2025-02-09 12:15:56'),(15,'فروشگاه',46,'محمود','کابل - افغانستان','2025-02-09 12:16:25','2025-02-09 12:16:25');
/*!40000 ALTER TABLE `warehouses` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-02-26 21:47:16
