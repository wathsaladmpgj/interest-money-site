-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 30, 2024 at 05:52 PM
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
-- Database: `interest`
--

-- --------------------------------------------------------

--
-- Table structure for table `borrowers`
--

CREATE TABLE `borrowers` (
  `id` int(11) NOT NULL,
  `borrower_details_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `lone_number` int(11) NOT NULL,
  `nic` varchar(20) NOT NULL,
  `address` varchar(50) NOT NULL,
  `lone_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `rental` decimal(10,2) DEFAULT 0.00,
  `agree_value` decimal(10,2) DEFAULT 0.00,
  `interest` decimal(10,2) DEFAULT 0.00,
  `interest_day` decimal(10,2) DEFAULT 0.00,
  `amount` decimal(10,2) DEFAULT 0.00,
  `total_arrears` decimal(10,2) DEFAULT 0.00,
  `total_payments` decimal(10,2) DEFAULT 0.00,
  `days_passed` int(11) DEFAULT 0,
  `no_pay` int(11) DEFAULT 0,
  `status` enum('yes','no','con') DEFAULT 'con',
  `no_rental` int(11) NOT NULL,
  `number_of_loans` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowers`
--

INSERT INTO `borrowers` (`id`, `borrower_details_id`, `name`, `lone_number`, `nic`, `address`, `lone_date`, `due_date`, `rental`, `agree_value`, `interest`, `interest_day`, `amount`, `total_arrears`, `total_payments`, `days_passed`, `no_pay`, `status`, `no_rental`, `number_of_loans`) VALUES
(58, 7, 'Janith Wathsala', 1, '200124401296', '1/A', '2024-11-29', '2025-02-01', 5000.00, 325000.00, 75000.00, 1153.85, 250000.00, 138000.00, 7000.00, 29, 2, 'con', 65, 0),
(59, 8, 'kavidu', 1, '22222222222', '1/A', '2024-12-06', '2025-02-08', 800.00, 52000.00, 12000.00, 184.62, 40000.00, 16800.00, 800.00, 22, 1, 'con', 65, 0),
(60, 7, 'Janith Wathsala', 2, '200124401296', '1/A', '2024-12-08', '2025-02-10', 600.00, 39000.00, 9000.00, 138.46, 30000.00, 10200.00, 1800.00, 20, 3, 'con', 65, 0),
(62, 7, 'Janith Wathsala', 3, '200124401296', '1/A', '2024-09-10', '2024-09-19', 2600.00, 26000.00, 6000.00, 600.00, 20000.00, 25000.00, 1000.00, 10, 1, 'no', 10, 0),
(64, 9, 'Thishari', 1, '09098767v', '1/A', '2024-05-15', '2024-06-23', 975.00, 39000.00, 9000.00, 225.00, 30000.00, 38400.00, 600.00, 40, 1, 'no', 40, 0),
(65, 8, 'kavidu', 2, '22222222222', '1/A', '2023-11-17', '2024-01-20', 800.00, 52000.00, 12000.00, 184.62, 40000.00, 52000.00, 0.00, 65, 0, 'no', 65, 0);

-- --------------------------------------------------------

--
-- Table structure for table `borrower_details`
--

CREATE TABLE `borrower_details` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `nic` varchar(20) NOT NULL,
  `address` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrower_details`
--

INSERT INTO `borrower_details` (`id`, `name`, `nic`, `address`) VALUES
(7, 'Janith Wathsala', '200124401296', '1/A'),
(8, 'kavidu', '22222222222', '1/A'),
(9, 'Thishari', '09098767v', '1/A');

-- --------------------------------------------------------

--
-- Table structure for table `employee_details`
--

CREATE TABLE `employee_details` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `nic` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_details`
--

INSERT INTO `employee_details` (`id`, `name`, `nic`) VALUES
(1, 'Janith Wathsala', '56345354'),
(3, 'Pasindu', '34561133221');

-- --------------------------------------------------------

--
-- Table structure for table `employee_payment_details`
--

CREATE TABLE `employee_payment_details` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `salary` int(11) NOT NULL,
  `allownce` int(11) NOT NULL,
  `privision` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_month` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_payment_details`
--

INSERT INTO `employee_payment_details` (`id`, `employee_id`, `salary`, `allownce`, `privision`, `payment_date`, `payment_month`) VALUES
(8, 1, 3000, 200, 100, '2024-12-16', '2024-11');

-- --------------------------------------------------------

--
-- Table structure for table `interestrate`
--

CREATE TABLE `interestrate` (
  `id` int(11) NOT NULL,
  `interest1` decimal(5,2) DEFAULT NULL,
  `interest2` decimal(5,2) DEFAULT NULL,
  `interest3` decimal(5,2) DEFAULT NULL,
  `interest4` decimal(5,2) DEFAULT NULL,
  `updated_month` varchar(20) DEFAULT NULL,
  `end_month` varchar(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interestrate`
--

INSERT INTO `interestrate` (`id`, `interest1`, `interest2`, `interest3`, `interest4`, `updated_month`, `end_month`) VALUES
(14, 10.00, 20.00, 30.00, 40.00, '2024/06', '2024/10'),
(15, 40.00, 30.00, 20.00, 10.00, '2024/11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `monthly_details`
--

CREATE TABLE `monthly_details` (
  `id` int(11) NOT NULL,
  `month` varchar(20) DEFAULT NULL,
  `payment_count` int(11) DEFAULT NULL,
  `total_monthly_payment` decimal(10,2) DEFAULT NULL,
  `total_interest_to_be_received` decimal(10,2) DEFAULT NULL,
  `monthly_payment_sum` decimal(10,2) DEFAULT NULL,
  `interest_received` decimal(10,2) DEFAULT NULL,
  `arrears` decimal(10,2) DEFAULT NULL,
  `total_month_capital` decimal(10,2) DEFAULT NULL,
  `capital_received` decimal(10,2) DEFAULT NULL,
  `interest1` decimal(10,2) DEFAULT NULL,
  `interest2` decimal(10,2) DEFAULT NULL,
  `interest3` decimal(10,2) DEFAULT NULL,
  `interest4` decimal(10,2) DEFAULT NULL,
  `borrower_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `monthly_details`
--

INSERT INTO `monthly_details` (`id`, `month`, `payment_count`, `total_monthly_payment`, `total_interest_to_be_received`, `monthly_payment_sum`, `interest_received`, `arrears`, `total_month_capital`, `capital_received`, `interest1`, `interest2`, `interest3`, `interest4`, `borrower_id`) VALUES
(55, '2024/December', 5, 163200.00, 36184.78, 3600.00, 1600.00, 159600.00, 127015.22, 2000.00, 640.00, 480.00, 320.00, 160.00, NULL),
(56, '2024/November', 1, 5000.00, 1153.85, 6000.00, 1153.85, -1000.00, 3846.15, 4846.15, 461.54, 346.16, 230.77, 115.39, NULL),
(58, '2024/September', 1, 52000.00, 12000.00, 1000.00, 600.00, 51000.00, 40000.00, 400.00, 60.00, 120.00, 180.00, 240.00, NULL),
(59, '2024/June', 1, 22425.00, 5175.00, 600.00, 225.00, 21825.00, 17250.00, 375.00, 22.50, 45.00, 67.50, 90.00, NULL),
(60, '2024/May', 0, 15600.00, 3600.00, 0.00, 0.00, 15600.00, 12000.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL),
(61, '2024/January', 0, 16000.00, 3692.40, 0.00, 0.00, 16000.00, 12307.60, 0.00, 0.00, 0.00, 0.00, 0.00, NULL),
(62, '2023/December', 0, 24800.00, 5723.22, 0.00, 0.00, 24800.00, 19076.78, 0.00, 0.00, 0.00, 0.00, 0.00, NULL),
(63, '2023/November', 0, 10400.00, 2400.06, 0.00, 0.00, 10400.00, 7999.94, 0.00, 0.00, 0.00, 0.00, 0.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `monthly_savings`
--

CREATE TABLE `monthly_savings` (
  `id` int(11) NOT NULL,
  `month` varchar(20) DEFAULT NULL,
  `capital_saving` decimal(10,2) DEFAULT NULL,
  `new_saving` decimal(10,2) DEFAULT NULL,
  `new_loan` decimal(10,2) DEFAULT NULL,
  `stock_increase_percentage` decimal(5,2) DEFAULT NULL,
  `total_stocks` decimal(10,2) DEFAULT NULL,
  `monthly_details_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `monthly_savings`
--

INSERT INTO `monthly_savings` (`id`, `month`, `capital_saving`, `new_saving`, `new_loan`, `stock_increase_percentage`, `total_stocks`, `monthly_details_id`) VALUES
(4612, '2024/November', 4846.15, 245153.85, 250000.00, 98.06, 294378.85, 56),
(4613, '2024/December', 2000.00, 68000.00, 70000.00, 97.14, 362378.85, 55),
(4820, '2024/September', 400.00, 19600.00, 20000.00, 98.00, 49225.00, 58),
(4967, '2024/May', 0.00, 30000.00, 30000.00, 100.00, 30000.00, 60),
(4968, '2024/June', 375.00, 0.00, 0.00, 0.00, 29625.00, 59),
(5007, '2024/January', 0.00, 0.00, 0.00, 0.00, 0.00, 61);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `borrower_id` int(11) DEFAULT NULL,
  `rental_amount` decimal(10,2) DEFAULT 0.00,
  `payment_date` date DEFAULT NULL,
  `du_date` date DEFAULT NULL,
  `monthly_details_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `borrower_id`, `rental_amount`, `payment_date`, `du_date`, `monthly_details_id`) VALUES
(75, 58, 1000.00, '2024-12-12', '2024-12-12', NULL),
(77, 59, 800.00, '2024-12-12', '2024-12-12', NULL),
(78, 60, 600.00, '2024-12-11', '2024-12-11', NULL),
(80, 60, 600.00, '2024-12-18', '2024-12-18', NULL),
(81, 60, 600.00, '2024-12-18', '2024-12-18', NULL),
(83, 58, 6000.00, '2024-11-30', '2024-11-30', NULL),
(84, 62, 1000.00, '2024-09-13', '2024-09-13', NULL),
(85, 64, 600.00, '2024-06-29', '2024-05-29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verification_code` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `borrowers`
--
ALTER TABLE `borrowers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_borrower_details_id` (`borrower_details_id`);

--
-- Indexes for table `borrower_details`
--
ALTER TABLE `borrower_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `nic` (`nic`);

--
-- Indexes for table `employee_details`
--
ALTER TABLE `employee_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nic` (`nic`);

--
-- Indexes for table `employee_payment_details`
--
ALTER TABLE `employee_payment_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `interestrate`
--
ALTER TABLE `interestrate`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `monthly_details`
--
ALTER TABLE `monthly_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_borrower_id` (`borrower_id`);

--
-- Indexes for table `monthly_savings`
--
ALTER TABLE `monthly_savings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `month` (`month`,`monthly_details_id`),
  ADD KEY `monthly_details_id` (`monthly_details_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `borrower_id` (`borrower_id`),
  ADD KEY `fk_monthly_details_id` (`monthly_details_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `borrowers`
--
ALTER TABLE `borrowers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `borrower_details`
--
ALTER TABLE `borrower_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `employee_details`
--
ALTER TABLE `employee_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employee_payment_details`
--
ALTER TABLE `employee_payment_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `interestrate`
--
ALTER TABLE `interestrate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `monthly_details`
--
ALTER TABLE `monthly_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `monthly_savings`
--
ALTER TABLE `monthly_savings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5079;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrowers`
--
ALTER TABLE `borrowers`
  ADD CONSTRAINT `fk_borrower_details_id` FOREIGN KEY (`borrower_details_id`) REFERENCES `borrower_details` (`id`);

--
-- Constraints for table `employee_payment_details`
--
ALTER TABLE `employee_payment_details`
  ADD CONSTRAINT `employee_payment_details_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employee_details` (`id`);

--
-- Constraints for table `monthly_details`
--
ALTER TABLE `monthly_details`
  ADD CONSTRAINT `fk_borrower_id` FOREIGN KEY (`borrower_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `monthly_savings`
--
ALTER TABLE `monthly_savings`
  ADD CONSTRAINT `monthly_savings_ibfk_1` FOREIGN KEY (`monthly_details_id`) REFERENCES `monthly_details` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_monthly_details_id` FOREIGN KEY (`monthly_details_id`) REFERENCES `monthly_details` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`borrower_id`) REFERENCES `borrowers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
