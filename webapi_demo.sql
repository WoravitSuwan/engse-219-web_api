-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 03, 2025 at 05:40 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webapi_demo`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `sku` varchar(32) NOT NULL,
  `name` varchar(150) NOT NULL,
  `brand` varchar(80) NOT NULL,
  `model` varchar(80) DEFAULT NULL,
  `category` varchar(80) NOT NULL,
  `price` decimal(12,2) NOT NULL CHECK (`price` >= 0),
  `stock` int(11) NOT NULL DEFAULT 0 CHECK (`stock` >= 0),
  `year` year(4) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `name`, `brand`, `model`, `category`, `price`, `stock`, `year`, `created_at`, `updated_at`) VALUES
(1, 'TSLA-M3', 'Tesla Model 3', 'Tesla', 'Model 3', 'รถยนต์ไฟฟ้า', 32990.00, 5, '2023', '2025-10-03 02:22:23', '2025-10-03 02:22:23'),
(2, 'TSLA-MY', 'Tesla Model Y', 'Tesla', 'Model Y', 'รถยนต์ไฟฟ้า', 39990.00, 3, '2024', '2025-10-03 02:22:23', '2025-10-03 02:22:23'),
(3, 'TSLA-MS', 'Tesla Model S', 'Tesla', 'Model S', 'สปอร์ตไฟฟ้า', 89990.00, 1, '2022', '2025-10-03 02:22:23', '2025-10-03 02:22:23'),
(4, 'TYT-COR', 'Toyota Corolla', 'Toyota', 'Corolla', 'ซีดาน', 19990.00, 8, '2021', '2025-10-03 02:22:23', '2025-10-03 02:22:23'),
(5, 'TYT-CAM', 'Toyota Camry', 'Toyota', 'Camry', 'ซีดาน', 24990.00, 6, '2022', '2025-10-03 02:22:23', '2025-10-03 02:22:23'),
(6, 'TYT-RAV4', 'Toyota RAV4', 'Toyota', 'RAV4', 'SUV', 27990.00, 4, '2023', '2025-10-03 02:22:23', '2025-10-03 02:22:23'),
(7, 'FRD-F150', 'Ford F-150', 'Ford', 'F-150', 'กระบะ', 34990.00, 2, '2022', '2025-10-03 02:22:23', '2025-10-03 02:22:23'),
(8, 'FRD-MST', 'Ford Mustang GT', 'Ford', 'Mustang GT', 'สปอร์ต', 55990.00, 2, '2021', '2025-10-03 02:22:23', '2025-10-03 02:22:23'),
(9, 'FRD-ECO', 'Ford EcoSport', 'Ford', 'EcoSport', 'SUV', 16990.00, 7, '2020', '2025-10-03 02:22:23', '2025-10-03 02:22:23'),
(10, 'MST-GT500', 'Mustang Shelby GT500', 'Ford', 'GT500', 'สปอร์ต', 89990.00, 1, '2022', '2025-10-03 02:22:23', '2025-10-03 02:22:23'),
(11, 'FER-F488', 'Ferrari 488', 'Ferrari', '488', 'สปอร์ต', 249990.00, 0, '2019', '2025-10-03 02:22:23', '2025-10-03 02:22:23'),
(12, 'FER-F812', 'Ferrari F8 Tributo', 'Ferrari', 'F8', 'สปอร์ต', 279990.00, 0, '2020', '2025-10-03 02:22:23', '2025-10-03 02:22:23'),
(13, 'TSLA-RD', 'Tesla Roadster (Concept)', 'Tesla', 'Roadster', 'สปอร์ตไฟฟ้า', 200000.00, 0, '2025', '2025-10-03 02:22:23', '2025-10-03 02:22:23'),
(14, 'TOY-HI', 'Toyota Hiace', 'Toyota', 'Hiace', 'ตู้โดยสาร', 22990.00, 3, '2018', '2025-10-03 02:22:23', '2025-10-03 02:22:23'),
(15, 'FRD-MAX', 'Ford Mustang Mach-E', 'Ford', 'Mach-E', 'รถยนต์ไฟฟ้า', 42990.00, 2, '2022', '2025-10-03 02:22:23', '2025-10-03 02:22:23'),
(16, 'TYT-PRI', 'Toyota Prius', 'Toyota', 'Prius', 'ไฮบริด', 21990.00, 4, '2020', '2025-10-03 02:22:23', '2025-10-03 02:22:23'),
(17, 'LUX-LX1', 'LuxSport LX1', 'LuxMotors', 'LX1', 'สปอร์ต', 45990.00, 1, '2023', '2025-10-03 02:22:23', '2025-10-03 02:22:23'),
(18, 'ECO-EV1', 'Econo EV1', 'Econo', 'EV1', 'รถยนต์ไฟฟ้า', 15990.00, 10, '2024', '2025-10-03 02:22:23', '2025-10-03 02:22:23'),
(19, 'FAST-FR1', 'FastCars FR1', 'FastCars', 'FR1', 'สปอร์ต', 89990.00, 1, '2021', '2025-10-03 02:22:23', '2025-10-03 02:22:23'),
(20, 'URB-CTY', 'Urban City', 'CityMotors', 'CTY', 'ฮีทชแบค', 12990.00, 12, '2023', '2025-10-03 02:22:23', '2025-10-03 02:22:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
