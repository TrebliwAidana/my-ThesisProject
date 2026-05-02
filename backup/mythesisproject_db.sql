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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attachments`
--

LOCK TABLES `attachments` WRITE;
/*!40000 ALTER TABLE `attachments` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
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
INSERT INTO `document_categories` VALUES (1,'Approved Income','Auto-generated financial approval documents — Approved Income',1,'2026-05-02 05:16:57','2026-05-02 05:16:57'),(2,'Approved Expense','Auto-generated financial approval documents — Approved Expense',1,'2026-05-02 05:16:57','2026-05-02 05:16:57'),(3,'Approved Receivable','Auto-generated financial approval documents — Approved Receivable',1,'2026-05-02 05:16:57','2026-05-02 05:16:57');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_versions`
--

LOCK TABLES `document_versions` WRITE;
/*!40000 ALTER TABLE `document_versions` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
INSERT INTO `documents` VALUES (1,1,NULL,'Annual Report 2024','',NULL,NULL,'2026-05-02 05:16:59','2026-05-02 05:16:59','2026-05-02 05:16:59',NULL),(2,2,NULL,'Meeting Minutes - January','',NULL,NULL,'2026-05-02 05:16:59','2026-05-02 05:16:59','2026-05-02 05:16:59',NULL),(3,2,NULL,'Budget Proposal Q1','',NULL,NULL,'2026-05-02 05:16:59','2026-05-02 05:16:59','2026-05-02 05:16:59',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financial_categories`
--

LOCK TABLES `financial_categories` WRITE;
/*!40000 ALTER TABLE `financial_categories` DISABLE KEYS */;
INSERT INTO `financial_categories` VALUES (1,'Membership & Dues','income','Fees collected for active membership, including annual dues, semester fees, and one-time induction fees.',1,1,'2026-05-02 05:16:59','2026-05-02 05:16:59',NULL),(2,'Fundraising Events','income','Proceeds from bake sales, car washes, raffles, auctions, fun runs, walk-a-thons, and seasonal sales.',1,1,'2026-05-02 05:16:59','2026-05-02 05:16:59',NULL),(3,'Activity & Service Income','income','Revenue from events, merchandise sales, lock-ins, and tutoring.',1,1,'2026-05-02 05:16:59','2026-05-02 05:16:59',NULL),(4,'Sponsorships & Donations','income','Cash or in-kind support from businesses, parents, alumni, and grants.',1,1,'2026-05-02 05:16:59','2026-05-02 05:16:59',NULL),(5,'Other Income','income','Bank interest, forfeited deposits, and recycling proceeds.',1,1,'2026-05-02 05:16:59','2026-05-02 05:16:59',NULL),(6,'Events & Activities','expense','Decorations, entertainment, food, prizes, photography, rentals.',1,1,'2026-05-02 05:16:59','2026-05-02 05:16:59',NULL),(7,'Promotional & Marketing','expense','Flyers, posters, ads, banners, signage.',1,1,'2026-05-02 05:16:59','2026-05-02 05:16:59',NULL),(8,'Supplies & Materials','expense','Office and craft supplies, tags, lanyards, batteries.',1,1,'2026-05-02 05:16:59','2026-05-02 05:16:59',NULL),(9,'Merchandise Production','expense','Production of shirts, hoodies, stickers, etc.',1,1,'2026-05-02 05:16:59','2026-05-02 05:16:59',NULL),(10,'Travel & Competitions','expense','Registration, transport, lodging, meals.',1,1,'2026-05-02 05:16:59','2026-05-02 05:16:59',NULL),(11,'Service & Community Projects','expense','Donations, project materials, appreciation gifts.',1,1,'2026-05-02 05:16:59','2026-05-02 05:16:59',NULL),(12,'Administrative','expense','Bank fees, hosting, printing, storage.',1,1,'2026-05-02 05:16:59','2026-05-02 05:16:59',NULL),(13,'Member Related Receivables','receivable','Unpaid dues, ticket balances, deposits, fines.',1,1,'2026-05-02 05:16:59','2026-05-02 05:16:59',NULL),(14,'Event Related Receivables','receivable','Uncollected sponsor pledges and grants.',1,1,'2026-05-02 05:16:59','2026-05-02 05:16:59',NULL),(15,'Fundraising Receivable','receivable','Unreturned raffle funds and unpaid auction bids.',1,1,'2026-05-02 05:16:59','2026-05-02 05:16:59',NULL),(16,'Staff / Advisor Related','receivable','Cash advances pending receipts and fines.',1,1,'2026-05-02 05:16:59','2026-05-02 05:16:59',NULL);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financial_transactions`
--

LOCK TABLES `financial_transactions` WRITE;
/*!40000 ALTER TABLE `financial_transactions` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `members`
--

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
INSERT INTO `members` VALUES (1,1,'System Administrato',NULL,'2024-01-01',NULL,NULL,'2026-05-02 05:16:59','2026-05-02 05:16:59',NULL,NULL),(2,2,'Club Adviser',NULL,'2024-01-01','2024-12-31',NULL,'2026-05-02 05:16:59','2026-05-02 05:16:59',NULL,NULL),(3,3,'Treasurer',NULL,'2024-01-01',NULL,NULL,'2026-05-02 05:16:59','2026-05-02 05:16:59',NULL,NULL),(4,4,'Auditor',NULL,'2024-01-01',NULL,NULL,'2026-05-02 05:16:59','2026-05-02 05:16:59',NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026_01_01_000001_create_roles_table',1),(2,'2026_01_01_000002_create_users_table',1),(3,'2026_01_01_000003_create_members_table',1),(4,'2026_01_01_000004_create_documents_table',1),(5,'2026_03_26_100701_create_sessions_table',1),(6,'2026_03_26_101131_create_cache_table',1),(7,'2026_03_27_014947_add_theme_to_users_table',1),(8,'2026_03_27_100607_create_permissions_table',1),(9,'2026_03_27_184702_add_remember_token_to_users_table',1),(10,'2026_03_28_000000_create_role_permissions_table',1),(11,'2026_03_28_235538_add_role_change_tracking_to_members_table',1),(12,'2026_03_28_235631_create_role_change_logs_table',1),(13,'2026_03_29_023116_add_missing_columns_to_users_table',1),(14,'2026_03_29_051857_create_jobs_table',1),(15,'2026_03_29_101033_add_student_info_to_users_table',1),(16,'2026_03_30_072132_add_role_hierarchy_columns',1),(17,'2026_03_31_155159_create_member_edit_logs_table',1),(18,'2026_04_01_033423_create_positions_table',1),(19,'2026_04_01_034931_add_columns_to_positions_table',1),(20,'2026_04_01_141714_add_is_predefined_to_roles_table',1),(21,'2026_04_01_172146_add_deleted_at_to_users_table',1),(22,'2026_04_01_174350_create_password_reset_tokens_table',1),(23,'2026_04_02_023357_add_gender_phone_birthday_to_users_table',1),(24,'2026_04_02_130539_create_notifications_table',1),(25,'2026_04_03_070804_add_category_to_documents_table',1),(26,'2026_04_03_083953_add_avatar_to_users_table',1),(27,'2026_04_03_150259_add_slug_and_module_to_permissions_table',1),(28,'2026_04_05_121219_add_deleted_at_to_organizations_table',1),(29,'2026_04_06_013438_add_type_and_academic_year_to_organizations_table',1),(30,'2026_04_15_092939_add_is_visible_to_roles_table',1),(31,'2026_04_15_124138_add_allowed_positions_to_roles_table',1),(32,'2026_04_16_122343_create_financial_transactions_table',1),(33,'2026_04_16_133425_drop_budgets_and_budget_items_tables',1),(34,'2026_04_16_204303_create_attachments_table',1),(35,'2026_04_16_211215_create_document_versions_table',1),(36,'2026_04_16_211452_restructure_documents_for_versioning',1),(37,'2026_04_16_220000_unify_permissions',1),(38,'2026_04_16_221315_add_abbreviation_to_roles_table',1),(39,'2026_04_16_221533_add_email_verified_at_to_users_table',1),(40,'2026_04_16_224004_add_deleted_at_to_financial_transactions_table',1),(41,'2026_04_17_000002_drop_permissions_name_unique',1),(42,'2026_04_17_000003_drop_roles_permissions_column',1),(43,'2026_04_17_000004_fix_permissions_schema',1),(44,'2026_04_17_074235_add_category_to_documents_table',1),(45,'2026_04_17_074520_add_is_public_to_documents_table',1),(46,'2026_04_17_074736_add_description_to_documents_table',1),(47,'2026_04_17_090800_add_soft_deletes_to_documents_table',1),(48,'2026_04_17_111126_add_joined_at_column_to_members_table',1),(49,'2026_04_17_111512_make_position_nullable_in_members_table',1),(50,'2026_04_17_111910_make_term_dates_nullable_in_members_table',1),(51,'2026_04_17_172849_add_last_login_at_to_members_table',1),(52,'2026_04_18_114332_create_document_categories_table',1),(53,'2026_04_18_130340_create_audit_logs_table',1),(54,'2026_04_19_162813_add_audit_fields_to_financial_transactions',1),(55,'2026_04_19_233007_cleanup_legacy_manage_permissions',1),(56,'2026_04_21_151148_add_document_category_id_to_documents_table',1),(57,'2026_04_21_194818_drop_is_public_from_documents',1),(58,'2026_04_22_025113_create_restored_backups_table',1),(59,'2026_04_22_225710_create_receivables_table',1),(60,'2026_04_22_225758_add_receivable_id_to_financial_transactions_table',1),(61,'2026_04_23_000531_add_receivable_flags_to_financial_transactions',1),(62,'2026_04_23_003015_add_income_transaction_id_to_receivables',1),(63,'2026_04_23_234432_seed_financial_document_categories',1),(64,'2026_04_23_235757_add_tags_to_documents_table',1),(65,'2026_04_25_224746_add_performance_indexes',1),(66,'2026_04_25_225615_add_performance_indexes_to_financial_transactions',1),(67,'2026_04_25_235852_create_financial_categories_table',1),(68,'2026_04_26_043145_add_auto_pay_columns_to_receivables_table',1),(69,'2026_04_26_224313_add_receivable_fields_to_financial_transactions_table',1),(70,'2026_04_27_035245_add_receivable_fields_to_financial_transactions',1),(71,'2026_04_27_040404_add_paid_status_to_financial_transactions',1),(72,'2026_04_28_132141_add_soft_deletes_to_roles_table',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'View Users','users.view','users','view','View Users','2026-05-02 05:16:57','2026-05-02 05:16:57'),(2,'Create Users','users.create','users','create','Create Users','2026-05-02 05:16:57','2026-05-02 05:16:57'),(3,'Edit Users','users.edit','users','edit','Edit Users','2026-05-02 05:16:57','2026-05-02 05:16:57'),(4,'Delete Users','users.delete','users','delete','Delete Users','2026-05-02 05:16:57','2026-05-02 05:16:57'),(5,'View Members','members.view','members','view','View Members','2026-05-02 05:16:57','2026-05-02 05:16:57'),(6,'Create Members','members.create','members','create','Create Members','2026-05-02 05:16:57','2026-05-02 05:16:57'),(7,'Edit Members','members.edit','members','edit','Edit Members','2026-05-02 05:16:57','2026-05-02 05:16:57'),(8,'Delete Members','members.delete','members','delete','Delete Members','2026-05-02 05:16:57','2026-05-02 05:16:57'),(9,'View Documents','documents.view','documents','view','View Documents','2026-05-02 05:16:57','2026-05-02 05:16:57'),(10,'Upload Documents','documents.create','documents','create','Upload Documents','2026-05-02 05:16:57','2026-05-02 05:16:57'),(11,'Edit Documents','documents.edit','documents','edit','Edit Documents','2026-05-02 05:16:57','2026-05-02 05:16:57'),(12,'Delete Documents','documents.delete','documents','delete','Delete Documents','2026-05-02 05:16:57','2026-05-02 05:16:57'),(13,'View Trash','documents.trash','documents','trash','View Trash','2026-05-02 05:16:57','2026-05-02 05:16:57'),(14,'Restore Documents','documents.restore','documents','restore','Restore Documents','2026-05-02 05:16:58','2026-05-02 05:16:58'),(15,'Permanently Delete','documents.force-delete','documents','force-delete','Permanently Delete','2026-05-02 05:16:58','2026-05-02 05:16:58'),(16,'View Document Categories','categories.view','categories','view','View Document Categories','2026-05-02 05:16:58','2026-05-02 05:16:58'),(17,'Create Document Categories','categories.create','categories','create','Create Document Categories','2026-05-02 05:16:58','2026-05-02 05:16:58'),(18,'Edit Document Categories','categories.edit','categories','edit','Edit Document Categories','2026-05-02 05:16:58','2026-05-02 05:16:58'),(19,'Delete Document Categories','categories.delete','categories','delete','Delete Document Categories','2026-05-02 05:16:58','2026-05-02 05:16:58'),(20,'View Financial Records','financial.view','financial','view','View Financial Records','2026-05-02 05:16:58','2026-05-02 05:16:58'),(21,'Record Income/Expense','financial.create','financial','create','Record Income/Expense','2026-05-02 05:16:58','2026-05-02 05:16:58'),(22,'Edit Transactions','financial.edit','financial','edit','Edit Transactions','2026-05-02 05:16:58','2026-05-02 05:16:58'),(23,'Delete Transactions','financial.delete','financial','delete','Delete Transactions','2026-05-02 05:16:58','2026-05-02 05:16:58'),(24,'Audit Transactions','financial.audit','financial','audit','Audit Transactions','2026-05-02 05:16:58','2026-05-02 05:16:58'),(25,'Approve Transactions','financial.approve','financial','approve','Approve Transactions','2026-05-02 05:16:58','2026-05-02 05:16:58'),(26,'Review Transactions','financial.review','financial','review','Review Transactions','2026-05-02 05:16:58','2026-05-02 05:16:58'),(27,'Manage Financial Categories','financial_categories.manage','financial_categories','manage','Manage Financial Categories','2026-05-02 05:16:58','2026-05-02 05:16:58'),(28,'View Reports','reports.view','reports','view','View Reports','2026-05-02 05:16:58','2026-05-02 05:16:58'),(29,'Generate Reports','reports.generate','reports','generate','Generate Reports','2026-05-02 05:16:58','2026-05-02 05:16:58'),(30,'View Public Reports','reports.public','reports','public','View Public Reports','2026-05-02 05:16:58','2026-05-02 05:16:58'),(31,'View Audit Logs','audit.view','audit','view','View Audit Logs','2026-05-02 05:16:58','2026-05-02 05:16:58'),(32,'Add Audit Remarks','audit.remarks','audit','remarks','Add Audit Remarks','2026-05-02 05:16:58','2026-05-02 05:16:58'),(33,'Monitor Activities','activities.monitor','activities','monitor','Monitor Activities','2026-05-02 05:16:58','2026-05-02 05:16:58'),(34,'View Roles','roles.view','roles','view','View Roles','2026-05-02 05:16:58','2026-05-02 05:16:58'),(35,'Create Roles','roles.create','roles','create','Create Roles','2026-05-02 05:16:58','2026-05-02 05:16:58'),(36,'Edit Roles','roles.edit','roles','edit','Edit Roles','2026-05-02 05:16:58','2026-05-02 05:16:58'),(37,'Delete Roles','roles.delete','roles','delete','Delete Roles','2026-05-02 05:16:58','2026-05-02 05:16:58'),(38,'View Permissions','permissions.view','permissions','view','View Permissions','2026-05-02 05:16:58','2026-05-02 05:16:58'),(39,'Edit Permissions','permissions.edit','permissions','edit','Edit Permissions','2026-05-02 05:16:58','2026-05-02 05:16:58'),(40,'View Backups','backups.view','backups','view','View Backups','2026-05-02 05:16:58','2026-05-02 05:16:58'),(41,'Create Backups','backups.create','backups','create','Create Backups','2026-05-02 05:16:58','2026-05-02 05:16:58'),(42,'Restore Backups','backups.restore','backups','restore','Restore Backups','2026-05-02 05:16:58','2026-05-02 05:16:58'),(43,'Delete Backups','backups.delete','backups','delete','Delete Backups','2026-05-02 05:16:58','2026-05-02 05:16:58');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restored_backups`
--

