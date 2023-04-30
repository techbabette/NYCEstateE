-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2023 at 04:09 PM
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
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `answer_id` int(20) NOT NULL,
  `question_id` int(20) NOT NULL,
  `answer` varchar(255) NOT NULL,
  `dateDeleted` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`answer_id`, `question_id`, `answer`, `dateDeleted`) VALUES
(13, 5, 'Test answer one', NULL),
(14, 5, 'Test answer two', '2023-04-24 11:16:00'),
(15, 5, 'Test answer three', '2023-04-24 11:12:48'),
(16, 5, 'Test answer four', '2023-04-24 11:15:44'),
(17, 5, 'Test answer five', '2023-04-24 11:15:44'),
(18, 5, 'Test answer three', NULL),
(19, 6, 'I like it a lot', NULL),
(20, 6, 'I dislike it', NULL),
(21, 7, 'Yes I do', NULL),
(22, 7, 'No I don\'t', NULL),
(23, 8, 'I think it\'s great', NULL),
(24, 8, 'I think it\'s not that great', NULL);

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
(2, 'Duplex'),
(3, 'House');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `favorite_id` int(20) NOT NULL,
  `user_id` int(20) NOT NULL,
  `listing_id` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`favorite_id`, `user_id`, `listing_id`) VALUES
(1, 4, 18);

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
(1, 9, 'icomoon-free:facebook', 0, '2023-04-08 03:58:37'),
(2, 10, 'la:twitter', 1, '2023-04-08 03:58:37'),
(3, 11, 'fa-file', 0, '2023-04-08 03:58:37'),
(4, 12, 'bx:sitemap', 0, '2023-04-08 03:58:37'),
(8, 12, 'bx:sitemap', 1, '2023-04-09 05:20:18'),
(9, 11, 'fa-file', 0, '2023-04-21 03:34:17'),
(10, 9, 'icomoon-free:facebook', 0, '2023-04-23 13:25:45'),
(11, 11, 'fa-file', 1, '2023-04-24 03:56:17'),
(12, 9, 'icomoon-free:facebook', 0, '2023-04-24 08:34:50'),
(13, 9, 'icomoon-free:facebook', 0, '2023-04-24 08:34:50'),
(14, 9, 'icomoon-free:facebook', 0, '2023-04-24 08:37:48'),
(15, 9, 'icomoon-free:facebook', 0, '2023-04-24 08:37:48'),
(16, 9, 'icomoon-free:facebook', 0, '2023-04-24 08:38:54'),
(17, 9, 'icomoon-free:facebook', 0, '2023-04-24 08:38:54'),
(18, 9, 'icomoon-free:facebook', 0, '2023-04-24 08:40:27'),
(19, 9, 'icomoon-free:faceboo', 0, '2023-04-24 08:42:12'),
(20, 9, 'icomoon-free:facebook', 1, '2023-04-24 08:42:20');

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
) ;

--
-- Dumping data for table `links`
--

INSERT INTO `links` (`link_id`, `access_level_id`, `link_title`, `href`, `landing`, `location`, `priority`, `parent_id`) VALUES
(1, 1, 'Nyc Estates', 'index.html', 1, 'head', 1, NULL),
(3, 1, 'Home', 'index.html', 1, 'navbar', 99, NULL),
(4, 1, 'Listings', 'listings.html', 0, 'navbar', 97, NULL),
(5, 4, 'Login', 'login.html', 0, 'navbar', 93, NULL),
(6, 4, 'Register', 'register.html', 0, 'navbar', 94, NULL),
(7, 2, 'Favorites', 'favorites.html', 0, 'navbar', 96, NULL),
(8, 3, 'Admins', 'admin.html', 0, 'navbar', 98, NULL),
(9, 1, 'Facebook', 'https://www.facebook.com/', 0, 'footer', 1, NULL),
(10, 1, 'Twitter', 'https://www.twitter.com/', 0, 'footer', 1, NULL),
(11, 1, 'Documentation', 'documentation.pdf', 1, 'footer', 99, NULL),
(12, 1, 'Sitemap', 'sitemap.xml', 1, 'footer', 1, NULL),
(24, 2, 'Contact', 'contact.html', 0, 'navbar', 92, NULL),
(25, 2, 'Survey', 'survey.html', 0, 'navbar', 93, NULL),
(26, 1, 'Listing', 'listing.html', 0, 'hidden', 1, NULL);

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
(15, 16, 1, '16818980812027868961643fba617d826.jpg', '2023-04-19 09:54:41'),
(16, 17, 1, '168217066120666745526443e325515b3.jpg', '2023-04-22 13:37:41'),
(17, 18, 1, '168230851112256859946445fd9f54852.jpg', '2023-04-24 03:55:11'),
(19, 19, 1, '1682853534473423041644e4e9e83e59.jpg', '2023-04-30 11:18:54'),
(20, 20, 1, '1682853916284322225644e501c097bc.jpg', '2023-04-30 11:25:16');

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
(15, 16, '1000.00', '2023-04-19 09:54:41'),
(16, 17, '1000.00', '2023-04-22 13:37:41'),
(17, 18, '1000.00', '2023-04-24 03:55:11'),
(23, 18, '1025.00', '2023-04-24 04:24:53'),
(24, 18, '1500.00', '2023-04-24 04:28:30'),
(25, 18, '1700.00', '2023-04-24 04:28:39'),
(26, 12, '1500.00', '2023-04-24 04:29:58'),
(27, 19, '1000.00', '2023-04-30 11:18:54'),
(28, 20, '1000.00', '2023-04-30 11:25:16');

