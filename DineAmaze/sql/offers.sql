-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2025 at 08:32 AM
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
-- Database: `dineamaze_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `offer_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `badge` varchar(50) NOT NULL,
  `image` varchar(255) NOT NULL,
  `valid_until` date DEFAULT NULL,
  `is_ongoing` tinyint(1) DEFAULT 0,
  `section` varchar(50) NOT NULL,
  `how_to_take` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`offer_id`, `title`, `description`, `badge`, `image`, `valid_until`, `is_ongoing`, `section`, `how_to_take`, `created_at`, `updated_at`) VALUES
(1, 'Family Meal Deal', 'Order any 4 main courses and get 20% off your total bill. Perfect for family gatherings!', '20% OFF', 'images/offers/family-meal.jpg', '0000-00-00', 0, '0', 'Simply mention this offer to your server when dining with your family at DineAmaze.', '2025-05-24 06:13:00', '2025-05-24 06:13:00'),
(2, 'Free Dessert', 'Spend over Rs. 1500 on your meal and receive a complimentary dessert of your choice.', 'FREE', 'images/offers/free-dessert.jpg', '0000-00-00', 0, '0', 'Your server will automatically offer you a free dessert when your bill exceeds Rs. 1500.', '2025-05-24 06:13:00', '2025-05-24 06:13:00'),
(3, 'Happy Hour Special', 'Enjoy 15% off on all beverages between 4PM and 6PM, Monday and Thursday.', 'HAPPY HOUR', 'images/offers/happy-hour.png', NULL, 1, '0', 'Visit us during happy hours and the discount will be automatically applied to your beverage order.', '2025-05-24 06:13:00', '2025-05-24 06:13:00'),
(4, 'Buy 1 Get 1 Free Appetizers', 'Order any appetizer and get a second one of equal or lesser value for free.', 'BUY 1 GET 1', 'images/offers/buy-one-get-one.jpg', '0000-00-00', 0, '0', 'Mention this offer when ordering appetizers at our restaurant. Only available for dine-in customers.', '2025-05-24 06:13:00', '2025-05-24 06:13:00'),
(5, 'Weekday Lunch Special', 'Enjoy our executive lunch menu at a special price of Rs. 599 per person, Monday to Friday, 12PM to 3PM.', 'WEEKDAY', 'images/offers/weekday-lunch.jpg', NULL, 1, '0', 'Simply visit us during weekday lunch hours and ask for the executive lunch menu.', '2025-05-24 06:13:00', '2025-05-24 06:13:00'),
(6, 'Birthday Special', 'Celebrate your birthday with us and receive a complimentary cake and 10% off your entire bill.', 'BIRTHDAY', 'images/offers/birthday-special.jpg', NULL, 1, '0', 'Show your ID proving it\'s your birthday (±3 days) when dining at our restaurant.', '2025-05-24 06:13:00', '2025-05-24 06:13:00'),
(7, 'Anniversary Celebration', 'Celebrate your anniversary at DineAmaze and receive a complimentary bottle of wine or sparkling juice.', 'ANNIVERSARY', 'images/offers/anniversary.jpg', NULL, 1, '0', 'Inform us when making your reservation that you\'re celebrating your anniversary.', '2025-05-24 06:13:00', '2025-05-24 06:13:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`offer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `offer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
