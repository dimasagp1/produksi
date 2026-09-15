-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table production_ek_dev.batches
CREATE TABLE IF NOT EXISTS `batches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `batch_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `color_id` bigint unsigned NOT NULL,
  `machine_id` bigint unsigned DEFAULT NULL,
  `target_quantity` int NOT NULL,
  `current_quantity` int NOT NULL DEFAULT '0',
  `reject_quantity` int NOT NULL DEFAULT '0',
  `start_date` date NOT NULL,
  `deadline_date` date DEFAULT NULL,
  `status` enum('planning','running','hold','completed','canceled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planning',
  `priority` enum('low','medium','high') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `batches_batch_code_unique` (`batch_code`),
  KEY `batches_product_id_foreign` (`product_id`),
  KEY `batches_color_id_foreign` (`color_id`),
  KEY `batches_machine_id_foreign` (`machine_id`),
  CONSTRAINT `batches_color_id_foreign` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `batches_machine_id_foreign` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) ON DELETE SET NULL,
  CONSTRAINT `batches_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table production_ek_dev.batches: ~1 rows (approximately)
INSERT INTO `batches` (`id`, `batch_code`, `product_id`, `color_id`, `machine_id`, `target_quantity`, `current_quantity`, `reject_quantity`, `start_date`, `deadline_date`, `status`, `priority`, `notes`, `created_at`, `updated_at`) VALUES
	(4, 'EKDEV-271225-001', 1, 1, 2, 10000, 4980, 10, '2025-12-27', '2025-12-27', 'running', 'low', NULL, '2025-12-27 04:19:05', '2025-12-27 04:42:00');

-- Dumping structure for table production_ek_dev.colors
CREATE TABLE IF NOT EXISTS `colors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `colors_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table production_ek_dev.colors: ~27 rows (approximately)
INSERT INTO `colors` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'Putih', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(2, 'Hitam', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(3, 'Merah', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(4, 'Kuning', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(5, 'Hijau', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(6, 'Biru', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(7, 'Biru Muda', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(8, 'Pink', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(9, 'Orange', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(10, 'Coral', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(11, 'Clear', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(12, 'Frosted', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(13, 'Hitam Transparan', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(14, 'Gold', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(15, 'Silver', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(16, 'List Gold', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(17, 'List Silver', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(18, 'Double List Gold', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(19, 'Natural', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(20, 'Amber', 'active', '2025-12-06 22:47:18', '2025-12-20 02:27:00'),
	(21, 'Deep Olive', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(22, 'Bluewis', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(23, 'Kuning Povidione', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(24, 'Kuning Brightening', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(25, 'Coral Protecting', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(26, 'Orange Vermint', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18'),
	(27, 'Hijau Vermint', 'active', '2025-12-06 22:47:18', '2025-12-06 22:47:18');

-- Dumping structure for table production_ek_dev.coordinators
CREATE TABLE IF NOT EXISTS `coordinators` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coordinators_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table production_ek_dev.coordinators: ~2 rows (approximately)
INSERT INTO `coordinators` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'Alip', 'active', '2025-12-07 04:32:04', '2025-12-13 02:55:15'),
	(2, 'Hanifan', 'active', '2025-12-07 04:32:04', '2025-12-07 04:32:04'),
	(3, 'Alvin', 'active', '2025-12-07 04:32:04', '2025-12-11 06:10:37');

-- Dumping structure for table production_ek_dev.daily_reports
CREATE TABLE IF NOT EXISTS `daily_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `report_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `production_date` date NOT NULL,
  `batch_id` bigint unsigned NOT NULL,
  `machine_id` bigint unsigned NOT NULL,
  `shift_id` bigint unsigned NOT NULL,
  `coordinator_id` bigint unsigned NOT NULL,
  `operator_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `color_id` bigint unsigned NOT NULL,
  `packaging_type_id` bigint unsigned DEFAULT NULL,
  `cycle_time` decimal(8,2) NOT NULL,
  `cavity` int NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `total_minutes` int NOT NULL,
  `qty_theory` int NOT NULL,
  `qty_actual` int NOT NULL,
  `qty_good` int NOT NULL,
  `qty_reject_total` int NOT NULL,
  `qty_sample` int NOT NULL DEFAULT '0' COMMENT 'Jumlah Sample',
  `total_runner` decimal(8,2) DEFAULT '0.00' COMMENT 'Total berat runner (kg)',
  `purging_kg` decimal(10,3) DEFAULT '0.000' COMMENT 'Input Kg Purging',
  `weight_per_pcs` decimal(10,3) DEFAULT '0.000' COMMENT 'Berat per Pcs (Gram)',
  `qty_purging` int NOT NULL DEFAULT '0',
  `total_output` int NOT NULL,
  `total_counter` int DEFAULT NULL,
  `wip_previous` int NOT NULL DEFAULT '0' COMMENT 'Sisa dari shift sebelumnya',
  `wip` int NOT NULL DEFAULT '0',
  `packaging_qty` int NOT NULL DEFAULT '0',
  `downtime_total` int NOT NULL DEFAULT '0',
  `efficiency` decimal(10,2) NOT NULL DEFAULT '0.00',
  `yield` decimal(5,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'submitted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_reports_report_code_unique` (`report_code`),
  KEY `daily_reports_batch_id_foreign` (`batch_id`),
  KEY `daily_reports_machine_id_foreign` (`machine_id`),
  KEY `daily_reports_shift_id_foreign` (`shift_id`),
  KEY `daily_reports_coordinator_id_foreign` (`coordinator_id`),
  KEY `daily_reports_operator_id_foreign` (`operator_id`),
  KEY `daily_reports_product_id_foreign` (`product_id`),
  KEY `daily_reports_color_id_foreign` (`color_id`),
  KEY `daily_reports_packaging_type_id_foreign` (`packaging_type_id`),
  CONSTRAINT `daily_reports_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`),
  CONSTRAINT `daily_reports_color_id_foreign` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`),
  CONSTRAINT `daily_reports_coordinator_id_foreign` FOREIGN KEY (`coordinator_id`) REFERENCES `coordinators` (`id`),
  CONSTRAINT `daily_reports_machine_id_foreign` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`),
  CONSTRAINT `daily_reports_operator_id_foreign` FOREIGN KEY (`operator_id`) REFERENCES `operators` (`id`),
  CONSTRAINT `daily_reports_packaging_type_id_foreign` FOREIGN KEY (`packaging_type_id`) REFERENCES `packaging_types` (`id`),
  CONSTRAINT `daily_reports_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `daily_reports_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table production_ek_dev.daily_reports: ~4 rows (approximately)
