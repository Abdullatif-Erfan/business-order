-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 05, 2026 at 01:18 PM
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
(18, '[{\"module\":\"dashboard\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"settings\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"journal\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"hr\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"order\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":1,\"edit_records\":1,\"delete_records\":0},{\"module\":\"buy\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":1,\"edit_records\":1,\"delete_records\":0},{\"module\":\"gudam\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"sales\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":1,\"edit_records\":1,\"delete_records\":0},{\"module\":\"expense\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":1,\"edit_records\":1,\"delete_records\":0},{\"module\":\"reports\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":0,\"delete_records\":0},{\"module\":\"users\",\"label\":0,\"total_access\":0,\"list\":1,\"create_records\":0,\"edit_records\":1,\"delete_records\":0},{\"module\":\"backup\",\"label\":0,\"total_access\":0,\"list\":0,\"create_records\":0,\"edit_records\":0,\"delete_records\":0}]', 11, 0, 13, '2026-07-24 12:36:55', 13, '2026-07-24 12:36:55', '2026-07-11 10:05:57', '2026-07-24 08:06:55');

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
(83, 3, NULL, 'مشتری خان - لبنات فروشی', NULL, 'kabul', NULL, 0, NULL, NULL, 1, NULL, 1, NULL, NULL, '2026-06-19 09:32:01', '2026-07-16 08:04:52'),
(84, 3, NULL, 'مشتری احمد', NULL, 'کابل', NULL, 0, NULL, NULL, 1, 6000, 1, NULL, NULL, '2026-06-20 06:57:15', '2026-07-23 13:51:53'),
(85, 3, NULL, 'مشتری محمود', NULL, 'کابل', NULL, 0, NULL, NULL, 1, 3000, 0, NULL, NULL, '2026-06-20 07:27:55', '2026-07-12 04:35:15'),
(86, 4, 0, 'تهیه کننده مواد غذایی خان', NULL, 'هرات', NULL, 0, NULL, NULL, 1, 8000, 1, NULL, NULL, '2026-06-20 07:41:38', '2026-06-20 08:00:30'),
(87, 2, NULL, 'کارمند عرفان', NULL, 'هرات', NULL, 0, NULL, 5000, 1, NULL, 0, 21, '2026-10-10', '2026-06-20 08:57:40', '2026-07-28 08:02:25'),
(88, 6, 0, 'شبکه نهله', NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, 0, NULL, NULL, '2026-06-20 08:59:05', '2026-06-20 08:59:05'),
(89, 6, 0, 'شبکه حاجی عوض', NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, 0, NULL, NULL, '2026-06-20 08:59:19', '2026-06-20 08:59:19'),
(90, 6, 0, 'صرافی احمدیان', NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, 0, NULL, NULL, '2026-06-20 08:59:39', '2026-06-20 08:59:39'),
(91, 4, 0, 'احمدی تهیه کننده سبزیجات', NULL, 'kabul', NULL, 0, NULL, NULL, 1, 6000, 1, NULL, NULL, '2026-06-22 14:34:51', '2026-06-22 14:34:51'),
(92, 2, 18, 'کریم درایور مازدا', NULL, 'کابل', NULL, 0, NULL, 8000, 1, NULL, 0, 20, '2026-06-06', '2026-06-22 14:35:36', '2026-07-23 13:51:53'),
(94, 3, 0, 'مشتری قادر سبزی فروش موفق', NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, 0, NULL, NULL, '2026-07-14 15:21:45', '2026-07-14 17:39:48'),
(95, 4, 0, 'رحمت - نوشیدنی ها', NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, 0, NULL, NULL, '2026-07-14 15:22:39', '2026-07-14 17:09:19'),
(96, 4, 0, 'قمبر - خوراکه فروشی توفیق', NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, 0, NULL, NULL, '2026-07-14 15:23:38', '2026-07-14 15:23:38'),
(97, 3, 0, 'مشتری سبزی فروش بهار', NULL, NULL, NULL, 0, NULL, NULL, 1, 60000, 1, NULL, NULL, '2026-07-14 15:24:32', '2026-07-14 15:24:32'),
(98, 3, 0, 'مشتری الکوزی نوشیدنی باب', NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, 0, NULL, NULL, '2026-07-14 15:25:08', '2026-07-14 15:25:08'),
(99, 4, 0, 'سهراب - تهیه کننده سبزیجات', NULL, NULL, NULL, 0, NULL, NULL, 1, 60000, 1, NULL, NULL, '2026-07-14 17:07:23', '2026-07-14 17:07:23'),
(100, 2, 19, 'کارمند خان علی', NULL, NULL, NULL, 0, NULL, 5000, 1, NULL, 0, 19, '2026-07-14', '2026-07-15 04:27:44', '2026-07-28 07:48:03'),
(103, 7, 0, 'حساب موتر 1150', NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, 0, 19, NULL, '2026-07-20 15:01:33', '2026-07-20 15:08:24'),
(104, 7, 0, 'حساب موتر 1120', NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, 0, 20, NULL, '2026-07-20 15:13:39', '2026-07-20 15:13:39'),
(105, 7, 0, 'حساب موتر 1290', NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, 0, 21, NULL, '2026-07-20 15:14:05', '2026-07-20 15:14:05'),
(106, 7, 0, 'حساب موتر 1560', NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, 0, 22, NULL, '2026-07-20 15:14:24', '2026-07-20 15:14:24');

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
(6, 'صرافی و بانک', 0, NULL, NULL),
(7, 'موتر', 0, NULL, NULL);

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
(66, 'بک اپ تمیز', 'db-2026-07-28_11-58-56.sql', '/storage/backups/db-2026-07-28_11-58-56.sql', 1785223736, '2026-07-28 11:58:56', 'ادمین عمومی', '2026-07-28 07:28:56', '2026-07-28 07:28:56'),
(67, 'بک اپ تمیز', 'db-2026-07-29_11-24-16.sql', '/storage/backups/db-2026-07-29_11-24-16.sql', 1785308057, '2026-07-29 11:24:17', 'ادمین عمومی', '2026-07-29 06:54:17', '2026-07-29 06:54:17'),
(68, 'befor testing buy invoice', 'db-2026-07-29_15-58-29.sql', '/storage/backups/db-2026-07-29_15-58-29.sql', 1785324509, '2026-07-29 15:58:29', 'ادمین عمومی', '2026-07-29 11:28:29', '2026-07-29 11:28:29'),
(69, 'قبل از ایجاد انوایس جدید فروشات', 'db-2026-08-03_18-55-48.sql', '/storage/backups/db-2026-08-03_18-55-48.sql', 1785767150, '2026-08-03 18:55:50', 'ادمین عمومی', '2026-08-03 14:25:50', '2026-08-03 14:25:50');

