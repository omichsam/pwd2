-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2025 at 09:00 PM
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
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assessments`
--

INSERT INTO `assessments` (`id`, `user_id`, `hospital_id`, `disability_type`, `assessment_date`, `assessment_time`, `status`, `medical_officer_id`, `health_officer_id`, `county_officer_id`, `comment`, `created_at`) VALUES
(1, 1, 1, 'Hearing Impairment', '2025-05-05', '10:00:00', 'approved_by_county_officer', 8, 1, 7, '', '2025-05-02 06:59:29'),
(8, 2, 1, 'Hearing', '2025-05-09', '10:30:00', 'approved_by_county_officer', 8, 1, 7, '', '2025-05-06 18:18:26'),
(9, 3, 1, NULL, '2025-05-16', '00:20:00', 'pending', NULL, NULL, NULL, '', '2025-05-13 08:31:56');

-- --------------------------------------------------------

--
-- Table structure for table `counties`
--

CREATE TABLE `counties` (
  `id` int(11) NOT NULL,
  `county_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `counties`
--

INSERT INTO `counties` (`id`, `county_name`) VALUES
(1, 'Mombasa'),
(2, 'Kwale'),
(3, 'Kilifi'),
(4, 'Tana River'),
(5, 'Lamu'),
(6, 'Taita Taveta'),
(7, 'Garissa'),
(8, 'Wajir'),
(9, 'Mandera'),
(10, 'Marsabit'),
(11, 'Isiolo'),
(12, 'Meru'),
(13, 'Tharaka-Nithi'),
(14, 'Embu'),
(15, 'Kitui'),
(16, 'Machakos'),
(17, 'Makueni'),
(18, 'Nyandarua'),
(19, 'Nyeri'),
(20, 'Kirinyaga'),
(21, 'Murang\'a'),
(22, 'Kiambu'),
(23, 'Turkana'),
(24, 'West Pokot'),
(25, 'Samburu'),
(26, 'Trans Nzoia'),
(27, 'Uasin Gishu'),
(28, 'Elgeyo Marakwet'),
(29, 'Nandi'),
(30, 'Baringo'),
(31, 'Laikipia'),
(32, 'Nakuru'),
(33, 'Narok'),
(34, 'Kajiado'),
(35, 'Kericho'),
(36, 'Bomet'),
(37, 'Kakamega'),
(38, 'Vihiga'),
(39, 'Bungoma'),
(40, 'Busia'),
(41, 'Siaya'),
(42, 'Kisumu'),
(43, 'Homa Bay'),
(44, 'Migori'),
(45, 'Kisii'),
(46, 'Nyamira'),
(47, 'Nairobi');

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `recommended_assistive_products` text DEFAULT NULL,
  `required_services` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hearing_disability_assessments`
--

INSERT INTO `hearing_disability_assessments` (`id`, `assessment_id`, `history_of_hearing_loss`, `history_of_hearing_devices`, `hearing_test_type_right`, `hearing_test_type_left`, `hearing_loss_degree_right`, `hearing_loss_degree_left`, `hearing_level_dbhl_right`, `hearing_level_dbhl_left`, `monaural_percentage_right`, `monaural_percentage_left`, `overall_binaural_percentage`, `conclusion`, `created_at`, `updated_at`, `recommended_assistive_products`, `required_services`) VALUES
(4, 1, '', '', '', '', '', '', 0.00, 0.00, 0.00, 0.00, 0.00, '', '2025-05-11 19:00:52', '2025-05-11 19:00:52', '', ''),
(5, 1, '', '', '', '', '', '', 0.00, 0.00, 0.00, 0.00, 0.00, '', '2025-05-11 19:02:29', '2025-05-11 19:02:29', '', ''),
(6, 8, 'From its medieval origins to the digital era, learn everything there is to know about the ubiquitous lorem ipsum passage.', 'From its medieval origins to the digital era, learn everything there is to know about the ubiquitous lorem ipsum passage.', 'Medium', 'Medium', '4', '3', 7.00, 4.00, 80.00, 50.00, 55.00, 'permanent', '2025-05-13 08:38:30', '2025-05-13 08:38:30', 'From its medieval origins to the digital era, learn everything there is to know about the ubiquitous lorem ipsum passage.', 'Nor is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but occasionally circumstances occur in which toil and pain can procure him some great pleasure.”'),
(7, 8, 'Nor is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but occasionally circumstances occur in which toil and pain can procure him some great pleasure.”', 'Nor is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but occasionally circumstances occur in which toil and pain can procure him some great pleasure.”', '7', '4', '7', '7', 9.00, 34.00, 80.00, 60.00, 63.33, 'permanent', '2025-05-13 08:40:43', '2025-05-13 08:40:43', 'Nor is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but occasionally circumstances occur in which toil and pain can procure him some great pleasure.”', 'Nor is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but occasionally circumstances occur in which toil and pain can procure him some great pleasure.”'),
(8, 8, 'Nor is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but occasionally circumstances occur in which toil and pain can procure him some great pleasure.”', 'Nor is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but occasionally circumstances occur in which toil and pain can procure him some great pleasure.”', '12', '12', '56', '45', 67.00, 34.00, 90.00, 80.00, 81.67, 'permanent', '2025-05-13 08:42:19', '2025-05-13 08:42:19', 'Nor is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but occasionally circumstances occur in which toil and pain can procure him some great pleasure.”', 'Nor is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but occasionally circumstances occur in which toil and pain can procure him some great pleasure.”');

-- --------------------------------------------------------

--
-- Table structure for table `hospitals`
--

CREATE TABLE `hospitals` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `county_id` int(11) DEFAULT NULL,
  `subcounty` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospitals`
