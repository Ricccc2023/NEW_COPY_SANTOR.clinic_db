-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2026 at 12:12 PM
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
-- Database: `clinic_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `age` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `email` varchar(120) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` varchar(20) NOT NULL,
  `tests` text NOT NULL,
  `status` enum('pending','confirmed') DEFAULT 'pending',
  `invoice_number` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `full_name`, `date_of_birth`, `sex`, `age`, `address`, `email`, `phone`, `doctor_id`, `appointment_date`, `appointment_time`, `tests`, `status`, `invoice_number`, `created_at`) VALUES
(68, 'Allen Burch', '1979-05-17', 'Male', 25, 'Obcaecati reprehende', 'maxica@mailinator.com', '+1 (773) 955-9565', 26, '2026-03-04', '7:00-8:00 AM', '[\"CBC\",\"Chest Xray\",\"ECG\"]', 'pending', NULL, '2026-03-03 06:26:43');

-- --------------------------------------------------------

--
-- Table structure for table `appointments_archive`
--

CREATE TABLE `appointments_archive` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `appointment_time` varchar(20) DEFAULT NULL,
  `tests` text DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `invoice_number` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments_archive`
--

INSERT INTO `appointments_archive` (`id`, `full_name`, `date_of_birth`, `sex`, `age`, `address`, `email`, `phone`, `doctor_id`, `appointment_date`, `appointment_time`, `tests`, `status`, `invoice_number`, `created_at`, `archived_at`) VALUES
(43, 'Juancho Mateo', '1996-06-27', 'Male', 0, 'Qui a eligendi cupid', 'lynow@mailinator.com', '09567632316', 26, '1990-03-30', '9:00-10:00 AM', '[\"Physical Exam\"]', 'pending', NULL, '2026-02-26 08:02:21', '2026-02-26 08:02:30'),
(44, 'Harry Ilagan', NULL, NULL, 71, 'Ex aut recusandae C', 'huzib@mailinator.com', '09624919707', 26, '2014-03-13', '10:00-11:00 AM', '[]', 'pending', NULL, '2026-02-26 11:21:38', '2026-02-26 11:21:44'),
(56, 'John Dizon', '1987-08-22', 'Male', 38, 'Makati City', 'john.d@email.com', '09171230002', 26, '2026-03-05', '10:00-11:00 AM', '[\"Chest Xray\"]', 'pending', 'APP-2026-0004', '2026-02-26 15:56:14', '2026-03-01 05:12:26'),
(58, 'Carlo Bautista', '1992-09-09', 'Male', 33, 'Taguig City', 'carlo.b@email.com', '09171230004', 26, '2026-03-06', '3:00-4:00 PM', '[\"Physical Exam\"]', 'pending', 'APP-2026-0006', '2026-02-26 15:56:14', '2026-03-01 05:12:16'),
(60, 'Erin Peters', NULL, NULL, 74, 'Omnis eaque dicta qu', 'zeqak@mailinator.com', '09567632316', 26, '1975-06-29', '11:00-12:00 PM', '[\"Urinalysis\",\"Fecalysis\",\"Chest Xray\",\"ECG\",\"Physical Exam\"]', 'pending', NULL, '2026-03-01 04:32:51', '2026-03-01 05:20:25'),
(61, 'Gavin Skyler', NULL, NULL, 30, 'trapiche', 'admin@gmail.com', '09567632316', 26, '2026-03-02', '7:00-8:00 AM', '[\"CBC\"]', 'pending', NULL, '2026-03-01 05:14:26', '2026-03-01 05:14:37'),
(62, 'Gavin Skyler', NULL, NULL, 30, 'trapiche', 'admin@gmail.com', '09567632316', 26, '2026-03-02', '7:00-8:00 AM', '[\"Chest Xray\"]', 'pending', NULL, '2026-03-01 05:19:03', '2026-03-01 05:19:14'),
(63, 'Harry Ilagan', '2017-06-10', 'Male', 0, 'trapiche tanauan', 'admin@gmail.com', '09567632316', 34, '2026-03-02', '7:00-8:00 AM', '[\"CBC\"]', 'pending', NULL, '2026-03-01 05:28:09', '2026-03-01 06:40:52'),
(67, 'Celso Alonzo', '2026-03-11', 'Male', 0, 'tanauan', 'admin@gmail.com', '09483771583', 34, '2026-03-02', '7:00-8:00 AM', '[\"CBC\",\"Urinalysis\",\"Fecalysis\",\"Chest Xray\",\"ECG\",\"Physical Exam\"]', 'pending', NULL, '2026-03-01 09:00:16', '2026-03-01 09:01:09'),
(69, 'Hillary Mack', '1986-12-23', 'Female', 42, 'Enim qui a adipisici', 'waxatejiq@mailinator.com', '+1 (716) 758-4399', 26, '2026-03-04', '8:00-9:00 AM', '[\"Urinalysis\",\"Fecalysis\",\"Chest Xray\"]', 'pending', NULL, '2026-03-03 06:27:25', '2026-03-06 05:36:55'),
(70, 'Chase Barron', '1972-12-09', 'Male', 12, 'Esse rerum adipisici', 'zyhiw@mailinator.com', '09108299107', 26, '1992-11-14', '8:00-9:00 AM', '[\"Fecalysis\",\"Chest Xray\",\"Physical Exam\"]', 'pending', NULL, '2026-03-03 06:30:16', '2026-03-03 06:30:25');

-- --------------------------------------------------------

--
-- Table structure for table `archived_billings`
--

CREATE TABLE `archived_billings` (
  `id` int(11) NOT NULL,
  `original_billing_id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `payment_mode` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `archived_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `billings`
--

CREATE TABLE `billings` (
  `id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `lab_total` decimal(10,2) DEFAULT 0.00,
  `professional_fee` decimal(10,2) DEFAULT 0.00,
  `procedure_fee` decimal(10,2) DEFAULT 0.00,
  `miscellaneous_fee` decimal(10,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `discount` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) DEFAULT 0.00,
  `payment_mode` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_archived` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billings`