-- --------------------------------------------------------

--
-- Table structure for table `bought_bill_payments`
--

CREATE TABLE `bought_bill_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bought_item_id` bigint(20) UNSIGNED NOT NULL,
  `billno` varchar(255) NOT NULL,
  `supplier_account_id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `currency_id` bigint(20) UNSIGNED NOT NULL,
  `cur_pay` decimal(15,2) NOT NULL,
  `remained` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_date` date NOT NULL,
  `note` text DEFAULT NULL,
  `journal_code` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `times` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bought_bill_payments`
--

INSERT INTO `bought_bill_payments` (`id`, `bought_item_id`, `billno`, `supplier_account_id`, `account_id`, `currency_id`, `cur_pay`, `remained`, `payment_date`, `note`, `journal_code`, `user_id`, `user_name`, `times`, `created_at`, `updated_at`) VALUES
(1, 6, '6', 91, 33, 1, 10.00, 150.00, '2026-08-04', 'dasdasd', 24, 13, 'ادمین عمومی', 1785845573, '2026-08-04 12:12:53', '2026-08-04 12:12:53'),
(2, 6, '6', 91, 33, 1, 20.00, 130.00, '2026-08-04', NULL, 25, 13, 'ادمین عمومی', 1785846306, '2026-08-04 12:25:06', '2026-08-04 12:25:06');

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
  `car_id` bigint(20) UNSIGNED NOT NULL,
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
  `isEditable` tinyint(4) DEFAULT 0 COMMENT '0: editable, 1: not editable',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bought_items`
--

INSERT INTO `bought_items` (`id`, `billno`, `factor`, `journal_code`, `category_id`, `total`, `cur_pay`, `remained`, `account_id`, `supplier_account_id`, `currency_id`, `car_id`, `tax_activation`, `note`, `idate`, `year`, `month`, `day`, `times`, `has_invoice`, `invoice_id`, `user_id`, `user_name`, `isEditable`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 1, NULL, 260.00, 260.00, 0.00, 33, 86, 1, 20, 1, '', '2026-07-29', 2026, 7, 29, '1785309011', 1, 6, 13, 'ادمین عمومی', 1, '2026-07-29 07:10:11', '2026-08-03 05:32:43'),
(2, 2, NULL, 2, NULL, 200.00, 0.00, 200.00, 33, 95, 1, 20, 0, '', '2026-07-29', 2026, 7, 29, '1785309072', 1, 5, 13, 'ادمین عمومی', 1, '2026-07-29 07:11:12', '2026-08-01 15:24:53'),
(3, 3, NULL, 4, NULL, 14169.60, 0.00, 14169.60, 33, 86, 1, 20, 1, NULL, '2026-08-01', 2026, 8, 1, '1785558500', 0, NULL, 13, 'ادمین عمومی', 1, '2026-08-01 04:28:20', '2026-08-01 13:47:32'),
(4, 4, NULL, 5, NULL, 65.00, 0.00, 65.00, 33, 95, 1, 20, 1, NULL, '2026-08-01', 2026, 8, 1, '1785558528', 0, NULL, 13, 'ادمین عمومی', 1, '2026-08-01 04:28:48', '2026-08-01 13:38:12'),
(5, 5, NULL, 6, NULL, 195.00, 0.00, 195.00, 33, 95, 1, 20, 1, NULL, '2026-08-01', 2026, 8, 1, '1785558583', 0, NULL, 13, 'ادمین عمومی', 1, '2026-08-01 04:29:43', '2026-08-01 13:03:30'),
(6, 6, NULL, 13, NULL, 160.00, 30.00, 130.00, 33, 91, 1, 20, 1, NULL, '2026-08-03', 2026, 8, 3, '1785761011', 0, NULL, 13, 'ادمین عمومی', 1, '2026-08-03 12:43:31', '2026-08-04 12:25:06'),
(7, 7, NULL, 26, NULL, 200.00, 0.00, 200.00, 33, 86, 1, 20, 0, NULL, '2026-08-04', 2026, 8, 4, '1785847552', 0, NULL, 13, 'ادمین عمومی', 0, '2026-08-04 12:45:52', '2026-08-04 12:45:52');

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
  `expected_profit` double DEFAULT NULL,
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

