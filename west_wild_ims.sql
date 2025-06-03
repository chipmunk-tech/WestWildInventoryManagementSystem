-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2025 at 03:11 PM
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
-- Database: `west_wild_ims`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `description`, `created_at`) VALUES
(1, 1, 'logout', 'User logged out', '2025-06-03 07:14:39'),
(2, 1, 'login', 'User logged in', '2025-06-03 07:14:44'),
(3, 1, 'add_item', 'Added new item: Car Covers', '2025-06-03 09:37:39'),
(4, 1, 'add_item', 'Added new item: Kilimanjaro Water Small Cattons', '2025-06-03 09:41:28'),
(5, 1, 'add_item', 'Added new item: Kilimanjaro Water Small Cattons', '2025-06-03 09:44:50'),
(6, 1, 'edit_category', 'Updated category: Vehicle Accessories', '2025-06-03 09:49:00'),
(7, 1, 'edit_category', 'Updated category: Beverages', '2025-06-03 09:49:07'),
(8, 1, 'issue_item', 'Issued Kilimanjaro Water Small Cattons to Guide Living Temba', '2025-06-03 09:49:54'),
(9, 1, 'edit_item', 'Edited item: Car Covers', '2025-06-03 10:01:40'),
(10, 1, 'edit_item', 'Edited item: Car Covers', '2025-06-03 10:02:20'),
(11, 1, 'edit_item', 'Edited item: Car Covers', '2025-06-03 10:04:13'),
(12, 1, 'edit_item', 'Edited item: Car Covers', '2025-06-03 10:05:23'),
(13, 1, 'edit_item', 'Edited item: Car Covers', '2025-06-03 10:05:33'),
(14, 1, 'edit_item', 'Edited item: Car Covers', '2025-06-03 10:06:22'),
(15, 1, 'edit_item', 'Edited item: Car Covers', '2025-06-03 10:06:30'),
(16, 1, 'edit_item', 'Edited item: Car Covers', '2025-06-03 10:06:52'),
(17, 1, 'delete_item', 'Deleted item: Kilimanjaro Water Small Cattons (Consumable item)', '2025-06-03 10:13:50'),
(18, 1, 'edit_item', 'Edited item: Kilimanjaro Water Small Cattons', '2025-06-03 10:14:17'),
(19, 1, 'edit_item', 'Edited item: Kilimanjaro Water Small Cattons', '2025-06-03 10:14:29'),
(20, 1, 'add_category', 'Added new category: New', '2025-06-03 10:23:35'),
(21, 1, 'delete_item', 'Deleted item: ', '2025-06-03 10:25:32'),
(22, 1, 'logout', 'User logged out', '2025-06-03 10:25:58'),
(23, 1, 'login', 'User logged in', '2025-06-03 10:26:05'),
(24, 1, 'add_item', 'Added new item: Cocacola', '2025-06-03 10:41:42'),
(25, 1, 'add_item', 'Added new item: Cocacola', '2025-06-03 10:42:47'),
(26, 1, 'delete_item', 'Deleted item: Cocacola (Consumable item)', '2025-06-03 10:45:33'),
(27, 1, 'add_item', 'Added new item: Napkins', '2025-06-03 10:46:17'),
(28, 1, 'add_item', 'Added new item: Napkins', '2025-06-03 10:48:55'),
(29, 1, 'add_item', 'Added new item: Napkins', '2025-06-03 10:49:04'),
(30, 1, 'add_item', 'Added new item: Napkins', '2025-06-03 10:50:34'),
(31, 1, 'add_item', 'Added new item: Napkins', '2025-06-03 10:51:19'),
(32, 1, 'add_item', 'Added new item: Napkins', '2025-06-03 10:52:53'),
(33, 1, 'delete_item', 'Deleted item: Napkins', '2025-06-03 11:02:01'),
(34, 1, 'delete_item', 'Deleted item: Napkins', '2025-06-03 11:02:08'),
(35, 1, 'delete_item', 'Deleted item: Napkins', '2025-06-03 11:02:12'),
(36, 1, 'delete_item', 'Deleted item: Napkins', '2025-06-03 11:02:16'),
(37, 1, 'delete_item', 'Deleted item: Napkins', '2025-06-03 11:02:19'),
(38, 1, 'issue_item', 'Issued Car Covers to Guide Living Temba', '2025-06-03 11:03:10'),
(40, 1, 'login', 'User logged in', '2025-06-03 12:23:57'),
(41, 1, 'issue_item', 'Issued Cocacola to Guide Living Temba', '2025-06-03 12:24:29'),
(42, 1, 'delete_item', 'Deleted item: ', '2025-06-03 12:24:59'),
(43, 1, 'delete_item', 'Deleted item: Napkins', '2025-06-03 12:25:33'),
(44, 1, 'delete_item', 'Deleted item: Kilimanjaro Water Small Cattons (Consumable item)', '2025-06-03 12:25:37'),
(45, 1, 'delete_item', 'Deleted item: Cocacola (Consumable item)', '2025-06-03 12:25:40'),
(46, 1, 'return_item', 'Returned Car Covers from Guide Living Temba', '2025-06-03 12:25:52'),
(47, 1, 'delete_item', 'Deleted item: Car Covers', '2025-06-03 12:25:58'),
(48, 1, 'delete_item', 'Deleted item: ', '2025-06-03 12:26:07'),
(49, 1, 'login', 'User logged in', '2025-06-03 12:32:06'),
(50, 1, 'delete_item', 'Deleted item: ', '2025-06-03 12:32:15'),
(51, 1, 'add_item', 'Added new item: Car Covers', '2025-06-03 12:33:28'),
(52, 1, 'add_item', 'Added new item: Kilimanjaro Water Small Cattons', '2025-06-03 12:33:53'),
(53, 1, 'add_item', 'Added new item: Cocacola Soda', '2025-06-03 12:34:22'),
(54, 1, 'add_item', 'Added new item: Napkins', '2025-06-03 12:34:38'),
(55, 1, 'add_item', 'Added new item: Toilet Papers', '2025-06-03 12:35:04'),
(56, 1, 'add_item', 'Added new item: Milk Powder', '2025-06-03 12:35:32'),
(57, 1, 'add_item', 'Added new item: Milo Powder', '2025-06-03 12:36:02'),
(58, 1, 'add_item', 'Added new item: Red Wine', '2025-06-03 12:36:32'),
(59, 1, 'add_item', 'Added new item: Santa Lucia Spaghetti ', '2025-06-03 12:37:27'),
(60, 1, 'add_item', 'Added new item: Santa Lucia Pasta', '2025-06-03 12:37:48'),
(61, 1, 'add_item', 'Added new item: Best Biscuit', '2025-06-03 12:38:21'),
(62, 1, 'add_item', 'Added new item: Kilimanjaro Tea Bags - Small', '2025-06-03 12:38:53'),
(63, 1, 'add_item', 'Added new item: Kilimanjaro Tea Bags - Big', '2025-06-03 12:39:11'),
(64, 1, 'add_item', 'Added new item: Bar Soaps', '2025-06-03 12:39:53'),
(65, 1, 'add_item', 'Added new item: Ginger Snaps Biscuit', '2025-06-03 12:40:37'),
(66, 1, 'add_item', 'Added new item: Sugar', '2025-06-03 12:41:07'),
(67, 1, 'add_item', 'Added new item: White Cats', '2025-06-03 12:41:30'),
(68, 1, 'add_item', 'Added new item: Tomato Paste', '2025-06-03 12:41:55'),
(69, 1, 'add_item', 'Added new item: Soya Sauce', '2025-06-03 12:42:17'),
(70, 1, 'add_item', 'Added new item: Azam Juice', '2025-06-03 12:42:50'),
(71, 1, 'issue_item', 'Issued Cocacola Soda to Guide Living Temba', '2025-06-03 12:44:08'),
(72, 1, 'issue_item', 'Issued Kilimanjaro Water Small Cattons to Guide Living Temba', '2025-06-03 12:45:04'),
(73, 1, 'update_profile', 'Updated profile information', '2025-06-03 12:51:05'),
(74, 1, 'logout', 'User logged out', '2025-06-03 12:51:09'),
(75, 1, 'login', 'User logged in', '2025-06-03 12:51:13');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Beverages', 'All beverage items including beers, water, and soft drinks', '2025-06-03 06:57:32', '2025-06-03 06:57:32'),
(2, 'Groceries & Snacks', 'Food items and snacks', '2025-06-03 06:57:32', '2025-06-03 06:57:32'),
(3, 'Pasta & Cereals', 'Pasta, spaghetti, and cereal products', '2025-06-03 06:57:32', '2025-06-03 06:57:32'),
(4, 'Dairy Products', 'Milk and dairy related items', '2025-06-03 06:57:32', '2025-06-03 06:57:32'),
(5, 'Tea & Coffee', 'Tea bags and coffee products', '2025-06-03 06:57:32', '2025-06-03 06:57:32'),
(6, 'Toiletries', 'Personal care and hygiene products', '2025-06-03 06:57:32', '2025-06-03 06:57:32'),
(7, 'Camping & Gear', 'Camping equipment and gear', '2025-06-03 06:57:32', '2025-06-03 06:57:32'),
(8, 'Vehicle Accessories', 'Vehicle related accessories and parts', '2025-06-03 06:57:32', '2025-06-03 06:57:32'),
(9, 'Electronics', 'Electronic devices including routers, modems, and accessories', '2025-06-03 06:57:32', '2025-06-03 06:57:32'),
(10, 'New', 'New', '2025-06-03 10:23:35', '2025-06-03 10:23:35');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `unit` varchar(20) NOT NULL,
  `supplier` varchar(100) DEFAULT NULL,
  `min_stock_level` int(11) DEFAULT 5,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`, `category_id`, `quantity`, `unit`, `supplier`, `min_stock_level`, `created_at`, `updated_at`) VALUES
(12, 'Car Covers', 8, 8, 'pcs', 'West Wild Adventure', 4, '2025-06-03 12:33:28', '2025-06-03 12:33:28'),
(13, 'Kilimanjaro Water Small Cattons', 1, 4, 'packs', 'West Wild Adventure', 5, '2025-06-03 12:33:53', '2025-06-03 12:45:04'),
(14, 'Cocacola Soda', 1, 10, 'packs', 'West Wild Adventure', 5, '2025-06-03 12:34:22', '2025-06-03 12:44:08'),
(15, 'Napkins', 6, 9, 'packs', 'West Wild Adventure', 5, '2025-06-03 12:34:38', '2025-06-03 12:34:38'),
(16, 'Toilet Papers', 6, 19, 'packs', 'West Wild Adventure', 5, '2025-06-03 12:35:04', '2025-06-03 12:35:04'),
(17, 'Milk Powder', 4, 1, 'packs', 'West Wild Adventure', 2, '2025-06-03 12:35:32', '2025-06-03 12:35:32'),
(18, 'Milo Powder', 4, 1, 'packs', 'West Wild Adventure', 2, '2025-06-03 12:36:02', '2025-06-03 12:36:02'),
(19, 'Red Wine', 1, 3, 'packs', 'West Wild Adventure', 5, '2025-06-03 12:36:32', '2025-06-03 12:36:32'),
(20, 'Santa Lucia Spaghetti ', 3, 4, 'packs', 'West Wild Adventure', 5, '2025-06-03 12:37:27', '2025-06-03 12:37:27'),
(21, 'Santa Lucia Pasta', 3, 1, 'packs', 'West Wild Adventure', 5, '2025-06-03 12:37:48', '2025-06-03 12:37:48'),
(22, 'Best Biscuit', 2, 1, 'boxes', 'West Wild Adventure', 5, '2025-06-03 12:38:21', '2025-06-03 12:38:21'),
(23, 'Kilimanjaro Tea Bags - Small', 5, 8, 'packs', 'West Wild Adventure', 5, '2025-06-03 12:38:53', '2025-06-03 12:38:53'),
(24, 'Kilimanjaro Tea Bags - Big', 5, 9, 'packs', 'West Wild Adventure', 5, '2025-06-03 12:39:11', '2025-06-03 12:39:11'),
(25, 'Bar Soaps', 2, 23, 'pcs', 'West Wild Adventure', 5, '2025-06-03 12:39:53', '2025-06-03 12:39:53'),
(26, 'Ginger Snaps Biscuit', 2, 1, 'boxes', 'West Wild Adventure', 1, '2025-06-03 12:40:37', '2025-06-03 12:40:37'),
(27, 'Sugar', 2, 9, 'kg', 'West Wild Adventure', 5, '2025-06-03 12:41:07', '2025-06-03 12:41:07'),
(28, 'White Cats', 2, 4, 'packs', 'West Wild Adventure', 2, '2025-06-03 12:41:30', '2025-06-03 12:41:30'),
(29, 'Tomato Paste', 2, 2, 'packs', 'West Wild Adventure', 5, '2025-06-03 12:41:55', '2025-06-03 12:41:55'),
(30, 'Soya Sauce', 2, 4, 'packs', 'West Wild Adventure', 5, '2025-06-03 12:42:17', '2025-06-03 12:42:17'),
(31, 'Azam Juice', 2, 8, 'pcs', 'West Wild Adventure', 5, '2025-06-03 12:42:50', '2025-06-03 12:42:50');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_issues`
--

