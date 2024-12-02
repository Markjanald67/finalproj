-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2024 at 04:43 PM
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
-- Database: `quickpuff`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Admin_id` int(188) NOT NULL,
  `Admin_name` varchar(188) NOT NULL,
  `Admin_email` varchar(188) NOT NULL,
  `Admin_pass` varchar(188) NOT NULL,
  `Admin_contact` varchar(188) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Admin_id`, `Admin_name`, `Admin_email`, `Admin_pass`, `Admin_contact`) VALUES
(0, 'admin', 'admin@gmail.com', '$2y$10$PsGZqW.aSfsOIESi65IEz.kmfYQ0VVJof7ZeO1T.Oo..vmjqQWhz.', '09935367769');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `product_id`, `quantity`) VALUES
(88, 3, 1, 1),
(89, 3, 2, 1),
(91, 2, 2, 1),
(92, 2, 1, 1),
(93, 5, 1, 1),
(94, 5, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `city_demographics`
--

CREATE TABLE `city_demographics` (
  `city_id` int(10) UNSIGNED NOT NULL,
  `city_name` varchar(50) NOT NULL,
  `user_count` int(10) UNSIGNED DEFAULT 0,
  `is_cavite` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `city_demographics`
--

INSERT INTO `city_demographics` (`city_id`, `city_name`, `user_count`, `is_cavite`) VALUES
(1, 'Bacoor', 2, 1),
(2, 'Dasmariñas', 4, 1),
(3, 'General Trias', 3, 1),
(4, 'Imus', 1, 1),
(5, 'Tagaytay', 2, 1),
(6, 'Trece Martires', 2, 1),
(7, 'Outside Cavite', 6, 0);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `ORDER_ID` int(11) UNSIGNED NOT NULL,
  `CREATED_AT` timestamp NOT NULL DEFAULT current_timestamp(),
  `QUANTITY` int(11) NOT NULL,
  `USER_ID` int(11) UNSIGNED NOT NULL,
  `TOTAL_AMOUNT` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ORDER_STATUS` enum('Pending','Shipped','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
  `UPDATED_AT` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`ORDER_ID`, `CREATED_AT`, `QUANTITY`, `USER_ID`, `TOTAL_AMOUNT`, `ORDER_STATUS`, `UPDATED_AT`) VALUES
(1, '2024-11-12 16:36:04', 0, 1, 550.00, 'Pending', '2024-11-12 16:36:04'),
(2, '2024-11-12 16:38:17', 0, 1, 550.00, 'Pending', '2024-11-12 16:38:17'),
(3, '2024-11-12 16:40:42', 0, 1, 550.00, 'Pending', '2024-11-12 16:40:42'),
(4, '2024-11-12 16:50:15', 0, 1, 1100.00, 'Pending', '2024-11-12 16:50:15'),
(5, '2024-11-12 17:43:12', 1, 1, 550.00, 'Pending', '2024-11-12 17:43:12'),
(6, '2024-11-12 17:43:40', 2, 1, 1100.00, 'Pending', '2024-11-12 17:43:40'),
(7, '2024-11-12 17:43:56', 2, 1, 1100.00, 'Pending', '2024-11-12 17:43:56'),
(8, '2024-11-12 18:46:06', 2, 1, 1100.00, 'Pending', '2024-11-12 18:46:06'),
(9, '2024-11-12 18:49:46', 2, 1, 1100.00, 'Pending', '2024-11-12 18:49:46'),
(10, '2024-11-12 19:00:04', 2, 1, 1100.00, 'Pending', '2024-11-12 19:00:04'),
(11, '2024-11-12 19:04:49', 2, 1, 1100.00, 'Pending', '2024-11-12 19:04:49'),
(12, '2024-11-12 19:25:09', 1, 1, 550.00, 'Pending', '2024-11-12 19:25:09'),
(13, '2024-11-12 19:27:21', 1, 1, 550.00, 'Pending', '2024-11-12 19:27:21'),
(14, '2024-11-12 19:27:53', 1, 1, 550.00, 'Pending', '2024-11-12 19:27:53'),
(15, '2024-11-12 19:39:19', 2, 1, 1100.00, 'Pending', '2024-11-12 19:39:19'),
(16, '2024-11-13 04:38:55', 2, 2, 1100.00, 'Pending', '2024-11-13 04:38:55'),
(17, '2024-11-13 04:55:58', 3, 2, 1650.00, 'Pending', '2024-11-13 04:55:58'),
(18, '2024-11-13 18:45:21', 1, 7, 550.00, 'Pending', '2024-11-13 18:45:21'),
(19, '2024-11-13 19:06:31', 1, 7, 550.00, 'Pending', '2024-11-13 19:06:31'),
(20, '2024-11-14 18:38:49', 1, 5, 550.00, 'Pending', '2024-11-14 18:38:49'),
(21, '2024-11-14 19:06:11', 0, 5, 0.00, 'Pending', '2024-11-14 19:06:11'),
(22, '2024-11-14 19:06:48', 1, 5, 550.00, 'Pending', '2024-11-14 19:06:48'),
(23, '2024-11-14 19:24:43', 1, 5, 550.00, 'Pending', '2024-11-14 19:24:43'),
(24, '2024-11-16 04:39:50', 2, 8, 1100.00, 'Pending', '2024-11-16 04:39:50'),
(25, '2024-11-18 15:34:47', 3, 3, 1650.00, 'Pending', '2024-11-18 15:34:47'),
(26, '2024-11-18 16:50:31', 1, 3, 550.00, 'Pending', '2024-11-18 16:50:31'),
(27, '2024-11-18 16:51:03', 1, 3, 550.00, 'Pending', '2024-11-18 16:51:03'),
(28, '2024-11-18 16:57:56', 1, 3, 550.00, 'Pending', '2024-11-18 16:57:56'),
(29, '2024-11-18 17:05:56', 1, 3, 550.00, 'Pending', '2024-11-18 17:05:56'),
(30, '2024-11-18 17:08:47', 1, 3, 550.00, 'Pending', '2024-11-18 17:08:47'),
(31, '2024-11-18 17:26:47', 1, 3, 550.00, 'Pending', '2024-11-18 17:26:47'),
(32, '2024-11-18 17:49:52', 1, 3, 550.00, 'Pending', '2024-11-18 17:49:52'),
(33, '2024-11-18 17:54:49', 1, 3, 550.00, 'Delivered', '2024-11-19 06:48:31'),
(34, '2024-11-19 06:51:20', 1, 5, 550.00, 'Delivered', '2024-11-19 07:10:25'),
(35, '2024-11-19 07:10:11', 1, 5, 550.00, 'Pending', '2024-11-19 07:10:11'),
(36, '2024-11-21 07:30:28', 2, 3, 1100.00, 'Pending', '2024-11-21 07:30:28'),
(37, '2024-11-22 07:44:42', 2, 3, 1100.00, 'Pending', '2024-11-22 07:44:42'),
(38, '2024-11-22 08:56:17', 1, 3, 550.00, 'Pending', '2024-11-22 08:56:17'),
(39, '2024-11-22 10:23:44', 1, 3, 550.00, 'Pending', '2024-11-22 10:23:44'),
(40, '2024-11-22 11:37:51', 1, 3, 550.00, 'Pending', '2024-11-22 11:37:51'),
(41, '2024-11-22 12:35:07', 1, 3, 550.00, 'Pending', '2024-11-22 12:35:07'),
(42, '2024-11-22 17:17:48', 1, 3, 550.00, 'Pending', '2024-11-22 17:17:48'),
(50, '2024-11-22 18:45:52', 1, 3, 550.00, 'Pending', '2024-11-22 18:45:52'),
(52, '2024-11-22 18:50:55', 1, 3, 550.00, 'Pending', '2024-11-22 18:50:55'),
(57, '2024-11-22 19:05:49', 1, 3, 550.00, 'Pending', '2024-11-22 19:05:49'),
(58, '2024-11-22 19:12:10', 1, 3, 550.00, 'Pending', '2024-11-22 19:12:10'),
(59, '2024-11-22 19:21:22', 1, 3, 550.00, 'Pending', '2024-11-22 19:21:22'),
(60, '2024-11-22 19:24:53', 1, 3, 550.00, 'Pending', '2024-11-22 19:24:53'),
(63, '2024-11-22 19:39:22', 1, 3, 550.00, 'Pending', '2024-11-22 19:39:22'),
(65, '2024-11-22 19:41:26', 1, 3, 550.00, 'Pending', '2024-11-22 19:41:26'),
(66, '2024-11-22 19:41:52', 1, 3, 550.00, 'Pending', '2024-11-22 19:41:52'),
(69, '2024-11-22 19:49:35', 1, 3, 550.00, 'Pending', '2024-11-22 19:49:35'),
(70, '2024-11-22 19:50:09', 1, 3, 550.00, 'Pending', '2024-11-22 19:50:09'),
(71, '2024-11-22 19:50:58', 1, 3, 550.00, 'Pending', '2024-11-22 19:50:58'),
(72, '2024-11-22 20:04:11', 1, 3, 550.00, 'Pending', '2024-11-22 20:04:11'),
(73, '2024-11-22 21:06:46', 1, 3, 550.00, 'Pending', '2024-11-22 21:06:46'),
(75, '2024-11-23 06:11:02', 1, 3, 550.00, 'Pending', '2024-11-23 06:11:02'),
(76, '2024-11-23 21:48:52', 4, 3, 2200.00, 'Pending', '2024-11-23 21:48:52'),
(77, '2024-11-23 21:51:06', 1, 3, 550.00, 'Pending', '2024-11-23 21:51:06'),
(78, '2024-11-23 21:59:46', 1, 3, 550.00, 'Pending', '2024-11-23 21:59:46'),
(79, '2024-11-23 22:02:16', 1, 3, 550.00, 'Pending', '2024-11-23 22:02:16'),
(80, '2024-11-23 22:18:33', 1, 3, 550.00, 'Pending', '2024-11-23 22:18:33'),
(81, '2024-11-23 22:20:00', 1, 3, 550.00, 'Pending', '2024-11-23 22:20:00'),
(82, '2024-11-23 22:21:02', 1, 3, 550.00, 'Pending', '2024-11-23 22:21:02'),
(83, '2024-11-23 22:21:45', 1, 3, 550.00, 'Pending', '2024-11-23 22:21:45'),
(84, '2024-11-23 22:22:30', 1, 3, 550.00, 'Pending', '2024-11-23 22:22:30'),
(85, '2024-11-23 22:35:30', 1, 3, 550.00, 'Pending', '2024-11-23 22:35:30'),
(86, '2024-11-23 22:43:20', 1, 3, 550.00, 'Pending', '2024-11-23 22:43:20'),
(87, '2024-11-24 07:51:38', 1, 3, 550.00, 'Pending', '2024-11-24 07:51:38'),
(97, '2024-11-24 10:50:51', 1, 2, 550.00, 'Pending', '2024-11-24 10:50:51');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) UNSIGNED NOT NULL,
  `ORDER_ID` int(11) UNSIGNED NOT NULL,
  `PRODUCT_ID` int(11) UNSIGNED NOT NULL,
  `QUANTITY` int(11) NOT NULL,
  `PRICE` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `ORDER_ID`, `PRODUCT_ID`, `QUANTITY`, `PRICE`) VALUES
(1, 1, 3, 1, 550.00),
(2, 2, 2, 1, 550.00),
(3, 3, 2, 1, 550.00),
(4, 4, 2, 2, 550.00),
(5, 5, 2, 1, 550.00),
(6, 6, 2, 2, 550.00),
(7, 7, 1, 1, 550.00),
(8, 7, 3, 1, 550.00),
(9, 8, 3, 1, 550.00),
(10, 8, 4, 1, 550.00),
(11, 9, 1, 1, 550.00),
(12, 9, 2, 1, 550.00),
(13, 10, 3, 1, 550.00),
(14, 10, 2, 1, 550.00),
(15, 11, 2, 1, 550.00),
(16, 11, 3, 1, 550.00),
(17, 12, 1, 1, 550.00),
(18, 13, 1, 1, 550.00),
(19, 14, 3, 1, 550.00),
(20, 15, 1, 1, 550.00),
(21, 15, 3, 1, 550.00),
(22, 16, 1, 1, 550.00),
(23, 16, 3, 1, 550.00),
(24, 17, 1, 1, 550.00),
(25, 17, 3, 1, 550.00),
(26, 17, 4, 1, 550.00),
(27, 18, 1, 1, 550.00),
(28, 19, 1, 1, 550.00),
(29, 20, 3, 1, 550.00),
(30, 22, 5, 1, 550.00),
(31, 23, 4, 1, 550.00),
(32, 24, 4, 1, 550.00),
(33, 24, 6, 1, 550.00),
(34, 25, 1, 1, 550.00),
(35, 25, 5, 1, 550.00),
(36, 25, 6, 1, 550.00),
(37, 26, 6, 1, 550.00),
(38, 27, 4, 1, 550.00),
(39, 28, 6, 1, 550.00),
(40, 29, 6, 1, 550.00),
(41, 30, 6, 1, 550.00),
(42, 31, 5, 1, 550.00),
(43, 32, 4, 1, 550.00),
(44, 33, 4, 1, 550.00),
(45, 34, 4, 1, 550.00),
(46, 35, 4, 1, 550.00),
(47, 36, 5, 1, 550.00),
(48, 36, 4, 1, 550.00),
(49, 37, 5, 2, 550.00),
(50, 38, 5, 1, 550.00),
(51, 39, 5, 1, 550.00),
(52, 40, 6, 1, 550.00),
(53, 41, 7, 1, 550.00),
(54, 42, 6, 1, 550.00),
(62, 50, 5, 1, 550.00),
(64, 52, 6, 1, 550.00),
(69, 57, 6, 1, 550.00),
(70, 58, 7, 1, 550.00),
(71, 59, 7, 1, 550.00),
(72, 60, 7, 1, 550.00),
(75, 63, 7, 1, 550.00),
(77, 65, 7, 1, 550.00),
(78, 66, 7, 1, 550.00),
(81, 69, 7, 1, 550.00),
(82, 70, 7, 1, 550.00),
(83, 71, 7, 1, 550.00),
(84, 72, 1, 1, 550.00),
(85, 73, 1, 1, 550.00),
(87, 75, 1, 1, 550.00),
(88, 76, 1, 4, 550.00),
(89, 77, 1, 1, 550.00),
(90, 78, 1, 1, 550.00),
(91, 79, 1, 1, 550.00),
(92, 80, 1, 1, 550.00),
(93, 81, 1, 1, 550.00),
(94, 82, 1, 1, 550.00),
(95, 83, 1, 1, 550.00),
(96, 84, 1, 1, 550.00),
(97, 85, 1, 1, 550.00),
(98, 86, 1, 1, 550.00),
(99, 87, 1, 1, 550.00),
(113, 97, 5, 1, 550.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `PRODUCT_ID` int(11) UNSIGNED NOT NULL,
  `PRODUCT_NAME` varchar(188) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `PRICE` decimal(10,2) NOT NULL,
  `STOCK` int(11) NOT NULL,
  `FLAVOURS` varchar(188) NOT NULL,
  `EXP_DATE` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`PRODUCT_ID`, `PRODUCT_NAME`, `image_path`, `PRICE`, `STOCK`, `FLAVOURS`, `EXP_DATE`) VALUES
(1, 'QPAL Ultra Vanilla', '/uploads/Vanilla.png', 550.00, 2, 'Vanilla', '2025-10-25'),
(2, 'QPAL Ultra Chocolate', '/uploads/Chocolate.png', 550.00, 0, 'Chocolate', '2025-12-31'),
(3, 'QPAL Ultra Strawberry', '/uploads/Strawberry.png', 550.00, 0, 'Strawberry', '2025-12-31'),
(4, 'QPAL Ultra Mango', '/uploads/Mango.png', 550.00, 0, 'Mango', '2025-12-31'),
(5, 'QPAL Ultra Lemon', '/uploads/Lemon.png', 550.00, 0, 'Lemon', '2025-12-31'),
(6, 'QPAL Ultra Blueberry', '/uploads/Blueberry.png', 550.00, 0, 'Blueberry', '2025-12-31'),
(7, 'QPAL ULTRA Bubblegum', '/uploads/Bubblegum.png', 550.00, 0, 'Bubblegum', '2026-12-31');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `TRANSACTION_ID` int(11) UNSIGNED NOT NULL,
  `ORDER_ID` int(11) UNSIGNED NOT NULL,
  `USER_ID` int(11) UNSIGNED NOT NULL,
  `DV_ADDRESS` varchar(255) DEFAULT NULL,
  `MODE_OF_PAYMENT` varchar(50) NOT NULL,
  `TOTAL_AMOUNT` decimal(10,2) NOT NULL,
  `CHANGE_AMOUNT` decimal(10,2) NOT NULL,
  `AMOUNT_TENDERED` decimal(10,2) NOT NULL,
  `GCASH_REF_NUMBER` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`TRANSACTION_ID`, `ORDER_ID`, `USER_ID`, `DV_ADDRESS`, `MODE_OF_PAYMENT`, `TOTAL_AMOUNT`, `CHANGE_AMOUNT`, `AMOUNT_TENDERED`, `GCASH_REF_NUMBER`) VALUES
(1, 1, 1, NULL, 'cash', 550.00, 1450.00, 2000.00, NULL),
(2, 2, 1, NULL, 'cash', 550.00, 1450.00, 2000.00, NULL),
(3, 3, 1, NULL, 'card', 550.00, 1450.00, 2000.00, NULL),
(4, 4, 1, NULL, 'card', 1100.00, 400.00, 1500.00, NULL),
(5, 5, 1, NULL, 'cash', 550.00, 1450.00, 2000.00, NULL),
(6, 6, 1, NULL, 'cash', 1100.00, 900.00, 2000.00, NULL),
(7, 7, 1, NULL, 'card', 1100.00, 900.00, 2000.00, NULL),
(8, 8, 1, NULL, 'card', 1100.00, 900.00, 2000.00, NULL),
(9, 9, 1, NULL, 'cash', 1100.00, 900.00, 2000.00, NULL),
(10, 10, 1, NULL, 'cash', 1100.00, 900.00, 2000.00, NULL),
(11, 11, 1, NULL, 'cash', 1100.00, 900.00, 2000.00, NULL),
(12, 12, 1, NULL, 'cash', 550.00, 1450.00, 2000.00, NULL),
(13, 13, 1, NULL, 'cash', 550.00, 1450.00, 2000.00, NULL),
(14, 14, 1, NULL, 'card', 550.00, 1450.00, 2000.00, NULL),
(15, 15, 1, NULL, 'card', 1100.00, 900.00, 2000.00, NULL),
(16, 16, 2, NULL, 'cash', 1100.00, 900.00, 2000.00, NULL),
(17, 17, 2, NULL, 'cash', 1650.00, 350.00, 2000.00, NULL),
(18, 18, 7, NULL, 'cash', 550.00, 1450.00, 2000.00, NULL),
(19, 19, 7, NULL, 'cash', 550.00, 1450.00, 2000.00, NULL),
(20, 20, 5, NULL, 'card', 550.00, 1450.00, 2000.00, NULL),
(21, 21, 5, 'yutyu', 'card', 0.00, 6.00, 6.00, NULL),
(22, 22, 5, 'qc', 'card', 550.00, 450.00, 1000.00, NULL),
(23, 23, 5, 'takloban', 'cash', 550.00, 1450.00, 2000.00, NULL),
(24, 24, 8, 'kanto lng ', 'cash', 1100.00, 899.95, 1999.95, NULL),
(25, 25, 3, 'asdasd', 'card', 1650.00, 350.00, 2000.00, NULL),
(26, 26, 3, 'qweq', 'cash', 550.00, 672.00, 1222.00, NULL),
(27, 27, 3, '13123', 'card', 550.00, 672.00, 1222.00, NULL),
(28, 28, 3, '13213', 'card', 550.00, 683.00, 1233.00, NULL),
(29, 29, 3, 'kld', 'card', 550.00, 450.00, 1000.00, NULL),
(30, 30, 3, '2313', 'cash', 550.00, 1672.00, 2222.00, NULL),
(31, 31, 3, 'asda', 'card', 550.00, 683.00, 1233.00, NULL),
(32, 32, 3, '12321321', 'cash', 550.00, 683.00, 1233.00, NULL),
(33, 33, 3, 'awqeqw', 'cash', 550.00, 683.00, 1233.00, NULL),
(34, 34, 5, '2324', 'cash', 550.00, 1450.00, 2000.00, NULL),
(35, 35, 5, '1232', 'card', 550.00, 683.00, 1233.00, NULL),
(36, 36, 3, 'cebu', 'card', 1100.00, 400.00, 1500.00, NULL),
(37, 37, 3, '123', 'card', 1100.00, 400.00, 1500.00, NULL),
(38, 38, 3, 'area 1', 'cash', 550.00, 450.00, 1000.00, NULL),
(39, 39, 3, '12312', 'cash', 550.00, 561.00, 1111.00, NULL),
(40, 40, 3, 'QWEWQ', 'gcash', 550.00, 0.00, 550.00, NULL),
(41, 41, 3, 'asdad', 'cash', 550.00, 0.00, 0.00, NULL),
(42, 42, 3, 'tawi tawi', 'cash', 550.00, 0.00, 550.00, NULL),
(43, 50, 3, '123', 'gcash', 550.00, 10561.00, 11111.00, 'GCASH17323011527943'),
(44, 52, 3, 'atlas', 'gcash', 550.00, 1450.00, 2000.00, 'GCASH17323014556270'),
(45, 57, 3, '123', 'gcash', 550.00, 450.02, 1000.02, 'GCASH17323023498417'),
(46, 58, 3, '123', 'gcash', 550.00, 672.00, 1222.00, 'GCASH17323027308617'),
(47, 59, 3, '123', 'cash', 550.00, 0.00, 0.00, NULL),
(48, 60, 3, '123', 'cash', 550.00, 0.00, 0.00, NULL),
(49, 63, 3, '123', 'cash', 550.00, 0.00, 0.00, NULL),
(50, 65, 3, '123', 'gcash', 550.00, 450.00, 1000.00, 'GCASH17323044864982'),
(51, 66, 3, '344', 'cash', 550.00, 0.00, 0.00, NULL),
(52, 69, 3, '123', 'gcash', 550.00, 281.00, 831.00, 'GCASH17323049754398'),
(53, 70, 3, '123', 'gcash', 550.00, 683.00, 1233.00, 'GCASH17323050098859'),
(54, 71, 3, '1233', 'cash', 550.00, 0.00, 0.00, NULL),
(55, 72, 3, '123', 'cash', 550.00, 0.00, 0.00, NULL),
(56, 73, 3, '123', 'cash', 550.00, 0.00, 0.00, NULL),
(57, 75, 3, 'santa lucia', 'gcash', 550.00, 450.00, 1000.00, 'GCASH17323422626217'),
(58, 76, 3, 'DASMA', 'cash', 2200.00, 0.00, 0.00, NULL),
(59, 77, 3, '123', 'cash', 550.00, 0.00, 0.00, NULL),
(60, 78, 3, '123', 'cash', 550.00, 0.00, 0.00, NULL),
(61, 79, 3, '123', 'cash', 550.00, 0.00, 0.00, NULL),
(62, 80, 3, '123', 'cash', 550.00, 0.00, 0.00, NULL),
(63, 81, 3, '123', 'cash', 550.00, 0.00, 0.00, NULL),
(64, 82, 3, '123', 'cash', 550.00, 0.00, 0.00, NULL),
(65, 83, 3, '123', 'cash', 550.00, 0.00, 0.00, NULL),
(66, 84, 3, '123', 'cash', 550.00, 0.00, 0.00, NULL),
(67, 85, 3, '123', 'cash', 550.00, 0.00, 0.00, NULL),
(68, 86, 3, '123', 'cash', 550.00, 0.00, 0.00, NULL),
(69, 87, 3, 'dasma', 'gcash', 550.00, 450.00, 1000.00, 'GCASH17324346987466'),
(70, 97, 2, '123', 'cash', 550.00, 0.00, 0.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `USER_ID` int(11) UNSIGNED NOT NULL,
  `USER_NAME` varchar(50) NOT NULL,
  `EMAIL` varchar(100) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `CONTACT_NO` varchar(15) DEFAULT NULL,
  `city` varchar(50) NOT NULL,
  `AGE` int(3) DEFAULT NULL CHECK (`AGE` >= 0),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`USER_ID`, `USER_NAME`, `EMAIL`, `PASSWORD`, `CONTACT_NO`, `city`, `AGE`, `created_at`, `profile_picture`) VALUES
(1, 'test', 'test@gmail.com', '$2y$10$30gwu6Y3E/9UbTTId0qAWuOB8lkFgtqWbhw3MKYwGqztWT/Cd5eWi', '0999123131', '', 21, '2024-11-12 16:33:08', NULL),
(2, '', 'user@gmail.com', '$2y$10$BK13WjZpTNX1mpR2SkyIk.8woSEi3W029BZhVcAOTljPRTXreNwty', NULL, '', 20, '2024-11-13 00:16:25', NULL),
(3, '', 'raven@gmail.com', '$2y$10$JGO4YSJWnUH7zbBhGs3q/.J6dzawI/8EcJFThxpibf7EeuK4oEGkS', NULL, '', 25, '2024-11-13 10:45:09', NULL),
(4, 'christian', 'christian@gmail.com', '$2y$10$mEQ.r1jqRexw8EbPpkF3yuP5ctonj4le6c.SZW80NChUszUjYZzdK', '099831231', '', 19, '2024-11-13 17:35:22', NULL),
(5, 'jassy', 'jassy@gmail.com', '$2y$10$M0BYgVRrAwORzWTQw8piTexep9cKfvjfmHXao1OhVYbC2mFviMsnG', '0123241341', 'Outside Cavite', 69, '2024-11-13 18:08:20', 'uploads/profile_5.jpg'),
(7, 'sample', 'sample@gmail.com', '$2y$10$zEbl.Xgsi.ypK6u5zyA8k.UO5ni0ahcdwh7bFgyH5U3lcov7Fybmy', '09678231311', 'Bacoor', 19, '2024-11-13 18:43:13', NULL),
(8, 'qpal', 'qpal@gmail.com', '$2y$10$ViYsMUi5rA2EUAroJYQJie7kBDzS0sX1iTZjkCUB3mpQSdfpYfp/q', '093182313163', 'General Trias', 23, '2024-11-13 19:08:50', NULL),
(9, 'javes', 'javes@gmail.com', '$2y$10$YKLjjvC/bwsk1hbZSZYJXe6K4bBtKcaahHonxZD6J8qi/rj1aWpHG', '0912831731', 'Dasmariñas', 19, '2024-11-14 10:53:53', NULL),
(10, 'cj', 'cjsabolo@kld.edu.ph', '$2y$10$WmqtCcolmplmSbNPGc5XeukI1BGHoEZ0Anjq34QN2HRNSmf8a9KP.', '123123123', 'Tagaytay', 34, '2024-11-14 10:55:44', NULL),
(13, 'akosi', 'akosi@gmail.com', '$2y$10$gCkdg543e2UnKJWA3UznxeS3F95L6MGkWaLBGfLmhB1FM8yzyLK02', '0192381384', 'Outside Cavite', 23, '2024-11-14 13:46:41', NULL),
(14, 'baliw', 'baliw@gmail.com', '$2y$10$dfRuMSBZDRpL9i95WCd9j.NhYO5USm/7LfQDfE1Za1QVlex2JlSQ2', '123131313', 'Dasmariñas', 23, '2024-11-14 13:47:32', NULL),
(15, 'badang ', 'badang@gmail.com', '$2y$10$ykK0rShCe08Dzr5Vtp6WpOSaTS.MjN0qbqfXgebjBs35FOC2OMOd.', '12313123', 'General Trias', 23, '2024-11-14 14:30:34', NULL),
(16, 'dikoalam', 'dikoalam@gmail.com', '$2y$10$BogsHlOlupobx.moBDGF6uR521ar9tSD3HzSeOC8Ek9PssaF.b7AG', '092131213', 'Trece Martires', 23, '2024-11-14 15:21:13', NULL),
(17, 'tangina', 'tangina@gmail.com', '$2y$10$Qa8jxSqFyvA0UgXxRnVFS.ArhYIZAWMOPpERdtMhjwsKsPLAsgl2C', '123123131', 'Trece Martires', 23, '2024-11-14 15:28:40', NULL),
(18, 'inamo', 'inamo@gmail.com', '$2y$10$69mBrqLEGXUlFIY4U94QxOSj6JqDGEoncziYH0CQ6TASTm9RjpEcu', '12321312312', 'Imus', 23, '2024-11-14 15:30:50', NULL),
(19, 'sigelang', 'sige@asd', '$2y$10$w9n4aMFxCDYJrLXmEt9s.OZaj2d5KZsOrLPJAT8C15DDaB7HBoKfK', '21312313', 'Bacoor', 23, '2024-11-14 15:32:13', NULL),
(20, 'akosi', 'akosi@gaga', '$2y$10$W70AasTfwsLXLupg0GknVOznCI/HnY91FOvQ7Qk3l9likU3m8sm3a', '12313123', 'Tagaytay', 23, '2024-11-14 15:37:12', NULL),
(21, 'anoba', 'anoba@wae', '$2y$10$vFBOVJytxqP/XyVLhMfd7Oz8oMm32btade.WumCIuBSa8cU287ZOu', '12321', 'General Trias', 23, '2024-11-14 15:44:04', NULL),
(22, 'ok', 'oklang@gmail.com', '$2y$10$PM1i/B.4KZ9UkRtfAy3U1.4HRsr0E5FC/xUJGO.ket4Cyh9LsseCK', '0192318', 'Dasmariñas', 12, '2024-11-14 15:50:29', NULL),
(23, 'jassy', 'jassy1@asda', '$2y$10$K7QErdVBk8H67YJxmhNo7OQ11rRpW7/c5O036buAOgKSvfxvb5x5W', '029312', 'Dasmariñas', 23, '2024-11-14 15:57:20', NULL);

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `update_user_count` AFTER INSERT ON `users` FOR EACH ROW BEGIN
    INSERT INTO city_demographics (city_name, user_count, is_cavite)
    VALUES (NEW.city, 1, IF(NEW.city IN ('Cavite City', 'Bacoor', 'Imus', 'Dasmariñas', 'Kawit', 'Noveleta', 'Rosario', 'General Trias', 'Tanza', 'Trece Martires', 'Silang', 'Tagaytay', 'Carmona', 'Maragondon', 'Ternate', 'Naic', 'Indang', 'Alfonso', 'General Emilio Aguinaldo', 'Mendez', 'Amadeo', 'Magallanes'), 1, 0))
    ON DUPLICATE KEY UPDATE user_count = user_count + 1;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Admin_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `fk_cart_user` (`user_id`),
  ADD KEY `fk_cart_product` (`product_id`);

--
-- Indexes for table `city_demographics`
--
ALTER TABLE `city_demographics`
  ADD PRIMARY KEY (`city_id`),
  ADD UNIQUE KEY `city_name` (`city_name`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`ORDER_ID`),
  ADD KEY `idx_orders_created_at` (`CREATED_AT`),
  ADD KEY `idx_orders_user_id` (`USER_ID`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ORDER_ID` (`ORDER_ID`),
  ADD KEY `PRODUCT_ID` (`PRODUCT_ID`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`PRODUCT_ID`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`TRANSACTION_ID`),
  ADD KEY `ORDER_ID` (`ORDER_ID`),
  ADD KEY `USER_ID` (`USER_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`USER_ID`),
  ADD UNIQUE KEY `EMAIL` (`EMAIL`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `city_demographics`
--
ALTER TABLE `city_demographics`
  MODIFY `city_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `ORDER_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `PRODUCT_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `TRANSACTION_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `USER_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`PRODUCT_ID`),
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`USER_ID`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`USER_ID`) REFERENCES `users` (`USER_ID`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`ORDER_ID`) REFERENCES `orders` (`ORDER_ID`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`PRODUCT_ID`) REFERENCES `products` (`PRODUCT_ID`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`ORDER_ID`) REFERENCES `orders` (`ORDER_ID`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`USER_ID`) REFERENCES `users` (`USER_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