INSERT INTO `bought_item_details` (`id`, `billno`, `bought_item_id`, `pre_list_id`, `category_id`, `supplier_account_id`, `amount`, `unit_id`, `buy_up`, `buy_tax_per`, `buy_tax_price`, `buy_up_vat`, `total`, `expected_profit`, `total_vat`, `sell_up`, `sell_tax_per`, `sell_tax_price`, `sell_up_vat`, `is_moved`, `times`, `user_id`, `user_name`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 85, 1, 86, 5, 7, 40, 6, 12, 52, 200, 10, 260, 50, 6, 15, 65, 0, '1785309011', 13, 'ادمین عمومی', '2026-07-29 07:10:11', '2026-07-29 13:30:55'),
(2, 2, 2, 92, 4, 95, 5, 3, 40, 0, 0, 0, 200, 10, 0, 50, 0, 0, 0, 0, '1785309072', 13, 'ادمین عمومی', '2026-07-29 07:11:12', '2026-07-29 11:18:50'),
(3, 3, 3, 88, 3, 86, 9, 6, 1000, 6, 540, 1540, 9000, 100, 13860, 1100, 6, 594, 1694, 0, '1785558500', 13, 'ادمین عمومی', '2026-08-01 04:28:20', '2026-08-01 04:28:20'),
(4, 3, 3, 89, 3, 86, 5, 6, 40, 6, 12, 52, 200, 5, 260, 45, 6, 13.5, 58.5, 0, '1785558500', 13, 'ادمین عمومی', '2026-08-01 04:28:20', '2026-08-01 04:28:20'),
(5, 3, 3, 95, 2, 86, 4, 18, 10, 6, 2.4, 12.4, 40, 3, 49.6, 13, 6, 3.12, 16.12, 0, '1785558500', 13, 'ادمین عمومی', '2026-08-01 04:28:20', '2026-08-01 04:28:20'),
(6, 4, 4, 90, 4, 95, 5, 18, 10, 6, 3, 13, 50, 3, 65, 13, 6, 3.9, 16.9, 0, '1785558528', 13, 'ادمین عمومی', '2026-08-01 04:28:48', '2026-08-01 04:28:48'),
(7, 5, 5, 112, 1, 95, 5, 7, 30, 6, 9, 39, 150, 5, 195, 35, 6, 10.5, 45.5, 0, '1785558583', 13, 'ادمین عمومی', '2026-08-01 04:29:43', '2026-08-01 04:29:43'),
(8, 6, 6, 94, 2, 91, 10, 18, 10, 6, 6, 16, 100, 2, 160, 12, 6, 7.2, 19.2, 0, '1785761011', 13, 'ادمین عمومی', '2026-08-03 12:43:31', '2026-08-03 12:54:29'),
(9, 7, 7, 85, 1, 86, 4, 7, 50, 0, 0, 0, 200, 20, 0, 70, 0, 0, 0, 0, '1785847552', 13, 'ادمین عمومی', '2026-08-04 12:45:52', '2026-08-04 12:45:52');

-- --------------------------------------------------------

--
-- Table structure for table `bought_item_pre_lists`
--

CREATE TABLE `bought_item_pre_lists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL COMMENT 'account_id',
  `name` varchar(255) NOT NULL,
  `unit_id` bigint(20) DEFAULT NULL COMMENT 'default unit_id',
  `unit_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bought_item_pre_lists`
--

INSERT INTO `bought_item_pre_lists` (`id`, `category_id`, `supplier_id`, `name`, `unit_id`, `unit_name`, `created_at`, `updated_at`) VALUES
(85, 1, 96, 'کیله', 7, 'کیلو', '2026-06-22 07:19:10', '2026-07-20 08:15:53'),
(86, 1, 96, 'خربوزه', 3, 'دانه', '2026-06-22 07:19:19', '2026-07-20 07:23:56'),
(87, 3, 86, 'نخود پلنگی', 8, 'سیر', '2026-06-22 07:19:24', '2026-07-20 08:15:43'),
(88, 3, 86, 'آرد گندم', 6, 'بوجی', '2026-06-22 07:19:34', '2026-07-20 08:15:37'),
(89, 3, 86, 'برنج', 6, 'بوجی', '2026-06-22 07:19:44', '2026-07-20 07:23:49'),
(90, 4, 95, 'نوشابه الکوزی خورد', 18, 'بسته', '2026-06-22 07:19:58', '2026-07-20 08:15:31'),
(91, 4, 95, 'نوشابه کوکاکولا کلان', 3, 'دانه', '2026-06-22 07:20:08', '2026-07-20 07:23:42'),
(92, 4, 95, 'جنسینگ', 3, 'دانه', '2026-06-22 07:20:20', '2026-07-20 08:15:23'),
(93, 2, 99, 'کاهو', 7, 'کیلو', '2026-06-22 07:20:34', '2026-07-20 07:23:31'),
(94, 2, 99, 'گشنیز', 18, 'بسته', '2026-06-22 07:20:38', '2026-07-20 07:23:23'),
(95, 2, 99, 'ملی سرخک', 18, 'بسته', '2026-06-22 07:20:46', '2026-07-20 07:23:16'),
(112, 1, 96, 'انگور', 7, 'کیلو', '2026-07-25 12:57:23', '2026-07-25 12:57:23');

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

--
-- Dumping data for table `buy_invoices`
--

INSERT INTO `buy_invoices` (`id`, `invoice_number`, `supplier_id`, `total`, `paid_amount`, `remaining`, `currency_id`, `tax_activation`, `status`, `invoice_date`, `due_date`, `notes`, `created_by`, `times`, `created_at`, `updated_at`) VALUES
(5, 'INV-20260729-1', 95, 200.00, 0.00, 200.00, 1, 0, 1, '2026-07-29', '2026-08-28', 'انوایس توسط سیستم اتومات ایجاد گردید', 13, '1785324876', '2026-07-29 11:34:36', '2026-07-29 11:34:36'),
(6, 'INV-20260729-2', 86, 260.00, 260.00, 0.00, 1, 1, 3, '2026-07-29', '2026-08-28', 'انوایس توسط سیستم اتومات ایجاد گردید', 13, '1785325303', '2026-07-29 11:41:43', '2026-07-29 11:42:36');

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

--
-- Dumping data for table `buy_invoice_items`
--

INSERT INTO `buy_invoice_items` (`id`, `invoice_id`, `bought_item_detail_id`, `bought_item_id`, `pre_list_id`, `amount`, `unit_id`, `unit_price`, `unit_price_vat`, `tax_percentage`, `tax_amount`, `buy_up_vat`, `total`, `total_vat`, `times`, `created_at`, `updated_at`) VALUES
(7, 5, 2, 2, 92, 5.00, 3, 40.00, 0.00, 0, 0.00, 0.00, 200.00, 0.00, '1785324876', '2026-07-29 11:34:36', '2026-07-29 11:34:36'),
(8, 6, 1, 1, 85, 5.00, 7, 40.00, 52.00, 6, 12.00, 52.00, 200.00, 260.00, '1785325303', '2026-07-29 11:41:43', '2026-07-29 11:41:43');

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

--
-- Dumping data for table `buy_invoice_payments`
--

INSERT INTO `buy_invoice_payments` (`id`, `invoice_id`, `payment_date`, `amount`, `payment_method`, `account_id`, `supplier_account_id`, `reference_number`, `notes`, `created_by`, `times`, `created_at`, `updated_at`) VALUES
(5, 6, '2026-07-29', 260.00, 1, 33, 86, NULL, NULL, 13, '1785325356', '2026-07-29 11:42:36', '2026-07-29 11:42:36');

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
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `supplier_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 96, 'میوه جات', '2026-06-19 07:02:20', '2026-07-16 07:44:29'),
(2, 99, 'سبزی جات', '2026-06-19 07:02:20', '2026-07-16 07:42:47'),
(3, 86, 'خوراکه باب', '2026-06-19 08:38:38', '2026-07-16 07:42:37'),
(4, 95, 'نوشیدنی ها', '2026-06-19 08:38:48', '2026-07-16 07:42:29');

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
-- Table structure for table `draft_orders`
--