CREATE TABLE `stock_issues` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity_issued` int(11) NOT NULL,
  `issued_to` varchar(100) NOT NULL,
  `issued_by` int(11) NOT NULL,
  `issued_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expected_return_at` timestamp NULL DEFAULT NULL,
  `returned_at` timestamp NULL DEFAULT NULL,
  `return_notes` text DEFAULT NULL,
  `status` enum('issued','returned','overdue') DEFAULT 'issued',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_issues`
--

INSERT INTO `stock_issues` (`id`, `item_id`, `quantity_issued`, `issued_to`, `issued_by`, `issued_at`, `expected_return_at`, `returned_at`, `return_notes`, `status`, `notes`) VALUES
(5, 14, 2, 'Guide Living Temba', 1, '2025-06-03 12:44:08', '2025-06-03 12:43:00', NULL, NULL, 'issued', '6 Dozens of water and Coca'),
(6, 13, 6, 'Guide Living Temba', 1, '2025-06-03 12:45:04', '2025-06-03 12:44:00', NULL, NULL, 'issued', '6 cartons of water and coca');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `movement_type` enum('in','out','return') NOT NULL,
  `quantity` int(11) NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `reference_type` enum('issue','adjustment','initial','return') NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `item_id`, `movement_type`, `quantity`, `reference_id`, `reference_type`, `created_by`, `created_at`, `notes`) VALUES