--

INSERT INTO `billings` (`id`, `visit_id`, `patient_id`, `lab_total`, `professional_fee`, `procedure_fee`, `miscellaneous_fee`, `subtotal`, `discount`, `total`, `payment_mode`, `created_at`, `is_archived`) VALUES
(1, 5, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'Cash', '2026-02-26 21:21:15', 1),
(2, 5, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'Cash', '2026-02-26 21:25:29', 1),
(3, 4, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'GCash', '2026-02-26 21:49:24', 1),
(4, 8, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'Cash', '2026-02-26 21:57:25', 1),
(5, 9, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 'Cash', '2026-02-26 22:14:31', 1),
(6, 10, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 'Cash', '2026-02-26 22:15:49', 0),
(7, 11, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 'Card', '2026-02-26 23:27:07', 0),
(8, 12, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 'Cash', '2026-02-26 23:42:48', 0),
(9, 13, 40, 350.00, 500.00, 0.00, 0.00, 850.00, 0.00, 850.00, 'Cash', '2026-02-27 00:01:40', 1),
(10, 14, 41, 200.00, 500.00, 0.00, 0.00, 700.00, 0.00, 700.00, 'GCash', '2026-02-27 00:01:40', 0),
(11, 15, 42, 800.00, 500.00, 0.00, 0.00, 1300.00, 0.00, 1300.00, 'Cash', '2026-02-27 00:01:40', 1),
(12, 16, 43, 600.00, 500.00, 0.00, 0.00, 1100.00, 0.00, 1100.00, 'Card', '2026-02-27 00:01:40', 1),
(13, 17, 44, 950.00, 500.00, 0.00, 0.00, 1450.00, 0.00, 1450.00, 'Cash', '2026-02-27 00:01:40', 1),
(14, 18, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 'Cash', '2026-03-01 13:03:25', 1),
(15, 19, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 'Cash', '2026-03-03 01:29:12', 1);

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `full_name`, `phone`, `is_available`, `created_at`, `user_id`) VALUES
(26, 'Ric Ilagan', '09123456789', 1, '2026-02-26 08:01:48', 12),
(34, 'Jonhson', '+1 (813) 631-9382', 1, '2026-03-01 05:25:40', 21),
(35, 'Juancho Mateooo', '09567632316', 1, '2026-03-01 05:33:36', 22),
(37, 'Bernard Gonzales', '09123654789', 1, '2026-03-01 09:10:54', 24);