CREATE TABLE `draft_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dord_num` bigint(20) NOT NULL COMMENT 'Draft Order Auto Number',
  `customer_id` bigint(20) UNSIGNED NOT NULL COMMENT 'customer_id = (account_id)',
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `pre_list_id` bigint(20) UNSIGNED NOT NULL,
  `amount` double NOT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `iby` bigint(20) UNSIGNED NOT NULL COMMENT 'Inserted By',
  `idate` varchar(255) NOT NULL COMMENT 'Inserted Date',
  `user_name` varchar(255) DEFAULT NULL,
  `state` int(11) NOT NULL DEFAULT 1 COMMENT '1:new, 2:progress, 3:completed, 4: cancelled ',
  `times` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `draft_orders`
--

INSERT INTO `draft_orders` (`id`, `dord_num`, `customer_id`, `category_id`, `pre_list_id`, `amount`, `unit_id`, `iby`, `idate`, `user_name`, `state`, `times`, `created_at`, `updated_at`) VALUES
(70, 1, 83, 3, 88, 4, 6, 13, '2026-08-01', 'ادمین عمومی', 3, '1785558284', '2026-08-01 04:24:44', '2026-08-01 13:03:30'),
(71, 1, 83, 3, 89, 4, 6, 13, '2026-08-01', 'ادمین عمومی', 3, '1785558284', '2026-08-01 04:24:44', '2026-08-01 13:03:30'),
(72, 1, 83, 1, 86, 4, 3, 13, '2026-08-01', 'ادمین عمومی', 3, '1785558284', '2026-08-01 04:24:44', '2026-08-01 13:03:30'),
(73, 2, 85, 3, 88, 5, 6, 13, '2026-08-01', 'ادمین عمومی', 3, '1785558316', '2026-08-01 04:25:16', '2026-08-01 15:24:53'),
(74, 2, 85, 4, 90, 5, 18, 13, '2026-08-01', 'ادمین عمومی', 3, '1785558316', '2026-08-01 04:25:16', '2026-08-01 15:24:53'),
(75, 2, 85, 2, 93, 5, 7, 13, '2026-08-01', 'ادمین عمومی', 3, '1785558316', '2026-08-01 04:25:16', '2026-08-01 15:24:53');

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
  `is_single_record` int(11) DEFAULT 0 COMMENT '0:single, 1:pair',
  `belongsToMe` int(11) DEFAULT 0 COMMENT '0: object 1:subject (my record)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(42, '2026_06_24_180653_add_has_invoice_column_to_bought_items_for_tracking', 13),
(43, '2026_07_08_165811_create_bought_returns_table', 14),
(44, '2026_07_16_124612_add_draft_orders_table', 15),
(45, '2026_07_17_161252_add_order_items_table', 16),
(46, '2026_07_22_075334_add_sales_bill_payment_table', 17),
(47, '2026_07_27_155803_add_warehouse_returns_table', 18),
(48, '2026_08_04_103854_create_bought_bill_payments_table', 19);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ord_num` varchar(255) DEFAULT NULL COMMENT 'system auto number',
  `bill_no` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Bought_items bill number',
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'supplier_id = (account_id)',
  `car_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `idate` varchar(255) NOT NULL COMMENT 'Inserted Date',
  `state` int(11) NOT NULL DEFAULT 1 COMMENT '1:new, 2:progress, 3:completed, 4: cancelled ',
  `times` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `ord_num`, `bill_no`, `category_id`, `supplier_id`, `car_id`, `user_id`, `user_name`, `idate`, `state`, `times`, `created_at`, `updated_at`) VALUES
