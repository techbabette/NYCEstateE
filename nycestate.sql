-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 30, 2023 at 06:34 AM
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
-- Table structure for table `activationlinks`
--

CREATE TABLE `activationlinks` (
  `activation_link_id` int(20) NOT NULL,
  `activation_link` varchar(255) NOT NULL,
  `user_id` int(20) NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(13, 5, 'Test answer one', '2023-05-09 04:47:50'),
(14, 5, 'Test answer two', '2023-04-24 11:16:00'),
(15, 5, 'Test answer three', '2023-04-24 11:12:48'),
(16, 5, 'Test answer four', '2023-04-24 11:15:44'),
(17, 5, 'Test answer five', '2023-04-24 11:15:44'),
(18, 5, 'Test answer three', '2023-05-09 04:43:47'),
(19, 6, 'I like it a lot', NULL),
(20, 6, 'I dislike it', NULL),
(21, 7, 'Yes I do', NULL),
(22, 7, 'No I don\'t', NULL),
(23, 8, 'I think it\'s great', NULL),
(24, 8, 'I think it\'s not that great', NULL),
(25, 5, 'Test answer four', '2023-05-09 04:47:50'),
(26, 5, 'I like it a lot', '2023-05-09 04:50:22'),
(27, 5, 'I do not like it a lot', '2023-05-09 04:50:22'),
(28, 5, 'I think it is great', '2023-05-09 04:50:22'),
(29, 5, 'This is an answer', '2023-05-09 04:54:21'),
(30, 5, 'This is a second answer', '2023-05-09 04:54:21'),
(31, 5, 'I think it is great', NULL),
(32, 5, 'I think it is not that great', NULL),
(33, 5, 'I think it is not that good', NULL);

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
(24, 4, 21),
(36, 4, 22),
(35, 4, 23),
(31, 12, 23),
(30, 12, 24),
(25, 15, 22),
(26, 16, 23);

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
  `link_title` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `href` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `landing` tinyint(1) NOT NULL,
  `location` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `priority` int(2) NOT NULL DEFAULT 1,
  `parent_id` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `links`
--

INSERT INTO `links` (`link_id`, `access_level_id`, `link_title`, `href`, `landing`, `location`, `priority`, `parent_id`) VALUES
(1, 1, 'Nyc Estates', 'index.html', 0, 'head', 1, NULL),
(3, 1, 'Home', 'index.html', 0, 'navbar', 99, NULL),
(4, 1, 'Listings', 'listings.html', 0, 'navbar', 97, NULL),
(5, 4, 'Login', 'login.html', 0, 'navbar', 92, NULL),
(6, 4, 'Register', 'register.html', 0, 'navbar', 93, NULL),
(7, 2, 'Favorites', 'favorites.html', 0, 'navbar', 96, NULL),
(8, 3, 'Admins', 'admin.html', 0, 'navbar', 98, NULL),
(9, 1, 'Facebook', 'https://www.facebook.com/', 0, 'footer', 1, NULL),
(10, 1, 'Twitter', 'https://www.twitter.com/', 0, 'footer', 1, NULL),
(11, 1, 'Documentation', 'documentation.pdf', 1, 'footer', 99, NULL),
(12, 1, 'Sitemap', 'sitemap.xml', 1, 'footer', 1, NULL),
(24, 2, 'Contact', 'contact.html', 0, 'navbar', 92, NULL),
(25, 2, 'Survey', 'survey.html', 0, 'navbar', 95, NULL),
(26, 1, 'Listing', 'listing.html', 0, 'hidden', 1, NULL),
(27, 1, 'Author', 'author.html', 0, 'navbar', 94, NULL),
(29, 1, 'Controller', 'index.php', 1, 'hidden', 1, NULL);

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
(21, 21, 1, '168303037913018028666451016b21d29.jpg', '2023-05-02 12:26:19'),
(22, 22, 1, '168303053151299789664510203742e9.jpg', '2023-05-02 12:28:51'),
(23, 21, 1, '1683119543147456126664525db7c42d3.jpg', '2023-05-03 13:12:23'),
(24, 22, 1, '1683119564125156160764525dcc0b80b.jpg', '2023-05-03 13:12:44'),
(25, 23, 1, '1683119973102208334764525f6578044.jpg', '2023-05-03 13:19:33'),
(26, 24, 1, '16832865134364778466454e9f157801.jpg', '2023-05-05 11:35:13'),
(27, 24, 1, '168328653810963444276454ea0aac6df.jpg', '2023-05-05 11:35:38'),
(28, 22, 1, '168524706621347766486472d45a44ae2.jpg', '2023-05-28 04:11:06'),
(29, 23, 1, '168524718212122345376472d4ced2c30.jpg', '2023-05-28 04:13:02'),
(30, 21, 1, '16852472133880683266472d4ed20095.jpg', '2023-05-28 04:13:34'),
(31, 23, 1, '16852540557974377316472efa7d8215.jpg', '2023-05-28 06:07:36'),
(32, 23, 1, '16852542567237493676472f070a802e.jpg', '2023-05-28 06:10:56'),
(33, 23, 1, '168525429919764614236472f09bb7470.jpg', '2023-05-28 06:11:40'),
(34, 23, 1, '168525448717950470196472f1571c3c8.jpg', '2023-05-28 06:14:47'),
(35, 23, 1, '168525455013689531106472f196f2a44.jpg', '2023-05-28 06:15:51'),
(36, 23, 1, '16852546162791009076472f1d85b682.jpg', '2023-05-28 06:16:56'),
(37, 23, 1, '16852549291426124996472f311bdd2e.jpg', '2023-05-28 06:22:09'),
(38, 23, 1, '16852550247865130006472f37053ed9.jpg', '2023-05-28 06:23:44'),
(39, 23, 1, '168525507911365573766472f3a7bb46c.jpg', '2023-05-28 06:24:39'),
(40, 23, 1, '168525546016641923056472f52411d64.jpg', '2023-05-28 06:31:00'),
(41, 23, 1, '16852557484367665516472f6440e3e6.jpg', '2023-05-28 06:35:48'),
(42, 23, 1, '16852557747257220686472f65ea8de3.jpg', '2023-05-28 06:36:15'),
(43, 23, 1, '16852579744550082586472fef69ea29.jpg', '2023-05-28 07:12:55'),
(44, 23, 1, '168525811310972790666472ff816d301.jpg', '2023-05-28 07:15:13'),
(45, 23, 1, '1685258436962211683647300c436c56.jpg', '2023-05-28 07:20:36'),
(46, 23, 1, '16852584661080325625647300e292ab2.jpg', '2023-05-28 07:21:07'),
(47, 23, 1, '1685258595157084763364730163ea70a.jpg', '2023-05-28 07:23:16'),
(48, 23, 1, '16852586931788572757647301c5d24cb.jpg', '2023-05-28 07:24:53'),
(49, 23, 1, '16852587401137575631647301f44d892.jpg', '2023-05-28 07:25:40'),
(50, 23, 1, '1685259063202396240164730337a7ede.jpg', '2023-05-28 07:31:03'),
(51, 23, 1, '1685259157181268673264730395d7438.jpg', '2023-05-28 07:32:38'),
(52, 23, 1, '16852593253375781616473043d724c4.jpg', '2023-05-28 07:35:25'),
(53, 23, 1, '16852594711330203212647304cf42bed.jpg', '2023-05-28 07:37:51'),
(54, 23, 1, '16852595031810726348647304ef40c91.jpg', '2023-05-28 07:38:23'),
(55, 23, 1, '168525967614652192316473059cbc81f.jpg', '2023-05-28 07:41:16'),
(56, 23, 1, '16852597702031741141647305fa79947.jpg', '2023-05-28 07:42:51'),
(57, 23, 1, '16852598375957122336473063d207ff.jpg', '2023-05-28 07:43:57'),
(58, 23, 1, '168525993120889287976473069be646e.jpg', '2023-05-28 07:45:32'),
(59, 23, 1, '16852599328856353526473069c8e95f.jpg', '2023-05-28 07:45:33'),
(60, 23, 1, '16852599711677465146647306c3f07f5.jpg', '2023-05-28 07:46:12'),
(61, 23, 1, '168526010814242732476473074c7ad7d.jpg', '2023-05-28 07:48:29'),
(62, 23, 1, '16852601921908983353647307a064978.jpg', '2023-05-28 07:49:52'),
(63, 23, 1, '1685260201982126290647307a9d8752.jpg', '2023-05-28 07:50:02'),
(64, 23, 1, '1685260642126058193564730962c8489.jpg', '2023-05-28 07:57:22'),
(65, 23, 1, '168526077426998597647309e66a7d7.jpg', '2023-05-28 07:59:34'),
(66, 23, 1, '1685263062363722783647312d6d79b3.jpg', '2023-05-28 08:37:42'),
(67, 23, 1, '16852631346045117486473131e2b3e1.jpg', '2023-05-28 08:38:54'),
(68, 23, 1, '168526316521423004326473133d5fe72.jpg', '2023-05-28 08:39:25'),
(69, 23, 1, '1685263295452514076647313bf0ab5f.jpg', '2023-05-28 08:41:35'),
(70, 23, 1, '168526365913256893816473152bec871.jpg', '2023-05-28 08:47:40'),
(71, 23, 1, '16852637114727884016473155f1cc32.jpg', '2023-05-28 08:48:31'),
(72, 23, 1, '1685263793933152553647315b15d9ff.jpg', '2023-05-28 08:49:53'),
(73, 23, 1, '1685263923154579665064731633ab23b.jpg', '2023-05-28 08:52:03'),
(74, 23, 1, '168526394614307653376473164a893be.jpg', '2023-05-28 08:52:26'),
(75, 23, 1, '1685264065719462152647316c1372e5.jpg', '2023-05-28 08:54:25'),
(76, 23, 1, '16852640752014419803647316cbd1ee8.jpg', '2023-05-28 08:54:35'),
(77, 23, 1, '1685264084151722645647316d410ac2.jpg', '2023-05-28 08:54:44'),
(78, 23, 1, '1685264249158781595264731779e9845.jpg', '2023-05-28 08:57:30'),
(79, 23, 1, '16852643781341239393647317fa2747d.png', '2023-05-28 08:59:38'),
(80, 23, 1, '168526439419855839026473180a8a1cf.jpg', '2023-05-28 08:59:54'),
(81, 22, 1, '168526442714511831186473182b8ff1e.jpg', '2023-05-28 09:00:28'),
(82, 21, 1, '16852644622960976946473184eeab6f.jpg', '2023-05-28 09:01:03'),
(83, 23, 1, '1685264635552583373647318fbd6a4f.png', '2023-05-28 09:03:56'),
(84, 23, 1, '168526464715783812676473190762008.jpg', '2023-05-28 09:04:07');

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
(29, 21, '40000.00', '2023-05-02 12:26:19'),
(30, 22, '1000000.00', '2023-05-02 12:28:51'),
(31, 23, '199999.00', '2023-05-03 13:19:33'),
(32, 23, '200000.00', '2023-05-03 13:20:05'),
(33, 24, '1000000.00', '2023-05-05 11:35:13');

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
(8, 21, 3, 1),
(9, 21, 2, 2),
(10, 21, 1, 1),
(11, 22, 2, 2),
(12, 22, 1, 2),
(13, 22, 3, 1),
(14, 23, 1, 1),
(15, 23, 2, 3),
(16, 23, 3, 2),
(17, 24, 3, 2),
(18, 24, 1, 1);

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
(21, 3, 3, 'First street listing', 'This is a great listing with a great description', 'First Street 20', 100.00, '2023-05-02 12:26:19', NULL),
(22, 1, 1, 'Broadway apartment', 'Broadway is famous for many things', 'Broadway 20', 500.00, '2023-05-02 12:28:51', NULL),
(23, 4, 2, 'Brinsmade Ave', 'This is a listing in The Bronx', 'Brinsmade avenue 193', 2000.00, '2023-05-03 13:19:33', NULL),
(24, 4, 3, 'Maximum three words', 'This is a great new listing', 'Greatest Street 22', 1500.00, '2023-05-05 11:35:13', '2023-05-29 12:17:49');

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
(6, 4, 4, 'Second message', 'This is the second question', '2023-04-27 14:56:16'),
(7, 4, 3, 'Issue I am having', 'I have an issue', '2023-05-02 11:54:30'),
(8, 15, 4, 'That\'s Not How Words Work', 'Yes very message very wow such text', '2023-05-02 19:13:39');

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
  `dateDeleted` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`question_id`, `question`, `dateDeleted`) VALUES
