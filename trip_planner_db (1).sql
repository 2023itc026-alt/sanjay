-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 03, 2026 at 01:30 PM
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
-- Database: `trip_planner_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `custom_activities`
--

CREATE TABLE `custom_activities` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `package_name` varchar(255) DEFAULT NULL,
  `activity_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `explore_places`
--

CREATE TABLE `explore_places` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `cat` varchar(100) DEFAULT NULL,
  `target_destination` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `explore_places`
--

INSERT INTO `explore_places` (`id`, `name`, `cat`, `target_destination`, `description`, `image`, `price`) VALUES
(1, 'dubai mariana', 'Park', 'Dubai', 'a very good place to see', 'dubaimarina.jpg', 0.00),
(2, 'chennai', 'Park', 'Dubai', 'sunset is awesome to see from the beach', 'chennai.jpg', 50.00),
(3, 'meesuem', 'Museum', 'Paris', 'good', 'muuseum.jpg', 0.00),
(4, 'aquarium', 'Park', 'Dubai', 'full of fishes that you wont normally see', 'aquarium.webp', 75.00),
(5, 'eiffel tower', 'Museum', 'Paris', 'a romantic place', 'eiffel tower.jpg', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `guides`
--

CREATE TABLE `guides` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `license_no` varchar(50) DEFAULT NULL,
  `experience` varchar(50) DEFAULT NULL,
  `picture` varchar(255) DEFAULT 'default-guide.png',
  `certified_badge` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guides`
--

INSERT INTO `guides` (`id`, `vehicle_id`, `name`, `age`, `license_no`, `experience`, `picture`, `certified_badge`) VALUES
(1, 1, 'Kavin Kumar', 34, 'TN-67-2021-00456', '5 Years', 'kavin.jpg', 1),
(2, 2, 'Rahul Sharma', 29, 'TN-59-2023-00888', '4 Years', 'rahul.jpg', 1),
(3, 1, 'Samantha', 38, 'TN-59-2023-00888', '4 Years', 'samantha.jpg', 2);

-- --------------------------------------------------------

--
-- Table structure for table `trips`
--

CREATE TABLE `trips` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `travel_date` date NOT NULL,
  `departure_date` date DEFAULT NULL,
  `itinerary` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trips`
--

INSERT INTO `trips` (`id`, `user_email`, `destination`, `travel_date`, `departure_date`, `itinerary`, `created_at`) VALUES
(2, 'sanjayprasath297@gmail.com', 'Dubai', '2026-03-12', NULL, 'Grand Royal Hotel', '2026-03-03 13:00:59'),
(5, 'sanjayprasath297@gmail.com', 'Dubai', '2026-03-12', NULL, '', '2026-03-11 12:56:05'),
(22, 'kanna16@gmail.com', 'Tokyo', '2026-04-03', '2026-04-04', 'Shibuya crossing', '2026-04-03 09:11:23');

-- --------------------------------------------------------

--
-- Table structure for table `trip_packages`
--

CREATE TABLE `trip_packages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_packages`
--

INSERT INTO `trip_packages` (`id`, `name`, `duration`, `price`, `description`, `image`) VALUES
(1, 'trip to kashmir', '5 DAYS/4 NIGHTS', 1.15, 'jhf', 'kashmir.jpg'),
(2, 'Dubai Luxury Week', '7 Days / 6 Nights', 1200.00, 'Experience the ultimate luxury in Dubai. Includes: \n- 5-star hotel stay at Burj Al Arab \n- Desert Safari with VIP dinner \n- Private Yacht tour around Palm Jumeirah \n- Entrance to Museum of the Future \n- Daily breakfast and airport transfers.', 'dubai_pkg.jpg'),
(3, 'Paris Romance', '5 Days / 4 Nights', 950.00, 'A romantic getaway in the heart of France. Includes: \n- Boutique hotel with Eiffel Tower view \n- Seine River dinner cruise \n- Guided tour of The Louvre \n- Wine tasting in Montmartre \n- All city transport passes.', 'paris_pkg.jpg'),
(4, 'London Explorer', '6 Days / 5 Nights', 850.00, 'Discover the history of the United Kingdom. Includes: \n- Central London accommodation \n- London Eye & Madame Tussauds tickets \n- Day trip to Stonehenge and Windsor Castle \n- Traditional Afternoon Tea experience \n- Oyster card for all Tube travel.', 'london_pkg.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT 'default-avatar.png',
  `token_expiry` datetime DEFAULT NULL,
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `created_at`, `reset_token`, `profile_pic`, `token_expiry`, `otp_code`, `otp_expiry`, `is_admin`) VALUES
(1, 'kannan', 'kanna16@gmail.com', '$2y$10$1qNLGQqVJ6uBKpmbPsNCDugLNWW03AJUQuHepGx9E2O57rnlSwjA2', '2026-02-04 07:20:28', NULL, 'default-avatar.png', NULL, NULL, NULL, 0),
(2, 'sanjayprasath', 'sanjay123@gamil.com', '$2y$10$4YldObEkw0Gx9hvv.vfvTOw.3lJFFFs9jFGTapGlRpRLtbh/D9/cK', '2026-02-04 07:38:44', NULL, 'default-avatar.png', NULL, NULL, NULL, 0),
(3, 'sanjay', 'sanjayprasath297@gmail.com', '$2y$10$fciP5ZpT1iWT2/bPYMIMp.nxS7tARcgpAc6Ip6SjQTMIkKuMQFg2W', '2026-02-10 12:16:53', NULL, 'default-avatar.png', NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `driver_name` varchar(100) DEFAULT NULL,
  `car_model` varchar(100) DEFAULT NULL,
  `price_per_day` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `location` varchar(100) DEFAULT 'Dubai'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `driver_name`, `car_model`, `price_per_day`, `image`, `location`) VALUES
(1, 'sanjay', 'toyota', 5.00, 'toyota.jpg', 'Tokyo'),
(2, 'Suresh Kumar', 'Hyundai', 120.00, 'hyundai.avif', 'Dubai');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `custom_activities`
--
ALTER TABLE `custom_activities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `explore_places`
--
ALTER TABLE `explore_places`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guides`
--
ALTER TABLE `guides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trip_packages`
--
ALTER TABLE `trip_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `custom_activities`
--
ALTER TABLE `custom_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `explore_places`
--
ALTER TABLE `explore_places`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `guides`
--
ALTER TABLE `guides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `trip_packages`
--
ALTER TABLE `trip_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `guides`
--
ALTER TABLE `guides`
  ADD CONSTRAINT `guides_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
