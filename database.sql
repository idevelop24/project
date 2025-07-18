-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 18, 2025 at 05:37 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vanilla`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(64) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `email` varchar(96) NOT NULL,
  `telephone` varchar(32) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `token` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `ip` varchar(40) DEFAULT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_groups`
--

CREATE TABLE `admin_groups` (
  `group_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `permissions` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `log_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(128) NOT NULL,
  `data` text DEFAULT NULL,
  `ip` varchar(40) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_sessions`
--

CREATE TABLE `admin_sessions` (
  `session_id` varchar(32) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `ip` varchar(40) NOT NULL,
  `user_agent` text NOT NULL,
  `expires` datetime NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_to_group`
--

CREATE TABLE `admin_to_group` (
  `admin_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_posts`
--

CREATE TABLE `tbl_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `image` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `posts_categories_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modify_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_archive` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_posts`
--

INSERT INTO `tbl_posts` (`id`, `title`, `content`, `image`, `posts_categories_id`, `created_at`, `modify_at`, `is_archive`, `status`) VALUES
(85, 'printer hp mb15 a', 'printer hp mb15 a\r\nwireless\r\ncharge2', '94c97cd7fc99bdd5758035e3fd94203a_-_hp-m15a.jpg', 0, '2024-11-08 13:56:30', '2024-11-08 14:31:07', 0, 1),
(86, 'scaner Ds 5500 N', 'scaner Ds 5500 N\r\nwireless', 'eb6f9f3fb838c68b7e35f32edf18baec_-_scaner-ds-5500n.jpg', 0, '2024-11-08 14:43:19', '2024-11-08 14:43:19', 1, 2),
(87, 'scaner perfection v39', 'scaner-perfection-v39', '5ac1ea5e3ea7694eb82fc124020d1c08_-_scaner-perfection-v39.jpg', 0, '2024-11-28 22:30:59', '2024-11-28 22:30:59', 0, 2),
(95, 'scanerperfection-v19-1', 'scanerperfection-v19-1', '52114c24e6c32405ae21b51c78b5f52e_-_scanerperfection-v19-1.jpg', 0, '2024-12-07 19:25:28', '2024-12-07 19:25:28', 0, 1),
(96, 'printer hp 4103', 'printer hp 4103\r\nprinter hp 4103', '13d0abc877882b605de4bdf086103fb1_-_hpm4103.png', 0, '2024-12-07 19:26:12', '2024-12-07 19:26:12', 0, 2),
(97, 'printer hp 178 nw', '', '89a93d3cc49d8d76953ccc1c532196b8_-_hp178nw.jpg', 0, '2024-12-07 19:27:12', '2024-12-07 19:27:12', 0, 4),
(98, 'printer canon 6030w', '', '0d7aeca97ade4262a3dc103df1865473_-_canon-6030w.png', 0, '2024-12-07 19:27:57', '2024-12-07 19:27:57', 0, 3),
(99, 'printer hp m11 a', '', 'df3254400ffa4b453f07768144b68e49_-_er70s-6-2.4.jpg', 0, '2024-12-07 19:28:41', '2024-12-13 17:27:57', 0, 4),
(101, 'محصول جدید تستی', 'محصول جدید تستی\r\nمحصول جدید تستی', '99031306ed4bc00f549d66aa3438f466_-_iphone16.jpg', 0, '2024-12-13 17:29:10', '2024-12-14 14:32:32', 0, 2),
(102, 'electrode 1439', '', 'b402cab8ff05f510a0150724a5b06e99_-_e309l-16-2-5.jpg', 0, '2024-12-14 14:35:52', '2024-12-14 14:35:52', 0, 4),
(103, 'electrode 1600v', '', 'ef8f0af0cd6e444b5a02a2eead4858a6_-_1600-3-25.jpg', 0, '2024-12-14 14:36:49', '2024-12-14 14:36:49', 0, 2),
(104, 'electrode 6013', '', 'ce8a5c0bb80f96912f82142e1343472b_-_6013-2-5.jpg', 0, '2024-12-14 14:38:31', '2024-12-14 14:38:31', 0, 1),
(105, 'electrode 7018 1230', '', '83eaf72961ec20cbf437d2da14b186f1_-_7018-1230-2-5.jpg', 0, '2024-12-14 14:39:32', '2024-12-14 14:39:32', 0, 2),
(106, 'sim joosh sim', '', 'c6012ba04721127992ba83185c7f4f4c_-_er70s-6-2.4.jpg', 0, '2024-12-14 14:40:53', '2024-12-14 14:40:53', 0, 1),
(107, 'overseas-metal-marker-black', '', 'fe9c63348bf7c8aed0a4403e4d6e56d8_-_overseas-metal-marker-black.jpg', 0, '2024-12-15 07:07:25', '2024-12-15 07:07:25', 0, 1),
(108, 'overseas-metal-marker-blue', 'overseas-metal-marker-blue', 'bda897052bc949e49da6b53d195615c9_-_overseas-metal-marker-blue.jpg', 0, '2024-12-15 07:08:53', '2024-12-15 07:08:53', 0, 1),
(109, 'overseas-metal-marker-gold', 'overseas-metal-marker-gold', 'ef4d0ed55596501d4ecb104666e922f2_-_overseas-metal-marker-gold.jpg', 0, '2024-12-15 07:10:15', '2024-12-15 07:10:15', 0, 2),
(110, 'overseas-metal-marker-green', 'overseas-metal-marker-green', 'fcabb6bcb3a9bfb7decb5035cbc02b0f_-_overseas-metal-marker-green.jpg', 0, '2024-12-15 07:11:10', '2024-12-15 07:12:41', 0, 2),
(111, 'overseas-metal-marker-red', 'overseas-metal-marker-red', '6ebef0bbab3be733dcc1bd0a25013706_-_overseas-metal-marker-red.jpg', 0, '2024-12-15 07:19:00', '2024-12-15 07:19:00', 0, 3),
(112, 'overseas-metal-marker-silver', 'overseas-metal-marker-silver', 'feff589daabc7d8b2c4505806b8e2ae5_-_overseas-metal-marker-silver.jpg', 0, '2024-12-15 07:19:48', '2024-12-15 07:19:48', 0, 2),
(113, 'overseas-metal-marker-yellow', 'overseas-metal-marker-yellow', '21416fe254b4f5f006765d463a13e828_-_overseas-metal-marker-yellow.jpg', 0, '2024-12-15 07:20:43', '2024-12-15 07:20:43', 0, 1),
(114, 'overseas-metal-marker-white', 'overseas-metal-marker-white', 'eb915f1be4e6da5e7ea6cd5a88c9284f_-_overseas-metal-marker-white.jpg', 0, '2024-12-15 07:21:40', '2024-12-15 07:21:40', 0, 3),
(115, 'weldcraft-tungsten-blue-1-6', 'weldcraft-tungsten-blue-1-6', 'e9e25577f29dca2213cd1bb1a0ab44bc_-_weldcraft-tungsten-blue-1-6.jpg', 0, '2024-12-15 07:29:11', '2024-12-15 07:29:11', 0, 4),
(116, 'weldcraft-tungsten-blue-in-hand-1-6', 'weldcraft-tungsten-blue-in-hand-1-6', '046eae8c53fc0917a9b115992c67c774_-_weldcraft-tungsten-blue-in-hand-1-6.jpg', 0, '2024-12-15 07:29:47', '2024-12-15 07:29:47', 0, 2),
(117, 'weldcraft-tungsten-purple-2-4', 'weldcraft-tungsten-purple-2-4', '67f2bfe09457fe787faf4ddc3bc487bc_-_weldcraft-tungsten-purple-in-hand-2-4.jpg', 0, '2024-12-15 08:00:12', '2024-12-15 08:00:12', 0, 4),
(118, 'weldcraft-tungsten-gray-1-6', '', '00989df28c88b4d4fdecc1fa94763d8b_-_weldcraft-tungsten-gray-1-6.jpg', 0, '2024-12-15 10:02:51', '2024-12-15 10:02:51', 0, 4),
(119, 'weldcraft-tungsten-gray-in-hand-2-4', '', '70b3d80d3df6033b40dacf6cb7e12ab6_-_weldcraft-tungsten-gray-in-hand-2-4.jpg', 0, '2024-12-15 10:03:40', '2024-12-15 10:03:40', 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_posts_blocks`
--

CREATE TABLE `tbl_posts_blocks` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `sort` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbl_posts_blocks`
--

INSERT INTO `tbl_posts_blocks` (`id`, `name`, `sort`) VALUES
(1, 'Block 1', 1),
(2, 'Block 2', 2),
(3, 'Block 3', 3),
(4, 'Block 4 - (Sales)', 4),
(5, 'Block 5 - (Discounts)', 5),
(6, 'Block 6 - (Accountings)', 6);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_posts_categories`
--

CREATE TABLE `tbl_posts_categories` (
  `ID` int(11) NOT NULL,
  `parent` int(11) NOT NULL DEFAULT 1,
  `name` text NOT NULL,
  `sort` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbl_posts_categories`
--

INSERT INTO `tbl_posts_categories` (`ID`, `parent`, `name`, `sort`, `status`) VALUES
(1, -1, 'Root', -100, 1),
(2, 1, 'Laptop', 1, 1),
(3, 1, 'Tablet', 2, 1),
(4, 2, 'Laptop Hp', 1, 1),
(5, 2, 'Laptop Asus', 2, 1),
(6, 2, 'Laptop Lenevo', 3, 1),
(7, 2, 'Laptop Asus', 4, 1),
(8, 4, 'Laptop Hp Gaming', 1, 1),
(9, 4, 'Laptop Hp Student', 2, 1),
(10, 4, 'Laptop Hp Design', 3, 1),
(17, 3, 'Tablet Hp', 3, 1),
(18, 3, 'Tablet Asus', 2, 1),
(19, 3, 'Tablet Sony', 1, 1),
(20, 17, 'Tablet Hp White', 1, 1),
(21, 17, 'Tablet Hp Black', 2, 1),
(24, 1, 'A', -1, 1),
(25, 1, 'B', -1, 1),
(26, 1, 'C', -1, 1),
(27, 1, 'Machines', -1, 1),
(28, 6, 'Laptop Lenevo Gaming', 3, 1),
(29, 27, 'Printer', 1, 1),
(30, 27, 'Scaner', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_posts_status`
--

CREATE TABLE `tbl_posts_status` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `sort` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbl_posts_status`
--

INSERT INTO `tbl_posts_status` (`id`, `name`, `sort`) VALUES
(1, 'Active', 1),
(2, 'Review', 2),
(3, 'Disable', 2),
(4, 'In seo', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_posts_to_blocks`
--

CREATE TABLE `tbl_posts_to_blocks` (
  `id` int(11) NOT NULL,
  `posts_id` int(11) NOT NULL,
  `post_blocks_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbl_posts_to_blocks`
--

INSERT INTO `tbl_posts_to_blocks` (`id`, `posts_id`, `post_blocks_id`) VALUES
(193, 85, 1),
(194, 85, 2),
(195, 85, 3),
(196, 85, 6),
(197, 86, 1),
(198, 86, 2),
(199, 86, 3),
(200, 86, 4),
(201, 87, 1),
(202, 87, 2),
(203, 87, 3),
(215, 95, 2),
(216, 96, 2),
(217, 96, 6),
(218, 97, 2),
(219, 97, 3),
(220, 97, 4),
(221, 97, 5),
(222, 98, 1),
(223, 98, 2),
(224, 98, 3),
(225, 98, 4),
(241, 99, 1),
(242, 99, 2),
(243, 99, 3),
(244, 99, 4),
(245, 99, 5),
(259, 101, 1),
(260, 101, 4),
(261, 101, 5),
(262, 102, 1),
(263, 103, 2),
(264, 104, 5),
(265, 104, 6),
(266, 105, 1),
(267, 106, 2),
(268, 106, 5),
(269, 107, 2),
(270, 108, 6),
(271, 109, 2),
(277, 110, 1),
(278, 110, 2),
(279, 110, 3),
(280, 110, 5),
(281, 119, 1),
(282, 119, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_posts_to_categories`
--

CREATE TABLE `tbl_posts_to_categories` (
  `id` int(11) NOT NULL,
  `posts_id` int(11) NOT NULL,
  `posts_categories_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbl_posts_to_categories`
--

INSERT INTO `tbl_posts_to_categories` (`id`, `posts_id`, `posts_categories_id`) VALUES
(191, 85, 27),
(192, 85, 29),
(193, 86, 27),
(194, 86, 30),
(195, 87, 27),
(196, 87, 30),
(219, 95, 27),
(220, 95, 30),
(221, 96, 27),
(222, 96, 29),
(223, 97, 27),
(224, 97, 29),
(225, 98, 27),
(226, 98, 29),
(238, 99, 27),
(239, 99, 29),
(240, 99, 30),
(252, 101, 27),
(253, 101, 29),
(254, 101, 30),
(255, 101, 26),
(256, 101, 25),
(257, 102, 26),
(258, 103, 26),
(259, 103, 25),
(260, 104, 26),
(261, 104, 25),
(262, 105, 27),
(263, 105, 29),
(264, 106, 26),
(265, 106, 25),
(266, 107, 26),
(267, 108, 26),
(268, 109, 26),
(271, 110, 26),
(272, 111, 26),
(273, 112, 26),
(274, 113, 26),
(275, 114, 26),
(276, 115, 26),
(277, 116, 26),
(278, 117, 26),
(279, 118, 26),
(280, 119, 26),
(281, 120, 26),
(282, 120, 25),
(283, 121, 26),
(284, 122, 26),
(285, 122, 25);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `admin_groups`
--
ALTER TABLE `admin_groups`
  ADD PRIMARY KEY (`group_id`);

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `token` (`token`);

--
-- Indexes for table `admin_to_group`
--
ALTER TABLE `admin_to_group`
  ADD PRIMARY KEY (`admin_id`,`group_id`);

--
-- Indexes for table `tbl_posts`
--
ALTER TABLE `tbl_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_posts_blocks`
--
ALTER TABLE `tbl_posts_blocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sort` (`sort`);

--
-- Indexes for table `tbl_posts_categories`
--
ALTER TABLE `tbl_posts_categories`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tbl_posts_status`
--
ALTER TABLE `tbl_posts_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sort` (`sort`);

--
-- Indexes for table `tbl_posts_to_blocks`
--
ALTER TABLE `tbl_posts_to_blocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sort` (`post_blocks_id`),
  ADD KEY `post` (`posts_id`);

--
-- Indexes for table `tbl_posts_to_categories`
--
ALTER TABLE `tbl_posts_to_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sort` (`posts_categories_id`),
  ADD KEY `post` (`posts_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_groups`
--
ALTER TABLE `admin_groups`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_posts`
--
ALTER TABLE `tbl_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT for table `tbl_posts_blocks`
--
ALTER TABLE `tbl_posts_blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_posts_categories`
--
ALTER TABLE `tbl_posts_categories`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `tbl_posts_status`
--
ALTER TABLE `tbl_posts_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_posts_to_blocks`
--
ALTER TABLE `tbl_posts_to_blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=283;

--
-- AUTO_INCREMENT for table `tbl_posts_to_categories`
--
ALTER TABLE `tbl_posts_to_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=286;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
