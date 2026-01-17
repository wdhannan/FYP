-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 17, 2026 at 04:57 PM
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
-- Database: `fyp`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `AppointmentID` varchar(50) NOT NULL,
  `ChildID` varchar(255) NOT NULL,
  `DoctorID` varchar(255) NOT NULL,
  `NurseID` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `birthrecord`
--

CREATE TABLE `birthrecord` (
  `BirthID` varchar(50) NOT NULL,
  `ChildID` varchar(50) DEFAULT NULL,
  `TimeOfBirth` time DEFAULT NULL,
  `GestationalAgeWeeks` int(11) DEFAULT NULL,
  `BirthPlace` varchar(50) DEFAULT NULL,
  `BirthType` varchar(10) DEFAULT NULL,
  `Complications` text DEFAULT NULL,
  `BabyCount` int(11) DEFAULT NULL,
  `BirthWeight` decimal(5,2) DEFAULT NULL,
  `BirthLength` decimal(5,2) DEFAULT NULL,
  `BirthCircumference` decimal(5,2) DEFAULT NULL,
  `VitaminKGiven` varchar(10) DEFAULT NULL,
  `ApgarScore` varchar(10) DEFAULT NULL,
  `BloodGroup` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `birthrecord`
--

INSERT INTO `birthrecord` (`BirthID`, `ChildID`, `TimeOfBirth`, `GestationalAgeWeeks`, `BirthPlace`, `BirthType`, `Complications`, `BabyCount`, `BirthWeight`, `BirthLength`, `BirthCircumference`, `VitaminKGiven`, `ApgarScore`, `BloodGroup`, `created_at`, `updated_at`) VALUES
('BIR000001', 'C001', '12:15:00', 40, 'Hospital', 'Cesarean', 'diabetes during pregnancy', 2, 2.30, 42.00, 36.00, '1', '8', 'A+', '2026-01-07 23:45:40', '2026-01-07 23:45:40');

-- --------------------------------------------------------

--
-- Table structure for table `child`
--

CREATE TABLE `child` (
  `ChildID` varchar(50) NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `DateOfBirth` date DEFAULT NULL,
  `Gender` varchar(10) DEFAULT NULL,
  `MyKidNumber` varchar(20) DEFAULT NULL,
  `ParentID` varchar(50) DEFAULT NULL,
  `Ethnic` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `child`
--

INSERT INTO `child` (`ChildID`, `FullName`, `DateOfBirth`, `Gender`, `MyKidNumber`, `ParentID`, `Ethnic`, `created_at`, `updated_at`) VALUES
('C001', 'NUR AYRA MALEEQA BINTI ZAKWAN', '2026-01-01', 'Female', '260101-14-0856', 'P006', 'Malay', '2026-01-07 14:35:23', '2026-01-07 14:35:23'),
('C002', 'AYDEN RAYYAN BIN ZAKWAN', '2026-01-01', 'Male', '260101-14-0997', 'P006', 'Malay', '2026-01-07 14:35:46', '2026-01-07 14:35:46'),
('C003', 'ALPHA NG CHOO LEE', '2027-06-12', 'Male', '251230-14-0943', 'P007', 'Chinese', '2026-01-07 14:35:46', '2026-01-07 14:35:46'),
('C004', 'DHARVHIN A/L KAMURILAN', '2027-04-12', 'Male', '251228-14-9887', 'P008', 'Indian', '2026-01-07 14:35:48', '2026-01-07 14:35:48'),
('C005', 'AZRA MEDINA BINTI AZMIL', '2026-04-01', 'Female', '260104-14-2478', 'P009', 'Malay', '2026-01-07 14:35:49', '2026-01-07 14:35:49');

-- --------------------------------------------------------

--
-- Table structure for table `developmentmilestone`
--

CREATE TABLE `developmentmilestone` (
  `MilestoneID` varchar(50) NOT NULL,
  `ChildID` varchar(50) DEFAULT NULL,
  `MilestoneType` varchar(50) DEFAULT NULL,
  `Notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `developmentmilestone`
--

INSERT INTO `developmentmilestone` (`MilestoneID`, `ChildID`, `MilestoneType`, `Notes`, `created_at`, `updated_at`) VALUES
('MIL000001', 'C001', 'Sitting Up', 'good', '2026-01-07 23:51:49', '2026-01-07 23:51:49'),
('MIL000002', 'C002', 'Laughing', 'good', '2026-01-09 06:57:52', '2026-01-09 06:57:52');

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

CREATE TABLE `doctor` (
  `DoctorID` varchar(255) NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`DoctorID`, `FullName`, `Email`, `created_at`, `updated_at`) VALUES
('D100', 'Dr Nafis Bin Affendey', 'mohdhilman05@gmail.com', '2026-01-17 04:14:19', '2026-01-17 04:14:19'),
('D108', 'Dr Dayangku Intan Binti Awang', 'dayang.intan@gmail.com', '2026-01-17 04:14:30', '2026-01-17 04:14:30'),
('D109', 'Dr Farhan Bin Mustafa', 'farhan.mustafa@gmail.com', '2026-01-17 04:14:24', '2026-01-17 04:14:24'),
('D123', 'Dr Insyirah Binti Mohd Ismail', 'insyirahismail26@gmail.com', '2026-01-17 04:13:57', '2026-01-17 04:13:57'),
('D133', 'Dr Nor Liyana Binti Ghazali', 'norliyana.g@gmail.com', '2026-01-17 04:14:36', '2026-01-17 04:14:36'),
('D201', 'Dr Khairul Azmi Bin Rosli', 'khairulazmi88@gmail.com', '2026-01-17 04:14:21', '2026-01-17 04:14:21'),
('D217', 'Dr Taufiq Bin Hidayat', 'taufiq.hidayat@gmail.com', '2026-01-17 04:14:35', '2026-01-17 04:14:35'),
('D247', 'Dr Siti Aishah Binti Rahman', 'sitiaishah_r@gmail.com', '2026-01-17 04:14:25', '2026-01-17 04:14:25'),
('D274', 'Dr Haris Bin Zainal', 'hariszainal77@gmail.com', '2026-01-17 04:14:32', '2026-01-17 04:14:32'),
('D277', 'Dr Farah Diana Binti Mazlan', 'farahdiana.m@gmail.com', '2026-01-17 04:14:33', '2026-01-17 04:14:33'),
('D278', 'Dr Syamsul Bahari Bin Khalid', 'syamsul.bahari@gmail.com', '2026-01-17 04:14:28', '2026-01-17 04:14:28'),
('D312', 'Dr Fatin Nabila Binti Yusuf', 'fatin.nabila95@gmail.com', '2026-01-17 04:14:27', '2026-01-17 04:14:27'),
('D411', 'Dr Puteri Sarah Binti Megat', 'puterisarah.m@gmail.com', '2026-01-17 04:14:22', '2026-01-17 04:14:22'),
('D456', 'Dr Elyna Binti Rajab', 'syaz.elyna@gmail.com', '2026-01-17 04:14:18', '2026-01-17 04:14:18');

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
-- Table structure for table `feedingrecord`
--

CREATE TABLE `feedingrecord` (
  `FeedingID` varchar(50) NOT NULL,
  `ChildID` varchar(50) DEFAULT NULL,
  `FeedingType` varchar(50) DEFAULT NULL,
  `FrequencyPerDay` int(11) DEFAULT NULL,
  `DateLogged` date DEFAULT NULL,
  `Remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feedingrecord`
--

INSERT INTO `feedingrecord` (`FeedingID`, `ChildID`, `FeedingType`, `FrequencyPerDay`, `DateLogged`, `Remarks`, `created_at`, `updated_at`) VALUES
('FED000001', 'C001', 'Solid Foods (e.g., rice porridge)', 3, '2026-01-14', 'good', '2026-01-07 23:52:35', '2026-01-07 23:52:35'),
('FED000002', 'C002', 'Finger Foods', 2, '2026-01-10', 'good', '2026-01-09 06:58:52', '2026-01-09 06:58:52');

-- --------------------------------------------------------

--
-- Table structure for table `growthchart`
--

CREATE TABLE `growthchart` (
  `GrowthID` varchar(50) NOT NULL,
  `ChildID` varchar(50) DEFAULT NULL,
  `DateMeasured` date DEFAULT NULL,
  `Age` int(11) DEFAULT NULL,
  `Weight` double(8,2) DEFAULT NULL,
  `Height` double(8,2) DEFAULT NULL,
  `HeadCircumference` double(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `growthchart`
--

INSERT INTO `growthchart` (`GrowthID`, `ChildID`, `DateMeasured`, `Age`, `Weight`, `Height`, `HeadCircumference`, `created_at`, `updated_at`) VALUES
('GROW000001', 'C001', '2026-01-14', 24, 15.00, 60.00, 55.00, '2026-01-08 00:14:30', '2026-01-08 00:14:30');

-- --------------------------------------------------------

--
-- Table structure for table `immunization`
--

CREATE TABLE `immunization` (
  `ImmunizationID` varchar(50) NOT NULL,
  `ChildID` varchar(50) DEFAULT NULL,
  `Age` int(11) DEFAULT NULL,
  `VaccineName` varchar(50) DEFAULT NULL,
  `Date` date DEFAULT NULL,
  `DoseNumber` varchar(10) DEFAULT NULL,
  `GivenBy` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `immunization`
--

INSERT INTO `immunization` (`ImmunizationID`, `ChildID`, `Age`, `VaccineName`, `Date`, `DoseNumber`, `GivenBy`, `created_at`, `updated_at`) VALUES
('IMM000001', 'C001', 24, 'Influenza (Flu)', '2026-01-14', '1st', 'Nurse Hannan', '2026-01-07 23:50:50', '2026-01-07 23:50:50'),
('IMM000002', 'C001', 16, 'Varicella (Chickenpox)', '2026-01-13', '1st', 'Nurse Hannan', '2026-01-09 06:57:22', '2026-01-09 06:57:22');

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
(1, '2014_10_12_000000_create_user_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_11_10_051000_create_parent_table', 2),
(6, '2025_11_10_051509_create_doctor_table', 2),
(7, '2025_11_10_051528_create_nurse_table', 2),
(8, '2025_11_10_051939_create_child_table', 2),
(9, '2025_11_10_052009_create_growthchart_table', 2),
(10, '2025_11_10_052010_create_birthrecord_table', 2),
(11, '2025_11_10_052028_create_schedule_table', 2),
(12, '2025_11_10_052035_create_report_table', 2),
(13, '2025_11_10_052055_create_screeningresult_table', 2),
(14, '2025_11_10_052117_create_immunization_table', 2),
(15, '2025_11_10_052200_create_appointment_table', 3),
(16, '2025_12_02_185128_create_developmentmilestone_table', 4),
(17, '2025_12_02_190705_create_feedingrecord_table', 4),
(18, '2026_01_05_125214_add_must_change_password_to_user_table', 5),
(19, '2026_01_05_155135_add_user_id_to_doctor_table', 6),
(20, '2026_01_05_162728_remove_user_id_from_doctor_table', 7),
(21, '2026_01_05_164538_change_user_id_to_string_in_user_table', 8),
(22, '2026_01_06_203739_add_remarks_to_feedingrecord_table', 9),
(23, '2026_01_06_210107_add_age_to_growthchart_table', 10),
(24, '2026_01_10_062530_increase_filename_size_in_schedule_table', 11);

-- --------------------------------------------------------

--
-- Table structure for table `nurse`
--

CREATE TABLE `nurse` (
  `NurseID` varchar(255) NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nurse`
--

INSERT INTO `nurse` (`NurseID`, `FullName`, `Email`, `created_at`, `updated_at`) VALUES
('N123', 'Muhammad Hafiz Bin Ismail', 'hafiz.ismail01@gmail.com', '2026-01-17 04:15:56', '2026-01-17 04:15:56'),
('N144', 'Prema a/p Subramaniam', 'prema.subra@gmail.com', '2026-01-17 04:15:58', '2026-01-17 04:15:58'),
('N156', 'Siti Sarah Binti Jamal', 'sitisarah.j@gmail.com', '2026-01-17 04:15:59', '2026-01-17 04:15:59'),
('N211', 'Ravindran a/l Kumar', 'ravindran.k@gmail.com', '2026-01-17 04:15:50', '2026-01-17 04:15:50'),
('N213', 'Nurul Ain Binti Razak', 'nurulain.razak@gmail.com', '2026-01-17 04:15:52', '2026-01-17 04:15:52'),
('N218', 'Lim Kok Leong', 'limkokleong77@gmail.com', '2026-01-17 04:16:00', '2026-01-17 04:16:00'),
('N219', 'Dayang Nurfarahin Binti Awang', 'dayang.farahin@gmail.com', '2026-01-17 04:16:03', '2026-01-17 04:16:03'),
('N222', 'Ganesan a/l Maniam', 'ganesan.maniam@gmail.com', '2026-01-17 04:16:02', '2026-01-17 04:16:02'),
('N224', 'Lee Wei Hong', 'leeweihong90@gmail.com', '2026-01-17 04:15:53', '2026-01-17 04:15:53'),
('N234', 'Wardatul Hannan Binti Wanri', 'hannan.wna@gmail.com', '2026-01-17 04:15:44', '2026-01-17 04:15:44'),
('N245', 'Ahmad Zafri Bin Roslan', 'ahmad.zafri88@gmail.com', '2026-01-17 04:15:49', '2026-01-17 04:15:49'),
('N321', 'Thiga a/p Morgan', 'thiga.morgan@gmail.com', '2026-01-17 04:15:55', '2026-01-17 04:15:55'),
('N678', 'Elyn Jayjay Binti Kamal', 'elynnjayjay@gmail.com', '2026-01-17 04:15:47', '2026-01-17 04:15:47');

-- --------------------------------------------------------

--
-- Table structure for table `parent`
--

CREATE TABLE `parent` (
  `ParentID` varchar(50) NOT NULL,
  `MotherName` varchar(255) NOT NULL,
  `MphoneNumber` varchar(20) DEFAULT NULL,
  `MEmail` varchar(50) DEFAULT NULL,
  `MIdentificationNumber` varchar(50) DEFAULT NULL,
  `FatherName` varchar(255) DEFAULT NULL,
  `FPhoneNumber` varchar(20) DEFAULT NULL,
  `FEmail` varchar(50) DEFAULT NULL,
  `FIdentificationNumber` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `parent`
--

INSERT INTO `parent` (`ParentID`, `MotherName`, `MphoneNumber`, `MEmail`, `MIdentificationNumber`, `FatherName`, `FPhoneNumber`, `FEmail`, `FIdentificationNumber`) VALUES
('P006', 'SUHAILA BINTI MADNOOR', '012-9691216', 'hannanwardatul@gmail.com', '980303-14-0986', 'ZAKWAN BIN ALI', '012-0978678', 'zack@gmail.com', '980706-14-0557'),
('P007', 'MARY NG LIEW', '012-0987897', 'mary@gmail.com', '901212-14-7664', 'JOHN NG SIANG', '012-8769465', 'john@gmail.com', '900505-14-5609'),
('P008', 'DHARSHINI A/P KUMULAN', '013-8972345', 'dharshini@gmail.com', '930415-14-3224', 'KAMURILAN A/L VHESSHAL', '019-8794367', 'kamurilan@gmail.com', '920617-14-8723'),
('P009', 'SYAZLIN BINTI SYUKUR', '016-8965723', 'syaz.elynnjj@gmail.com', '951018-14-9822', 'AZMIL BIN ZAMAN', '011-28882681', 'azz@gmail.com', '950326-14-7865');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `ReportID` varchar(50) NOT NULL,
  `ChildID` varchar(50) DEFAULT NULL,
  `DoctorID` varchar(50) DEFAULT NULL,
  `ReportDate` date DEFAULT NULL,
  `Diagnosis` text DEFAULT NULL,
  `Symptoms` text DEFAULT NULL,
  `Findings` text DEFAULT NULL,
  `FollowUpAdvices` text DEFAULT NULL,
  `Notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `ScheduleID` varchar(50) NOT NULL,
  `DoctorID` varchar(50) DEFAULT NULL,
  `UploadDate` date DEFAULT NULL,
  `FileName` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `screeningresult`
--

CREATE TABLE `screeningresult` (
  `ScreeningID` varchar(50) NOT NULL,
  `ChildID` varchar(50) DEFAULT NULL,
  `ScreeningType` varchar(50) DEFAULT NULL,
  `Result` varchar(50) DEFAULT NULL,
  `DateScreened` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `screeningresult`
--

INSERT INTO `screeningresult` (`ScreeningID`, `ChildID`, `ScreeningType`, `Result`, `DateScreened`, `created_at`, `updated_at`) VALUES
('SCR000001', 'C001', 'Vision Test', 'clear', '2026-01-14', '2026-01-07 23:52:08', '2026-01-07 23:52:08'),
('SCR000002', 'C002', 'Hearing Test', 'clear', '2026-01-10', '2026-01-09 06:58:32', '2026-01-09 06:58:32');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` varchar(50) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `PasswordHash`, `role`, `must_change_password`, `created_at`, `updated_at`) VALUES
('1', 'admin123', 'admin', 0, '2025-12-17 08:53:18', '2025-12-17 08:53:18'),
('10', '9PJN8vDF', 'doctor', 1, '2026-01-05 05:23:07', '2026-01-05 05:23:07'),
('11', 'kYNNF97m', 'doctor', 1, '2026-01-05 05:26:03', '2026-01-05 05:26:03'),
('12', 'doctor123', 'doctor', 0, '2026-01-05 05:26:07', '2026-01-05 05:59:41'),
('13', 'xYbAWyRv', 'doctor', 1, '2026-01-05 05:26:08', '2026-01-05 05:26:08'),
('14', 'CbecGyef', 'nurse', 1, '2026-01-05 05:28:24', '2026-01-05 05:28:24'),
('15', 'h8cJMH59', 'nurse', 1, '2026-01-05 05:28:28', '2026-01-05 05:28:28'),
('16', 'nurse123', 'nurse', 0, '2026-01-05 05:31:09', '2026-01-05 05:34:43'),
('17', 'B4hYjhs9', 'nurse', 1, '2026-01-05 05:31:13', '2026-01-05 05:31:13'),
('D100', 'cH2P47zj', 'doctor', 1, '2026-01-17 04:14:19', '2026-01-17 04:14:19'),
('D108', 'gcgRjKLw', 'doctor', 1, '2026-01-17 04:14:30', '2026-01-17 04:14:30'),
('D109', 'L8VetUDr', 'doctor', 1, '2026-01-17 04:14:24', '2026-01-17 04:14:24'),
('D123', 'TmT7Dw9p', 'doctor', 1, '2026-01-17 04:13:57', '2026-01-17 04:13:57'),
('D133', '4eXkmjru', 'doctor', 1, '2026-01-17 04:14:36', '2026-01-17 04:14:36'),
('D201', 'M9BacCgS', 'doctor', 1, '2026-01-17 04:14:21', '2026-01-17 04:14:21'),
('D217', '6VuB8tMc', 'doctor', 1, '2026-01-17 04:14:35', '2026-01-17 04:14:35'),
('D247', 'tF4DpY5Q', 'doctor', 1, '2026-01-17 04:14:25', '2026-01-17 04:14:25'),
('D274', 'ze7srxM5', 'doctor', 1, '2026-01-17 04:14:32', '2026-01-17 04:14:32'),
('D277', 'rVNLDmYc', 'doctor', 1, '2026-01-17 04:14:33', '2026-01-17 04:14:33'),
('D278', '9zYRCt3N', 'doctor', 1, '2026-01-17 04:14:28', '2026-01-17 04:14:28'),
('D312', 'vuXXsbxn', 'doctor', 1, '2026-01-17 04:14:27', '2026-01-17 04:14:27'),
('D411', 'm8ztUG22', 'doctor', 1, '2026-01-17 04:14:22', '2026-01-17 04:14:22'),
('D456', 'doctor123', 'doctor', 0, '2026-01-17 04:14:18', '2026-01-17 04:39:28'),
('N123', 'hZp89NSV', 'nurse', 1, '2026-01-17 04:15:56', '2026-01-17 04:15:56'),
('N144', 'kcNH5hq7', 'nurse', 1, '2026-01-17 04:15:58', '2026-01-17 04:15:58'),
('N156', 'h25rg53X', 'nurse', 1, '2026-01-17 04:15:59', '2026-01-17 04:15:59'),
('N211', 'pVjTZe8C', 'nurse', 1, '2026-01-17 04:15:50', '2026-01-17 04:15:50'),
('N213', 'x2yE3NBQ', 'nurse', 1, '2026-01-17 04:15:52', '2026-01-17 04:15:52'),
('N218', 'cd8YMark', 'nurse', 1, '2026-01-17 04:16:00', '2026-01-17 04:16:00'),
('N219', 'N2K47sup', 'nurse', 1, '2026-01-17 04:16:03', '2026-01-17 04:16:03'),
('N222', 'QyWfRjyp', 'nurse', 1, '2026-01-17 04:16:02', '2026-01-17 04:16:02'),
('N224', 't3JTsseF', 'nurse', 1, '2026-01-17 04:15:53', '2026-01-17 04:15:53'),
('N234', 'nurse123', 'nurse', 0, '2026-01-17 04:15:44', '2026-01-17 04:30:04'),
('N245', 'HAkP3hdK', 'nurse', 1, '2026-01-17 04:15:49', '2026-01-17 04:15:49'),
('N321', 'KAAmeXLw', 'nurse', 1, '2026-01-17 04:15:55', '2026-01-17 04:15:55'),
('N678', 'rnDRxuj9', 'nurse', 1, '2026-01-17 04:15:48', '2026-01-17 04:15:48'),
('P003', 'abBxPtKM', 'parent', 1, '2026-01-06 22:41:21', '2026-01-06 22:41:21'),
('P004', 'rYQpMcje', 'parent', 1, '2026-01-06 22:42:04', '2026-01-06 22:42:04'),
('P005', 'parent123', 'parent', 0, '2026-01-06 22:42:06', '2026-01-06 22:49:56'),
('P006', 'parent12345', 'parent', 0, '2026-01-07 14:35:23', '2026-01-07 14:38:44'),
('P007', 'Pb5nuGRy', 'parent', 1, '2026-01-07 14:35:46', '2026-01-07 14:35:46'),
('P008', '7zqZDjpA', 'parent', 1, '2026-01-07 14:35:48', '2026-01-07 14:35:48'),
('P009', 'parent123456', 'parent', 0, '2026-01-07 14:35:49', '2026-01-07 14:41:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`AppointmentID`),
  ADD KEY `appointment_childid_foreign` (`ChildID`),
  ADD KEY `appointment_doctorid_foreign` (`DoctorID`),
  ADD KEY `appointment_nurseid_foreign` (`NurseID`);

--
-- Indexes for table `birthrecord`
--
ALTER TABLE `birthrecord`
  ADD PRIMARY KEY (`BirthID`),
  ADD KEY `birthrecord_childid_foreign` (`ChildID`);

--
-- Indexes for table `child`
--
ALTER TABLE `child`
  ADD PRIMARY KEY (`ChildID`),
  ADD KEY `child_parentid_foreign` (`ParentID`);

--
-- Indexes for table `developmentmilestone`
--
ALTER TABLE `developmentmilestone`
  ADD PRIMARY KEY (`MilestoneID`),
  ADD KEY `developmentmilestone_childid_foreign` (`ChildID`);

--
-- Indexes for table `doctor`
--
ALTER TABLE `doctor`
  ADD PRIMARY KEY (`DoctorID`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `feedingrecord`
--
ALTER TABLE `feedingrecord`
  ADD PRIMARY KEY (`FeedingID`),
  ADD KEY `feedingrecord_childid_foreign` (`ChildID`);

--
-- Indexes for table `growthchart`
--
ALTER TABLE `growthchart`
  ADD PRIMARY KEY (`GrowthID`),
  ADD KEY `growthchart_childid_foreign` (`ChildID`);

--
-- Indexes for table `immunization`
--
ALTER TABLE `immunization`
  ADD PRIMARY KEY (`ImmunizationID`),
  ADD KEY `immunization_childid_foreign` (`ChildID`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nurse`
--
ALTER TABLE `nurse`
  ADD PRIMARY KEY (`NurseID`);

--
-- Indexes for table `parent`
--
ALTER TABLE `parent`
  ADD PRIMARY KEY (`ParentID`);

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
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`ReportID`),
  ADD KEY `report_childid_foreign` (`ChildID`),
  ADD KEY `report_doctorid_foreign` (`DoctorID`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`ScheduleID`),
  ADD KEY `schedule_doctorid_foreign` (`DoctorID`);

--
-- Indexes for table `screeningresult`
--
ALTER TABLE `screeningresult`
  ADD PRIMARY KEY (`ScreeningID`),
  ADD KEY `screeningresult_childid_foreign` (`ChildID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_childid_foreign` FOREIGN KEY (`ChildID`) REFERENCES `child` (`ChildID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `appointment_doctorid_foreign` FOREIGN KEY (`DoctorID`) REFERENCES `doctor` (`DoctorID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `appointment_nurseid_foreign` FOREIGN KEY (`NurseID`) REFERENCES `nurse` (`NurseID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `birthrecord`
--
ALTER TABLE `birthrecord`
  ADD CONSTRAINT `birthrecord_childid_foreign` FOREIGN KEY (`ChildID`) REFERENCES `child` (`ChildID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `child`
--
ALTER TABLE `child`
  ADD CONSTRAINT `child_parentid_foreign` FOREIGN KEY (`ParentID`) REFERENCES `parent` (`ParentID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `developmentmilestone`
--
ALTER TABLE `developmentmilestone`
  ADD CONSTRAINT `developmentmilestone_childid_foreign` FOREIGN KEY (`ChildID`) REFERENCES `child` (`ChildID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `feedingrecord`
--
ALTER TABLE `feedingrecord`
  ADD CONSTRAINT `feedingrecord_childid_foreign` FOREIGN KEY (`ChildID`) REFERENCES `child` (`ChildID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `growthchart`
--
ALTER TABLE `growthchart`
  ADD CONSTRAINT `growthchart_childid_foreign` FOREIGN KEY (`ChildID`) REFERENCES `child` (`ChildID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `immunization`
--
ALTER TABLE `immunization`
  ADD CONSTRAINT `immunization_childid_foreign` FOREIGN KEY (`ChildID`) REFERENCES `child` (`ChildID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `report_childid_foreign` FOREIGN KEY (`ChildID`) REFERENCES `child` (`ChildID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `report_doctorid_foreign` FOREIGN KEY (`DoctorID`) REFERENCES `doctor` (`DoctorID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `schedule_doctorid_foreign` FOREIGN KEY (`DoctorID`) REFERENCES `doctor` (`DoctorID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `screeningresult`
--
ALTER TABLE `screeningresult`
  ADD CONSTRAINT `screeningresult_childid_foreign` FOREIGN KEY (`ChildID`) REFERENCES `child` (`ChildID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
