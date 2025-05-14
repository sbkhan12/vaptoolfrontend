-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 14, 2025 at 11:23 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vapt_tool`
--

-- --------------------------------------------------------

--
-- Table structure for table `scan_history`
--

DROP TABLE IF EXISTS `scan_history`;
CREATE TABLE IF NOT EXISTS `scan_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `findings` text,
  `scan_date` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `critical` int DEFAULT '0',
  `high` int DEFAULT '0',
  `medium` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `scan_history`
--

INSERT INTO `scan_history` (`id`, `user_id`, `filename`, `status`, `findings`, `scan_date`, `created_at`, `critical`, `high`, `medium`) VALUES
(1, 1, 'testfile', 'Complete', '3 Critical, 2 High, 5 Medium', '2023-11-15 14:30:00', '2025-05-14 10:56:19', 0, 0, 0),
(2, 1, 'main.cpp', 'Complete', '3 Critical, 2 High, 5 Medium', '2023-11-15 14:30:00', '2025-05-14 10:58:02', 0, 0, 0),
(3, 1, 'utils.cpp', 'Complete', '1 Critical, 4 High, 2 Medium', '2023-11-14 09:15:00', '2025-05-14 10:58:02', 0, 0, 0),
(4, 1, 'network.cpp', 'Partial', '2 High, 3 Medium (Scan interrupted)', '2023-11-12 16:45:00', '2025-05-14 10:58:02', 0, 0, 0),
(5, 2, 'api.cpp', 'Complete', '0 Critical, 1 High, 3 Medium', '2023-11-10 11:25:00', '2025-05-14 10:58:02', 0, 0, 0),
(6, 2, 'auth.cpp', 'Failed', 'Scan failed due to network timeout', '2023-11-09 18:10:00', '2025-05-14 10:58:02', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `security_question` varchar(100) DEFAULT NULL,
  `security_answer` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `security_question`, `security_answer`) VALUES
(1, 'sc', 'as1@gmail.com', '$2y$10$cEmyitwF2jMUapREf2BONOU.8yYbTlXzP5UBvbeURgJmUYXYpKQAO', 'user', 'pet', 'sd'),
(2, 'sb', 'sb@gmail.com', '$2y$10$wCFRhYfRGFewzoqWc/pHhONYdTRnfI5e6ZiVHHMiOYY6X8UxA8k3y', 'user', 'pet', 'as'),
(3, 'admin', 'admin@gmail.com', '$2y$10$UESH9dqfxXA0yQgOsNZPae5YXK13uiVV4WYJOI6tAsRRmALZxhf3i', 'admin', 'school', 'admin'),
(4, 'sd', 'sd@gmail.com', '$2y$10$bcbtN0eSiCX2qvc1qZpX2ulVhq3aSfR.6efQ5BVF.y6JBpLtYrF2m', 'user', 'pet', 'sd'),
(5, 'dc@gmail.com', 'dc@gmail.com', '$2y$10$xz7zA9SAX9UXSLpJ2yLyyes95KbZ9EsTsN4/9.1zs5ZntgZOsPaYu', 'admin', 'pet', 'dc'),
(6, 'test', 'test@gmail.com', '$2y$10$OOz8Yzb7ybcyggASai0S3.s0Vsdt9fnZpDhIUOSB0e.4OwMh8Hz2S', 'user', 'pet', 'test'),
(7, 'user1', 'user1@gmail.com', '$2y$10$AfGUUIZ./IygUPoF87VZXO3aWA/5lXiBhLYDqhg7YsAG8sLsV2WLC', 'user', 'pet', 'user1'),
(8, 'as', 'as@gmail.com', '$2y$10$ktnvoeep0Jf3FTV/jFK/reJ38luawmmr5gYdGXEW8Lc0px1EJ1CVe', 'user', 'pet', 'as'),
(9, 'test1', 'test1@gmail.com', '$2y$10$npxf.MxVuvv6ZgJSAySEm.gjxUtTajPI6AwoBRLS2BmfsXuoXff1O', 'user', 'pet', 'test');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