-- --------------------------------------------------------

--
-- Table structure for table `listingrooms`
--

CREATE TABLE `listingrooms` (
  `listing_room_id` int(20) NOT NULL,
  `listing_id` int(20) NOT NULL,
  `room_type_id` int(20) NOT NULL,
  `numberOf` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `listingrooms`
--

INSERT INTO `listingrooms` (`listing_room_id`, `listing_id`, `room_type_id`, `numberOf`) VALUES
(1, 16, 2, 2),
(2, 16, 1, 1),
(3, 18, 2, 2),
(5, 18, 3, 1),
(6, 19, 3, 4),
(7, 20, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `listings`
--

CREATE TABLE `listings` (
  `listing_id` int(20) NOT NULL,
  `borough_id` int(20) NOT NULL,
  `building_type_id` int(20) NOT NULL,
  `listing_name` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `address` varchar(200) NOT NULL,
  `size` float(8,2) NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `dateDeleted` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `listings`
--

INSERT INTO `listings` (`listing_id`, `borough_id`, `building_type_id`, `listing_name`, `description`, `address`, `size`, `dateCreated`, `dateDeleted`) VALUES
(4, 1, 1, 'New listing', 'New description', 'Kings street 25', 35.00, '2023-04-22 13:24:08', NULL),
(12, 1, 1, 'Newer listing', 'This is the newest listing', 'King\'s street 20', 30.00, '2023-04-22 13:24:08', NULL),
(16, 3, 1, 'Listing with rooms', 'This listing has rooms', 'King\'s street 50', 30.00, '2023-04-22 13:24:08', NULL),
(17, 3, 2, 'Listing delete', 'This will be deleted', 'King\'s street 20', 30.00, '2023-04-22 13:37:41', '2023-04-30 09:38:23'),
(18, 3, 3, 'Listing edited', 'Listing has been edited wow', 'King\'s street NN', 200.00, '2023-04-24 03:55:11', NULL),
(19, 5, 2, 'Test listing', 'This is a great description', 'Newest address 50', 30.00, '2023-04-30 11:18:54', '2023-04-30 11:24:49'),
(20, 5, 2, 'Newest listing', 'This is a great description', 'Newest address 50', 30.00, '2023-04-30 11:25:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(20) NOT NULL,
  `user_id` int(20) NOT NULL,
  `message_type_id` int(20) NOT NULL,
  `title` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `user_id`, `message_type_id`, `title`, `message`, `dateCreated`) VALUES
(5, 4, 4, 'This is the first message', 'This is the first message', '2023-04-27 14:55:35'),
(6, 4, 4, 'Second message', 'This is the second question', '2023-04-27 14:56:16');

-- --------------------------------------------------------

--
-- Table structure for table `messagetypes`
--

CREATE TABLE `messagetypes` (
  `message_type_id` int(20) NOT NULL,
  `message_type_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messagetypes`
--

INSERT INTO `messagetypes` (`message_type_id`, `message_type_name`) VALUES
(2, 'Error'),
(3, 'Inquiry'),
(4, 'General');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `question_id` int(20) NOT NULL,
  `question` varchar(255) NOT NULL,
  `dateDeleted` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`question_id`, `question`, `dateDeleted`) VALUES
(5, 'How much do you like this website', NULL),
(6, 'Do you like this website', NULL),
(7, 'Do you dislike this website', NULL),
(8, 'How do you feel about this website', NULL);

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
(2, 3, 'Admin'),
(3, 4, 'Banned');

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
(2, 'Bedroom'),
(3, 'Bathroom');

-- --------------------------------------------------------

--
-- Table structure for table `useranswers`
--

CREATE TABLE `useranswers` (
  `useranswer_id` int(20) NOT NULL,
  `user_id` int(20) NOT NULL,
  `answer_id` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `useranswers`
--

INSERT INTO `useranswers` (`useranswer_id`, `user_id`, `answer_id`) VALUES
(1, 4, 19),
(24, 4, 24),
(25, 4, 22),
(26, 4, 18);

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
(4, 'marinakrsticrf@gmail.com', '$2y$10$uZvQ1Eu.TyUrZs/yAOuDkOE5wM/Gvg0suI7CEGD2kM0Q8U5V31Y4K', 'Marina', 'Krstic', '2023-03-16 10:25:32', 2),
(5, 'laznaemailadresa@ict.edu.rs', '$2y$10$1bkDlKj.jzuq7BIhacsdYe6rqKtGLxGD1Uc6c0mX8gJJFdROiw.Vy', 'Marko', 'Krstic', '2023-03-16 10:38:08', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accesslevels`
--
ALTER TABLE `accesslevels`
  ADD PRIMARY KEY (`access_level_id`);

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `question_id` (`question_id`);

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
  ADD KEY `listing_id` (`listing_id`) USING BTREE;

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
  ADD KEY `borough_id` (`borough_id`),
  ADD KEY `building_type_id` (`building_type_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `message_type_id` (`message_type_id`);

--
-- Indexes for table `messagetypes`
--
ALTER TABLE `messagetypes`
  ADD PRIMARY KEY (`message_type_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`);

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
-- Indexes for table `useranswers`
--
ALTER TABLE `useranswers`
  ADD PRIMARY KEY (`useranswer_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `answer_id` (`answer_id`);

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
  MODIFY `access_level_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `answer_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `boroughs`
--
ALTER TABLE `boroughs`
  MODIFY `borough_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `buildingtypes`
--
ALTER TABLE `buildingtypes`
  MODIFY `building_type_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `favorite_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `linkicons`
--
ALTER TABLE `linkicons`
  MODIFY `link_icon_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `links`
--
ALTER TABLE `links`
  MODIFY `link_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `listingphotos`
--
ALTER TABLE `listingphotos`
  MODIFY `photo_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `listingprices`
--
ALTER TABLE `listingprices`
  MODIFY `price_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `listingrooms`
--
ALTER TABLE `listingrooms`
  MODIFY `listing_room_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `listings`
--
ALTER TABLE `listings`
  MODIFY `listing_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `messagetypes`
--
ALTER TABLE `messagetypes`
  MODIFY `message_type_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `roomtypes`
--
ALTER TABLE `roomtypes`
  MODIFY `room_type_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `useranswers`
--
ALTER TABLE `useranswers`
  MODIFY `useranswer_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`listing_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `linkicons`
--
ALTER TABLE `linkicons`
  ADD CONSTRAINT `linkicons_ibfk_1` FOREIGN KEY (`link_id`) REFERENCES `links` (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `links`
--
ALTER TABLE `links`
  ADD CONSTRAINT `links_ibfk_1` FOREIGN KEY (`access_level_id`) REFERENCES `accesslevels` (`access_level_id`);

--
-- Constraints for table `listingphotos`
--
ALTER TABLE `listingphotos`
  ADD CONSTRAINT `listingphotos_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`listing_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `listingprices`
--
ALTER TABLE `listingprices`
  ADD CONSTRAINT `listingprices_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`listing_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `listingrooms`
--
ALTER TABLE `listingrooms`
  ADD CONSTRAINT `listingrooms_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`listing_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `listings`
--
ALTER TABLE `listings`
  ADD CONSTRAINT `listings_ibfk_1` FOREIGN KEY (`building_type_id`) REFERENCES `buildingtypes` (`building_type_id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `listings_ibfk_2` FOREIGN KEY (`borough_id`) REFERENCES `boroughs` (`borough_id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`message_type_id`) REFERENCES `messagetypes` (`message_type_id`);

--
-- Constraints for table `roles`
--
ALTER TABLE `roles`
  ADD CONSTRAINT `roles_ibfk_1` FOREIGN KEY (`access_level_id`) REFERENCES `accesslevels` (`access_level_id`);

--
-- Constraints for table `roomtypes`
--
ALTER TABLE `roomtypes`
  ADD CONSTRAINT `roomtypes_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `listingrooms` (`room_type_id`);

--
-- Constraints for table `useranswers`
--
ALTER TABLE `useranswers`
  ADD CONSTRAINT `useranswers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `useranswers_ibfk_2` FOREIGN KEY (`answer_id`) REFERENCES `answers` (`answer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