INSERT INTO `daily_reports` (`id`, `report_code`, `production_date`, `batch_id`, `machine_id`, `shift_id`, `coordinator_id`, `operator_id`, `product_id`, `color_id`, `packaging_type_id`, `cycle_time`, `cavity`, `start_time`, `end_time`, `total_minutes`, `qty_theory`, `qty_actual`, `qty_good`, `qty_reject_total`, `qty_sample`, `total_runner`, `purging_kg`, `weight_per_pcs`, `qty_purging`, `total_output`, `total_counter`, `wip_previous`, `wip`, `packaging_qty`, `downtime_total`, `efficiency`, `yield`, `notes`, `status`, `created_at`, `updated_at`) VALUES
	(6, 'DR-251227-822', '2025-12-27', 4, 2, 1, 1, 1, 1, 1, NULL, 20.00, 5, '07:00:00', '15:00:00', 480, 7050, 5010, 5000, 10, 20, 1.20, 12.000, 2.500, 4, 5010, 6000, 0, 100, 19, 10, 70.92, 99.80, 'test', 'submitted', '2025-12-27 04:42:00', '2025-12-27 04:54:06');

-- Dumping structure for table production_ek_dev.daily_report_downtimes
CREATE TABLE IF NOT EXISTS `daily_report_downtimes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `daily_report_id` bigint unsigned NOT NULL,
  `downtime_id` bigint unsigned NOT NULL,
  `duration` int NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `daily_report_downtimes_daily_report_id_foreign` (`daily_report_id`),
  KEY `daily_report_downtimes_downtime_id_foreign` (`downtime_id`),
  CONSTRAINT `daily_report_downtimes_daily_report_id_foreign` FOREIGN KEY (`daily_report_id`) REFERENCES `daily_reports` (`id`) ON DELETE CASCADE,
  CONSTRAINT `daily_report_downtimes_downtime_id_foreign` FOREIGN KEY (`downtime_id`) REFERENCES `downtimes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table production_ek_dev.daily_report_downtimes: ~0 rows (approximately)
INSERT INTO `daily_report_downtimes` (`id`, `daily_report_id`, `downtime_id`, `duration`, `remarks`, `created_at`, `updated_at`) VALUES
	(20, 6, 1, 10, NULL, '2025-12-27 04:42:00', '2025-12-27 04:42:00');

-- Dumping structure for table production_ek_dev.daily_report_rejects
CREATE TABLE IF NOT EXISTS `daily_report_rejects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `daily_report_id` bigint unsigned NOT NULL,
  `reject_item_id` bigint unsigned NOT NULL,
  `qty` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `daily_report_rejects_daily_report_id_foreign` (`daily_report_id`),
  KEY `daily_report_rejects_reject_item_id_foreign` (`reject_item_id`),
  CONSTRAINT `daily_report_rejects_daily_report_id_foreign` FOREIGN KEY (`daily_report_id`) REFERENCES `daily_reports` (`id`) ON DELETE CASCADE,
  CONSTRAINT `daily_report_rejects_reject_item_id_foreign` FOREIGN KEY (`reject_item_id`) REFERENCES `reject_items` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table production_ek_dev.daily_report_rejects: ~1 rows (approximately)
INSERT INTO `daily_report_rejects` (`id`, `daily_report_id`, `reject_item_id`, `qty`, `created_at`, `updated_at`) VALUES
	(19, 6, 1, 10, '2025-12-27 04:42:00', '2025-12-27 04:42:00');

-- Dumping structure for table production_ek_dev.downtimes
CREATE TABLE IF NOT EXISTS `downtimes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `downtimes_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table production_ek_dev.downtimes: ~9 rows (approximately)
INSERT INTO `downtimes` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'Breakdown / Failure (Kerusakan Mesin)', 'active', '2025-12-07 04:51:37', '2025-12-11 06:10:49'),
	(2, 'Tooling Failure (Kerusakan Tool/Mold)', 'active', '2025-12-07 04:51:37', '2025-12-07 04:51:37'),
	(3, 'Setup & Changeover (Persiapan/Ganti Setup)', 'active', '2025-12-07 04:51:37', '2025-12-07 04:51:37'),
	(4, 'Minor Stoppages / Jams (Stop Singkat/Macam)', 'active', '2025-12-07 04:51:37', '2025-12-07 04:51:37'),
	(5, 'Lack of Material (Menunggu Bahan Baku)', 'active', '2025-12-07 04:51:37', '2025-12-07 04:51:37'),
	(6, 'Lack of Operator (Kurang Operator)', 'active', '2025-12-07 04:51:37', '2025-12-07 04:51:37'),
	(7, 'Scheduled Maintenance (Perawatan Terjadwal)', 'active', '2025-12-07 04:51:37', '2025-12-07 04:51:37'),
	(8, 'QC Hold / Inspection (Menunggu Cek Kualitas)', 'active', '2025-12-07 04:51:37', '2025-12-07 04:51:37'),
	(9, 'Cleaning (Pembersihan)', 'active', '2025-12-07 04:51:37', '2025-12-07 04:51:37');

-- Dumping structure for table production_ek_dev.machines
CREATE TABLE IF NOT EXISTS `machines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table production_ek_dev.machines: ~14 rows (approximately)
INSERT INTO `machines` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'Stretch Blow Manual', 'active', '2025-12-06 18:21:13', '2025-12-06 19:32:48'),
	(2, 'Hot Stamping Roll', 'active', '2025-12-06 18:21:13', '2025-12-06 18:21:13'),
	(3, 'VICTOR MSZ30.1', 'active', '2025-12-06 18:21:13', '2025-12-06 18:21:13'),
	(4, 'VICTOR MSZ30.2', 'active', '2025-12-06 18:21:13', '2025-12-06 18:21:13'),
	(5, 'ASB-12M', 'active', '2025-12-06 18:21:13', '2025-12-06 18:21:13'),
	(6, 'CHUMPOWER CPSB TSS 3000', 'active', '2025-12-06 18:21:13', '2025-12-06 18:21:13'),
	(7, 'LANCING AT-150T.1', 'active', '2025-12-06 18:21:13', '2025-12-06 18:21:13'),
	(8, 'LANCING AT-150T.2', 'active', '2025-12-06 18:21:13', '2025-12-06 18:21:13'),
	(9, 'NIGATA CN75E', 'active', '2025-12-06 18:21:13', '2025-12-06 18:21:13'),
	(10, 'ARBURG 420M', 'active', '2025-12-06 18:21:13', '2025-12-06 18:21:13'),
	(11, 'LANCING AT-300T', 'active', '2025-12-06 18:21:13', '2025-12-06 18:21:13'),
	(12, 'Vertical Mixing 100 kg', 'active', '2025-12-06 18:21:13', '2025-12-06 18:21:13'),
	(15, 'Mesin Injection A1', 'inactive', '2025-12-06 19:46:17', '2025-12-15 21:34:13'),
	(16, 'Mesin Blowing B2', 'inactive', '2025-12-06 19:46:17', '2025-12-15 21:34:08');

