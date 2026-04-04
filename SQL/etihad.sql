-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 17, 2026 at 04:15 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `etihad`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `description`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 1, 'role_created', 'Admin created role user.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 04:21:46', '2026-03-14 04:21:46'),
(2, 1, 'user_created', 'Admin created user nadeem (nadeem@domain.com).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 04:22:21', '2026-03-14 04:22:21'),
(3, 1, 'admin_profile_updated', 'Admin updated their profile and settings.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 04:26:18', '2026-03-14 04:26:18'),
(4, 1, 'admin_profile_updated', 'Admin updated their profile and settings.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 04:26:25', '2026-03-14 04:26:25'),
(5, 1, 'admin_logout', 'Admin user logged out from admin panel.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 04:26:29', '2026-03-14 04:26:29'),
(6, 2, 'admin_login', 'Admin user logged in via admin panel.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 04:26:37', '2026-03-14 04:26:37'),
(7, 2, 'admin_login', 'Admin user logged in via admin panel.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 04:26:46', '2026-03-14 04:26:46'),
(8, 1, 'admin_login', 'Admin user logged in via admin panel.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 04:26:53', '2026-03-14 04:26:53'),
(9, 1, 'user_updated', 'Admin updated user nadeem (nadeem@domain.com).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 04:27:08', '2026-03-14 04:27:08'),
(10, 1, 'admin_logout', 'Admin user logged out from admin panel.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 04:27:12', '2026-03-14 04:27:12'),
(11, 2, 'admin_login', 'Admin user logged in via admin panel.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 04:27:20', '2026-03-14 04:27:20'),
(12, 2, 'admin_login', 'Admin user logged in via admin panel.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 04:34:41', '2026-03-14 04:34:41'),
(13, 2, 'admin_logout', 'Admin user logged out from admin panel.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 04:45:07', '2026-03-14 04:45:07'),
(14, 1, 'admin_login', 'Admin user logged in via admin panel.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 04:45:10', '2026-03-14 04:45:10'),
(15, 1, 'admin_login', 'Admin user logged in via admin panel.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 09:34:23', '2026-03-14 09:34:23'),
(16, 1, 'listing_created', 'Own listing created: First Post (ID: 1, slug: first post).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 10:40:37', '2026-03-14 10:40:37'),
(17, 1, 'listing_updated', 'Own listing updated: First Post (ID: 1, slug: first-post).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 10:40:49', '2026-03-14 10:40:49'),
(18, 1, 'listing_duplicated', 'Own listing duplicated: First Post (ID: 1) → new ID: 2, slug: first-post-2.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 10:46:41', '2026-03-14 10:46:41'),
(19, 1, 'listing_updated', 'Own listing updated: Second Post (ID: 2, slug: second-post).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 10:46:58', '2026-03-14 10:46:58'),
(20, 1, 'listing_updated', 'Own listing updated: Second Post (ID: 2, slug: second-post).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 10:47:18', '2026-03-14 10:47:18'),
(21, 1, 'listing_created', 'Dealer listing created: Third post (ID: 3, slug: third-post).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 10:51:40', '2026-03-14 10:51:40'),
(22, 1, 'listing_updated', 'Dealer listing updated: Third post of dealer (ID: 3, slug: third-post).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 10:52:03', '2026-03-14 10:52:03'),
(23, 1, 'listing_updated', 'Dealer listing updated: Third post of dealer (ID: 3, slug: third-post).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 11:01:47', '2026-03-14 11:01:47'),
(24, 1, 'listing_updated', 'Own listing updated: Second Post (ID: 2, slug: second-post).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 11:02:01', '2026-03-14 11:02:01'),
(25, 1, 'listing_updated', 'Own listing updated: Second Post (ID: 2, slug: second-post).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-14 11:02:14', '2026-03-14 11:02:14'),
(26, 1, 'admin_login', 'Admin user logged in via admin panel.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-15 01:42:45', '2026-03-15 01:42:45'),
(27, 1, 'admin_logout', 'Admin user logged out from admin panel.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-15 03:07:13', '2026-03-15 03:07:13'),
(28, 1, 'admin_login', 'Admin user logged in via admin panel.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-15 23:45:00', '2026-03-15 23:45:00'),
(29, 1, 'listing_updated', 'Dealer listing updated: Third post of dealer (ID: 3, slug: third-post).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-15 23:46:49', '2026-03-15 23:46:49'),
(30, 1, 'listing_updated', 'Dealer listing updated: Third post of dealer (ID: 3, slug: third-post).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 00:28:48', '2026-03-16 00:28:48'),
(31, 1, 'project_updated', 'Project updated: First (ID: 1, slug: first).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 03:21:01', '2026-03-16 03:21:01'),
(32, 1, 'project_updated', 'Project updated: First (ID: 1, slug: first).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 03:25:29', '2026-03-16 03:25:29'),
(33, 1, 'project_updated', 'Project updated: First (ID: 1, slug: first).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 03:32:23', '2026-03-16 03:32:23'),
(34, 1, 'project_updated', 'Project updated: First (ID: 1, slug: first).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 04:35:06', '2026-03-16 04:35:06'),
(35, 1, 'project_updated', 'Project updated: First (ID: 1, slug: first).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 05:27:08', '2026-03-16 05:27:08'),
(36, NULL, 'admin_login_failed', 'Failed admin login attempt for username: admin.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 10:28:40', '2026-03-16 10:28:40'),
(37, 1, 'admin_login', 'Admin user logged in via admin panel.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 10:28:44', '2026-03-16 10:28:44'),
(38, 1, 'cms_page_updated', 'CMS page updated: Listing Dealers (listing-dealers).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 11:06:52', '2026-03-16 11:06:52'),
(39, 1, 'cms_page_updated', 'CMS page updated: Listing (listing).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 11:08:02', '2026-03-16 11:08:02'),
(40, 1, 'cms_page_updated', 'CMS page updated: Projects (projects).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 11:15:55', '2026-03-16 11:15:55'),
(41, 1, 'admin_login', 'Admin user logged in via admin panel.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 23:16:31', '2026-03-16 23:16:31'),
(42, 1, 'cms_page_updated', 'CMS page updated: Privacy Policy (privacy-policy).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 23:17:16', '2026-03-16 23:17:16'),
(43, 1, 'cms_page_updated', 'CMS page updated: Terms Of Use (terms-of-use).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 23:19:18', '2026-03-16 23:19:18'),
(44, 1, 'cms_page_updated', 'CMS page updated: Our Team (our-team).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-16 23:40:20', '2026-03-16 23:40:20'),
(45, 1, 'dealer_updated', 'Dealer updated: M. Abu Bakar Khan (ID: 1).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 00:05:11', '2026-03-17 00:05:11'),
(46, 1, 'dealer_updated', 'Dealer updated: M. Abu Bakar Khan (ID: 1).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 00:10:06', '2026-03-17 00:10:06'),
(47, 1, 'dealer_updated', 'Dealer updated: M. Abu Bakar Khan (ID: 1).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 00:16:10', '2026-03-17 00:16:10'),
(48, 1, 'dealer_updated', 'Dealer updated: Nadeem (ID: 2).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 00:16:32', '2026-03-17 00:16:32'),
(49, 1, 'dealer_updated', 'Dealer updated: Nadeem (ID: 2).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 00:28:30', '2026-03-17 00:28:30'),
(50, 1, 'cms_page_updated', 'CMS page updated: Careers (careers).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 03:38:08', '2026-03-17 03:38:08'),
(51, 1, 'career_created', 'Career created: CSR (ID: 1).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 03:57:35', '2026-03-17 03:57:35');

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(60) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text DEFAULT NULL,
  `link` varchar(500) DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_notifications`
--

INSERT INTO `admin_notifications` (`id`, `type`, `title`, `body`, `link`, `read_at`, `created_at`, `updated_at`) VALUES
(1, 'job_application', 'New job application: CSR', 'M. Abu Bakar Khan applied for CSR', 'http://localhost/etihad/public/admin/job-applications/1', '2026-03-17 05:33:09', '2026-03-17 05:32:54', '2026-03-17 05:33:09');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `careers`
--

CREATE TABLE `careers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(120) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `department` varchar(120) DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL,
  `experience` varchar(255) DEFAULT NULL,
  `timings` varchar(255) DEFAULT NULL,
  `joining_month` varchar(60) DEFAULT NULL,
  `employment_type` varchar(80) DEFAULT NULL,
  `salary_range` varchar(120) DEFAULT NULL,
  `vacancies` int(10) UNSIGNED DEFAULT NULL,
  `apply_before` date DEFAULT NULL,
  `apply_email` varchar(255) DEFAULT NULL,
  `apply_url` varchar(500) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `meta_keywords` varchar(500) DEFAULT NULL,
  `canonical_url` varchar(500) DEFAULT NULL,
  `requirements` longtext DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `sort_order` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `careers`
--