-- --------------------------------------------------------

--
-- Table structure for table `doctors_archive`
--

CREATE TABLE `doctors_archive` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archived_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors_archive`
--

INSERT INTO `doctors_archive` (`id`, `full_name`, `phone`, `created_at`, `user_id`, `archived_at`, `archived_by`) VALUES
(27, 'WWW', '09123456789', '2026-02-26 17:20:52', 18, '2026-03-01 05:26:08', 9),
(28, 'DASDA', '091236547854', '2026-02-26 17:32:54', 19, '2026-02-26 17:51:54', 9);

-- --------------------------------------------------------

--
-- Table structure for table `doctor_attendance`
--

CREATE TABLE `doctor_attendance` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent') DEFAULT 'present',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_attendance`
--

INSERT INTO `doctor_attendance` (`id`, `doctor_id`, `attendance_date`, `status`, `created_at`) VALUES
(1, 37, '2026-03-06', 'present', '2026-03-06 03:02:14'),
(2, 35, '2026-03-20', 'present', '2026-03-20 11:09:20'),
(3, 34, '2026-03-20', 'present', '2026-03-20 11:09:22');

-- --------------------------------------------------------

--
-- Table structure for table `lab_tests`
--

CREATE TABLE `lab_tests` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `default_fee` decimal(10,2) DEFAULT 0.00,
  `test_code` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_tests`
--

INSERT INTO `lab_tests` (`id`, `name`, `default_fee`, `test_code`) VALUES
(1, 'CBC', 350.00, 'CBC'),
(2, 'Urinalysis', 200.00, 'URINALYSIS'),
(3, 'Fecalysis', 200.00, 'FECALYSIS'),
(4, 'Chest Xray', 800.00, 'XRAY'),
(5, 'ECG', 600.00, 'ECG'),
(6, 'Physical Exam', 500.00, 'PE');

-- --------------------------------------------------------

--
-- Table structure for table `notification_logs`
--

CREATE TABLE `notification_logs` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `status` varchar(20) NOT NULL,
  `response` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification_logs`
--

INSERT INTO `notification_logs` (`id`, `patient_id`, `phone`, `message`, `status`, `response`, `created_at`) VALUES
(4, 37, '09567632316', 'Good day Juancho Mateo,\n\nYour medical results are now available.\nYou may visit the clinic to review and claim them at your convenience.\n\nFor inquiries, please contact our clinic.\n\nThank you.', 'sent', '{\"ok\":true,\"response\":\"{\\\"data\\\":{\\\"success\\\":true,\\\"message\\\":\\\"SMS added to queue for processing\\\",\\\"smsBatchId\\\":\\\"699ffe7d824e8709cda5fc1f\\\",\\\"recipientCount\\\":1}}\"}', '2026-02-26 16:04:13'),
(5, 37, '09854002367', 'Good day Juancho Mateo,\n\nYour medical results are now available.\nYou may visit the clinic to review and claim them at your convenience.\n\nFor inquiries, please contact our clinic.\n\nThank you.', 'sent', '{\"ok\":true,\"response\":\"{\\\"data\\\":{\\\"success\\\":true,\\\"message\\\":\\\"SMS added to queue for processing\\\",\\\"smsBatchId\\\":\\\"69a02f1b824e8709cdabf856\\\",\\\"recipientCount\\\":1}}\"}', '2026-02-26 19:31:40');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `age` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `attending_doctor_id` int(11) NOT NULL,
  `date_admitted` date NOT NULL,
  `professional_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `invoice_number` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `appointment_id` int(11) DEFAULT NULL,
  `is_archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `full_name`, `date_of_birth`, `sex`, `age`, `address`, `phone`, `email`, `attending_doctor_id`, `date_admitted`, `professional_fee`, `invoice_number`, `created_at`, `appointment_id`, `is_archived`) VALUES