-- Dumping structure for table production_ek_dev.machine_product
CREATE TABLE IF NOT EXISTS `machine_product` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `machine_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `cycle_time` decimal(8,2) NOT NULL,
  `actual_cycle_time` decimal(8,2) NOT NULL DEFAULT '0.00',
  `cavity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `machine_product_machine_id_foreign` (`machine_id`),
  KEY `machine_product_product_id_foreign` (`product_id`),
  CONSTRAINT `machine_product_machine_id_foreign` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) ON DELETE CASCADE,
  CONSTRAINT `machine_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table production_ek_dev.machine_product: ~6 rows (approximately)
INSERT INTO `machine_product` (`id`, `machine_id`, `product_id`, `cycle_time`, `actual_cycle_time`, `cavity`, `created_at`, `updated_at`) VALUES
	(2, 3, 1, 20.00, 19.00, 5, '2025-12-06 22:14:06', '2025-12-27 04:28:57'),
	(3, 1, 165, 6.00, 5.00, 8, '2025-12-06 22:34:26', '2025-12-25 21:37:15'),
	(5, 1, 164, 5.00, 0.00, 1, '2025-12-06 22:37:13', '2025-12-06 22:37:13'),
	(6, 1, 163, 10.00, 0.00, 1, '2025-12-06 22:38:59', '2025-12-06 22:38:59'),
	(7, 4, 162, 0.00, 0.00, 1, '2025-12-07 05:48:32', '2025-12-07 05:48:32'),
	(10, 12, 161, 0.00, 0.00, 1, '2025-12-16 05:19:41', '2025-12-16 05:19:41');

-- Dumping structure for table production_ek_dev.machine_reject_item
CREATE TABLE IF NOT EXISTS `machine_reject_item` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `machine_id` bigint unsigned NOT NULL,
  `reject_item_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `machine_reject_item_machine_id_foreign` (`machine_id`),
  KEY `machine_reject_item_reject_item_id_foreign` (`reject_item_id`),
  CONSTRAINT `machine_reject_item_machine_id_foreign` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) ON DELETE CASCADE,
  CONSTRAINT `machine_reject_item_reject_item_id_foreign` FOREIGN KEY (`reject_item_id`) REFERENCES `reject_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table production_ek_dev.machine_reject_item: ~108 rows (approximately)
INSERT INTO `machine_reject_item` (`id`, `machine_id`, `reject_item_id`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, NULL, NULL),
	(2, 1, 3, NULL, NULL),
	(7, 3, 1, NULL, NULL),
	(8, 3, 2, NULL, NULL),
	(9, 3, 4, NULL, NULL),
	(10, 3, 11, NULL, NULL),
	(11, 3, 12, NULL, NULL),
	(12, 3, 9, NULL, NULL),
	(13, 3, 5, NULL, NULL),
	(14, 3, 8, NULL, NULL),
	(15, 3, 10, NULL, NULL),
	(16, 3, 6, NULL, NULL),
	(17, 4, 1, NULL, NULL),
	(18, 4, 2, NULL, NULL),
	(19, 4, 4, NULL, NULL),
	(20, 4, 11, NULL, NULL),
	(21, 4, 12, NULL, NULL),
	(22, 4, 9, NULL, NULL),
	(23, 4, 5, NULL, NULL),
	(24, 4, 8, NULL, NULL),
	(25, 4, 10, NULL, NULL),
	(26, 4, 6, NULL, NULL),
	(27, 11, 1, NULL, NULL),
	(28, 11, 14, NULL, NULL),
	(29, 11, 3, NULL, NULL),
	(30, 11, 13, NULL, NULL),
	(31, 11, 2, NULL, NULL),
	(32, 11, 4, NULL, NULL),
	(33, 11, 15, NULL, NULL),
	(34, 11, 12, NULL, NULL),
	(35, 11, 9, NULL, NULL),
	(36, 11, 8, NULL, NULL),
	(37, 11, 6, NULL, NULL),
	(38, 7, 1, NULL, NULL),
	(39, 7, 2, NULL, NULL),
	(40, 7, 9, NULL, NULL),
	(41, 7, 5, NULL, NULL),
	(42, 7, 8, NULL, NULL),
	(43, 7, 6, NULL, NULL),
	(44, 7, 17, NULL, NULL),
	(45, 7, 20, NULL, NULL),
	(46, 7, 19, NULL, NULL),
	(47, 7, 22, NULL, NULL),
	(48, 7, 21, NULL, NULL),
	(49, 7, 18, NULL, NULL),
	(50, 7, 7, NULL, NULL),
	(51, 8, 1, NULL, NULL),
	(52, 8, 17, NULL, NULL),
	(53, 8, 20, NULL, NULL),
	(54, 8, 19, NULL, NULL),
	(55, 8, 2, NULL, NULL),
	(56, 8, 22, NULL, NULL),
	(57, 8, 21, NULL, NULL),
	(58, 8, 9, NULL, NULL),
	(59, 8, 5, NULL, NULL),
	(60, 8, 8, NULL, NULL),
	(61, 8, 18, NULL, NULL),
	(62, 8, 6, NULL, NULL),
	(63, 8, 7, NULL, NULL),
	(64, 9, 1, NULL, NULL),
	(65, 9, 17, NULL, NULL),
	(66, 9, 20, NULL, NULL),
	(67, 9, 19, NULL, NULL),
	(68, 9, 2, NULL, NULL),
	(69, 9, 22, NULL, NULL),
	(70, 9, 21, NULL, NULL),
	(71, 9, 9, NULL, NULL),
	(72, 9, 5, NULL, NULL),
	(73, 9, 8, NULL, NULL),
	(74, 9, 18, NULL, NULL),
	(75, 9, 6, NULL, NULL),
	(76, 9, 7, NULL, NULL),
	(77, 6, 1, NULL, NULL),
	(78, 6, 24, NULL, NULL),
	(79, 6, 27, NULL, NULL),
	(80, 6, 3, NULL, NULL),
	(81, 6, 13, NULL, NULL),
	(82, 6, 2, NULL, NULL),
	(83, 6, 25, NULL, NULL),
	(84, 6, 26, NULL, NULL),
	(85, 6, 12, NULL, NULL),
	(86, 6, 8, NULL, NULL),
	(87, 6, 10, NULL, NULL),
	(88, 6, 23, NULL, NULL),
	(89, 6, 6, NULL, NULL),
	(90, 1, 24, NULL, NULL),
	(91, 1, 27, NULL, NULL),
	(92, 1, 13, NULL, NULL),
	(93, 1, 2, NULL, NULL),
	(94, 1, 25, NULL, NULL),
	(95, 1, 26, NULL, NULL),
	(96, 1, 12, NULL, NULL),
	(97, 1, 8, NULL, NULL),
	(98, 1, 10, NULL, NULL),
	(99, 1, 23, NULL, NULL),
	(100, 1, 6, NULL, NULL),
	(101, 5, 1, NULL, NULL),
	(102, 5, 24, NULL, NULL),
	(103, 5, 27, NULL, NULL),
	(104, 5, 3, NULL, NULL),
	(105, 5, 13, NULL, NULL),
	(106, 5, 2, NULL, NULL),
	(107, 5, 25, NULL, NULL),
	(108, 5, 26, NULL, NULL),
	(109, 5, 12, NULL, NULL),
	(110, 5, 8, NULL, NULL),
	(111, 5, 10, NULL, NULL),
	(112, 5, 23, NULL, NULL),
	(113, 5, 6, NULL, NULL),
	(114, 2, 28, NULL, NULL),
	(115, 2, 1, NULL, NULL),
	(116, 2, 2, NULL, NULL),
	(117, 2, 29, NULL, NULL),
	(118, 2, 5, NULL, NULL),
	(119, 2, 8, NULL, NULL);

-- Dumping structure for table production_ek_dev.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table production_ek_dev.migrations: ~19 rows (approximately)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2025_12_07_004407_create_products_table', 1),
	(5, '2025_12_07_005536_create_machines_table', 1),
	(6, '2025_12_07_005545_create_machine_product_table', 1),
	(7, '2025_12_07_014033_add_status_to_machines_table', 2),
	(8, '2025_12_07_014954_drop_code_column_from_machines_table', 3),
	(9, '2025_12_07_054510_create_colors_table', 4),
	(10, '2025_12_07_070838_create_reject_categories_table', 5),
	(11, '2025_12_07_070851_create_reject_items_table', 5),
	(12, '2025_12_07_072557_create_shifts_table', 6),
	(13, '2025_12_07_073714_create_coordinators_table', 7),
	(15, '2025_12_07_113541_create_downtimes_table', 8),
	(16, '2025_12_07_120050_create_packaging_types_table', 9),
	(17, '2025_12_07_121139_add_conversion_fields_to_packaging_types_table', 10),
	(18, '2025_12_07_122445_create_operators_table', 11),
	(19, '2025_12_07_123508_add_cavity_to_machine_product_table', 12),
	(20, '2025_12_07_130535_create_batches_table', 13),
	(21, '2025_12_07_131801_add_priority_to_batches_table', 14),
	(22, '2025_12_07_134105_create_daily_reports_table', 15),
	(23, '2025_12_07_134112_create_daily_report_details_tables', 15),
	(24, '2025_12_11_120134_add_yield_to_daily_reports_table', 16),
	(25, '2025_12_16_121326_create_machine_reject_item_table', 17),
	(26, '2025_12_16_123248_add_packaging_type_id_to_products_table', 18),
	(27, '2025_12_17_121251_add_actual_cycle_time_to_machine_product_table', 19),
	(28, '2025_12_18_123811_add_status_to_users_table', 20),
	(29, '2025_12_18_124539_modify_role_enum_in_users_table', 21),
	(30, '2025_12_19_130712_add_packaging_qty_to_products_table', 22),
	(31, '2025_12_19_132810_drop_conversion_quantity_from_packaging_types', 23),
	(32, '2025_12_20_222628_clean_unused_tables', 23),
	(33, '2025_12_26_034401_add_weight_and_sample_columns', 24);

-- Dumping structure for table production_ek_dev.operators
CREATE TABLE IF NOT EXISTS `operators` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `operators_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table production_ek_dev.operators: ~13 rows (approximately)
INSERT INTO `operators` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'Budi Santoso', 'active', '2025-12-07 05:29:13', '2025-12-12 22:15:02'),
	(2, 'Eko Prasetyo', 'active', '2025-12-07 05:29:13', '2025-12-07 05:29:13'),
	(3, 'Rizky Kurniawan', 'active', '2025-12-07 05:29:13', '2025-12-12 20:29:57'),
	(4, 'Agus Setiawan', 'active', '2025-12-07 05:29:13', '2025-12-15 19:41:55'),
	(5, 'Doni Haryanto', 'active', '2025-12-07 05:29:13', '2025-12-07 05:29:13'),
	(6, 'Fajar Nugroho', 'active', '2025-12-07 05:29:13', '2025-12-07 05:29:13'),
	(7, 'Gilang Ramadhan', 'active', '2025-12-07 05:29:13', '2025-12-07 05:29:13'),
	(8, 'Hendra Wijaya', 'active', '2025-12-07 05:29:13', '2025-12-07 05:29:13'),
	(9, 'Indra Gunawan', 'active', '2025-12-07 05:29:13', '2025-12-07 05:29:13'),
	(10, 'Joko Susilo', 'active', '2025-12-07 05:29:13', '2025-12-07 05:29:13'),
	(11, 'Pandu Wijaksono', 'active', '2025-12-11 06:07:21', '2025-12-11 06:07:21'),
	(12, 'Slamet Riyadi', 'active', '2025-12-12 21:46:31', '2025-12-12 21:46:31'),
	(13, 'Wahyu Hidayat', 'active', '2025-12-12 21:46:31', '2025-12-15 19:43:07');