(139, 'ORD-2026-08-0001', 5, 1, 96, 19, 13, 'ادمین عمومی', '2026-08-01', 3, '1785558348', '2026-08-01 04:25:48', '2026-08-01 04:29:43'),
(140, 'ORD-2026-08-0002', 6, 2, 99, 19, 13, 'ادمین عمومی', '2026-08-01', 3, '1785558348', '2026-08-01 04:25:48', '2026-08-03 12:43:31'),
(141, 'ORD-2026-08-0003', 3, 3, 86, 19, 13, 'ادمین عمومی', '2026-08-01', 3, '1785558348', '2026-08-01 04:25:48', '2026-08-01 04:28:20'),
(142, 'ORD-2026-08-0004', 4, 4, 95, 19, 13, 'ادمین عمومی', '2026-08-01', 3, '1785558348', '2026-08-01 04:25:48', '2026-08-01 04:28:48');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `pre_list_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `pre_list_id`, `category_id`, `unit_id`, `amount`, `created_at`, `updated_at`) VALUES
(123, 139, 86, 1, 3, 4.00, '2026-08-01 04:25:48', '2026-08-01 04:25:48'),
(124, 140, 93, 2, 7, 5.00, '2026-08-01 04:25:48', '2026-08-01 04:25:48'),
(125, 141, 88, 3, 6, 9.00, '2026-08-01 04:25:48', '2026-08-01 04:25:48'),
(126, 141, 89, 3, 6, 4.00, '2026-08-01 04:25:48', '2026-08-01 04:25:48'),
(127, 142, 90, 4, 18, 5.00, '2026-08-01 04:25:48', '2026-08-01 04:25:48');

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
(2, 'سیستم مدیریتی سفارشات آسان ارسال', 'مرکز تجارتی داودزی - کابل - افغانستان', '0729010123', 'headers/1783682766_header.jpg', 'logos/1783675724_sm-logo.jpeg', 0, 6, 1, 'اجناس فروخته شده پس گرفته نمیشود. و قرض هم داده نمیشود', 0, '2027-04-12', NULL, '2026-08-04 12:45:11');

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
(11, 'کارمندان / راننده گان', 1, 0, 13, '2026-07-11 10:05:48', '2026-07-23 13:37:31');

-- --------------------------------------------------------

--
-- Table structure for table `sales_bill_payments`
--

CREATE TABLE `sales_bill_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_sales_id` bigint(20) UNSIGNED NOT NULL,
  `billno` varchar(255) NOT NULL,
  `customer_account_id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `currency_id` bigint(20) UNSIGNED NOT NULL,
  `cur_pay` decimal(15,2) NOT NULL,
  `remained` decimal(15,2) NOT NULL,
  `payment_date` date NOT NULL,
  `note` text DEFAULT NULL,
  `journal_code` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `times` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_bill_payments`
--

INSERT INTO `sales_bill_payments` (`id`, `warehouse_sales_id`, `billno`, `customer_account_id`, `account_id`, `currency_id`, `cur_pay`, `remained`, `payment_date`, `note`, `journal_code`, `user_id`, `user_name`, `times`, `created_at`, `updated_at`) VALUES
(18, 5, '2', 83, 33, 1, 5.00, 11.32, '2026-08-01', 'پرداخت نقد فروش', '8', 13, 'ادمین عمومی', 1785591492, '2026-08-01 13:38:12', '2026-08-01 13:38:12'),
(19, 7, '4', 85, 33, 1, 44.00, 140.00, '2026-08-01', 'پرداخت نقد فروش', '10', 13, 'ادمین عمومی', 1785597893, '2026-08-01 15:24:53', '2026-08-03 12:37:25'),
(20, 8, '5', 94, 33, 1, 15.40, 50.00, '2026-08-03', 'پرداخت نقد فروش', '11', 13, 'ادمین عمومی', 1785735162, '2026-08-03 05:32:43', '2026-08-03 05:32:43'),
(21, 9, '6', 84, 33, 1, 673.60, 3000.00, '2026-08-03', NULL, '15', 13, 'ادمین عمومی', 1785763295, '2026-08-03 13:21:35', '2026-08-03 13:21:35'),
(22, 7, '4', 85, 33, 1, 40.00, 100.00, '2026-08-03', NULL, '16', 13, 'ادمین عمومی', 1785766743, '2026-08-03 14:19:03', '2026-08-03 14:19:03'),
(23, 9, '6', 84, 33, 1, 500.00, 2500.00, '2026-08-03', NULL, '17', 13, 'ادمین عمومی', 1785767001, '2026-08-03 14:23:21', '2026-08-03 14:23:21');

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
  `expected_profit` decimal(15,2) NOT NULL,
  `profit` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `is_returned` int(11) NOT NULL DEFAULT 0 COMMENT '0:not returned, 1:returned',
  `todays_date` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_details`
--

INSERT INTO `sales_details` (`id`, `billno`, `category_id`, `warehouse_id`, `warehouse_sales_id`, `pre_list_id`, `unit_id`, `amount`, `buy_up`, `sell_up`, `sell_up_no_tax`, `sell_tax_per`, `sell_tax_price`, `expected_profit`, `profit`, `total`, `is_returned`, `todays_date`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 1, 4, 89, 6, 4, 52, 69.44, 56, 6, 13.44, 4.00, 16, 224, 0, '2026-08-01', '2026-08-01 13:03:30', '2026-08-01 13:03:30'),
(2, 1, 3, 1, 4, 88, 6, 4, 1540, 1915.8, 1545, 6, 370.80, 5.00, 20, 6180, 0, '2026-08-01', '2026-08-01 13:03:30', '2026-08-01 13:03:30'),
(3, 2, 2, 1, 5, 95, 18, 1, 12.4, 16.32, 15, 6, 0.92, 3.00, 3, 15.4, 0, '2026-08-01', '2026-08-01 13:38:12', '2026-08-01 13:38:12'),
(4, 3, 4, 1, 6, 90, 18, 1, 13, 15.9, 15, 6, 0.90, 2.00, 2, 15.9, 0, '2026-08-01', '2026-08-01 13:47:32', '2026-08-01 13:47:32'),
(5, 4, 4, 1, 7, 90, 18, 4, 13, 16, 16, 0, 0.00, 3.00, 12, 64, 0, '2026-08-01', '2026-08-01 15:24:53', '2026-08-01 15:24:53'),
(6, 4, 1, 1, 7, 112, 7, 3, 39, 40, 40, 0, 0.00, 1.00, 3, 120, 0, '2026-08-01', '2026-08-01 15:24:53', '2026-08-03 12:37:20'),
(7, 5, 2, 1, 8, 95, 18, 1, 12.4, 15.4, 15, 0, 0.00, 3.00, 3, 15.4, 0, '2026-08-03', '2026-08-03 05:32:43', '2026-08-03 05:32:43'),
(8, 5, 1, 1, 8, 112, 7, 1, 39, 50, 50, 0, 0.00, 11.00, 11, 50, 0, '2026-08-03', '2026-08-03 05:32:43', '2026-08-03 05:32:43'),
(9, 6, 3, 1, 9, 88, 6, 2, 1540, 1836.8, 1640, 6, 196.80, 100.00, 593.6, 3673.6, 0, '2026-08-03', '2026-08-03 05:54:45', '2026-08-03 12:53:10'),
(10, 7, 2, 1, 10, 94, 18, 3, 16, 23.6, 20, 6, 3.60, 4.00, 12, 70.8, 0, '2026-08-03', '2026-08-03 12:55:35', '2026-08-03 12:55:35');

-- --------------------------------------------------------

--
-- Table structure for table `sales_invoices`
--

