-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Jul 01, 2025 at 12:19 PM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sms01`
--

-- --------------------------------------------------------

--
-- Table structure for table `bank_accounts`
--

CREATE TABLE `bank_accounts` (
  `id` bigint UNSIGNED NOT NULL,
  `account_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_branch` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `swift_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iban` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_type` enum('checking','savings','current','business') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'checking',
  `current_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `opening_date` date DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'LKR',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bank_accounts`
--

INSERT INTO `bank_accounts` (`id`, `account_name`, `account_number`, `bank_name`, `bank_branch`, `swift_code`, `iban`, `account_type`, `current_balance`, `opening_balance`, `opening_date`, `description`, `is_active`, `currency`, `created_at`, `updated_at`) VALUES
(1, 'Main Business Account', '123456789', 'Commercial Bank', NULL, NULL, NULL, 'current', 102460.00, 100000.00, NULL, NULL, 1, 'LKR', '2025-06-30 00:08:40', '2025-06-30 04:02:05');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('c525a5357e97fef8d3db25841c86da1a', 'i:1;', 1751354263),
('c525a5357e97fef8d3db25841c86da1a:timer', 'i:1751354263;', 1751354263);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `iD_Auto` smallint UNSIGNED NOT NULL,
  `category_Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`iD_Auto`, `category_Name`, `description`, `created_at`, `updated_at`, `status`) VALUES
(1, 'EL', 'Electric Item', NULL, NULL, 1),
(3, 'EN', 'Engine Item', NULL, NULL, 1),
(4, 'SP', 'Spare Part', NULL, NULL, 1),
(5, 'Oil', 'Oil', NULL, NULL, 1),
(6, 'MOD', 'Modify Item', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint UNSIGNED NOT NULL,
  `custom_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'All Groups',
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `balance_credit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `last_visit` datetime DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `custom_id`, `name`, `phone`, `email`, `nic`, `group_name`, `address`, `balance_credit`, `last_visit`, `user_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'CUST000001', 'Manura Madhushan', '0713698026', 'madhushanm99@gmail.com', '990890056V', 'All Groups', '150/7 Golden Grow, Kospalwainna', 0.00, NULL, NULL, 1, '2025-06-14 00:54:42', '2025-06-14 00:54:42'),
(2, 'CUST000002', 'Manura Madhushan', '0713698026', 'madhushanm@99gmail.com', '990890056V', 'All Groups', '150/7 Golden Grow, Kospalwainna', 0.00, NULL, NULL, 1, '2025-06-14 00:56:54', '2025-06-14 00:56:54'),
(3, 'CUST000003', 'Manura Madhushan', '0713698026', 'madhushanm99@gmail.com', '990890056v', 'All Groups', '150/7 Golden Grow, Kospalwainna', 0.00, NULL, NULL, 1, '2025-06-14 01:02:55', '2025-06-14 01:02:55'),
(4, 'CUST000004', 'Manura Madhushan', '0713697025', 'madhushanm991@gmail.com', '911511970v', 'All Groups', '150/7 Golden Grow, Kospalwainna', 0.00, NULL, NULL, 1, '2025-06-14 01:24:53', '2025-06-15 15:25:08'),
(5, 'CUST000005', 'Manura Madhushan', '0713698027', 'madhushanm919@gmail.com', '990898056v', 'All Groups', '150/7 Golden Grow, Kospalwainna', 0.00, NULL, NULL, 1, '2025-06-15 15:39:00', '2025-06-15 15:39:00'),
(6, 'CUST000006', 'Jagath perera', '0713698085', 'jagath99@gmail.com', '990890089v', 'All Groups', '150/7 Kospelwinna', 0.00, NULL, NULL, 1, '2025-06-16 19:37:57', '2025-06-16 19:37:57'),
(7, 'CUST000007', 'Nuwan Chandima', '0713698058', 'nuwan@gmail.com', '990890054v', 'All Groups', '150/7, Golden Grow , Kospelawinna,', 0.00, NULL, NULL, 1, '2025-06-29 12:36:28', '2025-06-29 12:36:28'),
(8, 'CUST000008', 'Miyuru Sanjana', '0741329334', 'miyurusanjana@gmail.com', '990890087v', 'All Groups', 'Ihala Galayaya, Pannala', 0.00, NULL, NULL, 1, '2025-06-29 15:26:10', '2025-06-29 15:26:10'),
(9, 'CUST000009', 'Almost Done', '0741329334', '2020t00915@stu.cmb.ac.lk', NULL, 'All Groups', 'Pannala', 0.00, NULL, NULL, 1, '2025-07-01 03:51:55', '2025-07-01 03:51:55');

-- --------------------------------------------------------

--
-- Table structure for table `customer_logins`
--

CREATE TABLE `customer_logins` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_custom_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reset_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_token_expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_logins`
--

INSERT INTO `customer_logins` (`id`, `customer_custom_id`, `email`, `password`, `reset_token`, `reset_token_expires_at`, `is_active`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 'CUST000001', 'madhushanm99@gmail.com', '$2y$12$Ym.U2STJhgqx31j5Z/EquulU.bn6MeKZq86p1qErRZGwznyraJIA.', NULL, NULL, 1, '2025-07-01 04:38:38', NULL, '2025-07-01 04:38:38'),
(2, 'CUST000009', '2020t00915@stu.cmb.ac.lk', '$2y$12$GtlhvKF1Vh6bX1PyB4rYiu6OmE7qca8/Fn/jAgYS.1TH2qCTF/Zpy', NULL, NULL, 1, NULL, '2025-07-01 03:51:56', '2025-07-01 03:51:56');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grn`
--