(16, 12, 'in', 8, NULL, 'initial', 1, '2025-06-03 12:33:28', 'Initial stock entry'),
(17, 13, 'in', 10, NULL, 'initial', 1, '2025-06-03 12:33:53', 'Initial stock entry'),
(18, 14, 'in', 12, NULL, 'initial', 1, '2025-06-03 12:34:22', 'Initial stock entry'),
(19, 15, 'in', 9, NULL, 'initial', 1, '2025-06-03 12:34:38', 'Initial stock entry'),
(20, 16, 'in', 19, NULL, 'initial', 1, '2025-06-03 12:35:04', 'Initial stock entry'),
(21, 17, 'in', 1, NULL, 'initial', 1, '2025-06-03 12:35:32', 'Initial stock entry'),
(22, 18, 'in', 1, NULL, 'initial', 1, '2025-06-03 12:36:02', 'Initial stock entry'),
(23, 19, 'in', 3, NULL, 'initial', 1, '2025-06-03 12:36:32', 'Initial stock entry'),
(24, 20, 'in', 4, NULL, 'initial', 1, '2025-06-03 12:37:27', 'Initial stock entry'),
(25, 21, 'in', 1, NULL, 'initial', 1, '2025-06-03 12:37:48', 'Initial stock entry'),
(26, 22, 'in', 1, NULL, 'initial', 1, '2025-06-03 12:38:21', 'Initial stock entry'),
(27, 23, 'in', 8, NULL, 'initial', 1, '2025-06-03 12:38:53', 'Initial stock entry'),
(28, 24, 'in', 9, NULL, 'initial', 1, '2025-06-03 12:39:11', 'Initial stock entry'),
(29, 25, 'in', 23, NULL, 'initial', 1, '2025-06-03 12:39:53', 'Initial stock entry'),
(30, 26, 'in', 1, NULL, 'initial', 1, '2025-06-03 12:40:37', 'Initial stock entry'),
(31, 27, 'in', 9, NULL, 'initial', 1, '2025-06-03 12:41:07', 'Initial stock entry'),
(32, 28, 'in', 4, NULL, 'initial', 1, '2025-06-03 12:41:30', 'Initial stock entry'),
(33, 29, 'in', 2, NULL, 'initial', 1, '2025-06-03 12:41:55', 'Initial stock entry'),
(34, 30, 'in', 4, NULL, 'initial', 1, '2025-06-03 12:42:17', 'Initial stock entry'),
(35, 31, 'in', 8, NULL, 'initial', 1, '2025-06-03 12:42:50', 'Initial stock entry'),
(36, 14, 'out', 2, NULL, 'issue', 1, '2025-06-03 12:44:08', 'Issued to: Guide Living Temba'),
(37, 13, 'out', 6, NULL, 'issue', 1, '2025-06-03 12:45:04', 'Issued to: Guide Living Temba');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$oFEnl.nPVbCsdaASkbYSke53QzjkTpV3eltDMSsMFGnaMYY9CFFHG', 'System Administrator', 'admin@westwild.com', 'admin', '2025-06-03 06:57:32', '2025-06-03 12:51:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `stock_issues`
--
ALTER TABLE `stock_issues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `issued_by` (`issued_by`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `stock_issues`
--
ALTER TABLE `stock_issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `stock_issues`
--
ALTER TABLE `stock_issues`
  ADD CONSTRAINT `stock_issues_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `stock_issues_ibfk_2` FOREIGN KEY (`issued_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `stock_movements_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
