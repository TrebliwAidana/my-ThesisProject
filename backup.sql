-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: mythesisproject_db
-- ------------------------------------------------------
-- Server version	8.4.3

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

--
-- Table structure for table `attachments`
--

DROP TABLE IF EXISTS `attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `attachable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachable_id` bigint unsigned NOT NULL,
  `document_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attachments_attachable_type_attachable_id_index` (`attachable_type`,`attachable_id`),
  KEY `attachments_document_id_foreign` (`document_id`),
  CONSTRAINT `attachments_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attachments`
--

LOCK TABLES `attachments` WRITE;
/*!40000 ALTER TABLE `attachments` DISABLE KEYS */;
INSERT INTO `attachments` VALUES (1,'App\\Models\\FinancialTransaction',1,28,'2026-05-04 17:34:03','2026-05-04 17:34:03'),(2,'App\\Models\\FinancialTransaction',2,29,'2026-05-04 17:34:03','2026-05-04 17:34:03'),(3,'App\\Models\\FinancialTransaction',3,30,'2026-05-04 17:34:03','2026-05-04 17:34:03'),(4,'App\\Models\\FinancialTransaction',4,31,'2026-05-04 17:34:03','2026-05-04 17:34:03'),(5,'App\\Models\\FinancialTransaction',5,32,'2026-05-04 17:34:04','2026-05-04 17:34:04'),(6,'App\\Models\\FinancialTransaction',6,33,'2026-05-04 17:34:04','2026-05-04 17:34:04'),(7,'App\\Models\\FinancialTransaction',7,34,'2026-05-04 17:34:04','2026-05-04 17:34:04'),(8,'App\\Models\\FinancialTransaction',8,35,'2026-05-04 17:34:04','2026-05-04 17:34:04'),(9,'App\\Models\\FinancialTransaction',9,36,'2026-05-04 17:34:05','2026-05-04 17:34:05'),(10,'App\\Models\\FinancialTransaction',10,37,'2026-05-04 17:34:05','2026-05-04 17:34:05'),(11,'App\\Models\\FinancialTransaction',18,38,'2026-05-04 17:34:05','2026-05-04 17:34:05'),(12,'App\\Models\\FinancialTransaction',19,39,'2026-05-04 17:34:05','2026-05-04 17:34:05'),(13,'App\\Models\\FinancialTransaction',20,40,'2026-05-04 17:34:05','2026-05-04 17:34:05'),(14,'App\\Models\\FinancialTransaction',21,41,'2026-05-04 17:34:06','2026-05-04 17:34:06'),(15,'App\\Models\\FinancialTransaction',22,42,'2026-05-04 17:34:06','2026-05-04 17:34:06'),(16,'App\\Models\\FinancialTransaction',23,43,'2026-05-04 17:34:06','2026-05-04 17:34:06'),(17,'App\\Models\\FinancialTransaction',24,44,'2026-05-04 17:34:06','2026-05-04 17:34:06'),(18,'App\\Models\\FinancialTransaction',25,45,'2026-05-04 17:34:07','2026-05-04 17:34:07'),(19,'App\\Models\\FinancialTransaction',26,46,'2026-05-04 17:34:07','2026-05-04 17:34:07'),(20,'App\\Models\\FinancialTransaction',27,47,'2026-05-04 17:34:07','2026-05-04 17:34:07'),(21,'App\\Models\\FinancialTransaction',28,48,'2026-05-04 17:34:07','2026-05-04 17:34:07'),(22,'App\\Models\\FinancialTransaction',36,49,'2026-05-04 17:34:07','2026-05-04 17:34:07'),(23,'App\\Models\\FinancialTransaction',37,50,'2026-05-04 17:34:08','2026-05-04 17:34:08'),(24,'App\\Models\\FinancialTransaction',38,51,'2026-05-04 17:34:08','2026-05-04 17:34:08'),(25,'App\\Models\\FinancialTransaction',39,52,'2026-05-04 17:34:08','2026-05-04 17:34:08'),(26,'App\\Models\\FinancialTransaction',40,53,'2026-05-04 17:34:08','2026-05-04 17:34:08'),(27,'App\\Models\\FinancialTransaction',41,54,'2026-05-04 17:34:08','2026-05-04 17:34:08');
/*!40000 ALTER TABLE `attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auditable_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auditable_id` bigint unsigned DEFAULT NULL,
  `auditable_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,1,'Wilbert Anadia','System Administrator','logout',NULL,NULL,NULL,NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:32:47','2026-05-04 17:32:47'),(2,1,'Wilbert Anadia','System Administrator','login',NULL,NULL,NULL,NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:33:30','2026-05-04 17:33:30'),(3,1,'Wilbert Anadia','System Administrator','backup_deleted',NULL,NULL,'Backup deleted: doc_backup__all__all__2026-05-05_013125.zip',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:33:44','2026-05-04 17:33:44'),(4,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',28,'Approval document auto-saved for Income transaction #1',NULL,'{\"document_id\": 28, \"transaction_id\": 1}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:03','2026-05-04 17:34:03'),(5,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',29,'Approval document auto-saved for Income transaction #2',NULL,'{\"document_id\": 29, \"transaction_id\": 2}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:03','2026-05-04 17:34:03'),(6,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',30,'Approval document auto-saved for Income transaction #3',NULL,'{\"document_id\": 30, \"transaction_id\": 3}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:03','2026-05-04 17:34:03'),(7,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',31,'Approval document auto-saved for Income transaction #4',NULL,'{\"document_id\": 31, \"transaction_id\": 4}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:03','2026-05-04 17:34:03'),(8,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',32,'Approval document auto-saved for Income transaction #5',NULL,'{\"document_id\": 32, \"transaction_id\": 5}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:04','2026-05-04 17:34:04'),(9,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',33,'Approval document auto-saved for Income transaction #6',NULL,'{\"document_id\": 33, \"transaction_id\": 6}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:04','2026-05-04 17:34:04'),(10,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',34,'Approval document auto-saved for Income transaction #7',NULL,'{\"document_id\": 34, \"transaction_id\": 7}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:04','2026-05-04 17:34:04'),(11,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',35,'Approval document auto-saved for Income transaction #8',NULL,'{\"document_id\": 35, \"transaction_id\": 8}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:04','2026-05-04 17:34:04'),(12,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',36,'Approval document auto-saved for Income transaction #9',NULL,'{\"document_id\": 36, \"transaction_id\": 9}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:05','2026-05-04 17:34:05'),(13,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',37,'Approval document auto-saved for Income transaction #10',NULL,'{\"document_id\": 37, \"transaction_id\": 10}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:05','2026-05-04 17:34:05'),(14,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',38,'Approval document auto-saved for Expense transaction #18',NULL,'{\"document_id\": 38, \"transaction_id\": 18}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:05','2026-05-04 17:34:05'),(15,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',39,'Approval document auto-saved for Expense transaction #19',NULL,'{\"document_id\": 39, \"transaction_id\": 19}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:05','2026-05-04 17:34:05'),(16,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',40,'Approval document auto-saved for Expense transaction #20',NULL,'{\"document_id\": 40, \"transaction_id\": 20}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:05','2026-05-04 17:34:05'),(17,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',41,'Approval document auto-saved for Expense transaction #21',NULL,'{\"document_id\": 41, \"transaction_id\": 21}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:06','2026-05-04 17:34:06'),(18,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',42,'Approval document auto-saved for Expense transaction #22',NULL,'{\"document_id\": 42, \"transaction_id\": 22}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:06','2026-05-04 17:34:06'),(19,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',43,'Approval document auto-saved for Expense transaction #23',NULL,'{\"document_id\": 43, \"transaction_id\": 23}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:06','2026-05-04 17:34:06'),(20,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',44,'Approval document auto-saved for Expense transaction #24',NULL,'{\"document_id\": 44, \"transaction_id\": 24}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:06','2026-05-04 17:34:06'),(21,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',45,'Approval document auto-saved for Expense transaction #25',NULL,'{\"document_id\": 45, \"transaction_id\": 25}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:07','2026-05-04 17:34:07'),(22,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',46,'Approval document auto-saved for Expense transaction #26',NULL,'{\"document_id\": 46, \"transaction_id\": 26}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:07','2026-05-04 17:34:07'),(23,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',47,'Approval document auto-saved for Expense transaction #27',NULL,'{\"document_id\": 47, \"transaction_id\": 27}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:07','2026-05-04 17:34:07'),(24,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',48,'Approval document auto-saved for Expense transaction #28',NULL,'{\"document_id\": 48, \"transaction_id\": 28}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:07','2026-05-04 17:34:07'),(25,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',49,'Approval document auto-saved for Receivable transaction #36',NULL,'{\"document_id\": 49, \"transaction_id\": 36}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:07','2026-05-04 17:34:07'),(26,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',50,'Approval document auto-saved for Receivable transaction #37',NULL,'{\"document_id\": 50, \"transaction_id\": 37}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:08','2026-05-04 17:34:08'),(27,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',51,'Approval document auto-saved for Receivable transaction #38',NULL,'{\"document_id\": 51, \"transaction_id\": 38}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:08','2026-05-04 17:34:08'),(28,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',52,'Approval document auto-saved for Receivable transaction #39',NULL,'{\"document_id\": 52, \"transaction_id\": 39}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:08','2026-05-04 17:34:08'),(29,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',53,'Approval document auto-saved for Receivable transaction #40',NULL,'{\"document_id\": 53, \"transaction_id\": 40}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:08','2026-05-04 17:34:08'),(30,1,'Wilbert Anadia','System Administrator','created','App\\Models\\Document',54,'Approval document auto-saved for Receivable transaction #41',NULL,'{\"document_id\": 54, \"transaction_id\": 41}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:08','2026-05-04 17:34:08'),(31,1,'Wilbert Anadia','System Administrator','backup_restored',NULL,NULL,'Backup restored: 27 documents, 27 files, 50 financial records, 27 approval docs regenerated, 0 skipped',NULL,'{\"files\": 27, \"scope\": {\"file_type\": \"all\", \"category_ids\": [], \"allowed_mimes\": [], \"category_label\": \"All Categories\"}, \"skipped\": 0, \"versions\": 27, \"documents\": 27, \"financial\": 50, \"categories\": 0, \"fin_skipped\": 0, \"financial_docs_generated\": 27}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-04 17:34:09','2026-05-04 17:34:09');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`),
  KEY `cache_key_index` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_categories`
--

DROP TABLE IF EXISTS `document_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_categories_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_categories`
--

LOCK TABLES `document_categories` WRITE;
/*!40000 ALTER TABLE `document_categories` DISABLE KEYS */;
INSERT INTO `document_categories` VALUES (1,'Approved Income','Auto-generated financial approval documents — Approved Income',1,'2026-05-04 17:32:01','2026-05-04 17:32:01'),(2,'Approved Expense','Auto-generated financial approval documents — Approved Expense',1,'2026-05-04 17:32:01','2026-05-04 17:32:01'),(3,'Approved Receivable','Auto-generated financial approval documents — Approved Receivable',1,'2026-05-04 17:32:01','2026-05-04 17:32:01');
/*!40000 ALTER TABLE `document_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_versions`
--

DROP TABLE IF EXISTS `document_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_versions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `document_id` bigint unsigned NOT NULL,
  `version_number` int NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint unsigned NOT NULL,
  `change_notes` text COLLATE utf8mb4_unicode_ci,
  `uploaded_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_versions_document_id_version_number_unique` (`document_id`,`version_number`),
  KEY `document_versions_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `document_versions_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_versions_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_versions`
--

LOCK TABLES `document_versions` WRITE;
/*!40000 ALTER TABLE `document_versions` DISABLE KEYS */;
INSERT INTO `document_versions` VALUES (1,1,1,'documents/1/ezydHhyTe0L2UESSQ2ZiyaVZLkUzpBp8r4wuB3tK.pdf','transaction_1_income_paid.pdf','application/pdf',31832,'Auto-saved on approval — Income transaction #1',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(2,2,1,'documents/2/geFkM3opbhCHKPuUGHxBrIHwEMDbwCqecXJbkCLw.pdf','transaction_2_income_paid.pdf','application/pdf',32069,'Auto-saved on approval — Income transaction #2',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(3,3,1,'documents/3/Pd9ZmuDUAiP0GPKZfwP9G0YzfhdqNoXF84hOXxTE.pdf','transaction_3_income_paid.pdf','application/pdf',31778,'Auto-saved on approval — Income transaction #3',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(4,4,1,'documents/4/rpWYg1msRFH7NAQYFpN18oBpAPJFogW7oc2Zah4l.pdf','transaction_4_income_paid.pdf','application/pdf',32093,'Auto-saved on approval — Income transaction #4',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(5,5,1,'documents/5/W1En2Kyph4l6ipsy01ID9ftqGjnoXyTmef9YJGak.pdf','transaction_5_income_paid.pdf','application/pdf',32090,'Auto-saved on approval — Income transaction #5',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(6,6,1,'documents/6/zH6HDbJECbzHzKcDCQy0q7u7exxBsFFNu8MgcUUg.pdf','transaction_6_income_paid.pdf','application/pdf',31924,'Auto-saved on approval — Income transaction #6',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(7,7,1,'documents/7/Gt1O7MAs8FQXuAqNv9eXQBNFDuJDBxItp5g5SEjc.pdf','transaction_7_income_paid.pdf','application/pdf',32060,'Auto-saved on approval — Income transaction #7',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(8,8,1,'documents/8/GV5UUQtXcRgx87EUBYXITaLRKvWXlvNheS5L7YF2.pdf','transaction_8_income_paid.pdf','application/pdf',32062,'Auto-saved on approval — Income transaction #8',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(9,9,1,'documents/9/UyHNBbOCWTMSh5Fr4EAROorvf3a386WnEu40jlue.pdf','transaction_9_income_paid.pdf','application/pdf',31991,'Auto-saved on approval — Income transaction #9',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(10,10,1,'documents/10/fEPiaJ4AKjqSwsIQFLJLCto1Di9JjEAX0x2PNbbX.pdf','transaction_10_income_paid.pdf','application/pdf',32090,'Auto-saved on approval — Income transaction #10',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(11,11,1,'documents/11/wDVrAh5gMJrHLTOdIWjXEYbEU49GzzZytOUKBM7z.pdf','transaction_18_expense_paid.pdf','application/pdf',33158,'Auto-saved on approval — Expense transaction #18',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(12,12,1,'documents/12/ZH53AbfJ3Swv1mOTUKaBuNq44lGSd8dSkEpXr7d9.pdf','transaction_19_expense_paid.pdf','application/pdf',32991,'Auto-saved on approval — Expense transaction #19',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(13,13,1,'documents/13/cUpSSXYBnh65TMDZkrYtYk7yyhsekGmb3mCCTmE5.pdf','transaction_20_expense_paid.pdf','application/pdf',33059,'Auto-saved on approval — Expense transaction #20',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(14,14,1,'documents/14/RkT3P5IX6OeUn4XSvV9bdH5JtLOws1BQ530GPcne.pdf','transaction_21_expense_paid.pdf','application/pdf',32921,'Auto-saved on approval — Expense transaction #21',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(15,15,1,'documents/15/W2eef5E1E0tk7olnGeoiskG4mL47KNRDy6Vm7PW6.pdf','transaction_22_expense_paid.pdf','application/pdf',32881,'Auto-saved on approval — Expense transaction #22',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(16,16,1,'documents/16/wDFgWJ9RHM4oBU5wLy9mQKnfoA2ejTsliyVtmpDd.pdf','transaction_23_expense_paid.pdf','application/pdf',33016,'Auto-saved on approval — Expense transaction #23',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(17,17,1,'documents/17/JJ0YhyiiNCAslZj7PAzvM6OLgewJ8Xbj03p3X7rg.pdf','transaction_24_expense_paid.pdf','application/pdf',33116,'Auto-saved on approval — Expense transaction #24',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(18,18,1,'documents/18/pxe1q0PgBV3Gd8KbWmuTJsMvXl2cToqwJUdCCcay.pdf','transaction_25_expense_paid.pdf','application/pdf',32723,'Auto-saved on approval — Expense transaction #25',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(19,19,1,'documents/19/IEAGmFsGozKgs5w3maTyAKpRf76IWeDOKOCLA91c.pdf','transaction_26_expense_paid.pdf','application/pdf',32898,'Auto-saved on approval — Expense transaction #26',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(20,20,1,'documents/20/VUuxm76GQOpDuYMgfHGkfokRJgyd4mKiik79KW6E.pdf','transaction_27_expense_paid.pdf','application/pdf',32840,'Auto-saved on approval — Expense transaction #27',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(21,21,1,'documents/21/jM71tVYGUaN4JaiTK8ln864tSNA3grhK4CY1TGMw.pdf','transaction_28_expense_paid.pdf','application/pdf',33030,'Auto-saved on approval — Expense transaction #28',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(22,22,1,'documents/22/n6HcKV0pZcyUKaDDs96ChUHH48TFklNQpcCYdWtm.pdf','transaction_36_receivable_paid.pdf','application/pdf',32383,'Auto-saved on approval — Receivable transaction #36',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(23,23,1,'documents/23/2qqWqHzItgPzLVWrV8xmc7PWkYV8sAIAdPi3f7HH.pdf','transaction_37_receivable_paid.pdf','application/pdf',32676,'Auto-saved on approval — Receivable transaction #37',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(24,24,1,'documents/24/4VpgIPtyQWkpsfsgnbMMjM44OdWqwg1TvfS2CHaH.pdf','transaction_38_receivable_paid.pdf','application/pdf',32663,'Auto-saved on approval — Receivable transaction #38',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(25,25,1,'documents/25/pK5zsm5T0rdwRT2ovgAi4HLl5bFl6YZ7ixtWyQTN.pdf','transaction_39_receivable_paid.pdf','application/pdf',32805,'Auto-saved on approval — Receivable transaction #39',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(26,26,1,'documents/26/3Y2qqQAXVSlwTHQZ5PTA1WzjQXzIR21OGZqwPoTg.pdf','transaction_40_receivable_paid.pdf','application/pdf',32504,'Auto-saved on approval — Receivable transaction #40',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(27,27,1,'documents/27/2X4bj8dc1kOyI6PMhtUQ2OtvxVgRcXuHNkyMbyDR.pdf','transaction_41_receivable_paid.pdf','application/pdf',32800,'Auto-saved on approval — Receivable transaction #41',1,'2026-05-04 17:34:02','2026-05-04 17:34:02'),(28,28,1,'documents/28/scc5RgFH04RhBhNvrJkaQn5ZP6ITvuPdNlb0Ur5x.pdf','transaction_1_income_paid.pdf','application/pdf',31986,'Auto-saved on approval — Income transaction #1',1,'2026-05-04 17:34:03','2026-05-04 17:34:03'),(29,29,1,'documents/29/Sos7uit0t2EyulPVXBjLLKvpdkGuiMFWcZEoLfwU.pdf','transaction_2_income_paid.pdf','application/pdf',32221,'Auto-saved on approval — Income transaction #2',1,'2026-05-04 17:34:03','2026-05-04 17:34:03'),(30,30,1,'documents/30/MBTSR8Pk1GtANGalgiiFbos4jSXMNjBiYP09yJuy.pdf','transaction_3_income_paid.pdf','application/pdf',31931,'Auto-saved on approval — Income transaction #3',1,'2026-05-04 17:34:03','2026-05-04 17:34:03'),(31,31,1,'documents/31/2trXwTTSw15uQeuWuRuuv5tP2FiTusAQcA8B8dJG.pdf','transaction_4_income_paid.pdf','application/pdf',32094,'Auto-saved on approval — Income transaction #4',1,'2026-05-04 17:34:03','2026-05-04 17:34:03'),(32,32,1,'documents/32/uNRyc4vv6ayffdHG8lbmk4iW2AJQjZj64icvIhQq.pdf','transaction_5_income_paid.pdf','application/pdf',32243,'Auto-saved on approval — Income transaction #5',1,'2026-05-04 17:34:04','2026-05-04 17:34:04'),(33,33,1,'documents/33/KJ8HWSGUpahJxpOVw1oxWhzpiBb1NC4eWeJ9f3Q6.pdf','transaction_6_income_paid.pdf','application/pdf',32077,'Auto-saved on approval — Income transaction #6',1,'2026-05-04 17:34:04','2026-05-04 17:34:04'),(34,34,1,'documents/34/kHWmyRx50siujNUJjYdWnUwt71wDqA5JMcwdmKM5.pdf','transaction_7_income_paid.pdf','application/pdf',32214,'Auto-saved on approval — Income transaction #7',1,'2026-05-04 17:34:04','2026-05-04 17:34:04'),(35,35,1,'documents/35/5qYBLy26zkggjBTBuhTwWg87vPeB7fnp0R7GjdkD.pdf','transaction_8_income_paid.pdf','application/pdf',32206,'Auto-saved on approval — Income transaction #8',1,'2026-05-04 17:34:04','2026-05-04 17:34:04'),(36,36,1,'documents/36/iBIyNioMZBlIfDnwVTxoUXlVMmLCJFqRpfInxiMA.pdf','transaction_9_income_paid.pdf','application/pdf',32142,'Auto-saved on approval — Income transaction #9',1,'2026-05-04 17:34:05','2026-05-04 17:34:05'),(37,37,1,'documents/37/bdAsThrHa3uOnqC7lGAK3M8Mj5N0GGKd5alHy1Q2.pdf','transaction_10_income_paid.pdf','application/pdf',32242,'Auto-saved on approval — Income transaction #10',1,'2026-05-04 17:34:05','2026-05-04 17:34:05'),(38,38,1,'documents/38/HvDdH0WImVcG4qb7CNFgdjoHfdq51K72jDaDlGTj.pdf','transaction_18_expense_paid.pdf','application/pdf',33318,'Auto-saved on approval — Expense transaction #18',1,'2026-05-04 17:34:05','2026-05-04 17:34:05'),(39,39,1,'documents/39/bKV0o8TTbfuuuGMZBHx7xXWi7IAcg75plwDYfWqc.pdf','transaction_19_expense_paid.pdf','application/pdf',33143,'Auto-saved on approval — Expense transaction #19',1,'2026-05-04 17:34:05','2026-05-04 17:34:05'),(40,40,1,'documents/40/JHFx6MG1MtNs3LZ6DHQ0jQe3hCtcTdhflgG1X4T8.pdf','transaction_20_expense_paid.pdf','application/pdf',33205,'Auto-saved on approval — Expense transaction #20',1,'2026-05-04 17:34:05','2026-05-04 17:34:05'),(41,41,1,'documents/41/h42RAZ7VH3YMdKG1uws0uUfXaWon5sCeiJMa4WOD.pdf','transaction_21_expense_paid.pdf','application/pdf',33083,'Auto-saved on approval — Expense transaction #21',1,'2026-05-04 17:34:06','2026-05-04 17:34:06'),(42,42,1,'documents/42/qI14zXhPnsY5qTZMcIjLreRxbHgaxlEhUbXEleD0.pdf','transaction_22_expense_paid.pdf','application/pdf',33026,'Auto-saved on approval — Expense transaction #22',1,'2026-05-04 17:34:06','2026-05-04 17:34:06'),(43,43,1,'documents/43/4hBKvbPJFksGaphXV5F0qfAclf93zd99JlxZwron.pdf','transaction_23_expense_paid.pdf','application/pdf',33161,'Auto-saved on approval — Expense transaction #23',1,'2026-05-04 17:34:06','2026-05-04 17:34:06'),(44,44,1,'documents/44/NP3J29vEfeFjZaYg1Eh1pEmwQlFBAXV6cvaq19kb.pdf','transaction_24_expense_paid.pdf','application/pdf',33116,'Auto-saved on approval — Expense transaction #24',1,'2026-05-04 17:34:06','2026-05-04 17:34:06'),(45,45,1,'documents/45/dojeOmtCxyG3wVkqaesprXMJwb6CQVdaqfhyGs0b.pdf','transaction_25_expense_paid.pdf','application/pdf',32869,'Auto-saved on approval — Expense transaction #25',1,'2026-05-04 17:34:07','2026-05-04 17:34:07'),(46,46,1,'documents/46/OnefnUp8rzr73GHGjtKWeAW2sF653acWu6p1O2El.pdf','transaction_26_expense_paid.pdf','application/pdf',33042,'Auto-saved on approval — Expense transaction #26',1,'2026-05-04 17:34:07','2026-05-04 17:34:07'),(47,47,1,'documents/47/DdD1P0U6gVUUHknDBI5jNFQKIOFlfsKnUObhNxFm.pdf','transaction_27_expense_paid.pdf','application/pdf',32996,'Auto-saved on approval — Expense transaction #27',1,'2026-05-04 17:34:07','2026-05-04 17:34:07'),(48,48,1,'documents/48/OLbL4j0ShqjUnX2EGHmI5F7JSm1Jzqh0HFozYcCs.pdf','transaction_28_expense_paid.pdf','application/pdf',33188,'Auto-saved on approval — Expense transaction #28',1,'2026-05-04 17:34:07','2026-05-04 17:34:07'),(49,49,1,'documents/49/pXUl0P0H5dp6vHBIg1TPmauFGroqLSsKv9oeyvBW.pdf','transaction_36_receivable_paid.pdf','application/pdf',32531,'Auto-saved on approval — Receivable transaction #36',1,'2026-05-04 17:34:07','2026-05-04 17:34:07'),(50,50,1,'documents/50/USkGgeJnUVKgdOjxuMJvTDEC8rFJYZdd2F1MOsvt.pdf','transaction_37_receivable_paid.pdf','application/pdf',32825,'Auto-saved on approval — Receivable transaction #37',1,'2026-05-04 17:34:08','2026-05-04 17:34:08'),(51,51,1,'documents/51/Eq1uN9VluwLVrhMTF43xQ8oJu5PNUww2GI4qcXzR.pdf','transaction_38_receivable_paid.pdf','application/pdf',32814,'Auto-saved on approval — Receivable transaction #38',1,'2026-05-04 17:34:08','2026-05-04 17:34:08'),(52,52,1,'documents/52/40jEMUNWshEZZqgbV8mvQrJqY10LCrmYAXy43y01.pdf','transaction_39_receivable_paid.pdf','application/pdf',32953,'Auto-saved on approval — Receivable transaction #39',1,'2026-05-04 17:34:08','2026-05-04 17:34:08'),(53,53,1,'documents/53/JscRahOa43RYommRlRwf8p1R7o1MzvKknOMEijZu.pdf','transaction_40_receivable_paid.pdf','application/pdf',32504,'Auto-saved on approval — Receivable transaction #40',1,'2026-05-04 17:34:08','2026-05-04 17:34:08'),(54,54,1,'documents/54/uHcxt67KUileSagNr7AMTDbWyrR0lqVDOYa1xabZ.pdf','transaction_41_receivable_paid.pdf','application/pdf',32799,'Auto-saved on approval — Receivable transaction #41',1,'2026-05-04 17:34:08','2026-05-04 17:34:08');
/*!40000 ALTER TABLE `document_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` bigint unsigned DEFAULT NULL,
  `current_version_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `tags` json DEFAULT NULL,
  `document_category_id` bigint unsigned DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_owner_id_foreign` (`owner_id`),
  KEY `documents_document_category_id_foreign` (`document_category_id`),
  CONSTRAINT `documents_document_category_id_foreign` FOREIGN KEY (`document_category_id`) REFERENCES `document_categories` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `documents_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
INSERT INTO `documents` VALUES (1,1,1,'Approved Income: Annual Membership Fees Collection [2026-02-10]','Auto-generated approval slip for Income #1. Amount: ₱5,000.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,1,'2026-05-04 09:30:30','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(2,1,2,'Approved Income: Tech Summit 2024 Registration Fees [2026-01-23]','Auto-generated approval slip for Income #2. Amount: ₱3,200.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,1,'2026-05-04 09:30:30','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(3,1,3,'Approved Income: Corporate Sponsorship — ABC Corp [2026-01-27]','Auto-generated approval slip for Income #3. Amount: ₱10,000.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,1,'2026-05-04 09:30:30','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(4,1,4,'Approved Income: Holiday Fundraising Drive [2025-12-05]','Auto-generated approval slip for Income #4. Amount: ₱4,500.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,1,'2026-05-04 09:30:30','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(5,1,5,'Approved Income: Python Workshop Registration Fees [2026-02-19]','Auto-generated approval slip for Income #5. Amount: ₱2,800.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,1,'2026-05-04 09:30:31','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(6,1,6,'Approved Income: Q1 Membership Renewal Batch [2026-03-30]','Auto-generated approval slip for Income #6. Amount: ₱1,500.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,1,'2026-05-04 09:30:31','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(7,1,7,'Approved Income: UI/UX Design Seminar Fees [2025-12-08]','Auto-generated approval slip for Income #7. Amount: ₱1,800.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,1,'2026-05-04 09:30:31','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(8,1,8,'Approved Income: Hackathon Entry Fees [2026-04-22]','Auto-generated approval slip for Income #8. Amount: ₱2,200.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,1,'2026-05-04 09:30:31','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(9,1,9,'Approved Income: Alumni Donation Drive [2026-01-27]','Auto-generated approval slip for Income #9. Amount: ₱6,000.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,1,'2026-05-04 09:30:31','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(10,1,10,'Approved Income: Spring Gala Ticket Sales [2025-12-07]','Auto-generated approval slip for Income #10. Amount: ₱3,800.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,1,'2026-05-04 09:30:31','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(11,1,11,'Approved Expense: Printing of Event Tarpaulins and Posters [2026-02-15]','Auto-generated approval slip for Expense #18. Amount: ₱850.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,2,'2026-05-04 09:30:32','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(12,1,12,'Approved Expense: Venue Rental — Gymnasium for Tech Summit [2025-12-24]','Auto-generated approval slip for Expense #19. Amount: ₱5,000.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,2,'2026-05-04 09:30:32','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(13,1,13,'Approved Expense: Catering Services — Annual General Meeting [2026-03-31]','Auto-generated approval slip for Expense #20. Amount: ₱3,200.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,2,'2026-05-04 09:30:32','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(14,1,14,'Approved Expense: Office Supplies Q1 [2026-03-01]','Auto-generated approval slip for Expense #21. Amount: ₱620.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,2,'2026-05-04 09:30:32','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(15,1,15,'Approved Expense: Transportation — Regional Competition [2025-12-27]','Auto-generated approval slip for Expense #22. Amount: ₱1,400.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,2,'2026-05-04 09:30:32','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(16,1,16,'Approved Expense: Projector and Sound System Rental [2025-12-08]','Auto-generated approval slip for Expense #23. Amount: ₱2,500.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,2,'2026-05-04 09:30:33','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(17,1,17,'Approved Expense: Certificates and Trophies — Hackathon [2026-03-21]','Auto-generated approval slip for Expense #24. Amount: ₱1,800.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,2,'2026-05-04 09:30:33','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(18,1,18,'Approved Expense: Internet Load — Officers Communication [2025-12-15]','Auto-generated approval slip for Expense #25. Amount: ₱400.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,2,'2026-05-04 09:30:33','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(19,1,19,'Approved Expense: Snacks — Workshop Participants [2026-03-05]','Auto-generated approval slip for Expense #26. Amount: ₱780.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,2,'2026-05-04 09:30:33','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(20,1,20,'Approved Expense: Event Banner Production [2026-04-03]','Auto-generated approval slip for Expense #27. Amount: ₱1,100.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,2,'2026-05-04 09:30:33','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(21,1,21,'Approved Expense: Miscellaneous Supplies — Fund Drive [2026-04-02]','Auto-generated approval slip for Expense #28. Amount: ₱450.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,2,'2026-05-04 09:30:34','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(22,1,22,'Approved Receivable: John Smith — Membership Fee [2026-03-27]','Auto-generated approval slip for Receivable #36. Amount: ₱500.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,3,'2026-05-04 09:30:34','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(23,1,23,'Approved Receivable: Maria Santos — Event Registration [2025-12-27]','Auto-generated approval slip for Receivable #37. Amount: ₱350.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,3,'2026-05-04 09:30:34','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(24,1,24,'Approved Receivable: TechStart Inc. — Sponsorship Pledge [2025-11-15]','Auto-generated approval slip for Receivable #38. Amount: ₱5,000.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,3,'2026-05-04 09:30:34','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(25,1,25,'Approved Receivable: Carlos Reyes — Workshop Fee [2026-04-10]','Auto-generated approval slip for Receivable #39. Amount: ₱280.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,3,'2026-05-04 09:30:34','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(26,1,26,'Approved Receivable: Ana Lim — Seminar Registration [2025-11-11]','Auto-generated approval slip for Receivable #40. Amount: ₱400.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,3,'2026-05-04 09:30:34','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(27,1,27,'Approved Receivable: Batch 2023 — Group Membership [2026-04-25]','Auto-generated approval slip for Receivable #41. Amount: ₱2,500.00. Approved by: Wilbert Anadia on May 5, 2026 1:30 AM.',NULL,3,'2026-05-04 09:30:35','2026-05-04 17:34:02','2026-05-04 17:34:02',NULL),(28,1,28,'Approved Income: Annual Membership Fees Collection [2026-02-09]','Auto-generated approval slip for Income #1. Amount: ₱5,000.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"income\", \"Membership Fee\", \"auto-generated\"]',1,'2026-05-04 17:34:03','2026-05-04 17:34:03','2026-05-04 17:34:03',NULL),(29,1,29,'Approved Income: Tech Summit 2024 Registration Fees [2026-01-22]','Auto-generated approval slip for Income #2. Amount: ₱3,200.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"income\", \"Event Registration\", \"auto-generated\"]',1,'2026-05-04 17:34:03','2026-05-04 17:34:03','2026-05-04 17:34:03',NULL),(30,1,30,'Approved Income: Corporate Sponsorship — ABC Corp [2026-01-26]','Auto-generated approval slip for Income #3. Amount: ₱10,000.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"income\", \"Sponsorship\", \"auto-generated\"]',1,'2026-05-04 17:34:03','2026-05-04 17:34:03','2026-05-04 17:34:03',NULL),(31,1,31,'Approved Income: Holiday Fundraising Drive [2025-12-04]','Auto-generated approval slip for Income #4. Amount: ₱4,500.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"income\", \"Fundraising\", \"auto-generated\"]',1,'2026-05-04 17:34:03','2026-05-04 17:34:03','2026-05-04 17:34:03',NULL),(32,1,32,'Approved Income: Python Workshop Registration Fees [2026-02-18]','Auto-generated approval slip for Income #5. Amount: ₱2,800.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"income\", \"Workshop Fee\", \"auto-generated\"]',1,'2026-05-04 17:34:04','2026-05-04 17:34:04','2026-05-04 17:34:04',NULL),(33,1,33,'Approved Income: Q1 Membership Renewal Batch [2026-03-29]','Auto-generated approval slip for Income #6. Amount: ₱1,500.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"income\", \"Membership Fee\", \"auto-generated\"]',1,'2026-05-04 17:34:04','2026-05-04 17:34:04','2026-05-04 17:34:04',NULL),(34,1,34,'Approved Income: UI/UX Design Seminar Fees [2025-12-07]','Auto-generated approval slip for Income #7. Amount: ₱1,800.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"income\", \"Seminar Fee\", \"auto-generated\"]',1,'2026-05-04 17:34:04','2026-05-04 17:34:04','2026-05-04 17:34:04',NULL),(35,1,35,'Approved Income: Hackathon Entry Fees [2026-04-21]','Auto-generated approval slip for Income #8. Amount: ₱2,200.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"income\", \"Competition Entry Fee\", \"auto-generated\"]',1,'2026-05-04 17:34:04','2026-05-04 17:34:04','2026-05-04 17:34:04',NULL),(36,1,36,'Approved Income: Alumni Donation Drive [2026-01-26]','Auto-generated approval slip for Income #9. Amount: ₱6,000.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"income\", \"Donation\", \"auto-generated\"]',1,'2026-05-04 17:34:05','2026-05-04 17:34:05','2026-05-04 17:34:05',NULL),(37,1,37,'Approved Income: Spring Gala Ticket Sales [2025-12-06]','Auto-generated approval slip for Income #10. Amount: ₱3,800.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"income\", \"Event Registration\", \"auto-generated\"]',1,'2026-05-04 17:34:05','2026-05-04 17:34:05','2026-05-04 17:34:05',NULL),(38,1,38,'Approved Expense: Printing of Event Tarpaulins and Posters [2026-02-14]','Auto-generated approval slip for Expense #18. Amount: ₱850.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"expense\", \"Printing & Documentation\", \"auto-generated\"]',2,'2026-05-04 17:34:05','2026-05-04 17:34:05','2026-05-04 17:34:05',NULL),(39,1,39,'Approved Expense: Venue Rental — Gymnasium for Tech Summit [2025-12-23]','Auto-generated approval slip for Expense #19. Amount: ₱5,000.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"expense\", \"Venue Rental\", \"auto-generated\"]',2,'2026-05-04 17:34:05','2026-05-04 17:34:05','2026-05-04 17:34:05',NULL),(40,1,40,'Approved Expense: Catering Services — Annual General Meeting [2026-03-30]','Auto-generated approval slip for Expense #20. Amount: ₱3,200.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"expense\", \"Food & Beverages\", \"auto-generated\"]',2,'2026-05-04 17:34:05','2026-05-04 17:34:05','2026-05-04 17:34:05',NULL),(41,1,41,'Approved Expense: Office Supplies Q1 [2026-02-28]','Auto-generated approval slip for Expense #21. Amount: ₱620.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"expense\", \"Office Supplies\", \"auto-generated\"]',2,'2026-05-04 17:34:06','2026-05-04 17:34:06','2026-05-04 17:34:06',NULL),(42,1,42,'Approved Expense: Transportation — Regional Competition [2025-12-26]','Auto-generated approval slip for Expense #22. Amount: ₱1,400.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"expense\", \"Transportation\", \"auto-generated\"]',2,'2026-05-04 17:34:06','2026-05-04 17:34:06','2026-05-04 17:34:06',NULL),(43,1,43,'Approved Expense: Projector and Sound System Rental [2025-12-07]','Auto-generated approval slip for Expense #23. Amount: ₱2,500.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"expense\", \"Equipment Rental\", \"auto-generated\"]',2,'2026-05-04 17:34:06','2026-05-04 17:34:06','2026-05-04 17:34:06',NULL),(44,1,44,'Approved Expense: Certificates and Trophies — Hackathon [2026-03-20]','Auto-generated approval slip for Expense #24. Amount: ₱1,800.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"expense\", \"Awards & Certificates\", \"auto-generated\"]',2,'2026-05-04 17:34:06','2026-05-04 17:34:06','2026-05-04 17:34:06',NULL),(45,1,45,'Approved Expense: Internet Load — Officers Communication [2025-12-14]','Auto-generated approval slip for Expense #25. Amount: ₱400.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"expense\", \"Communication\", \"auto-generated\"]',2,'2026-05-04 17:34:07','2026-05-04 17:34:07','2026-05-04 17:34:07',NULL),(46,1,46,'Approved Expense: Snacks — Workshop Participants [2026-03-04]','Auto-generated approval slip for Expense #26. Amount: ₱780.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"expense\", \"Food & Beverages\", \"auto-generated\"]',2,'2026-05-04 17:34:07','2026-05-04 17:34:07','2026-05-04 17:34:07',NULL),(47,1,47,'Approved Expense: Event Banner Production [2026-04-02]','Auto-generated approval slip for Expense #27. Amount: ₱1,100.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"expense\", \"Printing & Documentation\", \"auto-generated\"]',2,'2026-05-04 17:34:07','2026-05-04 17:34:07','2026-05-04 17:34:07',NULL),(48,1,48,'Approved Expense: Miscellaneous Supplies — Fund Drive [2026-04-01]','Auto-generated approval slip for Expense #28. Amount: ₱450.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"expense\", \"Miscellaneous\", \"auto-generated\"]',2,'2026-05-04 17:34:07','2026-05-04 17:34:07','2026-05-04 17:34:07',NULL),(49,1,49,'Approved Receivable: John Smith — Membership Fee [2026-03-26]','Auto-generated approval slip for Receivable #36. Amount: ₱500.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"receivable\", \"Membership Fee\", \"auto-generated\"]',3,'2026-05-04 17:34:07','2026-05-04 17:34:07','2026-05-04 17:34:07',NULL),(50,1,50,'Approved Receivable: Maria Santos — Event Registration [2025-12-26]','Auto-generated approval slip for Receivable #37. Amount: ₱350.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"receivable\", \"Event Registration\", \"auto-generated\"]',3,'2026-05-04 17:34:08','2026-05-04 17:34:08','2026-05-04 17:34:08',NULL),(51,1,51,'Approved Receivable: TechStart Inc. — Sponsorship Pledge [2025-11-14]','Auto-generated approval slip for Receivable #38. Amount: ₱5,000.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"receivable\", \"Sponsorship Pledge\", \"auto-generated\"]',3,'2026-05-04 17:34:08','2026-05-04 17:34:08','2026-05-04 17:34:08',NULL),(52,1,52,'Approved Receivable: Carlos Reyes — Workshop Fee [2026-04-09]','Auto-generated approval slip for Receivable #39. Amount: ₱280.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"receivable\", \"Workshop Fee\", \"auto-generated\"]',3,'2026-05-04 17:34:08','2026-05-04 17:34:08','2026-05-04 17:34:08',NULL),(53,1,53,'Approved Receivable: Ana Lim — Seminar Registration [2025-11-10]','Auto-generated approval slip for Receivable #40. Amount: ₱400.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"receivable\", \"Seminar Fee\", \"auto-generated\"]',3,'2026-05-04 17:34:08','2026-05-04 17:34:08','2026-05-04 17:34:08',NULL),(54,1,54,'Approved Receivable: Batch 2023 — Group Membership [2026-04-24]','Auto-generated approval slip for Receivable #41. Amount: ₱2,500.00. Approved by: Wilbert Anadia on May 5, 2026 1:34 AM.','[\"receivable\", \"Membership Fee\", \"auto-generated\"]',3,'2026-05-04 17:34:08','2026-05-04 17:34:08','2026-05-04 17:34:08',NULL);
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `financial_categories`
--

DROP TABLE IF EXISTS `financial_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `financial_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'both',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `financial_categories_name_unique` (`name`),
  KEY `financial_categories_created_by_foreign` (`created_by`),
  CONSTRAINT `financial_categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financial_categories`
--

LOCK TABLES `financial_categories` WRITE;
/*!40000 ALTER TABLE `financial_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `financial_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `financial_transactions`
--

DROP TABLE IF EXISTS `financial_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `financial_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `receivable_id` bigint unsigned DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `type` enum('income','expense','receivable') COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_date` date NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `status` enum('pending','audited','approved','rejected','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `is_receivable` tinyint(1) NOT NULL DEFAULT '0',
  `receivable_paid` tinyint(1) NOT NULL DEFAULT '0',
  `receipt_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `audited_by` bigint unsigned DEFAULT NULL,
  `audited_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `financial_transactions_user_id_foreign` (`user_id`),
  KEY `financial_transactions_approved_by_foreign` (`approved_by`),
  KEY `financial_transactions_type_transaction_date_index` (`type`,`transaction_date`),
  KEY `financial_transactions_status_index` (`status`),
  KEY `financial_transactions_audited_by_foreign` (`audited_by`),
  KEY `financial_transactions_receivable_id_foreign` (`receivable_id`),
  KEY `financial_transactions_type_status_transaction_date_index` (`type`,`status`,`transaction_date`),
  CONSTRAINT `financial_transactions_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `financial_transactions_audited_by_foreign` FOREIGN KEY (`audited_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `financial_transactions_receivable_id_foreign` FOREIGN KEY (`receivable_id`) REFERENCES `receivables` (`id`) ON DELETE SET NULL,
  CONSTRAINT `financial_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financial_transactions`
--

LOCK TABLES `financial_transactions` WRITE;
/*!40000 ALTER TABLE `financial_transactions` DISABLE KEYS */;
INSERT INTO `financial_transactions` VALUES (1,NULL,'Annual Membership Fees Collection',5000.00,'income','Membership Fee','2026-02-09',3,'approved',1,'2026-02-11 15:10:46','Approved and recorded.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,4,'2026-02-10 15:10:46'),(2,NULL,'Tech Summit 2024 Registration Fees',3200.00,'income','Event Registration','2026-01-22',3,'approved',1,'2026-01-28 06:10:14','Approved and recorded.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,1,'2026-01-26 06:10:14'),(3,NULL,'Corporate Sponsorship — ABC Corp',10000.00,'income','Sponsorship','2026-01-26',3,'approved',1,'2026-01-31 05:08:36','Approved and recorded.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,1,'2026-01-29 05:08:36'),(4,NULL,'Holiday Fundraising Drive',4500.00,'income','Fundraising','2025-12-04',5,'approved',2,'2025-12-08 15:12:40','Approved and recorded.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,4,'2025-12-07 15:12:40'),(5,NULL,'Python Workshop Registration Fees',2800.00,'income','Workshop Fee','2026-02-18',1,'approved',2,'2026-02-21 01:30:10','Approved and recorded.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,4,'2026-02-20 01:30:10'),(6,NULL,'Q1 Membership Renewal Batch',1500.00,'income','Membership Fee','2026-03-29',2,'approved',2,'2026-04-01 17:29:50','Approved and recorded.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,1,'2026-03-30 17:29:50'),(7,NULL,'UI/UX Design Seminar Fees',1800.00,'income','Seminar Fee','2025-12-07',5,'approved',1,'2025-12-12 04:25:41','Approved and recorded.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,4,'2025-12-11 04:25:41'),(8,NULL,'Hackathon Entry Fees',2200.00,'income','Competition Entry Fee','2026-04-21',3,'approved',2,'2026-04-25 22:13:04','Approved and recorded.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,1,'2026-04-23 22:13:04'),(9,NULL,'Alumni Donation Drive',6000.00,'income','Donation','2026-01-26',1,'approved',2,'2026-02-01 00:07:22','Approved and recorded.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,4,'2026-01-30 00:07:22'),(10,NULL,'Spring Gala Ticket Sales',3800.00,'income','Event Registration','2025-12-06',5,'approved',2,'2025-12-10 08:21:18','Approved and recorded.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,1,'2025-12-09 08:21:18'),(11,NULL,'Mid-Year Membership Fees',2500.00,'income','Membership Fee','2025-12-22',1,'audited',NULL,NULL,'Verified by auditor. Awaiting final approval.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,1,'2025-12-23 23:46:51'),(12,NULL,'DevFest Workshop Fees',1400.00,'income','Workshop Fee','2026-01-03',5,'audited',NULL,NULL,'Verified by auditor. Awaiting final approval.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,1,'2026-01-04 18:11:58'),(13,NULL,'Guest Speaker Donations',3000.00,'income','Donation','2026-03-18',2,'audited',NULL,NULL,'Verified by auditor. Awaiting final approval.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,1,'2026-03-22 03:56:43'),(14,NULL,'Incoming Sponsorship — XYZ Tech',8000.00,'income','Sponsorship','2025-12-09',2,'pending',NULL,NULL,'Submitted for review. Awaiting audit.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,NULL,NULL),(15,NULL,'New Member Registration Batch',1200.00,'income','Membership Fee','2026-01-04',5,'pending',NULL,NULL,'Submitted for review. Awaiting audit.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,NULL,NULL),(16,NULL,'Capstone Symposium Entry Fees',950.00,'income','Event Registration','2026-02-18',3,'pending',NULL,NULL,'Submitted for review. Awaiting audit.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,NULL,NULL),(17,NULL,'Duplicate Sponsorship Entry (rejected)',5000.00,'income','Sponsorship','2026-02-10',3,'rejected',NULL,NULL,'Rejected due to incomplete documentation.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,NULL,NULL),(18,NULL,'Printing of Event Tarpaulins and Posters',850.00,'expense','Printing & Documentation','2026-02-14',1,'approved',1,'2026-02-17 14:35:05','Approved and recorded.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,4,'2026-02-15 14:35:05'),(19,NULL,'Venue Rental — Gymnasium for Tech Summit',5000.00,'expense','Venue Rental','2025-12-23',2,'approved',2,'2025-12-26 05:14:02','Approved and recorded.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,4,'2025-12-25 05:14:02'),(20,NULL,'Catering Services — Annual General Meeting',3200.00,'expense','Food & Beverages','2026-03-30',1,'approved',1,'2026-04-01 16:07:53','Approved and recorded.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,1,'2026-03-31 16:07:53'),(21,NULL,'Office Supplies Q1',620.00,'expense','Office Supplies','2026-02-28',2,'approved',1,'2026-03-04 18:37:01','Approved and recorded.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,1,'2026-03-02 18:37:01'),(22,NULL,'Transportation — Regional Competition',1400.00,'expense','Transportation','2025-12-26',3,'approved',2,'2025-12-30 20:19:27','Approved and recorded.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,1,'2025-12-29 20:19:27'),(23,NULL,'Projector and Sound System Rental',2500.00,'expense','Equipment Rental','2025-12-07',2,'approved',2,'2025-12-11 11:06:10','Approved and recorded.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,1,'2025-12-09 11:06:10'),(24,NULL,'Certificates and Trophies — Hackathon',1800.00,'expense','Awards & Certificates','2026-03-20',1,'approved',1,'2026-03-23 13:55:45','Approved and recorded.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,4,'2026-03-21 13:55:45'),(25,NULL,'Internet Load — Officers Communication',400.00,'expense','Communication','2025-12-14',2,'approved',1,'2025-12-17 21:32:11','Approved and recorded.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,4,'2025-12-15 21:32:11'),(26,NULL,'Snacks — Workshop Participants',780.00,'expense','Food & Beverages','2026-03-04',1,'approved',2,'2026-03-06 20:11:50','Approved and recorded.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,1,'2026-03-05 20:11:50'),(27,NULL,'Event Banner Production',1100.00,'expense','Printing & Documentation','2026-04-02',5,'approved',2,'2026-04-07 05:08:16','Approved and recorded.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,4,'2026-04-05 05:08:16'),(28,NULL,'Miscellaneous Supplies — Fund Drive',450.00,'expense','Miscellaneous','2026-04-01',1,'approved',2,'2026-04-05 18:18:34','Approved and recorded.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,1,'2026-04-03 18:18:34'),(29,NULL,'Venue Deposit — Year-End Party',3000.00,'expense','Venue Rental','2025-11-12',4,'audited',NULL,NULL,'Verified by auditor. Awaiting final approval.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,1,'2025-11-16 04:57:53'),(30,NULL,'Meals — Committee Meeting',560.00,'expense','Food & Beverages','2025-11-19',4,'audited',NULL,NULL,'Verified by auditor. Awaiting final approval.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,4,'2025-11-20 13:50:17'),(31,NULL,'Office Supplies Q2 Restock',340.00,'expense','Office Supplies','2026-02-21',1,'audited',NULL,NULL,'Verified by auditor. Awaiting final approval.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,4,'2026-02-22 09:43:43'),(32,NULL,'Upcoming Seminar Materials',1200.00,'expense','Event Materials','2026-04-03',2,'pending',NULL,NULL,'Submitted for review. Awaiting audit.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,NULL,NULL),(33,NULL,'Transportation — Outreach Program',900.00,'expense','Transportation','2025-11-27',4,'pending',NULL,NULL,'Submitted for review. Awaiting audit.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,NULL,NULL),(34,NULL,'Printing — End-of-Year Report',480.00,'expense','Printing & Documentation','2025-12-25',3,'pending',NULL,NULL,'Submitted for review. Awaiting audit.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,NULL,NULL),(35,NULL,'Over-budget Catering Proposal (rejected)',8500.00,'expense','Food & Beverages','2025-12-16',4,'rejected',NULL,NULL,'Rejected due to incomplete documentation.',NULL,NULL,0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,NULL,NULL),(36,NULL,'John Smith — Membership Fee',500.00,'receivable','Membership Fee','2026-03-26',5,'paid',2,'2026-03-31 18:02:05','Payment received from John Smith. Amount credited to income.','John Smith','2026-04-25',0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,4,'2026-03-29 18:02:05'),(37,NULL,'Maria Santos — Event Registration',350.00,'receivable','Event Registration','2025-12-26',4,'paid',2,'2025-12-29 17:08:42','Payment received from Maria Santos. Amount credited to income.','Maria Santos','2026-01-09',0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,1,'2025-12-28 17:08:42'),(38,NULL,'TechStart Inc. — Sponsorship Pledge',5000.00,'receivable','Sponsorship Pledge','2025-11-14',4,'paid',1,'2025-11-19 13:44:13','Payment received from TechStart Inc.. Amount credited to income.','TechStart Inc.','2026-01-13',0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,1,'2025-11-17 13:44:13'),(39,NULL,'Carlos Reyes — Workshop Fee',280.00,'receivable','Workshop Fee','2026-04-09',1,'paid',1,'2026-04-12 13:36:21','Payment received from Carlos Reyes. Amount credited to income.','Carlos Reyes','2026-04-16',0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,4,'2026-04-10 13:36:21'),(40,NULL,'Ana Lim — Seminar Registration',400.00,'receivable','Seminar Fee','2025-11-10',2,'paid',1,'2025-11-13 12:14:44','Payment received from Ana Lim. Amount credited to income.','Ana Lim','2025-12-01',0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,4,'2025-11-12 12:14:44'),(41,NULL,'Batch 2023 — Group Membership',2500.00,'receivable','Membership Fee','2026-04-24',2,'paid',2,'2026-04-26 12:15:19','Payment received from Batch 2023 Block A. Amount credited to income.','Batch 2023 Block A','2026-05-24',0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,4,'2026-04-25 12:15:19'),(42,NULL,'GlobalSoft PH — Sponsorship Pledge',8000.00,'receivable','Sponsorship Pledge','2026-04-04',3,'approved',2,'2026-04-07 14:32:56','Approved. Awaiting payment from GlobalSoft PH.','GlobalSoft PH','2026-05-19',0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,4,'2026-04-05 14:32:56'),(43,NULL,'Michael Tan — Competition Entry',250.00,'receivable','Competition Entry Fee','2026-03-17',5,'approved',2,'2026-03-21 17:26:09','Approved. Awaiting payment from Michael Tan.','Michael Tan','2026-03-27',0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,4,'2026-03-20 17:26:09'),(44,NULL,'Sofia Cruz — Membership Fee',500.00,'receivable','Membership Fee','2026-05-03',1,'approved',1,'2026-05-07 19:02:04','Approved. Awaiting payment from Sofia Cruz.','Sofia Cruz','2026-06-02',0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,4,'2026-05-05 19:02:04'),(45,NULL,'Overdue Pledge — XYZ Solutions',3000.00,'receivable','Sponsorship Pledge','2025-11-18',3,'approved',1,'2025-11-22 19:02:41','Approved. Awaiting payment from XYZ Solutions.','XYZ Solutions','2025-11-03',0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,4,'2025-11-21 19:02:41'),(46,NULL,'Jake Torres — Workshop Registration',280.00,'receivable','Workshop Fee','2026-04-07',1,'audited',NULL,NULL,'Audited. Pending adviser approval for Jake Torres.','Jake Torres','2026-04-21',0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,4,'2026-04-10 23:32:35'),(47,NULL,'Ella Gomez — Seminar Fee',400.00,'receivable','Seminar Fee','2026-01-13',1,'audited',NULL,NULL,'Audited. Pending adviser approval for Ella Gomez.','Ella Gomez','2026-02-03',0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,4,'2026-01-15 23:27:32'),(48,NULL,'New Sponsor Pledge — DevHub Co.',6000.00,'receivable','Sponsorship Pledge','2026-03-04',3,'pending',NULL,NULL,'Awaiting audit for pledge from DevHub Co..','DevHub Co.','2026-05-03',0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,NULL,NULL),(49,NULL,'Luis Ramos — Event Registration',350.00,'receivable','Event Registration','2026-05-01',4,'pending',NULL,NULL,'Awaiting audit for pledge from Luis Ramos.','Luis Ramos','2026-05-15',0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,NULL,NULL),(50,NULL,'Invalid Pledge — Anonymous (rejected)',1000.00,'receivable','Sponsorship Pledge','2025-12-19',2,'rejected',NULL,NULL,'Pledge from Anonymous rejected — insufficient documentation.','Anonymous','2026-01-18',0,0,NULL,'2026-05-04 17:34:02','2026-05-04 17:34:02',NULL,NULL,NULL);
/*!40000 ALTER TABLE `financial_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

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

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member_edit_logs`
--

DROP TABLE IF EXISTS `member_edit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_edit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint unsigned NOT NULL,
  `edited_by` bigint unsigned NOT NULL,
  `field_changed` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_value` text COLLATE utf8mb4_unicode_ci,
  `new_value` text COLLATE utf8mb4_unicode_ci,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `member_edit_logs_member_id_created_at_index` (`member_id`,`created_at`),
  KEY `member_edit_logs_edited_by_index` (`edited_by`),
  CONSTRAINT `member_edit_logs_edited_by_foreign` FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `member_edit_logs_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_edit_logs`
--

LOCK TABLES `member_edit_logs` WRITE;
/*!40000 ALTER TABLE `member_edit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `member_edit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `joined_at` date DEFAULT NULL,
  `term_start` date DEFAULT NULL,
  `term_end` date DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `position_changed_at` timestamp NULL DEFAULT NULL,
  `position_changed_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `members_user_id_foreign` (`user_id`),
  KEY `members_position_changed_by_foreign` (`position_changed_by`),
  CONSTRAINT `members_position_changed_by_foreign` FOREIGN KEY (`position_changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `members`
--

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026_01_01_000001_create_roles_table',1),(2,'2026_01_01_000002_create_users_table',1),(3,'2026_01_01_000003_create_members_table',1),(4,'2026_01_01_000004_create_documents_table',1),(5,'2026_03_26_100701_create_sessions_table',1),(6,'2026_03_26_101131_create_cache_table',1),(7,'2026_03_27_014947_add_theme_to_users_table',1),(8,'2026_03_27_100607_create_permissions_table',1),(9,'2026_03_27_184702_add_remember_token_to_users_table',1),(10,'2026_03_28_000000_create_role_permissions_table',1),(11,'2026_03_28_235538_add_role_change_tracking_to_members_table',1),(12,'2026_03_28_235631_create_role_change_logs_table',1),(13,'2026_03_29_023116_add_missing_columns_to_users_table',1),(14,'2026_03_29_051857_create_jobs_table',1),(15,'2026_03_29_101033_add_student_info_to_users_table',1),(16,'2026_03_30_072132_add_role_hierarchy_columns',1),(17,'2026_03_31_155159_create_member_edit_logs_table',1),(18,'2026_04_01_033423_create_positions_table',1),(19,'2026_04_01_034931_add_columns_to_positions_table',1),(20,'2026_04_01_141714_add_is_predefined_to_roles_table',1),(21,'2026_04_01_172146_add_deleted_at_to_users_table',1),(22,'2026_04_01_174350_create_password_reset_tokens_table',1),(23,'2026_04_02_023357_add_gender_phone_birthday_to_users_table',1),(24,'2026_04_02_130539_create_notifications_table',1),(25,'2026_04_03_070804_add_category_to_documents_table',1),(26,'2026_04_03_083953_add_avatar_to_users_table',1),(27,'2026_04_03_150259_add_slug_and_module_to_permissions_table',1),(28,'2026_04_05_121219_add_deleted_at_to_organizations_table',1),(29,'2026_04_06_013438_add_type_and_academic_year_to_organizations_table',1),(30,'2026_04_15_092939_add_is_visible_to_roles_table',1),(31,'2026_04_15_124138_add_allowed_positions_to_roles_table',1),(32,'2026_04_16_122343_create_financial_transactions_table',1),(33,'2026_04_16_133425_drop_budgets_and_budget_items_tables',1),(34,'2026_04_16_204303_create_attachments_table',1),(35,'2026_04_16_211215_create_document_versions_table',1),(36,'2026_04_16_211452_restructure_documents_for_versioning',1),(37,'2026_04_16_220000_unify_permissions',1),(38,'2026_04_16_221315_add_abbreviation_to_roles_table',1),(39,'2026_04_16_221533_add_email_verified_at_to_users_table',1),(40,'2026_04_16_224004_add_deleted_at_to_financial_transactions_table',1),(41,'2026_04_17_000002_drop_permissions_name_unique',1),(42,'2026_04_17_000003_drop_roles_permissions_column',1),(43,'2026_04_17_000004_fix_permissions_schema',1),(44,'2026_04_17_074235_add_category_to_documents_table',1),(45,'2026_04_17_074520_add_is_public_to_documents_table',1),(46,'2026_04_17_074736_add_description_to_documents_table',1),(47,'2026_04_17_090800_add_soft_deletes_to_documents_table',1),(48,'2026_04_17_111126_add_joined_at_column_to_members_table',1),(49,'2026_04_17_111512_make_position_nullable_in_members_table',1),(50,'2026_04_17_111910_make_term_dates_nullable_in_members_table',1),(51,'2026_04_17_172849_add_last_login_at_to_members_table',1),(52,'2026_04_18_114332_create_document_categories_table',1),(53,'2026_04_18_130340_create_audit_logs_table',1),(54,'2026_04_19_162813_add_audit_fields_to_financial_transactions',1),(55,'2026_04_19_233007_cleanup_legacy_manage_permissions',1),(56,'2026_04_21_151148_add_document_category_id_to_documents_table',1),(57,'2026_04_21_194818_drop_is_public_from_documents',1),(58,'2026_04_22_025113_create_restored_backups_table',1),(59,'2026_04_22_225710_create_receivables_table',1),(60,'2026_04_22_225758_add_receivable_id_to_financial_transactions_table',1),(61,'2026_04_23_000531_add_receivable_flags_to_financial_transactions',1),(62,'2026_04_23_003015_add_income_transaction_id_to_receivables',1),(63,'2026_04_23_234432_seed_financial_document_categories',1),(64,'2026_04_23_235757_add_tags_to_documents_table',1),(65,'2026_04_25_224746_add_performance_indexes',1),(66,'2026_04_25_225615_add_performance_indexes_to_financial_transactions',1),(67,'2026_04_25_235852_create_financial_categories_table',1),(68,'2026_04_26_043145_add_auto_pay_columns_to_receivables_table',1),(69,'2026_04_26_224313_add_receivable_fields_to_financial_transactions_table',1),(70,'2026_04_27_035245_add_receivable_fields_to_financial_transactions',1),(71,'2026_04_27_040404_add_paid_status_to_financial_transactions',1),(72,'2026_04_28_132141_add_soft_deletes_to_roles_table',1),(73,'2026_05_03_192133_drop_full_name_from_users_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

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

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `position_change_logs`
--

DROP TABLE IF EXISTS `position_change_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `position_change_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint unsigned NOT NULL,
  `changed_by` bigint unsigned NOT NULL,
  `old_position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `position_change_logs_changed_by_foreign` (`changed_by`),
  KEY `position_change_logs_member_id_created_at_index` (`member_id`,`created_at`),
  CONSTRAINT `position_change_logs_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `position_change_logs_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `position_change_logs`
--

LOCK TABLES `position_change_logs` WRITE;
/*!40000 ALTER TABLE `position_change_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `position_change_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `positions`
--

DROP TABLE IF EXISTS `positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `positions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `positions_role_id_foreign` (`role_id`),
  CONSTRAINT `positions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `positions`
--

LOCK TABLES `positions` WRITE;
/*!40000 ALTER TABLE `positions` DISABLE KEYS */;
/*!40000 ALTER TABLE `positions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `receivables`
--

DROP TABLE IF EXISTS `receivables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `receivables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `income_transaction_id` bigint unsigned DEFAULT NULL,
  `reference_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `paid_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `due_date` date DEFAULT NULL,
  `status` enum('pending','partial','paid','overdue') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `paid_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `receivables_reference_no_unique` (`reference_no`),
  KEY `receivables_created_by_foreign` (`created_by`),
  KEY `receivables_income_transaction_id_foreign` (`income_transaction_id`),
  KEY `receivables_paid_by_foreign` (`paid_by`),
  CONSTRAINT `receivables_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `receivables_income_transaction_id_foreign` FOREIGN KEY (`income_transaction_id`) REFERENCES `financial_transactions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `receivables_paid_by_foreign` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `receivables`
--

LOCK TABLES `receivables` WRITE;
/*!40000 ALTER TABLE `receivables` DISABLE KEYS */;
/*!40000 ALTER TABLE `receivables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `restored_backups`
--

DROP TABLE IF EXISTS `restored_backups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `restored_backups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `backup_filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `backup_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `restored_by` bigint unsigned NOT NULL,
  `restored_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `restored_backups_backup_filename_unique` (`backup_filename`),
  KEY `restored_backups_restored_by_foreign` (`restored_by`),
  CONSTRAINT `restored_backups_restored_by_foreign` FOREIGN KEY (`restored_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restored_backups`
--

LOCK TABLES `restored_backups` WRITE;
/*!40000 ALTER TABLE `restored_backups` DISABLE KEYS */;
INSERT INTO `restored_backups` VALUES (1,'doc_backup__all__all__2026-05-05_013125.zip','c5a3d98e1b7e302414e4525a0c9e929e270fe98b3d7f2327012afb65a4aa18b5',1,'2026-05-04 17:34:08');
/*!40000 ALTER TABLE `restored_backups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permission`
--

DROP TABLE IF EXISTS `role_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permission` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role_permissions_role_id_foreign` (`role_id`),
  KEY `role_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permission`
--

LOCK TABLES `role_permission` WRITE;
/*!40000 ALTER TABLE `role_permission` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abbreviation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` int NOT NULL DEFAULT '1',
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `desc` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_predefined` tinyint(1) NOT NULL DEFAULT '0',
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `allowed_positions` json DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`),
  KEY `roles_parent_id_foreign` (`parent_id`),
  CONSTRAINT `roles_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,NULL,'System Administrator','SysAdmin',1,0,NULL,'2026-05-04 17:32:18','2026-05-04 17:32:18',1,1,NULL,NULL),(2,NULL,'Club Adviser','CA',2,0,NULL,'2026-05-04 17:32:18','2026-05-04 17:32:18',1,1,NULL,NULL),(3,NULL,'Treasurer','TR',3,0,NULL,'2026-05-04 17:32:18','2026-05-04 17:32:18',1,1,NULL,NULL),(4,NULL,'Auditor','AU',4,0,NULL,'2026-05-04 17:32:18','2026-05-04 17:32:18',1,1,NULL,NULL),(5,NULL,'Guest','G',10,0,NULL,'2026-05-04 17:32:18','2026-05-04 17:32:18',1,0,NULL,NULL);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

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
  KEY `sessions_id_index` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `middle_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year_level` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `role_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `theme` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'navy',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_student_id_unique` (`student_id`),
  KEY `users_role_id_foreign` (`role_id`),
  KEY `users_id_deleted_at_index` (`id`,`deleted_at`),
  KEY `users_is_active_deleted_at_index` (`is_active`,`deleted_at`),
  KEY `users_created_at_deleted_at_index` (`created_at`,`deleted_at`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Wilbert','Anadia',NULL,'sysadmin@gmail.com','2026-05-04 17:32:25',NULL,'$2y$12$nJeX2cL/KQi1SaSACI/dmObv1oBELqmshW2yKLrkvML640qJF06MC',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2026-05-04 17:32:25','2026-05-04 17:32:25','navy',NULL,NULL),(2,'Club','Adviser',NULL,'adviser@gmail.com','2026-05-04 17:32:25',NULL,'$2y$12$BHH7KPxA4sbfBqo2dKbVFeiQaTFx9wq3D1T8NmGudxggrXpIT3LWa',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,'2026-05-04 17:32:25','2026-05-04 17:32:25','navy',NULL,NULL),(3,'Treasurer','User',NULL,'treasurer@gmail.com','2026-05-04 17:32:25',NULL,'$2y$12$vO1xiUb4q4X7KDAQww2frOLfp5sJkpXFS4rmvNFZtb7vh4gyFXAGG',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,'2026-05-04 17:32:25','2026-05-04 17:32:25','navy',NULL,NULL),(4,'Auditor','User',NULL,'auditor@gmail.com','2026-05-04 17:32:26',NULL,'$2y$12$JXD8HyNnayeYz0exNTBB8OTYKfqtRS.w7hx9NNOpg.z/PdxbSFyPq',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'2026-05-04 17:32:26','2026-05-04 17:32:26','navy',NULL,NULL),(5,'Guest','User',NULL,'guest@gmail.com','2026-05-04 17:32:26',NULL,'$2y$12$Z6vkcmA2AQzR5Jb4w2XgheDna8wjUL1gnqUHieE36/dMFzAD8VTS.',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,5,'2026-05-04 17:32:26','2026-05-04 17:32:26','navy',NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-05  4:07:57