LOCK TABLES `restored_backups` WRITE;
/*!40000 ALTER TABLE `restored_backups` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permission`
--

LOCK TABLES `role_permission` WRITE;
/*!40000 ALTER TABLE `role_permission` DISABLE KEYS */;
INSERT INTO `role_permission` VALUES (1,1,33,NULL,NULL),(2,1,32,NULL,NULL),(3,1,31,NULL,NULL),(4,1,41,NULL,NULL),(5,1,43,NULL,NULL),(6,1,42,NULL,NULL),(7,1,40,NULL,NULL),(8,1,17,NULL,NULL),(9,1,19,NULL,NULL),(10,1,18,NULL,NULL),(11,1,16,NULL,NULL),(12,1,10,NULL,NULL),(13,1,12,NULL,NULL),(14,1,11,NULL,NULL),(15,1,15,NULL,NULL),(16,1,14,NULL,NULL),(17,1,13,NULL,NULL),(18,1,9,NULL,NULL),(19,1,27,NULL,NULL),(20,1,25,NULL,NULL),(21,1,24,NULL,NULL),(22,1,21,NULL,NULL),(23,1,23,NULL,NULL),(24,1,22,NULL,NULL),(25,1,26,NULL,NULL),(26,1,20,NULL,NULL),(27,1,6,NULL,NULL),(28,1,8,NULL,NULL),(29,1,7,NULL,NULL),(30,1,5,NULL,NULL),(31,1,39,NULL,NULL),(32,1,38,NULL,NULL),(33,1,29,NULL,NULL),(34,1,30,NULL,NULL),(35,1,28,NULL,NULL),(36,1,35,NULL,NULL),(37,1,37,NULL,NULL),(38,1,36,NULL,NULL),(39,1,34,NULL,NULL),(40,1,2,NULL,NULL),(41,1,4,NULL,NULL),(42,1,3,NULL,NULL),(43,1,1,NULL,NULL),(44,2,5,NULL,NULL),(45,2,9,NULL,NULL),(46,2,13,NULL,NULL),(47,2,14,NULL,NULL),(48,2,16,NULL,NULL),(49,2,20,NULL,NULL),(50,2,24,NULL,NULL),(51,2,25,NULL,NULL),(52,2,26,NULL,NULL),(53,2,27,NULL,NULL),(54,2,28,NULL,NULL),(55,2,31,NULL,NULL),(56,2,33,NULL,NULL),(57,2,40,NULL,NULL),(58,3,20,NULL,NULL),(59,3,21,NULL,NULL),(60,3,22,NULL,NULL),(61,3,23,NULL,NULL),(62,3,28,NULL,NULL),(63,3,29,NULL,NULL),(64,4,20,NULL,NULL),(65,4,24,NULL,NULL),(66,4,26,NULL,NULL),(67,4,28,NULL,NULL),(68,4,31,NULL,NULL),(69,4,32,NULL,NULL),(70,5,30,NULL,NULL);
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
INSERT INTO `roles` VALUES (1,NULL,'System Administrator','SysAdmin',1,0,NULL,'2026-05-02 05:16:57','2026-05-02 05:16:57',1,1,NULL,NULL),(2,NULL,'Club Adviser','CA',2,0,NULL,'2026-05-02 05:16:57','2026-05-02 05:16:57',1,1,NULL,NULL),(3,NULL,'Treasurer','TR',3,0,NULL,'2026-05-02 05:16:57','2026-05-02 05:16:57',1,1,NULL,NULL),(4,NULL,'Auditor','AU',4,0,NULL,'2026-05-02 05:16:57','2026-05-02 05:16:57',1,1,NULL,NULL),(5,NULL,'Guest','G',10,0,NULL,'2026-05-02 05:16:57','2026-05-02 05:16:57',1,0,NULL,NULL);
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
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
INSERT INTO `users` VALUES (1,'System Administrator','System','Administrator',NULL,'sysadmin@gmail.com','2026-05-02 05:16:58',NULL,'$2y$12$w4soiedqg6znhgfcb1gabOhY84lQaMyvx657EmNVJX23LeldZc67i',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2026-05-02 05:16:58','2026-05-02 05:16:58','navy',NULL,NULL),(2,'Club Adviser','Club','Adviser',NULL,'adviser@gmail.com','2026-05-02 05:16:58',NULL,'$2y$12$dQVReY8n1z/ISa9P6kyUEu1ck7bSxgOXIzd2c7UUMei/WYLNldUv.',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,'2026-05-02 05:16:58','2026-05-02 05:16:58','navy',NULL,NULL),(3,'Treasurer User','Treasurer','User',NULL,'treasurer@gmail.com','2026-05-02 05:16:58',NULL,'$2y$12$jGAMrtTJgJdTVLi95CY5zuagWOancDkyCfnF0fl9zKIqGgTYmuuA2',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,'2026-05-02 05:16:58','2026-05-02 05:16:58','navy',NULL,NULL),(4,'Auditor User','Auditor','User',NULL,'auditor@gmail.com','2026-05-02 05:16:59',NULL,'$2y$12$uQA1CJtbjJ4jivPY6nx9.ur.YzFxBDOwTCkg7NWvjg0ua5WX6I2RK',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'2026-05-02 05:16:59','2026-05-02 05:16:59','navy',NULL,NULL),(5,'Guest User','Guest','User',NULL,'guest@gmail.com','2026-05-02 05:16:59',NULL,'$2y$12$qMpOh4aYtZ1z6j46ggIlUucq/SFLurp4MM.Dmd5ErCMkK8pmr859i',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,5,'2026-05-02 05:16:59','2026-05-02 05:16:59','navy',NULL,NULL);
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

-- Dump completed on 2026-05-02 13:17:09