CREATE TABLE `sales_invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `sales_bill_numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sales_bill_numbers`)),
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

--
-- Dumping data for table `sales_invoices`
--

INSERT INTO `sales_invoices` (`id`, `invoice_number`, `sales_bill_numbers`, `customer_id`, `total`, `paid_amount`, `remaining`, `currency_id`, `tax_activation`, `status`, `invoice_date`, `due_date`, `notes`, `created_by`, `times`, `created_at`, `updated_at`) VALUES
(12, 'SINV-20260803-1', '\"[5]\"', 94, 65.40, 35.40, 30.00, 1, 0, 2, '2026-08-03', '2026-09-02', 'انوایس توسط سیستم اتومات ایجاد گردید', 13, '1785735627', '2026-08-03 05:40:28', '2026-08-04 05:23:09'),
(16, 'SINV-20260804-2', '\"[3,6]\"', 84, 3689.50, 3689.50, 0.00, 1, 1, 3, '2026-08-04', '2026-09-03', 'انوایس توسط سیستم اتومات ایجاد گردید', 13, '1785818893', '2026-08-04 04:48:13', '2026-08-04 04:57:09');

-- --------------------------------------------------------

--
-- Table structure for table `sales_invoice_items`
--

CREATE TABLE `sales_invoice_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `billno` bigint(20) NOT NULL COMMENT 'Sales Bill No',
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `total` decimal(15,2) DEFAULT 0.00,
  `cur_pay` decimal(15,2) DEFAULT 0.00,
  `remained` decimal(15,2) NOT NULL,
  `times` varchar(255) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `user_name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_invoice_items`
--

INSERT INTO `sales_invoice_items` (`id`, `billno`, `invoice_id`, `total`, `cur_pay`, `remained`, `times`, `invoice_date`, `user_name`, `created_at`, `updated_at`) VALUES
(43, 3, 16, 15.90, 0.00, 15.90, '1785818893', '2026-08-04', 'ادمین عمومی', '2026-08-04 04:48:13', '2026-08-04 04:48:13'),
(44, 6, 16, 3673.60, 1173.60, 2500.00, '1785818893', '2026-08-04', 'ادمین عمومی', '2026-08-04 04:48:13', '2026-08-04 04:48:13');

-- --------------------------------------------------------

--
-- Table structure for table `sales_invoice_payments`
--