-- Dumping structure for table production_ek_dev.packaging_types
CREATE TABLE IF NOT EXISTS `packaging_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `packaging_types_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table production_ek_dev.packaging_types: ~5 rows (approximately)
INSERT INTO `packaging_types` (`id`, `name`, `content_unit`, `status`, `created_at`, `updated_at`) VALUES
	(1, '486 x 296 x 364', 'Pcs', 'active', '2025-12-07 05:18:19', '2025-12-19 07:01:52'),
	(2, '590 x 420 x 530', 'Pcs', 'active', '2025-12-07 05:18:19', '2025-12-19 19:48:41'),
	(3, '486 x 296 x 334', 'Pcs', 'active', '2025-12-07 05:18:19', '2025-12-19 06:37:39'),
	(4, 'Pallet Kayu', 'Dus', 'inactive', '2025-12-07 05:18:19', '2025-12-19 19:50:49'),
	(5, 'Karton Reuse (Indotirta)', 'Pcs', 'inactive', '2025-12-07 05:18:19', '2025-12-20 03:24:05');

-- Dumping structure for table production_ek_dev.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `packaging_type_id` bigint unsigned DEFAULT NULL,
  `packaging_qty` decimal(8,2) DEFAULT NULL,
  `weight` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT 'Berat standar per pcs (Gram)',
  PRIMARY KEY (`id`),
  KEY `products_packaging_type_id_foreign` (`packaging_type_id`),
  CONSTRAINT `products_packaging_type_id_foreign` FOREIGN KEY (`packaging_type_id`) REFERENCES `packaging_types` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table production_ek_dev.products: ~165 rows (approximately)
