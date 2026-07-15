-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 15, 2026 at 02:17 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `business_order`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_metrics`
--

CREATE TABLE `access_metrics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `access` text NOT NULL,
  `roleId` int(11) NOT NULL,
  `isDeleted` int(11) NOT NULL DEFAULT 0,
  `createdBy` int(11) NOT NULL,
  `createdDtm` datetime NOT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `access_metrics`
--

INSERT INTO `access_metrics` (`id`, `access`, `roleId`, `isDeleted`, `createdBy`, `createdDtm`, `updatedBy`, `updatedDtm`, `created_at`, `updated_at`) VALUES
(12, '[{\"module\":\"dashboard\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"settings\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"journal\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"hr\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"order\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"buy\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"gudam\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"sales\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"expense\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"reports\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"users\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"backup\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0}]', 2, 0, 13, '2026-07-11 14:34:54', 13, '2026-07-11 14:34:54', '2025-03-09 07:57:07', '2026-07-11 10:04:54'),
(13, '[{\"module\":\"settings\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"rates\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"journal\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"income\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"expense\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"hr\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"buy\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"gudam\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"sales\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"clearance\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"reports\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0}]', 1, 0, 1, '2025-03-09 12:28:05', 1, '2025-03-09 12:28:05', '2025-03-09 07:57:45', '2025-03-09 07:58:05'),
(14, '[{\"module\":\"settings\",\"label\":0,\"total_access\":0,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"rates\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"journal\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"income\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"expense\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"hr\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"buy\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"gudam\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"sales\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"clearance\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"reports\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"users\",\"label\":0,\"total_access\":0,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0}]', 6, 0, 8, '2025-03-09 16:32:46', 8, '2025-03-09 16:32:46', '2025-03-09 07:58:13', '2025-03-09 12:02:46'),
(15, '[{\"module\":\"settings\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":1,\"edit_records\":0,\"delete_records\":0},{\"module\":\"rates\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":1,\"edit_records\":0,\"delete_records\":0},{\"module\":\"journal\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":1,\"edit_records\":0,\"delete_records\":0},{\"module\":\"income\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":1,\"edit_records\":0,\"delete_records\":0},{\"module\":\"expense\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":1,\"edit_records\":0,\"delete_records\":0},{\"module\":\"hr\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":1,\"edit_records\":0,\"delete_records\":0},{\"module\":\"buy\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":1,\"edit_records\":0,\"delete_records\":0},{\"module\":\"gudam\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":1,\"edit_records\":0,\"delete_records\":0},{\"module\":\"sales\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":1,\"edit_records\":0,\"delete_records\":0},{\"module\":\"clearance\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":1,\"edit_records\":0,\"delete_records\":0},{\"module\":\"reports\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":1,\"edit_records\":0,\"delete_records\":0}]', 8, 0, 1, '2025-03-09 12:28:59', 1, '2025-03-09 12:28:59', '2025-03-09 07:58:40', '2025-03-09 07:58:59'),
(16, '[{\"module\":\"settings\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"rates\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"journal\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"income\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"expense\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"hr\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"buy\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"gudam\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"sales\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"clearance\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"reports\",\"label\":0,\"total_access\":1,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"users\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":1,\"edit_records\":1,\"delete_records\":0}]', 9, 0, 1, '2025-03-11 13:20:25', 1, '2025-03-11 13:20:25', '2025-03-09 12:16:26', '2025-03-11 08:50:25'),
(17, '[{\"module\":\"dashboard\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"settings\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"journal\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"hr\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"order\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"buy\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"gudam\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"sales\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"expense\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"reports\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"users\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"backup\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0}]', 10, 0, 13, '2026-07-11 14:32:01', 13, '2026-07-11 14:32:01', '2026-07-11 08:50:27', '2026-07-11 10:02:01'),
(18, '[{\"module\":\"dashboard\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"settings\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"journal\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"hr\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"order\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":1,\"edit_records\":1,\"delete_records\":0},{\"module\":\"buy\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":1,\"edit_records\":1,\"delete_records\":0},{\"module\":\"gudam\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"sales\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"expense\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":1,\"edit_records\":0,\"delete_records\":0},{\"module\":\"reports\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"users\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"backup\",\"label\":0,\"total_access\":0,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0}]', 11, 0, 13, '2026-07-12 10:06:48', 13, '2026-07-12 10:06:48', '2026-07-11 10:05:57', '2026-07-12 05:36:48');

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(10) UNSIGNED NOT NULL,
  `account_type_id` bigint(20) UNSIGNED NOT NULL,
  `user_account_id` int(11) DEFAULT 0 COMMENT 'has connection with user table',
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_pre_select` int(11) NOT NULL DEFAULT 0,
  `percent` int(11) DEFAULT NULL,
  `net_salary` decimal(10,0) DEFAULT NULL COMMENT 'used for employee',
  `salary_currency` int(11) DEFAULT NULL COMMENT 'used for employee',
  `loan_limit` int(11) DEFAULT NULL,
  `loan_limit_option` tinyint(6) DEFAULT NULL COMMENT '0: no, 1:yes',
  `emp_car_id` bigint(20) DEFAULT NULL,
  `emp_start_date` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `account_type_id`, `user_account_id`, `name`, `phone`, `address`, `description`, `is_pre_select`, `percent`, `net_salary`, `salary_currency`, `loan_limit`, `loan_limit_option`, `emp_car_id`, `emp_start_date`, `created_at`, `updated_at`) VALUES
(33, 1, 0, 'خزانه شرکت', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-03-02 07:24:33', '2026-06-28 03:10:48'),
(83, 3, NULL, 'Abdul Latif Erfan', NULL, 'kabul', NULL, 0, NULL, NULL, 1, NULL, 1, NULL, NULL, '2026-06-19 09:32:01', '2026-07-12 04:42:43'),
(84, 3, 18, 'مشتری احمد', NULL, 'کابل', NULL, 0, NULL, NULL, 1, 6000, 1, NULL, NULL, '2026-06-20 06:57:15', '2026-07-12 05:06:01'),
(85, 3, NULL, 'مشتری محمود', NULL, 'کابل', NULL, 0, NULL, NULL, 1, 3000, 0, NULL, NULL, '2026-06-20 07:27:55', '2026-07-12 04:35:15'),
(86, 4, 0, 'تهیه کننده مواد غذایی خان', NULL, 'هرات', NULL, 0, NULL, NULL, 1, 8000, 1, NULL, NULL, '2026-06-20 07:41:38', '2026-06-20 08:00:30'),
(87, 2, 15, 'کارمند عرفان', NULL, 'هرات', NULL, 0, NULL, 5000, 1, NULL, 0, 21, '2026-10-10', '2026-06-20 08:57:40', '2026-07-15 05:07:48'),
(88, 6, 0, 'شبکه نهله', NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, 0, NULL, NULL, '2026-06-20 08:59:05', '2026-06-20 08:59:05'),
(89, 6, 0, 'شبکه حاجی عوض', NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, 0, NULL, NULL, '2026-06-20 08:59:19', '2026-06-20 08:59:19'),
(90, 6, 0, 'صرافی احمدیان', NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, 0, NULL, NULL, '2026-06-20 08:59:39', '2026-06-20 08:59:39'),
(91, 4, 0, 'احمدی تهیه کننده سبزیجات', NULL, 'kabul', NULL, 0, NULL, NULL, 1, 6000, 1, NULL, NULL, '2026-06-22 14:34:51', '2026-06-22 14:34:51'),
(92, 2, 17, 'کریم درایور مازدا', NULL, 'کابل', NULL, 0, NULL, 8000, 1, NULL, 0, 20, '2026-06-06', '2026-06-22 14:35:36', '2026-07-15 05:07:29'),
(94, 3, 0, 'مشتری قادر سبزی فروش موفق', NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, 0, NULL, NULL, '2026-07-14 15:21:45', '2026-07-14 17:39:48'),
(95, 4, 0, 'رحمت - نوشیدنی ها', NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, 0, NULL, NULL, '2026-07-14 15:22:39', '2026-07-14 17:09:19'),
(96, 4, 0, 'قمبر - خوراکه فروشی توفیق', NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, 0, NULL, NULL, '2026-07-14 15:23:38', '2026-07-14 15:23:38'),
(97, 3, 0, 'مشتری سبزی فروش بهار', NULL, NULL, NULL, 0, NULL, NULL, 1, 60000, 1, NULL, NULL, '2026-07-14 15:24:32', '2026-07-14 15:24:32'),
(98, 3, 0, 'مشتری الکوزی نوشیدنی باب', NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, 0, NULL, NULL, '2026-07-14 15:25:08', '2026-07-14 15:25:08'),
(99, 4, 0, 'سهراب - تهیه کننده سبزیجات', NULL, NULL, NULL, 0, NULL, NULL, 1, 60000, 1, NULL, NULL, '2026-07-14 17:07:23', '2026-07-14 17:07:23'),
(100, 2, 0, 'کارمند خان علی', NULL, NULL, NULL, 0, NULL, 5000, 1, NULL, 0, 19, '2026-07-14', '2026-07-15 04:27:44', '2026-07-15 05:06:49'),
(102, 2, 0, 'Abdul Latif Erfan22', '0729010123', 'kabul', NULL, 0, NULL, 555, 1, NULL, 1, 20, '2026-07-20', '2026-07-15 05:50:52', '2026-07-15 05:51:27');

-- --------------------------------------------------------

--
-- Table structure for table `account_types`
--

CREATE TABLE `account_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_disabled` int(2) NOT NULL COMMENT '0:not disabled, 1:disabled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account_types`
--

INSERT INTO `account_types` (`id`, `name`, `is_disabled`, `created_at`, `updated_at`) VALUES
(1, 'خزانه شرکت', 1, NULL, NULL),
(2, 'کارمندان / موتروانان', 0, NULL, NULL),
(3, 'مشتریان', 0, NULL, NULL),
(4, ' تهیه کننده گان', 0, NULL, NULL),
(5, 'سهم داران', 1, NULL, NULL),
(6, 'صرافی و بانک', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `backups`
--

CREATE TABLE `backups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `times` int(11) NOT NULL,
  `dates` varchar(100) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `backups`
--

INSERT INTO `backups` (`id`, `label`, `file_name`, `file_path`, `times`, `dates`, `created_by`, `created_at`, `updated_at`) VALUES
(57, 'clean backup with demo orders', 'db-2026-07-13_10-40-53.sql', '/storage/backups/db-2026-07-13_10-40-53.sql', 1783923057, '1405-04-22 10:40:56', 'ادمین عمومی', '2026-07-13 06:10:57', '2026-07-13 06:10:57');

-- --------------------------------------------------------

--
-- Table structure for table `bought_items`
--

CREATE TABLE `bought_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `billno` int(11) DEFAULT NULL,
  `factor` varchar(100) DEFAULT NULL,
  `journal_code` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `total` decimal(15,2) NOT NULL COMMENT 'اگر مالیات فعال است جمع مالیات و اگر نیست نیز جمع شود',
  `cur_pay` decimal(15,2) DEFAULT NULL,
  `remained` decimal(15,2) DEFAULT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_account_id` bigint(20) NOT NULL,
  `currency_id` bigint(20) UNSIGNED NOT NULL,
  `tax_activation` tinyint(2) DEFAULT 0 COMMENT '0:not, 1: yes',
  `note` varchar(255) DEFAULT NULL,
  `idate` varchar(255) DEFAULT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `times` varchar(255) DEFAULT NULL,
  `has_invoice` tinyint(1) NOT NULL DEFAULT 0,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'driver_id',
  `user_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bought_items`
--

INSERT INTO `bought_items` (`id`, `billno`, `factor`, `journal_code`, `category_id`, `total`, `cur_pay`, `remained`, `account_id`, `supplier_account_id`, `currency_id`, `tax_activation`, `note`, `idate`, `year`, `month`, `day`, `times`, `has_invoice`, `invoice_id`, `user_id`, `user_name`, `created_at`, `updated_at`) VALUES
(4, 1, '0', 1, NULL, 2750.00, 0.00, 2750.00, 33, 86, 1, 1, '', '2026-07-13', 2026, 7, 13, '1783954870', 0, NULL, 13, 'ادمین عمومی', '2026-07-13 15:01:59', '2026-07-13 15:02:04');

-- --------------------------------------------------------

--
-- Table structure for table `bought_item_details`
--

CREATE TABLE `bought_item_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `billno` int(11) DEFAULT NULL,
  `bought_item_id` bigint(20) UNSIGNED NOT NULL,
  `pre_list_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `supplier_account_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `buy_up` double NOT NULL,
  `buy_tax_per` tinyint(4) DEFAULT NULL,
  `buy_tax_price` double DEFAULT NULL,
  `buy_up_vat` double DEFAULT NULL,
  `total` double DEFAULT NULL COMMENT 'جمع بدون مالیات',
  `total_vat` double DEFAULT NULL COMMENT 'جمع با مالیات ',
  `sell_up` float DEFAULT NULL,
  `sell_tax_per` tinyint(4) DEFAULT NULL,
  `sell_tax_price` double DEFAULT NULL,
  `sell_up_vat` float DEFAULT NULL,
  `is_moved` int(11) NOT NULL,
  `times` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(200) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bought_item_details`
--

INSERT INTO `bought_item_details` (`id`, `billno`, `bought_item_id`, `pre_list_id`, `category_id`, `supplier_account_id`, `amount`, `unit_id`, `buy_up`, `buy_tax_per`, `buy_tax_price`, `buy_up_vat`, `total`, `total_vat`, `sell_up`, `sell_tax_per`, `sell_tax_price`, `sell_up_vat`, `is_moved`, `times`, `user_id`, `user_name`, `created_at`, `updated_at`) VALUES
(4, 1, 4, 88, NULL, 86, 5, 6, 500, 2, 50, 550, 2500, 2750, 550, 2, 55, 605, 1, '1783954870', 13, 'ادمین عمومی', '2026-07-13 15:01:59', '2026-07-13 15:01:59');

-- --------------------------------------------------------

--
-- Table structure for table `bought_item_pre_lists`
--

CREATE TABLE `bought_item_pre_lists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL COMMENT 'account_id',
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bought_item_pre_lists`
--

INSERT INTO `bought_item_pre_lists` (`id`, `category_id`, `supplier_id`, `name`, `created_at`, `updated_at`) VALUES
(85, 1, NULL, 'کیله', '2026-06-22 07:19:10', '2026-06-22 07:19:10'),
(86, 1, NULL, 'خربوزه', '2026-06-22 07:19:19', '2026-06-22 07:19:19'),
(87, 3, 96, 'نخود پلنگی', '2026-06-22 07:19:24', '2026-07-14 17:11:07'),
(88, 3, NULL, 'آرد گندم', '2026-06-22 07:19:34', '2026-06-22 07:19:34'),
(89, 3, 86, 'برنج', '2026-06-22 07:19:44', '2026-07-14 17:10:09'),
(90, 4, 95, 'نوشابه الکوزی خورد', '2026-06-22 07:19:58', '2026-07-14 17:10:00'),
(91, 4, 95, 'نوشابه کوکاکولا کلان', '2026-06-22 07:20:08', '2026-07-14 17:09:53'),
(92, 4, 95, 'جنسینگ', '2026-06-22 07:20:20', '2026-07-14 17:08:17'),
(93, 2, 99, 'کاهو', '2026-06-22 07:20:34', '2026-07-14 17:07:35'),
(94, 2, 91, 'گشنیز', '2026-06-22 07:20:38', '2026-07-14 17:06:19'),
(95, 2, 91, 'ملی سرخک', '2026-06-22 07:20:46', '2026-07-14 17:06:08');

-- --------------------------------------------------------

--
-- Table structure for table `bought_returns`
--

CREATE TABLE `bought_returns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bought_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bought_item_detail_id` bigint(20) UNSIGNED DEFAULT NULL,
  `billno` varchar(255) DEFAULT NULL,
  `return_number` varchar(255) NOT NULL,
  `return_date` date NOT NULL,
  `supplier_account_id` bigint(20) UNSIGNED NOT NULL,
  `pre_list_id` bigint(20) UNSIGNED NOT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` double NOT NULL,
  `unit_price` decimal(15,2) NOT NULL COMMENT 'tax or without tax',
  `total` decimal(15,2) NOT NULL COMMENT 'tax or without tax',
  `tax_percentage` decimal(5,2) DEFAULT NULL,
  `tax_amount` decimal(15,2) DEFAULT NULL,
  `currency_id` bigint(20) UNSIGNED NOT NULL,
  `reason` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `responsible` varchar(255) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `email` varchar(30) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `is_disabled` int(2) DEFAULT 0 COMMENT '0: not,  1:disabled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `responsible`, `phone`, `email`, `address`, `is_disabled`, `created_at`, `updated_at`) VALUES
(46, 'شعبه مرکزی', '', '', NULL, '', 1, '2025-02-04 10:07:34', '2025-02-04 10:07:34'),
(56, 'شعبه جاغوری', 'محمود', '0708088185', 'erfan@gmail.com', 'مرکز جاغوری', 0, '2025-03-09 12:13:29', '2025-03-11 05:10:27'),
(63, 'شعبه هرات', 'احمدی', '0708088185', NULL, 'جبریل - هرات', 0, '2025-03-11 05:10:02', '2025-03-11 05:10:02');

-- --------------------------------------------------------

--
-- Table structure for table `buy_invoices`
--

CREATE TABLE `buy_invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `supplier_id` int(10) UNSIGNED NOT NULL,
  `total` decimal(15,2) DEFAULT 0.00,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `remaining` decimal(15,2) DEFAULT 0.00,
  `currency_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tax_activation` int(2) DEFAULT 0 COMMENT '0:not, 1:yes',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0:draft, 1:pending, 2:partial, 3:paid, 4:cancelled',
  `invoice_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `times` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `buy_invoice_items`
--

CREATE TABLE `buy_invoice_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `bought_item_detail_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bought_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pre_list_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unit_id` int(11) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unit_price_vat` decimal(15,2) DEFAULT 0.00,
  `tax_percentage` int(11) NOT NULL,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `buy_up_vat` decimal(15,2) DEFAULT NULL COMMENT 'Buy Unit Price Value Added Tax',
  `total` decimal(15,2) DEFAULT 0.00,
  `total_vat` decimal(15,2) DEFAULT 0.00,
  `times` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `buy_invoice_payments`
--

CREATE TABLE `buy_invoice_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1:cash, 2:bank, 3:loan',
  `account_id` int(10) UNSIGNED DEFAULT NULL,
  `supplier_account_id` int(11) NOT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `times` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `name`, `created_at`, `updated_at`) VALUES
(19, 'موتر 1150', '2026-07-15 02:14:49', '2026-07-15 02:14:49'),
(20, 'موتر 1120', '2026-07-15 02:15:08', '2026-07-15 02:15:08'),
(21, 'موتر 1290', '2026-07-15 02:15:24', '2026-07-15 02:15:24'),
(22, 'موتر 1560', '2026-07-15 02:15:47', '2026-07-15 02:15:47');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'میوه جات', '2026-06-19 07:02:20', '2026-06-19 08:38:28'),
(2, 'سبزی جات', '2026-06-19 07:02:20', '2026-06-19 08:38:18'),
(3, 'خوراکه باب', '2026-06-19 08:38:38', '2026-06-19 08:38:38'),
(4, 'نوشیدنی ها', '2026-06-19 08:38:48', '2026-06-19 08:38:48');

-- --------------------------------------------------------

--
-- Table structure for table `clearances`
--

CREATE TABLE `clearances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('buy','sell') NOT NULL DEFAULT 'buy',
  `company_account_id` bigint(20) UNSIGNED NOT NULL,
  `customer_account_id` bigint(20) UNSIGNED NOT NULL,
  `total` double NOT NULL,
  `currency_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` int(11) NOT NULL,
  `details` varchar(255) NOT NULL,
  `bill_numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`bill_numbers`)),
  `dates` varchar(255) NOT NULL,
  `clearedBy` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `symbols` varchar(255) NOT NULL,
  `is_base` enum('yes','no') NOT NULL DEFAULT 'no',
  `color` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `name`, `symbols`, `is_base`, `color`, `created_at`, `updated_at`) VALUES
(1, 'افغانی', 'AFN', 'yes', '#4307e9', NULL, '2025-03-08 02:23:08');

-- --------------------------------------------------------

--
-- Table structure for table `expense_types`
--

CREATE TABLE `expense_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expense_types`
--

INSERT INTO `expense_types` (`id`, `name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'کرایه دوکان', NULL, '2025-02-24 08:53:26', '2025-02-24 08:53:26'),
(2, 'مصارفات دفتر', NULL, '2025-02-24 08:53:36', '2025-03-07 06:48:32'),
(3, 'بل برق', NULL, '2025-02-24 08:53:47', '2025-02-24 08:53:47'),
(6, 'مصارف روزانه جاغوری', 11, '2025-03-10 01:56:22', '2025-03-10 01:56:22');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `income_types`
--