(5, 'How much do you like this website', '2023-05-13 09:53:16'),
(6, 'Do you like this website', '2023-05-13 05:19:15'),
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
(3, 4, 'Banned'),
(5, 4, 'Inactive');

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
(26, 4, 18),
(27, 15, 22),
(28, 15, 19),
(29, 15, 23),
(30, 15, 18),
(31, 16, 13),
(32, 16, 22),
(33, 16, 19),
(34, 16, 23),
(35, 12, 31);

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
  `role_id` int(20) NOT NULL DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `name`, `lastName`, `dateCreated`, `role_id`) VALUES
(4, 'marinakrsticrf@gmail.com', '$2y$10$uZvQ1Eu.TyUrZs/yAOuDkOE5wM/Gvg0suI7CEGD2kM0Q8U5V31Y4K', 'Marina', 'Krstic', '2023-03-16 10:25:32', 2),
(5, 'laznaemailadresa@ict.edu.rs', '$2y$10$1bkDlKj.jzuq7BIhacsdYe6rqKtGLxGD1Uc6c0mX8gJJFdROiw.Vy', 'Marko', 'Krstic', '2023-03-16 10:38:08', 3),
(12, 'standarduser@gmail.com', '$2y$10$dvJElE3Zf4nZfkZzi8j40uvaBMeA8deYOvCpQZsAnSjxIHRT.BkDq', 'Stand', 'User', '2023-05-02 13:15:40', 1),
(13, 'banneduser@gmail.com', '$2y$10$mLif7XPkOUZdhmwFviCTFuXqL9CEUNvG.6V1r7km3uQ4CipwouNHq', 'Banned', 'User', '2023-05-02 13:16:13', 1),
(14, 'testemail@gmail.com', '$2y$10$SY1zineMNb5LnROGKmPnGOfv1d.hOHY6awpA9J8P/w8Ysva5JjvrS', 'Test', 'Account', '2023-05-02 19:09:02', 1),
(15, 'testemail2@gmail.com', '$2y$10$vJqPRMCjCpnVtZI2E3iOL.r1k7tKdH9UZ4JhW0DIq0hU.bjRLsBqy', 'Test', 'Account', '2023-05-02 19:10:01', 1),
(16, 'noabibovski@gmail.com', '$2y$10$qmu3fD2Q/jPUPLybSVNgbOBIfEVpBcMsKCkVMkD53lx1O.aTT8.92', 'Noa', 'Bibovski', '2023-05-03 16:05:50', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accesslevels`
--
ALTER TABLE `accesslevels`
  ADD PRIMARY KEY (`access_level_id`);

--
-- Indexes for table `activationlinks`
--
ALTER TABLE `activationlinks`
  ADD PRIMARY KEY (`activation_link_id`),
  ADD KEY `user_id` (`user_id`);

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
  ADD UNIQUE KEY `favorites` (`user_id`,`listing_id`),
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
-- AUTO_INCREMENT for table `activationlinks`
--
ALTER TABLE `activationlinks`
  MODIFY `activation_link_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `answer_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `boroughs`
--
ALTER TABLE `boroughs`
  MODIFY `borough_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `buildingtypes`
--
ALTER TABLE `buildingtypes`
  MODIFY `building_type_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `favorite_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `linkicons`
--
ALTER TABLE `linkicons`
  MODIFY `link_icon_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `links`
--
ALTER TABLE `links`
  MODIFY `link_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `listingphotos`
--
ALTER TABLE `listingphotos`
  MODIFY `photo_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `listingprices`
--
ALTER TABLE `listingprices`
  MODIFY `price_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `listingrooms`
--
ALTER TABLE `listingrooms`
  MODIFY `listing_room_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `listings`
--
ALTER TABLE `listings`
  MODIFY `listing_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `role_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `roomtypes`
--
ALTER TABLE `roomtypes`
  MODIFY `room_type_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `useranswers`
--
ALTER TABLE `useranswers`
  MODIFY `useranswer_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activationlinks`
--
ALTER TABLE `activationlinks`
  ADD CONSTRAINT `activationlinks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `listingrooms_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`listing_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `listingrooms_roomDelete` FOREIGN KEY (`room_type_id`) REFERENCES `roomtypes` (`room_type_id`);

--
-- Constraints for table `listings`
--
ALTER TABLE `listings`
  ADD CONSTRAINT `listings_ibfk_1` FOREIGN KEY (`building_type_id`) REFERENCES `buildingtypes` (`building_type_id`),
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
