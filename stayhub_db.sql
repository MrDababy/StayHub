-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 22, 2026 at 11:29 AM
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
-- Database: `stayhub_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `beds`
--

CREATE TABLE `beds` (
  `bed_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `bunk_number` int(11) NOT NULL,
  `bed_position` varchar(50) NOT NULL,
  `status` enum('Available','Occupied') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `beds`
--

INSERT INTO `beds` (`bed_id`, `room_id`, `bunk_number`, `bed_position`, `status`) VALUES
(3, 2, 1, 'right', 'Occupied'),
(4, 2, 2, 'left', 'Available'),
(5, 3, 1, 'right', 'Available'),
(6, 3, 2, 'left', 'Available'),
(7, 4, 1, 'rightlower', 'Available'),
(8, 4, 2, 'rightupper', 'Available'),
(9, 4, 3, 'leftlower', 'Occupied'),
(10, 4, 4, 'leftupper', 'Available'),
(11, 5, 1, 'rightlower', 'Available'),
(12, 5, 2, 'rightupper', 'Available'),
(13, 5, 3, 'leftlower', 'Available'),
(14, 5, 4, 'leftupper', 'Available'),
(15, 6, 1, 'right', 'Occupied'),
(16, 6, 2, 'left', 'Available'),
(17, 7, 1, 'right', 'Available'),
(18, 7, 2, 'left', 'Available'),
(19, 8, 1, 'rightlower', 'Available'),
(20, 8, 2, 'rightupper', 'Available'),
(21, 8, 3, 'leftlower', 'Available'),
(22, 8, 4, 'leftupper', 'Available'),
(23, 9, 1, 'rightlower', 'Available'),
(24, 9, 2, 'rightupper', 'Available'),
(25, 9, 3, 'leftlower', 'Available'),
(26, 9, 4, 'leftupper', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `blocks`
--

CREATE TABLE `blocks` (
  `block_id` int(11) NOT NULL,
  `block_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blocks`
--

INSERT INTO `blocks` (`block_id`, `block_name`) VALUES
(1, 'Block A'),
(2, 'Block B');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bed_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `status` enum('Pending','Confirmed','Cancelled') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `bed_id`, `booking_date`, `status`) VALUES
(1, 4, 1, '2026-07-21', 'Confirmed'),
(2, 3, 2, '2026-07-21', 'Confirmed'),
(3, 3, 2, '2026-07-21', 'Confirmed'),
(4, 6, 9, '2026-07-21', 'Confirmed'),
(5, 7, 9, '2026-07-21', 'Confirmed'),
(6, 8, 15, '2026-07-21', 'Confirmed'),
(7, 9, 3, '2026-07-22', 'Confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` enum('Paid','Pending') NOT NULL,
  `payment_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_id`, `amount`, `payment_method`, `payment_status`, `payment_date`) VALUES
(1, 2, 1200.00, 'card', 'Paid', '2026-07-21'),
(2, 3, 1200.00, 'card', 'Paid', '2026-07-21'),
(3, 4, 400000.00, 'card', 'Paid', '2026-07-21'),
(4, 5, 400000.00, 'card', 'Paid', '2026-07-21'),
(5, 6, 600000.00, 'card', 'Paid', '2026-07-21'),
(6, 7, 600000.00, 'halopesa', 'Paid', '2026-07-22');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `block_id` int(11) NOT NULL,
  `room_number` varchar(20) NOT NULL,
  `floor` int(11) NOT NULL,
  `room_type` enum('Double','Quadro') NOT NULL,
  `capacity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('Available','Full') NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `block_id`, `room_number`, `floor`, `room_type`, `capacity`, `price`, `status`, `image`) VALUES
(2, 1, 'F1-2a', 1, 'Double', 2, 600000.00, 'Available', 'images/roomf12a.jpg'),
(3, 1, 'F2-2a', 2, 'Double', 2, 600000.00, 'Available', 'images/roomf22a.jpg'),
(4, 1, 'F1-4a', 1, 'Quadro', 4, 400000.00, 'Available', 'images/roomf14a.jpg'),
(5, 1, 'F2-4a', 2, 'Quadro', 4, 400000.00, 'Available', 'images/roomf24a.jpg'),
(6, 2, 'F1-2a', 1, 'Double', 2, 600000.00, 'Available', 'images/roomf12a.jpg'),
(7, 2, 'F2-2a', 2, 'Double', 2, 600000.00, 'Available', 'images/roomf22a.jpg'),
(8, 2, 'F1-4a', 1, 'Quadro', 4, 400000.00, 'Available', 'images/roomf14a.jpg'),
(9, 2, 'F2-4a', 2, 'Quadro', 4, 600000.00, 'Available', 'images/roomf24a.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `gender` enum('male','female') DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `phone`, `gender`, `password`, `role`, `created_at`) VALUES
(1, 'Admin User', 'admin@stayhub.com', '0000000000', NULL, 'admin123', 'admin', '2026-07-20 22:09:36'),
(2, 'Test User', 'testuser@example.com', '0712345678', NULL, '$2y$10$bQ952R27DjKIVDG8ARahyeJ6bkL3m5vVeKSer5pEGai/DT5pmsP.S', 'student', '2026-07-20 22:26:14'),
(6, 'Madina salim', 'madina@gmail.com', '0679627870', 'female', '$2y$10$qNkLYVkx1oa1cABeJnhGl.MKyzDoArGJiBUZqi3hdbnVXMxXPQoSG', 'student', '2026-07-21 12:32:48'),
(7, 'edith stephan', 'edith@gmail.com', '1234567890', 'female', '$2y$10$g1pWSrkF0MOEbZ4H9oTCIOnc/0eiW53IEyzmGtjp2Z9TqDnPkyNZC', 'student', '2026-07-21 12:47:35'),
(8, 'tup tup', 'tuptup@gmail.com', '123456790', 'male', '$2y$10$4uSdF5IFiDDH.Xf4M44pF.eA44in7H9U6nV46Zmv53WIJRWBBrLja', 'student', '2026-07-21 14:11:31'),
(9, 'khairah tuku', 'khairahtuku@gmail.com', '1234589789', 'female', '$2y$10$Bh8uVtrUIwVCg9Yh620juO/TEO9Jo1CHhKdu.YKfivk1TNlrjSkAy', 'student', '2026-07-22 08:49:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `beds`
--
ALTER TABLE `beds`
  ADD PRIMARY KEY (`bed_id`);

--
-- Indexes for table `blocks`
--
ALTER TABLE `blocks`
  ADD PRIMARY KEY (`block_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `beds`
--
ALTER TABLE `beds`
  MODIFY `bed_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `blocks`
--
ALTER TABLE `blocks`
  MODIFY `block_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