CREATE TABLE `income_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

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
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journals`
--

CREATE TABLE `journals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` int(11) NOT NULL DEFAULT 0,
  `account_type_id` bigint(20) NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `bill_no` int(11) DEFAULT 0,
  `amount` double NOT NULL,
  `currency_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_type` int(11) NOT NULL COMMENT '1:recieved 2:paid',
  `payment_type` int(11) NOT NULL COMMENT '1: cache, 2: loan',
  `options` int(11) DEFAULT NULL COMMENT '1: cache2cache, 2:loan2loan, 3:cache2loan, 4:loan2cache',
  `option_label` varchar(100) DEFAULT NULL,
  `idate` varchar(30) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `updated_full_date` varchar(30) DEFAULT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `doc` varchar(255) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL COMMENT '1: old journal, 2: journal, 3:income, 4:expense, 5:salary, 6:participants, 7:buy, 8:sales, 9:buy invoice,  10:sales invoice, 11:return, 12:other',
  `category_id` int(11) DEFAULT NULL,
  `dynamic_type` int(11) DEFAULT NULL COMMENT 'has relation with income_type, expense_type, salary, Invoice id, ....',
  `dt_comment` varchar(255) DEFAULT NULL,
  `profit` double DEFAULT NULL,
  `is_cleared` int(11) DEFAULT 0 COMMENT '0: not cleared, 1:cleared',
  `cleared_round` int(11) DEFAULT 0,
  `times` varchar(255) NOT NULL DEFAULT '0',
  `is_single_record` int(11) NOT NULL DEFAULT 0 COMMENT '0:single, 1:pair',
  `belongsToMe` int(11) DEFAULT 0 COMMENT '0: object 1:subject (my record)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journals`
--

INSERT INTO `journals` (`id`, `code`, `account_type_id`, `account_id`, `bill_no`, `amount`, `currency_id`, `transaction_type`, `payment_type`, `options`, `option_label`, `idate`, `user_id`, `user_name`, `updated_full_date`, `year`, `month`, `day`, `doc`, `details`, `status`, `category_id`, `dynamic_type`, `dt_comment`, `profit`, `is_cleared`, `cleared_round`, `times`, `is_single_record`, `belongsToMe`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 33, 1, 2750, 1, 1, 2, NULL, 'قرضه خرید', '2026-07-13', 13, 'ادمین عمومی', NULL, 2026, 7, 13, NULL, 'قرضه خرید - بل BUY_1', 7, NULL, 2, 'clearable', NULL, 0, 0, '1783954870', 1, 0, '2026-07-13 15:02:04', '2026-07-13 15:02:04'),
(2, 1, 4, 86, 1, 2750, 1, 2, 2, NULL, 'طلب خرید ', '2026-07-13', 13, 'ادمین عمومی', NULL, 2026, 7, 13, NULL, 'طلب خرید - بل BUY_1', 7, NULL, 2, 'clearable', NULL, 0, 0, '1783954870', 1, 0, '2026-07-13 15:02:04', '2026-07-13 15:02:04');

-- --------------------------------------------------------

--
-- Table structure for table `last_logins`
--

CREATE TABLE `last_logins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `userId` bigint(20) NOT NULL,
  `sessionData` varchar(255) NOT NULL,
  `machineIp` varchar(255) NOT NULL,
  `userAgent` varchar(255) NOT NULL,
  `agentString` varchar(255) NOT NULL,
  `platform` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ltm_translations`
--