(40, 'Maria Gonzales', '1995-04-12', 'Female', 30, 'Quezon City', '09171231001', 'maria.g@email.com', 26, '2026-03-01', 500.00, 'INV-2026-0010', '2026-02-26 16:01:39', NULL, 0),
(42, 'Angela Ramos', NULL, 'Female', 0, 'Pasig City', '09171231003', 'angela.r@email.com', 34, '2026-03-02', 500.00, 'INV-2026-0012', '2026-02-26 16:01:40', NULL, 0),
(43, 'Carlo Bautista', '1992-09-09', 'Male', 33, 'Taguig City', '09171231004', 'carlo.b@email.com', 26, '2026-03-02', 500.00, 'INV-2026-0013', '2026-02-26 16:01:40', NULL, 0),
(44, 'Liza Fernandez', '1998-12-30', 'Female', 27, 'Manila City', '09171231005', 'liza.f@email.com', 26, '2026-03-03', 500.00, 'INV-2026-0014', '2026-02-26 16:01:40', NULL, 0),
(49, 'Harry Ilagan', NULL, NULL, 0, 'trapiche tanauan', '09567632316', 'admin@gmail.com', 34, '2026-03-02', 0.00, 'APP-2026-0005', '2026-03-01 06:40:52', 63, 0),
(50, 'Celso Alonzo', NULL, NULL, 0, 'tanauan', '09483771583', 'admin@gmail.com', 34, '2026-03-02', 0.00, 'APP-2026-0006', '2026-03-01 09:01:09', 67, 0),
(51, 'Chase Barron', NULL, NULL, 12, 'Esse rerum adipisici', '09108299107', 'zyhiw@mailinator.com', 26, '1992-11-14', 0.00, 'APP-2026-0007', '2026-03-03 06:30:25', 70, 0),
(52, 'Hillary Mack', NULL, NULL, 42, 'Enim qui a adipisici', '+1 (716) 758-4399', 'waxatejiq@mailinator.com', 26, '2026-03-04', 0.00, 'APP-2026-0008', '2026-03-06 05:36:55', 69, 0);

-- --------------------------------------------------------

--
-- Table structure for table `patients_archive`
--

CREATE TABLE `patients_archive` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `age` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `attending_doctor_id` int(11) DEFAULT NULL,
  `date_admitted` date NOT NULL,
  `professional_fee` decimal(10,2) NOT NULL,
  `invoice_number` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archived_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_tests`
--

CREATE TABLE `patient_tests` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `lab_test_id` int(11) NOT NULL,
  `status` enum('Pending','Ongoing','Completed') DEFAULT 'Pending',
  `result` text DEFAULT NULL,
  `result_date` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `test_fee` decimal(10,2) DEFAULT 0.00,
  `is_archived` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_tests`
--

INSERT INTO `patient_tests` (`id`, `patient_id`, `visit_id`, `lab_test_id`, `status`, `result`, `result_date`, `notes`, `test_fee`, `is_archived`, `created_at`) VALUES
(65, 40, 13, 1, 'Completed', NULL, NULL, NULL, 350.00, 0, '2026-02-27 00:01:40'),
(67, 42, 15, 4, 'Completed', NULL, NULL, NULL, 800.00, 0, '2026-02-27 00:01:40'),
(68, 43, 16, 5, 'Completed', NULL, NULL, NULL, 600.00, 0, '2026-02-27 00:01:40'),
(69, 44, 17, 1, 'Completed', NULL, NULL, NULL, 350.00, 0, '2026-02-27 00:01:40'),
(70, 44, 17, 5, 'Completed', NULL, NULL, NULL, 600.00, 0, '2026-02-27 00:01:40'),
(71, 40, 18, 1, 'Completed', NULL, '2026-03-01 06:02:51', '', 350.00, 0, '2026-03-01 06:02:51'),
(72, 42, 19, 1, 'Pending', NULL, NULL, '', 350.00, 0, '2026-03-02 18:28:37'),
(73, 50, 20, 1, 'Completed', NULL, '2026-03-02 21:20:07', '', 350.00, 0, '2026-03-02 21:20:07'),
(74, 50, 20, 4, 'Completed', NULL, '2026-03-02 21:20:07', '', 800.00, 0, '2026-03-02 21:20:07'),
(75, 50, 21, 4, 'Completed', NULL, '2026-03-06 06:00:53', 'Facere rerum non et ', 800.00, 0, '2026-03-06 06:00:53'),
(76, 50, 21, 5, 'Completed', NULL, '2026-03-06 06:00:53', 'Et voluptatibus earu', 600.00, 0, '2026-03-06 06:00:53'),
(77, 50, 21, 3, 'Completed', NULL, '2026-03-06 06:00:53', 'Voluptatibus dolorem', 200.00, 0, '2026-03-06 06:00:53'),
(78, 50, 21, 2, 'Completed', NULL, '2026-03-06 06:00:53', 'Nam lorem laborum es', 200.00, 0, '2026-03-06 06:00:53');

