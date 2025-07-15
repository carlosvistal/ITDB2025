-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 08, 2025 at 06:40 PM
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
-- Database: portfolios
--

-- --------------------------------------------------------

--
-- Table structure for table portfolios
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `gender` varchar(10) NOT NULL DEFAULT 'Male',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table portfolio
--

INSERT INTO `portfolios` (`id`, `user_id`, `title`, `description`, `file_path`, `created_at`, `project_title`, `project_description`) VALUES
(2, 1, 'First Project', 'Description of first project', NULL, '2025-07-08 15:40:01', 'randolph', 'aadsadsa'),
(3, 1, 'Second Project', 'Description of second project', NULL, '2025-07-08 16:23:28', 'asdasd', 'sadasdsa');

-- --------------------------------------------------------

--
-- Table structure for table users
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `gender` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
--
-- Dumping data for table users
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `location`, `profile_picture`, `gender`) VALUES
(1, 'rbarbecho', '$2y$10$n6/Lx93ZRZrFX0CKMFqAjeZLVufF1WeaVkb.ec3ONwSGVeWX0qmhO', 'barbecho092@gmail.com', 'Randolph Barbecho', NULL, 'profile_686d348a338dd7.41526470.jpg', 'Male');

--
-- Indexes for dumped tables
--

--
-- Indexes for table portfolios
--
ALTER TABLE portfolios
  ADD PRIMARY KEY (id),
  ADD KEY user_id (user_id);

--
-- Indexes for table users
--
ALTER TABLE users
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY email (email);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table portfolios
--
ALTER TABLE `portfolios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table users
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table portfolios
--
ALTER TABLE portfolios
  ADD CONSTRAINT portfolios_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;