--

INSERT INTO `hospitals` (`id`, `name`, `county_id`, `subcounty`, `address`, `created_at`) VALUES
(1, 'Hospital-1', 47, 'Starehe', '00100 K', '2025-05-01 23:21:31'),
(2, 'Hospital-2', 47, 'Starehe', '00100 -M', '2025-05-01 23:23:27'),
(3, 'Vihiga District Hospital', 38, 'Vihiga', 'Vihiga District Hospital', '2025-05-19 13:22:15'),
(4, 'Lumakanda District Hospital', 37, 'Lugari', 'Lumakanda District Hospital', '2025-05-19 13:22:15'),
(5, 'Matunda Sub-Disrict Hospital', 37, 'Lugari', 'Matunda Sub-Disrict Hospital', '2025-05-19 13:22:15'),
(6, 'Kakamega Provincial General Hospital', 37, 'Kakamega Central', 'Kakamega Provincial General Hospital', '2025-05-19 13:22:15'),
(7, 'Butere District Hospital', 37, 'Butere', 'Butere District Hospital', '2025-05-19 13:22:15'),
(8, 'Bungoma District Hospital', 39, 'Bungoma South', 'Bungoma District Hospital', '2025-05-19 13:22:15'),
(9, 'Kimilili District Hospital', 39, 'Bungoma North', 'Kimilili District Hospital', '2025-05-19 13:22:15'),
(10, 'Mt. Elgon District Hospital', 39, 'Mt.Elgon', 'Mt. Elgon District Hospital', '2025-05-19 13:22:15'),
(11, 'Sirisia Sub-District Hospital', 39, 'Bungoma West', 'Sirisia Sub-District Hospital', '2025-05-19 13:22:15'),
(12, 'Webuye District Hospital', 39, 'Bungoma East', 'Webuye District Hospital', '2025-05-19 13:22:15'),
(13, 'Busia District Hospital', 40, 'Busia', 'Busia District Hospital', '2025-05-19 13:22:15'),
(14, 'Teso District Hospital(kocholia)', 40, 'Teso North', 'Teso District Hospital(kocholia)', '2025-05-19 13:22:15'),
(15, 'Alupe Sub-District Hospital', 40, 'Teso South', 'Alupe Sub-District Hospital', '2025-05-19 13:22:15'),
(16, 'Khunyangu Sub-District Hosoital', 40, 'Butula', 'Khunyangu Sub-District Hosoital', '2025-05-19 13:22:15'),
(17, 'Port Victoria District Hospital', 40, 'Bunyala', 'Port Victoria District Hospital', '2025-05-19 13:22:15'),
(18, 'Sio Port District Hospital', 40, 'Samia', 'Sio Port District Hospital', '2025-05-19 13:22:15');

-- --------------------------------------------------------

--
-- Table structure for table `officials`
--