-- --------------------------------------------------------

--
-- Table structure for table `patient_tests_archive`
--

CREATE TABLE `patient_tests_archive` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `lab_test_id` int(11) NOT NULL,
  `status` enum('Pending','Ongoing','Completed') DEFAULT 'Pending',
  `result` text DEFAULT NULL,
  `result_date` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `test_fee` decimal(10,2) DEFAULT 0.00,
  `is_archived` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_test_results`
--

CREATE TABLE `patient_test_results` (
  `id` int(11) NOT NULL,
  `patient_test_id` int(11) NOT NULL,
  `parameter_name` varchar(100) DEFAULT NULL,
  `result_value` varchar(100) DEFAULT NULL,
  `interpretation` varchar(50) DEFAULT NULL,
  `meaning` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_test_results`
--

INSERT INTO `patient_test_results` (`id`, `patient_test_id`, `parameter_name`, `result_value`, `interpretation`, `meaning`, `created_at`) VALUES
(239, 65, 'Hemoglobin', '13.8 g/dL', 'Normal', 'Within normal range', '2026-02-27 00:01:40'),
(240, 65, 'WBC', '7.5 x10^9/L', 'Normal', 'No infection detected', '2026-02-27 00:01:40'),
(241, 65, 'Platelets', '250 x10^9/L', 'Normal', 'Normal clotting capacity', '2026-02-27 00:01:40'),
(245, 67, 'Findings', 'Clear lung fields', 'Normal', 'No signs of pneumonia or TB', '2026-02-27 00:01:40'),
(246, 68, 'Heart Rate', '72 bpm', 'Normal', 'Normal sinus rhythm', '2026-02-27 00:01:40'),
(247, 69, 'Hemoglobin', '12.5 g/dL', 'Normal', 'Within acceptable range', '2026-02-27 00:01:40'),
(248, 70, 'Heart Rate', '75 bpm', 'Normal', 'Normal rhythm', '2026-02-27 00:01:40'),
(249, 71, 'White Blood Cells', '', '', '', '2026-03-01 13:02:51'),
(250, 71, 'Red Blood Cells', '', '', '', '2026-03-01 13:02:51'),
(251, 71, 'Hemoglobin', '', '', '', '2026-03-01 13:02:51'),
(252, 71, 'Hematocrit', '', '', '', '2026-03-01 13:02:51'),
(253, 71, 'Platelets', '', '', '', '2026-03-01 13:02:51'),
(254, 72, 'White Blood Cells', '', '', '', '2026-03-03 01:28:37'),
(255, 72, 'Red Blood Cells', '', '', '', '2026-03-03 01:28:37'),
(256, 72, 'Hemoglobin', '', '', '', '2026-03-03 01:28:37'),
(257, 72, 'Hematocrit', '', '', '', '2026-03-03 01:28:37'),
(258, 72, 'Platelets', '', '', '', '2026-03-03 01:28:37'),
(259, 73, 'White Blood Cells', '', '', '', '2026-03-03 04:20:07'),
(260, 73, 'Red Blood Cells', '', '', '', '2026-03-03 04:20:07'),
(261, 73, 'Hemoglobin', '', '', '', '2026-03-03 04:20:07'),
(262, 73, 'Hematocrit', '', '', '', '2026-03-03 04:20:07'),
(263, 73, 'Platelets', '', '', '', '2026-03-03 04:20:07'),
(264, 74, 'Findings', '', '', '', '2026-03-03 04:20:07'),
(265, 74, 'Impression', '', '', '', '2026-03-03 04:20:07'),
(266, 75, 'Findings', 'Ut sit ut sint volu', 'Deserunt excepteur c', 'Harum minim elit cu', '2026-03-06 13:00:53'),
(267, 75, 'Impression', 'Culpa irure volupta', 'Ex anim nobis atque ', 'Tempore quo odio od', '2026-03-06 13:00:53'),
(268, 76, 'Heart Rate', 'Autem qui ullamco vo', 'Aut ipsum nostrud at', 'Atque voluptas elit', '2026-03-06 13:00:53'),
(269, 76, 'Rhythm', 'Itaque dolor sapient', 'Suscipit voluptate d', 'Voluptate amet aut ', '2026-03-06 13:00:53'),
(270, 76, 'Interpretation', 'Laborum Dolore dolo', 'Dignissimos beatae a', 'Non aliquid enim id ', '2026-03-06 13:00:53'),
(271, 77, 'Color', 'Consequatur fuga T', 'Qui aut voluptas rec', 'Distinctio Assumend', '2026-03-06 13:00:53'),
(272, 77, 'Consistency', 'Dolore ipsa incidid', 'Fuga Debitis unde e', 'Non doloremque quibu', '2026-03-06 13:00:53'),
(273, 77, 'Parasites', 'Quasi error quod cul', 'Minus nostrum repell', 'Iusto amet mollit f', '2026-03-06 13:00:53'),
(274, 77, 'Occult Blood', 'Nam voluptatem Volu', 'Fugit ea temporibus', 'Non saepe molestias ', '2026-03-06 13:00:53'),
(275, 78, 'Color', 'Cumque voluptatibus ', 'Modi minus tempora d', 'Odit saepe sint cum', '2026-03-06 13:00:53'),
(276, 78, 'Transparency', 'Qui fuga Itaque nem', 'Consequatur eum do d', 'Beatae dolores vitae', '2026-03-06 13:00:53'),
(277, 78, 'pH', 'Sed quia dolorem pro', 'In iste et ullam qua', 'Ullam qui dicta dolo', '2026-03-06 13:00:53'),
(278, 78, 'Protein', 'Libero quia aliquip ', 'Et consequatur elit', 'Non anim cupidatat r', '2026-03-06 13:00:53'),
(279, 78, 'Glucose', 'Voluptas cupiditate ', 'Officia incidunt et', 'Et dolore sunt sapie', '2026-03-06 13:00:53');