CREATE TABLE `ltm_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `locale` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `key` text NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `ltm_translations`
--

INSERT INTO `ltm_translations` (`id`, `status`, `locale`, `group`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 1, 'en', 'auth', 'failed', 'These credentials do not match our records.', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(2, 1, 'en', 'auth', 'password', 'The provided password is incorrect.', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(3, 1, 'en', 'auth', 'throttle', 'Too many login attempts. Please try again in :seconds seconds.', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(4, 1, 'en', 'common', 'add', 'Add', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(5, 1, 'en', 'common', 'edit', 'Edit', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(6, 1, 'en', 'common', 'delete', 'Delete', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(7, 1, 'en', 'common', 'close', 'Close', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(8, 1, 'en', 'common', 'save', 'Save', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(9, 1, 'en', 'common', 'number', 'Number', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(10, 1, 'en', 'common', 'name', 'Name', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(11, 1, 'en', 'common', 'loading', 'Loading...', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(12, 1, 'en', 'common', 'added_successfully', 'Added successfully', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(13, 1, 'en', 'common', 'add_failed', 'Add failed', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(14, 1, 'en', 'common', 'updated_successfully', 'Updated successfully', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(15, 1, 'en', 'common', 'update_failed', 'Update failed', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(16, 1, 'en', 'common', 'deleted_successfully', 'Deleted successfully', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(17, 1, 'en', 'common', 'delete_failed', 'Delete failed', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(18, 1, 'en', 'common', 'delete_confirm', 'Do you want to delete?', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(19, 1, 'en', 'common', 'no_data_found', 'No data found', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(20, 1, 'en', 'common', 'modal_title', '', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(21, 1, 'en', 'common', 'a', '', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(22, 1, 'en', 'common', 'title', 'تنظیمات', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(23, 1, 'en', 'dashboard', 'todays_tab', 'Today\'s Transactions', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(24, 1, 'en', 'dashboard', 'sales', 'Sales', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(25, 1, 'en', 'dashboard', 'sales_income', 'Sales Income', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(26, 1, 'en', 'dashboard', 'sales_talabat', 'Sales Receivable', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(27, 1, 'en', 'dashboard', 'sales_profit', 'Sales Profit', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(28, 1, 'en', 'dashboard', 'buy', 'Purchases', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(29, 1, 'en', 'dashboard', 'buy_paid', 'Purchase Payments', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(30, 1, 'en', 'dashboard', 'buy_loan', 'Purchase Payables', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(31, 1, 'en', 'dashboard', 'trans_expense', 'Transport Expenses', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(32, 1, 'en', 'dashboard', 'income', 'Income', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(33, 1, 'en', 'dashboard', 'expense', 'Expenses', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(34, 1, 'en', 'dashboard', 'khazana_income', 'Cash Treasury Inflow', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(35, 1, 'en', 'dashboard', 'khazana_outcome', 'Cash Treasury Outflow', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(36, 1, 'en', 'dashboard', 'search_currecny', 'Select Currency', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(37, 1, 'en', 'dashboard', 'search_year', 'Select Year', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(38, 1, 'en', 'dashboard', 'search_month', 'Select Month', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(39, 1, 'en', 'dashboard', 'search_day', 'Select Day', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(40, 1, 'en', 'dashboard', 'search_all', 'All', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(41, 1, 'en', 'dashboard', 'important_trans_tab', 'Key Business Transactions', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(42, 1, 'en', 'dashboard', 'sales_profit_plus_incom', 'Sales Profit + Income', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(43, 1, 'en', 'dashboard', 'net_profit', 'Net Profit', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(44, 1, 'en', 'dashboard', 'warehouse_value', 'Warehouse Stock Value', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(45, 1, 'en', 'dashboard', 'company_cache', 'Company Cash', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(46, 1, 'en', 'dashboard', 'company_capital', 'Company Capital', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(47, 1, 'en', 'dashboard', 'loans', 'Receivables', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(48, 1, 'en', 'dashboard', 'talabat', 'Payables', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(49, 1, 'en', 'dashboard', 'belance', 'Receivables & Payables Balance', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(50, 1, 'en', 'dashboard', 'company_loan', 'Company is in debt', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(51, 1, 'en', 'dashboard', 'company_talab', 'Company is owed money', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(52, 1, 'en', 'dashboard', 'company_clearance', 'Company is settled', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(53, 1, 'en', 'dashboard', 'khazana', 'Treasury', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(54, 1, 'en', 'dashboard', 'cache_in', 'Cash In', '2025-06-13 08:03:11', '2025-06-13 08:03:11'),
(55, 1, 'en', 'dashboard', 'cache_out', 'Cash Out', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(56, 1, 'en', 'dashboard', 'cache_balance', 'Balance', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(57, 1, 'en', 'dashboard', 'branch', 'Branches', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(58, 1, 'en', 'dashboard', 'open_this_branch', 'Enter This Branch', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(59, 1, 'en', 'dashboard', 'login_to_branch', 'Do you want to log in to this branch?', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(60, 1, 'en', 'dashboard', 'branch_login_error', 'Error switching branch!', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(61, 1, 'en', 'menu', 'dashboard', 'Dashboard', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(62, 1, 'en', 'menu', 'settings', 'Settings', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(63, 1, 'en', 'menu', 'rate', 'Rate', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(64, 1, 'en', 'menu', 'transaction', 'Transactions', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(65, 1, 'en', 'menu', 'rooznamcha', 'Rooznamcha', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(66, 1, 'en', 'menu', 'income', 'Income', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(67, 1, 'en', 'menu', 'expense', 'Expense', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(68, 1, 'en', 'menu', 'hr', 'HR', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(69, 1, 'en', 'menu', 'employee_lists', 'Employee Lists', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(70, 1, 'en', 'menu', 'salary', 'Salary', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(71, 1, 'en', 'menu', 'report', 'Report', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(72, 1, 'en', 'menu', 'buy', 'buy', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(73, 1, 'en', 'menu', 'buy_pre_list', 'Buy Pre List', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(74, 1, 'en', 'menu', 'new_buy', 'New Buy', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(75, 1, 'en', 'menu', 'bought_list', 'Bought_list', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(76, 1, 'en', 'menu', 'warehouse', 'Warehouse', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(77, 1, 'en', 'menu', 'warehouse_add', 'Add Prev Items', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(78, 1, 'en', 'menu', 'wastage', 'Wastage', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(79, 1, 'en', 'menu', 'sales', 'Sales', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(80, 1, 'en', 'menu', 'new_sales', 'New Sales', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(81, 1, 'en', 'menu', 'pos_sales', 'POS Sales', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(82, 1, 'en', 'menu', 'sold_list', 'Sold Lits', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(83, 1, 'en', 'menu', 'clearance', 'Clearance', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(84, 1, 'en', 'menu', 'bought_clearance', '‌‌Buy Clearance', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(85, 1, 'en', 'menu', 'sold_clearance', 'Sold Clearance', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(86, 1, 'en', 'menu', 'reports', 'Reports', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(87, 1, 'en', 'menu', 'user_management', 'User Management', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(88, 1, 'en', 'menu', 'role', 'Roles', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(89, 1, 'en', 'menu', 'users', 'Users', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(90, 1, 'en', 'menu', 'backup', 'Backup', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(91, 1, 'en', 'menu', 'cacheflow', 'Cache Flow', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(92, 1, 'en', 'menu', 'chartOfAccount', 'Chart of account', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(93, 1, 'en', 'pagination', 'previous', '&laquo; Previous', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(94, 1, 'en', 'pagination', 'next', 'Next &raquo;', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(95, 1, 'en', 'passwords', 'reset', 'Your password has been reset.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(96, 1, 'en', 'passwords', 'sent', 'We have emailed your password reset link.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(97, 1, 'en', 'passwords', 'throttled', 'Please wait before retrying.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(98, 1, 'en', 'passwords', 'token', 'This password reset token is invalid.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(99, 1, 'en', 'passwords', 'user', 'We can\'t find a user with that email address.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(100, 1, 'en', 'settings', 'branch', 'Branch', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(101, 1, 'en', 'settings', 'warehouse', 'Warehouse', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(102, 1, 'en', 'settings', 'unit', 'Unit', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(103, 1, 'en', 'settings', 'currency', 'Currency', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(104, 1, 'en', 'settings', 'account', 'Account', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(105, 1, 'en', 'settings', 'income_type', 'Income Type', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(106, 1, 'en', 'settings', 'expense_type', 'Expense Type', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(107, 1, 'en', 'settings', 'company_profile', 'Company Profile', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(108, 1, 'en', 'settings', 'branch_name', 'Branch Name', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(109, 1, 'en', 'settings', 'branch_resp', 'Responsible Person', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(110, 1, 'en', 'settings', 'branch_phone', 'Phone Number', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(111, 1, 'en', 'settings', 'branch_email', 'Email Address', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(112, 1, 'en', 'settings', 'branch_address', 'Address', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(113, 1, 'en', 'settings', 'warehouse_name', 'Warehouse Name', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(114, 1, 'en', 'settings', 'enter_warehouse_name', 'Enter warehouse name', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(115, 1, 'en', 'settings', 'related_branch', 'Related Branch', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(116, 1, 'en', 'settings', 'select_branch', 'Select Branch', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(117, 1, 'en', 'settings', 'responsible_person', 'Warehouse Manager', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(118, 1, 'en', 'settings', 'enter_responsible_name', 'Enter manager name', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(119, 1, 'en', 'settings', 'address', 'Address', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(120, 1, 'en', 'settings', 'enter_address', 'Enter address', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(121, 1, 'en', 'settings', 'select_branch_option', '--- Select Branch ---', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(122, 1, 'en', 'settings', 'a', '', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(123, 1, 'en', 'validation', 'accepted', 'The :attribute field must be accepted.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(124, 1, 'en', 'validation', 'accepted_if', 'The :attribute field must be accepted when :other is :value.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(125, 1, 'en', 'validation', 'active_url', 'The :attribute field must be a valid URL.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(126, 1, 'en', 'validation', 'after', 'The :attribute field must be a date after :date.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(127, 1, 'en', 'validation', 'after_or_equal', 'The :attribute field must be a date after or equal to :date.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(128, 1, 'en', 'validation', 'alpha', 'The :attribute field must only contain letters.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(129, 1, 'en', 'validation', 'alpha_dash', 'The :attribute field must only contain letters, numbers, dashes, and underscores.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(130, 1, 'en', 'validation', 'alpha_num', 'The :attribute field must only contain letters and numbers.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(131, 1, 'en', 'validation', 'array', 'The :attribute field must be an array.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(132, 1, 'en', 'validation', 'ascii', 'The :attribute field must only contain single-byte alphanumeric characters and symbols.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(133, 1, 'en', 'validation', 'before', 'The :attribute field must be a date before :date.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(134, 1, 'en', 'validation', 'before_or_equal', 'The :attribute field must be a date before or equal to :date.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(135, 1, 'en', 'validation', 'between.array', 'The :attribute field must have between :min and :max items.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(136, 1, 'en', 'validation', 'between.file', 'The :attribute field must be between :min and :max kilobytes.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(137, 1, 'en', 'validation', 'between.numeric', 'The :attribute field must be between :min and :max.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(138, 1, 'en', 'validation', 'between.string', 'The :attribute field must be between :min and :max characters.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(139, 1, 'en', 'validation', 'boolean', 'The :attribute field must be true or false.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(140, 1, 'en', 'validation', 'can', 'The :attribute field contains an unauthorized value.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(141, 1, 'en', 'validation', 'confirmed', 'The :attribute field confirmation does not match.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(142, 1, 'en', 'validation', 'contains', 'The :attribute field is missing a required value.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(143, 1, 'en', 'validation', 'current_password', 'The password is incorrect.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(144, 1, 'en', 'validation', 'date', 'The :attribute field must be a valid date.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(145, 1, 'en', 'validation', 'date_equals', 'The :attribute field must be a date equal to :date.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(146, 1, 'en', 'validation', 'date_format', 'The :attribute field must match the format :format.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(147, 1, 'en', 'validation', 'decimal', 'The :attribute field must have :decimal decimal places.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(148, 1, 'en', 'validation', 'declined', 'The :attribute field must be declined.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(149, 1, 'en', 'validation', 'declined_if', 'The :attribute field must be declined when :other is :value.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(150, 1, 'en', 'validation', 'different', 'The :attribute field and :other must be different.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(151, 1, 'en', 'validation', 'digits', 'The :attribute field must be :digits digits.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(152, 1, 'en', 'validation', 'digits_between', 'The :attribute field must be between :min and :max digits.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(153, 1, 'en', 'validation', 'dimensions', 'The :attribute field has invalid image dimensions.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(154, 1, 'en', 'validation', 'distinct', 'The :attribute field has a duplicate value.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(155, 1, 'en', 'validation', 'doesnt_end_with', 'The :attribute field must not end with one of the following: :values.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(156, 1, 'en', 'validation', 'doesnt_start_with', 'The :attribute field must not start with one of the following: :values.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(157, 1, 'en', 'validation', 'email', 'The :attribute field must be a valid email address.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(158, 1, 'en', 'validation', 'ends_with', 'The :attribute field must end with one of the following: :values.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(159, 1, 'en', 'validation', 'enum', 'The selected :attribute is invalid.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(160, 1, 'en', 'validation', 'exists', 'The selected :attribute is invalid.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(161, 1, 'en', 'validation', 'extensions', 'The :attribute field must have one of the following extensions: :values.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(162, 1, 'en', 'validation', 'file', 'The :attribute field must be a file.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(163, 1, 'en', 'validation', 'filled', 'The :attribute field must have a value.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(164, 1, 'en', 'validation', 'gt.array', 'The :attribute field must have more than :value items.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(165, 1, 'en', 'validation', 'gt.file', 'The :attribute field must be greater than :value kilobytes.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(166, 1, 'en', 'validation', 'gt.numeric', 'The :attribute field must be greater than :value.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(167, 1, 'en', 'validation', 'gt.string', 'The :attribute field must be greater than :value characters.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(168, 1, 'en', 'validation', 'gte.array', 'The :attribute field must have :value items or more.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(169, 1, 'en', 'validation', 'gte.file', 'The :attribute field must be greater than or equal to :value kilobytes.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(170, 1, 'en', 'validation', 'gte.numeric', 'The :attribute field must be greater than or equal to :value.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(171, 1, 'en', 'validation', 'gte.string', 'The :attribute field must be greater than or equal to :value characters.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(172, 1, 'en', 'validation', 'hex_color', 'The :attribute field must be a valid hexadecimal color.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(173, 1, 'en', 'validation', 'image', 'The :attribute field must be an image.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(174, 1, 'en', 'validation', 'in', 'The selected :attribute is invalid.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(175, 1, 'en', 'validation', 'in_array', 'The :attribute field must exist in :other.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(176, 1, 'en', 'validation', 'integer', 'The :attribute field must be an integer.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(177, 1, 'en', 'validation', 'ip', 'The :attribute field must be a valid IP address.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(178, 1, 'en', 'validation', 'ipv4', 'The :attribute field must be a valid IPv4 address.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(179, 1, 'en', 'validation', 'ipv6', 'The :attribute field must be a valid IPv6 address.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(180, 1, 'en', 'validation', 'json', 'The :attribute field must be a valid JSON string.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(181, 1, 'en', 'validation', 'list', 'The :attribute field must be a list.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(182, 1, 'en', 'validation', 'lowercase', 'The :attribute field must be lowercase.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(183, 1, 'en', 'validation', 'lt.array', 'The :attribute field must have less than :value items.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(184, 1, 'en', 'validation', 'lt.file', 'The :attribute field must be less than :value kilobytes.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(185, 1, 'en', 'validation', 'lt.numeric', 'The :attribute field must be less than :value.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(186, 1, 'en', 'validation', 'lt.string', 'The :attribute field must be less than :value characters.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(187, 1, 'en', 'validation', 'lte.array', 'The :attribute field must not have more than :value items.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(188, 1, 'en', 'validation', 'lte.file', 'The :attribute field must be less than or equal to :value kilobytes.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(189, 1, 'en', 'validation', 'lte.numeric', 'The :attribute field must be less than or equal to :value.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(190, 1, 'en', 'validation', 'lte.string', 'The :attribute field must be less than or equal to :value characters.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(191, 1, 'en', 'validation', 'mac_address', 'The :attribute field must be a valid MAC address.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(192, 1, 'en', 'validation', 'max.array', 'The :attribute field must not have more than :max items.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(193, 1, 'en', 'validation', 'max.file', 'The :attribute field must not be greater than :max kilobytes.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(194, 1, 'en', 'validation', 'max.numeric', 'The :attribute field must not be greater than :max.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(195, 1, 'en', 'validation', 'max.string', 'The :attribute field must not be greater than :max characters.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(196, 1, 'en', 'validation', 'max_digits', 'The :attribute field must not have more than :max digits.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(197, 1, 'en', 'validation', 'mimes', 'The :attribute field must be a file of type: :values.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(198, 1, 'en', 'validation', 'mimetypes', 'The :attribute field must be a file of type: :values.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(199, 1, 'en', 'validation', 'min.array', 'The :attribute field must have at least :min items.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(200, 1, 'en', 'validation', 'min.file', 'The :attribute field must be at least :min kilobytes.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(201, 1, 'en', 'validation', 'min.numeric', 'The :attribute field must be at least :min.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(202, 1, 'en', 'validation', 'min.string', 'The :attribute field must be at least :min characters.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(203, 1, 'en', 'validation', 'min_digits', 'The :attribute field must have at least :min digits.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(204, 1, 'en', 'validation', 'missing', 'The :attribute field must be missing.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(205, 1, 'en', 'validation', 'missing_if', 'The :attribute field must be missing when :other is :value.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(206, 1, 'en', 'validation', 'missing_unless', 'The :attribute field must be missing unless :other is :value.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(207, 1, 'en', 'validation', 'missing_with', 'The :attribute field must be missing when :values is present.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(208, 1, 'en', 'validation', 'missing_with_all', 'The :attribute field must be missing when :values are present.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(209, 1, 'en', 'validation', 'multiple_of', 'The :attribute field must be a multiple of :value.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(210, 1, 'en', 'validation', 'not_in', 'The selected :attribute is invalid.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(211, 1, 'en', 'validation', 'not_regex', 'The :attribute field format is invalid.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(212, 1, 'en', 'validation', 'numeric', 'The :attribute field must be a number.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(213, 1, 'en', 'validation', 'password.letters', 'The :attribute field must contain at least one letter.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(214, 1, 'en', 'validation', 'password.mixed', 'The :attribute field must contain at least one uppercase and one lowercase letter.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(215, 1, 'en', 'validation', 'password.numbers', 'The :attribute field must contain at least one number.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(216, 1, 'en', 'validation', 'password.symbols', 'The :attribute field must contain at least one symbol.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(217, 1, 'en', 'validation', 'password.uncompromised', 'The given :attribute has appeared in a data leak. Please choose a different :attribute.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(218, 1, 'en', 'validation', 'present', 'The :attribute field must be present.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(219, 1, 'en', 'validation', 'present_if', 'The :attribute field must be present when :other is :value.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(220, 1, 'en', 'validation', 'present_unless', 'The :attribute field must be present unless :other is :value.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(221, 1, 'en', 'validation', 'present_with', 'The :attribute field must be present when :values is present.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(222, 1, 'en', 'validation', 'present_with_all', 'The :attribute field must be present when :values are present.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(223, 1, 'en', 'validation', 'prohibited', 'The :attribute field is prohibited.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(224, 1, 'en', 'validation', 'prohibited_if', 'The :attribute field is prohibited when :other is :value.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(225, 1, 'en', 'validation', 'prohibited_unless', 'The :attribute field is prohibited unless :other is in :values.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(226, 1, 'en', 'validation', 'prohibits', 'The :attribute field prohibits :other from being present.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(227, 1, 'en', 'validation', 'regex', 'The :attribute field format is invalid.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(228, 1, 'en', 'validation', 'required', 'The :attribute field is required.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(229, 1, 'en', 'validation', 'required_array_keys', 'The :attribute field must contain entries for: :values.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(230, 1, 'en', 'validation', 'required_if', 'The :attribute field is required when :other is :value.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(231, 1, 'en', 'validation', 'required_if_accepted', 'The :attribute field is required when :other is accepted.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(232, 1, 'en', 'validation', 'required_if_declined', 'The :attribute field is required when :other is declined.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(233, 1, 'en', 'validation', 'required_unless', 'The :attribute field is required unless :other is in :values.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(234, 1, 'en', 'validation', 'required_with', 'The :attribute field is required when :values is present.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(235, 1, 'en', 'validation', 'required_with_all', 'The :attribute field is required when :values are present.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(236, 1, 'en', 'validation', 'required_without', 'The :attribute field is required when :values is not present.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(237, 1, 'en', 'validation', 'required_without_all', 'The :attribute field is required when none of :values are present.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(238, 1, 'en', 'validation', 'same', 'The :attribute field must match :other.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(239, 1, 'en', 'validation', 'size.array', 'The :attribute field must contain :size items.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(240, 1, 'en', 'validation', 'size.file', 'The :attribute field must be :size kilobytes.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(241, 1, 'en', 'validation', 'size.numeric', 'The :attribute field must be :size.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(242, 1, 'en', 'validation', 'size.string', 'The :attribute field must be :size characters.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(243, 1, 'en', 'validation', 'starts_with', 'The :attribute field must start with one of the following: :values.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(244, 1, 'en', 'validation', 'string', 'The :attribute field must be a string.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(245, 1, 'en', 'validation', 'timezone', 'The :attribute field must be a valid timezone.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(246, 1, 'en', 'validation', 'unique', 'The :attribute has already been taken.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(247, 1, 'en', 'validation', 'uploaded', 'The :attribute failed to upload.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(248, 1, 'en', 'validation', 'uppercase', 'The :attribute field must be uppercase.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(249, 1, 'en', 'validation', 'url', 'The :attribute field must be a valid URL.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(250, 1, 'en', 'validation', 'ulid', 'The :attribute field must be a valid ULID.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(251, 1, 'en', 'validation', 'uuid', 'The :attribute field must be a valid UUID.', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(252, 1, 'en', 'validation', 'custom.attribute-name.rule-name', 'custom-message', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(253, 1, 'fa', 'common', 'add', 'ثبت جدید', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(254, 1, 'fa', 'common', 'edit', 'ویرایش', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(255, 1, 'fa', 'common', 'delete', 'حذف', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(256, 1, 'fa', 'common', 'close', 'بستن', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(257, 1, 'fa', 'common', 'save', 'ثبت', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(258, 1, 'fa', 'common', 'number', 'نمبر', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(259, 1, 'fa', 'common', 'name', 'نام', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(260, 1, 'fa', 'common', 'loading', 'درحال بارگزاری ... ', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(261, 1, 'fa', 'common', 'added_successfully', 'موفقانه علاوه گردید', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(262, 1, 'fa', 'common', 'add_failed', 'ثبت نگردید', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(263, 1, 'fa', 'common', 'updated_successfully', 'موفقانه ویرایش گردید', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(264, 1, 'fa', 'common', 'update_failed', 'ویرایش نگردید', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(265, 1, 'fa', 'common', 'deleted_successfully', 'موفقانه حذف گردید', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(266, 1, 'fa', 'common', 'delete_failed', 'حذف نگردید', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(267, 1, 'fa', 'common', 'delete_confirm', 'آیا میخواهید حذف نمایید ؟', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(268, 1, 'fa', 'common', 'no_data_found', 'اطلاعات یافت نشد', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(269, 1, 'fa', 'common', 'modal_title', '', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(270, 1, 'fa', 'common', 'a', '', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(271, 1, 'fa', 'common', 'title', 'تنظیمات', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(272, 1, 'fa', 'dashboard', 'todays_tab', 'معاملات امروز', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(273, 1, 'fa', 'dashboard', 'sales', 'فروشات', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(274, 1, 'fa', 'dashboard', 'sales_income', 'دریافت فروشات', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(275, 1, 'fa', 'dashboard', 'sales_talabat', 'طلب فروشات', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(276, 1, 'fa', 'dashboard', 'sales_profit', 'مفاد فروشات', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(277, 1, 'fa', 'dashboard', 'buy', 'خرید', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(278, 1, 'fa', 'dashboard', 'buy_paid', 'پرداخت خرید', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(279, 1, 'fa', 'dashboard', 'buy_loan', 'قرضه خرید', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(280, 1, 'fa', 'dashboard', 'trans_expense', 'مصارف ترانسپورت', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(281, 1, 'fa', 'dashboard', 'income', 'عواید', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(282, 1, 'fa', 'dashboard', 'expense', 'مصارف', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(283, 1, 'fa', 'dashboard', 'khazana_income', 'آمد نقد خزانه', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(284, 1, 'fa', 'dashboard', 'khazana_outcome', 'رفت نقد خزانه', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(285, 1, 'fa', 'dashboard', 'search_currecny', 'انتخاب پول', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(286, 1, 'fa', 'dashboard', 'search_year', 'انتخاب سال', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(287, 1, 'fa', 'dashboard', 'search_month', 'انتخاب ماه', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(288, 1, 'fa', 'dashboard', 'search_day', 'انتخاب روز', '2025-06-13 08:03:12', '2025-06-13 08:03:12'),
(289, 1, 'fa', 'dashboard', 'search_all', 'همه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(290, 1, 'fa', 'dashboard', 'important_trans_tab', 'معاملات مهم تجارت', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(291, 1, 'fa', 'dashboard', 'sales_profit_plus_incom', 'مفاد فروشات + عواید', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(292, 1, 'fa', 'dashboard', 'net_profit', 'مفاد خالص', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(293, 1, 'fa', 'dashboard', 'warehouse_value', 'موجودی گدام', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(294, 1, 'fa', 'dashboard', 'company_cache', 'پول نقد شرکت', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(295, 1, 'fa', 'dashboard', 'company_capital', 'سرمایه شرکت', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(296, 1, 'fa', 'dashboard', 'loans', 'طلبات', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(297, 1, 'fa', 'dashboard', 'talabat', 'قرضه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(298, 1, 'fa', 'dashboard', 'belance', 'بیلانس طلبات و قرضه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(299, 1, 'fa', 'dashboard', 'company_loan', 'شرکت باقی است', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(300, 1, 'fa', 'dashboard', 'company_talab', 'شرکت طلب است', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(301, 1, 'fa', 'dashboard', 'company_clearance', 'تصفیه است', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(302, 1, 'fa', 'dashboard', 'khazana', 'خزانه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(303, 1, 'fa', 'dashboard', 'cache_in', 'آمد نقد', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(304, 1, 'fa', 'dashboard', 'cache_out', 'رفت نقد', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(305, 1, 'fa', 'dashboard', 'cache_balance', 'بیلانس', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(306, 1, 'fa', 'dashboard', 'branch', 'شعبات', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(307, 1, 'fa', 'dashboard', 'open_this_branch', 'ورود به این شعبه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(308, 1, 'fa', 'dashboard', 'login_to_branch', 'آیا میخواهید به این شعبه وارید شوید ؟', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(309, 1, 'fa', 'dashboard', 'branch_login_error', 'خطا در تغییر شعبه!', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(310, 1, 'fa', 'menu', 'dashboard', 'صفحه اصلی', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(311, 1, 'fa', 'menu', 'settings', 'تنظیمات', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(312, 1, 'fa', 'menu', 'rate', 'نرخ روز', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(313, 1, 'fa', 'menu', 'transaction', 'معاملات', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(314, 1, 'fa', 'menu', 'rooznamcha', 'روزنامچه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(315, 1, 'fa', 'menu', 'income', 'عواید', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(316, 1, 'fa', 'menu', 'expense', 'مصارف', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(317, 1, 'fa', 'menu', 'hr', 'منابع بشری', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(318, 1, 'fa', 'menu', 'employee_lists', 'لیست کارمندان', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(319, 1, 'fa', 'menu', 'salary', 'معاشات', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(320, 1, 'fa', 'menu', 'report', 'گزارش معاشات', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(321, 1, 'fa', 'menu', 'buy', ' خرید عمومی', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(322, 1, 'fa', 'menu', 'buy_pre_list', 'لیست خرید برای ثبت', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(323, 1, 'fa', 'menu', 'new_buy', 'خرید جدید', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(324, 1, 'fa', 'menu', 'bought_list', 'لیست خریداری‌شده', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(325, 1, 'fa', 'menu', 'warehouse', 'گدام', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(326, 1, 'fa', 'menu', 'warehouse_add', 'ثبت موجودی گدام', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(327, 1, 'fa', 'menu', 'wastage', 'ضایعات', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(328, 1, 'fa', 'menu', 'sales', 'فروش', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(329, 1, 'fa', 'menu', 'new_sales', 'فروشات جدید', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(330, 1, 'fa', 'menu', 'pos_sales', 'فروش دستگاهی (POS)', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(331, 1, 'fa', 'menu', 'sold_list', 'لیست فروشات', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(332, 1, 'fa', 'menu', 'clearance', 'تصفیه حساب', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(333, 1, 'fa', 'menu', 'bought_clearance', 'تصفیه حساب خرید', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(334, 1, 'fa', 'menu', 'sold_clearance', 'تصفیه حساب فروشات', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(335, 1, 'fa', 'menu', 'reports', 'گزارشات', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(336, 1, 'fa', 'menu', 'user_management', 'مدیریت کاربران', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(337, 1, 'fa', 'menu', 'role', 'رول ها', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(338, 1, 'fa', 'menu', 'users', 'کاربران', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(339, 1, 'fa', 'menu', 'backup', 'پشتیبان‌گیری', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(340, 1, 'fa', 'menu', 'cacheflow', 'حسابات مشتریان', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(341, 1, 'fa', 'menu', 'chartOfAccount', 'چارت حسابات', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(342, 1, 'fa', 'settings', 'branch', 'شعبه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(343, 1, 'fa', 'settings', 'warehouse', 'گدام', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(344, 1, 'fa', 'settings', 'unit', 'واحد اجناس', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(345, 1, 'fa', 'settings', 'currency', 'واحد پولی', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(346, 1, 'fa', 'settings', 'account', 'حساب', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(347, 1, 'fa', 'settings', 'income_type', 'کتگوری عواید', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(348, 1, 'fa', 'settings', 'expense_type', 'کتگوری مصارف', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(349, 1, 'fa', 'settings', 'company_profile', 'پروفایل شرکت', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(350, 1, 'fa', 'settings', 'branch_name', 'نام شعبه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(351, 1, 'fa', 'settings', 'branch_resp', 'مسؤل شعبه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(352, 1, 'fa', 'settings', 'branch_phone', 'شماره تماس', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(353, 1, 'fa', 'settings', 'branch_email', 'ایمیل آدرس', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(354, 1, 'fa', 'settings', 'branch_address', 'آدرس دفتر', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(355, 1, 'fa', 'settings', 'warehouse_name', 'نام گدام', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(356, 1, 'fa', 'settings', 'enter_warehouse_name', 'نام گدام را وارد کنید', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(357, 1, 'fa', 'settings', 'related_branch', 'شعبه مربوطه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(358, 1, 'fa', 'settings', 'select_branch', 'انتخاب شعبه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(359, 1, 'fa', 'settings', 'responsible_person', 'مسول گدام', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(360, 1, 'fa', 'settings', 'enter_responsible_name', 'نام مسول را وارد کنید', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(361, 1, 'fa', 'settings', 'address', 'آدرس', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(362, 1, 'fa', 'settings', 'enter_address', 'آدرس را وارد کنید', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(363, 1, 'fa', 'settings', 'select_branch_option', '--- انتخاب شعبه ---', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(364, 1, 'fa', 'settings', 'a', '', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(365, 1, 'fa', 'settings', 'title', 'تنظیمات', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(366, 1, 'fa', 'settings', 'message', 'پیام ', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(367, 1, 'fa', 'template', 'todays_tab', 'معاملات امروز', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(368, 1, 'fa', 'template', 'a', '', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(369, 1, 'fa', 'template', 'title', 'تنظیمات', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(370, 1, 'pa', 'common', 'add', 'اضافه کړئ', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(371, 1, 'pa', 'common', 'edit', 'سمول', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(372, 1, 'pa', 'common', 'delete', 'ړنګول', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(373, 1, 'pa', 'common', 'close', 'تړل', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(374, 1, 'pa', 'common', 'save', 'ثبتول', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(375, 1, 'pa', 'common', 'number', 'شمېره', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(376, 1, 'pa', 'common', 'name', 'نوم', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(377, 1, 'pa', 'common', 'loading', 'بار وړل کېږي...', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(378, 1, 'pa', 'common', 'added_successfully', 'په بریالیتوب سره اضافه شو', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(379, 1, 'pa', 'common', 'add_failed', 'اضافه نشو', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(380, 1, 'pa', 'common', 'updated_successfully', 'په بریالیتوب سره سم شو', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(381, 1, 'pa', 'common', 'update_failed', 'سم نشو', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(382, 1, 'pa', 'common', 'deleted_successfully', 'په بریالیتوب سره ړنګ شو', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(383, 1, 'pa', 'common', 'delete_failed', 'ړنګ نشو', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(384, 1, 'pa', 'common', 'delete_confirm', 'آیا غواړئ چې ړنګ یې کړئ؟', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(385, 1, 'pa', 'common', 'no_data_found', 'معلومات ونه موندل شول', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(386, 1, 'pa', 'common', 'modal_title', '', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(387, 1, 'pa', 'common', 'a', '', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(388, 1, 'pa', 'common', 'title', 'تنظیمات', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(389, 1, 'pa', 'dashboard', 'todays_tab', 'د نن ورځ معاملی', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(390, 1, 'pa', 'dashboard', 'sales', 'پلور', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(391, 1, 'pa', 'dashboard', 'sales_income', 'د پلور عاید', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(392, 1, 'pa', 'dashboard', 'sales_talabat', 'د پلور طلب', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(393, 1, 'pa', 'dashboard', 'sales_profit', 'د پلور ګټه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(394, 1, 'pa', 'dashboard', 'buy', 'پیرود', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(395, 1, 'pa', 'dashboard', 'buy_paid', 'د پیرود پیسې ورکړل شوې', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(396, 1, 'pa', 'dashboard', 'buy_loan', 'د پیرود پور', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(397, 1, 'pa', 'dashboard', 'trans_expense', 'د ترانسپورت لګښتونه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(398, 1, 'pa', 'dashboard', 'income', 'عایدات', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(399, 1, 'pa', 'dashboard', 'expense', 'لګښتونه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(400, 1, 'pa', 'dashboard', 'khazana_income', 'د خزانی نغدي راتګ', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(401, 1, 'pa', 'dashboard', 'khazana_outcome', 'د خزانی نغدي وتل', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(402, 1, 'pa', 'dashboard', 'search_currecny', 'پیسې وټاکئ', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(403, 1, 'pa', 'dashboard', 'search_year', 'کال وټاکئ', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(404, 1, 'pa', 'dashboard', 'search_month', 'میاشت وټاکئ', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(405, 1, 'pa', 'dashboard', 'search_day', 'ورځ وټاکئ', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(406, 1, 'pa', 'dashboard', 'search_all', 'ټول', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(407, 1, 'pa', 'dashboard', 'important_trans_tab', 'د تجارت مهم معاملات', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(408, 1, 'pa', 'dashboard', 'sales_profit_plus_incom', 'د پلور ګټه + عاید', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(409, 1, 'pa', 'dashboard', 'net_profit', 'خالصه ګټه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(410, 1, 'pa', 'dashboard', 'warehouse_value', 'د ګدام ارزښت', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(411, 1, 'pa', 'dashboard', 'company_cache', 'د شرکت نغدې پیسې', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(412, 1, 'pa', 'dashboard', 'company_capital', 'د شرکت سرمایه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(413, 1, 'pa', 'dashboard', 'loans', 'طلبونه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(414, 1, 'pa', 'dashboard', 'talabat', 'پورونه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(415, 1, 'pa', 'dashboard', 'belance', 'د طلب او پور توازن', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(416, 1, 'pa', 'dashboard', 'company_loan', 'شرکت پور دار دی', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(417, 1, 'pa', 'dashboard', 'company_talab', 'شرکت طلبګار دی', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(418, 1, 'pa', 'dashboard', 'company_clearance', 'شرکت تصفیه شوی دی', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(419, 1, 'pa', 'dashboard', 'khazana', 'خزانه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(420, 1, 'pa', 'dashboard', 'cache_in', 'نغدي راتګ', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(421, 1, 'pa', 'dashboard', 'cache_out', 'نغدي وتل', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(422, 1, 'pa', 'dashboard', 'cache_balance', 'توازن', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(423, 1, 'pa', 'dashboard', 'branch', 'څانګې', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(424, 1, 'pa', 'dashboard', 'open_this_branch', 'دې څانګې ته داخل شئ', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(425, 1, 'pa', 'dashboard', 'login_to_branch', 'ایا غواړئ دې څانګې ته داخل شئ؟', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(426, 1, 'pa', 'dashboard', 'branch_login_error', 'د څانګې په بدلولو کې تېروتنه!', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(427, 1, 'pa', 'menu', 'dashboard', 'ډشبورډ', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(428, 1, 'pa', 'menu', 'settings', 'تنظیمات', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(429, 1, 'pa', 'menu', 'rate', 'نرخ', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(430, 1, 'pa', 'menu', 'transaction', 'راکړې ورکړې', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(431, 1, 'pa', 'menu', 'rooznamcha', 'روځنامچه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(432, 1, 'pa', 'menu', 'income', 'عواید', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(433, 1, 'pa', 'menu', 'expense', 'لګښتونه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(434, 1, 'pa', 'menu', 'hr', 'د بشري منابعو څانګه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(435, 1, 'pa', 'menu', 'employee_lists', 'د کارکوونکو لیست', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(436, 1, 'pa', 'menu', 'salary', 'معاش', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(437, 1, 'pa', 'menu', 'report', 'راپور', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(438, 1, 'pa', 'menu', 'buy', 'پیرودل', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(439, 1, 'pa', 'menu', 'buy_pre_list', 'د مخکېنیو پیرودنو لیست', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(440, 1, 'pa', 'menu', 'new_buy', 'نوی پیرود', '2025-06-13 08:03:13', '2025-06-13 08:03:13');
INSERT INTO `ltm_translations` (`id`, `status`, `locale`, `group`, `key`, `value`, `created_at`, `updated_at`) VALUES
(441, 1, 'pa', 'menu', 'bought_list', 'د پیرودل شویو لیست', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(442, 1, 'pa', 'menu', 'warehouse', 'ګودام', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(443, 1, 'pa', 'menu', 'warehouse_add', 'خپل شتون ثبت کړئ', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(444, 1, 'pa', 'menu', 'wastage', 'ضایعات', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(445, 1, 'pa', 'menu', 'sales', 'خرڅلاو', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(446, 1, 'pa', 'menu', 'new_sales', 'نوي خرڅلاو', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(447, 1, 'pa', 'menu', 'pos_sales', 'POS خرڅلاو', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(448, 1, 'pa', 'menu', 'sold_list', 'د خرڅلاو شویو لیست', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(449, 1, 'pa', 'menu', 'clearance', 'تصفيه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(450, 1, 'pa', 'menu', 'bought_clearance', 'د خرڅلاو تصفيه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(451, 1, 'pa', 'menu', 'sold_clearance', 'د پلورل شویو تصفيه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(452, 1, 'pa', 'menu', 'reports', 'راپورونه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(453, 1, 'pa', 'menu', 'user_management', 'د کاروونکو مدیریت', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(454, 1, 'pa', 'menu', 'role', 'دندې / رولونه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(455, 1, 'pa', 'menu', 'users', 'کاروونکي', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(456, 1, 'pa', 'menu', 'backup', 'شاتړ / بیک‌اپ', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(457, 1, 'pa', 'menu', 'cacheflow', 'د پیرودونکو حسابونه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(458, 1, 'pa', 'menu', 'chartOfAccount', 'د حسابونو چارټ', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(459, 1, 'pa', 'settings', 'branch', 'شعبه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(460, 1, 'pa', 'settings', 'warehouse', 'گدام', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(461, 1, 'pa', 'settings', 'unit', 'واحد اجناس', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(462, 1, 'pa', 'settings', 'currency', 'واحد پولی', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(463, 1, 'pa', 'settings', 'account', 'حساب', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(464, 1, 'pa', 'settings', 'income_type', 'کتگوری عواید', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(465, 1, 'pa', 'settings', 'expense_type', 'کتگوری مصارف', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(466, 1, 'pa', 'settings', 'company_profile', 'پروفایل شرکت', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(467, 1, 'pa', 'settings', 'branch_name', 'نام شعبه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(468, 1, 'pa', 'settings', 'branch_resp', 'مسؤل شعبه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(469, 1, 'pa', 'settings', 'branch_phone', 'شماره تماس', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(470, 1, 'pa', 'settings', 'branch_email', 'ایمیل آدرس', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(471, 1, 'pa', 'settings', 'branch_address', 'آدرس دفتر', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(472, 1, 'pa', 'settings', 'warehouse_name', 'د ګدام نوم', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(473, 1, 'pa', 'settings', 'enter_warehouse_name', 'د ګدام نوم ولیکئ', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(474, 1, 'pa', 'settings', 'related_branch', 'اړوند څانګه', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(475, 1, 'pa', 'settings', 'select_branch', 'څانګه وټاکئ', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(476, 1, 'pa', 'settings', 'responsible_person', 'د ګدام مسؤل', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(477, 1, 'pa', 'settings', 'enter_responsible_name', 'د مسؤل نوم ولیکئ', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(478, 1, 'pa', 'settings', 'address', 'پته', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(479, 1, 'pa', 'settings', 'enter_address', 'پته ولیکئ', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(480, 1, 'pa', 'settings', 'select_branch_option', '--- څانګه وټاکئ ---', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(481, 1, 'pa', 'settings', 'a', '', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(482, 1, 'pa', 'settings', 'title', 'تنظیمات', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(483, 1, 'pa', 'settings', 'message', 'پیام ', '2025-06-13 08:03:13', '2025-06-13 08:03:13'),
(688, 1, 'en', 'vendor/backup', 'exception_message', 'Exception message: :message', '2025-06-13 08:03:14', '2025-06-13 08:03:14'),
(689, 1, 'en', 'vendor/backup', 'exception_trace', 'Exception trace: :trace', '2025-06-13 08:03:14', '2025-06-13 08:03:14'),
(690, 1, 'en', 'vendor/backup', 'exception_message_title', 'Exception message', '2025-06-13 08:03:14', '2025-06-13 08:03:14'),
(691, 1, 'en', 'vendor/backup', 'exception_trace_title', 'Exception trace', '2025-06-13 08:03:14', '2025-06-13 08:03:14'),
(692, 1, 'en', 'vendor/backup', 'backup_failed_subject', 'Failed backup of :application_name', '2025-06-13 08:03:14', '2025-06-13 08:03:14'),
(693, 1, 'en', 'vendor/backup', 'backup_failed_body', 'Important: An error occurred while backing up :application_name', '2025-06-13 08:03:14', '2025-06-13 08:03:14'),
(694, 1, 'en', 'vendor/backup', 'backup_successful_subject', 'Successful new backup of :application_name', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(695, 1, 'en', 'vendor/backup', 'backup_successful_subject_title', 'Successful new backup!', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(696, 1, 'en', 'vendor/backup', 'backup_successful_body', 'Great news, a new backup of :application_name was successfully created on the disk named :disk_name.', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(697, 1, 'en', 'vendor/backup', 'cleanup_failed_subject', 'Cleaning up the backups of :application_name failed.', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(698, 1, 'en', 'vendor/backup', 'cleanup_failed_body', 'An error occurred while cleaning up the backups of :application_name', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(699, 1, 'en', 'vendor/backup', 'cleanup_successful_subject', 'Clean up of :application_name backups successful', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(700, 1, 'en', 'vendor/backup', 'cleanup_successful_subject_title', 'Clean up of backups successful!', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(701, 1, 'en', 'vendor/backup', 'cleanup_successful_body', 'The clean up of the :application_name backups on the disk named :disk_name was successful.', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(702, 1, 'en', 'vendor/backup', 'healthy_backup_found_subject', 'The backups for :application_name on disk :disk_name are healthy', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(703, 1, 'en', 'vendor/backup', 'healthy_backup_found_subject_title', 'The backups for :application_name are healthy', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(704, 1, 'en', 'vendor/backup', 'healthy_backup_found_body', 'The backups for :application_name are considered healthy. Good job!', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(705, 1, 'en', 'vendor/backup', 'unhealthy_backup_found_subject', 'Important: The backups for :application_name are unhealthy', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(706, 1, 'en', 'vendor/backup', 'unhealthy_backup_found_subject_title', 'Important: The backups for :application_name are unhealthy. :problem', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(707, 1, 'en', 'vendor/backup', 'unhealthy_backup_found_body', 'The backups for :application_name on disk :disk_name are unhealthy.', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(708, 1, 'en', 'vendor/backup', 'unhealthy_backup_found_not_reachable', 'The backup destination cannot be reached. :error', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(709, 1, 'en', 'vendor/backup', 'unhealthy_backup_found_empty', 'There are no backups of this application at all.', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(710, 1, 'en', 'vendor/backup', 'unhealthy_backup_found_old', 'The latest backup made on :date is considered too old.', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(711, 1, 'en', 'vendor/backup', 'unhealthy_backup_found_unknown', 'Sorry, an exact reason cannot be determined.', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(712, 1, 'en', 'vendor/backup', 'unhealthy_backup_found_full', 'The backups are using too much storage. Current usage is :disk_usage which is higher than the allowed limit of :disk_limit.', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(713, 1, 'en', 'vendor/backup', 'no_backups_info', 'No backups were made yet', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(714, 1, 'en', 'vendor/backup', 'application_name', 'Application name', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(715, 1, 'en', 'vendor/backup', 'backup_name', 'Backup name', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(716, 1, 'en', 'vendor/backup', 'disk', 'Disk', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(717, 1, 'en', 'vendor/backup', 'newest_backup_size', 'Newest backup size', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(718, 1, 'en', 'vendor/backup', 'number_of_backups', 'Number of backups', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(719, 1, 'en', 'vendor/backup', 'total_storage_used', 'Total storage used', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(720, 1, 'en', 'vendor/backup', 'newest_backup_date', 'Newest backup date', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(721, 1, 'en', 'vendor/backup', 'oldest_backup_date', 'Oldest backup date', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(756, 1, 'fa', 'vendor/backup', 'exception_message', 'پیغام خطا: :message', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(757, 1, 'fa', 'vendor/backup', 'exception_trace', 'جزییات خطا: :trace', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(758, 1, 'fa', 'vendor/backup', 'exception_message_title', 'پیغام خطا', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(759, 1, 'fa', 'vendor/backup', 'exception_trace_title', 'جزییات خطا', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(760, 1, 'fa', 'vendor/backup', 'backup_failed_subject', 'پشتیبان‌گیری :application_name با خطا مواجه شد.', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(761, 1, 'fa', 'vendor/backup', 'backup_failed_body', 'پیغام مهم: هنگام پشتیبان‌گیری از :application_name خطایی رخ داده است. ', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(762, 1, 'fa', 'vendor/backup', 'backup_successful_subject', 'نسخه پشتیبان جدید :application_name با موفقیت ساخته شد.', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(763, 1, 'fa', 'vendor/backup', 'backup_successful_subject_title', 'پشتیبان‌گیری موفق!', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(764, 1, 'fa', 'vendor/backup', 'backup_successful_body', 'خبر خوب، به تازگی نسخه پشتیبان :application_name روی دیسک :disk_name با موفقیت ساخته شد. ', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(765, 1, 'fa', 'vendor/backup', 'cleanup_failed_subject', 'پاک‌‌سازی نسخه پشتیبان :application_name انجام نشد.', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(766, 1, 'fa', 'vendor/backup', 'cleanup_failed_body', 'هنگام پاک‌سازی نسخه پشتیبان :application_name خطایی رخ داده است.', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(767, 1, 'fa', 'vendor/backup', 'cleanup_successful_subject', 'پاک‌سازی نسخه پشتیبان :application_name با موفقیت انجام شد.', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(768, 1, 'fa', 'vendor/backup', 'cleanup_successful_subject_title', 'پاک‌سازی نسخه پشتیبان!', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(769, 1, 'fa', 'vendor/backup', 'cleanup_successful_body', 'پاک‌سازی نسخه پشتیبان :application_name روی دیسک :disk_name با موفقیت انجام شد.', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(770, 1, 'fa', 'vendor/backup', 'healthy_backup_found_subject', 'نسخه پشتیبان :application_name روی دیسک :disk_name سالم بود.', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(771, 1, 'fa', 'vendor/backup', 'healthy_backup_found_subject_title', 'نسخه پشتیبان :application_name سالم بود.', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(772, 1, 'fa', 'vendor/backup', 'healthy_backup_found_body', 'نسخه پشتیبان :application_name به نظر سالم میاد. دمت گرم!', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(773, 1, 'fa', 'vendor/backup', 'unhealthy_backup_found_subject', 'خبر مهم: نسخه پشتیبان :application_name سالم نبود.', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(774, 1, 'fa', 'vendor/backup', 'unhealthy_backup_found_subject_title', 'خبر مهم: نسخه پشتیبان :application_name سالم نبود. :problem', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(775, 1, 'fa', 'vendor/backup', 'unhealthy_backup_found_body', 'نسخه پشتیبان :application_name روی دیسک :disk_name سالم نبود.', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(776, 1, 'fa', 'vendor/backup', 'unhealthy_backup_found_not_reachable', 'مقصد پشتیبان‌گیری در دسترس نبود. :error', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(777, 1, 'fa', 'vendor/backup', 'unhealthy_backup_found_empty', 'برای این برنامه هیچ نسخه پشتیبانی وجود ندارد.', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(778, 1, 'fa', 'vendor/backup', 'unhealthy_backup_found_old', 'آخرین نسخه پشتیبان برای تاریخ :date است، که به نظر خیلی قدیمی میاد. ', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(779, 1, 'fa', 'vendor/backup', 'unhealthy_backup_found_unknown', 'متاسفانه دلیل دقیقی قابل تعیین نیست.', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(780, 1, 'fa', 'vendor/backup', 'unhealthy_backup_found_full', 'نسخه‌های پشتیبان حجم زیادی اشغال کرده‌اند. میزان دیسک استفاده‌شده :disk_usage است که از میزان مجاز :disk_limit فراتر رفته است. ', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(781, 1, 'fa', 'vendor/backup', 'no_backups_info', 'هنوز نسخه پشتیبان تهیه نشده است', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(782, 1, 'fa', 'vendor/backup', 'application_name', 'نام نرم‌افزار', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(783, 1, 'fa', 'vendor/backup', 'backup_name', 'نام نسخه پشتیبان', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(784, 1, 'fa', 'vendor/backup', 'disk', 'دیسک', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(785, 1, 'fa', 'vendor/backup', 'newest_backup_size', 'اندازه جدیدترین نسخه پشتیبان', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(786, 1, 'fa', 'vendor/backup', 'number_of_backups', 'تعداد نسخه‌های پشتیبان', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(787, 1, 'fa', 'vendor/backup', 'total_storage_used', 'کل فضای ذخیره‌سازی استفاده‌شده', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(788, 1, 'fa', 'vendor/backup', 'newest_backup_date', 'تاریخ جدیدترین نسخه پشتیبان', '2025-06-13 08:03:15', '2025-06-13 08:03:15'),
(789, 1, 'fa', 'vendor/backup', 'oldest_backup_date', 'تاریخ قدیمی‌ترین نسخه پشتیبان', '2025-06-13 08:03:15', '2025-06-13 08:03:15');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(5, '2025_01_24_063831_create_account_types_table', 1),
(6, '2025_01_24_063844_create_backups_table', 1),
(7, '2025_01_24_063910_create_bought_items_table', 1),
(8, '2025_01_24_063930_create_bought_item_details_table', 1),
(9, '2025_01_24_063944_create_bought_item_pre_lists_table', 1),
(10, '2025_01_24_063955_create_branches_table', 1),
(11, '2025_01_24_064013_create_business_types_table', 1),
(12, '2025_01_24_064036_create_currencies_table', 1),
(14, '2025_01_24_064101_create_journals_table', 1),
(15, '2025_01_24_064117_create_org_bios_table', 1),
(16, '2025_01_24_064127_create_packages_table', 1),
(17, '2025_01_24_064137_create_rates_table', 1),
(18, '2025_01_24_064147_create_salaries_table', 1),
(19, '2025_01_24_064209_create_table_access_metrics_table', 1),
(20, '2025_01_24_064222_create_table_last_logins_table', 1),
(21, '2025_01_24_064244_create_table_reset_passwords_table', 1),
(22, '2025_01_24_064256_create_table_roles_table', 1),
(23, '2025_01_24_064308_create_table_users_table', 1),
(24, '2025_01_24_064317_create_units_table', 1),
(25, '2025_01_24_064347_create_warehouses_table', 1),
(26, '2025_01_24_064354_create_warehouse_items_table', 1),
(27, '2025_01_24_064402_create_warehouse_sales_table', 1),
(28, '2025_01_24_105520_create_personal_access_tokens_table', 2),
(29, '2025_01_24_063753_create_accounts_table', 3),
(30, '2025_01_24_131340_create_personal_access_tokens_table', 4),
(31, '2025_02_21_031315_create_sales_details', 5),
(32, '2025_02_24_115524_income_type', 6),
(33, '2025_02_24_115533_expense_type', 6),
(34, '2025_02_25_062744_create_clearances_table', 7),
(35, '2025_04_10_185453_warehouse_wastage', 8),
(36, '2024_09_07_115247_add_username_field_to_users_table', 9),
(37, '2014_04_02_193005_create_translations_table', 10),
(40, '2026_06_20_180319_create_orders_table', 11),
(41, '2026_06_24_174555_create_buy_invoices', 12),
(42, '2026_06_24_180653_add_has_invoice_column_to_bought_items_for_tracking', 13);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ord_num` bigint(20) DEFAULT NULL COMMENT 'system auto number',
  `pre_list_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'employee_id = (account_id)',
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'supplier_id = (account_id)',
  `customer_id` bigint(20) NOT NULL COMMENT 'account_id in accounts',
  `amount` double NOT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `iby` varchar(255) NOT NULL COMMENT 'Inserted By',
  `idate` varchar(255) NOT NULL COMMENT 'Inserted Date',
  `state` int(11) NOT NULL DEFAULT 0 COMMENT '0:Draft, 1:new, 2:cancelled, 3: completed',
  `done_year` int(11) DEFAULT NULL,
  `done_month` int(11) DEFAULT NULL,
  `done_day` int(11) DEFAULT NULL,
  `done_by` varchar(255) DEFAULT NULL,
  `times` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `ord_num`, `pre_list_id`, `category_id`, `employee_id`, `supplier_id`, `customer_id`, `amount`, `unit_id`, `iby`, `idate`, `state`, `done_year`, `done_month`, `done_day`, `done_by`, `times`, `created_at`, `updated_at`) VALUES
(34, 2, 88, 3, 92, 91, 0, 4, 6, 'شرکت نرم افزار کاوشگران', '2026-06-20 00:00:00', 2, NULL, NULL, NULL, NULL, '1782139020', '2026-06-22 14:37:00', '2026-06-23 12:45:57'),
(35, 2, 93, 2, 92, 91, 0, 5, 9, 'شرکت نرم افزار کاوشگران', '2026-06-20 00:00:00', 2, NULL, NULL, NULL, NULL, '1782139020', '2026-06-22 14:37:00', '2026-06-23 12:45:57'),
(59, 10, 89, 3, 87, 91, 0, 333, 6, 'ادمین عمومی', '2026-07-16', 1, NULL, NULL, NULL, NULL, '1783954687', '2026-07-13 14:58:07', '2026-07-13 14:58:07'),
(60, 4, 88, 3, 92, 86, 0, 3, 6, 'ادمین عمومی', '2026-07-13', 0, NULL, NULL, NULL, NULL, '1783954720', '2026-07-13 14:58:40', '2026-07-14 12:51:16'),
(61, 4, 89, 3, 92, 86, 0, 4, 6, 'ادمین عمومی', '2026-07-13', 0, NULL, NULL, NULL, NULL, '1783954720', '2026-07-13 14:58:40', '2026-07-14 12:51:16'),
(62, 9, 93, 2, 87, 91, 0, 4, 3, 'ادمین عمومی', '2026-07-13', 0, NULL, NULL, NULL, NULL, '1784033398', '2026-07-14 12:49:58', '2026-07-14 12:49:58'),
(63, 8, 94, 2, 87, 86, 0, 3, 7, 'ادمین عمومی', '2026-07-13', 0, NULL, NULL, NULL, NULL, '1784033414', '2026-07-14 12:50:14', '2026-07-14 12:50:14'),
(64, 7, 88, 3, 87, 86, 0, 5, 6, 'ادمین عمومی', '2026-07-13', 0, NULL, NULL, NULL, NULL, '1784033427', '2026-07-14 12:50:27', '2026-07-14 12:50:27'),
(65, 5, 93, 2, 87, 86, 0, 4, 7, 'ادمین عمومی', '2026-06-23', 0, NULL, NULL, NULL, NULL, '1784033444', '2026-07-14 12:50:44', '2026-07-14 12:50:44'),
(66, 6, 87, 1, 87, 86, 0, 2, 3, 'ادمین عمومی', '2026-07-11', 0, NULL, NULL, NULL, NULL, '1784049356', '2026-07-14 17:15:56', '2026-07-14 17:15:56'),
(67, 6, 89, 3, 87, 86, 0, 3, 8, 'ادمین عمومی', '2026-07-11', 0, NULL, NULL, NULL, NULL, '1784049356', '2026-07-14 17:15:56', '2026-07-14 17:15:56'),
(68, 3, 93, 2, 87, 86, 0, 5, 8, 'ادمین عمومی', '2026-06-22', 3, NULL, NULL, NULL, NULL, '1784049388', '2026-07-14 17:16:28', '2026-07-14 17:16:28'),
(69, 3, 90, 4, 87, 86, 0, 6, 9, 'ادمین عمومی', '2026-06-22', 3, NULL, NULL, NULL, NULL, '1784049388', '2026-07-14 17:16:28', '2026-07-14 17:16:28'),
(70, 1, 88, 3, 87, 86, 0, 5, 6, 'ادمین عمومی', '2026-06-22', 3, NULL, NULL, NULL, NULL, '1784049413', '2026-07-14 17:16:53', '2026-07-14 17:16:53'),
(71, 1, 92, 4, 87, 86, 0, 6, 4, 'ادمین عمومی', '2026-06-22', 3, NULL, NULL, NULL, NULL, '1784049413', '2026-07-14 17:16:53', '2026-07-14 17:16:53'),
(72, 1, 86, 1, 87, 86, 0, 7, 8, 'ادمین عمومی', '2026-06-22', 3, NULL, NULL, NULL, NULL, '1784049413', '2026-07-14 17:16:53', '2026-07-14 17:16:53'),
(73, NULL, 94, 2, NULL, NULL, 94, 20, 18, 'ادمین عمومی', '2026-07-14', 0, NULL, NULL, NULL, NULL, '1784050983', '2026-07-14 17:44:50', '2026-07-14 17:44:50'),
(74, NULL, 93, 2, NULL, NULL, 94, 30, 18, 'ادمین عمومی', '2026-07-14', 0, NULL, NULL, NULL, NULL, '1784050983', '2026-07-14 17:44:50', '2026-07-14 17:44:50'),
(75, NULL, 94, 2, NULL, NULL, 94, 35, 18, 'ادمین عمومی', '2026-07-14', 0, NULL, NULL, NULL, NULL, '1784050983', '2026-07-14 17:44:50', '2026-07-14 17:44:50');

-- --------------------------------------------------------

--
-- Table structure for table `org_bios`
--

CREATE TABLE `org_bios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `header` varchar(255) NOT NULL,
  `logos` varchar(255) NOT NULL,
  `tax_activation` tinyint(2) NOT NULL DEFAULT 0 COMMENT '0:not active 1:active',
  `tax_per` tinyint(4) DEFAULT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1 COMMENT '0: not active, 1: active',
  `note_for_print` varchar(255) DEFAULT NULL,
  `is_expired` tinyint(4) DEFAULT 0 COMMENT '0:open, 1:expired',
  `expired_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `org_bios`
--

INSERT INTO `org_bios` (`id`, `name`, `address`, `phone`, `header`, `logos`, `tax_activation`, `tax_per`, `is_active`, `note_for_print`, `is_expired`, `expired_date`, `created_at`, `updated_at`) VALUES
(2, 'سیستم مدیریتی سفارشات آسان ارسال', 'مرکز تجارتی داودزی - کابل - افغانستان', '0729010123', 'headers/1783682766_header.jpg', 'logos/1783675724_sm-logo.jpeg', 1, 6, 1, 'اجناس فروخته شده پس گرفته نمیشود. و قرض هم داده نمیشود', 0, '2027-04-12', NULL, '2026-07-15 05:25:30');

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` int(11) NOT NULL DEFAULT 1 COMMENT '1:lite, 2:business, 3:business+',
  `activated_by` varchar(255) DEFAULT NULL,
  `activated_date` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0: not active, 1:active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `name`, `type`, `activated_by`, `activated_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 'lite', 1, NULL, NULL, 0, NULL, NULL),
(4, 'business', 2, NULL, NULL, 0, NULL, NULL),
(5, 'Business Plus', 3, NULL, NULL, 0, NULL, NULL),
(6, 'Business Plus + POS', 4, NULL, NULL, 0, NULL, NULL),
(7, 'Business Plus + POS + Production', 5, NULL, NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rates`
--

