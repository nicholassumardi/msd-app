-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 22, 2024 at 04:41 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `msd`
--

-- --------------------------------------------------------

--
-- Table structure for table `age_classifications`
--

CREATE TABLE `age_classifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rule` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `age_classifications`
--

INSERT INTO `age_classifications` (`id`, `rule`, `label`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '17', '17-20 Tahun', NULL, NULL, NULL),
(2, '21', '21-30 Tahun', NULL, NULL, NULL),
(3, '31', '31-40 Tahun', NULL, NULL, NULL),
(4, '41', '41-50 Tahun', NULL, NULL, NULL),
(5, '51', '51-60 Tahun', NULL, NULL, NULL),
(6, '61', '61-70 Tahun', NULL, NULL, NULL),
(7, '71', '71-80 Tahun', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Sertifikasi Internal Auditor', NULL, NULL, NULL),
(2, 'Sertifikasi Umum', NULL, NULL, NULL),
(3, 'Sertifikasi Kendaraan & Civil', NULL, NULL, NULL),
(4, 'Sertifikasi Tanggap Darurat', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `code`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Karunia Alam Segar', 'KAS', NULL, NULL, NULL),
(2, 'Karya Indah Alam Sejahtera', 'KIAS', NULL, NULL, NULL),
(3, 'Harum Alam Segar', 'HAS', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `company_id`, `parent_id`, `name`, `code`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 0, 'Quality Control', 'QC', NULL, NULL, NULL),
(2, 1, 0, 'REF', 'REF', NULL, NULL, NULL),
(3, 2, 0, 'TEK', 'TEK', NULL, NULL, NULL),
(4, 3, 1, 'AUDIT', 'AUDITTEST', '2024-11-20 20:02:29', '2024-11-20 20:02:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employment_duration_classifications`
--

CREATE TABLE `employment_duration_classifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rule` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employment_duration_classifications`
--

INSERT INTO `employment_duration_classifications` (`id`, `rule`, `label`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '0', 'G (0-1 Tahun)', NULL, NULL, NULL),
(2, '1', 'F (1-5 Tahun)', NULL, NULL, NULL),
(3, '5', 'E (5-10 Tahun)', NULL, NULL, NULL),
(4, '10', 'D (10-15 Tahun)', NULL, NULL, NULL),
(5, '15', 'C (15-20 Tahun)', NULL, NULL, NULL),
(6, '20', 'B (20-25 Tahun)', NULL, NULL, NULL),
(7, '25', 'A (25 Tahun ke atas)', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `general_classifications`
--

CREATE TABLE `general_classifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rule` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `general_classifications`
--

INSERT INTO `general_classifications` (`id`, `rule`, `label`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '1946', 'Baby Boomers', NULL, NULL, NULL),
(2, '1965', 'Gen X', NULL, NULL, NULL),
(3, '1977', 'Gen Y', NULL, NULL, NULL),
(4, '1995', 'Gen Z', NULL, NULL, NULL),
(5, '2012', 'Gen Alpha', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ikws`
--

CREATE TABLE `ikws` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_task_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_codes`
--

CREATE TABLE `job_codes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'KODE PEH (NS || ST || SE || SE || SD || DV)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_codes`
--

INSERT INTO `job_codes` (`id`, `department_id`, `code`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'ST125', '2024-11-20 13:11:52', '2024-11-20 20:32:54', NULL),
(2, 4, 'NS325', '2024-11-21 08:23:07', '2024-11-21 08:23:07', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `job_descriptions`
--

CREATE TABLE `job_descriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_code_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_tasks`
--

CREATE TABLE `job_tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_code_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2013_10_17_075722_create_companies_table', 1),
(2, '2013_10_17_075752_create_departments_table', 1),
(3, '2014_10_12_000000_create_users_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2023_10_17_094044_create_certificates_table', 1),
(6, '2024_10_16_063252_create_general_classifications_table', 1),
(7, '2024_10_16_063327_create_age_classifications_table', 1),
(8, '2024_10_16_063340_create_employment_duration_classifications_table', 1),
(9, '2024_10_16_075833_create_user_service_years_table', 1),
(10, '2024_10_16_082828_create_user_certifications_table', 1),
(11, '2024_10_16_094536_create_user_employee_numbers_table', 1),
(12, '2024_10_31_085326_create_job_codes_table', 1),
(13, '2024_10_31_085436_create_job_tasks_table', 1),
(14, '2024_10_31_095506_create_job_descriptions_table', 1),
(15, '2024_11_03_134708_create_ikws_table', 1),
(16, '2024_11_04_014421_create_user_job_code_table', 1),
(17, '2024_11_04_020651_create_user_certificate_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `identity_card` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('male','female') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `religion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `education` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marital_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL COMMENT '1 : AKTIF || 2: NON AKTIF',
  `employee_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Klasifikasi : Staff || Outsourcing || Karyawan || PHL || Kantin',
  `section` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'QC || PRD || B-PO2C || FRAK 2 || TEK M',
  `position_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SPV || KRB || KARU 1 || MTC || BONGKAR 1 || ADM',
  `status_twiji` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Guteji || Intiji',
  `schedule_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Shift || Non Shift',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_account` tinyint(4) DEFAULT NULL COMMENT '1: AKTIF || 2: NON AKTIF',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `uuid`, `name`, `company_id`, `department_id`, `date_of_birth`, `identity_card`, `gender`, `religion`, `email`, `photo`, `education`, `marital_status`, `address`, `phone`, `status`, `employee_type`, `section`, `position_code`, `status_twiji`, `schedule_type`, `password`, `status_account`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'b9110bdc-6485-4af8-9bf9-e910b9792eb7', 'Abigail Soleman', 1, 1, '1993-08-27', '3213032708930010', 'female', 'Katolik', 'wings@wings.com', NULL, 'S1', 'Belum Menikah', 'BTN PUSKOPAD SUKAJAYA, RT/RW 59/17, CIGADUNG, SUBANG', '08993663995', 1, 'Staff', 'PRD', 'KRB', 'INTIJI', 'Shift', '$2y$10$XoiC3DwoAw8HQMamE9TrduP00Jt8mxGV/Y4HoMYN7AwXX7m4EM59.', 1, NULL, NULL, NULL),
(2, 'f720774b-d046-477b-8cd0-154611dc1d61', 'ROBBY DWI YANTO', 2, 3, '1963-12-12', '3506021212690005', 'male', 'Islam', 'wings1@wings.com', NULL, 'SMA', 'Menikah', 'KLASMAN RT 001 RW 005 DS. REJOTENGAH KEC. DEKET KAB. GRESIK', '081236914562', 1, 'Staff', 'QC', 'SPV', 'GUTEJI', 'Shift', '$2y$10$q6qZwYurSmN3oUvjeJtAeu4XDl66UgPXr.gEuBoP684axJ7fGn4vy', 1, NULL, NULL, NULL),
(3, '66c9f90c-acaf-4fed-bcbb-edaeb9c5b636', 'RUSMAN', 1, 1, '2015-11-13', '1222222234444444', 'male', 'Katolik', NULL, '', 'SMA', 'Belum Menikah', 'JALAN MARGO CITY NO 32 JAKARTA', '+62 (888) 866-67-77', 1, 'OS', 'QC', 'SPV', 'TWIJI', 'Shift', '$2y$10$r4lnV.n0V070ExdPdjcTeudOs19jQyQ0IY55K8JY2rjB9SJHgoMc.', NULL, '2024-11-20 11:14:34', '2024-11-20 11:14:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_certificate`
--

CREATE TABLE `user_certificate` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `certificate_id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_certificate`
--

INSERT INTO `user_certificate` (`id`, `user_id`, `certificate_id`, `description`, `expiration_date`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'Pemadam kebakaran kelas A dan C', '2026-10-13', NULL, NULL, NULL),
(2, 1, 2, 'Penyelia Halal', '2027-12-13', NULL, NULL, NULL),
(3, 2, 3, 'SIO FORKLIFT', '2025-11-13', NULL, NULL, NULL),
(4, 3, 1, 'TEST1', '2024-11-21', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_certifications`
--

CREATE TABLE `user_certifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `certificate_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_employee_numbers`
--

CREATE TABLE `user_employee_numbers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `employee_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registry_date` date DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_employee_numbers`
--

INSERT INTO `user_employee_numbers` (`id`, `user_id`, `employee_number`, `registry_date`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, '55555555', '2024-11-20', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_job_code`
--

CREATE TABLE `user_job_code` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `job_code_id` bigint(20) UNSIGNED DEFAULT NULL,
  `group` char(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'GROUP A || B || C || D',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_job_code`
--

INSERT INTO `user_job_code` (`id`, `user_id`, `job_code_id`, `group`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 2, 'A', 'TEST KODE JABATANs', 1, NULL, '2024-11-21 10:25:25', NULL),
(2, 2, 2, 'C', 'TEST ABCD', 1, NULL, '2024-11-21 10:36:20', '2024-11-21 10:36:20');

-- --------------------------------------------------------

--
-- Table structure for table `user_service_years`
--

CREATE TABLE `user_service_years` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `leave_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_service_years`
--

INSERT INTO `user_service_years` (`id`, `user_id`, `join_date`, `leave_date`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, '2022-10-12', NULL, NULL, NULL, NULL),
(2, 2, '2019-05-02', NULL, NULL, NULL, NULL),
(3, 3, '2024-11-12', '1970-01-01', '2024-11-20 11:14:34', '2024-11-20 11:14:34', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `age_classifications`
--
ALTER TABLE `age_classifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `departments_company_id_foreign` (`company_id`);

--
-- Indexes for table `employment_duration_classifications`
--
ALTER TABLE `employment_duration_classifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `general_classifications`
--
ALTER TABLE `general_classifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ikws`
--
ALTER TABLE `ikws`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ikws_job_task_id_foreign` (`job_task_id`);

--
-- Indexes for table `job_codes`
--
ALTER TABLE `job_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_codes_department_id_foreign` (`department_id`);

--
-- Indexes for table `job_descriptions`
--
ALTER TABLE `job_descriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_descriptions_job_code_id_foreign` (`job_code_id`);

--
-- Indexes for table `job_tasks`
--
ALTER TABLE `job_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_tasks_job_code_id_foreign` (`job_code_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `users_identity_card_unique` (`identity_card`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_company_id_foreign` (`company_id`),
  ADD KEY `users_department_id_foreign` (`department_id`);

--
-- Indexes for table `user_certificate`
--
ALTER TABLE `user_certificate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_certificate_user_id_foreign` (`user_id`),
  ADD KEY `user_certificate_certificate_id_foreign` (`certificate_id`);

--
-- Indexes for table `user_certifications`
--
ALTER TABLE `user_certifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_certifications_user_id_foreign` (`user_id`),
  ADD KEY `user_certifications_certificate_id_foreign` (`certificate_id`);

--
-- Indexes for table `user_employee_numbers`
--
ALTER TABLE `user_employee_numbers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_employee_numbers_employee_number_unique` (`employee_number`),
  ADD KEY `user_employee_numbers_user_id_foreign` (`user_id`);

--
-- Indexes for table `user_job_code`
--
ALTER TABLE `user_job_code`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_job_code_user_id_foreign` (`user_id`),
  ADD KEY `user_job_code_job_code_id_foreign` (`job_code_id`);

--
-- Indexes for table `user_service_years`
--
ALTER TABLE `user_service_years`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_service_years_user_id_foreign` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `age_classifications`
--
ALTER TABLE `age_classifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `employment_duration_classifications`
--
ALTER TABLE `employment_duration_classifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `general_classifications`
--
ALTER TABLE `general_classifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ikws`
--
ALTER TABLE `ikws`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_codes`
--
ALTER TABLE `job_codes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `job_descriptions`
--
ALTER TABLE `job_descriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_tasks`
--
ALTER TABLE `job_tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_certificate`
--
ALTER TABLE `user_certificate`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_certifications`
--
ALTER TABLE `user_certifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_employee_numbers`
--
ALTER TABLE `user_employee_numbers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_job_code`
--
ALTER TABLE `user_job_code`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_service_years`
--
ALTER TABLE `user_service_years`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ikws`
--
ALTER TABLE `ikws`
  ADD CONSTRAINT `ikws_job_task_id_foreign` FOREIGN KEY (`job_task_id`) REFERENCES `job_tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_codes`
--
ALTER TABLE `job_codes`
  ADD CONSTRAINT `job_codes_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_descriptions`
--
ALTER TABLE `job_descriptions`
  ADD CONSTRAINT `job_descriptions_job_code_id_foreign` FOREIGN KEY (`job_code_id`) REFERENCES `job_codes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_tasks`
--
ALTER TABLE `job_tasks`
  ADD CONSTRAINT `job_tasks_job_code_id_foreign` FOREIGN KEY (`job_code_id`) REFERENCES `job_codes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_certificate`
--
ALTER TABLE `user_certificate`
  ADD CONSTRAINT `user_certificate_certificate_id_foreign` FOREIGN KEY (`certificate_id`) REFERENCES `certificates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_certificate_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_certifications`
--
ALTER TABLE `user_certifications`
  ADD CONSTRAINT `user_certifications_certificate_id_foreign` FOREIGN KEY (`certificate_id`) REFERENCES `certificates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_certifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_employee_numbers`
--
ALTER TABLE `user_employee_numbers`
  ADD CONSTRAINT `user_employee_numbers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_job_code`
--
ALTER TABLE `user_job_code`
  ADD CONSTRAINT `user_job_code_job_code_id_foreign` FOREIGN KEY (`job_code_id`) REFERENCES `job_codes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_job_code_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_service_years`
--
ALTER TABLE `user_service_years`
  ADD CONSTRAINT `user_service_years_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