-- --------------------------------------------------------

--
-- Table structure for table `patient_test_results_archive`
--

CREATE TABLE `patient_test_results_archive` (
  `id` int(11) NOT NULL,
  `patient_test_id` int(11) NOT NULL,
  `parameter_name` varchar(100) DEFAULT NULL,
  `result_value` varchar(100) DEFAULT NULL,
  `interpretation` varchar(50) DEFAULT NULL,
  `meaning` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_test_visits`
--

CREATE TABLE `patient_test_visits` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_test_visits`
--

INSERT INTO `patient_test_visits` (`id`, `patient_id`, `created_at`) VALUES
(13, 40, '2026-03-01 09:10:00'),
(14, 41, '2026-03-01 10:00:00'),
(15, 42, '2026-03-02 09:00:00'),
(16, 43, '2026-03-02 11:00:00'),
(17, 44, '2026-03-03 09:00:00'),
(18, 40, '2026-03-01 06:02:51'),
(19, 42, '2026-03-02 18:28:37'),
(20, 50, '2026-03-02 21:20:07'),
(21, 50, '2026-03-06 06:00:53');

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

CREATE TABLE `tests` (
  `id` int(11) NOT NULL,
  `test_name` varchar(100) NOT NULL,
  `status` enum('active','archived') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `username` varchar(60) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','lab','doctor') NOT NULL DEFAULT 'lab',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `password`, `role`, `created_at`) VALUES
(9, 'Admin', 'Admin123', 'Admin123', 'admin', '2026-02-22 19:10:10'),
(12, 'Ric Ilagan', 'ilagan', 'ilagan', 'doctor', '2026-02-26 08:01:48'),
(21, 'Jonhson Villena', 'villena', 'villena', 'doctor', '2026-03-01 05:25:40'),
(22, 'Juancho Mateoo', 'juancho', 'juancho', 'staff', '2026-03-01 05:33:36'),
(24, 'Bernard Gonzales', 'Bernard', 'Bernard', 'staff', '2026-03-01 09:10:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_appointments_doctor_date` (`doctor_id`,`appointment_date`);

--
-- Indexes for table `appointments_archive`
--
ALTER TABLE `appointments_archive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `archived_billings`
--
ALTER TABLE `archived_billings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `billings`
--
ALTER TABLE `billings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_doctor_user` (`user_id`);

--
-- Indexes for table `doctors_archive`
--
ALTER TABLE `doctors_archive`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_doctor_user` (`user_id`);

--
-- Indexes for table `doctor_attendance`
--
ALTER TABLE `doctor_attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`doctor_id`,`attendance_date`);

--
-- Indexes for table `lab_tests`
--
ALTER TABLE `lab_tests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification_logs`
--
ALTER TABLE `notification_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_patients_doctor` (`attending_doctor_id`);

--
-- Indexes for table `patients_archive`
--
ALTER TABLE `patients_archive`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_archive_patient_doctor` (`attending_doctor_id`),
  ADD KEY `fk_archive_patient_user` (`archived_by`);

--
-- Indexes for table `patient_tests`
--
ALTER TABLE `patient_tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `lab_test_id` (`lab_test_id`);

--
-- Indexes for table `patient_tests_archive`
--
ALTER TABLE `patient_tests_archive`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `lab_test_id` (`lab_test_id`);

--
-- Indexes for table `patient_test_results`
--
ALTER TABLE `patient_test_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_test_id` (`patient_test_id`);

--
-- Indexes for table `patient_test_results_archive`
--
ALTER TABLE `patient_test_results_archive`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_test_id` (`patient_test_id`);

--
-- Indexes for table `patient_test_visits`
--
ALTER TABLE `patient_test_visits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `appointments_archive`
--
ALTER TABLE `appointments_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `archived_billings`
--
ALTER TABLE `archived_billings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `billings`
--
ALTER TABLE `billings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `doctors_archive`
--
ALTER TABLE `doctors_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `doctor_attendance`
--
ALTER TABLE `doctor_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lab_tests`
--
ALTER TABLE `lab_tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notification_logs`
--
ALTER TABLE `notification_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `patients_archive`
--
ALTER TABLE `patients_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `patient_tests`
--
ALTER TABLE `patient_tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `patient_tests_archive`
--
ALTER TABLE `patient_tests_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `patient_test_results`
--
ALTER TABLE `patient_test_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=280;

--
-- AUTO_INCREMENT for table `patient_test_results_archive`
--
ALTER TABLE `patient_test_results_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `patient_test_visits`
--
ALTER TABLE `patient_test_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `fk_appointment_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`);

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `fk_doctor_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `fk_patient_doctor` FOREIGN KEY (`attending_doctor_id`) REFERENCES `doctors` (`id`);

--
-- Constraints for table `patients_archive`
--
ALTER TABLE `patients_archive`
  ADD CONSTRAINT `fk_archive_patient_doctor` FOREIGN KEY (`attending_doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_archive_patient_user` FOREIGN KEY (`archived_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `patient_tests`
--
ALTER TABLE `patient_tests`
  ADD CONSTRAINT `patient_tests_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_tests_ibfk_2` FOREIGN KEY (`lab_test_id`) REFERENCES `lab_tests` (`id`);

--
-- Constraints for table `patient_test_results`
--
ALTER TABLE `patient_test_results`
  ADD CONSTRAINT `patient_test_results_ibfk_1` FOREIGN KEY (`patient_test_id`) REFERENCES `patient_tests` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
