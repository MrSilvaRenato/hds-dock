-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 11, 2024 at 02:35 AM
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
-- Database: `warehouse_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `booking_date` date DEFAULT NULL,
  `booking_time` time DEFAULT NULL,
  `dock_number` int(11) NOT NULL,
  `transport_company_name` varchar(255) DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `pallets_quantity` int(11) DEFAULT NULL,
  `truck_type` enum('Rigid Truck','B-Double','Single Trailer') DEFAULT NULL,
  `contact_name` varchar(255) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_date`, `booking_time`, `dock_number`, `transport_company_name`, `client_name`, `pallets_quantity`, `truck_type`, `contact_name`, `contact_number`, `created_at`) VALUES
(1, '2024-10-10', '06:45:00', 26, 'ERH Transport', 'Other', 2, '', 'James', '0422422422', '2024-10-10 13:14:19'),
(2, '2024-10-10', '06:00:00', 24, 'ERH Transport', 'Other', 2, '', 'James', '0422422422', '2024-10-10 13:39:27'),
(3, '2024-10-12', '07:30:00', 26, 'ERH Transport', 'Other', 2, '', 'James', '0422422422', '2024-10-10 16:03:29'),
(4, '2024-10-12', '12:45:00', 27, 'ERH Transport', 'Other', 2, '', 'James', '0422422422', '2024-10-10 16:03:56'),
(5, '2024-10-12', '14:15:00', 27, 'ERH Transport', 'Other', 2, '', 'James', '0422422422', '2024-10-10 16:04:12'),
(6, '2024-10-15', '09:00:00', 27, 'Renato Trans', 'Other', 10, '', 'Renato', '0422422422', '2024-10-10 17:39:54'),
(7, '2024-10-11', '06:00:00', 25, 'Renato Trans', 'Other', 10, '', 'Renato', '0422422422', '2024-10-10 22:47:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