INSERT INTO `careers` (`id`, `title`, `slug`, `location`, `department`, `education`, `experience`, `timings`, `joining_month`, `employment_type`, `salary_range`, `vacancies`, `apply_before`, `apply_email`, `apply_url`, `meta_title`, `meta_description`, `meta_keywords`, `canonical_url`, `requirements`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'CSR', 'csr-march-2026', 'Lahore', 'Sales', 'F.A', '2 years', '10:00 AM - 6:00 PM', 'April 2026', 'Full Time', '40k', 2, '2026-02-04', NULL, NULL, 'CSR', 'CSR', 'CSR', NULL, 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.\r\nLorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.\r\nLorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.\r\nLorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.', 'active', 1, '2026-03-17 03:57:35', '2026-03-17 03:57:35');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `state_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `state_id`, `name`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Lahore', 1, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(2, 1, 'Faisalabad', 2, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(3, 1, 'Rawalpindi', 3, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(4, 1, 'Multan', 4, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(5, 1, 'Gujranwala', 5, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(6, 1, 'Sialkot', 6, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(7, 1, 'Bahawalpur', 7, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(8, 1, 'Sargodha', 8, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(9, 1, 'Sheikhupura', 9, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(10, 1, 'Rahim Yar Khan', 10, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(11, 1, 'Jhang', 11, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(12, 1, 'Dera Ghazi Khan', 12, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(13, 1, 'Gujrat', 13, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(14, 1, 'Sahiwal', 14, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(15, 1, 'Wah Cantonment', 15, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(16, 1, 'Kasur', 16, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(17, 1, 'Okara', 17, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(18, 1, 'Mandi Bahauddin', 18, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(19, 1, 'Chiniot', 19, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(20, 1, 'Khanewal', 20, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(21, 1, 'Hafizabad', 21, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(22, 1, 'Muzaffargarh', 22, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(23, 1, 'Khanpur', 23, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(24, 1, 'Gojra', 24, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(25, 1, 'Bahawalnagar', 25, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(26, 1, 'Muridke', 26, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(27, 1, 'Pakpattan', 27, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(28, 1, 'Jhelum', 28, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(29, 1, 'Chishtian', 29, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(30, 1, 'Attock', 30, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(31, 1, 'Mianwali', 31, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(32, 1, 'Kamoke', 32, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(33, 1, 'Vihari', 33, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(34, 1, 'Kamalia', 34, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(35, 1, 'Ahmedpur East', 35, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(36, 1, 'Kot Addu', 36, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(37, 1, 'Wazirabad', 37, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(38, 1, 'Layyah', 38, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(39, 1, 'Taxila', 39, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(40, 1, 'Khushab', 40, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(41, 1, 'Mian Channu', 41, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(42, 1, 'Burewala', 42, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(43, 1, 'Chakwal', 43, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(44, 1, 'Toba Tek Singh', 44, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(45, 1, 'Jaranwala', 45, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(46, 1, 'Haroonabad', 46, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(47, 1, 'Narowal', 47, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(48, 1, 'Bhalwal', 48, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(49, 1, 'Hasilpur', 49, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(50, 1, 'Mailsi', 50, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(51, 1, 'Daska', 51, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(52, 1, 'Pattoki', 52, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(53, 1, 'Renala Khurd', 53, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(54, 1, 'Nankana Sahib', 54, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(55, 2, 'Karachi', 1, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(56, 2, 'Hyderabad', 2, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(57, 2, 'Sukkur', 3, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(58, 2, 'Larkana', 4, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(59, 2, 'Nawabshah', 5, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(60, 2, 'Mirpurkhas', 6, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(61, 2, 'Jacobabad', 7, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(62, 2, 'Shikarpur', 8, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(63, 2, 'Khairpur', 9, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(64, 2, 'Dadu', 10, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(65, 2, 'Tando Allahyar', 11, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(66, 2, 'Tando Adam', 12, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(67, 2, 'Badin', 13, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(68, 2, 'Sanghar', 14, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(69, 2, 'Thatta', 15, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(70, 2, 'Naushahro Feroze', 16, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(71, 2, 'Umerkot', 17, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(72, 2, 'Ghotki', 18, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(73, 2, 'Matiari', 19, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(74, 2, 'Jamshoro', 20, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(75, 2, 'Kamber', 21, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(76, 2, 'Kashmore', 22, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(77, 2, 'Tharparkar', 23, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(78, 2, 'Sujawal', 24, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(79, 3, 'Peshawar', 1, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(80, 3, 'Mardan', 2, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(81, 3, 'Mingora', 3, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(82, 3, 'Kohat', 4, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(83, 3, 'Abbottabad', 5, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(84, 3, 'Bannu', 6, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(85, 3, 'Dera Ismail Khan', 7, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(86, 3, 'Swabi', 8, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(87, 3, 'Nowshera', 9, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(88, 3, 'Charsadda', 10, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(89, 3, 'Mansehra', 11, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(90, 3, 'Swat', 12, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(91, 3, 'Haripur', 13, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(92, 3, 'Malakand', 14, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(93, 3, 'Karak', 15, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(94, 3, 'Hangu', 16, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(95, 3, 'Tank', 17, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(96, 3, 'Lakki Marwat', 18, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(97, 3, 'Dera Ismail Khan', 19, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(98, 3, 'Battagram', 20, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(99, 3, 'Upper Dir', 21, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(100, 3, 'Lower Dir', 22, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(101, 3, 'Buner', 23, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(102, 3, 'Shangla', 24, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(103, 3, 'Kohistan', 25, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(104, 3, 'Torghar', 26, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(105, 3, 'Chitral', 27, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(106, 4, 'Quetta', 1, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(107, 4, 'Turbat', 2, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(108, 4, 'Khuzdar', 3, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(109, 4, 'Chaman', 4, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(110, 4, 'Hub', 5, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(111, 4, 'Sibi', 6, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(112, 4, 'Loralai', 7, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(113, 4, 'Zhob', 8, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(114, 4, 'Gwadar', 9, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(115, 4, 'Dera Bugti', 10, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(116, 4, 'Dera Murad Jamali', 11, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(117, 4, 'Usta Muhammad', 12, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(118, 4, 'Surab', 13, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(119, 4, 'Mastung', 14, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(120, 4, 'Nushki', 15, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(121, 4, 'Kalat', 16, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(122, 4, 'Panjgur', 17, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(123, 4, 'Kech', 18, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(124, 4, 'Kharan', 19, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(125, 4, 'Washuk', 20, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(126, 4, 'Awaran', 21, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(127, 4, 'Barkhan', 22, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(128, 4, 'Musakhel', 23, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(129, 4, 'Sherani', 24, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(130, 4, 'Kohlu', 25, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(131, 4, 'Duki', 26, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(132, 4, 'Ziarat', 27, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(133, 4, 'Lehri', 28, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(134, 5, 'Islamabad', 1, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(135, 5, 'Rawalpindi', 2, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(136, 6, 'Gilgit', 1, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(137, 6, 'Skardu', 2, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(138, 6, 'Chilas', 3, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(139, 6, 'Astore', 4, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(140, 6, 'Hunza', 5, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(141, 6, 'Nagar', 6, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(142, 6, 'Ghanche', 7, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(143, 6, 'Shigar', 8, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(144, 6, 'Kharmang', 9, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(145, 6, 'Diamer', 10, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(146, 7, 'Muzaffarabad', 1, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(147, 7, 'Mirpur', 2, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(148, 7, 'Rawalakot', 3, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(149, 7, 'Kotli', 4, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(150, 7, 'Bhimber', 5, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(151, 7, 'Bagh', 6, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(152, 7, 'Neelum', 7, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(153, 7, 'Hattian', 8, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(154, 7, 'Haveli', 9, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(155, 7, 'Sudhnati', 10, '2026-03-14 05:36:11', '2026-03-14 05:36:11');

-- --------------------------------------------------------

--
-- Table structure for table `cms_pages`
--

CREATE TABLE `cms_pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(80) NOT NULL,
  `name` varchar(120) NOT NULL,
  `heading` varchar(255) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `meta_keywords` varchar(500) DEFAULT NULL,
  `canonical_url` varchar(500) DEFAULT NULL,
  `banner_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cms_pages`
--

INSERT INTO `cms_pages` (`id`, `slug`, `name`, `heading`, `content`, `meta_title`, `meta_description`, `meta_keywords`, `canonical_url`, `banner_image`, `created_at`, `updated_at`) VALUES
(1, 'home', 'Home', 'Welcome to Etihad Group', '<p>Etihad Group is a leading real estate developer in Pakistan. We deliver quality residential and commercial projects with a commitment to excellence and customer satisfaction.</p><p>Explore our featured projects and property listings to find your ideal investment or dream home.</p>', 'Etihad Group | Premier Real Estate in Pakistan', 'Etihad Group offers premium real estate projects and property listings across Pakistan. Find your dream home or investment opportunity.', 'real estate, Pakistan, Etihad Group, property, projects, housing', NULL, NULL, '2026-03-16 11:02:14', '2026-03-16 11:02:14'),
(2, 'about-us', 'About Us', 'About Etihad Group', '<p>Founded in 2004, Etihad Group has established itself as one of Pakistan\'s most trusted real estate developers. Our mission is to lead the land development sector and rank as the country\'s top property developer.</p><p>We are known for exceptional customer care and the delivery of quality projects including renowned developments such as the LUMS Campus. Our team is dedicated to helping clients find their ideal home or investment.</p>', 'About Us | Etihad Group Real Estate', 'Learn about Etihad Group, a leading real estate developer in Pakistan since 2004. Our mission, values, and commitment to quality.', 'about Etihad Group, real estate Pakistan, property developer', NULL, NULL, '2026-03-16 11:02:14', '2026-03-16 11:02:14'),
(3, 'contact-us', 'Contact Us', 'Get in Touch', '<p>We would love to hear from you. Whether you have a question about our projects, listings, or need assistance, our team is ready to help.</p><p>Reach out via the details in Contact Settings, or use the form below. We typically respond within 24 hours.</p>', 'Contact Us | Etihad Group', 'Contact Etihad Group for inquiries about real estate projects and property listings. We are here to help.', 'contact Etihad Group, real estate inquiry, Pakistan', NULL, NULL, '2026-03-16 11:02:14', '2026-03-16 11:02:14'),
(4, 'listing', 'Listing', 'Property Listings', '<p>Browse our handpicked property listings. We offer a wide range of options including plots, homes, apartments, and commercial spaces to suit every need and budget.</p><p>Use the filters to narrow your search by location, type, and purpose. All listings are verified and updated regularly.</p>', 'Property Listings | Etihad Group', 'Browse property listings from Etihad Group. Plots, homes, apartments, and commercial real estate across Pakistan.', 'property listings, real estate, Pakistan, plots, homes', NULL, 'cms/banners/fGoSkLtGSt5MU4tFp2Ierc1wN3RV732rQHIiDR0h.jpg', '2026-03-16 11:02:14', '2026-03-16 11:08:02'),
(5, 'listing-dealers', 'Listing Dealers', 'Dealer Listings', '<p>Explore properties listed by our verified dealers. These listings offer additional variety and options from trusted partners across the region.</p><p>Each dealer is vetted to ensure quality and reliability. Find your next property from our dealer network.</p>', 'Dealer Listings | Etihad Group', 'Browse dealer property listings on Etihad Group. Verified dealers and quality real estate options.', 'dealer listings, property dealers, real estate Pakistan', NULL, 'cms/banners/nZlWh6m5P3GygBRAY9XHiypwR0vqmNwWc06XC4Np.jpg', '2026-03-16 11:02:14', '2026-03-16 11:06:52'),
(6, 'projects', 'Projects', 'Our Projects', '<p>Discover Etihad Group\'s portfolio of residential and commercial projects across Pakistan. From master-planned communities to premium developments, we deliver quality and value.</p><p>Browse our active projects, explore amenities and locations, and find the right investment or home for you.</p>', 'Our Projects | Etihad Group Real Estate', 'Explore Etihad Group real estate projects in Pakistan. Residential and commercial developments with quality construction and prime locations.', 'Etihad Group projects, real estate projects Pakistan, housing developments', NULL, 'cms/banners/Voj3jWXkELPE45hbvzLPm6CfIQJt452sMzM05Ie3.jpg', '2026-03-16 11:14:58', '2026-03-16 11:15:55'),
(7, 'privacy-policy', 'Privacy Policy', 'Privacy Policy', '<h1>Privacy Policy for Etihad Marketing</h1><p>At Etihad, accessible from https://etihadmarketing.co/, one of our main priorities is the privacy of our visitors. This Privacy Policy document contains types of information that is collected and recorded by Etihad and how we use it.</p><p>If you have additional questions or require more information about our Privacy Policy, do not hesitate to contact us.</p><p>This Privacy Policy applies only to our online activities and is valid for visitors to our website with regards to the information that they shared and/or collect in Etihad. This policy is not applicable to any information collected offline or via channels other than this website.</p><h2>Consent</h2><p>By using our website, you hereby consent to our Privacy Policy and agree to its terms.</p><h2>Information we collect</h2><p>The personal information that you are asked to provide, and the reasons why you are asked to provide it, will be made clear to you at the point we ask you to provide your personal information.</p><p>If you contact us directly, we may receive additional information about you such as your name, email address, phone number, the contents of the message and/or attachments you may send us, and any other information you may choose to provide.</p><p>When you register for an Account, we may ask for your contact information, including items such as name, company name, address, email address, and telephone number.</p><h2>How we use your information</h2><p>We use the information we collect in various ways, including to:</p><ul><li>Provide, operate, and maintain our website</li><li>Improve, personalize, and expand our website</li><li>Understand and analyze how you use our website</li><li>Develop new products, services, features, and functionality</li><li>Communicate with you, either directly or through one of our partners, including for customer service, to provide you with updates and other information relating to the website, and for marketing and promotional purposes</li><li>Send you emails</li><li>Find and prevent fraud</li></ul><h2>Log Files</h2><p>Etihad follows a standard procedure of using log files. These files log visitors when they visit websites. All hosting companies do this and a part of hosting services’ analytics. The information collected by log files include internet protocol (IP) addresses, browser type, Internet Service Provider (ISP), date and time stamp, referring/exit pages, and possibly the number of clicks. These are not linked to any information that is personally identifiable. The purpose of the information is for analyzing trends, administering the site, tracking users’ movement on the website, and gathering demographic information.</p><h2>Advertising Partners Privacy Policies</h2><p>You may consult this list to find the Privacy Policy for each of the advertising partners of Etihad.</p><p>Third-party ad servers or ad networks uses technologies like cookies, JavaScript, or Web Beacons that are used in their respective advertisements and links that appear on Etihad, which are sent directly to users’ browser. They automatically receive your IP address when this occurs. These technologies are used to measure the effectiveness of their advertising campaigns and/or to personalize the advertising content that you see on websites that you visit.</p><p>Note that Etihad has no access to or control over these cookies that are used by third-party advertisers.</p><h2>Third Party Privacy Policies</h2><p>Etihad’s Privacy Policy does not apply to other advertisers or websites. Thus, we are advising you to consult the respective Privacy Policies of these third-party ad servers for more detailed information. It may include their practices and instructions about how to opt-out of certain options.</p><p>You can choose to disable cookies through your individual browser options. To know more detailed information about cookie management with specific web browsers, it can be found at the browsers’ respective websites.</p><h2>CCPA Privacy Rights (Do Not Sell My Personal Information)</h2><p>Under the CCPA, among other rights, California consumers have the right to:</p><p>Request that a business that collects a consumer’s personal data disclose the categories and specific pieces of personal data that a business has collected about consumers.</p><p>Request that a business delete any personal data about the consumer that a business has collected.</p><p>Request that a business that sells a consumer’s personal data, not sell the consumer’s personal data.</p><p>If you make a request, we have one month to respond to you. If you would like to exercise any of these rights, please contact us.</p><h2>GDPR Data Protection Rights</h2><p>We would like to make sure you are fully aware of all of your data protection rights. Every user is entitled to the following:</p><p>The right to access – You have the right to request copies of your personal data. We may charge you a small fee for this service.</p><p>The right to rectification – You have the right to request that we correct any information you believe is inaccurate. You also have the right to request that we complete the information you believe is incomplete.</p><p>The right to erasure – You have the right to request that we erase your personal data, under certain conditions.</p><p>The right to restrict processing – You have the right to request that we restrict the processing of your personal data, under certain conditions.</p><p>The right to object to processing – You have the right to object to our processing of your personal data, under certain conditions.</p><p>The right to data portability – You have the right to request that we transfer the data that we have collected to another organization, or directly to you, under certain conditions.</p><p>If you make a request, we have one month to respond to you. If you would like to exercise any of these rights, please contact us.</p><h2>Children’s Information</h2><p>Another part of our priority is adding protection for children while using the internet. We encourage parents and guardians to observe, participate in, and/or monitor and guide their online activity.</p><p>Etihad does not knowingly collect any Personal Identifiable Information from children under the age of 13. If you think that your child provided this kind of information on our website, we strongly encourage you to contact us immediately and we will do our best efforts to promptly remove such information from our records.</p>', 'Privacy Policy | Etihad Group Real Estate', 'Read our Privacy Policy. We explain how we collect, use, and protect your information when you use our website and property services.', 'privacy policy, data protection, personal information, cookies', NULL, 'cms/banners/AgvXAd5BtDTfRe4seV4WpWtoQ5XZWLHle0AQsbZb.jpg', '2026-03-16 23:16:09', '2026-03-16 23:17:16'),
(8, 'terms-of-use', 'Terms Of Use', 'Terms Of Use', '<p>The Etihad Marketing website located at https://etihadmarketing.co/ is a copyrighted work belonging to Etihad . Certain features of the Site may be subject to additional guidelines, terms, or rules, which will be posted on the Site in connection with such features.</p><p>All such additional terms, guidelines, and rules are incorporated by reference into these Terms.</p><p>These Terms of Use described the legally binding terms and conditions that oversee your use of the Site. BY LOGGING INTO THE SITE, YOU ARE BEING COMPLIANT THAT THESE TERMS and you represent that you have the authority and capacity to enter into these Terms. YOU SHOULD BE AT LEAST 18 YEARS OF AGE TO ACCESS THE SITE. IF YOU DISAGREE WITH ALL OF THE PROVISION OF THESE TERMS, DO NOT LOG INTO AND/OR USE THE SITE.</p><p>These terms require the use of arbitration Section 10.2 on an individual basis to resolve disputes and also limit the remedies available to you in the event of a dispute. These Terms of Use were created with the help of the Terms Of Use Generator.</p><p><strong>Access to the Site</strong></p><p>Subject to these Terms. Company grants you a non-transferable, non-exclusive, revocable, limited license to access the Site solely for your own personal, noncommercial use.</p><p>Certain Restrictions. The rights approved to you in these Terms are subject to the following restrictions: (a) you shall not sell, rent, lease, transfer, assign, distribute, host, or otherwise commercially exploit the Site; (b) you shall not change, make derivative works of, disassemble, reverse compile or reverse engineer any part of the Site; (c) you shall not access the Site in order to build a similar or competitive website; and (d) except as expressly stated herein, no part of the Site may be copied, reproduced, distributed, republished, downloaded, displayed, posted or transmitted in any form or by any means unless otherwise indicated, any future release, update, or other addition to functionality of the Site shall be subject to these Terms. All copyright and other proprietary notices on the Site must be retained on all copies thereof.</p><p>Company reserves the right to change, suspend, or cease the Site with or without notice to you. You approved that Company will not be held liable to you or any third-party for any change, interruption, or termination of the Site or any part.</p><p>No Support or Maintenance. You agree that Company will have no obligation to provide you with any support in connection with the Site.</p><p>Excluding any User Content that you may provide, you are aware that all the intellectual property rights, including copyrights, patents, trademarks, and trade secrets, in the Site and its content are owned by Company or Company’s suppliers. Note that these Terms and access to the Site do not give you any rights, title or interest in or to any intellectual property rights, except for the limited access rights expressed in Section 2.1. Company and its suppliers reserve all rights not granted in these Terms.</p><p><strong>Third-Party Links &amp; Ads; Other Users</strong></p><p>Third-Party Links &amp; Ads. The Site may contain links to third-party websites and services, and/or display advertisements for third-parties. Such Third-Party Links &amp; Ads are not under the control of Company, and Company is not responsible for any Third-Party Links &amp; Ads. Company provides access to these Third-Party Links &amp; Ads only as a convenience to you, and does not review, approve, monitor, endorse, warrant, or make any representations with respect to Third-Party Links &amp; Ads. You use all Third-Party Links &amp; Ads at your own risk, and should apply a suitable level of caution and discretion in doing so. When you click on any of the Third-Party Links &amp; Ads, the applicable third party’s terms and policies apply, including the third party’s privacy and data gathering practices.</p><p>Other Users. Each Site user is solely responsible for any and all of its own User Content. Because we do not control User Content, you acknowledge and agree that we are not responsible for any User Content, whether provided by you or by others. You agree that Company will not be responsible for any loss or damage incurred as the result of any such interactions. If there is a dispute between you and any Site user, we are under no obligation to become involved.</p><p>You hereby release and forever discharge the Company and our officers, employees, agents, successors, and assigns from, and hereby waive and relinquish, each and every past, present and future dispute, claim, controversy, demand, right, obligation, liability, action and cause of action of every kind and nature, that has arisen or arises directly or indirectly out of, or that relates directly or indirectly to, the Site. If you are a California resident, you hereby waive California civil code section 1542 in connection with the foregoing, which states: “a general release does not extend to claims which the creditor does not know or suspect to exist in his or her favor at the time of executing the release, which if known by him or her must have materially affected his or her settlement with the debtor.”</p><p><strong>Disclaimers</strong></p><p>The site is provided on an “as-is” and “as available” basis, and company and our suppliers expressly disclaim any and all warranties and conditions of any kind, whether express, implied, or statutory, including all warranties or conditions of merchantability, fitness for a particular purpose, title, quiet enjoyment, accuracy, or non-infringement. We and our suppliers make not guarantee that the site will meet your requirements, will be available on an uninterrupted, timely, secure, or error-free basis, or will be accurate, reliable, free of viruses or other harmful code, complete, legal, or safe. If applicable law requires any warranties with respect to the site, all such warranties are limited in duration to ninety (90) days from the date of first use.</p><p>Some jurisdictions do not allow the exclusion of implied warranties, so the above exclusion may not apply to you. Some jurisdictions do not allow limitations on how long an implied warranty lasts, so the above limitation may not apply to you.</p><p><strong>Limitation on Liability</strong></p><p>To the maximum extent permitted by law, in no event shall company or our suppliers be liable to you or any third-party for any lost profits, lost data, costs of procurement of substitute products, or any indirect, consequential, exemplary, incidental, special or punitive damages arising from or relating to these terms or your use of, or incapability to use the site even if company has been advised of the possibility of such damages. Access to and use of the site is at your own discretion and risk, and you will be solely responsible for any damage to your device or computer system, or loss of data resulting therefrom.</p><p>To the maximum extent permitted by law, notwithstanding anything to the contrary contained herein, our liability to you for any damages arising from or related to this agreement, will at all times be limited to a maximum of fifty U.S. dollars (u.s. $50). The existence of more than one claim will not enlarge this limit. You agree that our suppliers will have no liability of any kind arising from or relating to this agreement.</p><p>Some jurisdictions do not allow the limitation or exclusion of liability for incidental or consequential damages, so the above limitation or exclusion may not apply to you.</p><p>Term and Termination. Subject to this Section, these Terms will remain in full force and effect while you use the Site. We may suspend or terminate your rights to use the Site at any time for any reason at our sole discretion, including for any use of the Site in violation of these Terms. Upon termination of your rights under these Terms, your Account and right to access and use the Site will terminate immediately. You understand that any termination of your Account may involve deletion of your User Content associated with your Account from our live databases. Company will not have any liability whatsoever to you for any termination of your rights under these Terms. Even after your rights under these Terms are terminated, the following provisions of these Terms will remain in effect: Sections 2 through 2.5, Section 3 and Sections 4 through 10.</p><p><strong>Copyright Policy.</strong></p><p>Company respects the intellectual property of others and asks that users of our Site do the same. In connection with our Site, we have adopted and implemented a policy respecting copyright law that provides for the removal of any infringing materials and for the termination of users of our online Site who are repeated infringers of intellectual property rights, including copyrights. If you believe that one of our users is, through the use of our Site, unlawfully infringing the copyright(s) in a work, and wish to have the allegedly infringing material removed, the following information in the form of a written notification (pursuant to 17 U.S.C. § 512(c)) must be provided to our designated Copyright Agent:</p><p>your physical or electronic signature;</p><p>identification of the copyrighted work(s) that you claim to have been infringed;</p><p>identification of the material on our services that you claim is infringing and that you request us to remove;</p><p>sufficient information to permit us to locate such material;</p><p>your address, telephone number, and e-mail address;</p><p>a statement that you have a good faith belief that use of the objectionable material is not authorized by the copyright owner, its agent, or under the law; and</p><p>a statement that the information in the notification is accurate, and under penalty of perjury, that you are either the owner of the copyright that has allegedly been infringed or that you are authorized to act on behalf of the copyright owner.</p><p>Please note that, pursuant to 17 U.S.C. § 512(f), any misrepresentation of material fact in a written notification automatically subjects the complaining party to liability for any damages, costs and attorney’s fees incurred by us in connection with the written notification and allegation of copyright infringement.</p><p><strong>General</strong></p><p>These Terms are subject to occasional revision, and if we make any substantial changes, we may notify you by sending you an e-mail to the last e-mail address you provided to us and/or by prominently posting notice of the changes on our Site. You are responsible for providing us with your most current e-mail address. In the event that the last e-mail address that you have provided us is not valid our dispatch of the e-mail containing such notice will nonetheless constitute effective notice of the changes described in the notice. Any changes to these Terms will be effective upon the earliest of thirty (30) calendar days following our dispatch of an e-mail notice to you or thirty (30) calendar days following our posting of notice of the changes on our Site. These changes will be effective immediately for new users of our Site. Continued use of our Site following notice of such changes shall indicate your acknowledgement of such changes and agreement to be bound by the terms and conditions of such changes. Dispute Resolution. Please read this Arbitration Agreement carefully. It is part of your contract with Company and affects your rights. It contains procedures for MANDATORY BINDING ARBITRATION AND A CLASS ACTION WAIVER.</p><p>Applicability of Arbitration Agreement. All claims and disputes in connection with the Terms or the use of any product or service provided by the Company that cannot be resolved informally or in small claims court shall be resolved by binding arbitration on an individual basis under the terms of this Arbitration Agreement. Unless otherwise agreed to, all arbitration proceedings shall be held in English. This Arbitration Agreement applies to you and the Company, and to any subsidiaries, affiliates, agents, employees, predecessors in interest, successors, and assigns, as well as all authorized or unauthorized users or beneficiaries of services or goods provided under the Terms.</p><p>Notice Requirement and Informal Dispute Resolution. Before either party may seek arbitration, the party must first send to the other party a written Notice of Dispute describing the nature and basis of the claim or dispute, and the requested relief. A Notice to the Company should be sent to: Etihad. After the Notice is received, you and the Company may attempt to resolve the claim or dispute informally. If you and the Company do not resolve the claim or dispute within thirty (30) days after the Notice is received, either party may begin an arbitration proceeding. The amount of any settlement offer made by any party may not be disclosed to the arbitrator until after the arbitrator has determined the amount of the award to which either party is entitled.</p><p>Arbitration Rules. Arbitration shall be initiated through the American Arbitration Association, an established alternative dispute resolution provider that offers arbitration as set forth in this section. If AAA is not available to arbitrate, the parties shall agree to select an alternative ADR Provider. The rules of the ADR Provider shall govern all aspects of the arbitration except to the extent such rules are in conflict with the Terms. The AAA Consumer Arbitration Rules governing the arbitration are available online at adr.org or by calling the AAA at 1-800-778-7879. The arbitration shall be conducted by a single, neutral arbitrator. Any claims or disputes where the total amount of the award sought is less than Ten Thousand U.S. Dollars (US $10,000.00) may be resolved through binding non-appearance-based arbitration, at the option of the party seeking relief. For claims or disputes where the total amount of the award sought is Ten Thousand U.S. Dollars (US $10,000.00) or more, the right to a hearing will be determined by the Arbitration Rules. Any hearing will be held in a location within 100 miles of your residence, unless you reside outside of the United States, and unless the parties agree otherwise. If you reside outside of the U.S., the arbitrator shall give the parties reasonable notice of the date, time and place of any oral hearings. Any judgment on the award rendered by the arbitrator may be entered in any court of competent jurisdiction. If the arbitrator grants you an award that is greater than the last settlement offer that the Company made to you prior to the initiation of arbitration, the Company will pay you the greater of the award or $2,500.00. Each party shall bear its own costs and disbursements arising out of the arbitration and shall pay an equal share of the fees and costs of the ADR Provider.</p><p>Additional Rules for Non-Appearance Based Arbitration. If non-appearance based arbitration is elected, the arbitration shall be conducted by telephone, online and/or based solely on written submissions; the specific manner shall be chosen by the party initiating the arbitration. The arbitration shall not involve any personal appearance by the parties or witnesses unless otherwise agreed by the parties.</p><p>Time Limits. If you or the Company pursues arbitration, the arbitration action must be initiated and/or demanded within the statute of limitations and within any deadline imposed under the AAA Rules for the pertinent claim.</p><p>Authority of Arbitrator. If arbitration is initiated, the arbitrator will decide the rights and liabilities of you and the Company, and the dispute will not be consolidated with any other matters or joined with any other cases or parties. The arbitrator shall have the authority to grant motions dispositive of all or part of any claim. The arbitrator shall have the authority to award monetary damages, and to grant any non-monetary remedy or relief available to an individual under applicable law, the AAA Rules, and the Terms. The arbitrator shall issue a written award and statement of decision describing the essential findings and conclusions on which the award is based. The arbitrator has the same authority to award relief on an individual basis that a judge in a court of law would have. The award of the arbitrator is final and binding upon you and the Company.</p><p>Waiver of Jury Trial. THE PARTIES HEREBY WAIVE THEIR CONSTITUTIONAL AND STATUTORY RIGHTS TO GO TO COURT AND HAVE A TRIAL IN FRONT OF A JUDGE OR A JURY, instead electing that all claims and disputes shall be resolved by arbitration under this Arbitration Agreement. Arbitration procedures are typically more limited, more efficient and less expensive than rules applicable in a court and are subject to very limited review by a court. In the event any litigation should arise between you and the Company in any state or federal court in a suit to vacate or enforce an arbitration award or otherwise, YOU AND THE COMPANY WAIVE ALL RIGHTS TO A JURY TRIAL, instead electing that the dispute be resolved by a judge.</p><p>Waiver of Class or Consolidated Actions. All claims and disputes within the scope of this arbitration agreement must be arbitrated or litigated on an individual basis and not on a class basis, and claims of more than one customer or user cannot be arbitrated or litigated jointly or consolidated with those of any other customer or user.</p><p>Confidentiality. All aspects of the arbitration proceeding shall be strictly confidential. The parties agree to maintain confidentiality unless otherwise required by law. This paragraph shall not prevent a party from submitting to a court of law any information necessary to enforce this Agreement, to enforce an arbitration award, or to seek injunctive or equitable relief.</p><p>Severability. If any part or parts of this Arbitration Agreement are found under the law to be invalid or unenforceable by a court of competent jurisdiction, then such specific part or parts shall be of no force and effect and shall be severed and the remainder of the Agreement shall continue in full force and effect.</p><p>Right to Waive. Any or all of the rights and limitations set forth in this Arbitration Agreement may be waived by the party against whom the claim is asserted. Such waiver shall not waive or affect any other portion of this Arbitration Agreement.</p><p>Survival of Agreement. This Arbitration Agreement will survive the termination of your relationship with Company.</p><p>Small Claims Court. Nonetheless the foregoing, either you or the Company may bring an individual action in small claims court.</p><p>Emergency Equitable Relief. Anyhow the foregoing, either party may seek emergency equitable relief before a state or federal court in order to maintain the status quo pending arbitration. A request for interim measures shall not be deemed a waiver of any other rights or obligations under this Arbitration Agreement.</p><p>Claims Not Subject to Arbitration. Notwithstanding the foregoing, claims of defamation, violation of the Computer Fraud and Abuse Act, and infringement or misappropriation of the other party’s patent, copyright, trademark or trade secrets shall not be subject to this Arbitration Agreement.</p><p>In any circumstances where the foregoing Arbitration Agreement permits the parties to litigate in court, the parties hereby agree to submit to the personal jurisdiction of the courts located within Netherlands County, California, for such purposes.</p><p>The Site may be subject to U.S. export control laws and may be subject to export or import regulations in other countries. You agree not to export, re-export, or transfer, directly or indirectly, any U.S. technical data acquired from Company, or any products utilizing such data, in violation of the United States export laws or regulations.</p><p>Company is located at the address in Section 10.8. If you are a California resident, you may report complaints to the Complaint Assistance Unit of the Division of Consumer Product of the California Department of Consumer Affairs by contacting them in writing at 400 R Street, Sacramento, CA 95814, or by telephone at (800) 952-5210.</p><p>Electronic Communications. The communications between you and Company use electronic means, whether you use the Site or send us emails, or whether Company posts notices on the Site or communicates with you via email. For contractual purposes, you (a) consent to receive communications from Company in an electronic form; and (b) agree that all terms and conditions, agreements, notices, disclosures, and other communications that Company provides to you electronically satisfy any legal obligation that such communications would satisfy if it were be in a hard copy writing.</p><p>Entire Terms. These Terms constitute the entire agreement between you and us regarding the use of the Site. Our failure to exercise or enforce any right or provision of these Terms shall not operate as a waiver of such right or provision. The section titles in these Terms are for convenience only and have no legal or contractual effect. The word “including” means “including without limitation”. If any provision of these Terms is held to be invalid or unenforceable, the other provisions of these Terms will be unimpaired and the invalid or unenforceable provision will be deemed modified so that it is valid and enforceable to the maximum extent permitted by law. Your relationship to Company is that of an independent contractor, and neither party is an agent or partner of the other. These Terms, and your rights and obligations herein, may not be assigned, subcontracted, delegated, or otherwise transferred by you without Company’s prior written consent, and any attempted assignment, subcontract, delegation, or transfer in violation of the foregoing will be null and void. Company may freely assign these Terms. The terms and conditions set forth in these Terms shall be binding upon assignees.</p><p><strong>Your Privacy. Please read our Privacy Policy.</strong></p><p>Copyright/Trademark Information. Copyright ©. All rights reserved. All trademarks, logos and service marks displayed on the Site are our property or the property of other third parties. You are not permitted to use these Marks without our prior written consent or the consent of such third party which may own the Marks.</p><p>Contact Information</p><p>Address: Etihad</p><p>Email: info@etihadmarketing.co</p>', 'Terms Of Use | Etihad Group Real Estate', 'Terms of Use for our website. Please read the conditions that apply when you use our property listings and services.', 'terms of use, terms and conditions, website terms', NULL, 'cms/banners/buue1Pr3QU0tDyxlGzKlCsG3eTdiIXhzQzwjM3BL.jpg', '2026-03-16 23:18:44', '2026-03-16 23:19:18'),
(9, 'our-team', 'Our Team', 'Our Team', '<p>Meet our dedicated team of professionals. We work together to deliver the best real estate experience.</p>', 'Our Team | Etihad Group Real Estate', 'Meet the Etihad Group team. Our dealers and professionals are here to help you find your ideal property.', 'our team, dealers, real estate team, Etihad Group', NULL, 'cms/banners/M1nMTx0mGyjGy2gCTid6yb5GzYhWJUDlo2foDjKU.jpg', '2026-03-16 23:33:40', '2026-03-16 23:40:20'),
(10, 'careers', 'Careers', 'Careers', '<p>Join our team. Explore open positions and find your next opportunity at Etihad Group.</p>', 'Careers | Etihad Group', 'View career opportunities at Etihad Group. Browse open positions and apply today.', 'careers, jobs, Etihad Group, employment', NULL, 'cms/banners/LRzFQOpsd6qsaFnLLonUPjvNeLImitEQLtN9zDCW.jpg', '2026-03-17 03:35:07', '2026-03-17 03:38:08');

-- --------------------------------------------------------

--
-- Table structure for table `contact_settings`
--

CREATE TABLE `contact_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `address` text DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_settings`
--

INSERT INTO `contact_settings` (`id`, `address`, `latitude`, `longitude`, `email`, `phone`, `whatsapp`, `facebook`, `instagram`, `linkedin`, `youtube`, `created_at`, `updated_at`) VALUES
(1, '123 Main Boulevard, Lahore, Punjab, Pakistan', 31.5204000, 74.3587000, 'info@etihadgroup.com', '+92 42 123 4567', '+92 300 1234567', 'https://facebook.com/etihadgroup', 'https://instagram.com/etihadgroup', 'https://linkedin.com/company/etihadgroup', 'https://youtube.com/@etihadgroup', '2026-03-16 04:05:53', '2026-03-16 04:05:53');

-- --------------------------------------------------------

--
-- Table structure for table `dealers`
--

CREATE TABLE `dealers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(120) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `info_detail` text DEFAULT NULL,
  `view_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `meta_keywords` varchar(500) DEFAULT NULL,
  `canonical_url` varchar(500) DEFAULT NULL,
  `banner_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dealers`
--

INSERT INTO `dealers` (`id`, `name`, `slug`, `status`, `email`, `phone`, `whatsapp`, `mobile`, `address`, `city`, `state`, `profile_pic`, `info_detail`, `view_count`, `meta_title`, `meta_description`, `meta_keywords`, `canonical_url`, `banner_image`, `created_at`, `updated_at`) VALUES
(1, 'M. Abu Bakar Khan', 'mabubakarkhan', 'active', 'm.abubakar.khan.6692@gmail.com', '03331022025', '03331022025', '03331022025', 'Canal Bank Road', 'Lahore', 'Punjab', 'dealers/EJNgg9BgAswwhxxq5x6ROLRjkNphCiUGUf4VLFkg.jpg', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.', 6, 'M. Abu Bakar Khan', 'M. Abu Bakar Khan', 'M. Abu Bakar Khan', NULL, 'dealers/banners/TQMXc1bTOTalFYLmYXOK6uWfnuHhJVCiknWS6VNy.jpg', '2026-03-14 10:16:38', '2026-03-17 01:25:11'),
(2, 'Nadeem', 'nadeem', 'active', 'nadeem@domian.com', '03331022028', '03331022028', '03331022028', 'Hajb=very uni', 'Lahore', 'Punjab', 'dealers/HnvgfFCZXm9zdwBDxlQ99zLBenPGKw6UlzDiHRjr.png', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.', 0, 'Nadeem', 'Nadeem', 'Nadeem', NULL, 'dealers/banners/sr0wCfGyjopyUd6H4wGHZbWcP2xc7dc6Iwb9VuaR.jpg', '2026-03-14 10:17:39', '2026-03-17 00:28:30');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_applications`
--

CREATE TABLE `job_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `career_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `mobile` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(120) DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `cv_path` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_applications`
--

INSERT INTO `job_applications` (`id`, `career_id`, `name`, `mobile`, `email`, `address`, `city`, `education`, `comments`, `cv_path`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'M. Abu Bakar Khan', '03331022028', 'm.abubakar.khan.6692@gmail.com', 'Canal Bank Rd', 'Lahore', 'asd', 'Helojd', 'job-applications/vG6Ql31JoKOcheW9VtDufbUlb7ZrQsXX3ICczgn4.pdf', 'new', '2026-03-17 05:32:54', '2026-03-17 05:32:54');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(6, '2026_03_14_085950_add_username_to_users_table', 2),
(7, '2026_03_14_085952_create_admin_user', 2),
(8, '2026_03_14_090236_update_admin_password_to_chorchot', 3),
(9, '2026_03_14_090542_update_admin_password_to_chor', 4),
(10, '2026_03_14_090000_create_roles_and_permissions_tables', 5),
(11, '2026_03_14_090100_create_activity_logs_table', 5),
(12, '2026_03_14_091500_add_settings_to_users_table', 6),
(13, '2026_03_14_092000_attach_manage_users_to_user_role', 7),
(14, '2026_03_14_100000_drop_roles_and_permissions_tables', 8),
(15, '2026_03_14_110000_create_project_types_table', 9),
(16, '2026_03_14_110001_create_projects_table', 9),
(17, '2026_03_14_120000_project_project_types_pivot', 10),
(18, '2026_03_14_130000_create_states_and_cities_tables', 11),
(19, '2026_03_14_140000_add_latitude_longitude_to_projects', 12),
(20, '2026_03_14_150000_change_featured_youtube_to_text', 13),
(21, '2026_03_14_160000_create_dealers_table', 14),
(22, '2026_03_14_160001_create_properties_table', 14),
(23, '2026_03_15_100000_add_seo_to_projects_and_properties', 15),
(24, '2026_03_15_120000_add_status_to_projects_and_properties', 16),
(25, '2026_03_15_140000_add_status_to_dealers', 17),
(26, '2026_03_16_100000_property_multiple_project_types_and_purpose', 18),
(27, '2026_03_16_120000_create_visitor_daily_counts_table', 19),
(28, '2026_03_16_130000_add_visitor_breakdown_to_visitor_daily_counts', 20),
(29, '2026_03_16_140000_create_property_requests_table', 21),
(30, '2026_03_17_100000_add_developer_logo_to_projects', 22),
(31, '2026_03_17_110000_create_contact_settings_table', 23),
(32, '2026_03_17_120000_add_view_count_to_properties_and_projects', 24),
(33, '2026_03_16_151308_add_project_id_to_property_requests_table', 25),
(34, '2026_03_18_100000_add_status_to_property_requests_table', 26),
(35, '2026_03_19_100000_create_cms_pages_table', 27),
(36, '2026_03_20_100000_insert_projects_cms_page', 28),
(37, '2026_03_19_120000_seed_projects_cms_page', 29),
(38, '2026_03_19_130000_seed_privacy_policy_cms_page', 30),
(39, '2026_03_19_140000_seed_terms_of_use_cms_page', 31),
(40, '2026_03_21_100000_add_info_detail_to_dealers', 32),
(41, '2026_03_21_110000_seed_our_teams_cms_page', 32),
(42, '2026_03_21_120000_add_view_count_to_dealers', 33),
(43, '2026_03_22_100000_add_slug_to_dealers', 34),
(44, '2026_03_22_110000_add_meta_and_banner_to_dealers', 35),
(45, '2026_03_23_100000_seed_careers_cms_page', 36),
(46, '2026_03_23_101000_create_careers_table', 36),
(47, '2026_03_23_102000_add_metadata_to_careers_table', 37),
(48, '2026_03_25_100000_create_job_applications_table', 38),
(49, '2026_03_25_101000_create_admin_notifications_table', 38),
(50, '2026_03_26_100000_add_email_to_job_applications', 39);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `price` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `short_address` varchar(255) DEFAULT NULL,
  `full_address` text DEFAULT NULL,
  `google_map` text DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `address_image` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `homepage_listing_image` varchar(255) DEFAULT NULL,
  `featured_youtube_url` text DEFAULT NULL,
  `featured_video_title` varchar(255) DEFAULT NULL,
  `featured_video_description` longtext DEFAULT NULL,
  `about_developers` longtext DEFAULT NULL,
  `developer_logo` varchar(255) DEFAULT NULL,
  `project_file_pdf` varchar(255) DEFAULT NULL,
  `noc_planning_content` longtext DEFAULT NULL,
  `noc_planning_image` varchar(255) DEFAULT NULL,
  `future_note_title` varchar(255) DEFAULT NULL,
  `future_note_content` longtext DEFAULT NULL,
  `extra_section_title` varchar(255) DEFAULT NULL,
  `extra_section_content` longtext DEFAULT NULL,
  `unique_features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`unique_features`)),
  `price_plan_section_title` varchar(255) DEFAULT NULL,
  `price_plan_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`price_plan_items`)),
  `faqs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`faqs`)),
  `plans` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`plans`)),
  `title_descriptions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`title_descriptions`)),
  `videos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`videos`)),
  `gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gallery`)),
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `view_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `slug`, `status`, `price`, `description`, `state`, `city`, `short_address`, `full_address`, `google_map`, `latitude`, `longitude`, `address_image`, `logo`, `featured_image`, `homepage_listing_image`, `featured_youtube_url`, `featured_video_title`, `featured_video_description`, `about_developers`, `developer_logo`, `project_file_pdf`, `noc_planning_content`, `noc_planning_image`, `future_note_title`, `future_note_content`, `extra_section_title`, `extra_section_content`, `unique_features`, `price_plan_section_title`, `price_plan_items`, `faqs`, `plans`, `title_descriptions`, `videos`, `gallery`, `meta_title`, `meta_description`, `meta_keywords`, `canonical_url`, `view_count`, `created_at`, `updated_at`) VALUES
(1, 'First', 'first', 'active', 'starting from 3000', 'Nestled in the heart of Lahore, Etihad Town Phase 1 is a hidden gem waiting to be discovered. As you enter this enchanting residential community, you’ll immediately feel a sense of charm and tranquility. The lush green landscapes and meticulously designed streets create a picturesque environment that is simply breathtaking. Take a stroll through the well-maintained parks, or enjoy a leisurely bike ride along the tree-lined avenues. The captivating architecture of the houses adds to the charm, with each home showcasing its own unique style and character.\r\nWhether you’re seeking a peaceful retreat or a vibrant community to call home, Etihad Town\r\nPhase 1 has it all. So come and discover the hidden charm of this remarkable residential\r\ncommunity in Lahore. You won’t be disappointed.', 'Punjab', 'Lahore', 'Raiwind Rd, Lahore', 'Raiwind Rd, Lahore', 'google map', 31.51650000, 74.34990000, 'projects/1/address/TRpCIpONJVuANJabydyBddlBMcfxepNQpmTKIEH8.png', 'projects/1/logo/MUKOSubfupJRBJ65EVCKAa4uHGjnkELzO8eGkbuf.png', 'projects/1/featured/gkXYmqf3hReonQOwF9hQSEdFVQs5CYNrx8wpyEDu.png', 'projects/1/homepage/Ki5SpQZ3jvD5XikG4Aw22sQ0ic8bSuuJCQh2HEMD.png', 'We2a0eJz6T8?si=Q-HTCKrekFT7yLHj', 'Video Title', '<p>This is it asd sad sad sad sa dsa dsa dsa d sad sadsad sad sadsad sadasdasdLorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.</p>', 'This project\'s primary feature is that it was created by renowned real estate developer The Etihad Group guarantees that their client will receive exceptional customer care that surpasses their expectations and results in the delivery of their ideal home. Our mission at etihad group, which was founded in 2004, is to lead the land development sector and rank as Pakistan\'s top property developer. Etihad Group Holdings is a well-known builder. Among the well-known buildings this firm worked on were the LUMS Campus, the UCP Lahore Campus, Sukh Chayn Gardens, and the Sheikh Zaid Medical College and Hospital. Etihad Town, though, is the builder\'s debut undertaking.', 'projects/1/developer_logo/IGwurH2JB58WteKMBUncB870LsUGZnhnItFYc85B.png', 'projects/1/pdf/DzemueJQemq2xELaEhOidtHQV0E7605WXziHpX3b.pdf', '<p>Corporate infographic explaining the problem of missed follow-ups in businesses, showing how leads from website inquiries, social media, email, and referrals can be lost without a structured CRM lead management process, branded with the Enterests logo.</p>', 'projects/1/noc/lK5Y4y0PN3uZZRkIfmj8CYBiVZ4IC4wk55KwLoMF.png', 'Future Note', 'Corporate infographic explaining the problem of missed follow-ups in businesses, showing how leads from website inquiries, social media, email, and referrals can be lost without a structured CRM lead management process, branded with the Enterests logo.', 'Xtra Section', '<p>Corporate infographic explaining the problem of missed follow-ups in businesses, showing how leads from website inquiries, social media, email, and referrals can be lost without a structured CRM lead management process, branded with the Enterests logo.</p>', '[{\"title\":\"One\",\"icon\":\"arrow-down\"},{\"title\":\"two\",\"icon\":\"briefcase\"}]', 'Price Plan Of Etihad Town', '[\"1215151212\",\"121212\",\"asdjahdsad isajdsaj\"]', '[{\"question\":\"one\",\"answer\":\"the one\"},{\"question\":\"Two\",\"answer\":\"This is two\"}]', '[{\"title\":\"first plan\",\"image\":\"projects\\/1\\/plans\\/8xPloktKuuRv7PHcni0Ye2Z52TVVZJE2Qb5IH8Y2.png\"},{\"title\":\"second\",\"image\":\"projects\\/1\\/plans\\/KDrUiqFnD3eEC3NPHjQC1eQqfTgtr5kNDSIXEIVZ.png\"}]', '{\"section_title\":\"Is Etihad Town Yield Good Return?\",\"section_description\":\"Definitely yes! Etihad Town Phase 1 has quickly attracted the interest of both domestic and foreign investors thanks to the project\'s governing authorities. Furthermore, the LDA has authorized Etihad Town Phase 1.\",\"items\":[{\"title\":\"Market Trends:\",\"description\":\"There may be ups and downs in the Lahore, Pakistan, real estate market. Keeping an eye on these patterns can reveal information about Etihad Town Phase 1\'s future possibilities.\"},{\"title\":\"Living standard in Etihad Town\",\"description\":\"In addition, the community has a variety of recreational amenities including playgrounds and sports courts, so there\'s always something entertaining to do for people of all ages. Furthermore, you can benefit from the ease of 24-hour security, which guarantees a safe and secure environment for you and your family.\"}]}', '[\"We2a0eJz6T8?si=Q-HTCKrekFT7yLHj\",\"We2a0eJz6T8?si=Q-HTCKrekFT7yLHj\",\"We2a0eJz6T8?si=Q-HTCKrekFT7yLHj\",\"We2a0eJz6T8?si=Q-HTCKrekFT7yLHj\",\"We2a0eJz6T8?si=Q-HTCKrekFT7yLHj\"]', '[{\"path\":\"projects\\/1\\/gallery\\/iLPSLtJYg3EMa1iCjzNtlz9zhOulHvUfwuG7SEmV.png\",\"order\":0},{\"path\":\"projects\\/1\\/gallery\\/apfqwKzy0CttYcRzQ4pIZCk5cyUzzYjLRE4InqKR.png\",\"order\":1}]', NULL, NULL, NULL, NULL, 20, '2026-03-14 06:17:48', '2026-03-17 01:20:41'),
(2, 'Second', 'Second', 'active', 'starting from 3000', 'Nestled in the heart of Lahore, Etihad Town Phase 1 is a hidden gem waiting to be discovered. As you enter this enchanting residential community, you’ll immediately feel a sense of charm and tranquility. The lush green landscapes and meticulously designed streets create a picturesque environment that is simply breathtaking. Take a stroll through the well-maintained parks, or enjoy a leisurely bike ride along the tree-lined avenues. The captivating architecture of the houses adds to the charm, with each home showcasing its own unique style and character.\r\nWhether you’re seeking a peaceful retreat or a vibrant community to call home, Etihad Town\r\nPhase 1 has it all. So come and discover the hidden charm of this remarkable residential\r\ncommunity in Lahore. You won’t be disappointed.', 'Punjab', 'Lahore', 'Raiwind Rd, Lahore', 'Raiwind Rd, Lahore', 'google map', 31.51650000, 74.34990000, NULL, 'projects/1/logo/MUKOSubfupJRBJ65EVCKAa4uHGjnkELzO8eGkbuf.png', 'projects/1/featured/gkXYmqf3hReonQOwF9hQSEdFVQs5CYNrx8wpyEDu.png', 'projects/1/homepage/Ki5SpQZ3jvD5XikG4Aw22sQ0ic8bSuuJCQh2HEMD.png', 'We2a0eJz6T8?si=Q-HTCKrekFT7yLHj', 'Video Title', '<p>This is it</p>', 'This project\'s primary feature is that it was created by renowned real estate developer The Etihad Group guarantees that their client will receive exceptional customer care that surpasses their expectations and results in the delivery of their ideal home. Our mission at etihad group, which was founded in 2004, is to lead the land development sector and rank as Pakistan\'s top property developer. Etihad Group Holdings is a well-known builder. Among the well-known buildings this firm worked on were the LUMS Campus, the UCP Lahore Campus, Sukh Chayn Gardens, and the Sheikh Zaid Medical College and Hospital. Etihad Town, though, is the builder\'s debut undertaking.', NULL, 'projects/1/pdf/DzemueJQemq2xELaEhOidtHQV0E7605WXziHpX3b.pdf', '<p>Corporate infographic explaining the problem of missed follow-ups in businesses, showing how leads from website inquiries, social media, email, and referrals can be lost without a structured CRM lead management process, branded with the Enterests logo.</p>', 'projects/1/noc/lK5Y4y0PN3uZZRkIfmj8CYBiVZ4IC4wk55KwLoMF.png', 'Future Note', 'Corporate infographic explaining the problem of missed follow-ups in businesses, showing how leads from website inquiries, social media, email, and referrals can be lost without a structured CRM lead management process, branded with the Enterests logo.', 'Xtra Section', '<p>Corporate infographic explaining the problem of missed follow-ups in businesses, showing how leads from website inquiries, social media, email, and referrals can be lost without a structured CRM lead management process, branded with the Enterests logo.</p>', '[{\"title\":\"One\",\"icon\":\"marker\"},{\"title\":\"two\",\"icon\":\"three\"}]', 'Price Plan Of Etihad Town', '[\"1215151212\",\"121212\",\"asdjahdsad isajdsaj\"]', '[{\"question\":\"one\",\"answer\":\"the one\"},{\"question\":\"Two\",\"answer\":\"This is two\"}]', '[{\"title\":\"first plan\",\"image\":\"projects\\/1\\/plans\\/8xPloktKuuRv7PHcni0Ye2Z52TVVZJE2Qb5IH8Y2.png\"},{\"title\":\"second\",\"image\":\"projects\\/1\\/plans\\/KDrUiqFnD3eEC3NPHjQC1eQqfTgtr5kNDSIXEIVZ.png\"}]', '{\"section_title\":\"Is Etihad Town Yield Good Return?\",\"section_description\":\"Definitely yes! Etihad Town Phase 1 has quickly attracted the interest of both domestic and foreign investors thanks to the project\'s governing authorities. Furthermore, the LDA has authorized Etihad Town Phase 1.\",\"items\":[{\"title\":\"Market Trends:\",\"description\":\"There may be ups and downs in the Lahore, Pakistan, real estate market. Keeping an eye on these patterns can reveal information about Etihad Town Phase 1\'s future possibilities.\"},{\"title\":\"Living standard in Etihad Town\",\"description\":\"In addition, the community has a variety of recreational amenities including playgrounds and sports courts, so there\'s always something entertaining to do for people of all ages. Furthermore, you can benefit from the ease of 24-hour security, which guarantees a safe and secure environment for you and your family.\"}]}', '[\"da\",\"klla\"]', '[{\"path\":\"projects\\/1\\/gallery\\/iLPSLtJYg3EMa1iCjzNtlz9zhOulHvUfwuG7SEmV.png\",\"order\":0},{\"path\":\"projects\\/1\\/gallery\\/apfqwKzy0CttYcRzQ4pIZCk5cyUzzYjLRE4InqKR.png\",\"order\":1}]', NULL, NULL, NULL, NULL, 1, '2026-03-14 06:43:29', '2026-03-16 10:51:20');

-- --------------------------------------------------------

--
-- Table structure for table `project_project_type`
--

CREATE TABLE `project_project_type` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `project_type_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_project_type`
--

INSERT INTO `project_project_type` (`id`, `project_id`, `project_type_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-03-14 06:17:48', '2026-03-14 06:17:48'),
(2, 2, 1, '2026-03-14 06:43:29', '2026-03-14 06:43:29');

-- --------------------------------------------------------

--
-- Table structure for table `project_types`
--

CREATE TABLE `project_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `show_in_projects` tinyint(1) NOT NULL DEFAULT 1,
  `show_in_properties` tinyint(1) NOT NULL DEFAULT 1,
  `show_in_dealers` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_types`
--

INSERT INTO `project_types` (`id`, `name`, `slug`, `show_in_projects`, `show_in_properties`, `show_in_dealers`, `created_at`, `updated_at`) VALUES
(1, 'Residential', 'Residential', 1, 1, 1, '2026-03-14 05:27:54', '2026-03-14 05:27:54'),
(2, 'Commercial', 'Commercial', 1, 1, 1, '2026-03-14 05:28:08', '2026-03-14 05:28:08');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `dealer_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `purpose` varchar(20) NOT NULL DEFAULT 'sale',
  `description` text DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `short_address` varchar(255) DEFAULT NULL,
  `town` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `google_map` text DEFAULT NULL,
  `price_string` varchar(255) DEFAULT NULL,
  `price_digits` decimal(15,2) DEFAULT NULL,
  `property_type` varchar(255) DEFAULT NULL,
  `bedrooms` tinyint(3) UNSIGNED DEFAULT NULL,
  `bathrooms` tinyint(3) UNSIGNED DEFAULT NULL,
  `garage` tinyint(3) UNSIGNED DEFAULT NULL,
  `kitchen` tinyint(3) UNSIGNED DEFAULT NULL,
  `area_marla` decimal(10,2) DEFAULT NULL,
  `area_kanal` decimal(10,2) DEFAULT NULL,
  `amenities_description` text DEFAULT NULL,
  `videos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`videos`)),
  `gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gallery`)),
  `video_gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`video_gallery`)),
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `location_accessibility` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`location_accessibility`)),
  `nearest_hospitals` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`nearest_hospitals`)),
  `nearest_markets` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`nearest_markets`)),
  `nearest_restaurants` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`nearest_restaurants`)),
  `amenities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`amenities`)),
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `view_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `title`, `slug`, `status`, `dealer_id`, `purpose`, `description`, `featured_image`, `city`, `state`, `address`, `short_address`, `town`, `latitude`, `longitude`, `google_map`, `price_string`, `price_digits`, `property_type`, `bedrooms`, `bathrooms`, `garage`, `kitchen`, `area_marla`, `area_kanal`, `amenities_description`, `videos`, `gallery`, `video_gallery`, `features`, `location_accessibility`, `nearest_hospitals`, `nearest_markets`, `nearest_restaurants`, `amenities`, `meta_title`, `meta_description`, `meta_keywords`, `canonical_url`, `view_count`, `created_at`, `updated_at`) VALUES
(1, 'First Post', 'first-post', 'active', 0, 'sale', 'adsad dasdsa', 'properties/1/rh401gSwrsuw4Y3XdtUERsvZQHlD0LFMLlSassGq.jpg', 'Lahore', 'Punjab', 'Canal Bank Rd', 'Canal Bank Rd', 'Lahore', 31.55800000, 74.35071000, 'google map', 'ajkakjja', 456125.00, 'home', 10, 20, 12, 2, 20.00, 1.00, 'sdjsakd ksaas dsajdkja a dkasjdkasjd', '[\"asda\"]', '[{\"path\":\"properties\\/1\\/gallery\\/OcdlumsmDW4SAXFUcRqtJpPX8k4SZr5fvx3G8Asj.png\",\"order\":0},{\"path\":\"properties\\/1\\/gallery\\/fSItvMA9UyDQl1IGdtCxOLIKXCW83QRH07LEvHEX.png\",\"order\":1},{\"path\":\"properties\\/1\\/gallery\\/uoCG6Gu6TrxCKFMaIfeExtUnPmk7ODIP5XyLv45p.png\",\"order\":2}]', '[\"dasd\",\"215a\"]', '[\"adasd\",\"oana\"]', '[\"jua\"]', '[\"khuags\"]', '[\"jhsaugu\"]', '[\"kaia\",\"sakijad sajd\"]', '[{\"title\":\"hello\",\"icon\":\"location-marker\"},{\"title\":\"World\",\"icon\":\"flag\"}]', NULL, NULL, NULL, NULL, 2, '2026-03-14 10:40:37', '2026-03-16 06:27:52'),
(2, 'Second Post', 'second-post', 'active', 0, 'sale', 'adsad dasdsa', 'properties/2/jrXvFCIixgQHgkV0N9vYUOrqT8VjEP5slZuDPCvB.png', 'Lahore', 'Punjab', 'Canal Bank Rd', 'Canal Bank Rd', 'Lahore', 31.59250000, 74.30950000, 'google map', 'ajkakjja', 334545.00, 'home', 10, 7, 12, 2, 20.00, 1.00, 'sdjsakd ksaas dsajdkja a dkasjdkasjd', '[\"asda\"]', '[]', '[\"dasd\",\"215a\"]', '[\"adasd\",\"oana\"]', '[\"jua\"]', '[\"khuags\"]', '[\"jhsaugu\"]', '[\"kaia\",\"sakijad sajd\"]', '[{\"title\":\"hello\",\"icon\":\"location-marker\"},{\"title\":\"World\",\"icon\":\"flag\"}]', NULL, NULL, NULL, 'https://map.com', 4, '2026-03-14 10:46:41', '2026-03-17 01:46:55'),
(3, 'Third post of dealer', 'third-post', 'active', 1, 'sale', 'This is it', 'properties/3/E7SD0fpaXtrk7tUoGPNLrkYzoqfNs5vj0frgneWC.png', 'Lahore', 'Punjab', 'Canal Bank Rd', 'Canal Bank Rd', 'Lahore', 31.52160000, 74.41110000, 'googl aka map', 'Panch Lakh', 500000.00, 'flat', 10, 1, 15, 12, 10.00, 0.50, 'dsadsad', '[\"We2a0eJz6T8?si=Q-HTCKrekFT7yLHj\"]', '[{\"path\":\"properties\\/3\\/gallery\\/ehlcQhdOMdgMemfJSRRZ1MlhgdySQU7Km53BMjBp.png\",\"order\":0},{\"path\":\"properties\\/3\\/gallery\\/UCnOX27K4V2iXFHpr6q1D6zha6KVi1CJyzUYygRT.jpg\",\"order\":1},{\"path\":\"properties\\/3\\/gallery\\/oUfMNHRkLMm7200Se3oa3IGjcDWrNEGCvchWzmvr.png\",\"order\":2}]', '[\"INCURvH0F7o?si=MUFg0ubRlphfyqco\",\"1BYKnCiUZ98?si=VxRjS84lAmiNXucx\",\"9tZMxzxviFY?si=MTXmeVP982iO2R_9\"]', '[\"adad\"]', '[\"dasd\"]', '[\"asdasd\"]', '[\"551\"]', '[\"320\"]', '[{\"title\":\"hello world\",\"icon\":\"home\"},{\"title\":\"Scholl\",\"icon\":\"office-building\"}]', 'This is is ig', 'asdsad sadsa d', 'asd adsa dasd', 'https://map.com', 20, '2026-03-14 10:51:40', '2026-03-17 01:25:11');

-- --------------------------------------------------------

--
-- Table structure for table `property_project_type`
--

CREATE TABLE `property_project_type` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `property_id` bigint(20) UNSIGNED NOT NULL,
  `project_type_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `property_project_type`
--

INSERT INTO `property_project_type` (`id`, `property_id`, `project_type_id`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '2026-03-15 01:52:17', '2026-03-15 01:52:17'),
(2, 2, 2, '2026-03-15 01:52:17', '2026-03-15 01:52:17'),
(3, 3, 1, '2026-03-15 01:52:17', '2026-03-15 01:52:17');

-- --------------------------------------------------------

--
-- Table structure for table `property_requests`
--

CREATE TABLE `property_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `property_id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `type` varchar(20) NOT NULL DEFAULT 'own',
  `dealer_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `property_requests`
--

INSERT INTO `property_requests` (`id`, `property_id`, `project_id`, `type`, `dealer_id`, `name`, `phone`, `email`, `message`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, 0, 'dealer', 1, 'M. Abu Bakar Khan', '03331022028', 'm.abubakar.khan.6692@gmail.com', 'Hello world', 'new', '2026-03-16 01:41:18', '2026-03-16 01:41:18'),
(2, 0, 1, 'project', 0, 'M. Abu Bakar Khan', '03331022028', 'm.abubakar.khan.6692@gmail.com', 'this is it', 'new', '2026-03-16 10:21:51', '2026-03-16 10:21:51'),
(3, 3, 0, 'dealer', 1, 'M. Abu Bakar Khan', '03331022028', 'm.abubakar.khan.6692@gmail.com', 'dasdas', 'new', '2026-03-16 10:23:21', '2026-03-16 10:23:21'),
(4, 0, 1, 'project', 0, 'M. Abu Bakar Khan', '03331022028', 'm.abubakar.khan.6692@gmail.com', 'this is it', 'seen', '2026-03-16 10:26:49', '2026-03-16 10:47:37'),
(5, 0, 1, 'project', 0, 'M. Abu Bakar Khan', '03331022028', 'm.abubakar.khan.6692@gmail.com', 'ass', 'new', '2026-03-16 10:34:22', '2026-03-16 10:34:22');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('LiUxoKlGG91gGe2nLTFuK7GY6k40jz60Db1wkJI7', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTnJtOEtqZ3pSenowS3JpYWlmbUN4WmVCVVdNdkdBR1FkcTZGckIwTiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly9sb2NhbGhvc3QvZXRpaGFkL3B1YmxpYy9vdXItdGVhbSI7czo1OiJyb3V0ZSI7czo0OiJ0ZWFtIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo4OiJhZG1pbl9pZCI7aToxO30=', 1773744985);

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`id`, `name`, `slug`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Punjab', 'punjab', 1, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(2, 'Sindh', 'sindh', 2, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(3, 'Khyber Pakhtunkhwa', 'khyber-pakhtunkhwa', 3, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(4, 'Balochistan', 'balochistan', 4, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(5, 'Islamabad', 'islamabad', 5, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(6, 'Gilgit-Baltistan', 'gilgit-baltistan', 6, '2026-03-14 05:36:11', '2026-03-14 05:36:11'),
(7, 'Azad Jammu and Kashmir', 'ajk', 7, '2026-03-14 05:36:11', '2026-03-14 05:36:11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `settings`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Admin', 'admin@example.com', '2026-03-14 04:00:28', '$2y$12$uOEnhA/3QSVbdtpG0PO/meNAdhEKgvxn63Jg56CaJAcBV6d1TwVf2', NULL, '{\"timezone\":null,\"language\":\"en\",\"dark_mode\":true,\"email_notifications\":true}', '2026-03-14 04:00:28', '2026-03-14 04:26:25'),
(2, 'nadeem', 'Nadeem', 'nadeem@domain.com', NULL, '$2y$12$D0/Qef/ag1R4zyZ0KEog9uWS/xwKfQdav24WhbWiUG3IauFhO8ktK', NULL, NULL, '2026-03-14 04:22:21', '2026-03-14 04:27:08');

-- --------------------------------------------------------

--
-- Table structure for table `visitor_daily_counts`
--

CREATE TABLE `visitor_daily_counts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `count_own_listing` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `count_dealer_listing` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `count_projects` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `visitor_daily_counts`
--

INSERT INTO `visitor_daily_counts` (`id`, `date`, `count`, `count_own_listing`, `count_dealer_listing`, `count_projects`, `created_at`, `updated_at`) VALUES
(1, '2026-03-09', 12, 5, 4, 5, '2026-03-15 02:36:32', '2026-03-15 02:36:32'),
(2, '2026-03-10', 18, 7, 5, 7, '2026-03-15 02:36:32', '2026-03-15 02:36:32'),
(3, '2026-03-11', 9, 4, 3, 4, '2026-03-15 02:36:32', '2026-03-15 02:36:32'),
(4, '2026-03-12', 22, 9, 6, 8, '2026-03-15 02:36:32', '2026-03-15 02:36:32'),
(5, '2026-03-13', 15, 6, 4, 6, '2026-03-15 02:36:32', '2026-03-15 02:36:32'),
(6, '2026-03-14', 11, 4, 3, 5, '2026-03-15 02:36:32', '2026-03-15 02:36:32'),
(7, '2026-03-15', 19, 8, 5, 7, '2026-03-15 02:36:32', '2026-03-15 02:36:32'),
(8, '2026-03-16', 34, 2, 14, 18, '2026-03-16 06:17:34', '2026-03-16 10:51:20'),
(9, '2026-03-17', 13, 4, 6, 3, '2026-03-16 23:45:35', '2026-03-17 01:46:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `careers`
--
ALTER TABLE `careers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `careers_slug_unique` (`slug`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cities_state_id_foreign` (`state_id`);

--
-- Indexes for table `cms_pages`
--
ALTER TABLE `cms_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cms_pages_slug_unique` (`slug`);

--
-- Indexes for table `contact_settings`
--
ALTER TABLE `contact_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dealers`
--
ALTER TABLE `dealers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dealers_slug_unique` (`slug`),
  ADD KEY `dealers_status_index` (`status`);

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
  ADD KEY `jobs_queue_reserved_at_available_at_index` (`queue`,`reserved_at`,`available_at`);

--
-- Indexes for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_applications_career_id_foreign` (`career_id`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `projects_slug_unique` (`slug`),
  ADD KEY `projects_status_index` (`status`);

--
-- Indexes for table `project_project_type`
--
ALTER TABLE `project_project_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_project_type_project_id_project_type_id_unique` (`project_id`,`project_type_id`),
  ADD KEY `project_project_type_project_type_id_foreign` (`project_type_id`);

--
-- Indexes for table `project_types`
--
ALTER TABLE `project_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_types_slug_unique` (`slug`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `properties_slug_unique` (`slug`),
  ADD KEY `properties_dealer_id_index` (`dealer_id`),
  ADD KEY `properties_status_index` (`status`);

--
-- Indexes for table `property_project_type`
--
ALTER TABLE `property_project_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `property_project_type_property_id_project_type_id_unique` (`property_id`,`project_type_id`),
  ADD KEY `property_project_type_project_type_id_foreign` (`project_type_id`);

--
-- Indexes for table `property_requests`
--
ALTER TABLE `property_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_requests_property_id_foreign` (`property_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `states_slug_unique` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- Indexes for table `visitor_daily_counts`
--
ALTER TABLE `visitor_daily_counts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `visitor_daily_counts_date_unique` (`date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `careers`
--
ALTER TABLE `careers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- AUTO_INCREMENT for table `cms_pages`
--
ALTER TABLE `cms_pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `contact_settings`
--
ALTER TABLE `contact_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dealers`
--
ALTER TABLE `dealers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- AUTO_INCREMENT for table `job_applications`
--
ALTER TABLE `job_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `project_project_type`
--
ALTER TABLE `project_project_type`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `project_types`
--
ALTER TABLE `project_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `property_project_type`
--
ALTER TABLE `property_project_type`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `property_requests`
--
ALTER TABLE `property_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `visitor_daily_counts`
--
ALTER TABLE `visitor_daily_counts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD CONSTRAINT `job_applications_career_id_foreign` FOREIGN KEY (`career_id`) REFERENCES `careers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_project_type`
--
ALTER TABLE `project_project_type`
  ADD CONSTRAINT `project_project_type_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_project_type_project_type_id_foreign` FOREIGN KEY (`project_type_id`) REFERENCES `project_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `property_project_type`
--
ALTER TABLE `property_project_type`
  ADD CONSTRAINT `property_project_type_project_type_id_foreign` FOREIGN KEY (`project_type_id`) REFERENCES `project_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `property_project_type_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