CREATE TABLE `sales_invoice_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `journal_code` bigint(20) UNSIGNED NOT NULL,
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

--
-- Dumping data for table `sales_invoice_payments`
--

INSERT INTO `sales_invoice_payments` (`id`, `invoice_id`, `journal_code`, `payment_date`, `amount`, `payment_method`, `account_id`, `customer_account_id`, `reference_number`, `notes`, `created_by`, `times`, `created_at`, `updated_at`) VALUES
(23, 16, 0, '2026-08-04', 10.00, 1, 33, 84, NULL, NULL, 13, '1785818968', '2026-08-04 04:49:28', '2026-08-04 04:49:28'),
(24, 16, 0, '2026-08-04', 10.00, 1, 33, 84, NULL, NULL, 13, '1785819290', '2026-08-04 04:54:50', '2026-08-04 04:54:50'),
(25, 16, 0, '2026-08-04', 2494.00, 1, 33, 84, NULL, NULL, 13, '1785819402', '2026-08-04 04:56:42', '2026-08-04 04:56:42'),
(26, 16, 0, '2026-08-04', 1.90, 1, 33, 84, NULL, NULL, 13, '1785819429', '2026-08-04 04:57:09', '2026-08-04 04:57:09'),
(27, 12, 22, '2026-08-04', 10.00, 1, 33, 94, NULL, NULL, 13, '1785820941', '2026-08-04 05:22:21', '2026-08-04 05:22:21'),
(28, 12, 23, '2026-08-04', 10.00, 2, 33, 94, NULL, NULL, 13, '1785820989', '2026-08-04 05:23:09', '2026-08-04 05:23:09');

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
  `id` bigint(20) UNSIGNED NOT NULL,
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
  `car_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`car_ids`)),
  `customer_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`customer_ids`)),
  `createdBy` int(11) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `account_id`, `full_name`, `user_name`, `email`, `username`, `email_verified_at`, `password`, `roleId`, `isAdmin`, `isDeleted`, `isHidden`, `photo`, `car_ids`, `customer_ids`, `createdBy`, `remember_token`, `created_at`, `updated_at`) VALUES
(13, 0, 'ادمین عمومی', 'erfan', 'abdullatif.erfan@gmail.com', NULL, NULL, '$2y$12$uP/I5.H3FOetkMv3BxItyObFevcS59e683dVSa/ftBj9PEWz2qCqm', 2, 1, 0, 1, 'user_photos/rxLaGsTzamy2iFo10rXZizlIIAEjuTSiIHks2Hbr.jpg', NULL, NULL, 1, NULL, '2025-03-19 03:44:39', '2026-07-10 10:21:23'),
(18, 92, 'کریم دریور مازدا', 'karim', 'karim@gmail.com', NULL, NULL, '$2y$12$9GELXqCwnYefLyAfYwdm2.gMKQHP5e.Bfo84vPGBozrlEm3zJp5si', 11, 0, 0, 0, 'user_photos/L98dzg9W19La4zj0msiDfyTEG2rDMtdZeGyS6lGn.png', '[\"21\",\"22\"]', '[\"94\",\"97\",\"98\"]', 13, NULL, '2026-07-12 05:06:01', '2026-07-27 08:05:10'),
(19, 100, 'khan ali', 'khanali', NULL, NULL, NULL, '$2y$12$iXx57uo1XZqbveWMWaTKO.ZSxBQD946JZc2ZbNWx5boxNSnFqj/oO', 11, 0, 0, 0, 'user_photos/16fGC03WjNQDvVzIwg5mTdbY76zPWiRUZ7beXbUX.png', '[\"20\",\"19\"]', '[\"84\",\"85\"]', 13, NULL, '2026-07-23 11:49:10', '2026-07-23 13:38:15');

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
  `billno` bigint(20) NOT NULL COMMENT 'belongs to billno is bought_item_details',
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
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `car_id` bigint(20) UNSIGNED NOT NULL,
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

INSERT INTO `warehouse_items` (`id`, `billno`, `warehouse_id`, `buy_pre_id`, `name`, `in_amount`, `out_amount`, `available_amount`, `unit_id`, `buy_up`, `buy_tax_per`, `buy_tax_price`, `buy_up_vat`, `total`, `available_total`, `sell_up`, `sell_tax_per`, `sell_tax_price`, `sell_up_vat`, `currency_id`, `category_id`, `supplier_id`, `car_id`, `user_id`, `idate`, `year`, `month`, `day`, `created_at`, `updated_at`, `times`, `is_cleared`) VALUES
(1, 1, 1, 85, NULL, 6, 0, 6, 7, 50, 0, 0, 0, 300, 300.00, 70, 0, 0, 0, 1, 1, 86, 20, 13, '2026-08-04', 2026, 8, 4, '2026-07-29 07:10:11', '2026-08-04 12:45:52', NULL, 0),
(2, 2, 1, 92, NULL, 0, 0, 0, 3, 40, 0, 0, 0, 0, 0.00, 50, 0, 0, 0, 1, 4, 95, 20, 13, '2026-07-29', 2026, 7, 29, '2026-07-29 07:11:12', '2026-07-29 14:34:01', NULL, 0),
(3, 1, 1, 85, NULL, 0, 0, 0, 7, 40, 6, 12, 52, 0, 0.00, 50, 6, 15, 65, 1, 1, 86, 19, 13, '2026-07-29', 2026, 7, 29, '2026-07-29 14:12:40', '2026-07-29 14:25:17', NULL, 0),
(4, 3, 1, 88, NULL, 9, 6, 3, 6, 1000, 6, 540, 1540, 13860, 4620.00, 1100, 6, 594, 1694, 1, 3, 86, 20, 13, '2026-08-01', 2026, 8, 1, '2026-08-01 04:28:20', '2026-08-03 12:53:10', NULL, 0),
(5, 3, 1, 89, NULL, 5, 4, 1, 6, 40, 6, 12, 52, 260, 52.00, 45, 6, 13.5, 58.5, 1, 3, 86, 20, 13, '2026-08-01', 2026, 8, 1, '2026-08-01 04:28:20', '2026-08-01 13:03:30', NULL, 0),
(6, 3, 1, 95, NULL, 4, 2, 2, 18, 10, 6, 2.4, 12.4, 49.6, 24.80, 13, 6, 3.12, 16.12, 1, 2, 86, 20, 13, '2026-08-01', 2026, 8, 1, '2026-08-01 04:28:20', '2026-08-03 05:32:43', NULL, 0),
(7, 4, 1, 90, NULL, 5, 5, 0, 18, 10, 6, 3, 13, 65, 0.00, 13, 6, 3.9, 16.9, 1, 4, 95, 20, 13, '2026-08-01', 2026, 8, 1, '2026-08-01 04:28:48', '2026-08-01 15:24:53', NULL, 0),
(8, 5, 1, 112, NULL, 5, 4, 1, 7, 30, 6, 9, 39, 195, 39.00, 35, 6, 10.5, 45.5, 1, 1, 95, 20, 13, '2026-08-01', 2026, 8, 1, '2026-08-01 04:29:43', '2026-08-03 12:37:20', NULL, 0),
(9, 6, 1, 94, NULL, 10, 3, 7, 18, 10, 6, 6, 16, 160, 112.00, 12, 6, 7.2, 19.2, 1, 2, 91, 20, 13, '2026-08-03', 2026, 8, 3, '2026-08-03 12:43:31', '2026-08-03 12:55:35', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_returns`
--

CREATE TABLE `warehouse_returns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `return_number` varchar(255) NOT NULL,
  `warehouse_item_id` bigint(20) UNSIGNED NOT NULL,
  `pre_list_id` bigint(20) UNSIGNED NOT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `currency_id` bigint(20) UNSIGNED NOT NULL,
  `billno` varchar(255) NOT NULL COMMENT 'bought_items bill number',
  `return_date` date NOT NULL,
  `supplier_account_id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `car_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `remaining_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `reason` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `warehouse_returns`
--

INSERT INTO `warehouse_returns` (`id`, `return_number`, `warehouse_item_id`, `pre_list_id`, `unit_id`, `currency_id`, `billno`, `return_date`, `supplier_account_id`, `account_id`, `car_id`, `quantity`, `unit_price`, `total`, `paid_amount`, `remaining_amount`, `reason`, `status`, `user_id`, `user_name`, `created_at`, `updated_at`) VALUES
(2, 'RET-2026-07-0001', 3, 85, 7, 1, '1', '2026-07-29', 86, 33, 19, 3.00, 40.00, 120.00, 0.00, 120.00, 'نخواستند', 0, 13, 'ادمین عمومی', '2026-07-29 14:25:17', '2026-07-29 14:25:17'),
(3, 'RET-2026-07-0002', 2, 92, 3, 1, '2', '2026-07-29', 95, 33, 20, 5.00, 40.00, 200.00, 0.00, 200.00, 'نخواستند', 0, 13, 'ادمین عمومی', '2026-07-29 14:33:21', '2026-07-29 14:34:01');

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_sales`
--

CREATE TABLE `warehouse_sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `billno` int(11) DEFAULT NULL,
  `journal_code` bigint(20) UNSIGNED NOT NULL,
  `factor` varchar(100) DEFAULT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `customer_account_id` bigint(20) UNSIGNED NOT NULL,
  `total` double(15,2) NOT NULL COMMENT 'with or without tax',
  `cur_pay` double NOT NULL,
  `remained` double NOT NULL,
  `currency_id` bigint(20) UNSIGNED NOT NULL,
  `car_id` bigint(20) UNSIGNED NOT NULL,
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

--
-- Dumping data for table `warehouse_sales`
--

