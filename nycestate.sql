-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 16, 2023 at 09:12 AM
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

-- --------------------------------------------------------

--
-- Table structure for table `buildingtypes`
--

CREATE TABLE `buildingtypes` (
  `building_type_id` int(20) NOT NULL,
  `type_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `links`
--

CREATE TABLE `links` (
  `link_id` int(20) NOT NULL,
  `access_level_id` int(20) NOT NULL,
  `link_title` varchar(50) NOT NULL,
  `href` varchar(50) DEFAULT NULL,
  `landing` tinyint(1) NOT NULL,
  `location` varchar(20) NOT NULL,
  `parent_id` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `links`
--

INSERT INTO `links` (`link_id`, `access_level_id`, `link_title`, `href`, `landing`, `location`, `parent_id`) VALUES
(1, 1, 'NYCEstate', 'index.html', 1, 'head', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `listingphotos`
--

CREATE TABLE `listingphotos` (
  `photo_id` int(20) NOT NULL,
  `listing_id` int(20) NOT NULL,
  `main` tinyint(1) NOT NULL,
  `path` varchar(200) NOT NULL,
  `user_id` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `listingprices`
--

CREATE TABLE `listingprices` (
  `price_id` int(20) NOT NULL,
  `listing_id` int(20) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `dateSet` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `listingrooms`
--

CREATE TABLE `listingrooms` (
  `listingRoom_id` int(20) NOT NULL,
  `listing_id` int(20) NOT NULL,
  `roomType_id` int(20) NOT NULL,
  `numberOf` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 2, 'standard'),
(2, 3, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `roomtypes`
--

CREATE TABLE `roomtypes` (
  `roomType_id` int(20) NOT NULL,
  `roonName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'ilija.krstic.155.21@ict.edu.rs', '459f6fe00d942096b0329947990bf4a7', 'Marko', 'Krstic', '2023-03-08 09:14:07', 2);

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
-- Indexes for table `links`
--
ALTER TABLE `links`
  ADD PRIMARY KEY (`link_id`),
  ADD UNIQUE KEY `access_level_id` (`access_level_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `listingphotos`
--
ALTER TABLE `listingphotos`
  ADD PRIMARY KEY (`photo_id`),
  ADD UNIQUE KEY `listing_id` (`listing_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

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
  ADD PRIMARY KEY (`listingRoom_id`),
  ADD KEY `listing_id` (`listing_id`),
  ADD KEY `roomType_id` (`roomType_id`);

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
  ADD PRIMARY KEY (`roomType_id`);

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
  MODIFY `borough_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `buildingtypes`
--
ALTER TABLE `buildingtypes`
  MODIFY `building_type_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `favorite_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `links`
--
ALTER TABLE `links`
  MODIFY `link_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `listingphotos`
--
ALTER TABLE `listingphotos`
  MODIFY `photo_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `listingprices`
--
ALTER TABLE `listingprices`
  MODIFY `price_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `listingrooms`
--
ALTER TABLE `listingrooms`
  MODIFY `listingRoom_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `listings`
--
ALTER TABLE `listings`
  MODIFY `listing_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roomtypes`
--
ALTER TABLE `roomtypes`
  MODIFY `roomType_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
