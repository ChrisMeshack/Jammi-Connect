-- phpMyAdmin SQL Dump
-- Database: `jamii_connect_db`

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jamii_connect_db`
--
CREATE DATABASE IF NOT EXISTS `jamii_connect_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `jamii_connect_db`;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `created_at`) VALUES
(1, 'Alice Smith', 'alice@example.com', '$2y$10$O9wR/E.n5A/w6hVw2B6P9.m6j1hQ5M4B9T6E1Q7D9E1Q7D9E1Q7D9', '2026-06-23 10:00:00'),
(2, 'Bob Jones', 'bob@example.com', '$2y$10$O9wR/E.n5A/w6hVw2B6P9.m6j1hQ5M4B9T6E1Q7D9E1Q7D9E1Q7D9', '2026-06-23 10:15:00'),
(3, 'Charlie Brown', 'charlie@example.com', '$2y$10$O9wR/E.n5A/w6hVw2B6P9.m6j1hQ5M4B9T6E1Q7D9E1Q7D9E1Q7D9', '2026-06-23 10:30:00'),
(4, 'Diana Prince', 'diana@example.com', '$2y$10$O9wR/E.n5A/w6hVw2B6P9.m6j1hQ5M4B9T6E1Q7D9E1Q7D9E1Q7D9', '2026-06-23 10:45:00'),
(5, 'Evan Wright', 'evan@example.com', '$2y$10$O9wR/E.n5A/w6hVw2B6P9.m6j1hQ5M4B9T6E1Q7D9E1Q7D9E1Q7D9', '2026-06-23 11:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_announcements_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `user_id`, `created_at`) VALUES
(1, 'Water Supply Interruption', 'There will be a water supply interruption in the central district on Friday.', 1, '2026-06-23 11:10:00'),
(2, 'New Library Hours', 'The county library will now be open until 8 PM on weekdays.', 2, '2026-06-23 11:20:00'),
(3, 'Road Maintenance Notice', 'Main street will be closed for repairs this weekend.', 1, '2026-06-23 11:30:00'),
(4, 'Health Clinic Updates', 'Free vaccination drive starts next Monday at the health clinic.', 3, '2026-06-23 11:40:00'),
(5, 'Town Hall Meeting', 'Join us for the monthly town hall meeting on Thursday.', 4, '2026-06-23 11:50:00');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `venue` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `venue`, `created_at`) VALUES
(1, 'Community Clean-up', 'Join us to clean the local park.', '2026-07-10', 'Central Park', '2026-06-23 12:00:00'),
(2, 'Farmers Market', 'Local farmers market with fresh produce.', '2026-07-12', 'Town Square', '2026-06-23 12:10:00'),
(3, 'Summer Festival', 'Annual summer festival with games and food.', '2026-08-05', 'Community Center', '2026-06-23 12:20:00'),
(4, 'Tech Workshop', 'Free workshop on basic coding skills.', '2026-07-15', 'Library IT Lab', '2026-06-23 12:30:00'),
(5, 'Art Exhibition', 'Showcase of local artists work.', '2026-07-20', 'County Art Gallery', '2026-06-23 12:40:00');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `schedule` varchar(200) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `schedule`, `location`, `contact`) VALUES
(1, 'Garbage Collection', 'Mon-Wed-Fri, 6am-12pm', 'All Residential Areas', '555-0101'),
(2, 'Library Access', 'Mon-Sat, 9am-8pm', 'Central Library', '555-0102'),
(3, 'Health Clinic', 'Mon-Fri, 8am-5pm', 'County Health Center', '555-0103'),
(4, 'Business Registration', 'Mon-Fri, 9am-4pm', 'County HQ, Desk 3', '555-0104'),
(5, 'Vehicle Licensing', 'Mon-Fri, 8am-3pm', 'Transport Office', '555-0105');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- --------------------------------------------------------
-- DEMONSTRATION QUERIES FOR TASK 4
-- --------------------------------------------------------

-- SELECT with WHERE, ORDER BY, JOIN
-- SELECT a.title, a.content, u.full_name FROM announcements a JOIN users u ON a.user_id = u.id WHERE a.created_at >= '2026-06-01 00:00:00' ORDER BY a.created_at DESC;

-- UPDATE with a WHERE clause
-- UPDATE events SET venue = 'Updated Venue' WHERE id = 1;

-- DELETE with a WHERE clause
-- DELETE FROM services WHERE id = 5;