CREATE TABLE `officials` (
  `id` int(11) NOT NULL,
  `license_id` varchar(50) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `id_number` int(10) NOT NULL,
  `county_id` int(11) DEFAULT NULL,
  `mobile_number` varchar(20) DEFAULT NULL,
  `type` enum('medical_officer','health_officer','county_officer') NOT NULL,
  `specialisation` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `hospital_id` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `officials`
--

INSERT INTO `officials` (`id`, `license_id`, `name`, `email`, `id_number`, `county_id`, `mobile_number`, `type`, `specialisation`, `department`, `password`, `hospital_id`, `active`, `created_at`) VALUES
(1, 'HO-123', 'Health Officer', 'healthofficer@gmail.com', 0, 38, '07007070707', 'health_officer', NULL, NULL, '$2y$10$exnzvv94LBUy2HsPH0fEzenTpHLPGG8uRa5SihEg88Avjhvl3qrrC', 1, 1, '2025-05-01 09:16:29'),
(2, 'HO-1234', 'Health Officer 1', 'healthofficer1@gmail.com', 0, 38, '0707707707', 'health_officer', NULL, NULL, '$2y$10$Rc/44FZ4TlmoNsvAFOKndeyPE6TGEcyM0o58kzsCDWXC/8buz4Gbe', 2, 1, '2025-05-01 09:20:27'),
(7, 'CO-123', 'County County', 'county@gmail.com', 36123456, 38, '0720202020', 'county_officer', 'County Administrator', '', '$2y$10$8Y5Xu0/8sXBZLzryqoqvY.9bUnN1ThgAoAquFKvMwmst0qEOZACHC', NULL, 0, '2025-05-06 18:21:53'),
(8, 'MO-123', 'MEDICAL OFFICER 1', 'medicalofficer@gmail.com', 0, 38, '0707070707', 'medical_officer', NULL, NULL, '$2y$10$WwjielpIGJCeL2JiG9DlreAVa09rtH9PXsfBHTJTeZ85ipzpq8AeS', 1, 1, '2025-05-12 07:37:19');

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
  `county_id` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `next_of_kin_name` varchar(100) DEFAULT NULL,
  `next_of_kin_mobile` varchar(20) DEFAULT NULL,
  `next_of_kin_relationship` varchar(50) DEFAULT NULL,
  `subcounty` varchar(50) DEFAULT NULL,
  `education_level` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `gender`, `dob`, `marital_status`, `id_number`, `occupation`, `mobile_number`, `email`, `county_id`, `type`, `next_of_kin_name`, `next_of_kin_mobile`, `next_of_kin_relationship`, `subcounty`, `education_level`, `password`, `created_at`) VALUES
(1, 'Samson', 'Male', '2025-05-01', 'single', '36363636', 'farmer', '0707070707', 'sam@gmail.com', 38, 'pwd', 'james', '0707070707', 'Parent', 'Starehe', 'No Formal', '$2y$10$jVw/pwthKeLkICElWCIv8u42oLdaap1TCyGjOlgEz5AkiAOSJ9Pdi', '2025-05-01 05:10:35'),
(2, 'Joy Joy', 'Female', '2025-05-06', 'married', '36000000', 'IT', '+254700000000', 'joy@gmail.com', 38, 'pwd', 'Jane Doe', '+254700000000', 'Friend', 'CBD', 'Bachelor', '$2y$10$uJ80YWD24/rHPrpWx/sWbuhgWf0fIYvCV3A7YOtK5EfwOrQoFycnq', '2025-05-06 18:12:06'),
(3, 'Tester 1', 'Male', '2000-05-13', 'single', '36970428', 'Tester', '+25470770777', 'tester1@gmail.com', 38, 'pwd', 'Jane Tester', '+25470070707', 'Relative', 'Central District', 'No Formal', '$2y$10$p.ChP2mJmOnuL9MkkxouJ.4H8S4DVG.9Bkjg2QSP7aJhl9QwQu5Li', '2025-05-13 08:30:33');

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
-- Indexes for table `counties`
--
ALTER TABLE `counties`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_hospitals_county` (`county_id`);

--
-- Indexes for table `officials`
--
ALTER TABLE `officials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `license_id` (`license_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `hospital_id` (`hospital_id`),
  ADD KEY `fk_officials_county` (`county_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_number` (`id_number`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_county` (`county_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assessments`
--
ALTER TABLE `assessments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `counties`
--
ALTER TABLE `counties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hearing_disability_assessments`
--
ALTER TABLE `hearing_disability_assessments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `hospitals`
--
ALTER TABLE `hospitals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `officials`
--
ALTER TABLE `officials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- Constraints for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD CONSTRAINT `fk_hospitals_county` FOREIGN KEY (`county_id`) REFERENCES `counties` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `officials`
--
ALTER TABLE `officials`
  ADD CONSTRAINT `fk_officials_county` FOREIGN KEY (`county_id`) REFERENCES `counties` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `officials_ibfk_1` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_county` FOREIGN KEY (`county_id`) REFERENCES `counties` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
