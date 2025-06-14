-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 04, 2025 at 08:37 AM
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
-- Database: `tecroot`
--

-- --------------------------------------------------------

--
-- Table structure for table `advertisement`
--

CREATE TABLE `advertisement` (
  `id` int(11) NOT NULL,
  `Product_Name` varchar(255) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Image_Path` varchar(255) NOT NULL,
  `Publish` tinyint(1) DEFAULT 0,
  `Category` enum('accessories','merchandise','collectibles','video-games') NOT NULL,
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp(),
  `Updated_At` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `advertisement`
--

INSERT INTO `advertisement` (`id`, `Product_Name`, `Price`, `Image_Path`, `Publish`, `Category`, `Created_At`, `Updated_At`) VALUES
(1, 'headset', 200000.00, 'uploads/t-1.jpg', 1, 'accessories', '2025-03-26 18:08:02', '2025-03-26 18:08:02'),
(2, 'abcd', 5000.00, 'uploads/t-1.jpg', 1, 'accessories', '2025-03-28 06:51:47', '2025-03-28 06:54:03');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `position`, `salary`) VALUES
(2, 'shanith', 'head', 2000.00),
(4, 'ometh', 'PR', 4000.00);

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `unit_cost` decimal(10,2) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`item_id`, `item_name`, `quantity`, `unit_cost`, `image_path`, `created_at`, `updated_at`) VALUES
(3, 'xyz', 4, 1000.00, 'uploads/1.png', '2025-03-28 07:10:36', '2025-03-28 07:11:29');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `company` varchar(100) DEFAULT NULL,
  `country` varchar(100) NOT NULL,
  `street_address` varchar(255) NOT NULL,
  `apartment` varchar(100) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `postcode` varchar(20) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `total_items` int(11) NOT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `first_name`, `last_name`, `email`, `phone`, `company`, `country`, `street_address`, `apartment`, `city`, `postcode`, `payment_method`, `total_amount`, `total_items`, `status`, `created_at`, `updated_at`) VALUES
(2, 'Pavapratha', 'Mathi', 'govan.rock468@gmail.com', '+94 788566452', 'ESOFT Metro Campus', 'Sri Lanka', '22 pieris road', '', 'Colombo', '10370', '', 405000.00, 3, 'pending', '2025-04-29 03:45:05', '2025-04-29 03:45:05'),
(3, 'Pavapratha', 'jhwjg', 'pavaprathamathi@gmail.com', '+94 788566452', 'najbd', 'Sri Lanka', 'sjajbda', 'adjan', 'Colombo', '10370', '', 5000.00, 1, 'pending', '2025-04-29 03:46:28', '2025-04-29 03:46:28'),
(4, 'Pavapratha', 'jhwjg', 'pavaprathamathi@gmail.com', '+94 788566452', 'People Solutions Development ', 'Sri Lanka', 'sjajbda', 'adjan', 'Colombo', '10370', '', 5000.00, 1, 'pending', '2025-04-29 03:51:00', '2025-04-29 03:51:00'),
(5, 'Pavapratha', 'Mathi', 'govan.rock468@gmail.com', 'yriugwheif', 'ESOFT Metro Campus', 'Sri Lanka', '22 pieris road', 'adjan', 'Colombo', '10370', '', 200000.00, 1, 'pending', '2025-04-29 05:56:56', '2025-04-29 05:56:56'),
(6, 'vuhdb', 'hsvajbdskhalkjdb', 'pavaprathamathi@gmail.com', '+94 788566452', '', 'Sri Lanka', '22 pieris road', 'adjan', 'dja', '10370', '', 205000.00, 2, 'pending', '2025-04-29 07:05:53', '2025-04-29 07:05:53'),
(7, 'Pavapratha', 'Mathi', 'govan.rock468@gmail.com', '+94 788566452', 'ESOFT Metro Campus', 'Sri Lanka', '22 pieris road', 'adjan', 'Colombo', '10370', '', 5000.00, 1, 'pending', '2025-04-30 15:53:33', '2025-04-30 15:53:33'),
(8, 'Pavapratha', 'Mathi', 'govan.rock468@gmail.com', '+94 76 488 6903', 'ESOFT Metro Campus', 'Sri Lanka', 'sjajbda', 'adjan', 'dnjbh', 'cn scn', '', 5000.00, 1, 'pending', '2025-04-30 15:56:14', '2025-04-30 15:56:14'),
(9, 'Pavapratha', 'Mathi', 'govan.rock468@gmail.com', '+94 76 488 6903', 'ESOFT Metro Campus', 'Sri Lanka', '22 pieris road', 'adjan', 'Colombo', '10370', '', 5000.00, 1, 'pending', '2025-04-30 17:37:20', '2025-04-30 17:37:20'),
(10, 'x,amc', 'calc', 'pavapratha414@gmail.com', 'x,la ,', 'lxma, c', 'Sri Lanka', 'l,cla', 'lcmal', 'clma,', 'cal', '', 10000.00, 2, 'pending', '2025-04-30 17:41:19', '2025-04-30 17:41:19'),
(11, 'ghvjk', 'Mathi', 'yathukulan.7@gmail.com', 'jcnkjvbe', 'najbd', 'Sri Lanka', 'jb', 'knk ', 'ihubj', 'ichlk', '', 5000.00, 1, 'cancelled', '2025-05-01 04:37:34', '2025-05-03 06:18:05'),
(12, 'kbsjc', 'cknswijo;', 'pavapratha414@gmail.com', '+94 788566452', 'ckpnsjc', 'Sri Lanka', 'jb', 'lcmal', 'dja', 'cal', '', 5000.00, 1, 'pending', '2025-05-01 05:10:28', '2025-05-01 05:10:28'),
(14, 'vuhdb', 'jhwjg', 'yathukulan.7@gmail.com', '+94788566452', 'People Solutions Development ', 'Sri Lanka', 'sjajbda', 'kacm', 'dnjbh', 'cn scn', '', 25000.00, 5, 'cancelled', '2025-05-01 06:12:48', '2025-05-02 22:44:34'),
(16, 'Pavapratha', 'hsvajbdskhalkjdb', 'yathukulan.7@gmail.com', '+94 75 876 3382', 'jdnv', 'Sri Lanka', 'kn wmc', 'ej3nwj', 'dja', '10370', 'cod', 5000.00, 1, 'completed', '2025-05-02 08:18:57', '2025-05-02 22:20:24');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `product_name`, `quantity`, `price`, `subtotal`) VALUES
(1, 2, 1, 'headset', 2, 200000.00, 400000.00),
(2, 2, 2, 'abcd', 1, 5000.00, 5000.00),
(3, 3, 2, 'abcd', 1, 5000.00, 5000.00),
(4, 4, 2, 'abcd', 1, 5000.00, 5000.00),
(5, 5, 1, 'headset', 1, 200000.00, 200000.00),
(6, 6, 1, 'headset', 1, 200000.00, 200000.00),
(7, 6, 2, 'abcd', 1, 5000.00, 5000.00),
(8, 7, 2, 'abcd', 1, 5000.00, 5000.00),
(9, 8, 2, 'abcd', 1, 5000.00, 5000.00),
(10, 9, 2, 'abcd', 1, 5000.00, 5000.00),
(11, 10, 2, 'abcd', 2, 5000.00, 10000.00),
(12, 11, 2, 'abcd', 1, 5000.00, 5000.00),
(13, 12, 2, 'abcd', 1, 5000.00, 5000.00),
(16, 14, 2, 'abcd', 5, 5000.00, 25000.00),
(18, 16, 2, 'abcd', 1, 5000.00, 5000.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `mobile_number` varchar(15) NOT NULL,
  `location` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `created_at`, `mobile_number`, `location`, `profile_picture`) VALUES
(1, 'ometh', 'emp123@company.com', '123456', '2025-03-23 08:14:49', '', '', NULL),
(10, 'imashi', 'ima@gmail.com', '678901', '2025-03-25 19:19:27', '1234567890', 'colombo', NULL),
(12, 'vinuli', 'vinulifernando@gmail.com', '230425', '2025-03-25 19:21:21', '0987654321', 'colombo', NULL),
(13, 'hi', 'emphi@gmail.com', '123890', '2025-03-25 19:42:31', '23456789', 'galle', 'uploads/67e3892b9c518.jpeg'),
(14, 'bye', 'empbye@gamil.com', '$2y$10$GLY2VkwgMPEWQWTSfJHw4ujVUsH7crCdXhpcywN0kz1NG/fto8uO.', '2025-03-27 04:23:44', '0987654321', 'ella', NULL),
(15, 'kaveesha', 'empkave@gmail.com', '$2y$10$DIqaEJzyNhYgRJ1rzJdThur03QsdZov3YTBs9X29JK4LRBYdL3Bhi', '2025-03-27 08:04:46', '123456', 'ella', NULL),
(16, 'kave', 'empk@gmail.com', '$2y$10$fEqGAdJwnOxjNJwVFQ1w1uWL8eKfO6LCAYRWo6bPWl51IoUSgkaWC', '2025-03-27 08:06:26', '123', 'ella', NULL),
(17, 'pavi', 'pavi@gmail.com', '$2y$10$N2lbZkhfbfeAutuxo/e.ROU/0Um1gC7gXdZxbx3Tz.KyuGuj83XNe', '2025-03-28 04:22:00', '67890', 'ella', NULL),
(18, 'ometh hettiarachchi', 'ometh@gmail.com', '$2y$10$9k09VyVUNKxMJwH7vTX1WOHBwcP2zfgX2r3VOMS.cOUzIi23HM/Vi', '2025-03-28 05:23:26', '07634334244colo', 'wqde', NULL),
(19, 'shanith', 'empshanith@gmail.com', '$2y$10$/Zjp8Xbsw7oVVli1qZg4heinJ13Nwrn4TLiKZCbbVHrHVBf5FBOxC', '2025-03-28 05:30:57', '0741505447', 'gampaha', NULL),
(20, 'vinuli', 'vinuli@gmail.com', '$2y$10$H1cbdVr73kd7FrFxK/Ik1O8kNLtxx3XLEZX5jFc.XT47MR47CqzY2', '2025-03-28 06:43:54', '123456', 'scu', NULL),
(21, 'ometh', 'empometh@gmail.com', '$2y$10$uzcGPgnK9E5tkpweqUixdOO.J9I7RLG3lDuEIQu.dmAFlxouJ9Kpa', '2025-03-28 06:50:17', '1234566789', 'xzdffg', NULL),
(22, 'pavapratha', 'govan.rock468@gmail.com', '$2y$10$2lILruu.axlcv45m4Kumh.BHVAcYyJC.0ddCCSrDFYfBdKRKC58F2', '2025-04-26 17:37:23', '0788566452', 'Colombo 03', NULL),
(23, 'pavapratha', 'yathukulan.7@gmail.com', '$2y$10$MKRIOFbNphnbuDQ/qsF24.Awv0YiVYecAnDSu63Rmi9oyHbnhQfYO', '2025-04-26 17:37:59', '0788566452', 'Colombo 03', NULL),
(24, 'pavapratha', 'emp12345@gmail.com', '$2y$10$28eGl9Ky5Ek1VWFOc6j.uOQHQ24DpcHBthdU8UGOmwm0zdaPNdkKK', '2025-05-02 05:54:32', '0788566452', 'Colombo 03', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advertisement`
--
ALTER TABLE `advertisement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

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
-- AUTO_INCREMENT for table `advertisement`
--
ALTER TABLE `advertisement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