CREATE TABLE `rates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `from_currency_id` bigint(20) UNSIGNED NOT NULL,
  `from_currency_amount` double NOT NULL DEFAULT 1,
  `to_currency_id` bigint(20) UNSIGNED NOT NULL,
  `to_currency_amount` double NOT NULL,
  `reverse_amount` double NOT NULL,
  `greater_account_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rates`
--

INSERT INTO `rates` (`id`, `from_currency_id`, `from_currency_amount`, `to_currency_id`, `to_currency_amount`, `reverse_amount`, `greater_account_id`, `created_at`, `updated_at`) VALUES
(12, 2, 1, 1, 72.5, 0.0137931034, 2, '2025-03-08 08:55:30', '2025-03-08 08:55:30'),
(13, 1, 1, 3, 3, 0.3333333, 1, '2025-03-08 08:55:30', '2025-03-08 08:55:30'),
(14, 1, 1, 4, 500, 0.002, 1, '2025-03-08 08:55:30', '2025-03-08 08:55:30');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `roleId` tinyint(3) UNSIGNED NOT NULL,
  `role` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `isDeleted` tinyint(4) NOT NULL DEFAULT 0,
  `createdBy` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`roleId`, `role`, `status`, `isDeleted`, `createdBy`, `created_at`, `updated_at`) VALUES
(2, 'ادمین', 1, 0, 1, '2025-02-22 08:21:30', '2025-02-22 13:46:22'),
(10, 'عادی (بیننده)', 1, 0, 13, '2026-07-11 08:49:53', '2026-07-11 10:04:39'),
(11, 'دریوران', 1, 0, 13, '2026-07-11 10:05:48', '2026-07-11 10:05:48');

-- --------------------------------------------------------

--
-- Table structure for table `sales_details`
--

CREATE TABLE `sales_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `billno` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `warehouse_id` int(11) NOT NULL,
  `warehouse_sales_id` bigint(20) UNSIGNED NOT NULL,
  `pre_list_id` bigint(20) UNSIGNED DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `amount` double NOT NULL,
  `buy_up` double DEFAULT NULL COMMENT 'with or without tax',
  `sell_up` double DEFAULT NULL COMMENT 'with or without tax',
  `sell_up_no_tax` decimal(10,0) DEFAULT 0 COMMENT 'sell_up without tax',
  `sell_tax_per` int(11) DEFAULT NULL,
  `sell_tax_price` decimal(15,2) DEFAULT NULL,
  `profit` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `is_returned` int(11) NOT NULL DEFAULT 0 COMMENT '0:not returned, 1:returned',
  `todays_date` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_invoices`
--

CREATE TABLE `sales_invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `total` decimal(15,2) DEFAULT 0.00,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `remaining` decimal(15,2) DEFAULT 0.00,
  `currency_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tax_activation` int(2) DEFAULT 0 COMMENT '0:not, 1:yes',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0:draft, 1:pending, 2:partial, 3:paid, 4:cancelled',
  `invoice_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `times` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_invoice_items`