INSERT INTO `warehouse_sales` (`id`, `billno`, `journal_code`, `factor`, `account_id`, `customer_account_id`, `total`, `cur_pay`, `remained`, `currency_id`, `car_id`, `note`, `idate`, `user_id`, `user_name`, `year`, `month`, `day`, `times`, `has_invoice`, `invoice_id`, `tax_activation`, `is_cleared`, `created_at`, `updated_at`) VALUES
(4, 1, 7, NULL, 33, 83, 7940.96, 0, 7940.96, 1, 20, NULL, '2026-08-01', 13, 'ادمین عمومی', 2026, 8, 1, 1785589410, 0, NULL, 1, 0, '2026-08-01 13:03:30', '2026-08-01 13:03:30'),
(5, 2, 8, NULL, 33, 83, 16.32, 5, 11.32, 1, 20, NULL, '2026-08-01', 13, 'ادمین عمومی', 2026, 8, 1, 1785591492, 0, NULL, 1, 0, '2026-08-01 13:38:12', '2026-08-01 13:38:12'),
(6, 3, 9, NULL, 33, 84, 15.90, 15.9, 0, 1, 20, NULL, '2026-08-01', 13, 'ادمین عمومی', 2026, 8, 1, 1785592052, 1, 16, 1, 0, '2026-08-01 13:47:32', '2026-08-04 04:54:50'),
(7, 4, 10, NULL, 33, 85, 184.00, 84, 100, 1, 20, NULL, '2026-08-01', 13, 'ادمین عمومی', 2026, 8, 1, 1785597893, 0, NULL, 0, 0, '2026-08-01 15:24:53', '2026-08-03 14:19:03'),
(8, 5, 11, NULL, 33, 94, 65.40, 35.4, 30, 1, 20, NULL, '2026-08-03', 13, 'ادمین عمومی', 2026, 8, 3, 1785735162, 1, 12, 0, 0, '2026-08-03 05:32:43', '2026-08-04 05:23:09'),
(9, 6, 12, NULL, 33, 84, 3673.60, 3673.6, 0, 1, 20, NULL, '2026-08-03', 13, 'ادمین عمومی', 2026, 8, 3, 1785736484, 1, 16, 1, 0, '2026-08-03 05:54:44', '2026-08-04 04:57:09'),
(10, 7, 14, NULL, 33, 98, 70.80, 0, 70.8, 1, 20, NULL, '2026-08-03', 13, 'ادمین عمومی', 2026, 8, 3, 1785761735, 0, NULL, 1, 0, '2026-08-03 12:55:35', '2026-08-03 12:55:35');

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
-- Indexes for table `bought_bill_payments`
--
ALTER TABLE `bought_bill_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bought_bill_payments_billno_index` (`billno`),
  ADD KEY `bought_bill_payments_payment_date_index` (`payment_date`),
  ADD KEY `bought_bill_payments_supplier_account_id_index` (`supplier_account_id`),
  ADD KEY `bought_bill_payments_account_id_index` (`account_id`),
  ADD KEY `bought_bill_payments_bought_item_id_foreign` (`bought_item_id`);

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
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `draft_orders`
--
ALTER TABLE `draft_orders`
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
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ord_num` (`ord_num`),
  ADD UNIQUE KEY `category_id` (`category_id`,`supplier_id`,`times`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`,`pre_list_id`,`category_id`,`unit_id`),
  ADD KEY `order_items_pre_list_id_foreign` (`pre_list_id`),
  ADD KEY `order_items_category_id_foreign` (`category_id`),
  ADD KEY `order_items_unit_id_foreign` (`unit_id`);

--
-- Indexes for table `org_bios`
--
ALTER TABLE `org_bios`
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
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`roleId`);

--
-- Indexes for table `sales_bill_payments`
--
ALTER TABLE `sales_bill_payments`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `sales_invoice_items_invoice_id_foreign` (`invoice_id`);

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
-- Indexes for table `warehouse_returns`
--
ALTER TABLE `warehouse_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `warehouse_returns_return_number_unique` (`return_number`),
  ADD KEY `warehouse_returns_billno_index` (`billno`,`car_id`) USING BTREE;

--
-- Indexes for table `warehouse_sales`
--
ALTER TABLE `warehouse_sales`
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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `account_types`
--
ALTER TABLE `account_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `backups`
--
ALTER TABLE `backups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `bought_bill_payments`
--
ALTER TABLE `bought_bill_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bought_items`
--
ALTER TABLE `bought_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `bought_item_details`
--
ALTER TABLE `bought_item_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `bought_item_pre_lists`
--
ALTER TABLE `bought_item_pre_lists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `buy_invoices`
--
ALTER TABLE `buy_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `buy_invoice_items`
--
ALTER TABLE `buy_invoice_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `buy_invoice_payments`
--
ALTER TABLE `buy_invoice_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `draft_orders`
--
ALTER TABLE `draft_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

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
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journals`
--
ALTER TABLE `journals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `last_logins`
--
ALTER TABLE `last_logins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `org_bios`
--
ALTER TABLE `org_bios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `roleId` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `sales_bill_payments`
--
ALTER TABLE `sales_bill_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `sales_details`
--
ALTER TABLE `sales_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sales_invoices`
--
ALTER TABLE `sales_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `sales_invoice_items`
--
ALTER TABLE `sales_invoice_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `sales_invoice_payments`
--
ALTER TABLE `sales_invoice_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `table_reset_passwords`
--
ALTER TABLE `table_reset_passwords`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `warehouse_items`
--
ALTER TABLE `warehouse_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `warehouse_returns`
--
ALTER TABLE `warehouse_returns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `warehouse_sales`
--
ALTER TABLE `warehouse_sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bought_bill_payments`
--
ALTER TABLE `bought_bill_payments`
  ADD CONSTRAINT `bought_bill_payments_bought_item_id_foreign` FOREIGN KEY (`bought_item_id`) REFERENCES `bought_items` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `buy_invoice_payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `buy_invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_pre_list_id_foreign` FOREIGN KEY (`pre_list_id`) REFERENCES `bought_item_pre_lists` (`id`),
  ADD CONSTRAINT `order_items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`);

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
