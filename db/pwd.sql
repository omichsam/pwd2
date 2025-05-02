-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2025 at 10:45 AM
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
-- Database: `pwd`
--

-- --------------------------------------------------------

--
-- Table structure for table `assessments`
--

CREATE TABLE `assessments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `hospital_id` int(11) DEFAULT NULL,
  `disability_type` varchar(50) DEFAULT NULL,
  `assessment_date` date DEFAULT NULL,
  `assessment_time` time DEFAULT NULL,
  `status` enum('pending','checked','approved_by_health_officer','approved_by_county_officer','rejected') DEFAULT 'pending',
  `medical_officer_id` int(11) DEFAULT NULL,
  `health_officer_id` int(11) DEFAULT NULL,
  `county_officer_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assessments`
--

INSERT INTO `assessments` (`id`, `user_id`, `hospital_id`, `disability_type`, `assessment_date`, `assessment_time`, `status`, `medical_officer_id`, `health_officer_id`, `county_officer_id`, `created_at`) VALUES
(6, NULL, NULL, NULL, NULL, NULL, 'approved_by_county_officer', NULL, NULL, NULL, '2025-05-02 06:55:01'),
(7, 1, 1, NULL, '2025-05-05', '10:00:00', 'pending', NULL, NULL, NULL, '2025-05-02 06:59:29');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `assessment_id` int(11) DEFAULT NULL,
  `file_path` text DEFAULT NULL,
  `document_type` varchar(100) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hearing_disability_assessments`
--

CREATE TABLE `hearing_disability_assessments` (
  `id` int(11) NOT NULL,
  `assessment_id` int(11) DEFAULT NULL,
  `history_of_hearing_loss` text DEFAULT NULL,
  `history_of_hearing_devices` text DEFAULT NULL,
  `hearing_test_type_right` varchar(100) DEFAULT NULL,
  `hearing_test_type_left` varchar(100) DEFAULT NULL,
  `hearing_loss_degree_right` varchar(100) DEFAULT NULL,
  `hearing_loss_degree_left` varchar(100) DEFAULT NULL,
  `hearing_level_dbhl_right` decimal(5,2) DEFAULT NULL,
  `hearing_level_dbhl_left` decimal(5,2) DEFAULT NULL,
  `monaural_percentage_right` decimal(5,2) DEFAULT NULL,
  `monaural_percentage_left` decimal(5,2) DEFAULT NULL,
  `overall_binaural_percentage` decimal(5,2) DEFAULT NULL,
  `conclusion` enum('temporary','permanent') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospitals`
--

CREATE TABLE `hospitals` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `county` varchar(50) DEFAULT NULL,
  `subcounty` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospitals`
--

INSERT INTO `hospitals` (`id`, `name`, `county`, `subcounty`, `address`, `created_at`) VALUES
(1, 'Hospital-1', 'Nairobi', 'Starehe', '00100 K', '2025-05-01 23:21:31'),
(2, 'Hospital-2', 'Nairobi', 'Starehe', '00100 -M', '2025-05-01 23:23:27');

-- --------------------------------------------------------

--
-- Table structure for table `officials`
--

CREATE TABLE `officials` (
  `id` int(11) NOT NULL,
  `license_id` varchar(50) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mobile_number` varchar(20) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `hospital_id` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `officials`
--

INSERT INTO `officials` (`id`, `license_id`, `name`, `email`, `mobile_number`, `type`, `password`, `hospital_id`, `active`, `created_at`) VALUES
(5, 'HO-123', 'Health Officer', 'healthofficer@gmail.com', '07007070707', 'health-officer', '$2y$10$exnzvv94LBUy2HsPH0fEzenTpHLPGG8uRa5SihEg88Avjhvl3qrrC', NULL, 0, '2025-05-01 09:16:29'),
(6, 'HO-1234', 'Health Officer 1', 'healthofficer1@gmail.com', '0707707707', 'health-officer', '$2y$10$Rc/44FZ4TlmoNsvAFOKndeyPE6TGEcyM0o58kzsCDWXC/8buz4Gbe', 1, 1, '2025-05-01 09:20:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `marital_status` varchar(20) DEFAULT NULL,
  `id_number` varchar(50) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `mobile_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `next_of_kin_name` varchar(100) DEFAULT NULL,
  `next_of_kin_mobile` varchar(20) DEFAULT NULL,
  `next_of_kin_relationship` varchar(50) DEFAULT NULL,
  `county` varchar(50) DEFAULT NULL,
  `subcounty` varchar(50) DEFAULT NULL,
  `education_level` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `gender`, `dob`, `marital_status`, `id_number`, `occupation`, `mobile_number`, `email`, `type`, `next_of_kin_name`, `next_of_kin_mobile`, `next_of_kin_relationship`, `county`, `subcounty`, `education_level`, `password`, `created_at`) VALUES
(1, 'Samson', 'Male', '2025-05-01', 'single', '36363636', 'farmer', '0707070707', 'sam@gmail.com', 'pwd', 'james', '0707070707', 'Parent', 'Nairobi', 'Starehe', 'Bachelor', '$2y$10$DdwvCIAcAG9NWQbG8w6EWenIasmDz.E/PnFAzJUQbKWacmPFO0/e2', '2025-05-01 05:10:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assessments`
--
ALTER TABLE `assessments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `hospital_id` (`hospital_id`),
  ADD KEY `medical_officer_id` (`medical_officer_id`),
  ADD KEY `health_officer_id` (`health_officer_id`),
  ADD KEY `county_officer_id` (`county_officer_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `assessment_id` (`assessment_id`);

--
-- Indexes for table `hearing_disability_assessments`
--
ALTER TABLE `hearing_disability_assessments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assessment_id` (`assessment_id`);

--
-- Indexes for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `officials`
--
ALTER TABLE `officials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `license_id` (`license_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `hospital_id` (`hospital_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_number` (`id_number`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assessments`
--
ALTER TABLE `assessments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hearing_disability_assessments`
--
ALTER TABLE `hearing_disability_assessments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospitals`
--
ALTER TABLE `hospitals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `officials`
--
ALTER TABLE `officials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assessments`
--
ALTER TABLE `assessments`
  ADD CONSTRAINT `assessments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `assessments_ibfk_2` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`id`),
  ADD CONSTRAINT `assessments_ibfk_3` FOREIGN KEY (`medical_officer_id`) REFERENCES `officials` (`id`),
  ADD CONSTRAINT `assessments_ibfk_4` FOREIGN KEY (`health_officer_id`) REFERENCES `officials` (`id`),
  ADD CONSTRAINT `assessments_ibfk_5` FOREIGN KEY (`county_officer_id`) REFERENCES `officials` (`id`);

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`);

--
-- Constraints for table `hearing_disability_assessments`
--
ALTER TABLE `hearing_disability_assessments`
  ADD CONSTRAINT `hearing_disability_assessments_ibfk_1` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`);

--
-- Constraints for table `officials`
--
ALTER TABLE `officials`
  ADD CONSTRAINT `officials_ibfk_1` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