--

CREATE TABLE `sales_invoice_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `sales_details_id` bigint(20) UNSIGNED DEFAULT NULL,
  `warehouse_sales_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pre_list_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` double NOT NULL DEFAULT 0,
  `unit_id` int(11) NOT NULL,
  `unit_price` double NOT NULL DEFAULT 0,
  `tax_percentage` int(11) NOT NULL,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sell_up_vat` decimal(15,2) DEFAULT NULL COMMENT 'Sell Unit Price Value Added Tax',
  `total` decimal(15,2) DEFAULT 0.00,
  `total_vat` decimal(15,2) DEFAULT 0.00,
  `times` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_invoice_payments`
--

CREATE TABLE `sales_invoice_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1:cash, 2:bank, 3:loan',
  `account_id` int(10) UNSIGNED DEFAULT NULL,
  `customer_account_id` int(11) NOT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `times` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0Ei6rxaaW1mnHVLxBUCwWgVF9hlQBiBXohv4Lyo6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiUzhHYUVjZE5LVllyb21tZm9HZWNCWjk1clllVXJPMDRhb1pmMmpCNSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1749192820),
('5bDeKndYaJVzQv98oe1wjfozP83iOFo49hvaImQT', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQmdvNWJjdjROWUNhTUZiUXpYQ3ZxRDdtRTVzUzJOOUluTGxHOUJ3MSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1749192827),
('Brq0P3hz7cBSYKyrzNiiyTxhLQJBRE4Vsj9Ydf6i', 13, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YToxNTp7czo2OiJfdG9rZW4iO3M6NDA6InhEZkVlUzZjTHBGcW10SEhLeFR5OGJROVpybG83SGcwT1gzZXRVOEkiO3M6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6MjY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9ob21lIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9ob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo2OiJ1c2VySWQiO2k6MTM7czo0OiJyb2xlIjtpOjI7czo4OiJyb2xlVGV4dCI7czoxMDoi2KfYr9mF24zZhiI7czo0OiJuYW1lIjtzOjQzOiLYtNix2qnYqiDZhtix2YUg2KfZgdiy2KfYsSDaqdin2YjYtNqv2LHYp9mGIjtzOjc6ImlzQWRtaW4iO2k6MTtzOjk6ImJyYW5jaF9pZCI7aTo0NjtzOjEwOiJhY2Nlc3NJbmZvIjthOjEyOntzOjg6InNldHRpbmdzIjthOjc6e3M6NjoibW9kdWxlIjtzOjg6InNldHRpbmdzIjtzOjU6ImxhYmVsIjtpOjA7czoxMjoidG90YWxfYWNjZXNzIjtpOjE7czo0OiJsaXN0IjtpOjA7czoxNDoiY3JlYXRlX3JlY29yZHMiO2k6MDtzOjEyOiJlZGl0X3JlY29yZHMiO2k6MDtzOjE0OiJkZWxldGVfcmVjb3JkcyI7aTowO31zOjU6InJhdGVzIjthOjc6e3M6NjoibW9kdWxlIjtzOjU6InJhdGVzIjtzOjU6ImxhYmVsIjtpOjA7czoxMjoidG90YWxfYWNjZXNzIjtpOjE7czo0OiJsaXN0IjtpOjA7czoxNDoiY3JlYXRlX3JlY29yZHMiO2k6MDtzOjEyOiJlZGl0X3JlY29yZHMiO2k6MDtzOjE0OiJkZWxldGVfcmVjb3JkcyI7aTowO31zOjc6ImpvdXJuYWwiO2E6Nzp7czo2OiJtb2R1bGUiO3M6Nzoiam91cm5hbCI7czo1OiJsYWJlbCI7aTowO3M6MTI6InRvdGFsX2FjY2VzcyI7aToxO3M6NDoibGlzdCI7aTowO3M6MTQ6ImNyZWF0ZV9yZWNvcmRzIjtpOjA7czoxMjoiZWRpdF9yZWNvcmRzIjtpOjA7czoxNDoiZGVsZXRlX3JlY29yZHMiO2k6MDt9czo2OiJpbmNvbWUiO2E6Nzp7czo2OiJtb2R1bGUiO3M6NjoiaW5jb21lIjtzOjU6ImxhYmVsIjtpOjA7czoxMjoidG90YWxfYWNjZXNzIjtpOjE7czo0OiJsaXN0IjtpOjA7czoxNDoiY3JlYXRlX3JlY29yZHMiO2k6MDtzOjEyOiJlZGl0X3JlY29yZHMiO2k6MDtzOjE0OiJkZWxldGVfcmVjb3JkcyI7aTowO31zOjc6ImV4cGVuc2UiO2E6Nzp7czo2OiJtb2R1bGUiO3M6NzoiZXhwZW5zZSI7czo1OiJsYWJlbCI7aTowO3M6MTI6InRvdGFsX2FjY2VzcyI7aToxO3M6NDoibGlzdCI7aTowO3M6MTQ6ImNyZWF0ZV9yZWNvcmRzIjtpOjA7czoxMjoiZWRpdF9yZWNvcmRzIjtpOjA7czoxNDoiZGVsZXRlX3JlY29yZHMiO2k6MDt9czoyOiJociI7YTo3OntzOjY6Im1vZHVsZSI7czoyOiJociI7czo1OiJsYWJlbCI7aTowO3M6MTI6InRvdGFsX2FjY2VzcyI7aToxO3M6NDoibGlzdCI7aTowO3M6MTQ6ImNyZWF0ZV9yZWNvcmRzIjtpOjA7czoxMjoiZWRpdF9yZWNvcmRzIjtpOjA7czoxNDoiZGVsZXRlX3JlY29yZHMiO2k6MDt9czozOiJidXkiO2E6Nzp7czo2OiJtb2R1bGUiO3M6MzoiYnV5IjtzOjU6ImxhYmVsIjtpOjA7czoxMjoidG90YWxfYWNjZXNzIjtpOjE7czo0OiJsaXN0IjtpOjA7czoxNDoiY3JlYXRlX3JlY29yZHMiO2k6MDtzOjEyOiJlZGl0X3JlY29yZHMiO2k6MDtzOjE0OiJkZWxldGVfcmVjb3JkcyI7aTowO31zOjU6Imd1ZGFtIjthOjc6e3M6NjoibW9kdWxlIjtzOjU6Imd1ZGFtIjtzOjU6ImxhYmVsIjtpOjA7czoxMjoidG90YWxfYWNjZXNzIjtpOjE7czo0OiJsaXN0IjtpOjA7czoxNDoiY3JlYXRlX3JlY29yZHMiO2k6MDtzOjEyOiJlZGl0X3JlY29yZHMiO2k6MDtzOjE0OiJkZWxldGVfcmVjb3JkcyI7aTowO31zOjU6InNhbGVzIjthOjc6e3M6NjoibW9kdWxlIjtzOjU6InNhbGVzIjtzOjU6ImxhYmVsIjtpOjA7czoxMjoidG90YWxfYWNjZXNzIjtpOjE7czo0OiJsaXN0IjtpOjA7czoxNDoiY3JlYXRlX3JlY29yZHMiO2k6MDtzOjEyOiJlZGl0X3JlY29yZHMiO2k6MDtzOjE0OiJkZWxldGVfcmVjb3JkcyI7aTowO31zOjk6ImNsZWFyYW5jZSI7YTo3OntzOjY6Im1vZHVsZSI7czo5OiJjbGVhcmFuY2UiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6NzoicmVwb3J0cyI7YTo3OntzOjY6Im1vZHVsZSI7czo3OiJyZXBvcnRzIjtzOjU6ImxhYmVsIjtpOjA7czoxMjoidG90YWxfYWNjZXNzIjtpOjE7czo0OiJsaXN0IjtpOjA7czoxNDoiY3JlYXRlX3JlY29yZHMiO2k6MDtzOjEyOiJlZGl0X3JlY29yZHMiO2k6MDtzOjE0OiJkZWxldGVfcmVjb3JkcyI7aTowO31zOjU6InVzZXJzIjthOjc6e3M6NjoibW9kdWxlIjtzOjU6InVzZXJzIjtzOjU6ImxhYmVsIjtpOjA7czoxMjoidG90YWxfYWNjZXNzIjtpOjE7czo0OiJsaXN0IjtpOjA7czoxNDoiY3JlYXRlX3JlY29yZHMiO2k6MDtzOjEyOiJlZGl0X3JlY29yZHMiO2k6MDtzOjE0OiJkZWxldGVfcmVjb3JkcyI7aTowO319czoxMDoiaXNMb2dnZWRJbiI7YjoxO3M6MTI6InBhY2thZ2VfdHlwZSI7aTo0O3M6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjEzO3M6NjoibG9jYWxlIjtzOjI6ImVuIjt9', 1749192762),
('e22HBk4qqnjEo0UHAQDQgyX7Gj1SxRGzkxYve1Pf', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiY1VuODJpRklzUHJGM2VYQ3JBY3VwYnlwcWtlbnBVV2FnR25jcE05ZSI7czo2OiJsb2NhbGUiO3M6MjoiZW4iO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM1OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvc2V0LWxvY2FsZS9lbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1749192822),
('F6wXlxeQR16Ohf0TFv0inwlepmjLs76W6KaKHPtO', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoibkZ2VHpVeE45d211WUFCOU16dk9LOWxSTlpHVFRlUm50M1psMTdQMiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1749192798),
('gGY3UVmo5meIbgdVGQBJXdGcOC8gjK7rdBWjBfOe', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMWdvR3c0SkptWDJPbWw4OTZHbGE2YzFuQ054NTdudHJ0VWZ1Rjc2NCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1749192790),
('IG7wCwxYBdrVKDC6KwDlQLKHnOvNzEqubtO5sO7L', 13, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YToxMzp7czo2OiJfdG9rZW4iO3M6NDA6InZ0ajR6Uzc0UUczWUtYUFZReU5yNVRvRUd5dE9GbTNpd2NoUUtvdGUiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjIxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjY6InVzZXJJZCI7aToxMztzOjQ6InJvbGUiO2k6MjtzOjg6InJvbGVUZXh0IjtzOjEwOiLYp9iv2YXbjNmGIjtzOjQ6Im5hbWUiO3M6NDM6Iti02LHaqdiqINmG2LHZhSDYp9mB2LLYp9ixINqp2KfZiNi02q/Ysdin2YYiO3M6NzoiaXNBZG1pbiI7aToxO3M6OToiYnJhbmNoX2lkIjtpOjQ2O3M6MTA6ImFjY2Vzc0luZm8iO2E6MTI6e3M6ODoic2V0dGluZ3MiO2E6Nzp7czo2OiJtb2R1bGUiO3M6ODoic2V0dGluZ3MiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6NToicmF0ZXMiO2E6Nzp7czo2OiJtb2R1bGUiO3M6NToicmF0ZXMiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6Nzoiam91cm5hbCI7YTo3OntzOjY6Im1vZHVsZSI7czo3OiJqb3VybmFsIjtzOjU6ImxhYmVsIjtpOjA7czoxMjoidG90YWxfYWNjZXNzIjtpOjE7czo0OiJsaXN0IjtpOjA7czoxNDoiY3JlYXRlX3JlY29yZHMiO2k6MDtzOjEyOiJlZGl0X3JlY29yZHMiO2k6MDtzOjE0OiJkZWxldGVfcmVjb3JkcyI7aTowO31zOjY6ImluY29tZSI7YTo3OntzOjY6Im1vZHVsZSI7czo2OiJpbmNvbWUiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6NzoiZXhwZW5zZSI7YTo3OntzOjY6Im1vZHVsZSI7czo3OiJleHBlbnNlIjtzOjU6ImxhYmVsIjtpOjA7czoxMjoidG90YWxfYWNjZXNzIjtpOjE7czo0OiJsaXN0IjtpOjA7czoxNDoiY3JlYXRlX3JlY29yZHMiO2k6MDtzOjEyOiJlZGl0X3JlY29yZHMiO2k6MDtzOjE0OiJkZWxldGVfcmVjb3JkcyI7aTowO31zOjI6ImhyIjthOjc6e3M6NjoibW9kdWxlIjtzOjI6ImhyIjtzOjU6ImxhYmVsIjtpOjA7czoxMjoidG90YWxfYWNjZXNzIjtpOjE7czo0OiJsaXN0IjtpOjA7czoxNDoiY3JlYXRlX3JlY29yZHMiO2k6MDtzOjEyOiJlZGl0X3JlY29yZHMiO2k6MDtzOjE0OiJkZWxldGVfcmVjb3JkcyI7aTowO31zOjM6ImJ1eSI7YTo3OntzOjY6Im1vZHVsZSI7czozOiJidXkiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6NToiZ3VkYW0iO2E6Nzp7czo2OiJtb2R1bGUiO3M6NToiZ3VkYW0iO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6NToic2FsZXMiO2E6Nzp7czo2OiJtb2R1bGUiO3M6NToic2FsZXMiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6OToiY2xlYXJhbmNlIjthOjc6e3M6NjoibW9kdWxlIjtzOjk6ImNsZWFyYW5jZSI7czo1OiJsYWJlbCI7aTowO3M6MTI6InRvdGFsX2FjY2VzcyI7aToxO3M6NDoibGlzdCI7aTowO3M6MTQ6ImNyZWF0ZV9yZWNvcmRzIjtpOjA7czoxMjoiZWRpdF9yZWNvcmRzIjtpOjA7czoxNDoiZGVsZXRlX3JlY29yZHMiO2k6MDt9czo3OiJyZXBvcnRzIjthOjc6e3M6NjoibW9kdWxlIjtzOjc6InJlcG9ydHMiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6NToidXNlcnMiO2E6Nzp7czo2OiJtb2R1bGUiO3M6NToidXNlcnMiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fX1zOjEwOiJpc0xvZ2dlZEluIjtiOjE7czoxMjoicGFja2FnZV90eXBlIjtpOjQ7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTM7fQ==', 1749192839),
('KYNt2HELg4p1uLAc79kZorBPmTdq6YNFMFU4sf23', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNnJ6ekR4TUJkNk5pVlNTbVIzNVFHTHV6Y1ZMbEtLOWU0bXBReEk1dSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2hvbWUiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czoyNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2hvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1749192764),
('m39HqSZYishDSXbaxtnObyuv0nuHewWUR9D9YrjJ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiZEF1MU5McUNGVnBqc0NRRGJVa25XcDRQVzZuUlM3T0hqUTk5S1RDbCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1749192845),
('mcZAwLHrzHR4ZKdEMzR1GIbGxayT3Ar3E8w1Uxy9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiVlNHbk44NVNaSmJMSWtxeXRyQ3p2Qm80YVE2S3Vzc2swMDIyRUlyZSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1749192853),
('pFE7zRMcHPvp5Oh4KuwlwqKIZywo6oLfEGyYD7Yf', 13, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YToxMzp7czo2OiJfdG9rZW4iO3M6NDA6IjFnb0d3NEpKbVgyT21sODk2R2xhNmMxbkNOeDU3bnRydFVmdUY3NjQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjIxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjY6InVzZXJJZCI7aToxMztzOjQ6InJvbGUiO2k6MjtzOjg6InJvbGVUZXh0IjtzOjEwOiLYp9iv2YXbjNmGIjtzOjQ6Im5hbWUiO3M6NDM6Iti02LHaqdiqINmG2LHZhSDYp9mB2LLYp9ixINqp2KfZiNi02q/Ysdin2YYiO3M6NzoiaXNBZG1pbiI7aToxO3M6OToiYnJhbmNoX2lkIjtpOjQ2O3M6MTA6ImFjY2Vzc0luZm8iO2E6MTI6e3M6ODoic2V0dGluZ3MiO2E6Nzp7czo2OiJtb2R1bGUiO3M6ODoic2V0dGluZ3MiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6NToicmF0ZXMiO2E6Nzp7czo2OiJtb2R1bGUiO3M6NToicmF0ZXMiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6Nzoiam91cm5hbCI7YTo3OntzOjY6Im1vZHVsZSI7czo3OiJqb3VybmFsIjtzOjU6ImxhYmVsIjtpOjA7czoxMjoidG90YWxfYWNjZXNzIjtpOjE7czo0OiJsaXN0IjtpOjA7czoxNDoiY3JlYXRlX3JlY29yZHMiO2k6MDtzOjEyOiJlZGl0X3JlY29yZHMiO2k6MDtzOjE0OiJkZWxldGVfcmVjb3JkcyI7aTowO31zOjY6ImluY29tZSI7YTo3OntzOjY6Im1vZHVsZSI7czo2OiJpbmNvbWUiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6NzoiZXhwZW5zZSI7YTo3OntzOjY6Im1vZHVsZSI7czo3OiJleHBlbnNlIjtzOjU6ImxhYmVsIjtpOjA7czoxMjoidG90YWxfYWNjZXNzIjtpOjE7czo0OiJsaXN0IjtpOjA7czoxNDoiY3JlYXRlX3JlY29yZHMiO2k6MDtzOjEyOiJlZGl0X3JlY29yZHMiO2k6MDtzOjE0OiJkZWxldGVfcmVjb3JkcyI7aTowO31zOjI6ImhyIjthOjc6e3M6NjoibW9kdWxlIjtzOjI6ImhyIjtzOjU6ImxhYmVsIjtpOjA7czoxMjoidG90YWxfYWNjZXNzIjtpOjE7czo0OiJsaXN0IjtpOjA7czoxNDoiY3JlYXRlX3JlY29yZHMiO2k6MDtzOjEyOiJlZGl0X3JlY29yZHMiO2k6MDtzOjE0OiJkZWxldGVfcmVjb3JkcyI7aTowO31zOjM6ImJ1eSI7YTo3OntzOjY6Im1vZHVsZSI7czozOiJidXkiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6NToiZ3VkYW0iO2E6Nzp7czo2OiJtb2R1bGUiO3M6NToiZ3VkYW0iO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6NToic2FsZXMiO2E6Nzp7czo2OiJtb2R1bGUiO3M6NToic2FsZXMiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6OToiY2xlYXJhbmNlIjthOjc6e3M6NjoibW9kdWxlIjtzOjk6ImNsZWFyYW5jZSI7czo1OiJsYWJlbCI7aTowO3M6MTI6InRvdGFsX2FjY2VzcyI7aToxO3M6NDoibGlzdCI7aTowO3M6MTQ6ImNyZWF0ZV9yZWNvcmRzIjtpOjA7czoxMjoiZWRpdF9yZWNvcmRzIjtpOjA7czoxNDoiZGVsZXRlX3JlY29yZHMiO2k6MDt9czo3OiJyZXBvcnRzIjthOjc6e3M6NjoibW9kdWxlIjtzOjc6InJlcG9ydHMiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6NToidXNlcnMiO2E6Nzp7czo2OiJtb2R1bGUiO3M6NToidXNlcnMiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fX1zOjEwOiJpc0xvZ2dlZEluIjtiOjE7czoxMjoicGFja2FnZV90eXBlIjtpOjQ7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTM7fQ==', 1749192793),
('PXU1rVPCwiR8qNXbPSxNiNrZG6mdeI6etEtXuwKv', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoidmo2NGJzbFpUZTh2dFZLS0QxSG81M3k2OHJJOHFzSXA4QktCaEMwZCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1749192769),
('tCw0HVeLJ3nxiAUZz1SKZgpN21tW0IHP8xFka6zE', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSU9WVUNxd3dDclRMU1dBWG9OaVVSWmI1NFdzVE5udFNRNVlxb2hIayI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1749192808),
('TxlLSRH6JpromVga6uyo4nWGvQApiMfTwUe1Ad14', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUFZ6UkxEZUp3dk50ZFdRdHFEc0dVRkREY3BQd2pucVhoSDZTZE1DMyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2hvbWUiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czoyNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2hvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1749192847),
('vXQ6idvyTJkowE5Gp4s1yY9m1r3PArXGuneAoMxg', 13, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YToxMzp7czo2OiJfdG9rZW4iO3M6NDA6IklPVlVDcXd3Q3JUTFNXQVhvTmlVUlpiNTRXc1RObnRTUTVZcW9oSGsiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjIxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjY6InVzZXJJZCI7aToxMztzOjQ6InJvbGUiO2k6MjtzOjg6InJvbGVUZXh0IjtzOjEwOiLYp9iv2YXbjNmGIjtzOjQ6Im5hbWUiO3M6NDM6Iti02LHaqdiqINmG2LHZhSDYp9mB2LLYp9ixINqp2KfZiNi02q/Ysdin2YYiO3M6NzoiaXNBZG1pbiI7aToxO3M6OToiYnJhbmNoX2lkIjtpOjQ2O3M6MTA6ImFjY2Vzc0luZm8iO2E6MTI6e3M6ODoic2V0dGluZ3MiO2E6Nzp7czo2OiJtb2R1bGUiO3M6ODoic2V0dGluZ3MiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6NToicmF0ZXMiO2E6Nzp7czo2OiJtb2R1bGUiO3M6NToicmF0ZXMiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6Nzoiam91cm5hbCI7YTo3OntzOjY6Im1vZHVsZSI7czo3OiJqb3VybmFsIjtzOjU6ImxhYmVsIjtpOjA7czoxMjoidG90YWxfYWNjZXNzIjtpOjE7czo0OiJsaXN0IjtpOjA7czoxNDoiY3JlYXRlX3JlY29yZHMiO2k6MDtzOjEyOiJlZGl0X3JlY29yZHMiO2k6MDtzOjE0OiJkZWxldGVfcmVjb3JkcyI7aTowO31zOjY6ImluY29tZSI7YTo3OntzOjY6Im1vZHVsZSI7czo2OiJpbmNvbWUiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6NzoiZXhwZW5zZSI7YTo3OntzOjY6Im1vZHVsZSI7czo3OiJleHBlbnNlIjtzOjU6ImxhYmVsIjtpOjA7czoxMjoidG90YWxfYWNjZXNzIjtpOjE7czo0OiJsaXN0IjtpOjA7czoxNDoiY3JlYXRlX3JlY29yZHMiO2k6MDtzOjEyOiJlZGl0X3JlY29yZHMiO2k6MDtzOjE0OiJkZWxldGVfcmVjb3JkcyI7aTowO31zOjI6ImhyIjthOjc6e3M6NjoibW9kdWxlIjtzOjI6ImhyIjtzOjU6ImxhYmVsIjtpOjA7czoxMjoidG90YWxfYWNjZXNzIjtpOjE7czo0OiJsaXN0IjtpOjA7czoxNDoiY3JlYXRlX3JlY29yZHMiO2k6MDtzOjEyOiJlZGl0X3JlY29yZHMiO2k6MDtzOjE0OiJkZWxldGVfcmVjb3JkcyI7aTowO31zOjM6ImJ1eSI7YTo3OntzOjY6Im1vZHVsZSI7czozOiJidXkiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6NToiZ3VkYW0iO2E6Nzp7czo2OiJtb2R1bGUiO3M6NToiZ3VkYW0iO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6NToic2FsZXMiO2E6Nzp7czo2OiJtb2R1bGUiO3M6NToic2FsZXMiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6OToiY2xlYXJhbmNlIjthOjc6e3M6NjoibW9kdWxlIjtzOjk6ImNsZWFyYW5jZSI7czo1OiJsYWJlbCI7aTowO3M6MTI6InRvdGFsX2FjY2VzcyI7aToxO3M6NDoibGlzdCI7aTowO3M6MTQ6ImNyZWF0ZV9yZWNvcmRzIjtpOjA7czoxMjoiZWRpdF9yZWNvcmRzIjtpOjA7czoxNDoiZGVsZXRlX3JlY29yZHMiO2k6MDt9czo3OiJyZXBvcnRzIjthOjc6e3M6NjoibW9kdWxlIjtzOjc6InJlcG9ydHMiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fXM6NToidXNlcnMiO2E6Nzp7czo2OiJtb2R1bGUiO3M6NToidXNlcnMiO3M6NToibGFiZWwiO2k6MDtzOjEyOiJ0b3RhbF9hY2Nlc3MiO2k6MTtzOjQ6Imxpc3QiO2k6MDtzOjE0OiJjcmVhdGVfcmVjb3JkcyI7aTowO3M6MTI6ImVkaXRfcmVjb3JkcyI7aTowO3M6MTQ6ImRlbGV0ZV9yZWNvcmRzIjtpOjA7fX1zOjEwOiJpc0xvZ2dlZEluIjtiOjE7czoxMjoicGFja2FnZV90eXBlIjtpOjQ7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTM7fQ==', 1749192815),
('WdjhSwyyERb5mVkr8bXR7fXmnXG8zpjZPWPv9w1R', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidnRqNHpTNzRRRzNZS1hQVlF5TnI1VG9FR3l0T0ZtM2l3Y2hRS290ZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1749192835),
('yZe3HrmlyM3h1enDD5orFaiuAsUzLtmpZ2v3rZxm', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiU3Rlb255dWJoNXlHWXJxZ0hzUnFNWFphd2RqREFoYkJPNUZJdkpucyI7czo2OiJsb2NhbGUiO3M6MjoicGEiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM1OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvc2V0LWxvY2FsZS9wYSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1749192801);

-- --------------------------------------------------------

--
-- Table structure for table `table_reset_passwords`
--

CREATE TABLE `table_reset_passwords` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `activation_id` varchar(255) NOT NULL,
  `agent` varchar(255) NOT NULL,
  `client_ip` varchar(255) NOT NULL,
  `isDeleted` tinyint(4) NOT NULL DEFAULT 0,
  `createdBy` bigint(20) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `name`, `created_at`, `updated_at`) VALUES
(3, 'دانه', '2025-01-31 12:34:42', '2025-01-31 12:34:42'),
(4, 'عدد', '2025-01-31 12:38:33', '2025-01-31 13:00:55'),
(6, 'بوجی', '2025-02-01 11:22:22', '2025-02-01 11:22:33'),
(7, 'کیلو', '2025-02-19 06:50:04', '2025-02-19 06:50:04'),
(8, 'سیر', '2025-02-19 06:50:09', '2025-02-19 06:50:09'),
(9, 'چارک', '2025-02-19 06:50:16', '2025-02-24 02:23:07'),
(10, 'گرام', '2025-03-09 13:24:15', '2025-03-09 13:24:15'),
(12, 'کارتن', '2025-04-12 04:05:59', '2025-04-13 12:46:13'),
(16, 'متر', '2025-12-03 15:28:01', '2025-12-03 15:28:01'),
(18, 'بسته', '2026-07-14 17:40:16', '2026-07-14 17:40:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_id` int(11) DEFAULT 0 COMMENT 'has relation with account, if account is selected, will show only this records',
  `full_name` varchar(255) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `account_id`, `full_name`, `user_name`, `email`, `username`, `email_verified_at`, `password`, `roleId`, `isAdmin`, `isDeleted`, `isHidden`, `photo`, `createdBy`, `remember_token`, `created_at`, `updated_at`) VALUES
(13, 0, 'ادمین عمومی', 'erfan', 'abdullatif.erfan@gmail.com', NULL, NULL, '$2y$12$uP/I5.H3FOetkMv3BxItyObFevcS59e683dVSa/ftBj9PEWz2qCqm', 2, 1, 0, 1, 'user_photos/rxLaGsTzamy2iFo10rXZizlIIAEjuTSiIHks2Hbr.jpg', 1, NULL, '2025-03-19 03:44:39', '2026-07-10 10:21:23'),
(14, 0, 'ادمین هرات', 'herat', 'herat@gmail.com', NULL, NULL, '$2y$12$2gP1/m/IKE.Cu0t.eJaikOzzezqyBaKTPw5r34imLa3cNskTb8zBC', 9, 1, 0, 0, NULL, 13, NULL, '2025-04-13 12:32:05', '2025-04-13 12:32:05'),
(15, 87, 'عرفان (بیننده)', 'viewer', NULL, NULL, NULL, '$2y$12$KGhvSi53UMBU4HGQt/w.a.Yl4vMAphJsh.aDb8jKnq37U59RTG2XG', 10, 0, 0, 0, NULL, 13, NULL, '2026-07-11 10:24:47', '2026-07-12 04:46:56'),
(17, 92, 'کریم دریور مازدا', 'driver', NULL, NULL, NULL, '$2y$12$1r78DMeZmXrbkpQg8FQv3uPjiHWbA59sojmEQTfZbb9JF4thBq0UK', 11, 0, 0, 0, NULL, 13, NULL, '2026-07-11 10:27:46', '2026-07-12 04:54:10'),
(18, 84, 'مشتری احمد', 'ahmad', NULL, NULL, NULL, '$2y$12$cAWGI5c6E3dXApE.WwEemu7umece18I6nCe2/aqmpo.7txmTMm7CC', 10, 0, 0, 0, NULL, 13, NULL, '2026-07-12 05:06:01', '2026-07-12 05:06:01');

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `responsible` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `name`, `responsible`, `address`, `created_at`, `updated_at`) VALUES
(1, 'فروشگاه', 'محمود', 'امارات متحده عربی', '2025-02-09 12:16:25', '2026-06-19 06:46:04');

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_items`
--

CREATE TABLE `warehouse_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `buy_pre_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `in_amount` double DEFAULT NULL,
  `out_amount` double DEFAULT NULL,
  `available_amount` double NOT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `buy_up` double NOT NULL,
  `buy_tax_per` int(11) DEFAULT NULL,
  `buy_tax_price` double DEFAULT NULL,
  `buy_up_vat` double DEFAULT NULL,
  `total` double NOT NULL COMMENT 'with or without tax',
  `available_total` decimal(15,2) DEFAULT NULL,
  `sell_up` double DEFAULT NULL,
  `sell_tax_per` int(11) DEFAULT NULL,
  `sell_tax_price` double DEFAULT NULL,
  `sell_up_vat` double DEFAULT NULL,
  `currency_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL COMMENT 'user_id',
  `idate` varchar(30) NOT NULL,
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `day` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `times` int(11) DEFAULT NULL,
  `is_cleared` int(2) NOT NULL COMMENT '0: not cleared, 1:cleared'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `warehouse_items`
--

INSERT INTO `warehouse_items` (`id`, `warehouse_id`, `buy_pre_id`, `name`, `in_amount`, `out_amount`, `available_amount`, `unit_id`, `buy_up`, `buy_tax_per`, `buy_tax_price`, `buy_up_vat`, `total`, `available_total`, `sell_up`, `sell_tax_per`, `sell_tax_price`, `sell_up_vat`, `currency_id`, `category_id`, `user_id`, `idate`, `year`, `month`, `day`, `created_at`, `updated_at`, `times`, `is_cleared`) VALUES
(4, 1, 88, '', 5, 0, 5, 6, 500, 2, 50, 550, 2750, 2750.00, 550, 2, 55, 605, 1, NULL, 13, '2026-07-13', 2026, 7, 13, NULL, NULL, 1783954870, 0);

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_sales`
--

CREATE TABLE `warehouse_sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `billno` int(11) DEFAULT NULL,
  `factor` varchar(100) DEFAULT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `customer_account_id` bigint(20) UNSIGNED NOT NULL,
  `total` double(15,2) NOT NULL COMMENT 'with or without tax',
  `cur_pay` double NOT NULL,
  `remained` double NOT NULL,
  `currency_id` bigint(20) UNSIGNED NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `idate` varchar(100) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `day` int(11) DEFAULT NULL,
  `times` int(11) DEFAULT NULL,
  `has_invoice` tinyint(1) DEFAULT 0,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tax_activation` tinyint(4) DEFAULT 0,
  `is_cleared` int(6) NOT NULL COMMENT '0: not cleared, 1:cleared',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_wastage`
--

CREATE TABLE `warehouse_wastage` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_item_id` int(11) NOT NULL,
  `buy_pre_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `bought_up` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `currency_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `idate` varchar(255) DEFAULT NULL,
  `iby` varchar(255) DEFAULT NULL,
  `expired_date` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_metrics`
--
ALTER TABLE `access_metrics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `accounts_name_index` (`name`),
  ADD KEY `accounts_account_type_id_index` (`account_type_id`),
  ADD KEY `user_account_id_in_accounts` (`user_account_id`);

--
-- Indexes for table `account_types`
--
ALTER TABLE `account_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `backups`
--
ALTER TABLE `backups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bought_items`
--
ALTER TABLE `bought_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bought_items_times` (`times`),
  ADD KEY `idx_bought_items_invoice_id` (`invoice_id`);

--
-- Indexes for table `bought_item_details`
--
ALTER TABLE `bought_item_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bought_item_details_times` (`times`);

--
-- Indexes for table `bought_item_pre_lists`
--
ALTER TABLE `bought_item_pre_lists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`) USING BTREE;

--
-- Indexes for table `bought_returns`
--
ALTER TABLE `bought_returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bought_returns_bought_item_id_index` (`bought_item_id`),
  ADD KEY `bought_returns_bought_item_detail_id_index` (`bought_item_detail_id`),
  ADD KEY `bought_returns_billno_index` (`billno`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branches_name_unique` (`name`);

--
-- Indexes for table `buy_invoices`
--
ALTER TABLE `buy_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `buy_invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `buy_invoices_supplier_id_foreign` (`supplier_id`),
  ADD KEY `buy_invoices_currency_id_foreign` (`currency_id`);

--
-- Indexes for table `buy_invoice_items`
--
ALTER TABLE `buy_invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buy_invoice_items_invoice_id_foreign` (`invoice_id`),
  ADD KEY `buy_invoice_items_pre_list_id_foreign` (`pre_list_id`);

--
-- Indexes for table `buy_invoice_payments`
--
ALTER TABLE `buy_invoice_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buy_invoice_payments_invoice_id_foreign` (`invoice_id`),
  ADD KEY `buy_invoice_payments_account_id_foreign` (`account_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clearances`
--
ALTER TABLE `clearances`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_types`
--
ALTER TABLE `expense_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `income_types`
--
ALTER TABLE `income_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `journals`
--
ALTER TABLE `journals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `last_logins`
--
ALTER TABLE `last_logins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ltm_translations`
--
ALTER TABLE `ltm_translations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `org_bios`
--
ALTER TABLE `org_bios`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `rates`
--
ALTER TABLE `rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`roleId`);

--
-- Indexes for table `sales_details`
--
ALTER TABLE `sales_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_invoices`
--
ALTER TABLE `sales_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `sales_invoices_currency_id_foreign` (`currency_id`),
  ADD KEY `sales_invoices_customer_id_foreign` (`customer_id`) USING BTREE;

--
-- Indexes for table `sales_invoice_items`
--
ALTER TABLE `sales_invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_invoice_items_invoice_id_foreign` (`invoice_id`),
  ADD KEY `sales_invoice_items_pre_list_id_foreign` (`pre_list_id`);

--
-- Indexes for table `sales_invoice_payments`
--
ALTER TABLE `sales_invoice_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_invoice_payments_invoice_id_foreign` (`invoice_id`),
  ADD KEY `sales_invoice_payments_account_id_foreign` (`account_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `table_reset_passwords`
--
ALTER TABLE `table_reset_passwords`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `warehouse_items`
--
ALTER TABLE `warehouse_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_warehouse_items_times` (`times`);

--
-- Indexes for table `warehouse_sales`
--
ALTER TABLE `warehouse_sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `warehouse_wastage`
--
ALTER TABLE `warehouse_wastage`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_metrics`
--
ALTER TABLE `access_metrics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `account_types`
--
ALTER TABLE `account_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `backups`
--
ALTER TABLE `backups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `bought_items`
--
ALTER TABLE `bought_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bought_item_details`
--
ALTER TABLE `bought_item_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bought_item_pre_lists`
--
ALTER TABLE `bought_item_pre_lists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `bought_returns`
--
ALTER TABLE `bought_returns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `buy_invoices`
--
ALTER TABLE `buy_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `buy_invoice_items`
--
ALTER TABLE `buy_invoice_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `buy_invoice_payments`
--
ALTER TABLE `buy_invoice_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `clearances`
--
ALTER TABLE `clearances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `expense_types`
--
ALTER TABLE `expense_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `income_types`
--
ALTER TABLE `income_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journals`
--
ALTER TABLE `journals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `last_logins`
--
ALTER TABLE `last_logins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ltm_translations`
--
ALTER TABLE `ltm_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1538;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `org_bios`
--
ALTER TABLE `org_bios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rates`
--
ALTER TABLE `rates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `roleId` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `sales_details`
--
ALTER TABLE `sales_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sales_invoices`
--
ALTER TABLE `sales_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sales_invoice_items`
--
ALTER TABLE `sales_invoice_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `sales_invoice_payments`
--
ALTER TABLE `sales_invoice_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `table_reset_passwords`
--
ALTER TABLE `table_reset_passwords`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `warehouse_items`
--
ALTER TABLE `warehouse_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `warehouse_sales`
--
ALTER TABLE `warehouse_sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `warehouse_wastage`
--
ALTER TABLE `warehouse_wastage`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bought_returns`
--
ALTER TABLE `bought_returns`
  ADD CONSTRAINT `bought_returns_bought_item_detail_id_foreign` FOREIGN KEY (`bought_item_detail_id`) REFERENCES `bought_item_details` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bought_returns_bought_item_id_foreign` FOREIGN KEY (`bought_item_id`) REFERENCES `bought_items` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `buy_invoices`
--
ALTER TABLE `buy_invoices`
  ADD CONSTRAINT `buy_invoices_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
  ADD CONSTRAINT `buy_invoices_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `accounts` (`id`);

--
-- Constraints for table `buy_invoice_items`
--
ALTER TABLE `buy_invoice_items`
  ADD CONSTRAINT `buy_invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `buy_invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `buy_invoice_items_pre_list_id_foreign` FOREIGN KEY (`pre_list_id`) REFERENCES `bought_item_pre_lists` (`id`);

--
-- Constraints for table `buy_invoice_payments`
--
ALTER TABLE `buy_invoice_payments`
  ADD CONSTRAINT `buy_invoice_payments_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `buy_invoice_payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `buy_invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales_invoices`
--
ALTER TABLE `sales_invoices`
  ADD CONSTRAINT `sales_invoices_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
  ADD CONSTRAINT `sales_invoices_supplier_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `accounts` (`id`);

--
-- Constraints for table `sales_invoice_payments`
--
ALTER TABLE `sales_invoice_payments`
  ADD CONSTRAINT `sales_invoice_payments_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `sales_invoice_payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `sales_invoices` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
