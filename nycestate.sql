-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 21, 2023 at 05:44 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nycestate`
--

-- --------------------------------------------------------

--
-- Table structure for table `accesslevels`
--

CREATE TABLE `accesslevels` (
  `access_level_id` int(20) NOT NULL,
  `level` int(10) NOT NULL,
  `level_title` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accesslevels`
--

INSERT INTO `accesslevels` (`access_level_id`, `level`, `level_title`) VALUES
(1, 1, 'Logged out'),
(2, 2, 'Logged in'),
(3, 3, 'Admin'),
(4, 0, 'Only logged out');

-- --------------------------------------------------------

--
-- Table structure for table `boroughs`
--

CREATE TABLE `boroughs` (
  `borough_id` int(20) NOT NULL,
  `borough_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `boroughs`
--

INSERT INTO `boroughs` (`borough_id`, `borough_name`) VALUES
(1, 'Manhattan'),
(2, 'Brooklyn'),
(3, 'Queens'),
(4, 'The Bronx'),
(5, 'Staten Island');

-- --------------------------------------------------------

--
-- Table structure for table `buildingtypes`
--

CREATE TABLE `buildingtypes` (
  `building_type_id` int(20) NOT NULL,
  `type_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buildingtypes`
--

INSERT INTO `buildingtypes` (`building_type_id`, `type_name`) VALUES
(1, 'Apartment'),
(2, 'Duplex');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `favorite_id` int(20) NOT NULL,
  `user_id` int(20) NOT NULL,
  `listing_id` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `linkicons`
--

CREATE TABLE `linkicons` (
  `link_icon_id` int(20) NOT NULL,
  `link_id` int(20) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `linkicons`
--

INSERT INTO `linkicons` (`link_icon_id`, `link_id`, `icon`, `active`, `dateCreated`) VALUES
(1, 9, 'icomoon-free:facebook', 1, '2023-04-08 03:58:37'),
(2, 10, 'la:twitter', 1, '2023-04-08 03:58:37'),
(3, 11, 'fa-file', 0, '2023-04-08 03:58:37'),
(4, 12, 'bx:sitemap', 0, '2023-04-08 03:58:37'),
(8, 12, 'bx:sitemap', 1, '2023-04-09 05:20:18'),
(9, 11, 'fa-file', 1, '2023-04-21 03:34:17');

-- --------------------------------------------------------

--
-- Table structure for table `links`
--

CREATE TABLE `links` (
  `link_id` int(20) NOT NULL,
  `access_level_id` int(20) NOT NULL,
  `link_title` varchar(50) NOT NULL,
  `href` varchar(50) DEFAULT NULL,
  `landing` tinyint(1) NOT NULL,
  `location` varchar(20) NOT NULL,
  `priority` int(2) NOT NULL DEFAULT 1,
  `parent_id` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `links`
--

INSERT INTO `links` (`link_id`, `access_level_id`, `link_title`, `href`, `landing`, `location`, `priority`, `parent_id`) VALUES
(1, 1, 'Nyc Estates', 'index.html', 1, 'head', 1, NULL),
(3, 1, 'Home', 'index.html', 1, 'navbar', 99, NULL),
(4, 1, 'Listings', 'listings.html', 0, 'navbar', 97, NULL),
(5, 4, 'Login', 'login.html', 0, 'navbar', 95, NULL),
(6, 4, 'Register', 'register.html', 0, 'navbar', 94, NULL),
(7, 2, 'Favorites', 'favorites.html', 0, 'navbar', 96, NULL),
(8, 3, 'Admins', 'admin.html', 0, 'navbar', 98, NULL),
(9, 1, 'Facebook', 'https://www.facebook.com/', 0, 'footer', 1, NULL),
(10, 1, 'Twitter', 'https://www.twitter.com/', 0, 'footer', 1, NULL),
(11, 1, 'Documentation', 'documentation.pdf', 1, 'footer', 99, NULL),
(12, 1, 'Sitemap', 'sitemap.xml', 1, 'footer', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `listingphotos`
--

CREATE TABLE `listingphotos` (
  `photo_id` int(20) NOT NULL,
  `listing_id` int(20) NOT NULL,
  `main` tinyint(1) NOT NULL,
  `path` varchar(200) NOT NULL,
  `dateUploaded` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `listingphotos`
--

INSERT INTO `listingphotos` (`photo_id`, `listing_id`, `main`, `path`, `dateUploaded`) VALUES
(3, 4, 1, '16818274541544568656643ea67eb94c0.png', '2023-04-18 14:17:34'),
(11, 12, 1, '1681897858933328828643fb982abdef.jpg', '2023-04-19 09:50:58'),
(15, 16, 1, '16818980812027868961643fba617d826.jpg', '2023-04-19 09:54:41');

-- --------------------------------------------------------

--
-- Table structure for table `listingprices`
--

CREATE TABLE `listingprices` (
  `price_id` int(20) NOT NULL,
  `listing_id` int(20) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `listingprices`
--

INSERT INTO `listingprices` (`price_id`, `listing_id`, `price`, `date`) VALUES
(3, 4, '2000.00', '2023-04-18 14:17:34'),
(11, 12, '1000.00', '2023-04-19 09:50:58'),
(15, 16, '1000.00', '2023-04-19 09:54:41');

-- --------------------------------------------------------

--
-- Table structure for table `listingrooms`
--

CREATE TABLE `listingrooms` (
  `listing_room_id` int(20) NOT NULL,
  `listing_id` int(20) NOT NULL,
  `room_type_id` int(20) NOT NULL,
  `numberOf` int(10) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `listingrooms`
--

INSERT INTO `listingrooms` (`listing_room_id`, `listing_id`, `room_type_id`, `numberOf`, `active`) VALUES
(1, 16, 2, 2, 1),
(2, 16, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `listings`
--

CREATE TABLE `listings` (
  `listing_id` int(20) NOT NULL,
  `user_id` int(20) NOT NULL,
  `borough_id` int(20) NOT NULL,
  `building_type_id` int(20) NOT NULL,
  `listing_name` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `address` varchar(200) NOT NULL,
  `size` float(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `listings`
--

INSERT INTO `listings` (`listing_id`, `user_id`, `borough_id`, `building_type_id`, `listing_name`, `description`, `address`, `size`) VALUES
(4, 0, 1, 1, 'New listing', 'New description', 'Kings street 25', 35.00),
(12, 4, 1, 1, 'Newer listing', 'This is the newest listing', 'King\'s street 20', 30.00),
(16, 4, 3, 1, 'Listing with rooms', 'This listing has rooms', 'King\'s street 50', 30.00);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(20) NOT NULL,
  `access_level_id` int(20) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `access_level_id`, `role_name`) VALUES
(1, 2, 'Standard'),
(2, 3, 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `roomtypes`
--

CREATE TABLE `roomtypes` (
  `room_type_id` int(20) NOT NULL,
  `room_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roomtypes`
--

INSERT INTO `roomtypes` (`room_type_id`, `room_name`) VALUES
(1, 'Livingroom'),
(2, 'Bedroom');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `role_id` int(20) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `name`, `lastName`, `dateCreated`, `role_id`) VALUES
(4, 'marinakrsticrf@gmail.com', 'ce16a80506a2e2c17ce18c18e26451da', 'Marina', 'Krstic', '2023-03-16 10:25:32', 2),
(5, 'laznaemailadresa@ict.edu.rs', 'd0df4b94b9bfeff699bd026858547f17', 'Marko', 'Krstic', '2023-03-16 10:38:08', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accesslevels`
--
ALTER TABLE `accesslevels`
  ADD PRIMARY KEY (`access_level_id`);

--
-- Indexes for table `boroughs`
--
ALTER TABLE `boroughs`
  ADD PRIMARY KEY (`borough_id`);

--
-- Indexes for table `buildingtypes`
--
ALTER TABLE `buildingtypes`
  ADD PRIMARY KEY (`building_type_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`favorite_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `listing_id` (`listing_id`);

--
-- Indexes for table `linkicons`
--
ALTER TABLE `linkicons`
  ADD PRIMARY KEY (`link_icon_id`),
  ADD KEY `link_id` (`link_id`);

--
-- Indexes for table `links`
--
ALTER TABLE `links`
  ADD PRIMARY KEY (`link_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `access_level_id` (`access_level_id`) USING BTREE;

--
-- Indexes for table `listingphotos`
--
ALTER TABLE `listingphotos`
  ADD PRIMARY KEY (`photo_id`),
  ADD UNIQUE KEY `listing_id` (`listing_id`);

--
-- Indexes for table `listingprices`
--
ALTER TABLE `listingprices`
  ADD PRIMARY KEY (`price_id`),
  ADD KEY `listing_id` (`listing_id`);

--
-- Indexes for table `listingrooms`
--
ALTER TABLE `listingrooms`
  ADD PRIMARY KEY (`listing_room_id`),
  ADD KEY `listing_id` (`listing_id`),
  ADD KEY `roomType_id` (`room_type_id`);

--
-- Indexes for table `listings`
--
ALTER TABLE `listings`
  ADD PRIMARY KEY (`listing_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `borough_id` (`borough_id`),
  ADD KEY `building_type_id` (`building_type_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD KEY `access_level_id` (`access_level_id`);

--
-- Indexes for table `roomtypes`
--
ALTER TABLE `roomtypes`
  ADD PRIMARY KEY (`room_type_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accesslevels`
--
ALTER TABLE `accesslevels`
  MODIFY `access_level_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `boroughs`
--
ALTER TABLE `boroughs`
  MODIFY `borough_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `buildingtypes`
--
ALTER TABLE `buildingtypes`
  MODIFY `building_type_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `favorite_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `linkicons`
--
ALTER TABLE `linkicons`
  MODIFY `link_icon_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `links`
--
ALTER TABLE `links`
  MODIFY `link_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `listingphotos`
--
ALTER TABLE `listingphotos`
  MODIFY `photo_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `listingprices`
--
ALTER TABLE `listingprices`
  MODIFY `price_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `listingrooms`
--
ALTER TABLE `listingrooms`
  MODIFY `listing_room_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `listings`
--
ALTER TABLE `listings`
  MODIFY `listing_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roomtypes`
--
ALTER TABLE `roomtypes`
  MODIFY `room_type_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