CREATE TABLE `grn` (
  `grn_id` smallint UNSIGNED NOT NULL,
  `grn_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `grn_date` date NOT NULL,
  `po_Auto_ID` smallint UNSIGNED DEFAULT NULL,
  `po_No` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supp_Cus_ID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `received_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grn`
--

INSERT INTO `grn` (`grn_id`, `grn_no`, `grn_date`, `po_Auto_ID`, `po_No`, `supp_Cus_ID`, `invoice_no`, `invoice_date`, `received_by`, `note`, `status`, `created_at`, `updated_at`) VALUES
(2, 'GRN000001', '2025-06-14', NULL, NULL, 'DPMC', '151515', '2025-06-13', 'Manura Madhushan', NULL, 1, '2025-06-13 19:04:56', '2025-06-13 19:05:15'),
(3, 'GRN000002', '2025-06-14', NULL, NULL, 'DPMC', '151518', '2025-06-14', 'Manura Madhushan', NULL, 1, '2025-06-13 19:38:20', '2025-06-13 19:38:20'),
(4, 'GRN000003', '2025-06-17', NULL, NULL, 'DnD', 'inv1589', '2025-06-17', 'Admin', NULL, 1, '2025-06-16 19:43:27', '2025-06-16 19:43:27'),
(5, 'GRN000004', '2025-06-30', NULL, NULL, 'DPMC', '151565', '2025-06-30', 'User', NULL, 1, '2025-06-30 02:17:55', '2025-06-30 02:17:55'),
(6, 'GRN000005', '2025-06-30', NULL, NULL, 'RAM', '151565', NULL, 'Admin', NULL, 1, '2025-06-30 05:50:00', '2025-06-30 05:50:00'),
(7, 'GRN000006', '2025-06-30', NULL, NULL, 'RAM', '151568', NULL, 'Admin', NULL, 1, '2025-06-30 05:56:48', '2025-06-30 05:56:48'),
(8, 'GRN000007', '2025-06-30', NULL, NULL, 'DnD', '151565', NULL, 'Admin', NULL, 1, '2025-06-30 06:02:45', '2025-06-30 06:02:45'),
(9, 'GRN000008', '2025-06-30', NULL, NULL, 'DnD', '151565', NULL, 'Admin', NULL, 1, '2025-06-30 06:09:27', '2025-06-30 06:09:27'),
(10, 'GRN000009', '2025-06-30', NULL, NULL, 'DnD', '151565', NULL, 'Admin', NULL, 1, '2025-06-30 06:10:32', '2025-06-30 06:10:32'),
(11, 'GRN000010', '2025-06-30', NULL, NULL, 'DnD', '151565', NULL, 'Admin', NULL, 1, '2025-06-30 06:14:35', '2025-06-30 06:14:35'),
(12, 'GRN000011', '2025-06-30', NULL, NULL, 'AM', '151565', NULL, 'Admin', NULL, 1, '2025-06-30 06:29:03', '2025-06-30 06:29:03'),
(13, 'GRN000012', '2025-06-30', NULL, NULL, 'AM', '151565', NULL, 'Admin', NULL, 1, '2025-06-30 06:30:06', '2025-06-30 06:30:06'),
(14, 'GRN000013', '2025-06-30', NULL, NULL, 'AM', '151565', NULL, 'Admin', NULL, 1, '2025-06-30 06:38:23', '2025-06-30 06:38:23'),
(15, 'GRN000014', '2025-06-30', NULL, NULL, 'AM', '151568', NULL, 'Admin', NULL, 1, '2025-06-30 06:39:28', '2025-06-30 06:39:28'),
(16, 'GRN000015', '2025-06-30', NULL, NULL, 'AM', '151568', NULL, 'Admin', NULL, 1, '2025-06-30 06:40:44', '2025-06-30 06:40:44'),
(17, 'GRN000016', '2025-06-30', NULL, NULL, 'AM', '151565', NULL, 'Admin', NULL, 1, '2025-06-30 06:45:49', '2025-06-30 06:45:49'),
(18, 'GRN000017', '2025-06-30', NULL, NULL, 'AM', '151565', '2025-06-30', 'Admin', NULL, 1, '2025-06-30 06:51:34', '2025-06-30 06:51:34'),
(19, 'GRN000018', '2025-06-30', NULL, NULL, 'AM', '151565', '2025-06-30', 'Admin', NULL, 1, '2025-06-30 06:52:21', '2025-06-30 06:52:21'),
(20, 'GRN000019', '2025-06-30', NULL, NULL, 'AM', '151565', NULL, 'Admin', NULL, 1, '2025-06-30 07:02:04', '2025-06-30 07:02:04'),
(21, 'GRN000020', '2025-06-30', NULL, NULL, 'AM', '1515658', NULL, 'Admin', NULL, 1, '2025-06-30 11:10:38', '2025-06-30 11:10:38'),
(22, 'GRN000021', '2025-06-30', NULL, NULL, 'DPMC', NULL, NULL, 'Admin', NULL, 1, '2025-06-30 11:37:52', '2025-06-30 11:37:52');

-- --------------------------------------------------------

--
-- Table structure for table `grn_items`
--

CREATE TABLE `grn_items` (
  `grn_item_id` bigint UNSIGNED NOT NULL,
  `grn_id` smallint UNSIGNED NOT NULL,
  `item_ID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty_received` int NOT NULL,
  `price` double NOT NULL,
  `line_total` double NOT NULL,
  `remarks` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cost_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grn_items`
--

INSERT INTO `grn_items` (`grn_item_id`, `grn_id`, `item_ID`, `item_Name`, `qty_received`, `price`, `line_total`, `remarks`, `created_at`, `updated_at`, `cost_value`, `discount`) VALUES
(2, 2, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 5, 2500, 12500, NULL, '2025-06-13 19:04:56', '2025-06-13 19:04:56', 0.00, NULL),
(3, 3, 'DD121181', 'OIL FILTER WITH SPRING', 10, 450, 4500, NULL, '2025-06-13 19:38:20', '2025-06-13 19:38:20', 0.00, NULL),
(4, 4, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 5, 2500, 12500, NULL, '2025-06-16 19:43:27', '2025-06-16 19:43:27', 0.00, NULL),
(5, 4, 'DJ151089', 'Spocket', 5, 500, 2500, NULL, '2025-06-16 19:43:27', '2025-06-16 19:43:27', 0.00, NULL),
(6, 5, '36DJ4031', 'KIT END -KIT MJR MAST CYL & GR\r\n', 20, 1070, 21400, NULL, '2025-06-30 02:17:55', '2025-06-30 02:17:55', 0.00, NULL),
(7, 6, 'ASKBS0104', 'BRAKE SHOES PULSAR DTSI', 10, 950, 9500, NULL, '2025-06-30 05:50:00', '2025-06-30 05:50:00', 0.00, NULL),
(8, 7, 'DD111018', 'PLUG SPARK', 10, 750, 7500, NULL, '2025-06-30 05:56:48', '2025-06-30 05:56:48', 0.00, NULL),
(9, 8, 'DD111018', 'PLUG SPARK', 5, 750, 3750, NULL, '2025-06-30 06:02:45', '2025-06-30 06:02:45', 0.00, NULL),
(10, 9, 'BGO10W30C', 'BAJAJ GENUINE OIL 10W30 CAN 1L', 5, 2800, 14000, NULL, '2025-06-30 06:09:27', '2025-06-30 06:09:27', 0.00, NULL),
(11, 10, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500, 2500, NULL, '2025-06-30 06:10:32', '2025-06-30 06:10:32', 0.00, NULL),
(12, 11, '36DJ4031', 'KIT END -KIT MJR MAST CYL & GR\r\n', 5, 1070, 5350, NULL, '2025-06-30 06:14:35', '2025-06-30 06:14:35', 0.00, NULL),
(13, 12, 'AA121006', 'OIL FILTER ELEMENT 3W', 5, 350, 1750, NULL, '2025-06-30 06:29:03', '2025-06-30 06:29:03', 0.00, NULL),
(14, 13, 'AA121006', 'OIL FILTER ELEMENT 3W', 2, 350, 700, NULL, '2025-06-30 06:30:06', '2025-06-30 06:30:06', 0.00, NULL),
(15, 14, 'AA121006', 'OIL FILTER ELEMENT 3W', 2, 350, 700, NULL, '2025-06-30 06:38:23', '2025-06-30 06:38:23', 0.00, NULL),
(16, 15, 'AA121006', 'OIL FILTER ELEMENT 3W', 1, 350, 350, NULL, '2025-06-30 06:39:28', '2025-06-30 06:39:28', 0.00, NULL),
(17, 16, 'AA121006', 'OIL FILTER ELEMENT 3W', 1, 350, 350, NULL, '2025-06-30 06:40:44', '2025-06-30 06:40:44', 0.00, NULL),
(18, 17, 'AA121006', 'OIL FILTER ELEMENT 3W', 1, 350, 350, NULL, '2025-06-30 06:45:49', '2025-06-30 06:45:49', 0.00, NULL),
(19, 18, 'AA121006', 'OIL FILTER ELEMENT 3W', 1, 350, 350, NULL, '2025-06-30 06:51:34', '2025-06-30 06:51:34', 0.00, NULL),
(20, 19, 'AA121006', 'OIL FILTER ELEMENT 3W', 1, 350, 350, NULL, '2025-06-30 06:52:21', '2025-06-30 06:52:21', 0.00, NULL),
(21, 20, 'AA121006', 'OIL FILTER ELEMENT 3W', 1, 350, 350, NULL, '2025-06-30 07:02:04', '2025-06-30 07:02:04', 0.00, NULL),
(22, 21, 'AA121006', 'OIL FILTER ELEMENT 3W', 5, 350, 1662.5, NULL, '2025-06-30 11:10:38', '2025-06-30 11:10:38', 0.00, 5.00),
(23, 22, 'DS141014', 'Fuel Tube', 10, 350, 3325, NULL, '2025-06-30 11:37:52', '2025-06-30 11:37:52', 0.00, 5.00);

-- --------------------------------------------------------

--
-- Table structure for table `invoice_returns`
--

CREATE TABLE `invoice_returns` (
  `id` bigint UNSIGNED NOT NULL,
  `return_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sales_invoice_id` bigint UNSIGNED NOT NULL,
  `invoice_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `return_date` date NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `processed_by` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_returns`
--

INSERT INTO `invoice_returns` (`id`, `return_no`, `sales_invoice_id`, `invoice_no`, `customer_id`, `return_date`, `total_amount`, `reason`, `notes`, `processed_by`, `status`, `created_at`, `updated_at`) VALUES
(1, 'RTN000001', 5, 'INV000005', 'CUST000002', '2025-06-29', 2500.00, 'Damaged During Delivery', NULL, 'Admin', 'completed', '2025-06-29 16:19:58', '2025-06-29 16:19:58'),
(2, 'RTN000002', 1, 'INV000001', 'CUST000001', '2025-06-29', 2500.00, 'Customer Changed Mind', NULL, 'Admin', 'completed', '2025-06-29 16:21:14', '2025-06-29 16:21:14'),
(3, 'RTN000003', 5, 'INV000005', 'CUST000002', '2025-06-30', 2500.00, 'Defective Item', NULL, 'Admin', 'completed', '2025-06-30 05:31:01', '2025-06-30 05:31:01');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_return_items`
--

CREATE TABLE `invoice_return_items` (
  `id` bigint UNSIGNED NOT NULL,
  `invoice_return_id` bigint UNSIGNED NOT NULL,
  `line_no` int NOT NULL,
  `item_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty_returned` int NOT NULL,
  `original_qty` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `discount` decimal(5,2) NOT NULL DEFAULT '0.00',
  `line_total` decimal(10,2) NOT NULL,
  `return_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_return_items`
--

INSERT INTO `invoice_return_items` (`id`, `invoice_return_id`, `line_no`, `item_id`, `item_name`, `qty_returned`, `original_qty`, `unit_price`, `discount`, `line_total`, `return_reason`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 3, 2500.00, 0.00, 2500.00, '', '2025-06-29 16:19:58', '2025-06-29 16:19:58'),
(2, 2, 1, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 1, 2500.00, 0.00, 2500.00, '', '2025-06-29 16:21:14', '2025-06-29 16:21:14'),
(3, 3, 1, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 3, 2500.00, 0.00, 2500.00, 'expired', '2025-06-30 05:31:01', '2025-06-30 05:31:01');

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `item_ID_Auto` smallint UNSIGNED NOT NULL,
  `item_ID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_Type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `catagory_Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sales_Price` decimal(15,2) NOT NULL,
  `units` double NOT NULL,
  `unitofMeture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`item_ID_Auto`, `item_ID`, `item_Name`, `product_Type`, `catagory_Name`, `sales_Price`, `units`, `unitofMeture`, `location`, `created_at`, `updated_at`, `status`) VALUES
(1, '36DJ4031', 'KIT END -KIT MJR MAST CYL & GR\r\n', 'Genuine', 'SP', 1070.00, 1, 'Item', '', NULL, NULL, 1),
(2, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 'After Marcket', 'Oil', 2500.00, 1, 'L', 'Rack002', '2025-06-04 15:38:45', '2025-06-04 15:38:45', 1),
(3, 'DD121181', 'OIL FILTER WITH SPRING', 'Genuine', 'Spare', 450.00, 1, 'Item', 'Rack003', '2025-06-04 15:39:23', '2025-06-04 15:39:23', 1),
(4, 'AA121006', 'OIL FILTER ELEMENT 3W', 'Genuine', 'Spare', 350.00, 1, 'Item', 'Rack004', '2025-06-04 15:40:02', '2025-06-04 15:40:02', 1),
(5, 'DD111018', 'PLUG SPARK', 'Genuine', 'Spare', 750.00, 1, 'Item', 'Rack002', '2025-06-04 15:40:39', '2025-06-04 15:40:39', 1),
(6, 'BGO10W30C', 'BAJAJ GENUINE OIL 10W30 CAN 1L', 'Genuine', 'Oil', 2800.00, 1, 'L', 'Rack001', '2025-06-04 15:41:30', '2025-06-04 15:41:30', 1),
(7, 'ASKBS0104', 'BRAKE SHOES PULSAR DTSI', 'After Marcket', 'Spare', 950.00, 1, 'Item', 'Rack003', '2025-06-04 15:42:09', '2025-06-04 15:42:09', 1),
(8, 'DJ151071', 'SET - DISC PAD [ ETS ]', 'Genuine', 'Spare', 1000.00, 1, 'Item', 'Rack003', '2025-06-04 15:42:52', '2025-06-04 15:42:52', 1),
(9, 'DS141014', 'Fuel Tube', 'Genuine', 'Spare', 350.00, 1, 'Item', 'Rack002', '2025-06-04 15:43:28', '2025-06-04 15:43:28', 1),
(10, '36314002', 'KIT BALL STEEL STEERING', 'Genuine', 'Spare', 350.00, 1, 'Item', 'Rack003', '2025-06-04 15:43:54', '2025-06-04 15:43:54', 1),
(11, 'JA541205', 'GASKET MAGNETO COVER PUL 135', 'Genuine', 'Spare', 350.00, 1, 'Item', 'Rack005', '2025-06-04 15:44:35', '2025-06-04 15:44:35', 1),
(12, 'DJ151089', 'Spocket', 'Genuine', 'Spare', 500.00, 1, 'Item', 'Rack001', '2025-06-16 19:41:19', '2025-06-16 19:41:19', 1);

-- --------------------------------------------------------

--
-- Table structure for table `item_location`
--

CREATE TABLE `item_location` (
  `iD_Auto` smallint UNSIGNED NOT NULL,
  `location_Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `item_location`
--

INSERT INTO `item_location` (`iD_Auto`, `location_Name`, `description`, `created_at`, `updated_at`, `status`) VALUES
(1, 'Rack001', 'Rack001', NULL, NULL, 1),
(2, 'Rack002', 'Rack002', NULL, NULL, 1),
(3, 'Rack003', 'Rack003', NULL, NULL, 1),
(4, 'Rack004', 'Rack004', NULL, NULL, 1),
(5, 'Rack005', 'Rack005', NULL, NULL, 1),
(6, 'Rack006', 'Rack006', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `item_stock`
--

CREATE TABLE `item_stock` (
  `iD_Auto` smallint UNSIGNED NOT NULL,
  `item_ID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sales_Price` decimal(10,2) NOT NULL,
  `qty` int NOT NULL,
  `reorder_Lvl` int NOT NULL,
  `reorder_Qty` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_types`
--

CREATE TABLE `job_types` (
  `id` bigint UNSIGNED NOT NULL,
  `jobCustomID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jobType` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `salesPrice` decimal(10,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_types`
--

INSERT INTO `job_types` (`id`, `jobCustomID`, `jobType`, `salesPrice`, `status`, `created_at`, `updated_at`) VALUES
(1, 'JOB00001', 'RUNNING REPAIR', 1000.00, 1, NULL, NULL),
(2, 'JOB00002', 'REPLACING MAJOR KIT', 350.00, 1, NULL, NULL),
(3, 'JOB00003', 'REPLACING FRONT WHEEL BERING', 350.00, 1, NULL, NULL),
(4, 'JOB00004', 'REPLACING FRONT WHEEL BERING', 500.00, 1, NULL, NULL),
(5, 'JOB00005', 'GEAR CABLE REPIRES', 1500.00, 1, NULL, NULL),
(6, 'JOB00006', 'REPAIRING CHARGESS', 150.00, 1, NULL, NULL),
(7, 'JOB00007', 'FULL SERVICE AND REPIAR CHARGES', 4250.00, 1, NULL, NULL),
(8, 'JOB00008', 'NORMAL SERVICE 125CC', 2500.00, 1, NULL, NULL),
(9, 'JOB00009', 'BODY WOSH', 550.00, 1, NULL, NULL),
(10, 'JOB00010', 'REPAIRING CHARGESS', 150.00, 1, NULL, NULL),
(11, 'JOB00011', 'REPLACING CHAIN AND SPOKET SET', 650.00, 1, NULL, NULL),
(12, 'JOB00012', '3W FULL GRESSING', 850.00, 1, NULL, NULL),
(13, 'JOB00013', 'REPAIRING CHARGESS', 650.00, 1, NULL, NULL),
(14, 'JOB00014', '3W REPLECING REAR WHEEL BERING', 1000.00, 1, NULL, NULL),
(15, 'JOB00015', '3W FULL GRESSING', 850.00, 1, NULL, NULL),
(16, 'JOB00016', '3W FULL GRESSING', 850.00, 1, NULL, NULL),
(17, 'JOB00017', '3W FULL SERVICE', 3750.00, 1, NULL, NULL),
(18, 'JOB00018', '3W FULL GRESSING', 850.00, 1, NULL, NULL),
(19, 'JOB00019', '3W FULL SERVICE', 3750.00, 1, NULL, NULL),
(20, 'JOB00020', 'NORMAL SERVICE 125CC', 2500.00, 1, NULL, NULL),
(21, 'JOB00021', 'CARB OVERHAUL', 650.00, 1, NULL, NULL),
(22, 'JOB00022', 'START MOTOR -CLEANING/GRESSING/REPARING/REPLACING BRUSH', 350.00, 1, NULL, NULL),
(23, 'JOB00023', 'BODY WOSH', 550.00, 1, NULL, NULL),
(24, 'JOB00024', 'CARB OVERHAUL', 550.00, 1, NULL, NULL),
(25, 'JOB00025', 'BODY WOSH', 550.00, 1, NULL, NULL),
(26, 'JOB00026', 'CARB OVERHAUL', 550.00, 1, NULL, NULL),
(27, 'JOB00027', 'SCOOTER BELT SIDE REPAIRE', 1250.00, 1, NULL, NULL),
(28, 'JOB00028', 'RUNNING REPAIR', 650.00, 1, NULL, NULL),
(29, 'JOB00029', 'REPAIRING CHARGESS', 650.00, 1, NULL, NULL),
(30, 'JOB00030', 'NORMAL SERVICE 180CC', 2500.00, 1, NULL, NULL),
(31, 'JOB00031', 'CLUTCH OVERHAUL', 1250.00, 1, NULL, NULL),
(32, 'JOB00032', 'START MOTOR REPLACING', 500.00, 1, NULL, NULL),
(33, 'JOB00033', 'WIRING CHARGESS', 1750.00, 1, NULL, NULL),
(34, 'JOB00034', 'START MOTOR -CLEANING/GRESSING/REPARING/REPLACING BRUSH', 1000.00, 1, NULL, NULL),
(35, 'JOB00035', '3W REPLECING FRONT SHOK', 350.00, 1, NULL, NULL),
(36, 'JOB00036', 'BODY WOSH', 500.00, 1, NULL, NULL),
(37, 'JOB00037', 'ACSIDENT REPIAR', 5000.00, 1, NULL, NULL),
(38, 'JOB00038', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 500.00, 1, NULL, NULL),
(39, 'JOB00039', 'CARB OVERHAUL', 850.00, 1, NULL, NULL),
(40, 'JOB00040', 'AIR FILTER CLEANING', 200.00, 1, NULL, NULL),
(41, 'JOB00041', 'REPLACING HANDLE CUP SET / PORK OIL', 1250.00, 1, NULL, NULL),
(42, 'JOB00042', 'REPLACING SWIM ARM BUSH', 1500.00, 1, NULL, NULL),
(43, 'JOB00043', 'NORMAL SERVICE 100CC', 2500.00, 1, NULL, NULL),
(44, 'JOB00044', 'NORMAL SERVICE 125CC', 2500.00, 1, NULL, NULL),
(45, 'JOB00045', 'REPLACING F', 250.00, 1, NULL, NULL),
(46, 'JOB00046', 'REPLACING FRONT DIS PAD', 250.00, 1, NULL, NULL),
(47, 'JOB00047', 'REPLACING REAR BREAK SHOE', 200.00, 1, NULL, NULL),
(48, 'JOB00048', 'BODY WOSH', 550.00, 1, NULL, NULL),
(49, 'JOB00049', '3W FULL GRESSING', 850.00, 1, NULL, NULL),
(50, 'JOB00050', '3W FULL SERVICE', 3750.00, 1, NULL, NULL),
(51, 'JOB00051', 'NORMAL SERVICE 150CC', 2500.00, 1, NULL, NULL),
(152, 'JOB00052', 'CARB OVERHAUL', 550.00, 1, NULL, NULL),
(153, 'JOB00053', 'BODY WOSH', 550.00, 1, NULL, NULL),
(154, 'JOB00054', 'REPLECING REAR BRAKE SHOE', 250.00, 1, NULL, NULL),
(155, 'JOB00055', 'CLEANING FRONT HUB', 200.00, 1, NULL, NULL),
(156, 'JOB00056', 'GRESSING & REPAIRING', 650.00, 1, NULL, NULL),
(157, 'JOB00057', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 2000.00, 1, NULL, NULL),
(158, 'JOB00058', 'BODY WOSH', 550.00, 1, NULL, NULL),
(159, 'JOB00059', 'NORMAL SERVICE 125CC', 2500.00, 1, NULL, NULL),
(160, 'JOB00060', 'CARB / FUEL TANK CLEANING', 1000.00, 1, NULL, NULL),
(161, 'JOB00061', 'CARB / FUEL TANK/ AIR FILTER CLEANING', 1650.00, 1, NULL, NULL),
(162, 'JOB00062', '3W FULL SERVICE', 3750.00, 1, NULL, NULL),
(163, 'JOB00063', 'START MOTOR -CLEANING/GRESSING/REPARING/REPLACING BRUSH', 1000.00, 1, NULL, NULL),
(164, 'JOB00064', 'BODY WOSH', 550.00, 1, NULL, NULL),
(165, 'JOB00065', 'ADJUSTING / OILING DRIVE CHAIN', 100.00, 1, NULL, NULL),
(166, 'JOB00066', 'FULL SERVICE 125CC', 3250.00, 1, NULL, NULL),
(167, 'JOB00067', 'REPLACING HANDLE CUP SET / PORK OIL', 1500.00, 1, NULL, NULL),
(168, 'JOB00068', 'ADJUSTING / OILING DRIVE CHAIN', 100.00, 1, NULL, NULL),
(169, 'JOB00069', 'BODY WOSH', 550.00, 1, NULL, NULL),
(170, 'JOB00070', 'CHANGING ENGINE OIL', 100.00, 1, NULL, NULL),
(171, 'JOB00071', '3W FULL GRESSING', 750.00, 1, NULL, NULL),
(172, 'JOB00072', 'REPAIRING CHARGESS', 500.00, 1, NULL, NULL),
(173, 'JOB00073', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 350.00, 1, NULL, NULL),
(174, 'JOB00074', 'NORMAL SERVICE FZ', 3750.00, 1, NULL, NULL),
(175, 'JOB00075', 'REPLACING SWIM ARM BUSH FZ', 1000.00, 1, NULL, NULL),
(176, 'JOB00076', 'FUEL TANK/PUMP /EFI CLEANING & TUNING WITH WURTH CLEANER', 5500.00, 1, NULL, NULL),
(177, 'JOB00077', '3W FULL SERVICE', 3750.00, 1, NULL, NULL),
(178, 'JOB00078', '3W FULL GRESSING', 850.00, 1, NULL, NULL),
(179, 'JOB00079', '3W FULL GRESSING', 850.00, 1, NULL, NULL),
(180, 'JOB00080', '3W REPLECING REAR WHEEL BERING', 1000.00, 1, NULL, NULL),
(181, 'JOB00081', 'NORMAL SERVICE TVS SCOOTER', 2500.00, 1, NULL, NULL),
(182, 'JOB00082', 'NORMAL SERVICE', 2650.00, 1, NULL, NULL),
(183, 'JOB00083', 'BO', 550.00, 1, NULL, NULL),
(184, 'JOB00084', 'ELE', 500.00, 1, NULL, NULL),
(185, 'JOB00085', 'REPLACING CHAIN AND SPOKET SET', 1000.00, 1, NULL, NULL),
(186, 'JOB00086', 'REPLACING CHAIN LINK', 150.00, 1, NULL, NULL),
(187, 'JOB00087', 'REPLACING FRONT SPOKET', 150.00, 1, NULL, NULL),
(188, 'JOB00088', 'WIRING CHARGESS', 1750.00, 1, NULL, NULL),
(189, 'JOB00089', 'PART ASSEMBLING CHARGES', 1000.00, 1, NULL, NULL),
(190, 'JOB00090', 'REPLACING CHAIN AND SPOKET SET', 750.00, 1, NULL, NULL),
(191, 'JOB00091', 'CHANGING ENGINE OIL', 100.00, 1, NULL, NULL),
(192, 'JOB00092', 'START MOTOR REPLACING', 500.00, 1, NULL, NULL),
(193, 'JOB00093', 'FULL SERVICE 150CC', 3500.00, 1, NULL, NULL),
(194, 'JOB00094', 'BODY WOSH', 550.00, 1, NULL, NULL),
(195, 'JOB00095', 'START MOTOR -CLEANING/GRESSING/REPARING/REPLACING BRUSH', 650.00, 1, NULL, NULL),
(196, 'JOB00096', 'ADJUSTING / OILING DRIVE CHAIN', 100.00, 1, NULL, NULL),
(197, 'JOB00097', 'FUEL TANK CLEANING / REPAIRING', 1000.00, 1, NULL, NULL),
(198, 'JOB00098', '3W FULL GRESSING', 850.00, 1, NULL, NULL),
(199, 'JOB00099', 'REPAIRING CHARGESS', 900.00, 1, NULL, NULL),
(200, 'JOB00100', 'START MOTOR -CLEANING/GRESSING/REPARING/REPLACING BRUSH', 650.00, 1, NULL, NULL),
(201, 'JOB00101', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 750.00, 1, NULL, NULL),
(202, 'JOB00102', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 350.00, 1, NULL, NULL),
(203, 'JOB00103', 'REPLECING SPEED METER CABLE', 150.00, 1, NULL, NULL),
(204, 'JOB00104', 'REPAIRING CHARGESS', 2000.00, 1, NULL, NULL),
(205, 'JOB00105', 'REPAIRING CHARGESS', 850.00, 1, NULL, NULL),
(206, 'JOB00106', 'NORMAL SERVICE', 2500.00, 1, NULL, NULL),
(207, 'JOB00107', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 1250.00, 1, NULL, NULL),
(208, 'JOB00108', 'REPAIRING CHARGESS', 1000.00, 1, NULL, NULL),
(209, 'JOB00109', 'BODY WOSH', 550.00, 1, NULL, NULL),
(210, 'JOB00110', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 650.00, 1, NULL, NULL),
(211, 'JOB00111', 'NORMAL SERVICE', 2500.00, 1, NULL, NULL),
(212, 'JOB00112', 'REPLACING FRONT DIS PAD', 150.00, 1, NULL, NULL),
(213, 'JOB00113', 'NORMAL SERVICE SCOOTER', 2550.00, 1, NULL, NULL),
(214, 'JOB00114', 'BODY WOSH', 650.00, 1, NULL, NULL),
(215, 'JOB00115', 'ADJUSTING / OILING DRIVE CHAIN', 100.00, 1, NULL, NULL),
(216, 'JOB00116', 'ADJUSTING / OILING DRIVE CHAIN', 150.00, 1, NULL, NULL),
(217, 'JOB00117', 'ADJUSTING CUP SET', 100.00, 1, NULL, NULL),
(218, 'JOB00118', 'BODY WOSH', 550.00, 1, NULL, NULL),
(219, 'JOB00119', 'REPAIRING CHARGESS', 1500.00, 1, NULL, NULL),
(220, 'JOB00120', 'REPAIRING CHARGESS', 250.00, 1, NULL, NULL),
(221, 'JOB00121', 'BODY WOSH', 550.00, 1, NULL, NULL),
(222, 'JOB00122', 'REPAIRING CHARGESS', 350.00, 1, NULL, NULL),
(223, 'JOB00123', 'FULL SERVICE 150CC', 3500.00, 1, NULL, NULL),
(224, 'JOB00124', 'REPLACING CHAIN AND SPOKET SET WITH SWIM ARM BUSH SET', 1250.00, 1, NULL, NULL),
(225, 'JOB00125', 'BODY WOSH', 550.00, 1, NULL, NULL),
(226, 'JOB00126', 'CARB OVERHAUL', 550.00, 1, NULL, NULL),
(227, 'JOB00127', '3W TOP / UNDER WOSH /OILING', 1750.00, 1, NULL, NULL),
(228, 'JOB00128', '3W FULL GRESSING', 750.00, 1, NULL, NULL),
(229, 'JOB00129', 'CARB OVERHAUL', 550.00, 1, NULL, NULL),
(230, 'JOB00130', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 450.00, 1, NULL, NULL),
(231, 'JOB00131', 'NORMAL SERVICE 100CC', 2500.00, 1, NULL, NULL),
(232, 'JOB00132', 'BODY WOSH', 550.00, 1, NULL, NULL),
(233, 'JOB00133', 'CABLE OILING / ADJSTING', 250.00, 1, NULL, NULL),
(234, 'JOB00134', 'CHANGING ENGINE OIL', 100.00, 1, NULL, NULL),
(235, 'JOB00135', 'ADJUSTING / OILING DRIVE CHAIN', 100.00, 1, NULL, NULL),
(236, 'JOB00136', 'CLEANING CARB', 500.00, 1, NULL, NULL),
(237, 'JOB00137', 'WIRING CHARGESS', 2500.00, 1, NULL, NULL),
(238, 'JOB00138', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 1650.00, 1, NULL, NULL),
(239, 'JOB00139', 'BODY WOSH', 500.00, 1, NULL, NULL),
(240, 'JOB00140', 'BODY WOSH', 550.00, 1, NULL, NULL),
(241, 'JOB00141', 'CLEANING REAR HUB', 250.00, 1, NULL, NULL),
(242, 'JOB00142', 'REPLACING FORK OIL SEAL', 1000.00, 1, NULL, NULL),
(243, 'JOB00143', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 2450.00, 1, NULL, NULL),
(244, 'JOB00144', 'BODY WOSH', 550.00, 1, NULL, NULL),
(245, 'JOB00145', 'NORMAL SERVICE', 2500.00, 1, NULL, NULL),
(246, 'JOB00146', 'REPLACING CLUTCH PLATE', 750.00, 1, NULL, NULL),
(247, 'JOB00147', 'REPLACING FORK OIL SEAL', 750.00, 1, NULL, NULL),
(248, 'JOB00148', 'NORMAL SERVICE', 2500.00, 1, NULL, NULL),
(249, 'JOB00149', 'REPAIRING CHARGESS', 750.00, 1, NULL, NULL),
(250, 'JOB00150', 'BODY WOSH', 550.00, 1, NULL, NULL),
(251, 'JOB00151', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 1500.00, 1, NULL, NULL),
(252, 'JOB00152', 'NORMAL SERVICE', 2500.00, 1, NULL, NULL),
(253, 'JOB00153', 'BODY WOSH', 550.00, 1, NULL, NULL),
(254, 'JOB00154', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 1250.00, 1, NULL, NULL),
(255, 'JOB00155', 'BODY WOSH', 550.00, 1, NULL, NULL),
(256, 'JOB00156', 'CABLE OILING / ADJSTING', 250.00, 1, NULL, NULL),
(257, 'JOB00157', 'REPAIRING CHARGESS', 250.00, 1, NULL, NULL),
(258, 'JOB00158', 'REPLACING HANDLE CUP SET / PORK OIL', 1250.00, 1, NULL, NULL),
(259, 'JOB00159', 'REPLACING CHAIN AND SPOKET SET', 750.00, 1, NULL, NULL),
(260, 'JOB00160', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 7300.00, 1, NULL, NULL),
(261, 'JOB00161', 'NORMAL SERVICE 125CC', 2550.00, 1, NULL, NULL),
(262, 'JOB00162', 'CARB OVERHAUL', 650.00, 1, NULL, NULL),
(263, 'JOB00163', 'NORMAL SERVICE', 2500.00, 1, NULL, NULL),
(264, 'JOB00164', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 1000.00, 1, NULL, NULL),
(265, 'JOB00165', 'BODY WOSH', 650.00, 1, NULL, NULL),
(266, 'JOB00166', 'REPLACING MINOR KIT', 650.00, 1, NULL, NULL),
(267, 'JOB00167', 'CARB OVERHAUL', 650.00, 1, NULL, NULL),
(268, 'JOB00168', 'CLEANING REAR HUB', 250.00, 1, NULL, NULL),
(269, 'JOB00169', 'NORMAL SERVICE', 2500.00, 1, NULL, NULL),
(270, 'JOB00170', 'CARB OVERHAUL', 650.00, 1, NULL, NULL),
(271, 'JOB00171', '1st FREE SERVICE', 2000.00, 1, NULL, NULL),
(272, 'JOB00172', 'REPLACING SWIM ARM BUSH', 1250.00, 1, NULL, NULL),
(273, 'JOB00173', 'BODY WOSH', 550.00, 1, NULL, NULL),
(274, 'JOB00174', 'NORMAL SERVICE 100CC', 2500.00, 1, NULL, NULL),
(275, 'JOB00175', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 2000.00, 1, NULL, NULL),
(276, 'JOB00176', 'CHANGING ENGINE OIL', 100.00, 1, NULL, NULL),
(277, 'JOB00177', 'REPLECING REAR BRAKE SHOE', 400.00, 1, NULL, NULL),
(278, 'JOB00178', 'LEATH WORK CHARGESS', 3900.00, 1, NULL, NULL),
(279, 'JOB00179', 'NORMAL SERVICE 125CC', 2500.00, 1, NULL, NULL),
(280, 'JOB00180', 'REPLECING FORK OIL', 350.00, 1, NULL, NULL),
(281, 'JOB00181', 'REPLACING CHAIN AND SPOKET SET', 650.00, 1, NULL, NULL),
(282, 'JOB00182', 'BODY WOSH', 550.00, 1, NULL, NULL),
(283, 'JOB00183', 'REPLACING CHAIN AND SPOKET SET', 750.00, 1, NULL, NULL),
(284, 'JOB00184', 'CHANGING ENGINE OIL', 100.00, 1, NULL, NULL),
(285, 'JOB00185', 'REPAIRING CHARGESS', 500.00, 1, NULL, NULL),
(286, 'JOB00186', 'REPLACING HANDLE CUP SET / PORK OIL', 2200.00, 1, NULL, NULL),
(287, 'JOB00187', 'REPAIRING CHARGESS', 1000.00, 1, NULL, NULL),
(288, 'JOB00188', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 450.00, 1, NULL, NULL),
(289, 'JOB00189', '2nd FREE SERVICE', 2000.00, 1, NULL, NULL),
(290, 'JOB00190', 'BODY WOSH', 550.00, 1, NULL, NULL),
(291, 'JOB00191', 'REPAIRING CHARGESS', 550.00, 1, NULL, NULL),
(292, 'JOB00192', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 1150.00, 1, NULL, NULL),
(293, 'JOB00193', 'NORMAL SERVICE 150CC', 2500.00, 1, NULL, NULL),
(294, 'JOB00194', 'BODY WOSH', 550.00, 1, NULL, NULL),
(295, 'JOB00195', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 750.00, 1, NULL, NULL),
(296, 'JOB00196', 'NORMAL SERVICE 100CC', 2500.00, 1, NULL, NULL),
(297, 'JOB00197', 'RE BORING', 1500.00, 1, NULL, NULL),
(298, 'JOB00198', 'DE CARBANICD VALVE PISTON / REPLACING TIMINGCHIN /REPLACING CLUCH ASSEMBLY', 3500.00, 1, NULL, NULL),
(299, 'JOB00199', 'REPAIRING CHARGESS', 750.00, 1, NULL, NULL),
(300, 'JOB00200', '3W FULL SERVICE', 3750.00, 1, NULL, NULL),
(301, 'JOB00201', '3W FULL GRESSING', 850.00, 1, NULL, NULL),
(302, 'JOB00202', 'REPLACING PIVOT PIN / GRESSING CUP SET', 2000.00, 1, NULL, NULL),
(303, 'JOB00203', 'NORMAL SERVICE', 2500.00, 1, NULL, NULL),
(304, 'JOB00204', 'REPLACING MINOR KIT', 350.00, 1, NULL, NULL),
(305, 'JOB00205', 'REPLACING METER CABLE', 250.00, 1, NULL, NULL),
(306, 'JOB00206', 'REPAIRING CHARGESS', 1000.00, 1, NULL, NULL),
(307, 'JOB00207', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 750.00, 1, NULL, NULL),
(308, 'JOB00208', 'BODY WOSH', 700.00, 1, NULL, NULL),
(309, 'JOB00209', 'REPLACING FORK OIL', 450.00, 1, NULL, NULL),
(310, 'JOB00210', 'CHANGING ENGINE OIL', 100.00, 1, NULL, NULL),
(311, 'JOB00211', 'ADJUSTING / OILING DRIVE CHAIN', 100.00, 1, NULL, NULL),
(312, 'JOB00212', '3W FULL GRESSING', 2000.00, 1, NULL, NULL),
(313, 'JOB00213', 'REPAIRING CHARGESS', 6000.00, 1, NULL, NULL),
(314, 'JOB00214', 'LEATH WORK CHARGESS', 1700.00, 1, NULL, NULL),
(315, 'JOB00215', 'BODY WOSH', 550.00, 1, NULL, NULL),
(316, 'JOB00216', 'REPLACING MAJOR KIT', 350.00, 1, NULL, NULL),
(317, 'JOB00217', 'BODY WOSH', 550.00, 1, NULL, NULL),
(318, 'JOB00218', 'ADJUSTING / OILING DRIVE CHAIN', 100.00, 1, NULL, NULL),
(319, 'JOB00219', 'BODY WOSH', 550.00, 1, NULL, NULL),
(320, 'JOB00220', 'CHANGING ENGINE OIL', 100.00, 1, NULL, NULL),
(321, 'JOB00221', 'CHANGING ENGINE OIL', 100.00, 1, NULL, NULL),
(322, 'JOB00222', 'ADJUSTING / OILING DRIVE CHAIN', 100.00, 1, NULL, NULL),
(323, 'JOB00223', 'CHANGING ENGINE OIL', 100.00, 1, NULL, NULL),
(324, 'JOB00224', 'REPLACING CHAIN AND SPOKET SET', 850.00, 1, NULL, NULL),
(325, 'JOB00225', 'BODY WOSH', 550.00, 1, NULL, NULL),
(326, 'JOB00226', 'REPLECING REAR BRAKE SHOE', 150.00, 1, NULL, NULL),
(327, 'JOB00227', 'REPLACING CLUTCH CABLE', 50.00, 1, NULL, NULL),
(328, 'JOB00228', 'REPLACING HANDLE CUP SET / PORK OIL', 1500.00, 1, NULL, NULL),
(329, 'JOB00229', 'REPAIRING CHARGESS', 300.00, 1, NULL, NULL),
(330, 'JOB00230', 'FULL SERVICE', 5450.00, 1, NULL, NULL),
(331, 'JOB00231', 'REPLACING REAR BREAK CABLE', 500.00, 1, NULL, NULL),
(332, 'JOB00232', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 500.00, 1, NULL, NULL),
(333, 'JOB00233', 'BODY WOSH', 500.00, 1, NULL, NULL),
(334, 'JOB00234', 'NORMAL SERVICE 125CC', 2500.00, 1, NULL, NULL),
(335, 'JOB00235', 'BODY WOSH', 550.00, 1, NULL, NULL),
(336, 'JOB00236', 'REPLACING FRONT PAKING CABLE', 500.00, 1, NULL, NULL),
(337, 'JOB00237', 'REPIARING FRONT BREAK SYSTEM', 750.00, 1, NULL, NULL),
(338, 'JOB00238', 'CHANGING ENGINE OIL', 50.00, 1, NULL, NULL),
(339, 'JOB00239', 'BODY WOSH', 550.00, 1, NULL, NULL),
(340, 'JOB00240', 'REPLACING CLUTCH BEARING', 350.00, 1, NULL, NULL),
(341, 'JOB00241', 'BODY WOSH', 550.00, 1, NULL, NULL),
(342, 'JOB00242', 'CABLE OILING / ADJSTING', 250.00, 1, NULL, NULL),
(343, 'JOB00243', 'BODY WOSH', 500.00, 1, NULL, NULL),
(344, 'JOB00244', 'CABLE OILING / ADJSTING', 250.00, 1, NULL, NULL),
(345, 'JOB00245', '1st FREE SERVICE', 2000.00, 1, NULL, NULL),
(346, 'JOB00246', 'CARB TUNING GREEN TEST', 350.00, 1, NULL, NULL),
(347, 'JOB00247', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 500.00, 1, NULL, NULL),
(348, 'JOB00248', 'ENGING REMOVING / FITTING', 2500.00, 1, NULL, NULL),
(349, 'JOB00249', 'BODY WOSH', 500.00, 1, NULL, NULL),
(350, 'JOB00250', 'CLEANING FUEL TANK', 500.00, 1, NULL, NULL),
(351, 'JOB00251', 'FULL SERVICE', 3750.00, 1, NULL, NULL),
(352, 'JOB00252', 'REPLACING ACC CABLE', 350.00, 1, NULL, NULL),
(353, 'JOB00253', 'REPLACING SPOKET HUB BEARING', 300.00, 1, NULL, NULL),
(354, 'JOB00254', 'NORMAL SERVICE 125CC', 2500.00, 1, NULL, NULL),
(355, 'JOB00255', 'FULL SERVICE', 3750.00, 1, NULL, NULL),
(356, 'JOB00256', 'REPLACING PIVOT PIN / GRESSING CUP SET', 2500.00, 1, NULL, NULL),
(357, 'JOB00257', '3W FULL GRESSING / REPLACING CUPLIN RUBBER', 1650.00, 1, NULL, NULL),
(358, 'JOB00258', 'CARB TUNING GREEN TEST', 200.00, 1, NULL, NULL),
(359, 'JOB00259', 'BREAK OVERHAUL', 1000.00, 1, NULL, NULL),
(360, 'JOB00260', 'BODY WOSH', 500.00, 1, NULL, NULL),
(361, 'JOB00261', 'BODY WOSH', 500.00, 1, NULL, NULL),
(362, 'JOB00262', 'CHANGING ENGINE OIL', 100.00, 1, NULL, NULL),
(363, 'JOB00263', 'ADJUSTING / OILING DRIVE CHAIN', 100.00, 1, NULL, NULL),
(364, 'JOB00264', 'REPLACING GEAR SHAFT OIL SEAL', 150.00, 1, NULL, NULL),
(365, 'JOB00265', 'BODY WOSH', 550.00, 1, NULL, NULL),
(366, 'JOB00266', 'CABLE OILING / ADJSTING CHAIN / ENGINE OIL CHANGING', 350.00, 1, NULL, NULL),
(367, 'JOB00267', 'REPLACING CUPSET 3W', 1500.00, 1, NULL, NULL),
(368, 'JOB00268', 'BREAK OVERHAUL', 1000.00, 1, NULL, NULL),
(369, 'JOB00269', 'CHANGING ENGINE OIL', 50.00, 1, NULL, NULL),
(370, 'JOB00270', 'BODY WOSH', 550.00, 1, NULL, NULL),
(371, 'JOB00271', 'REPLACING CHAIN AND SPOKET SET', 650.00, 1, NULL, NULL),
(372, 'JOB00272', 'CHANGING ENGINE OIL / CABLE OILING', 150.00, 1, NULL, NULL),
(373, 'JOB00273', 'NORMAL SERVICE 125CC', 2500.00, 1, NULL, NULL),
(374, 'JOB00274', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 2000.00, 1, NULL, NULL),
(375, 'JOB00275', 'BREAK PUMP REPIRE', 300.00, 1, NULL, NULL),
(376, 'JOB00276', 'REPLACING CUPSET 2W', 1000.00, 1, NULL, NULL),
(377, 'JOB00277', 'CABLE OILING / ADJSTING CHAIN / ENGINE OIL CHANGING/REPLACING REAR WHEL BERING', 750.00, 1, NULL, NULL),
(378, 'JOB00278', 'BODY WOSH', 550.00, 1, NULL, NULL),
(379, 'JOB00279', 'BODY WOSH', 550.00, 1, NULL, NULL),
(380, 'JOB00280', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 450.00, 1, NULL, NULL),
(381, 'JOB00281', 'BODY WOSH', 550.00, 1, NULL, NULL),
(382, 'JOB00282', 'REPAIRING CHARGESS', 650.00, 1, NULL, NULL),
(383, 'JOB00283', '3W FULL GRESSING', 850.00, 1, NULL, NULL),
(384, 'JOB00284', '3W TOP / UNDER WOSH /OILING', 2000.00, 1, NULL, NULL),
(385, 'JOB00285', 'WIRING CHARGESS', 2000.00, 1, NULL, NULL),
(386, 'JOB00286', 'CLEANING FUEL TANK', 850.00, 1, NULL, NULL),
(387, 'JOB00287', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 650.00, 1, NULL, NULL),
(388, 'JOB00288', 'BODY WOSH', 550.00, 1, NULL, NULL),
(389, 'JOB00289', 'CLEANING REAR & FRONT HUB', 450.00, 1, NULL, NULL),
(390, 'JOB00290', 'BODY WOSH', 550.00, 1, NULL, NULL),
(391, 'JOB00291', 'CLEANING REAR & FRONT HUB', 450.00, 1, NULL, NULL),
(392, 'JOB00292', 'BODY WOSH', 550.00, 1, NULL, NULL),
(393, 'JOB00293', 'CHANGING ENGINE OIL', 100.00, 1, NULL, NULL),
(394, 'JOB00294', 'ADJSTING BREAK', 100.00, 1, NULL, NULL),
(395, 'JOB00295', 'BODY WOSH', 500.00, 1, NULL, NULL),
(396, 'JOB00296', 'REPLACING REAR & FRONT BREAK SHOES', 400.00, 1, NULL, NULL),
(397, 'JOB00297', 'NORMAL SERVICE 125CC', 2650.00, 1, NULL, NULL),
(398, 'JOB00298', 'BREAK OVERHAUL', 650.00, 1, NULL, NULL),
(399, 'JOB00299', 'CARB TUNING GREEN TEST', 300.00, 1, NULL, NULL),
(400, 'JOB00300', 'REPLACING PIVOT PIN / FRONT BREAK SHOES / GRESSING CUP SET/', 2000.00, 1, NULL, NULL),
(401, 'JOB00301', 'REPLACING HANDLE CUP SET', 750.00, 1, NULL, NULL),
(402, 'JOB00302', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 500.00, 1, NULL, NULL),
(403, 'JOB00303', 'REPLACING PIVOT PIN / GRESSING CUP SET', 2000.00, 1, NULL, NULL),
(404, 'JOB00304', 'BODY WOSH', 550.00, 1, NULL, NULL),
(405, 'JOB00305', 'CABLE OILING / ADJSTING CHAIN / ENGINE OIL CHANGING', 350.00, 1, NULL, NULL),
(406, 'JOB00306', 'REPLACING FORK TUBE / FORK OIL / FORK OIL SEAL', 1000.00, 1, NULL, NULL),
(407, 'JOB00307', 'REPLACING CHAIN AND SPOKET SET', 850.00, 1, NULL, NULL),
(408, 'JOB00308', 'BODY WOSH', 550.00, 1, NULL, NULL),
(409, 'JOB00309', 'BODY WOSH', 550.00, 1, NULL, NULL),
(410, 'JOB00310', 'ADJUSTING / OILING DRIVE CHAIN', 100.00, 1, NULL, NULL),
(411, 'JOB00311', 'BODY WOSH', 550.00, 1, NULL, NULL),
(412, 'JOB00312', 'REPAIRING CHARGESS', 400.00, 1, NULL, NULL),
(413, 'JOB00313', '1st FREE SERVICE', 2000.00, 1, NULL, NULL),
(414, 'JOB00314', '1st FREE SERVICE', 2000.00, 1, NULL, NULL),
(415, 'JOB00315', 'BODY WOSH', 550.00, 1, NULL, NULL),
(416, 'JOB00316', 'CLEANING CARB', 500.00, 1, NULL, NULL),
(417, 'JOB00317', 'CABLE OILING / ADJSTING', 350.00, 1, NULL, NULL),
(418, 'JOB00318', 'ADJUSTING / OILING DRIVE CHAIN', 50.00, 1, NULL, NULL),
(419, 'JOB00319', 'NORMAL SERVICE 100CC', 2500.00, 1, NULL, NULL),
(420, 'JOB00320', '1st FREE SERVICE', 2000.00, 1, NULL, NULL),
(421, 'JOB00321', '3W TOP / UNDER WOSH /OILING', 2000.00, 1, NULL, NULL),
(422, 'JOB00322', 'CABLE OILING / ADJSTING', 350.00, 1, NULL, NULL),
(423, 'JOB00323', '3W FULL GRESSING', 850.00, 1, NULL, NULL),
(424, 'JOB00324', 'BODY WOSH', 550.00, 1, NULL, NULL),
(425, 'JOB00325', 'REPAIRING CHARGESS', 2750.00, 1, NULL, NULL),
(426, 'JOB00326', 'NORMAL SERVICE 125CC', 2500.00, 1, NULL, NULL),
(427, 'JOB00327', 'BODY WOSH', 500.00, 1, NULL, NULL),
(428, 'JOB00328', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 1000.00, 1, NULL, NULL),
(429, 'JOB00329', '1st FREE SERVICE', 2000.00, 1, NULL, NULL),
(430, 'JOB00330', 'BODY WOSH', 550.00, 1, NULL, NULL),
(431, 'JOB00331', 'REPLACING HANDLE CUP SET / PORK OIL', 1000.00, 1, NULL, NULL),
(432, 'JOB00332', 'REPLACING REAR BREAK SHOE', 200.00, 1, NULL, NULL),
(433, 'JOB00333', '2nd FREE SERVICE', 2000.00, 1, NULL, NULL),
(434, 'JOB00334', 'REPLACING REAR BREAK SHOE', 150.00, 1, NULL, NULL),
(435, 'JOB00335', '3W FULL SERVICE', 3750.00, 1, NULL, NULL),
(436, 'JOB00336', '3W FULL GRESSING', 850.00, 1, NULL, NULL),
(437, 'JOB00337', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 1000.00, 1, NULL, NULL),
(438, 'JOB00338', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 3500.00, 1, NULL, NULL),
(439, 'JOB00339', 'CARB TUNING GREEN TEST', 250.00, 1, NULL, NULL),
(440, 'JOB00340', 'NORMAL SERVICE', 2500.00, 1, NULL, NULL),
(441, 'JOB00341', 'CARB / FUEL TANK CLEANING', 1000.00, 1, NULL, NULL),
(442, 'JOB00342', 'NORMAL SERVICE 125CC', 2500.00, 1, NULL, NULL),
(443, 'JOB00343', 'FULL SERVICE', 3500.00, 1, NULL, NULL),
(444, 'JOB00344', 'REPLACING CLUTCH PLATE / HEAD CAM RUBBER / START MOTOR', 2000.00, 1, NULL, NULL),
(445, 'JOB00345', 'FULL SERVICE 150CC / REPLACING CHAIN & SPOKET', 3500.00, 1, NULL, NULL),
(446, 'JOB00346', 'ENGINE OVERHAUL REPLACING ROD KIT REPLACING CAM BEARING', 10000.00, 1, NULL, NULL),
(447, 'JOB00347', 'BODY WOSH', 500.00, 1, NULL, NULL),
(448, 'JOB00348', 'CARB TUNING GREEN TEST', 250.00, 1, NULL, NULL),
(449, 'JOB00349', 'AIR FILTER CLEANING', 250.00, 1, NULL, NULL),
(450, 'JOB00350', 'REPLACING CUPSET (SCOOTY) FRONT BREAK SHOES', 1500.00, 1, NULL, NULL),
(451, 'JOB00351', 'REPLACING DRIVE BELT', 1000.00, 1, NULL, NULL),
(452, 'JOB00352', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 1000.00, 1, NULL, NULL),
(453, 'JOB00353', 'REPLACING COIL PAG ASSLY', 1800.00, 1, NULL, NULL),
(454, 'JOB00354', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 2000.00, 1, NULL, NULL),
(455, 'JOB00355', 'CLEANING FUEL TANK', 350.00, 1, NULL, NULL),
(456, 'JOB00356', 'NORMAL SERVICE', 2500.00, 1, NULL, NULL),
(457, 'JOB00357', 'BREAK PUMP REPAIR', 500.00, 1, NULL, NULL),
(458, 'JOB00358', 'BODY WOSH', 500.00, 1, NULL, NULL),
(459, 'JOB00359', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 500.00, 1, NULL, NULL),
(460, 'JOB00360', 'CABLE OILING / ADJSTING', 250.00, 1, NULL, NULL),
(461, 'JOB00361', 'GRESSING CUP SET REPLACING FORK OIL', 1000.00, 1, NULL, NULL),
(462, 'JOB00362', 'BREAK PUMP REPAIR', 500.00, 1, NULL, NULL),
(463, 'JOB00363', '3W FULL GRESSING', 850.00, 1, NULL, NULL),
(464, 'JOB00364', '3W REPLACING REAR WHEEL BERING 2SIDE', 2000.00, 1, NULL, NULL),
(465, 'JOB00365', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 750.00, 1, NULL, NULL),
(466, 'JOB00366', 'NORMAL SERVICE 125CC', 2500.00, 1, NULL, NULL),
(467, 'JOB00367', 'REBORING 135CC / REPLACE CAM BEARING', 1750.00, 1, NULL, NULL),
(468, 'JOB00368', 'DE CARB AND VALVE PISTON / REPLACING TIMING CHAIN / REPLACING CLUTCH ASSEMBLY', 4250.00, 1, NULL, NULL),
(469, 'JOB00369', 'REPLACING CHAIN AND SPOKET SET', 550.00, 1, NULL, NULL),
(470, 'JOB00370', 'REPLACING SPOKET HUB BEARING', 250.00, 1, NULL, NULL),
(471, 'JOB00371', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 750.00, 1, NULL, NULL),
(472, 'JOB00372', '3W REPLACING FRONT WHEEL MINOR KIT', 350.00, 1, NULL, NULL),
(473, 'JOB00373', 'CLEANING CARB', 650.00, 1, NULL, NULL),
(474, 'JOB00374', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 450.00, 1, NULL, NULL),
(475, 'JOB00375', 'RE BORING 100 CC', 1650.00, 1, NULL, NULL),
(476, 'JOB00376', 'CRANK FIXING CHARGE 100CC', 1650.00, 1, NULL, NULL),
(477, 'JOB00377', 'ENGINE OVERHAUL 100CC', 7500.00, 1, NULL, NULL),
(478, 'JOB00378', 'BODY WOSH REPAIR CHARGES', 1250.00, 1, NULL, NULL),
(479, 'JOB00379', '1st FREE SERVICE', 2000.00, 1, NULL, NULL),
(480, 'JOB00380', '3W FULL GRESSING', 850.00, 1, NULL, NULL),
(481, 'JOB00381', 'REPAIRING CHARGESS', 550.00, 1, NULL, NULL),
(482, 'JOB00382', 'PAINTING GRIP HANDLE & SILENSOR', 3750.00, 1, NULL, NULL),
(483, 'JOB00383', 'ACCIDENT REPAIR CHARGES', 2925.00, 1, NULL, NULL),
(484, 'JOB00384', '1st FREE SERVICE', 2000.00, 1, NULL, NULL),
(485, 'JOB00385', '1st FREE SERVICE', 2000.00, 1, NULL, NULL),
(486, 'JOB00386', '1st FREE SERVICE', 2000.00, 1, NULL, NULL),
(487, 'JOB00387', 'NORMAL SERVICE SCOOTER', 2650.00, 1, NULL, NULL),
(488, 'JOB00388', '3W REPLACING PIVOT PIN / CUP SET / FRONT WHEEL BERING', 3250.00, 1, NULL, NULL),
(489, 'JOB00389', 'NORMAL SERVICE 135CC', 2500.00, 1, NULL, NULL),
(490, 'JOB00390', 'REPLACING CARB', 350.00, 1, NULL, NULL),
(491, 'JOB00391', 'REPLACING CHAIN AND SPOKET SET', 750.00, 1, NULL, NULL),
(492, 'JOB00392', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 350.00, 1, NULL, NULL),
(493, 'JOB00393', 'REPAIRING CHARGESS', 250.00, 1, NULL, NULL),
(494, 'JOB00394', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 1700.00, 1, NULL, NULL),
(495, 'JOB00395', 'REPAIRING CHARGESS', 350.00, 1, NULL, NULL),
(496, 'JOB00396', 'FULL SERVICE 180CC', 3850.00, 1, NULL, NULL),
(497, 'JOB00397', 'FULL SERVICE (125M GOVERNMENT MODEL)', 2500.00, 1, NULL, NULL),
(498, 'JOB00398', '3W FULL GRESSING', 950.00, 1, NULL, NULL),
(499, 'JOB00399', '3W REPLACING REAR WHEEL BERING 2SIDE', 2500.00, 1, NULL, NULL),
(500, 'JOB00400', 'NORMAL SERVICE SCOOTER', 2650.00, 1, NULL, NULL),
(501, 'JOB00401', 'REPLACING ACC CABLE', 150.00, 1, NULL, NULL),
(502, 'JOB00402', 'REPLACING FRONT DIS PAD', 250.00, 1, NULL, NULL),
(503, 'JOB00403', 'ADJUSTING / OILING DRIVE CHAIN', 100.00, 1, NULL, NULL),
(504, 'JOB00404', 'ADJUSTING CUP SET', 50.00, 1, NULL, NULL),
(505, 'JOB00405', 'CHANGING ENGINE OIL', 100.00, 1, NULL, NULL),
(506, 'JOB00406', 'NORMAL SERVICE', 2500.00, 1, NULL, NULL),
(507, 'JOB00407', 'FULL SERVICE', 3850.00, 1, NULL, NULL),
(508, 'JOB00408', 'CHANGING ENGINE OIL', 100.00, 1, NULL, NULL),
(509, 'JOB00409', 'ADJUSTING / OILING DRIVE CHAIN', 50.00, 1, NULL, NULL),
(510, 'JOB00410', 'BODY WOSH', 550.00, 1, NULL, NULL),
(511, 'JOB00411', 'REPLACING CHAIN AND SPOKET SET', 750.00, 1, NULL, NULL),
(512, 'JOB00412', 'CHANGING ENGINE OIL', 100.00, 1, NULL, NULL),
(513, 'JOB00413', 'REPAIRING CHARGESS', 250.00, 1, NULL, NULL),
(514, 'JOB00414', 'REPLACING GEAR SHAFT OIL SEAL', 150.00, 1, NULL, NULL),
(515, 'JOB00415', 'BODY WOSH', 550.00, 1, NULL, NULL),
(516, 'JOB00416', 'CHANGING ENGINE OIL', 100.00, 1, NULL, NULL),
(517, 'JOB00417', 'ADJUSTING CUP SET', 50.00, 1, NULL, NULL),
(518, 'JOB00418', 'ADJUSTING / OILING DRIVE CHAIN', 100.00, 1, NULL, NULL),
(519, 'JOB00419', '1st FREE SERVICE', 2000.00, 1, NULL, NULL),
(520, 'JOB00420', '3W FULL GRESSING', 850.00, 1, NULL, NULL),
(521, 'JOB00421', '3W FULL SERVICE', 3750.00, 1, NULL, NULL),
(522, 'JOB00422', 'REPLACING CHAIN AND SPOKET SET', 750.00, 1, NULL, NULL),
(523, 'JOB00423', 'KTM 200 NORMAL SERVICE', 2650.00, 1, NULL, NULL),
(524, 'JOB00424', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 1250.00, 1, NULL, NULL),
(525, 'JOB00425', 'FULL SERVICE', 3750.00, 1, NULL, NULL),
(526, 'JOB00426', 'WIRING CHARGESS', 2000.00, 1, NULL, NULL),
(527, 'JOB00427', 'BODY WOSH', 550.00, 1, NULL, NULL),
(528, 'JOB00428', 'REPAIRING CHARGESS', 650.00, 1, NULL, NULL),
(529, 'JOB00429', 'REPLACING HEAD COVER RUBBER', 200.00, 1, NULL, NULL),
(530, 'JOB00430', 'BODY WOSH', 550.00, 1, NULL, NULL),
(531, 'JOB00431', 'BODY WOSH', 550.00, 1, NULL, NULL),
(532, 'JOB00432', 'REPAIRING CHARGESS', 350.00, 1, NULL, NULL),
(533, 'JOB00433', '3W FULL GRESSING', 750.00, 1, NULL, NULL),
(534, 'JOB00434', '3W FULL SERVICE', 3500.00, 1, NULL, NULL),
(535, 'JOB00435', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 500.00, 1, NULL, NULL),
(536, 'JOB00436', 'BODY WOSH', 500.00, 1, NULL, NULL),
(537, 'JOB00437', 'ADJUSTING / OILING DRIVE CHAIN', 100.00, 1, NULL, NULL),
(538, 'JOB00438', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 500.00, 1, NULL, NULL),
(539, 'JOB00439', 'REPLACING CHAIN AND SPOKET SET', 750.00, 1, NULL, NULL),
(540, 'JOB00440', 'CHANGING ENGINE OIL/REPAIR CHARGE', 300.00, 1, NULL, NULL),
(541, 'JOB00441', 'NORMAL SERVICE FZ', 2500.00, 1, NULL, NULL),
(542, 'JOB00442', 'REBORING CHARGE 100CC', 1500.00, 1, NULL, NULL),
(543, 'JOB00443', 'REPLACING CONN ROD KIT', 1500.00, 1, NULL, NULL),
(544, 'JOB00444', 'ENGINE OVERHAUL 100CC', 7500.00, 1, NULL, NULL),
(545, 'JOB00445', 'REPLACING SPARK PLUG', 250.00, 1, NULL, NULL),
(546, 'JOB00446', 'CABLE OILING / ADJUSTING CHAIN / ENGINE OIL CHANGING', 150.00, 1, NULL, NULL),
(547, 'JOB00447', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 700.00, 1, NULL, NULL),
(548, 'JOB00448', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 750.00, 1, NULL, NULL),
(549, 'JOB00449', 'REPLACING FRONT / REAR BRAKE SHOE', 300.00, 1, NULL, NULL),
(550, 'JOB00450', 'NORMAL SERVICE 100CC', 2500.00, 1, NULL, NULL),
(551, 'JOB00451', '3W FULL GRESSING', 850.00, 1, NULL, NULL),
(552, 'JOB00452', '3W TOP / UNDER WOSH /OILING', 2350.00, 1, NULL, NULL),
(553, 'JOB00453', '3W REPLACING PIVOT PIN / CUP SET / FRONT SHOK', 2500.00, 1, NULL, NULL),
(554, 'JOB00454', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 1750.00, 1, NULL, NULL),
(555, 'JOB00455', 'WIRING CHARGESS', 2500.00, 1, NULL, NULL),
(556, 'JOB00456', 'FULL SERVICE', 3750.00, 1, NULL, NULL),
(557, 'JOB00457', 'ENGINE OVERHAUL 125CC', 6500.00, 1, NULL, NULL),
(558, 'JOB00458', '3W FULL SERVICE', 3750.00, 1, NULL, NULL),
(559, 'JOB00459', '3W FULL GRESSING', 850.00, 1, NULL, NULL),
(560, 'JOB00460', 'REPLACING CHAIN AND SPOKET SET', 850.00, 1, NULL, NULL),
(561, 'JOB00461', 'REPAIRING CHARGESS', 3750.00, 1, NULL, NULL),
(562, 'JOB00462', 'FULL SERVICE 100CC', 3500.00, 1, NULL, NULL),
(563, 'JOB00463', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 200.00, 1, NULL, NULL),
(564, 'JOB00464', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 1500.00, 1, NULL, NULL),
(565, 'JOB00465', '3W FULL GRESSING', 850.00, 1, NULL, NULL),
(566, 'JOB00466', 'ADJUSTING CUP SET', 100.00, 1, NULL, NULL),
(567, 'JOB00467', 'REPAIRING CHARGESS', 750.00, 1, NULL, NULL),
(568, 'JOB00468', '3W FULL GRESSING', 750.00, 1, NULL, NULL),
(569, 'JOB00469', '3W REPLACING REAR WHEEL BERING 2SIDE', 2000.00, 1, NULL, NULL),
(570, 'JOB00470', 'WIRING CHARGESS / CONVERT BCU', 5550.00, 1, NULL, NULL),
(571, 'JOB00471', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 2850.00, 1, NULL, NULL),
(572, 'JOB00472', 'CHANGING ENGINE OIL', 100.00, 1, NULL, NULL),
(573, 'JOB00473', 'BODY WOSH', 500.00, 1, NULL, NULL),
(574, 'JOB00474', 'BODY WOSH', 500.00, 1, NULL, NULL),
(575, 'JOB00475', 'FULL SERVICE (125M GOVERNMENT MODEL)', 2500.00, 1, NULL, NULL),
(576, 'JOB00476', 'REPAIRING CHARGESS', 300.00, 1, NULL, NULL),
(577, 'JOB00477', 'BODY WOSH', 500.00, 1, NULL, NULL),
(578, 'JOB00478', 'NORMAL SERVICE 100CC', 2850.00, 1, NULL, NULL),
(579, 'JOB00479', 'FULL SERVICE 200NS', 4250.00, 1, NULL, NULL),
(580, 'JOB00480', 'FORK/COLOM ELIMENT', 2500.00, 1, NULL, NULL),
(581, 'JOB00481', 'ACCIDENT REPAIR CHARGES', 2500.00, 1, NULL, NULL),
(582, 'JOB00482', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 350.00, 1, NULL, NULL),
(583, 'JOB00483', 'BODY WOSH', 500.00, 1, NULL, NULL),
(584, 'JOB00484', 'FULL SERVICE 150CC', 3500.00, 1, NULL, NULL),
(585, 'JOB00485', 'REPAIRING CHARGESS', 1000.00, 1, NULL, NULL),
(586, 'JOB00486', 'WIRING HANESS REPAIRING', 2500.00, 1, NULL, NULL),
(587, 'JOB00487', '2nd FREE SERVICE', 2000.00, 1, NULL, NULL),
(588, 'JOB00488', 'NORMAL SERVICE 125CC', 2500.00, 1, NULL, NULL),
(589, 'JOB00489', 'CLEANING CARB', 500.00, 1, NULL, NULL),
(590, 'JOB00490', 'NORMAL SERVICE 135CC', 2500.00, 1, NULL, NULL),
(591, 'JOB00491', 'BODY WOSH', 550.00, 1, NULL, NULL),
(592, 'JOB00492', 'REPLACING MINOR KIT', 650.00, 1, NULL, NULL),
(593, 'JOB00493', 'ADJUSTING / OILING DRIVE CHAIN', 100.00, 1, NULL, NULL),
(594, 'JOB00494', 'HALF GRESSING', 450.00, 1, NULL, NULL),
(595, 'JOB00495', '3W REPLACING REAR WHEEL BERING', 1250.00, 1, NULL, NULL),
(596, 'JOB00496', 'REPLACING FORK OIL / FORK OIL SEAL', 1250.00, 1, NULL, NULL),
(597, 'JOB00497', '2nd FREE SERVICE', 2000.00, 1, NULL, NULL),
(598, 'JOB00498', 'BODY WOSH', 500.00, 1, NULL, NULL),
(599, 'JOB00499', 'CLEANING CARB', 500.00, 1, NULL, NULL),
(600, 'JOB00500', '3W FULL SERVICE (2 STOCK)', 2750.00, 1, NULL, NULL),
(601, 'JOB00501', '3W FULL GRESSING', 850.00, 1, NULL, NULL),
(602, 'JOB00502', 'REBORING CHARGE 225', 2250.00, 1, NULL, NULL),
(603, 'JOB00503', 'CRANK FIXING CHARGE 225', 2500.00, 1, NULL, NULL),
(604, 'JOB00504', 'VALVE SEAT IN/EX CUTTING CHARGE', 1000.00, 1, NULL, NULL),
(605, 'JOB00505', 'ENGINE OVERHAUL 225', 10000.00, 1, NULL, NULL),
(606, 'JOB00506', 'REPLACING MAGNETO COIL ASSLY', 2000.00, 1, NULL, NULL),
(607, 'JOB00507', 'REPLACING GEAR SHAFT OIL SEAL', 250.00, 1, NULL, NULL),
(608, 'JOB00508', 'CARB TUNING GREEN TEST', 250.00, 1, NULL, NULL),
(609, 'JOB00509', 'REPAIRING CHARGESS', 650.00, 1, NULL, NULL),
(610, 'JOB00510', '3W FULL SERVICE', 3750.00, 1, NULL, NULL),
(611, 'JOB00511', '3W FULL GRESSING', 850.00, 1, NULL, NULL),
(612, 'JOB00512', '2nd FREE SERVICE', 2000.00, 1, NULL, NULL),
(613, 'JOB00513', '1st FREE SERVICE', 2000.00, 1, NULL, NULL),
(614, 'JOB00514', 'REPLACING MINOR KIT', 500.00, 1, NULL, NULL),
(615, 'JOB00515', 'CLEANING CARB', 550.00, 1, NULL, NULL),
(616, 'JOB00516', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 1850.00, 1, NULL, NULL),
(617, 'JOB00517', 'CARB OVERHAUL', 1000.00, 1, NULL, NULL),
(618, 'JOB00518', 'CHANGING ENGINE OIL', 100.00, 1, NULL, NULL),
(619, 'JOB00519', 'START MOTOR REPLACING', 450.00, 1, NULL, NULL),
(620, 'JOB00520', 'REPLACING REAR BREAK SHOE', 250.00, 1, NULL, NULL),
(621, 'JOB00521', '3W FULL GRESSING', 850.00, 1, NULL, NULL),
(622, 'JOB00522', '3W FULL SERVICE', 3750.00, 1, NULL, NULL),
(623, 'JOB00523', '3W TOP / UNDER WOSH /OILING', 2300.00, 1, NULL, NULL),
(624, 'JOB00524', '3W FULL GRESSING / REPLACING CUPLIN RUBBER', 1650.00, 1, NULL, NULL),
(625, 'JOB00525', '3W REPLACING FRONT WHEEL MINOR KIT', 350.00, 1, NULL, NULL),
(626, 'JOB00526', 'REPAIRING CHARGESS', 350.00, 1, NULL, NULL),
(627, 'JOB00527', 'REPLACING TOP GASKET KIT', 2000.00, 1, NULL, NULL),
(628, 'JOB00528', 'NORMAL SERVICE FZ', 2500.00, 1, NULL, NULL),
(629, 'JOB00529', 'WIRING CHARGESS / CONVERT BCU', 2800.00, 1, NULL, NULL),
(630, 'JOB00530', 'BODY WOSH', 550.00, 1, NULL, NULL),
(631, 'JOB00531', 'REPLACING CHAIN AND SPOKET SET', 1000.00, 1, NULL, NULL),
(632, 'JOB00532', '3W TOP / UNDER WOSH /OILING', 2350.00, 1, NULL, NULL),
(633, 'JOB00533', '3W FULL GRESSING / REPLACING CUPLIN RUBBER', 1650.00, 1, NULL, NULL),
(634, 'JOB00534', '3W REPLACING TOP GASKET KIT', 1650.00, 1, NULL, NULL),
(635, 'JOB00535', '3W CABLE OILING / ADJUSTING', 350.00, 1, NULL, NULL),
(636, 'JOB00536', 'BODY WOSH REPAIR CHARGES', 650.00, 1, NULL, NULL),
(637, 'JOB00537', '2nd FREE SERVICE', 2000.00, 1, NULL, NULL),
(638, 'JOB00538', 'START MOTOR REPLACING', 350.00, 1, NULL, NULL),
(639, 'JOB00539', 'BODY WOSH REPAIR CHARGES', 600.00, 1, NULL, NULL),
(640, 'JOB00540', 'REPLACING REAR BREAK SHOE', 250.00, 1, NULL, NULL),
(641, 'JOB00541', 'REPAIRING CHARGESS', 500.00, 1, NULL, NULL),
(642, 'JOB00542', '3W FULL GRESSING / REPLACING CUPLIN RUBBER', 1300.00, 1, NULL, NULL),
(643, 'JOB00543', 'FULL SERVICE 135CC', 3750.00, 1, NULL, NULL),
(644, 'JOB00544', '3W FULL SERVICE', 3750.00, 1, NULL, NULL),
(645, 'JOB00545', '3W FULL GRESSING', 850.00, 1, NULL, NULL),
(646, 'JOB00546', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 1000.00, 1, NULL, NULL),
(647, 'JOB00547', 'ELECTRICAL REPAIRE / CHEAKING / TESTING', 2250.00, 1, NULL, NULL),
(648, 'JOB00548', 'NORMAL SERVICE 180CC', 2500.00, 1, NULL, NULL),
(649, 'JOB00549', 'BODY WOSH', 500.00, 1, NULL, NULL),
(650, 'JOB00550', '1st FREE SERVICE', 2000.00, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_04_21_031838_add_two_factor_columns_to_users_table', 1),
(5, '2025_04_21_031905_create_personal_access_tokens_table', 1),
(6, '2025_05_10_141938_create_permissions_table', 1),
(7, '2025_05_12_020238_create_suppliers_table', 1),
(8, '2025_05_12_110951_create_supplier_groups_table', 1),
(9, '2025_05_13_042146_create_item_table', 1),
(10, '2025_05_13_042237_create_item_location_table', 1),
(11, '2025_05_13_042306_create_item_stock_table', 1),
(12, '2025_05_13_053507_create_category_table', 1),
(13, '2025_05_14_044109_create_products_table', 1),
(14, '2025_05_14_141409_create_po_table', 1),
(15, '2025_05_15_113044_create_po__item_table', 1),
(16, '2025_06_14_032046_create_grn_table', 2),
(17, '2025_06_14_032234_create_grn_items_table', 2),
(18, '2025_06_14_032644_create_stock_table', 2),
(19, '2025_06_14_083551_create_purchase_returns_table', 3),
(20, '2025_06_14_083938_create_purchase_return_items_table', 3),
(21, '2025_06_14_100308_add_cost_and_discount_to_grn_items_table', 4),
(24, '2025_06_16_160147_create_vehicle_routes_table', 5),
(25, '2025_06_16_160204_create_vehicle_brands_table', 5),
(26, '2025_06_25_143241_create_job_types_table', 6),
(27, '2025_06_28_051753_create_quotations_table', 7),
(28, '2025_06_28_051820_create_quotation_items_table', 7),
(29, '2025_06_29_200030_add_soft_deletes_to_quotations_table', 8),
(30, '2025_06_29_200202_add_soft_deletes_to_quotation_items_table', 9),
(31, '2025_06_29_202036_create_sales_invoices_table', 10),
(32, '2025_06_29_202049_create_sales_invoice_items_table', 10),
(33, '2025_01_21_000001_create_invoice_returns_table', 11),
(34, '2025_01_21_000002_create_invoice_return_items_table', 11),
(35, '2025_06_30_043550_create_payment_methods_table', 12),
(36, '2025_06_30_043559_create_bank_accounts_table', 12),
(37, '2025_06_30_043604_create_payment_categories_table', 12),
(38, '2025_06_30_044118_create_customers_table', 13),
(39, '2025_06_30_044327_add_index_to_customers_custom_id', 14),
(40, '2025_06_30_043607_create_payment_transactions_table', 15),
(41, '2025_06_30_043753_seed_payment_default_data', 16),
(42, '2025_06_30_104035_add_invoice_return_id_to_payment_transactions_table', 17),
(43, '2025_06_30_112908_add_grn_id_to_payment_transactions_table', 18),
(44, '2025_06_30_124726_add_purchase_return_id_to_payment_transactions_table', 19),
(45, '2025_06_30_170415_add_cost_value_to_stock_table', 20),
(46, '2025_01_22_000001_create_service_invoices_table', 21),
(47, '2025_01_22_000002_create_service_invoice_items_table', 21),
(48, '2025_06_30_173719_add_mileage_to_service_invoices_table', 22),
(49, '2025_07_01_033406_add_service_invoice_id_to_payment_transactions_table', 23),
(50, '2025_07_01_085413_create_customer_logins_table', 24);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_categories`
--

CREATE TABLE `payment_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('income','expense') COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_categories`
--

INSERT INTO `payment_categories` (`id`, `name`, `code`, `type`, `parent_id`, `description`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Sales Revenue', 'SALES_REV', 'income', NULL, 'Revenue from sales invoices', 1, 1, '2025-06-29 23:16:47', '2025-06-29 23:16:47'),
(2, 'Customer Payments', 'CUST_PAY', 'income', NULL, 'Payments received from customers', 1, 2, '2025-06-29 23:16:47', '2025-06-29 23:16:47'),
(3, 'Other Income', 'OTHER_INC', 'income', NULL, 'Miscellaneous income', 1, 3, '2025-06-29 23:16:47', '2025-06-29 23:16:47'),
(4, 'Interest Income', 'INT_INC', 'income', NULL, 'Interest earned on bank accounts', 1, 4, '2025-06-29 23:16:47', '2025-06-29 23:16:47'),
(5, 'Supplier Payments', 'SUPP_PAY', 'expense', NULL, 'Payments made to suppliers', 1, 1, '2025-06-29 23:16:47', '2025-06-29 23:16:47'),
(6, 'Operating Expenses', 'OP_EXP', 'expense', NULL, 'Day-to-day operating expenses', 1, 2, '2025-06-29 23:16:47', '2025-06-29 23:16:47'),
(7, 'Salary & Wages', 'SALARY', 'expense', NULL, 'Employee salaries and wages', 1, 3, '2025-06-29 23:16:47', '2025-06-29 23:16:47'),
(8, 'Utilities', 'UTILITIES', 'expense', NULL, 'Electricity, water, internet, phone bills', 1, 4, '2025-06-29 23:16:47', '2025-06-29 23:16:47'),
(9, 'Rent & Lease', 'RENT', 'expense', NULL, 'Office rent and equipment lease', 1, 5, '2025-06-29 23:16:47', '2025-06-29 23:16:47'),
(10, 'Transportation', 'TRANSPORT', 'expense', NULL, 'Vehicle fuel, maintenance, transport costs', 1, 6, '2025-06-29 23:16:47', '2025-06-29 23:16:47'),
(11, 'Office Supplies', 'OFFICE_SUP', 'expense', NULL, 'Stationery, office equipment, supplies', 1, 7, '2025-06-29 23:16:47', '2025-06-29 23:16:47'),
(12, 'Professional Services', 'PROF_SERV', 'expense', NULL, 'Legal, accounting, consulting fees', 1, 8, '2025-06-29 23:16:47', '2025-06-29 23:16:47'),
(13, 'Marketing & Advertising', 'MARKETING', 'expense', NULL, 'Marketing and advertising expenses', 1, 9, '2025-06-29 23:16:47', '2025-06-29 23:16:47'),
(14, 'Insurance', 'INSURANCE', 'expense', NULL, 'Business insurance premiums', 1, 10, '2025-06-29 23:16:47', '2025-06-29 23:16:47'),
(15, 'Bank Charges', 'BANK_CHG', 'expense', NULL, 'Bank fees and charges', 1, 11, '2025-06-29 23:16:47', '2025-06-29 23:16:47'),
(16, 'Other Expenses', 'OTHER_EXP', 'expense', NULL, 'Miscellaneous expenses', 1, 12, '2025-06-29 23:16:47', '2025-06-29 23:16:47'),
(19, 'Service Income', 'service_income', 'income', NULL, 'Income from service invoices', 1, 1, '2025-07-01 03:01:35', '2025-07-01 03:01:35');

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `requires_reference` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name`, `code`, `description`, `is_active`, `requires_reference`, `created_at`, `updated_at`) VALUES
(1, 'Cash', 'CASH', 'Cash payments and receipts', 1, 0, '2025-06-29 23:16:47', '2025-06-29 23:16:47'),
(2, 'Bank Transfer', 'BANK', 'Bank to bank transfers', 1, 1, '2025-06-29 23:16:47', '2025-06-29 23:16:47'),
(3, 'Check', 'CHECK', 'Check payments', 1, 1, '2025-06-29 23:16:47', '2025-06-29 23:16:47'),
(4, 'Credit Card', 'CARD', 'Credit/Debit card payments', 1, 1, '2025-06-29 23:16:47', '2025-06-29 23:16:47'),
(5, 'Digital Wallet', 'WALLET', 'Digital wallet payments (PayPal, etc.)', 1, 1, '2025-06-29 23:16:47', '2025-06-29 23:16:47');

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `transaction_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('cash_in','cash_out') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `transaction_date` date NOT NULL,
  `transaction_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method_id` bigint UNSIGNED NOT NULL,
  `bank_account_id` bigint UNSIGNED DEFAULT NULL,
  `payment_category_id` bigint UNSIGNED NOT NULL,
  `customer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sales_invoice_id` bigint UNSIGNED DEFAULT NULL,
  `purchase_order_id` smallint UNSIGNED DEFAULT NULL,
  `service_invoice_id` bigint UNSIGNED DEFAULT NULL,
  `grn_id` smallint UNSIGNED DEFAULT NULL,
  `purchase_return_id` bigint UNSIGNED DEFAULT NULL,
  `invoice_return_id` bigint UNSIGNED DEFAULT NULL,
  `status` enum('draft','pending','approved','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `approved_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_by` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `attachments` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_transactions`
--

INSERT INTO `payment_transactions` (`id`, `transaction_no`, `type`, `amount`, `transaction_date`, `transaction_time`, `description`, `reference_no`, `payment_method_id`, `bank_account_id`, `payment_category_id`, `customer_id`, `supplier_id`, `sales_invoice_id`, `purchase_order_id`, `service_invoice_id`, `grn_id`, `purchase_return_id`, `invoice_return_id`, `status`, `approved_by`, `approved_at`, `created_by`, `updated_by`, `notes`, `attachments`, `created_at`, `updated_at`) VALUES
(1, 'TXN202506300001', 'cash_in', 5000.00, '2025-06-30', '2025-06-30 05:38:40', 'Test cash in transaction', NULL, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completed', NULL, NULL, 'System Test', NULL, NULL, NULL, '2025-06-30 00:08:40', '2025-06-30 00:08:40'),
(2, 'TXN202506300002', 'cash_in', 15000.00, '2025-06-30', '2025-06-30 05:40:23', 'Customer payment for invoice', NULL, 2, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completed', NULL, NULL, 'System Test', NULL, NULL, NULL, '2025-06-30 00:10:23', '2025-06-30 00:10:23'),
(3, 'TXN202506300003', 'cash_in', 1070.00, '2025-06-30', '2025-06-30 08:03:16', 'Payment for Invoice INV000019', NULL, 1, NULL, 2, NULL, NULL, 19, NULL, NULL, NULL, NULL, NULL, 'completed', 'User', '2025-06-30 08:03:16', 'User', NULL, NULL, NULL, '2025-06-30 02:33:16', '2025-06-30 02:33:16'),
(4, 'TXN202506300004', 'cash_in', 2500.00, '2025-06-30', '2025-06-30 08:10:58', 'Payment for Invoice INV000020', NULL, 1, NULL, 2, NULL, NULL, 20, NULL, NULL, NULL, NULL, NULL, 'completed', 'User', '2025-06-30 08:10:58', 'User', NULL, NULL, NULL, '2025-06-30 02:40:58', '2025-06-30 02:40:58'),
(5, 'TXN202506300005', 'cash_in', 2500.00, '2025-06-30', '2025-06-30 08:11:48', 'Payment for Invoice INV000022', NULL, 1, NULL, 2, NULL, NULL, 22, NULL, NULL, NULL, NULL, NULL, 'completed', 'User', '2025-06-30 08:11:48', 'User', NULL, NULL, NULL, '2025-06-30 02:41:48', '2025-06-30 02:41:48'),
(6, 'TXN202506300006', 'cash_in', 1070.00, '2025-06-30', '2025-06-30 09:02:39', 'Payment for Invoice INV000029', NULL, 2, 1, 2, NULL, NULL, 29, NULL, NULL, NULL, NULL, NULL, 'completed', 'User', '2025-06-30 09:02:39', 'User', NULL, NULL, NULL, '2025-06-30 03:32:39', '2025-06-30 03:32:39'),
(7, 'TXN202506300007', 'cash_in', 1070.00, '2025-06-30', '2025-06-30 09:03:27', 'Payment for Invoice INV000030', NULL, 1, NULL, 2, NULL, NULL, 30, NULL, NULL, NULL, NULL, NULL, 'completed', 'User', '2025-06-30 09:03:27', 'User', NULL, NULL, NULL, '2025-06-30 03:33:27', '2025-06-30 03:33:27'),
(8, 'TXN202506300008', 'cash_in', 1070.00, '2025-06-30', '2025-06-30 09:06:20', 'Payment for Invoice INV000031', NULL, 2, 1, 2, NULL, NULL, 31, NULL, NULL, NULL, NULL, NULL, 'completed', 'User', '2025-06-30 09:06:20', 'User', NULL, NULL, NULL, '2025-06-30 03:36:20', '2025-06-30 03:36:20'),
(9, 'TXN202506300009', 'cash_in', 750.00, '2025-06-30', '2025-06-30 09:15:16', 'Payment for Invoice INV000032', NULL, 1, NULL, 2, NULL, NULL, 32, NULL, NULL, NULL, NULL, NULL, 'completed', 'User', '2025-06-30 09:15:16', 'User', NULL, NULL, NULL, '2025-06-30 03:45:16', '2025-06-30 03:45:16'),
(10, 'TXN202506300010', 'cash_in', 320.00, '2025-06-30', '2025-06-30 09:32:05', 'Payment for Invoice INV000032', NULL, 2, 1, 2, NULL, NULL, 32, NULL, NULL, NULL, NULL, NULL, 'completed', 'User', '2025-06-30 09:32:05', 'User', NULL, NULL, NULL, '2025-06-30 04:02:05', '2025-06-30 04:02:05'),
(11, 'TXN202506300011', 'cash_in', 1070.00, '2025-06-30', '2025-06-30 09:32:45', 'Payment for Invoice INV000028', NULL, 1, NULL, 2, NULL, NULL, 28, NULL, NULL, NULL, NULL, NULL, 'completed', 'User', '2025-06-30 09:32:45', 'User', NULL, NULL, NULL, '2025-06-30 04:02:45', '2025-06-30 04:02:45'),
(12, 'TXN202506300012', 'cash_in', 1070.00, '2025-06-30', '2025-06-30 09:32:57', 'Payment for Invoice INV000027', NULL, 1, NULL, 2, NULL, NULL, 27, NULL, NULL, NULL, NULL, NULL, 'completed', 'User', '2025-06-30 09:32:57', 'User', NULL, NULL, NULL, '2025-06-30 04:02:57', '2025-06-30 04:02:57'),
(13, 'TXN202506300013', 'cash_in', 1070.00, '2025-06-30', '2025-06-30 09:40:02', 'Payment for Invoice INV000026', NULL, 1, NULL, 2, NULL, NULL, 26, NULL, NULL, NULL, NULL, NULL, 'completed', 'User', '2025-06-30 09:40:02', 'User', NULL, NULL, NULL, '2025-06-30 04:10:02', '2025-06-30 04:10:02'),
(14, 'TXN202506300014', 'cash_in', 70.00, '2025-06-30', '2025-06-30 10:16:15', 'Payment for Invoice INV000033', NULL, 1, NULL, 2, NULL, NULL, 33, NULL, NULL, NULL, NULL, NULL, 'completed', 'User', '2025-06-30 10:16:15', 'User', NULL, NULL, NULL, '2025-06-30 04:46:15', '2025-06-30 04:46:15'),
(15, 'TXN202506300015', 'cash_in', 20.00, '2025-06-30', '2025-06-30 10:17:55', 'Payment for Invoice INV000033', NULL, 1, NULL, 2, NULL, NULL, 33, NULL, NULL, NULL, NULL, NULL, 'completed', 'User', '2025-06-30 10:17:55', 'User', NULL, NULL, NULL, '2025-06-30 04:47:55', '2025-06-30 04:47:55'),
(16, 'TXN202506300016', 'cash_in', 80.00, '2025-06-30', '2025-06-30 10:26:47', 'Payment for Invoice INV000033', NULL, 1, NULL, 2, NULL, NULL, 33, NULL, NULL, NULL, NULL, NULL, 'completed', 'User', '2025-06-30 10:26:47', 'User', NULL, NULL, NULL, '2025-06-30 04:56:47', '2025-06-30 04:56:47'),
(17, 'TXN202506300017', 'cash_in', 7500.00, '2025-06-30', '2025-06-30 11:00:13', 'Payment for Invoice INV000005', NULL, 1, NULL, 2, NULL, NULL, 5, NULL, NULL, NULL, NULL, NULL, 'completed', 'Admin', '2025-06-30 11:00:13', 'Admin', NULL, NULL, NULL, '2025-06-30 05:30:13', '2025-06-30 05:30:13'),
(18, 'TXN202506300018', 'cash_out', 2500.00, '2025-06-30', '2025-06-30 11:01:01', 'Refund for Return #RTN000003 (Invoice #INV000005)', 'RTN000003', 1, NULL, 6, 'CUST000002', NULL, 5, NULL, NULL, NULL, NULL, 3, 'completed', 'Admin', '2025-06-30 11:01:01', 'Admin', NULL, NULL, NULL, '2025-06-30 05:31:01', '2025-06-30 05:31:01'),
(19, 'TXN202506300019', 'cash_out', 7500.00, '2025-06-30', '2025-06-30 11:31:48', 'Payment for GRN GRN000006', NULL, 1, NULL, 5, NULL, 'RAM', NULL, NULL, NULL, 7, NULL, NULL, 'completed', 'Admin', '2025-06-30 11:31:48', 'Admin', NULL, NULL, NULL, '2025-06-30 06:01:48', '2025-06-30 06:01:48'),
(20, 'TXN202506300020', 'cash_out', 1750.00, '2025-06-30', '2025-06-30 11:59:16', 'Payment for GRN GRN000011', NULL, 1, NULL, 5, NULL, 'AM', NULL, NULL, NULL, 12, NULL, NULL, 'completed', 'Admin', '2025-06-30 11:59:16', 'Admin', NULL, NULL, NULL, '2025-06-30 06:29:16', '2025-06-30 06:29:16'),
(21, 'TXN202506300021', 'cash_out', 700.00, '2025-06-30', '2025-06-30 12:00:13', 'Payment for GRN GRN000012', NULL, 1, NULL, 5, NULL, 'AM', NULL, NULL, NULL, 13, NULL, NULL, 'completed', 'Admin', '2025-06-30 12:00:13', 'Admin', NULL, NULL, NULL, '2025-06-30 06:30:13', '2025-06-30 06:30:13'),
(22, 'TXN202506300022', 'cash_out', 350.00, '2025-06-30', '2025-06-30 12:10:53', 'Payment for GRN GRN000015', NULL, 1, NULL, 5, NULL, 'AM', NULL, NULL, NULL, 16, NULL, NULL, 'completed', 'Admin', '2025-06-30 12:10:53', 'Admin', NULL, NULL, NULL, '2025-06-30 06:40:53', '2025-06-30 06:40:53'),
(23, 'TXN202506300023', 'cash_out', 350.00, '2025-06-30', '2025-06-30 12:21:46', 'Payment for GRN GRN000017', NULL, 1, NULL, 5, NULL, 'AM', NULL, NULL, NULL, 18, NULL, NULL, 'completed', 'Admin', '2025-06-30 12:21:46', 'Admin', NULL, NULL, NULL, '2025-06-30 06:51:46', '2025-06-30 06:51:46'),
(24, 'TXN202506300024', 'cash_out', 350.00, '2025-06-30', '2025-06-30 12:22:34', 'Payment for GRN GRN000018', NULL, 1, NULL, 5, NULL, 'AM', NULL, NULL, NULL, 19, NULL, NULL, 'completed', 'Admin', '2025-06-30 12:22:34', 'Admin', NULL, NULL, NULL, '2025-06-30 06:52:34', '2025-06-30 06:52:34'),
(25, 'TXN202506300025', 'cash_out', 350.00, '2025-06-30', '2025-06-30 12:32:14', 'Payment for GRN GRN000019', NULL, 1, NULL, 5, NULL, 'AM', NULL, NULL, NULL, 20, NULL, NULL, 'completed', 'Admin', '2025-06-30 12:32:14', 'Admin', NULL, NULL, NULL, '2025-06-30 07:02:14', '2025-06-30 07:02:14'),
(26, 'TXN202506300026', 'cash_out', 350.00, '2025-06-30', '2025-06-30 12:40:57', 'Payment for GRN GRN000016', NULL, 1, NULL, 5, NULL, 'AM', NULL, NULL, NULL, 17, NULL, NULL, 'completed', 'Admin', '2025-06-30 12:40:57', 'Admin', NULL, NULL, NULL, '2025-06-30 07:10:57', '2025-06-30 07:10:57'),
(27, 'TXN202506300027', 'cash_out', 2250.00, '2025-06-30', '2025-06-30 13:07:41', 'Payment for Purchase Return PR000002', NULL, 1, NULL, 5, NULL, 'DPMC', NULL, NULL, NULL, NULL, 2, NULL, 'completed', NULL, NULL, 'Admin', NULL, NULL, NULL, '2025-06-30 07:37:41', '2025-06-30 07:37:41'),
(28, 'TXN202506300028', 'cash_in', 350.00, '2025-06-30', '2025-06-30 13:23:46', 'Refund for Purchase Return PR000007', NULL, 1, NULL, 5, NULL, 'AM', NULL, NULL, NULL, NULL, 7, NULL, 'completed', NULL, NULL, 'Admin', NULL, NULL, NULL, '2025-06-30 07:53:46', '2025-06-30 07:53:46'),
(29, 'TXN202506300029', 'cash_out', 1662.50, '2025-06-30', '2025-06-30 16:40:45', 'Payment for GRN GRN000020', NULL, 1, NULL, 5, NULL, 'AM', NULL, NULL, NULL, 21, NULL, NULL, 'completed', 'Admin', '2025-06-30 16:40:45', 'Admin', NULL, NULL, NULL, '2025-06-30 11:10:45', '2025-06-30 11:10:45'),
(30, 'TXN202506300030', 'cash_in', 332.50, '2025-06-30', '2025-06-30 17:00:33', 'Refund for Purchase Return PR000008', NULL, 1, NULL, 5, NULL, 'AM', NULL, NULL, NULL, NULL, 10, NULL, 'completed', NULL, NULL, 'Admin', NULL, NULL, NULL, '2025-06-30 11:30:33', '2025-06-30 11:30:33'),
(31, 'TXN202506300031', 'cash_out', 3325.00, '2025-06-30', '2025-06-30 17:07:56', 'Payment for GRN GRN000021', NULL, 1, NULL, 5, NULL, 'DPMC', NULL, NULL, NULL, 22, NULL, NULL, 'completed', 'Admin', '2025-06-30 17:07:56', 'Admin', NULL, NULL, NULL, '2025-06-30 11:37:56', '2025-06-30 11:37:56'),
(34, 'TXN202507010001', 'cash_in', 350.00, '2025-07-01', '2025-07-01 08:31:35', 'Payment for service invoice #SRV000011', NULL, 1, NULL, 19, 'CUST000008', NULL, NULL, NULL, 11, NULL, NULL, NULL, 'completed', NULL, NULL, 'Admin', NULL, NULL, NULL, '2025-07-01 03:01:35', '2025-07-01 03:01:35'),
(35, 'TXN202507010002', 'cash_in', 1420.00, '2025-07-01', '2025-07-01 08:32:33', 'Payment for service invoice #SRV000012', NULL, 1, NULL, 19, 'CUST000001', NULL, NULL, NULL, 12, NULL, NULL, NULL, 'completed', NULL, NULL, 'Admin', NULL, NULL, NULL, '2025-07-01 03:02:33', '2025-07-01 03:02:33');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `usertype` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `suppliers` tinyint(1) NOT NULL DEFAULT '0',
  `products` tinyint(1) NOT NULL DEFAULT '0',
  `purchaseOrder` tinyint(1) NOT NULL DEFAULT '0',
  `recevingGRN` tinyint(1) NOT NULL DEFAULT '0',
  `purchaseReturn` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `po`
--

CREATE TABLE `po` (
  `po_Auto_ID` smallint UNSIGNED NOT NULL,
  `po_No` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `po_date` date NOT NULL,
  `supp_Cus_ID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `grand_Total` double NOT NULL,
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Reff_No` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emp_Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orderStatus` enum('draft','pending','approved','received','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `po`
--

INSERT INTO `po` (`po_Auto_ID`, `po_No`, `po_date`, `supp_Cus_ID`, `grand_Total`, `note`, `Reff_No`, `emp_Name`, `orderStatus`, `created_at`, `updated_at`, `status`) VALUES
(2, 'PO000001', '2025-06-06', 'RAM', 29190, NULL, NULL, 'Manura', 'draft', '2025-06-05 15:11:24', '2025-06-07 06:26:07', 1),
(3, 'PO000002', '2025-06-06', 'RAM', 21940, NULL, NULL, 'Manura', 'pending', '2025-06-05 18:05:43', '2025-06-07 06:38:01', 1),
(4, 'PO000003', '2025-06-07', 'AM', 27250, 'ffff', 'dddd', 'Manura', 'pending', '2025-06-07 06:38:38', '2025-06-07 06:56:41', 0),
(5, 'PO000004', '2025-06-17', 'DnD', 7500, NULL, NULL, NULL, 'pending', '2025-06-16 19:42:23', '2025-06-16 19:42:23', 1);

-- --------------------------------------------------------

--
-- Table structure for table `po__item`
--

CREATE TABLE `po__item` (
  `po_Item_Auto_ID` smallint UNSIGNED NOT NULL,
  `po_Auto_ID` smallint NOT NULL,
  `po_No` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `list_No` smallint NOT NULL,
  `item_ID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` smallint NOT NULL,
  `price` double NOT NULL,
  `line_Total` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `po__item`
--

INSERT INTO `po__item` (`po_Item_Auto_ID`, `po_Auto_ID`, `po_No`, `list_No`, `item_ID`, `qty`, `price`, `line_Total`, `created_at`, `updated_at`, `status`) VALUES
(1, 1, 'PO000001', 1, '36DJ4031', 2, 1070, 2140, '2025-06-05 05:57:32', '2025-06-05 05:57:32', 1),
(26, 2, 'PO000001', 1, '36DJ4031', 17, 1070, 18190, '2025-06-07 06:26:07', '2025-06-07 06:26:07', 1),
(27, 2, 'PO000001', 2, 'DD121181', 5, 450, 2250, '2025-06-07 06:26:07', '2025-06-07 06:26:07', 1),
(28, 2, 'PO000001', 3, 'EO', 2, 2500, 5000, '2025-06-07 06:26:07', '2025-06-07 06:26:07', 1),
(29, 2, 'PO000001', 4, 'DD111018', 5, 750, 3750, '2025-06-07 06:26:07', '2025-06-07 06:26:07', 1),
(30, 3, 'PO000002', 1, '36DJ4031', 17, 1070, 18190, '2025-06-07 06:38:01', '2025-06-07 06:38:01', 1),
(31, 3, 'PO000002', 2, 'DD121181', 5, 450, 2250, '2025-06-07 06:38:01', '2025-06-07 06:38:01', 1),
(32, 3, 'PO000002', 3, 'DD111018', 2, 750, 1500, '2025-06-07 06:38:01', '2025-06-07 06:38:01', 1),
(35, 4, 'PO000003', 1, 'EO', 10, 2500, 25000, '2025-06-07 06:56:41', '2025-06-07 06:56:41', 1),
(36, 4, 'PO000003', 2, 'DD121181', 5, 450, 2250, '2025-06-07 06:56:41', '2025-06-07 06:56:41', 1),
(37, 5, 'PO000004', 1, 'DJ151089', 5, 500, 2500, '2025-06-16 19:42:23', '2025-06-16 19:42:23', 1),
(38, 5, 'PO000004', 2, 'EO', 2, 2500, 5000, '2025-06-16 19:42:23', '2025-06-16 19:42:23', 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `item_ID_Auto` smallint UNSIGNED NOT NULL,
  `item_ID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_Type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `catagory_Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sales_Price` decimal(15,2) NOT NULL,
  `units` double NOT NULL,
  `unitofMeture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_returns`
--

CREATE TABLE `purchase_returns` (
  `id` bigint UNSIGNED NOT NULL,
  `return_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `grn_id` smallint UNSIGNED NOT NULL,
  `grn_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `supp_Cus_ID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `returned_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_returns`
--

INSERT INTO `purchase_returns` (`id`, `return_no`, `grn_id`, `grn_no`, `supp_Cus_ID`, `note`, `returned_by`, `status`, `created_at`, `updated_at`) VALUES
(1, 'PR000001', 2, 'GRN000001', 'DPMC', NULL, 'Manura Madhushan', 1, '2025-06-13 22:34:54', '2025-06-13 22:34:54'),
(2, 'PR000002', 3, 'GRN000002', 'DPMC', NULL, 'Manura Madhushan', 1, '2025-06-13 22:44:58', '2025-06-13 22:44:58'),
(3, 'PR000003', 14, 'GRN000013', 'AM', NULL, 'Admin', 1, '2025-06-30 07:48:10', '2025-06-30 07:48:10'),
(4, 'PR000004', 20, 'GRN000019', 'AM', NULL, 'Admin', 1, '2025-06-30 07:48:39', '2025-06-30 07:48:39'),
(5, 'PR000005', 20, 'GRN000019', 'AM', NULL, 'Admin', 1, '2025-06-30 07:48:53', '2025-06-30 07:48:53'),
(6, 'PR000006', 20, 'GRN000019', 'AM', NULL, 'Admin', 1, '2025-06-30 07:49:10', '2025-06-30 07:49:10'),
(7, 'PR000007', 20, 'GRN000019', 'AM', NULL, 'Admin', 1, '2025-06-30 07:53:25', '2025-06-30 07:53:25'),
(10, 'PR000008', 21, 'GRN000020', 'AM', NULL, 'Admin', 1, '2025-06-30 11:30:26', '2025-06-30 11:30:26');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_items`
--

CREATE TABLE `purchase_return_items` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_return_id` bigint UNSIGNED NOT NULL,
  `item_ID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty_returned` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `line_total` decimal(12,2) NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_return_items`
--

INSERT INTO `purchase_return_items` (`id`, `purchase_return_id`, `item_ID`, `item_Name`, `qty_returned`, `price`, `line_total`, `reason`, `created_at`, `updated_at`) VALUES
(1, 1, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 2500.00, 'Seal Brocken', '2025-06-13 22:34:54', '2025-06-13 22:34:54'),
(2, 2, 'DD121181', 'OIL FILTER WITH SPRING', 5, 450.00, 2250.00, NULL, '2025-06-13 22:44:58', '2025-06-13 22:44:58'),
(3, 3, 'AA121006', 'OIL FILTER ELEMENT 3W', 2, 350.00, 700.00, 'wrong item', '2025-06-30 07:48:10', '2025-06-30 07:48:10'),
(4, 4, 'AA121006', 'OIL FILTER ELEMENT 3W', 1, 350.00, 350.00, 'wrong item', '2025-06-30 07:48:39', '2025-06-30 07:48:39'),
(5, 5, 'AA121006', 'OIL FILTER ELEMENT 3W', 1, 350.00, 350.00, 'wrong item', '2025-06-30 07:48:53', '2025-06-30 07:48:53'),
(6, 6, 'AA121006', 'OIL FILTER ELEMENT 3W', 1, 350.00, 350.00, 'wrong item', '2025-06-30 07:49:10', '2025-06-30 07:49:10'),
(7, 7, 'AA121006', 'OIL FILTER ELEMENT 3W', 1, 350.00, 350.00, 'wrong item', '2025-06-30 07:53:25', '2025-06-30 07:53:25'),
(10, 10, 'AA121006', 'OIL FILTER ELEMENT 3W', 1, 332.50, 332.50, 'wrong item', '2025-06-30 11:30:26', '2025-06-30 11:30:26');

-- --------------------------------------------------------

--
-- Table structure for table `quotations`
--

CREATE TABLE `quotations` (
  `id` bigint UNSIGNED NOT NULL,
  `quotation_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_custom_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quotation_date` date NOT NULL,
  `grand_total` decimal(10,2) NOT NULL,
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quotations`
--

INSERT INTO `quotations` (`id`, `quotation_no`, `customer_custom_id`, `vehicle_no`, `quotation_date`, `grand_total`, `note`, `created_by`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'QT00001', 'CUST000004', 'MF-7636', '2025-06-28', 2850.00, NULL, 'Admin', 1, '2025-06-28 00:16:50', '2025-06-28 00:16:50', NULL),
(2, 'QT00002', 'CUST000006', 'BSC-8156', '2025-06-28', 2950.00, NULL, 'Admin', 1, '2025-06-28 00:29:57', '2025-06-29 14:33:21', '2025-06-29 14:33:21'),
(3, 'QT00003', 'CUST000005', 'BSC-8168', '2025-06-28', 2500.00, NULL, 'Admin', 1, '2025-06-28 07:23:36', '2025-06-28 07:23:36', NULL),
(4, 'QT00004', 'CUST000005', 'BSC-8168', '2025-06-28', 1400.00, NULL, 'Admin', 1, '2025-06-28 07:30:59', '2025-06-29 14:20:05', NULL),
(5, 'QT00005', 'CUST000001', 'BSC-8167', '2025-07-01', 1070.00, NULL, 'Admin', 1, '2025-07-01 02:26:34', '2025-07-01 02:26:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `quotation_items`
--

CREATE TABLE `quotation_items` (
  `id` bigint UNSIGNED NOT NULL,
  `quotation_id` bigint UNSIGNED NOT NULL,
  `line_no` int UNSIGNED NOT NULL,
  `item_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qty` int NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL,
  `line_total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quotation_items`
--

INSERT INTO `quotation_items` (`id`, `quotation_id`, `line_no`, `item_type`, `item_id`, `description`, `qty`, `price`, `line_total`, `created_at`, `updated_at`, `status`, `deleted_at`) VALUES
(1, 1, 1, 'spare', 'EO', 'EO - 20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 2500.00, '2025-06-28 00:16:50', '2025-06-28 00:16:50', 1, NULL),
(2, 1, 2, 'job', 'JOB00002', 'REPLACING MAJOR KIT', 1, 350.00, 350.00, '2025-06-28 00:16:50', '2025-06-28 00:16:50', 1, NULL),
(3, 2, 1, 'spare', 'EO', 'EO - 20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 2500.00, '2025-06-28 00:29:57', '2025-06-29 14:33:21', 1, '2025-06-29 14:33:21'),
(4, 2, 2, 'spare', 'DD121181', 'DD121181 - OIL FILTER WITH SPRING', 1, 450.00, 450.00, '2025-06-28 00:29:57', '2025-06-29 14:33:21', 1, '2025-06-29 14:33:21'),
(5, 3, 1, 'spare', 'EO', 'EO - 20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 2500.00, '2025-06-28 07:23:36', '2025-06-28 07:23:36', 1, NULL),
(7, 4, 1, 'spare', 'DD121181', 'DD121181 - OIL FILTER WITH SPRING', 1, 450.00, 450.00, '2025-06-29 14:20:05', '2025-06-29 14:20:05', 1, NULL),
(8, 4, 2, 'spare', 'ASKBS0104', 'ASKBS0104 - BRAKE SHOES PULSAR DTSI', 1, 950.00, 950.00, '2025-06-29 14:20:05', '2025-06-29 14:20:05', 1, NULL),
(9, 5, 1, 'spare', '36DJ4031', '36DJ4031 - KIT END -KIT MJR MAST CYL & GR', 1, 1070.00, 1070.00, '2025-07-01 02:26:34', '2025-07-01 02:26:34', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sales_invoices`
--

CREATE TABLE `sales_invoices` (
  `id` bigint UNSIGNED NOT NULL,
  `invoice_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_date` date NOT NULL,
  `grand_total` decimal(10,2) NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('hold','finalized') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hold',
  `created_by` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales_invoices`
--

INSERT INTO `sales_invoices` (`id`, `invoice_no`, `customer_id`, `invoice_date`, `grand_total`, `notes`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'INV000001', 'CUST000001', '2025-06-29', 2500.00, NULL, 'finalized', 'User', '2025-06-29 15:07:59', '2025-06-29 15:08:13'),
(2, 'INV000002', 'CUST000003', '2025-06-29', 2500.00, NULL, 'finalized', 'User', '2025-06-29 15:08:43', '2025-06-29 15:08:43'),
(3, 'INV000003', 'CUST000001', '2025-06-29', 2500.00, NULL, 'finalized', 'User', '2025-06-29 15:21:48', '2025-06-29 15:21:48'),
(4, 'INV000004', 'CUST000008', '2025-06-29', 2375.00, NULL, 'finalized', 'User', '2025-06-29 15:26:31', '2025-06-29 15:26:31'),
(5, 'INV000005', 'CUST000002', '2025-06-29', 7500.00, NULL, 'finalized', 'User', '2025-06-29 16:17:43', '2025-06-29 16:17:43'),
(6, 'INV000006', 'CUST000002', '2025-06-30', 2500.00, NULL, 'finalized', 'User', '2025-06-30 01:44:20', '2025-06-30 01:44:20'),
(7, 'INV000007', 'CUST000001', '2025-06-30', 2500.00, NULL, 'finalized', 'User', '2025-06-30 01:49:10', '2025-06-30 01:49:10'),
(8, 'INV000008', 'CUST000001', '2025-06-30', 2500.00, NULL, 'finalized', 'User', '2025-06-30 01:49:58', '2025-06-30 01:49:58'),
(9, 'INV000009', 'CUST000001', '2025-06-30', 2500.00, NULL, 'finalized', 'User', '2025-06-30 01:54:12', '2025-06-30 01:54:12'),
(10, 'INV000010', 'CUST000001', '2025-06-30', 2500.00, NULL, 'finalized', 'User', '2025-06-30 01:54:46', '2025-06-30 01:54:46'),
(11, 'INV000011', 'CUST000001', '2025-06-30', 2500.00, NULL, 'finalized', 'User', '2025-06-30 02:00:25', '2025-06-30 02:00:25'),
(12, 'INV000012', 'CUST000001', '2025-06-30', 2500.00, NULL, 'finalized', 'User', '2025-06-30 02:02:39', '2025-06-30 02:02:39'),
(13, 'INV000013', 'CUST000001', '2025-06-30', 2500.00, NULL, 'finalized', 'User', '2025-06-30 02:03:05', '2025-06-30 02:03:05'),
(14, 'INV000014', 'CUST000001', '2025-06-30', 2500.00, NULL, 'finalized', 'User', '2025-06-30 02:05:54', '2025-06-30 02:05:54'),
(15, 'INV000015', 'CUST000001', '2025-06-30', 2500.00, NULL, 'finalized', 'User', '2025-06-30 02:15:38', '2025-06-30 02:15:38'),
(16, 'INV000016', 'CUST000001', '2025-06-30', 1070.00, NULL, 'finalized', 'User', '2025-06-30 02:19:48', '2025-06-30 02:19:48'),
(17, 'INV000017', 'CUST000001', '2025-06-30', 1070.00, NULL, 'finalized', 'User', '2025-06-30 02:22:14', '2025-06-30 02:22:14'),
(18, 'INV000018', 'CUST000001', '2025-06-30', 1070.00, NULL, 'finalized', 'User', '2025-06-30 02:26:13', '2025-06-30 02:26:13'),
(19, 'INV000019', 'CUST000001', '2025-06-30', 1070.00, NULL, 'finalized', 'User', '2025-06-30 02:33:04', '2025-06-30 02:33:04'),
(20, 'INV000020', 'CUST000001', '2025-06-30', 2500.00, NULL, 'finalized', 'User', '2025-06-30 02:40:38', '2025-06-30 02:40:38'),
(21, 'INV000021', 'CUST000002', '2025-06-30', 2500.00, NULL, 'finalized', 'User', '2025-06-30 02:41:18', '2025-06-30 02:41:18'),
(22, 'INV000022', 'CUST000001', '2025-06-30', 2500.00, NULL, 'finalized', 'User', '2025-06-30 02:41:36', '2025-06-30 02:41:36'),
(23, 'INV000023', 'CUST000001', '2025-06-30', 1070.00, NULL, 'finalized', 'User', '2025-06-30 02:50:17', '2025-06-30 02:50:17'),
(24, 'INV000024', 'CUST000001', '2025-06-30', 1070.00, NULL, 'finalized', 'User', '2025-06-30 03:01:16', '2025-06-30 03:01:16'),
(25, 'INV000025', 'CUST000001', '2025-06-30', 1070.00, NULL, 'finalized', 'User', '2025-06-30 03:02:36', '2025-06-30 03:02:36'),
(26, 'INV000026', 'CUST000001', '2025-06-30', 1070.00, NULL, 'finalized', 'User', '2025-06-30 03:09:08', '2025-06-30 03:09:08'),
(27, 'INV000027', 'CUST000001', '2025-06-30', 1070.00, NULL, 'finalized', 'User', '2025-06-30 03:18:35', '2025-06-30 03:18:35'),
(28, 'INV000028', 'CUST000001', '2025-06-30', 1070.00, NULL, 'finalized', 'User', '2025-06-30 03:24:19', '2025-06-30 03:24:19'),
(29, 'INV000029', 'CUST000001', '2025-06-30', 1070.00, NULL, 'finalized', 'User', '2025-06-30 03:32:31', '2025-06-30 03:32:31'),
(30, 'INV000030', 'CUST000001', '2025-06-30', 1070.00, NULL, 'finalized', 'User', '2025-06-30 03:33:20', '2025-06-30 03:33:20'),
(31, 'INV000031', 'CUST000001', '2025-06-30', 1070.00, NULL, 'finalized', 'User', '2025-06-30 03:35:55', '2025-06-30 03:35:55'),
(32, 'INV000032', 'CUST000001', '2025-06-30', 1070.00, NULL, 'finalized', 'User', '2025-06-30 03:44:57', '2025-06-30 03:44:57'),
(33, 'INV000033', 'CUST000001', '2025-06-30', 1070.00, NULL, 'finalized', 'User', '2025-06-30 04:10:37', '2025-06-30 04:10:37');

-- --------------------------------------------------------

--
-- Table structure for table `sales_invoice_items`
--

CREATE TABLE `sales_invoice_items` (
  `id` bigint UNSIGNED NOT NULL,
  `sales_invoice_id` bigint UNSIGNED NOT NULL,
  `line_no` int NOT NULL,
  `item_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `discount` decimal(5,2) NOT NULL DEFAULT '0.00',
  `line_total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales_invoice_items`
--

INSERT INTO `sales_invoice_items` (`id`, `sales_invoice_id`, `line_no`, `item_id`, `item_name`, `qty`, `unit_price`, `discount`, `line_total`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-29 15:07:59', '2025-06-29 15:07:59'),
(2, 2, 1, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-29 15:08:43', '2025-06-29 15:08:43'),
(3, 3, 1, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-29 15:21:48', '2025-06-29 15:21:48'),
(4, 4, 1, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 5.00, 2375.00, '2025-06-29 15:26:31', '2025-06-29 15:26:31'),
(5, 5, 1, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 3, 2500.00, 0.00, 7500.00, '2025-06-29 16:17:43', '2025-06-29 16:17:43'),
(6, 6, 1, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-30 01:44:20', '2025-06-30 01:44:20'),
(7, 7, 1, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-30 01:49:10', '2025-06-30 01:49:10'),
(8, 8, 1, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-30 01:49:58', '2025-06-30 01:49:58'),
(9, 9, 1, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-30 01:54:12', '2025-06-30 01:54:12'),
(10, 10, 1, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-30 01:54:46', '2025-06-30 01:54:46'),
(11, 11, 1, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-30 02:00:25', '2025-06-30 02:00:25'),
(12, 12, 1, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-30 02:02:39', '2025-06-30 02:02:39'),
(13, 13, 1, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-30 02:03:05', '2025-06-30 02:03:05'),
(14, 14, 1, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-30 02:05:54', '2025-06-30 02:05:54'),
(15, 15, 1, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-30 02:15:38', '2025-06-30 02:15:38'),
(16, 16, 1, '36DJ4031', 'KIT END -KIT MJR MAST CYL & GR\r\n', 1, 1070.00, 0.00, 1070.00, '2025-06-30 02:19:48', '2025-06-30 02:19:48'),
(17, 17, 1, '36DJ4031', 'KIT END -KIT MJR MAST CYL & GR\r\n', 1, 1070.00, 0.00, 1070.00, '2025-06-30 02:22:14', '2025-06-30 02:22:14'),
(18, 18, 1, '36DJ4031', 'KIT END -KIT MJR MAST CYL & GR\r\n', 1, 1070.00, 0.00, 1070.00, '2025-06-30 02:26:13', '2025-06-30 02:26:13'),
(19, 19, 1, '36DJ4031', 'KIT END -KIT MJR MAST CYL & GR\r\n', 1, 1070.00, 0.00, 1070.00, '2025-06-30 02:33:04', '2025-06-30 02:33:04'),
(20, 20, 1, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-30 02:40:38', '2025-06-30 02:40:38'),
(21, 21, 1, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-30 02:41:18', '2025-06-30 02:41:18'),
(22, 22, 1, 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-30 02:41:36', '2025-06-30 02:41:36'),
(23, 23, 1, '36DJ4031', 'KIT END -KIT MJR MAST CYL & GR\r\n', 1, 1070.00, 0.00, 1070.00, '2025-06-30 02:50:17', '2025-06-30 02:50:17'),
(24, 24, 1, '36DJ4031', 'KIT END -KIT MJR MAST CYL & GR\r\n', 1, 1070.00, 0.00, 1070.00, '2025-06-30 03:01:16', '2025-06-30 03:01:16'),
(25, 25, 1, '36DJ4031', 'KIT END -KIT MJR MAST CYL & GR\r\n', 1, 1070.00, 0.00, 1070.00, '2025-06-30 03:02:36', '2025-06-30 03:02:36'),
(26, 26, 1, '36DJ4031', 'KIT END -KIT MJR MAST CYL & GR\r\n', 1, 1070.00, 0.00, 1070.00, '2025-06-30 03:09:08', '2025-06-30 03:09:08'),
(27, 27, 1, '36DJ4031', 'KIT END -KIT MJR MAST CYL & GR\r\n', 1, 1070.00, 0.00, 1070.00, '2025-06-30 03:18:35', '2025-06-30 03:18:35'),
(28, 28, 1, '36DJ4031', 'KIT END -KIT MJR MAST CYL & GR\r\n', 1, 1070.00, 0.00, 1070.00, '2025-06-30 03:24:19', '2025-06-30 03:24:19'),
(29, 29, 1, '36DJ4031', 'KIT END -KIT MJR MAST CYL & GR\r\n', 1, 1070.00, 0.00, 1070.00, '2025-06-30 03:32:31', '2025-06-30 03:32:31'),
(30, 30, 1, '36DJ4031', 'KIT END -KIT MJR MAST CYL & GR\r\n', 1, 1070.00, 0.00, 1070.00, '2025-06-30 03:33:20', '2025-06-30 03:33:20'),
(31, 31, 1, '36DJ4031', 'KIT END -KIT MJR MAST CYL & GR\r\n', 1, 1070.00, 0.00, 1070.00, '2025-06-30 03:35:55', '2025-06-30 03:35:55'),
(32, 32, 1, '36DJ4031', 'KIT END -KIT MJR MAST CYL & GR\r\n', 1, 1070.00, 0.00, 1070.00, '2025-06-30 03:44:57', '2025-06-30 03:44:57'),
(33, 33, 1, '36DJ4031', 'KIT END -KIT MJR MAST CYL & GR\r\n', 1, 1070.00, 0.00, 1070.00, '2025-06-30 04:10:37', '2025-06-30 04:10:37');

-- --------------------------------------------------------

--
-- Table structure for table `service_invoices`
--

CREATE TABLE `service_invoices` (
  `id` bigint UNSIGNED NOT NULL,
  `invoice_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mileage` int DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `job_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `parts_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('hold','finalized') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hold',
  `created_by` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_invoices`
--

INSERT INTO `service_invoices` (`id`, `invoice_no`, `customer_id`, `vehicle_no`, `mileage`, `invoice_date`, `job_total`, `parts_total`, `grand_total`, `notes`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'SRV000001', 'CUST000001', 'BSC-8167', 85650, '2025-06-30', 2500.00, 2950.00, 5450.00, NULL, 'hold', 'Admin', '2025-06-30 12:07:46', '2025-06-30 12:07:46'),
(2, 'SRV000002', 'CUST000006', 'BSC-8156', 65452, '2025-06-30', 2500.00, 2500.00, 5000.00, NULL, 'hold', 'Admin', '2025-06-30 12:09:15', '2025-06-30 12:09:15'),
(3, 'SRV000003', 'CUST000001', 'BSC-8167', 85555, '2025-07-01', 2500.00, 2500.00, 5000.00, NULL, 'hold', 'Admin', '2025-06-30 22:02:10', '2025-06-30 22:02:10'),
(4, 'SRV000004', 'CUST000001', 'BSC-8167', 85522, '2025-07-01', 350.00, 2500.00, 2850.00, NULL, 'finalized', 'Admin', '2025-06-30 22:02:52', '2025-06-30 22:02:52'),
(5, 'SRV000005', 'CUST000001', 'BSC-8167', 84652, '2025-07-01', 350.00, 450.00, 800.00, NULL, 'finalized', 'Admin', '2025-06-30 22:09:40', '2025-06-30 22:09:40'),
(6, 'SRV000006', 'CUST000001', 'MF-7636', 87456, '2025-07-01', 350.00, 450.00, 800.00, NULL, 'finalized', 'Admin', '2025-06-30 22:19:57', '2025-06-30 22:19:57'),
(7, 'SRV000007', 'CUST000001', 'BSC-8167', 74125, '2025-07-01', 350.00, 2500.00, 2850.00, NULL, 'hold', 'Admin', '2025-06-30 22:22:09', '2025-06-30 22:22:09'),
(8, 'SRV000008', 'CUST000001', 'BSC-8167', 87452, '2025-07-01', 350.00, 2500.00, 2850.00, NULL, 'hold', 'Admin', '2025-06-30 22:28:37', '2025-06-30 22:28:37'),
(9, 'SRV000009', 'CUST000001', 'BSC-8167', 78945, '2025-07-01', 350.00, 2500.00, 2850.00, NULL, 'finalized', 'Admin', '2025-06-30 22:44:25', '2025-06-30 22:44:25'),
(10, 'SRV000010', 'CUST000001', 'BSC-8167', 85464, '2025-07-01', 350.00, 2500.00, 2850.00, NULL, 'finalized', 'Admin', '2025-07-01 02:16:14', '2025-07-01 02:16:14'),
(11, 'SRV000011', 'CUST000008', NULL, 87546, '2025-07-01', 350.00, 0.00, 350.00, NULL, 'finalized', 'Admin', '2025-07-01 02:23:46', '2025-07-01 02:23:46'),
(12, 'SRV000012', 'CUST000001', 'BSC-8167', 79463, '2025-07-01', 350.00, 1070.00, 1420.00, NULL, 'finalized', 'Admin', '2025-07-01 03:02:19', '2025-07-01 03:02:19');

-- --------------------------------------------------------

--
-- Table structure for table `service_invoice_items`
--

CREATE TABLE `service_invoice_items` (
  `id` bigint UNSIGNED NOT NULL,
  `service_invoice_id` bigint UNSIGNED NOT NULL,
  `line_no` int NOT NULL,
  `item_type` enum('job','spare') COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `line_total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_invoice_items`
--

INSERT INTO `service_invoice_items` (`id`, `service_invoice_id`, `line_no`, `item_type`, `item_id`, `item_name`, `qty`, `unit_price`, `discount`, `line_total`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'job', 'JOB00051', 'NORMAL SERVICE 150CC', 1, 2500.00, 0.00, 2500.00, '2025-06-30 12:07:46', '2025-06-30 12:07:46'),
(2, 1, 2, 'spare', 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-30 12:07:46', '2025-06-30 12:07:46'),
(3, 1, 3, 'spare', 'DD121181', 'OIL FILTER WITH SPRING', 1, 450.00, 0.00, 450.00, '2025-06-30 12:07:46', '2025-06-30 12:07:46'),
(4, 2, 1, 'job', 'JOB00008', 'NORMAL SERVICE 125CC', 1, 2500.00, 0.00, 2500.00, '2025-06-30 12:09:15', '2025-06-30 12:09:15'),
(5, 2, 2, 'spare', 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-30 12:09:15', '2025-06-30 12:09:15'),
(6, 3, 1, 'job', 'JOB00020', 'NORMAL SERVICE 125CC', 1, 2500.00, 0.00, 2500.00, '2025-06-30 22:02:10', '2025-06-30 22:02:10'),
(7, 3, 2, 'spare', 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-30 22:02:10', '2025-06-30 22:02:10'),
(8, 4, 1, 'job', 'JOB00003', 'REPLACING FRONT WHEEL BERING', 1, 350.00, 0.00, 350.00, '2025-06-30 22:02:52', '2025-06-30 22:02:52'),
(9, 4, 2, 'spare', 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-30 22:02:52', '2025-06-30 22:02:52'),
(10, 5, 1, 'job', 'JOB00002', 'REPLACING MAJOR KIT', 1, 350.00, 0.00, 350.00, '2025-06-30 22:09:40', '2025-06-30 22:09:40'),
(11, 5, 2, 'spare', 'DD121181', 'OIL FILTER WITH SPRING', 1, 450.00, 0.00, 450.00, '2025-06-30 22:09:40', '2025-06-30 22:09:40'),
(12, 6, 1, 'job', 'JOB00002', 'REPLACING MAJOR KIT', 1, 350.00, 0.00, 350.00, '2025-06-30 22:19:57', '2025-06-30 22:19:57'),
(13, 6, 2, 'spare', 'DD121181', 'OIL FILTER WITH SPRING', 1, 450.00, 0.00, 450.00, '2025-06-30 22:19:57', '2025-06-30 22:19:57'),
(14, 7, 1, 'job', 'JOB00002', 'REPLACING MAJOR KIT', 1, 350.00, 0.00, 350.00, '2025-06-30 22:22:09', '2025-06-30 22:22:09'),
(15, 7, 2, 'spare', 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-30 22:22:09', '2025-06-30 22:22:09'),
(18, 9, 1, 'job', 'JOB00002', 'REPLACING MAJOR KIT', 1, 350.00, 0.00, 350.00, '2025-06-30 22:44:25', '2025-06-30 22:44:25'),
(19, 9, 2, 'spare', 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-30 22:44:25', '2025-06-30 22:44:25'),
(20, 8, 1, 'job', 'JOB00002', 'REPLACING MAJOR KIT', 1, 350.00, 0.00, 350.00, '2025-06-30 23:00:41', '2025-06-30 23:00:41'),
(21, 8, 2, 'spare', 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-06-30 23:00:41', '2025-06-30 23:00:41'),
(22, 10, 1, 'job', 'JOB00002', 'REPLACING MAJOR KIT', 1, 350.00, 0.00, 350.00, '2025-07-01 02:16:14', '2025-07-01 02:16:14'),
(23, 10, 2, 'spare', 'EO', '20W /50 1L APPROVED  ENGINE OIL', 1, 2500.00, 0.00, 2500.00, '2025-07-01 02:16:14', '2025-07-01 02:16:14'),
(24, 11, 1, 'job', 'JOB00002', 'REPLACING MAJOR KIT', 1, 350.00, 0.00, 350.00, '2025-07-01 02:23:46', '2025-07-01 02:23:46'),
(25, 12, 1, 'job', 'JOB00002', 'REPLACING MAJOR KIT', 1, 350.00, 0.00, 350.00, '2025-07-01 03:02:19', '2025-07-01 03:02:19'),
(26, 12, 2, 'spare', '36DJ4031', 'KIT END -KIT MJR MAST CYL & GR', 1, 1070.00, 0.00, 1070.00, '2025-07-01 03:02:19', '2025-07-01 03:02:19');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('PtiTi3RbwjH1xZi8C0COqzPlGIwpCMh8gyFRHuPo', 3, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo4OntzOjY6Il90b2tlbiI7czo0MDoiTkNMQXBVYncxNDc5MkFmaUVic0V6Qk1aa3MxelpNbkhETk9FS2RMayI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM2OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvc2FsZXMtaW52b2ljZXMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO3M6MjE6InBhc3N3b3JkX2hhc2hfc2FuY3R1bSI7czo2MDoiJDJ5JDEyJFltLlUyU1RKaGdxeDMxajVaL0VxdXVsVS5ibjZNZUtacTg2cDFxRXJSWkd3em55cmFKSUEuIjtzOjMwOiJlZGl0X3NlcnZpY2VfaW52b2ljZV9qb2JfaXRlbXMiO2E6MTp7aTowO2E6NTp7czo3OiJpdGVtX2lkIjtzOjg6IkpPQjAwMDAyIjtzOjExOiJkZXNjcmlwdGlvbiI7czoxOToiUkVQTEFDSU5HIE1BSk9SIEtJVCI7czozOiJxdHkiO2k6MTtzOjU6InByaWNlIjtzOjY6IjM1MC4wMCI7czoxMDoibGluZV90b3RhbCI7czo2OiIzNTAuMDAiO319czozMjoiZWRpdF9zZXJ2aWNlX2ludm9pY2Vfc3BhcmVfaXRlbXMiO2E6MTp7aTowO2E6NTp7czo3OiJpdGVtX2lkIjtzOjI6IkVPIjtzOjExOiJkZXNjcmlwdGlvbiI7czozMToiMjBXIC81MCAxTCBBUFBST1ZFRCAgRU5HSU5FIE9JTCI7czozOiJxdHkiO2k6MTtzOjU6InByaWNlIjtzOjc6IjI1MDAuMDAiO3M6MTA6ImxpbmVfdG90YWwiO3M6NzoiMjUwMC4wMCI7fX19', 1751365416),
('riO2RPpyMIi6EK5re7Y3VUGFS5dCZSvNOhOgnPCN', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOUpTYkFtVm9KaHdPdE9TaFZLR2N5S2oyQWFobG1wNk11N1N3NldENSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jdXN0b21lci9kYXNoYm9hcmQiO31zOjU1OiJsb2dpbl9jdXN0b21lcl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1751365423);

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `id` bigint UNSIGNED NOT NULL,
  `item_ID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `cost_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`id`, `item_ID`, `quantity`, `cost_value`, `created_at`, `updated_at`) VALUES
(2, 'EO', 45, 0.00, '2025-06-13 19:04:56', '2025-06-30 06:10:32'),
(3, 'DD121181', 85, 0.00, '2025-06-13 19:38:20', '2025-06-13 22:44:58'),
(4, 'DJ151089', 58, 0.00, '2025-06-16 19:43:27', '2025-06-16 19:43:27'),
(5, '36DJ4031', 10, 0.00, '2025-06-30 02:17:55', '2025-06-30 06:14:35'),
(6, 'ASKBS0104', 10, 0.00, '2025-06-30 05:50:00', '2025-06-30 05:50:00'),
(7, 'DD111018', 15, 0.00, '2025-06-30 05:56:48', '2025-06-30 06:02:45'),
(8, 'BGO10W30C', 5, 0.00, '2025-06-30 06:09:27', '2025-06-30 06:09:27'),
(9, 'AA121006', 3, 0.00, '2025-06-30 06:29:03', '2025-06-30 11:30:26'),
(10, 'DS141014', 10, 332.50, '2025-06-30 11:37:52', '2025-06-30 11:37:52');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `Supp_ID` int UNSIGNED NOT NULL,
  `Supp_CustomID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Supp_Name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `Company_Name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `Phone` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `Fax` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `Email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `Web` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `Address1` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `Supp_Group_Name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `Remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `Last_GRN` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `Total_Orders` double NOT NULL DEFAULT '0',
  `Total_Spent` double NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '1',
  `created_At` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`Supp_ID`, `Supp_CustomID`, `Supp_Name`, `Company_Name`, `Phone`, `Fax`, `Email`, `Web`, `Address1`, `Supp_Group_Name`, `Remark`, `Last_GRN`, `Total_Orders`, `Total_Spent`, `status`, `created_At`, `updated_at`) VALUES
(1, 'AM', 'AUTO MART', 'AUTO MART', '0773997951', NULL, NULL, NULL, 'Rathnapurta', NULL, NULL, NULL, 0, 0, 1, '2025-06-05 02:52:25', '2025-06-05 02:52:25'),
(2, 'DPMC', 'DAVID PIERIS MOTOR COMPANY (PVT) LTD', 'DAVID PIERIS MOTOR COMPANY (PVT) LTD', '0114700600', NULL, NULL, NULL, 'PANNIPITIYA ROAD BATTARAMULLA', NULL, NULL, NULL, 0, 0, 1, '2025-06-05 02:53:15', '2025-06-05 02:53:15'),
(3, 'RAM', 'RUWANPURA AUTO MART', 'RUWANPURA AUTO MART', '0715629798', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, '2025-06-05 02:53:47', '2025-06-05 02:53:47'),
(4, 'DnD', 'D and D Motors', 'D and D Motors', '0713698026', NULL, 'dand@gmail.com', NULL, '150/7, Golden Grow , Kospelawinna,', NULL, NULL, NULL, 0, 0, 1, '2025-06-17 06:40:28', '2025-06-17 06:40:28');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_groups`
--

CREATE TABLE `supplier_groups` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `usertype` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_team_id` bigint UNSIGNED DEFAULT NULL,
  `profile_photo_path` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `phone`, `usertype`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `remember_token`, `current_team_id`, `profile_photo_path`, `created_at`, `updated_at`) VALUES
(1, 'Manura Madhushan', 'madhushanm99@gmail.com', NULL, '0713698026', 'admin', '$2y$12$Ym.U2STJhgqx31j5Z/EquulU.bn6MeKZq86p1qErRZGwznyraJIA.', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-04 15:19:12', '2025-06-04 15:19:12'),
(3, 'Admin', 'admin@gmail.com', NULL, '0713698027', 'admin', '$2y$12$Ym.U2STJhgqx31j5Z/EquulU.bn6MeKZq86p1qErRZGwznyraJIA.', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-04 15:19:12', '2025-06-04 15:19:12'),
(4, 'Manager', 'manager@gmail.com', NULL, '0713698028', 'manager', '$2y$12$Ym.U2STJhgqx31j5Z/EquulU.bn6MeKZq86p1qErRZGwznyraJIA.', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-04 15:19:12', '2025-06-04 15:19:12'),
(7, 'User', 'user@gmail.com', NULL, '0713698029', 'user', '$2y$12$Ym.U2STJhgqx31j5Z/EquulU.bn6MeKZq86p1qErRZGwznyraJIA.', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-04 15:19:12', '2025-06-04 15:19:12');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `vehicle_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_id` bigint UNSIGNED NOT NULL,
  `model` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `engine_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chassis_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route_id` bigint UNSIGNED DEFAULT NULL,
  `year_of_manufacture` year DEFAULT NULL,
  `date_of_purchase` date DEFAULT NULL,
  `last_entry` timestamp NULL DEFAULT NULL,
  `registration_status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `customer_id`, `vehicle_no`, `brand_id`, `model`, `engine_no`, `chassis_no`, `route_id`, `year_of_manufacture`, `date_of_purchase`, `last_entry`, `registration_status`, `created_at`, `updated_at`, `status`) VALUES
(1, 1, 'BSC-8167', 4, 'Pulser 150', '444dfdd5d4d', 'ds54f5dssd444', 1, '2015', '2016-03-15', NULL, 1, '2025-06-16 15:24:58', '2025-06-16 15:24:58', 1),
(2, 5, 'BSC-8168', 4, 'Pulser 150', '444dfdd5d4d', 'ds54f5dssd444', 11, '2015', '2016-04-11', NULL, 1, '2025-06-16 15:26:23', '2025-06-16 15:26:23', 1),
(3, 4, 'MF-7636', 4, 'CT 100', 'sdfsdf5s5dfsf', 'dfsf66ewfrw', 5, '2004', '2005-04-01', NULL, 1, '2025-06-16 15:28:01', '2025-06-16 15:28:01', 1),
(4, 6, 'BSC-8156', 4, 'Pulser 150', 'sdfsdf5s5dfsss', 'dfsf66ewfrsa', 1, '2015', '2016-04-15', NULL, 1, '2025-06-16 19:39:29', '2025-06-16 19:39:29', 1);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_brands`
--

CREATE TABLE `vehicle_brands` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicle_brands`
--

INSERT INTO `vehicle_brands` (`id`, `name`, `created_at`, `updated_at`, `status`) VALUES
(1, 'Honda', '2025-06-16 20:52:58', '2025-06-16 20:52:58', 1),
(2, 'Yamaha', '2025-06-16 20:52:58', '2025-06-16 20:52:58', 1),
(3, 'Suzuki', '2025-06-16 20:52:58', '2025-06-16 20:52:58', 1),
(4, 'Bajaj', '2025-06-16 20:52:58', '2025-06-16 20:52:58', 1),
(5, 'TVS', '2025-06-16 20:52:58', '2025-06-16 20:52:58', 1),
(6, 'Hero', '2025-06-16 20:52:58', '2025-06-16 20:52:58', 1),
(7, 'KTM', '2025-06-16 20:52:58', '2025-06-16 20:52:58', 1);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_routes`
--

CREATE TABLE `vehicle_routes` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicle_routes`
--

INSERT INTO `vehicle_routes` (`id`, `name`, `created_at`, `updated_at`, `status`) VALUES
(1, 'No route', '2025-06-16 20:54:13', '2025-06-16 20:54:13', 1),
(2, 'Colombo - Kandy', '2025-06-16 20:54:13', '2025-06-16 20:54:13', 1),
(3, 'Colombo - Galle', '2025-06-16 20:54:13', '2025-06-16 20:54:13', 1),
(4, 'Colombo - Kurunegala', '2025-06-16 20:54:13', '2025-06-16 20:54:13', 1),
(5, 'Colombo - Anuradhapura', '2025-06-16 20:54:13', '2025-06-16 20:54:13', 1),
(6, 'Colombo - Ratnapura', '2025-06-16 20:54:13', '2025-06-16 20:54:13', 1),
(7, 'Kandy - Nuwara Eliya', '2025-06-16 20:54:13', '2025-06-16 20:54:13', 1),
(8, 'Jaffna - Vavuniya', '2025-06-16 20:54:13', '2025-06-16 20:54:13', 1),
(9, 'Colombo - Matara', '2025-06-16 20:54:13', '2025-06-16 20:54:13', 1),
(10, 'Galle - Hambantota', '2025-06-16 20:54:13', '2025-06-16 20:54:13', 1),
(11, 'Badulla - Monaragala', '2025-06-16 20:54:13', '2025-06-16 20:54:13', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bank_accounts_account_number_unique` (`account_number`),
  ADD KEY `bank_accounts_is_active_account_type_index` (`is_active`,`account_type`),
  ADD KEY `bank_accounts_bank_name_index` (`bank_name`);

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
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`iD_Auto`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customers_custom_id_index` (`custom_id`);

--
-- Indexes for table `customer_logins`
--
ALTER TABLE `customer_logins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_logins_email_unique` (`email`),
  ADD KEY `customer_logins_customer_custom_id_foreign` (`customer_custom_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `grn`
--
ALTER TABLE `grn`
  ADD PRIMARY KEY (`grn_id`),
  ADD UNIQUE KEY `grn_grn_no_unique` (`grn_no`),
  ADD KEY `grn_supp_cus_id_foreign` (`supp_Cus_ID`);

--
-- Indexes for table `grn_items`
--
ALTER TABLE `grn_items`
  ADD PRIMARY KEY (`grn_item_id`),
  ADD KEY `grn_items_grn_id_foreign` (`grn_id`);

--
-- Indexes for table `invoice_returns`
--
ALTER TABLE `invoice_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_returns_return_no_unique` (`return_no`),
  ADD KEY `invoice_returns_sales_invoice_id_foreign` (`sales_invoice_id`),
  ADD KEY `invoice_returns_customer_id_return_date_index` (`customer_id`,`return_date`),
  ADD KEY `invoice_returns_status_index` (`status`);

--
-- Indexes for table `invoice_return_items`
--
ALTER TABLE `invoice_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_return_items_invoice_return_id_line_no_index` (`invoice_return_id`,`line_no`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`item_ID_Auto`),
  ADD UNIQUE KEY `item_item_id_unique` (`item_ID`);

--
-- Indexes for table `item_location`
--
ALTER TABLE `item_location`
  ADD PRIMARY KEY (`iD_Auto`);

--
-- Indexes for table `item_stock`
--
ALTER TABLE `item_stock`
  ADD PRIMARY KEY (`iD_Auto`),
  ADD UNIQUE KEY `item_stock_item_id_unique` (`item_ID`);

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
-- Indexes for table `job_types`
--
ALTER TABLE `job_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_types_jobcustomid_unique` (`jobCustomID`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payment_categories`
--
ALTER TABLE `payment_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_categories_name_type_unique` (`name`,`type`),
  ADD UNIQUE KEY `payment_categories_code_unique` (`code`),
  ADD KEY `payment_categories_type_is_active_index` (`type`,`is_active`),
  ADD KEY `payment_categories_parent_id_index` (`parent_id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_methods_name_unique` (`name`),
  ADD UNIQUE KEY `payment_methods_code_unique` (`code`),
  ADD KEY `payment_methods_is_active_index` (`is_active`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_transactions_transaction_no_unique` (`transaction_no`),
  ADD KEY `payment_transactions_payment_method_id_foreign` (`payment_method_id`),
  ADD KEY `payment_transactions_bank_account_id_foreign` (`bank_account_id`),
  ADD KEY `payment_transactions_payment_category_id_foreign` (`payment_category_id`),
  ADD KEY `payment_transactions_type_transaction_date_index` (`type`,`transaction_date`),
  ADD KEY `payment_transactions_status_transaction_date_index` (`status`,`transaction_date`),
  ADD KEY `payment_transactions_customer_id_index` (`customer_id`),
  ADD KEY `payment_transactions_supplier_id_index` (`supplier_id`),
  ADD KEY `payment_transactions_sales_invoice_id_index` (`sales_invoice_id`),
  ADD KEY `payment_transactions_purchase_order_id_index` (`purchase_order_id`),
  ADD KEY `payment_transactions_created_by_index` (`created_by`),
  ADD KEY `payment_transactions_invoice_return_id_foreign` (`invoice_return_id`),
  ADD KEY `payment_transactions_grn_id_index` (`grn_id`),
  ADD KEY `payment_transactions_purchase_return_id_foreign` (`purchase_return_id`),
  ADD KEY `payment_transactions_service_invoice_id_index` (`service_invoice_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_usertype_unique` (`usertype`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `po`
--
ALTER TABLE `po`
  ADD PRIMARY KEY (`po_Auto_ID`),
  ADD UNIQUE KEY `po_po_no_unique` (`po_No`),
  ADD KEY `po_supp_cus_id_foreign` (`supp_Cus_ID`);

--
-- Indexes for table `po__item`
--
ALTER TABLE `po__item`
  ADD PRIMARY KEY (`po_Item_Auto_ID`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`item_ID_Auto`),
  ADD UNIQUE KEY `products_item_id_unique` (`item_ID`);

--
-- Indexes for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_returns_return_no_unique` (`return_no`),
  ADD KEY `purchase_returns_supp_cus_id_foreign` (`supp_Cus_ID`);

--
-- Indexes for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_return_items_purchase_return_id_foreign` (`purchase_return_id`);

--
-- Indexes for table `quotations`
--
ALTER TABLE `quotations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quotations_quotation_no_unique` (`quotation_no`);

--
-- Indexes for table `quotation_items`
--
ALTER TABLE `quotation_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quotation_items_quotation_id_foreign` (`quotation_id`);

--
-- Indexes for table `sales_invoices`
--
ALTER TABLE `sales_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_invoices_invoice_no_unique` (`invoice_no`),
  ADD KEY `sales_invoices_customer_id_invoice_date_index` (`customer_id`,`invoice_date`),
  ADD KEY `sales_invoices_status_index` (`status`);

--
-- Indexes for table `sales_invoice_items`
--
ALTER TABLE `sales_invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_invoice_items_sales_invoice_id_line_no_index` (`sales_invoice_id`,`line_no`);

--
-- Indexes for table `service_invoices`
--
ALTER TABLE `service_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `service_invoices_invoice_no_unique` (`invoice_no`),
  ADD KEY `service_invoices_customer_id_invoice_date_index` (`customer_id`,`invoice_date`),
  ADD KEY `service_invoices_vehicle_no_invoice_date_index` (`vehicle_no`,`invoice_date`),
  ADD KEY `service_invoices_status_index` (`status`),
  ADD KEY `service_invoices_invoice_no_index` (`invoice_no`);

--
-- Indexes for table `service_invoice_items`
--
ALTER TABLE `service_invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_invoice_items_service_invoice_id_line_no_index` (`service_invoice_id`,`line_no`),
  ADD KEY `service_invoice_items_item_type_item_id_index` (`item_type`,`item_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_item_id_unique` (`item_ID`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`Supp_ID`),
  ADD UNIQUE KEY `suppliers_supp_customid_unique` (`Supp_CustomID`);

--
-- Indexes for table `supplier_groups`
--
ALTER TABLE `supplier_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicles_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `vehicle_brands`
--
ALTER TABLE `vehicle_brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicle_brands_name_unique` (`name`);

--
-- Indexes for table `vehicle_routes`
--
ALTER TABLE `vehicle_routes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicle_routes_name_unique` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `iD_Auto` smallint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `customer_logins`
--
ALTER TABLE `customer_logins`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grn`
--
ALTER TABLE `grn`
  MODIFY `grn_id` smallint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `grn_items`
--
ALTER TABLE `grn_items`
  MODIFY `grn_item_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `invoice_returns`
--
ALTER TABLE `invoice_returns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `invoice_return_items`
--
ALTER TABLE `invoice_return_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `item_ID_Auto` smallint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `item_location`
--
ALTER TABLE `item_location`
  MODIFY `iD_Auto` smallint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `item_stock`
--
ALTER TABLE `item_stock`
  MODIFY `iD_Auto` smallint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_types`
--
ALTER TABLE `job_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=651;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `payment_categories`
--
ALTER TABLE `payment_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `po`
--
ALTER TABLE `po`
  MODIFY `po_Auto_ID` smallint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `po__item`
--
ALTER TABLE `po__item`
  MODIFY `po_Item_Auto_ID` smallint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `item_ID_Auto` smallint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `quotation_items`
--
ALTER TABLE `quotation_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `sales_invoices`
--
ALTER TABLE `sales_invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `sales_invoice_items`
--
ALTER TABLE `sales_invoice_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `service_invoices`
--
ALTER TABLE `service_invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `service_invoice_items`
--
ALTER TABLE `service_invoice_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `Supp_ID` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `supplier_groups`
--
ALTER TABLE `supplier_groups`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vehicle_brands`
--
ALTER TABLE `vehicle_brands`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `vehicle_routes`
--
ALTER TABLE `vehicle_routes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer_logins`
--
ALTER TABLE `customer_logins`
  ADD CONSTRAINT `customer_logins_customer_custom_id_foreign` FOREIGN KEY (`customer_custom_id`) REFERENCES `customers` (`custom_id`) ON DELETE CASCADE;

--
-- Constraints for table `grn`
--
ALTER TABLE `grn`
  ADD CONSTRAINT `grn_supp_cus_id_foreign` FOREIGN KEY (`supp_Cus_ID`) REFERENCES `suppliers` (`Supp_CustomID`) ON DELETE SET NULL;

--
-- Constraints for table `grn_items`
--
ALTER TABLE `grn_items`
  ADD CONSTRAINT `grn_items_grn_id_foreign` FOREIGN KEY (`grn_id`) REFERENCES `grn` (`grn_id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_returns`
--
ALTER TABLE `invoice_returns`
  ADD CONSTRAINT `invoice_returns_sales_invoice_id_foreign` FOREIGN KEY (`sales_invoice_id`) REFERENCES `sales_invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_return_items`
--
ALTER TABLE `invoice_return_items`
  ADD CONSTRAINT `invoice_return_items_invoice_return_id_foreign` FOREIGN KEY (`invoice_return_id`) REFERENCES `invoice_returns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_categories`
--
ALTER TABLE `payment_categories`
  ADD CONSTRAINT `payment_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `payment_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD CONSTRAINT `payment_transactions_bank_account_id_foreign` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_transactions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`custom_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_transactions_grn_id_foreign` FOREIGN KEY (`grn_id`) REFERENCES `grn` (`grn_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_transactions_invoice_return_id_foreign` FOREIGN KEY (`invoice_return_id`) REFERENCES `invoice_returns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_transactions_payment_category_id_foreign` FOREIGN KEY (`payment_category_id`) REFERENCES `payment_categories` (`id`),
  ADD CONSTRAINT `payment_transactions_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`),
  ADD CONSTRAINT `payment_transactions_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `po` (`po_Auto_ID`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_transactions_purchase_return_id_foreign` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_transactions_sales_invoice_id_foreign` FOREIGN KEY (`sales_invoice_id`) REFERENCES `sales_invoices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_transactions_service_invoice_id_foreign` FOREIGN KEY (`service_invoice_id`) REFERENCES `service_invoices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_transactions_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`Supp_CustomID`) ON DELETE SET NULL;

--
-- Constraints for table `po`
--
ALTER TABLE `po`
  ADD CONSTRAINT `po_supp_cus_id_foreign` FOREIGN KEY (`supp_Cus_ID`) REFERENCES `suppliers` (`Supp_CustomID`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  ADD CONSTRAINT `purchase_returns_supp_cus_id_foreign` FOREIGN KEY (`supp_Cus_ID`) REFERENCES `suppliers` (`Supp_CustomID`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  ADD CONSTRAINT `purchase_return_items_purchase_return_id_foreign` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quotation_items`
--
ALTER TABLE `quotation_items`
  ADD CONSTRAINT `quotation_items_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales_invoice_items`
--
ALTER TABLE `sales_invoice_items`
  ADD CONSTRAINT `sales_invoice_items_sales_invoice_id_foreign` FOREIGN KEY (`sales_invoice_id`) REFERENCES `sales_invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_invoice_items`
--
ALTER TABLE `service_invoice_items`
  ADD CONSTRAINT `service_invoice_items_service_invoice_id_foreign` FOREIGN KEY (`service_invoice_id`) REFERENCES `service_invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