INSERT INTO `products` (`id`, `name`, `status`, `created_at`, `updated_at`, `packaging_type_id`, `packaging_qty`, `weight`) VALUES
	(1, 'Botol HDPE MHS 100 mL', 'aktif', '2025-12-06 20:42:59', '2025-12-27 04:28:57', 3, 250.00, 2.500),
	(2, 'Botol HDPE MHS 100 mL - Kuning', 'aktif', '2025-12-06 20:42:59', '2025-12-19 05:58:49', NULL, NULL, 0.000),
	(3, 'Botol HDPE MHS 100 mL - Merah', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(4, 'Botol HDPE MHS 100 mL - Hitam', 'aktif', '2025-12-06 20:42:59', '2025-12-25 22:24:46', 3, 250.00, 0.000),
	(5, 'Tutup PP MHS 100 mL', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(6, 'Tutup PP MHS 100 mL - Kuning', 'aktif', '2025-12-06 20:42:59', '2025-12-19 07:08:46', 1, 1000.00, 0.000),
	(7, 'Tutup PP MHS 100 mL - Merah', 'aktif', '2025-12-06 20:42:59', '2025-12-19 07:08:46', 1, 1000.00, 0.000),
	(8, 'Tutup PP MHS 100 mL - Hitam', 'aktif', '2025-12-06 20:42:59', '2025-12-19 07:08:46', 1, 1000.00, 0.000),
	(9, 'Plug LDPE MHS 100 mL', 'aktif', '2025-12-06 20:42:59', '2025-12-25 22:24:46', 3, 15000.00, 0.000),
	(10, 'Botol PET Sinai 60 mL 9.5', 'aktif', '2025-12-06 20:42:59', '2025-12-19 07:08:46', 1, 512.00, 0.000),
	(11, 'Tutup PP Sinai 60 mL', 'aktif', '2025-12-06 20:42:59', '2025-12-25 22:24:46', 3, 4750.00, 0.000),
	(12, 'Botol PET Sari Kurma 350gr Clear P23', 'aktif', '2025-12-06 20:42:59', '2025-12-19 19:48:41', 2, 330.00, 0.000),
	(13, 'Tutup PP Sari Kurma 350 Gram', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(14, 'Botol PET Madu HNI 190 mL Clear P23', 'aktif', '2025-12-06 20:42:59', '2025-12-19 19:48:41', 2, 432.00, 0.000),
	(15, 'Tutup PP Madu HNI 190 mL', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(16, 'Botol PET Minyak Kayu Putih 100 mL', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(17, 'Tutup PP Minyak Kayu Putih 100 mL', 'aktif', '2025-12-06 20:42:59', '2025-12-19 07:08:46', 1, 4750.00, 0.000),
	(18, 'Botol PET Minyak Telon 100 mL P12.5', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(19, 'Tutup PP Minyak Telon 100 mL', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(20, 'Botol PET Sano 73 mL', 'aktif', '2025-12-06 20:42:59', '2025-12-19 07:08:46', 1, 308.00, 0.000),
	(21, 'Tutup HDPE Putih Sano 73 mL', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(22, 'Botol PET Zidavit 200 mL Clear P30', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(23, 'Tutup HDPE Sano Zidavit', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(24, 'Botol PET Deep Olive 250 mL', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(25, 'Tutup PP Deep Olive', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(26, 'Botol PET Extrafood 200 mL Clear P30', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(27, 'Tutup HDPE Extrafood', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(28, 'Botol HDPE Deep Squa 60 mL White', 'aktif', '2025-12-06 20:42:59', '2025-12-19 05:58:49', NULL, NULL, 0.000),
	(29, 'Tutup PP Deep Squa 60 mL White', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(30, 'Botol HDPE Deep Squa 100 mL White', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(31, 'Tutup PP Deep Squa 100 mL White', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(32, 'Botol PET Sano 200 mL Amber P30 - Polos', 'aktif', '2025-12-06 20:42:59', '2025-12-19 07:08:46', 1, 118.00, 0.000),
	(33, 'Tutup HDPE Sano - Namasindo', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(34, 'Botol PET Sano 135 mL Clear P22', 'aktif', '2025-12-06 20:42:59', '2025-12-19 07:08:46', 1, 189.00, 0.000),
	(35, 'Botol PET Waji Oil 100 mL Hitam Transparan P17', 'aktif', '2025-12-06 20:42:59', '2025-12-25 22:24:46', 3, 252.00, 0.000),
	(36, 'Pump Spray Waji Oil', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(37, 'Tutup Waji Oil', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(38, 'Botol PET Waji Oil 65 mL Hitam Transparan P17', 'aktif', '2025-12-06 20:42:59', '2025-12-25 22:24:46', 3, 399.00, 0.000),
	(39, 'Tutup Jamur D24 Hitam', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(40, 'Tutup HDPE Sano Gold - Indotirta', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(41, 'Botol PET Sano 200 mL Amber P30 - BW', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(42, 'Tutup HDPE Sano - Indotirta', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(43, 'Botol PET Gizidat 130 mL Clear P14', 'aktif', '2025-12-06 20:42:59', '2025-12-19 19:48:41', 2, 520.00, 0.000),
	(44, 'Tutup HDPE Gizidat Putih', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(45, 'Botol PET Zam-zam 100 mL Clear P16', 'aktif', '2025-12-06 20:42:59', '2025-12-19 07:08:46', 1, 216.00, 0.000),
	(46, 'Tutup HDPE Zam-zam Lip Ring 100 mL', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(47, 'Botol PET Zam Zam Fluba 100 mL Clear P16', 'aktif', '2025-12-06 20:42:59', '2025-12-19 07:08:46', 1, 216.00, 0.000),
	(48, 'Botol PET Gizidat 60 mL Clear P10.7', 'aktif', '2025-12-06 20:42:59', '2025-12-25 22:24:46', 3, 364.00, 0.000),
	(49, 'Botol PET Kale 150 mL Clear P16.5', 'aktif', '2025-12-06 20:42:59', '2025-12-19 07:08:46', 1, 187.00, 0.000),
	(50, 'Tutup HDPE Gizidat Hitam', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(51, 'Botol PET Kale 250 mL Clear P27', 'aktif', '2025-12-06 20:42:59', '2025-12-19 07:08:46', 1, 100.00, 0.000),
	(52, 'Tutup HDPE Kale D38 Hitam', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(53, 'Botol PET Sano 135 mL Amber P19', 'aktif', '2025-12-06 20:42:59', '2025-12-19 07:08:46', 1, 189.00, 0.000),
	(54, 'Botol PET Zam-zam 60 mL Clear P10.8', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(55, 'Tutup HDPE D30 Putih', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(56, 'Botol PET Kapsul D38 100 mL Amber', 'aktif', '2025-12-06 20:42:59', '2025-12-25 22:24:46', 3, 228.00, 0.000),
	(57, 'Tutup HDPE Kapsul Hitam', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(58, 'Tutup HDPE Kapsul Gold', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(59, 'Botol PET Kapsul D38 135 mL Putih', 'aktif', '2025-12-06 20:42:59', '2025-12-25 22:24:46', 3, 177.00, 0.000),
	(60, 'Tutup HDPE Kapsul D38 Putih-Polos', 'aktif', '2025-12-06 20:42:59', '2025-12-25 22:24:46', 3, 2000.00, 0.000),
	(61, 'Tutup HDPE Kapsul D38 Double List Gold', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(62, 'Botol PET Kapsul D38 135 mL Hitam', 'aktif', '2025-12-06 20:42:59', '2025-12-19 07:08:46', 1, 216.00, 0.000),
	(63, 'Tutup HDPE Kapsul D38 Hitam-Polos', 'aktif', '2025-12-06 20:42:59', '2025-12-25 22:24:46', 3, 2000.00, 0.000),
	(64, 'Botol PET Sano 200 mL Amber P30 - TJI', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(65, 'Botol PET Round 200 mL Clear P30', 'aktif', '2025-12-06 20:42:59', '2025-12-25 22:24:46', 3, 128.00, 0.000),
	(66, 'Botol PET Vitabumin 130 mL Clear P22', 'aktif', '2025-12-06 20:42:59', '2025-12-19 19:48:41', 2, 520.00, 0.000),
	(67, 'Tutup HDPE Vitabumin 130 mL', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(68, 'Botol PET Zam-zam 100 mL Amber P19', 'aktif', '2025-12-06 20:42:59', '2025-12-19 07:08:46', 1, 198.00, 0.000),
	(69, 'Botol PET Madu TJ 150 mL Clear', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(70, 'Botol PET Madu TJ 250 mL Clear', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(71, 'Tutup Fliptop Madu Kuning Membran', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(72, 'Botol HDPE CMM 150 mL', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(73, 'Botol PET SHL 350 mL Clear P42', 'aktif', '2025-12-06 20:42:59', '2025-12-19 19:48:41', 2, 180.00, 0.000),
	(74, 'Tutup HDPE SHL 350 mL', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(75, 'Botol HDPE ZJP 266 mL', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(76, 'Botol HDPE ZJP 465 mL', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(77, 'Botol PET ZJP 500 mL', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(78, 'Jeriken HDPE ZJP 5 Liter', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(79, 'Tutup PP Jeriken ZJP 5 Liter', 'aktif', '2025-12-06 20:42:59', '2025-12-06 20:42:59', NULL, NULL, 0.000),
	(80, 'Botol PET PUM 600 mL', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(81, 'Tutup HDPE PUM 600 mL', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(82, 'Botol PET Kapsul PS 60 mL Hitam', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(83, 'Tutup PP Kapsul List Gold PS 60 mL + Stamping Logo', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(84, 'Plug PP Kapsul List Gold PS 60 mL', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(85, 'Botol PET Vicofood 180 mL Clear P27 Bulat', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(86, 'Tutup PP 180 Bulat', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(87, 'Botol PET Vicofood 250 mL Clear P27 Kotak', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(88, 'Tutup PP 250 Kotak', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(89, 'Botol PET Sano 200 mL Clear P30 - Ardhi Jaya', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(90, 'Botol PET Kapsul D38 100 mL Clear', 'aktif', '2025-12-06 20:43:00', '2025-12-25 22:24:46', 3, 228.00, 0.000),
	(91, 'Tutup PP Kapsul D38 Natural', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(92, 'Tutup PP Kapsul D38 Putih Polos', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(93, 'Botol PET AMDK 330 mL Bluewis P9.1', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(94, 'Botol PET AMDK 600 mL Bluewis P12', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(95, 'Botol PET SterIlyn 500 mL (tanpa tutup)', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(96, 'Botol HDPE Bodywash 250 mL Kuning (Brightening)', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(97, 'Botol HDPE Bodywash 250 mL Coral (Protecting)', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(98, 'Tutup PP Fliptop Jamur Body Wash 24 White', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(99, 'Botol PET Kapsul 120 mL Hijau', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(100, 'Tutup PP Kapsul List Gold 120 mL', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(101, 'Botol HDPE Vermint 30 mL Orange', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(102, 'Tutup PP Vermint Orange 30mL Stamping', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(103, 'Botol HDPE Vermint 60 mL Orange', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(104, 'Tutup PP Vermint Orange 60mL Stamping', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(105, 'Botol PET Spiva 550 mL Clear P27 (tanpa tutup)', 'aktif', '2025-12-06 20:43:00', '2025-12-19 19:48:41', 2, 140.00, 0.000),
	(106, 'Botol PET Spiva 380 mL Clear P27 (tanpa tutup)', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(107, 'Botol HDPE Pupuk 1000ml Putih PK', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(108, 'Tutup PP Pupuk Putih', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(109, 'Plug LDPE Pupuk Natural', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(110, 'Botol HDPE Pupuk 1000ml Kuning Povidione', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(111, 'Tutup PP Pupuk Merah', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(112, 'Tutup PP Kapsul D38 Putih List Silver', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(113, 'Tutup PP Kapsul D38 Putih List Gold', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(114, 'Botol PET Kapsul D38 135 mL Clear', 'aktif', '2025-12-06 20:43:00', '2025-12-19 07:08:46', 1, 216.00, 0.000),
	(115, 'Botol PET Madu HNI 190 mL Clear P23 - Polos', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(116, 'Pump Spray D24 Hitam', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(117, 'Botol HDPE Vermint 60 mL Hijau', 'aktif', '2025-12-06 20:43:00', '2025-12-19 05:58:49', NULL, NULL, 0.000),
	(118, 'Tutup PP Vermint Hijau 60mL Stamping', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(119, 'Botol PET Susu UHT', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(120, 'Tutup HDPE Susu UHT', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(121, 'Botol PET Zam-zam 100 mL Clear JD', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(122, 'Botol PET Susu D30 250 mL Clear JD', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(123, 'Tutup PP D30 Zam-zam Putih JD', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(124, 'Botol PET Zam-zam Susu 130 mL Clear IP', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(125, 'Tutup PP Zam-zam Susu 130 mL IP', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(126, 'Botol HDPE Kapsul 100 mL Putih', 'aktif', '2025-12-06 20:43:00', '2025-12-19 05:58:49', NULL, NULL, 0.000),
	(127, 'Tutup PP Kapsul Naturonal + Foam Alu', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(128, 'Botol PET Kale 500 mL Clear P42', 'aktif', '2025-12-06 20:43:00', '2025-12-19 19:50:16', 5, 200.00, 0.000),
	(129, 'Botol PET Kale 250 mL Clear P27 (Free Item)', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(130, 'Tutup HDPE Kale D38 Hitam (Free Item)', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(131, 'Botol PET Kale 500 mL Clear P42 (Free Item)', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(132, 'Tutup HDPE Kale D38 Putih (Free Item)', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(133, 'Ember Plastik PP 4 Lt + Handle', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(134, 'Ember Plastik PP 18 Lt + Tutup Putih + Handle', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(135, 'Cup 120 mL HD', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(136, 'Cup 200 mL KP', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(137, 'Botol PET 330 mL SN30 Clear NO', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(138, 'Botol PET 220 mL SN30 Clear KP', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(139, 'Botol PET 600 mL SN30 Clear NO', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(140, 'Botol PET 1500 mL SN30 Clear PB', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(141, 'Tutup HDPE Orange SN30 OP', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(142, 'Botol PET 330 mL SN30 Bluewis 9.1', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(143, 'Botol PET 600 mL SN30 Bluewis 12', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(144, 'Tutup HDPE Biru Muda SN30', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(145, 'Botol PET 330 mL LN30 Clear NO', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(146, 'Botol PET 600 mL LN30 Clear NO', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(147, 'Botol PET 1500 mL LN30 Clear NO', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(148, 'Tutup HDPE Putih LN30', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(149, 'POT Cream 10gr Putih', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(150, 'Plug POT Cream 10gr Clear', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(151, 'Tutup POT Cream 10gr Putih', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(152, 'Sendok Takar Susu', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(153, 'Tutup HDPE Biru LN30', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(154, 'Tutup PP Kapsul + Foam Alu', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(155, 'Botol PET Cimory 250 mL Clear P23', 'aktif', '2025-12-06 20:43:00', '2025-12-19 19:48:41', 2, 520.00, 0.000),
	(156, 'Tutup PP Kapsul 100 mL', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(157, 'Botol PET Kapsul D38 100 mL Putih', 'aktif', '2025-12-06 20:43:00', '2025-12-25 22:24:46', 3, 228.00, 0.000),
	(158, 'Tutup PP Kapsul D38 Hitam Double List Gold', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(159, 'Botol PET Body Mist 30 mL Pink', 'aktif', '2025-12-06 20:43:00', '2025-12-25 22:24:46', 3, 528.00, 0.000),
	(160, 'Botol PET Body Mist 60 mL Pink', 'aktif', '2025-12-06 20:43:00', '2025-12-19 07:08:46', 1, 440.00, 0.000),
	(161, 'Botol PET Body Wash 250 mL Frosted', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(162, 'Botol PET Madu 190 mL Clear P23', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(163, 'Tutup PP Madu 190 mL Kuning', 'aktif', '2025-12-06 20:43:00', '2025-12-06 20:43:00', NULL, NULL, 0.000),
	(164, 'Botol PET Sari Kurma 350gr Clear P23 - Polos', 'aktif', '2025-12-06 20:43:00', '2025-12-20 01:19:51', NULL, NULL, 0.000),
	(165, 'Tutup PP Kapsul D38 Hitam List Gold', 'aktif', '2025-12-06 20:43:00', '2025-12-25 22:24:46', 3, 250.00, 2.500);

-- Dumping structure for table production_ek_dev.reject_categories
CREATE TABLE IF NOT EXISTS `reject_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reject_categories_name_unique` (`name`),
  UNIQUE KEY `reject_categories_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table production_ek_dev.reject_categories: ~2 rows (approximately)
INSERT INTO `reject_categories` (`id`, `name`, `code`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'REJECT GA', 'GA', 'active', '2025-12-07 00:12:46', '2025-12-07 00:12:46'),
	(2, 'REJECT RECYCLE', 'RECYCLE', 'active', '2025-12-07 00:12:46', '2025-12-07 00:12:46');

-- Dumping structure for table production_ek_dev.reject_items
CREATE TABLE IF NOT EXISTS `reject_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reject_items_name_unique` (`name`),
  KEY `reject_items_category_id_foreign` (`category_id`),
  CONSTRAINT `reject_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `reject_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table production_ek_dev.reject_items: ~28 rows (approximately)
INSERT INTO `reject_items` (`id`, `category_id`, `name`, `status`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Black Spot', 'active', '2025-12-07 00:12:46', '2025-12-11 06:10:53'),
	(2, 1, 'Contamination', 'active', '2025-12-07 00:12:46', '2025-12-07 00:12:46'),
	(3, 2, 'Bubble', 'active', '2025-12-07 00:12:46', '2025-12-07 00:12:46'),
	(4, 2, 'Flash', 'active', '2025-12-07 00:12:46', '2025-12-07 00:12:46'),
	(5, 2, 'Short Mould', 'active', '2025-12-07 00:12:46', '2025-12-07 00:12:46'),
	(6, 2, 'Warna TMS', 'active', '2025-12-07 00:12:46', '2025-12-07 00:12:46'),
	(7, 2, 'Weld Line', 'active', '2025-12-07 00:12:46', '2025-12-07 00:12:46'),
	(8, 1, 'Start up', 'active', '2025-12-19 05:15:25', '2025-12-19 05:15:25'),
	(9, 2, 'Setting', 'active', '2025-12-19 05:15:59', '2025-12-19 05:15:59'),
	(10, 2, 'Tipis', 'active', '2025-12-19 05:16:57', '2025-12-19 05:16:57'),
	(11, 2, 'Kulit Jeruk', 'active', '2025-12-19 05:17:26', '2025-12-19 05:17:26'),
	(12, 2, 'Scratch', 'active', '2025-12-19 05:17:51', '2025-12-19 05:17:51'),
	(13, 1, 'Cloudy', 'active', '2025-12-19 05:18:24', '2025-12-19 05:18:24'),
	(14, 1, 'Body Bergelombang', 'active', '2025-12-19 05:18:47', '2025-12-19 05:18:47'),
	(15, 1, 'Pelangi Tidak Rapi', 'active', '2025-12-19 05:19:15', '2025-12-19 05:19:15'),
	(17, 2, 'Body Asimetri', 'active', '2025-12-19 05:20:06', '2025-12-19 05:20:06'),
	(18, 2, 'Ulir rusak', 'active', '2025-12-19 05:20:19', '2025-12-19 05:20:19'),
	(19, 2, 'Cembung', 'active', '2025-12-19 05:20:47', '2025-12-19 05:20:47'),
	(20, 2, 'Cekung', 'active', '2025-12-19 05:20:58', '2025-12-19 05:20:58'),
	(21, 2, 'Pin/ Engsel patah', 'active', '2025-12-19 05:21:14', '2025-12-19 05:21:14'),
	(22, 2, 'Permukaan silver/ glossy & doff', 'active', '2025-12-19 05:21:23', '2025-12-19 05:21:23'),
	(23, 1, 'Unmold (semua posisi)', 'active', '2025-12-19 05:21:55', '2025-12-19 05:21:55'),
	(24, 1, 'Bottom pecah/retak/bolong', 'active', '2025-12-19 05:22:24', '2025-12-19 05:22:24'),
	(25, 1, 'Gagal blow', 'active', '2025-12-19 05:22:39', '2025-12-19 05:22:39'),
	(26, 1, 'Logo Hilang', 'active', '2025-12-19 05:23:25', '2025-12-19 05:23:25'),
	(27, 1, 'Bottom tidak center', 'active', '2025-12-19 05:23:39', '2025-12-19 05:23:39'),
	(28, 1, 'Baret', 'active', '2025-12-19 05:23:56', '2025-12-19 05:23:56'),
	(29, 1, 'List miring', 'active', '2025-12-19 05:24:14', '2025-12-19 05:24:14');

-- Dumping structure for table production_ek_dev.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table production_ek_dev.sessions: ~2 rows (approximately)
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('gXvncM9lF3tQVg0sALsGl2yGvLj51YU29HSxwPdl', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibHNveTR3NTRpQk9ud3A5QWF0NFhsNko4cG53cFl1Z1ozRlJMNnRKMCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC91c2VycyI7czo1OiJyb3V0ZSI7czoxMToidXNlcnMuaW5kZXgiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1766840852);

-- Dumping structure for table production_ek_dev.shifts
CREATE TABLE IF NOT EXISTS `shifts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shifts_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table production_ek_dev.shifts: ~2 rows (approximately)
INSERT INTO `shifts` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'Shift 1', 'active', '2025-12-07 00:27:57', '2025-12-11 06:10:44'),
	(2, 'Shift 2', 'active', '2025-12-07 00:27:57', '2025-12-07 00:27:57'),
	(3, 'Shift 3', 'active', '2025-12-07 00:27:57', '2025-12-07 00:27:57');

-- Dumping structure for table production_ek_dev.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('super_admin','admin','leader','manager') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'leader',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table production_ek_dev.users: ~5 rows (approximately)
INSERT INTO `users` (`id`, `name`, `email`, `role`, `status`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Administrator', 'admin@ekdev.com', 'super_admin', 'active', NULL, '$2y$12$NiP9F4TvXp6neJm493VhHODDO5HaamGroAfHeEUmlpuVWDnsgMP6W', NULL, '2025-12-15 12:09:26', '2025-12-18 05:48:53'),
	(2, 'Koordinator Produksi', 'op@ekdev.com', 'leader', 'active', NULL, '$2y$12$2JyI5.W4eiZ1VibnH.LOAuEU9HFqOL5bZDvUq7yLSe6.3qjbuu6tq', NULL, '2025-12-15 12:09:26', '2025-12-18 05:42:52'),
	(3, 'General Manager', 'gm@ekdev.com', 'manager', 'active', NULL, '$2y$12$8EWYqWMO1cKoXQH8gjMuaOhU7gSly88GzaGa8DhH9i/cCtxeeIGx.', NULL, '2025-12-15 12:09:26', '2025-12-18 05:42:37'),
	(4, 'SPV Produksi', 'produksi@ekdev.com', 'admin', 'active', NULL, '$2y$12$ZjDcwmCtIUhRilmRjhq1e.UGXhRZJqmuSftiVzDnMBguetYfSSBE2', NULL, '2025-12-18 05:40:39', '2025-12-18 05:43:08'),
	(5, 'SPV PPIC', 'ppic@ekdev.com', 'admin', 'active', NULL, '$2y$12$MfsyYjoA2jmXVU/IboXJhOkQjDOjtFtIBY0FBmm6Xdhtjn5GY5Zci', NULL, '2025-12-18 05:42:25', '2025-12-18 05:42:25');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
