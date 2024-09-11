-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 11, 2024 at 12:45 PM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aola`
--

-- --------------------------------------------------------

--
-- Table structure for table `loan_applications`
--

CREATE TABLE `loan_applications` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `loan_id` int(11) DEFAULT NULL,
  `loan_type` varchar(255) NOT NULL,
  `loan_code` varchar(255) NOT NULL,
  `employment_status` varchar(255) DEFAULT NULL,
  `additional_details` text,
  `employer_name` varchar(255) DEFAULT NULL,
  `monthly_income` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `loan_applications`
--

INSERT INTO `loan_applications` (`id`, `username`, `loan_id`, `loan_type`, `loan_code`, `employment_status`, `additional_details`, `employer_name`, `monthly_income`, `created_at`, `status`) VALUES
(1, 'JOSHUA', NULL, 'SALARY ADVANCE', 'A04', 'Employed', '', 'TAMISEMI', '200000', '2024-09-11 06:41:27', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `loan_packages`
--

CREATE TABLE `loan_packages` (
  `id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `loan_type` varchar(255) NOT NULL,
  `loan_code` varchar(255) NOT NULL,
  `qualification` text NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `duration` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `loan_packages`
--

INSERT INTO `loan_packages` (`id`, `loan_id`, `username`, `loan_type`, `loan_code`, `qualification`, `interest_rate`, `duration`) VALUES
(1, 0, 'CRDB_BANK', 'SALARY ADVANCE', 'A04', '1.EMPLOYED', '4.00', '1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `phone` int(10) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('applicant','provider') NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `region`, `phone`, `email`, `role`, `password`, `created_at`) VALUES
(5, 'CRDB_BANK', 'DAR ES SALAAM', 222556780, 'Crdb@gmail.com', 'provider', '61503690505f84b144e6ac89124540a3eb8d22e77db76500984cfc50a1d8776e', '2024-09-06 13:19:54'),
(6, 'JOSHUA', 'DAR ES SALAAM', 687604900, 'Joshua@gmail.com', 'applicant', 'cbfad02f9ed2a8d1e08d8f74f5303e9eb93637d47f82ab6f1c15871cf8dd0481', '2024-09-06 13:23:35'),
(7, 'NBC_BANK', 'MBEYA', 2147483647, 'nbc@gmail.com', 'provider', '61503690505f84b144e6ac89124540a3eb8d22e77db76500984cfc50a1d8776e', '2024-09-06 13:48:03'),
(8, 'EMANUEL', 'MBEYA', 222555225, 'emma@gmail.com', 'applicant', 'cbfad02f9ed2a8d1e08d8f74f5303e9eb93637d47f82ab6f1c15871cf8dd0481', '2024-09-06 13:50:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `loan_applications`
--
ALTER TABLE `loan_applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_packages`
--
ALTER TABLE `loan_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `loan_applications`
--
ALTER TABLE `loan_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `loan_packages`
--
ALTER TABLE `loan_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `loan_packages`
--
ALTER TABLE `loan_packages`
  ADD CONSTRAINT `loan_packages_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loan_providers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
