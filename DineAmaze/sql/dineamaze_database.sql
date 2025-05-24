-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2025 at 01:34 PM
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
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`, `name`, `email`, `created_at`, `profile_picture`, `phone`) VALUES
(1, 'admin', '$2y$10$LCt0GHe3lbmjthme.eir0ePUJltD4GMG4JrKSAd90VTK//ZQK3joa', 'Mr. Admin1', 'admin@dineamaze.com', '2025-04-05 16:37:25', '1_1747465749_images.jpg', '9819721210');

-- --------------------------------------------------------

--
-- Table structure for table `contact_message`
--

CREATE TABLE `contact_message` (
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `submission_date` datetime DEFAULT current_timestamp(),
  `is_read` enum('Yes','No') DEFAULT 'No',
  `admin_response` text DEFAULT NULL,
  `response_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_message`
--

INSERT INTO `contact_message` (`message_id`, `user_id`, `message`, `submission_date`, `is_read`, `admin_response`, `response_date`) VALUES
(1, 7, 'Hello I  Guess I miss my purse in your place.  if You found it then keep it in a safe place.', '2025-05-18 01:40:49', 'Yes', 'Ok ma\'am no worries . we will take care of that', '2025-05-17 21:56:50'),
(2, 7, 'Thank you for the kind words.', '2025-05-18 01:45:46', 'Yes', 'You  are  welcome 😊', '2025-05-17 22:01:15'),
(3, 8, 'Hello I am Samir and I  apologize for the late coming in your workspace and have to wait for my response.', '2025-05-18 01:56:27', 'Yes', 'it\'s ok sir. Don\'t have to be Guilty', '2025-05-17 22:12:32'),
(4, 8, 'helllo', '2025-05-18 10:35:47', 'Yes', 'hello sir', '2025-05-23 12:56:15');

-- --------------------------------------------------------

--
-- Table structure for table `id_documents`
--

CREATE TABLE `id_documents` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `document_path` varchar(255) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_group_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `id_documents`
--

INSERT INTO `id_documents` (`id`, `order_id`, `document_path`, `upload_date`, `order_group_id`) VALUES
(1, 2, 'uploads/1742577408_image(6).png', '2025-03-21 17:16:48', NULL),
(2, 3, 'uploads/1742579601_Daigram.png', '2025-03-21 17:53:21', NULL),
(3, 5, 'uploads/1742610833_image(6).png', '2025-03-22 02:33:53', NULL),
(4, 6, 'uploads/1742614675_camera_man.jpg', '2025-03-22 03:37:55', NULL),
(5, 7, 'uploads/1742615621_WBS.png', '2025-03-22 03:53:41', NULL),
(6, 2, 'uploads/1742622456_image(6).png', '2025-03-22 05:47:36', NULL),
(7, 3, 'uploads/1742622686_image(6).png', '2025-03-22 05:51:26', NULL),
(8, 4, 'uploads/1742622787_image(6).png', '2025-03-22 05:53:07', NULL),
(9, 5, 'uploads/1742622851_image(6).png', '2025-03-22 05:54:11', NULL),
(10, 6, 'uploads/1742623033_image(6).png', '2025-03-22 05:57:13', NULL),
(11, 7, 'uploads/1742623174_image(6).png', '2025-03-22 05:59:34', NULL),
(12, 8, 'uploads/1742623265_image(6).png', '2025-03-22 06:01:05', NULL),
(13, 9, 'uploads/1742625883_image(4).png', '2025-03-22 06:44:43', NULL),
(14, 10, 'uploads/1742626111_image(6).png', '2025-03-22 06:48:31', NULL),
(15, 11, 'uploads/1742626184_crosshair.png', '2025-03-22 06:49:44', NULL),
(16, 12, 'uploads/1742626306_Daigram.png', '2025-03-22 06:51:46', NULL),
(17, 13, 'uploads/1742626614_image(6).png', '2025-03-22 06:56:54', NULL),
(18, 14, 'uploads/1742626651_image(6).png', '2025-03-22 06:57:31', NULL),
(19, 15, 'uploads/1742627043_image(6).png', '2025-03-22 07:04:03', NULL),
(20, 16, 'uploads/1742627084_image(6).png', '2025-03-22 07:04:44', NULL),
(21, 17, 'uploads/1742627299_image(6).png', '2025-03-22 07:08:19', NULL),
(22, 18, 'uploads/1742627634_lenna_image.png', '2025-03-22 07:13:54', NULL),
(23, 19, 'uploads/1742627774_image(6).png', '2025-03-22 07:16:14', NULL),
(24, 0, 'uploads/1742628284_crosshair.png', '2025-03-22 07:24:44', NULL),
(25, 2, 'uploads/1742628888_mouse.png', '2025-03-22 07:34:48', NULL),
(26, 3, 'uploads/1742629208_mouse.png', '2025-03-22 07:40:08', NULL),
(27, 4, 'uploads/1742629246_Blank diagram(1).png', '2025-03-22 07:40:46', NULL),
(28, 5, 'uploads/1742629668_Qr Code Design Vector PNG Images, Code Icon Design Vector, Code Icons, Icon, Illustration PNG Image For Free Download.jpg', '2025-03-22 07:47:48', NULL),
(29, 6, 'uploads/1742630049_Daigram.png', '2025-03-22 07:54:09', NULL),
(30, 7, 'uploads/1742631429_Daigram.png', '2025-03-22 08:17:09', NULL),
(31, 10, 'uploads/1742632261_a turtle with red hoodies.png', '2025-03-22 08:31:01', NULL),
(32, 13, 'uploads/1742632375_FDD.png', '2025-03-22 08:32:55', NULL),
(33, 17, 'uploads/1742632760_image(6).png', '2025-03-22 08:39:20', NULL),
(34, 20, 'uploads/1742632851_image(6).png', '2025-03-22 08:40:51', NULL),
(35, 23, 'uploads/1742633023_image(6).png', '2025-03-22 08:43:43', NULL),
(36, 26, 'uploads/1742633217_lenna_image.png', '2025-03-22 08:46:57', NULL),
(37, 29, 'uploads/1742633292_lenna_image.png', '2025-03-22 08:48:12', NULL),
(38, 32, 'uploads/1742633489_lenna_image.png', '2025-03-22 08:51:29', NULL),
(39, 36, 'uploads/1742633779_image(6).png', '2025-03-22 08:56:19', NULL),
(40, 40, 'uploads/1742634516_image(6).png', '2025-03-22 09:08:36', NULL),
(41, 42, 'uploads/1742958052_camera_man.jpg', '2025-03-26 03:00:52', NULL),
(42, 43, 'uploads/1744217281_Buff Khaja set.jpg', '2025-04-09 16:48:01', NULL),
(43, 44, 'uploads/1744227088_frontImageFile.jpg', '2025-04-09 19:31:28', NULL),
(44, 45, 'uploads/1744227268_frontImageFile.jpg', '2025-04-09 19:34:28', NULL),
(45, 46, 'uploads/1744227657_frontImageFile.jpg', '2025-04-09 19:40:57', NULL),
(46, 47, 'uploads/1744227767_frontImageFile.jpg', '2025-04-09 19:42:47', NULL),
(47, 48, 'uploads/1744227920_frontImageFile.jpg', '2025-04-09 19:45:20', NULL),
(48, 49, 'uploads/1744228087_frontImageFile.jpg', '2025-04-09 19:48:07', NULL),
(49, 54, 'uploads/1744229079_frontImageFile.jpg', '2025-04-09 20:04:39', NULL),
(50, 55, 'uploads/1744229313_frontImageFile.jpg', '2025-04-09 20:08:33', NULL),
(51, 56, 'uploads/1744229457_frontImageFile.jpg', '2025-04-09 20:10:57', NULL),
(52, 57, 'uploads/1744229681_frontImageFile.jpg', '2025-04-09 20:14:41', NULL),
(53, 0, 'uploads/1744229847_frontImageFile.jpg', '2025-04-09 20:17:27', 'order_67f6d5d7ccb80'),
(54, 0, 'uploads/1744230135_frontImageFile.jpg', '2025-04-09 20:22:15', 'order_67f6d6f7832b9'),
(55, 0, 'uploads/1744230891_Veg Thali Set.jpg', '2025-04-09 20:34:51', 'order_67f6d9eb02e9c'),
(56, 0, 'uploads/1744231189_frontImageFile.jpg', '2025-04-09 20:39:49', 'order_67f6db1515b45'),
(57, 0, 'uploads/1744231558_frontImageFile.jpg', '2025-04-09 20:45:58', 'order_67f6dc8676806'),
(58, 0, 'uploads/1744232047_frontImageFile.jpg', '2025-04-09 20:54:07', 'order_67f6de6f474e0'),
(59, 0, 'uploads/1744260885_frontImageFile.jpg', '2025-04-10 04:54:45', 'order_67f74f15bbe69'),
(60, 0, 'uploads/1744269408_frontImageFile.jpg', '2025-04-10 07:16:48', 'order_67f77060b0889'),
(61, 0, 'uploads/1744824000_Buff Khaja set.jpg', '2025-04-16 17:20:01', 'order_67ffe6c0f412d'),
(62, 0, 'uploads/1744827482_frontImageFile.jpg', '2025-04-16 18:18:02', 'order_67fff45a82c11'),
(63, 0, 'uploads/1744876414_frontImageFile.jpg', '2025-04-17 07:53:34', 'order_6800b37e35f74'),
(64, 0, 'uploads/1744876519_frontImageFile.jpg', '2025-04-17 07:55:19', 'order_6800b3e78e110'),
(65, 0, 'uploads/1745127676_frontImageFile.jpg', '2025-04-20 05:41:16', 'order_680488fcba78f'),
(66, 0, 'uploads/1745128772_frontImageFile.jpg', '2025-04-20 05:59:32', 'order_68048d4486a41'),
(67, 0, 'uploads/1745129062_frontImageFile.jpg', '2025-04-20 06:04:22', 'order_68048e660ccae'),
(68, 0, 'uploads/1745129686_frontImageFile.jpg', '2025-04-20 06:14:46', 'order_680490d65340c'),
(69, 0, 'uploads/1745130232_frontImageFile.jpg', '2025-04-20 06:23:52', 'order_680492f8b8ad8'),
(70, 0, 'uploads/1745132719_frontImageFile.jpg', '2025-04-20 07:05:19', 'order_68049cafb5c7a'),
(71, 0, 'uploads/1745133023_frontImageFile.jpg', '2025-04-20 07:10:23', 'order_68049ddf19ff8'),
(72, 0, 'uploads/1745133684_frontImageFile.jpg', '2025-04-20 07:21:24', 'order_6804a0742f5b4'),
(73, 0, 'uploads/1745133775_frontImageFile.jpg', '2025-04-20 07:22:55', 'order_6804a0cf2749a'),
(74, 0, 'uploads/1745134171_frontImageFile.jpg', '2025-04-20 07:29:31', 'order_6804a25b1ba10'),
(75, 0, 'uploads/1745134239_frontImageFile.jpg', '2025-04-20 07:30:39', 'order_6804a29f2091a'),
(76, 0, 'uploads/1745134362_frontImageFile.jpg', '2025-04-20 07:32:42', 'order_6804a31ad3584'),
(77, 0, 'uploads/1745134377_frontImageFile.jpg', '2025-04-20 07:32:57', 'order_6804a3298ed49'),
(78, 0, 'uploads/1745134447_frontImageFile.jpg', '2025-04-20 07:34:07', 'order_6804a36fcba45'),
(79, 0, 'uploads/1745134815_frontImageFile.jpg', '2025-04-20 07:40:15', 'order_6804a4df00b0f'),
(80, 0, 'uploads/1746114700_frontImageFile.jpg', '2025-05-01 15:51:40', 'order_6813988c53aca'),
(81, 0, 'uploads/1746114947_frontImageFile.jpg', '2025-05-01 15:55:47', 'order_6813998390b5a'),
(82, 0, 'uploads/1746115114_frontImageFile.jpg', '2025-05-01 15:58:34', 'order_68139a2a25ff1'),
(83, 0, 'uploads/1746370102_frontImageFile.jpg', '2025-05-04 14:48:22', 'order_68177e364546c'),
(84, 0, 'uploads/1747507303_profile.jpg', '2025-05-17 18:41:43', 'order_6828d86748c99'),
(85, 0, 'uploads/1747513481_frontImageFile.jpg', '2025-05-17 20:24:41', 'order_6828f089c947e'),
(86, 0, 'uploads/1747514139_frontImageFile.jpg', '2025-05-17 20:35:39', 'order_6828f31b43700'),
(87, 0, 'uploads/1747981857_kati roll.jpeg', '2025-05-23 06:30:57', 'order_68301621ea8fd');

-- --------------------------------------------------------

--
-- Table structure for table `menu_category`
--

CREATE TABLE `menu_category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_category`
--

INSERT INTO `menu_category` (`category_id`, `category_name`) VALUES
(1, 'Traditional Nepali Meals and Platter'),
(2, 'Street Food and Quick Bites'),
(3, 'Pizza, Burgers, and Snacks'),
(4, 'Cozy Bowls and Noodle Delights'),
(5, 'Desserts'),
(6, 'Beverages');

-- --------------------------------------------------------

--
-- Table structure for table `menu_item`
--

CREATE TABLE `menu_item` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `image_name` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `ingredients` text DEFAULT NULL,
  `is_customizable` tinyint(1) DEFAULT 0,
  `price` decimal(10,2) DEFAULT NULL,
  `offer_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_item`
--

INSERT INTO `menu_item` (`item_id`, `item_name`, `image_name`, `category_id`, `ingredients`, `is_customizable`, `price`, `offer_price`) VALUES
(1, 'Buff Khaja Set', 'Traditional Nepali Meals and Platter/67f613b032d9a.jpg', 1, 'Sukuti (dry meat), chewra, saag, achar, curd, papad', 0, 200.00, NULL),
(2, 'Newari Khaja Set', 'Traditional Nepali Meals and Platter/67f6129b41476.jpg', 1, 'Beaten Rice, Fried Egg, Buffalo Meat, Soybeans, Pickle', 0, 270.00, 320.00),
(3, 'Chicken-Momo', 'Street Food and Quick Bites/67f76a0839f37.jpg', 2, 'Flour Dough, Chicken, Onion, Garlic, Spices', 0, 180.00, 150.00),
(5, 'Cheese Pizza', 'Pizza, Burgers, and Snacks/67f766f1c1882.jpg', 3, 'Pizza Base, Cheese, Tomato Sauce, Herbs', 1, 450.00, 400.00),
(6, 'Chicken Burger', 'Pizza, Burgers, and Snacks/67f767783c963.jpg', 3, 'Burger Bun, Chicken Patty, Lettuce, Tomato, Cheese', 1, 300.00, NULL),
(7, 'Thukpa', 'Cozy Bowls and Noodle Delights/67f76c81cb207.jpg', 4, 'Noodles, Vegetables, Chicken, Spices', 0, 220.00, 200.00),
(8, 'Veg-Chowmein', 'Cozy Bowls and Noodle Delights/67f768952163a.png', 4, 'Noodles, Cabbage, Carrot, Onion, Soy Sauce', 1, 200.00, NULL),
(9, 'Chocolate Cake', 'Desserts/67f767fdb6852.jpg', 5, 'Flour, Cocoa, Sugar, Eggs, Butter', 0, 150.00, 130.00),
(10, 'Lassi', 'Beverages/67f769c0c8ede.jpg', 6, 'Yogurt, Sugar, Cardamom, Ice', 0, 80.00, NULL),
(13, 'Sel Roti', 'Traditional Nepali Meals and Platter/67f608b465967.jpeg', 1, 'Rice Flour, Sugar, Yogurt, Ghee, Cardamom', 0, 100.00, NULL),
(15, 'Veg Thali Set', 'Traditional Nepali Meals and Platter/67f61219236ed.jpg', 1, 'Rice, dal, mixed veg curry, achar, saag, curd, papad.', 0, 200.00, NULL),
(16, 'Pakoda', 'Street Food and Quick Bites/67f76a90a04e4.jpg', 2, 'Chickpea Flour, Potatoes, Onion, Spinach, Cumin, Coriander, Turmeric', 0, 80.00, NULL),
(20, 'Pizza Roll', 'Street Food and Quick Bites/67f76b878e118.jpg', 2, 'Pizza Dough, Cheese, Tomato Sauce, Chicken (or Veggies), Herbs', 1, 150.00, NULL),
(21, 'Pepperoni Pizza', 'Pizza, Burgers, and Snacks/67f76aebe906a.jpg', 3, 'Pizza Dough, Tomato Sauce, Mozzarella Cheese, Pepperoni', 1, 550.00, 500.00),
(22, 'Veg-Burger', 'Pizza, Burgers, and Snacks/67f76cdcd2352.jpg', 3, 'Burger Bun, Veggie Patty, Lettuce, Tomato, Onion, Pickles, Sauce', 1, 320.00, NULL),
(23, 'French Fries', 'Pizza, Burgers, and Snacks/67f768c24e165.jpg', 3, 'Potatoes, Salt, Cooking Oil', 0, 150.00, NULL),
(24, 'Chicken Nuggets', 'Pizza, Burgers, and Snacks/67f767a47cf26.jpg', 3, 'Chicken Breast, Breadcrumbs, Spices, Cooking Oil', 0, 280.00, 250.00),
(25, 'Mushroom Pizza', 'Pizza, Burgers, and Snacks/67f76a520efd6.jpg', 3, 'Pizza Dough, Tomato Sauce, Mozzarella Cheese, Mushrooms', 1, 520.00, NULL),
(26, 'Ramen', 'Cozy Bowls and Noodle Delights/67f76bb548d4c.jpg', 4, 'Noodles, Broth, Sliced Meat (Pork/Chicken), Egg, Seaweed, Green Onions', 1, 380.00, NULL),
(27, 'Pho', 'Cozy Bowls and Noodle Delights/67f76b3ec5321.jpg', 4, 'Rice Noodles, Aromatic Broth, Sliced Beef, Herbs, Bean Sprouts, Lime', 0, 350.00, 320.00),
(28, 'Laksa', 'Cozy Bowls and Noodle Delights/67f769974be57.jpg', 4, 'Rice Noodles, Spicy Coconut Broth, Shrimp, Tofu, Fish Balls, Bean Sprouts,Egg', 1, 420.00, NULL),
(29, 'Vanilla Ice Cream', 'Desserts/67f76ca9a5cfd.jpg', 5, 'Milk, Cream, Sugar, Vanilla Extract', 0, 120.00, NULL),
(30, 'Gulab Jamun', 'Desserts/67f76942551ab.jpg', 5, 'Milk Solids, Flour, Sugar Syrup, Cardamom', 0, 180.00, 160.00),
(31, 'Fruit Salad', 'Desserts/67f769147f6de.jpg', 5, 'Assorted Seasonal Fruits (e.g., Watermelon, Banana, Apple, Grapes)', 0, 150.00, NULL),
(32, 'Cheesecake', 'Desserts/67f7671edb329.jpg', 5, 'Cream Cheese, Graham Cracker Crust, Sugar, Eggs, Vanilla', 0, 250.00, 220.00),
(40, 'Cheese pasta', 'Street Food and Quick Bites/67f766960ff90.jpg', 2, 'Pasta, cheese, milk, butter, flour, salt, pepper.', 0, 120.00, NULL),
(42, 'Signature pasta', 'Pizza, Burgers, and Snacks/67f76c5cdea72.jpg', 3, 'Pasta, Guanciale, Canned Tomatoes, Pecorino Romano Cheese, (Optional: White Wine), Red Pepper Flakes, Olive Oil, Fresh Herbs (Parsley), Salt, and Black Pepper.', 1, 200.00, NULL),
(43, 'Samosa', 'Street Food and Quick Bites/67f76be3821fc.jpg', 2, 'Maida, boiled potatoes, green peas, green chili, cumin, garam masala, salt.', 0, 30.00, NULL),
(44, 'Hot Chocolate ', 'Beverages/67f600c30204d.jpg', 6, 'Milk, cocoa powder, sugar, chocolate , vanilla ', 0, 265.00, NULL),
(45, 'Panneer Pizza (Medium)', 'Pizza, Burgers, and Snacks/67f60f52e1792.jpeg', 3, 'Paneer, pizza base, cheese, capsicum, onion, tomato sauce, chili flakes, oregano.', 1, 460.00, NULL),
(46, 'Mutton Thali Set', 'Traditional Nepali Meals and Platter/67f60ff20a636.jpeg', 1, 'Mutton curry, rice, dal, achar, saag, curd, papad.', 0, 300.00, NULL),
(47, 'Chicken Thali Set', 'Traditional Nepali Meals and Platter/67f6114ad078d.png', 1, 'Chicken curry, rice, dal, achar, saag, curd, papad.', 0, 240.00, NULL),
(48, 'Roti Tarkari ', 'Traditional Nepali Meals and Platter/67f616437f9b1.jpeg', 1, 'Roti ,Tarkari,Achar,Papad,Curd', 0, 150.00, NULL),
(49, 'Cappuccino ', 'Beverages/67f61e3f835fb.jpg', 6, 'Espresso, steamed milk, milk foam', 0, 150.00, NULL),
(50, 'Puri Tarkari', 'Traditional Nepali Meals and Platter/67f76e0d98d8a.jpg', 1, 'Wheat flour, potato, peas, spices, salt, oil.', 0, 120.00, NULL),
(51, 'Chicken Khaja Set', 'Traditional Nepali Meals and Platter/67f76ea829306.jpg', 1, 'Beaten rice (chiura), chicken curry/choila, boiled egg, achar, bhatmas (fried soybeans), saag, salad.', 0, 260.00, NULL),
(52, 'Veg Dhido Set', 'Traditional Nepali Meals and Platter/67f76f3ae7615.jpg', 1, 'Buckwheat or millet dhido, gundruk, dal, saag, achar, curd, salad.', 0, 209.99, NULL),
(53, 'Chicken Dhido Set', 'Traditional Nepali Meals and Platter/67f7717c6725e.jpg', 1, 'Buckwheat or millet dhido, chicken curry, dal, saag, gundruk, achar, curd.', 0, 280.00, NULL),
(54, 'Aalu Paratha (per piece)', 'Traditional Nepali Meals and Platter/680a8c6f26248.jpeg', 1, 'Wheat flour, boiled potatoes, green chili, coriander, cumin, salt, ghee/butter.', 0, 80.00, 50.00),
(55, 'Paneer Paratha (per piece)', 'Traditional Nepali Meals and Platter/67f77293e4f13.jpg', 1, 'Wheat flour, grated paneer, green chili, coriander, spices, salt, ghee/butter.', 0, 70.00, NULL),
(56, 'Panner Katti Roll', 'Pizza, Burgers, and Snacks/6830a2d3dcc5f.jpeg', 3, 'veggies, spicy paneer, mixed peppers, and onions', 0, 200.00, 150.00);

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_hidden` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`offer_id`, `title`, `description`, `badge`, `image`, `valid_until`, `is_ongoing`, `section`, `how_to_take`, `created_at`, `updated_at`, `is_hidden`) VALUES
(1, 'Family Meal Deal', 'Order any 4 main courses and get 20% off your total bill. Perfect for family gatherings!', '20% OFF', 'images/offers/family-meal.jpg', '0000-00-00', 0, '0', 'Simply mention this offer to your server when dining with your family at DineAmaze.', '2025-05-24 06:13:00', '2025-05-24 06:13:00', 0),
(2, 'Free Dessert', 'Spend over Rs. 1500 on your meal and receive a complimentary dessert of your choice.', 'FREE', 'images/offers/free-dessert.jpg', '0000-00-00', 0, '0', 'Your server will automatically offer you a free dessert when your bill exceeds Rs. 1500.', '2025-05-24 06:13:00', '2025-05-24 06:13:00', 0),
(3, 'Happy Hour Special', 'Enjoy 15% off on all beverages between 4PM and 6PM, Monday and Thursday.', 'HAPPY HOUR', 'images/offers/happy-hour.png', NULL, 1, '0', 'Visit us during happy hours and the discount will be automatically applied to your beverage order.', '2025-05-24 06:13:00', '2025-05-24 06:13:00', 0),
(4, 'Buy 1 Get 1 Free Appetizers', 'Order any appetizer and get a second one of equal or lesser value for free.', 'BUY 1 GET 1', 'images/offers/buy-one-get-one.jpg', '0000-00-00', 0, '0', 'Mention this offer when ordering appetizers at our restaurant. Only available for dine-in customers.', '2025-05-24 06:13:00', '2025-05-24 06:13:00', 0),
(5, 'Weekday Lunch Special', 'Enjoy our executive lunch menu at a special price of Rs. 599 per person, Monday to Friday, 12PM to 3PM.', 'WEEKDAY', 'images/offers/weekday-lunch.jpg', NULL, 1, '0', 'Simply visit us during weekday lunch hours and ask for the executive lunch menu.', '2025-05-24 06:13:00', '2025-05-24 06:13:00', 0),
(6, 'Birthday Special', 'Celebrate your birthday with us and receive a complimentary cake and 10% off your entire bill.', 'BIRTHDAY', 'images/offers/birthday-special.jpg', NULL, 1, '0', 'Show your ID proving it\'s your birthday (±3 days) when dining at our restaurant.', '2025-05-24 06:13:00', '2025-05-24 06:13:00', 0),
(7, 'Anniversary Celebration', 'Celebrate your anniversary at DineAmaze and receive a complimentary bottle of wine or sparkling juice.', 'ANNIVERSARY', 'images/offers/anniversary.jpg', NULL, 1, '0', 'Inform us when making your reservation that you\'re celebrating your anniversary.', '2025-05-24 06:13:00', '2025-05-24 06:13:00', 0),
(8, 'asdfasfas', 'fadsfasdfasf', 'asdfasfadsfasdfasdfasfas', 'images/offers/68316a46007b9.jpeg', NULL, 1, '0', 'asdfasdfasfadsfdsa', '2025-05-24 06:42:14', '2025-05-24 06:42:14', 0),
(9, 'asdfasfas', 'fadsfasdfasf', 'asdfasfadsfasdfasdfasfas', 'images/offers/68316a67e6210.jpeg', NULL, 1, '0', 'asdfasdfasfadsfdsa', '2025-05-24 06:42:47', '2025-05-24 06:42:47', 0),
(10, 'samir ', 'hello hi by e', '50% off', 'images/offers/kati roll.jpeg', NULL, 1, 'current-offers', 'asdfajslkjdfljasljflasljfasd', '2025-05-24 08:15:53', '2025-05-24 08:18:22', 0);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL,
  `review_text` text NOT NULL,
  `review_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `isHidden` varchar(3) DEFAULT 'No'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`review_id`, `user_id`, `rating`, `review_text`, `review_date`, `isHidden`) VALUES
(4, 8, 4, 'Excellent  Hospitality 😊', '2025-05-22 18:15:00', 'No'),
(6, 7, 5, 'Nice Food Concept', '2025-05-16 18:15:00', 'No');

-- --------------------------------------------------------

--
-- Table structure for table `saved_customizations`
--

CREATE TABLE `saved_customizations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `custom_name` varchar(255) NOT NULL,
  `customization_data` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saved_customizations`
--

INSERT INTO `saved_customizations` (`id`, `user_id`, `item_id`, `custom_name`, `customization_data`, `created_at`) VALUES
(2, 8, 6, 'Chicken Burger Custom', '{\"item_id\":\"6\",\"quantity\":1,\"details\":{\"toppings\":[{\"id\":\"cheese\",\"name\":\"Extra Cheese\",\"price\":50},{\"id\":\"paneer\",\"name\":\"Paneer\",\"price\":60},{\"id\":\"corn\",\"name\":\"Sweet Corn\",\"price\":30}],\"removed_ingredients\":[\"Chicken Patty\"],\"special_instructions\":\"\"}}', '2025-05-23 10:47:34'),
(3, 8, 20, 'Pizza Roll Custom', '{\"item_id\":\"20\",\"quantity\":1,\"details\":{\"toppings\":[{\"id\":\"cheese\",\"name\":\"Extra Cheese\",\"price\":50},{\"id\":\"pepperoni\",\"name\":\"Pepperoni\",\"price\":70},{\"id\":\"onions\",\"name\":\"Onions\",\"price\":30},{\"id\":\"bell_peppers\",\"name\":\"Bell Peppers\",\"price\":35}],\"removed_ingredients\":[],\"special_instructions\":\"\"}}', '2025-05-23 10:48:04'),
(4, 8, 26, 'Ramen Custom', '{\"item_id\":\"26\",\"quantity\":1,\"details\":{\"toppings\":[{\"id\":\"chicken\",\"name\":\"Grilled Chicken\",\"price\":80},{\"id\":\"corn\",\"name\":\"Sweet Corn\",\"price\":30}],\"removed_ingredients\":[],\"special_instructions\":\"\"}}', '2025-05-23 10:57:40'),
(5, 8, 26, 'Ramen Custom1', '{\"item_id\":\"26\",\"quantity\":1,\"details\":{\"toppings\":[{\"id\":\"chicken\",\"name\":\"Grilled Chicken\",\"price\":80},{\"id\":\"paneer\",\"name\":\"Paneer\",\"price\":60},{\"id\":\"corn\",\"name\":\"Sweet Corn\",\"price\":30},{\"id\":\"jalapenos\",\"name\":\"Jalapeños\",\"price\":40}],\"removed_ingredients\":[],\"special_instructions\":\"\"}}', '2025-05-23 10:57:48'),
(6, 8, 21, 'Pepperoni Pizza Custom', '{\"item_id\":\"21\",\"quantity\":1,\"details\":{\"toppings\":[{\"id\":\"mushrooms\",\"name\":\"Mushrooms\",\"price\":40},{\"id\":\"onions\",\"name\":\"Onions\",\"price\":30}],\"removed_ingredients\":[],\"special_instructions\":\"\"}}', '2025-05-23 15:48:35'),
(7, 8, 22, 'Veg-Burger Custom', '{\"item_id\":\"22\",\"quantity\":1,\"details\":{\"toppings\":[{\"id\":\"mushrooms\",\"name\":\"Mushrooms\",\"price\":40},{\"id\":\"olives\",\"name\":\"Olives\",\"price\":45}],\"removed_ingredients\":[],\"special_instructions\":\"\"}}', '2025-05-23 15:56:34'),
(8, 8, 5, 'Cheese Pizza Custom', '{\"item_id\":\"5\",\"quantity\":1,\"details\":{\"toppings\":[{\"id\":\"cheese\",\"name\":\"Extra Cheese\",\"price\":50},{\"id\":\"pepperoni\",\"name\":\"Pepperoni\",\"price\":70},{\"id\":\"onions\",\"name\":\"Onions\",\"price\":30},{\"id\":\"jalapenos\",\"name\":\"Jalapeños\",\"price\":40}],\"removed_ingredients\":[\"Herbs\"],\"special_instructions\":\"\"}}', '2025-05-24 01:08:59'),
(9, 8, 6, 'Chicken Burger Custom1', '{\"item_id\":\"6\",\"quantity\":1,\"details\":{\"toppings\":[{\"id\":\"bell_peppers\",\"name\":\"Bell Peppers\",\"price\":35},{\"id\":\"chicken\",\"name\":\"Grilled Chicken\",\"price\":80},{\"id\":\"paneer\",\"name\":\"Paneer\",\"price\":60},{\"id\":\"corn\",\"name\":\"Sweet Corn\",\"price\":30},{\"id\":\"jalapenos\",\"name\":\"Jalapeños\",\"price\":40}],\"removed_ingredients\":[\"Lettuce\",\"Cheese\"],\"special_instructions\":\"\"}}', '2025-05-24 11:28:26');

-- --------------------------------------------------------

--
-- Table structure for table `takeout_customers`
--

CREATE TABLE `takeout_customers` (
  `id` int(11) NOT NULL,
  `order_group_id` varchar(50) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `pickup_time` varchar(50) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `takeout_customers`
--

INSERT INTO `takeout_customers` (`id`, `order_group_id`, `full_name`, `email`, `contact_number`, `pickup_time`, `order_date`) VALUES
(5, 'order_67f6dc8676806', 'samir', 'samirxtha098@gmail.com', '8092389021', '11:11', '2025-04-10 02:30:58'),
(7, 'order_67f74f15bbe69', 'Sadikshya Munankarmi', 'sadikshyamunankarmi7@gmail.com', '9846269098', '13:40', '2025-04-10 10:39:45'),
(9, 'order_67ffe6c0f412d', 'samir', 'samirxtha098@gmail.com', '9819721210', '12:55', '2025-04-16 23:05:01'),
(11, 'order_6800b37e35f74', 'samir shrestha', 'samirxtha098@gmail.com', '9819721210', '13:50', '2025-04-17 13:38:34'),
(12, 'order_6800b3e78e110', 'samirshrestha', 'samirxtha098@gmail.com', '9819721210', '13:50', '2025-04-17 13:40:19'),
(13, 'order_680488fcba78f', 'samir shrestha', 'samirxtha098@gmail.com', '9819721210', '11:36', '2025-04-20 11:26:16'),
(14, 'order_68048d4486a41', 'Samir  Shrestha', 'samirxtha098@gmail.com', '9819721210', '11:55', '2025-04-20 11:44:32'),
(15, 'order_68048e660ccae', 'samir  shrestha', 'samirxtha098@gmail.com', '9819721210', '11:59', '2025-04-20 11:49:22'),
(16, 'order_680490d65340c', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '12:10', '2025-04-20 11:59:46'),
(17, 'order_680492f8b8ad8', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '12:06', '2025-04-20 12:08:52'),
(18, 'order_68049cafb5c7a', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '13:01', '2025-04-20 12:50:19'),
(19, 'order_68049ddf19ff8', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '13:10', '2025-04-20 12:55:23'),
(20, 'order_6804a0742f5b4', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '13:16', '2025-04-20 13:06:24'),
(21, 'order_6804a0cf2749a', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '13:20', '2025-04-20 13:07:55'),
(22, 'order_6804a25b1ba10', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '13:26', '2025-04-20 13:14:31'),
(23, 'order_6804a29f2091a', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '13:27', '2025-04-20 13:15:39'),
(24, 'order_6804a3298ed49', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '13:29', '2025-04-20 13:17:57'),
(25, 'order_6804a36fcba45', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '13:30', '2025-04-20 13:19:07'),
(26, 'order_6804a4df00b0f', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '13:31', '2025-04-20 13:25:15'),
(27, 'order_6813988c53aca', 'Nijal Shankar ', 'np03cs4a220139@heraldcollege.edu.np', '9876543210', '21:54', '2025-05-01 21:36:40'),
(28, 'order_6813998390b5a', 'Samir', 'samir872@gmail.com', '9819721210', '22:00', '2025-05-01 21:40:47'),
(29, 'order_68139a2a25ff1', 'samir shrestha', 'samirxtha098@gmail.com', '9819721210', '22:00', '2025-05-01 21:43:34'),
(30, 'order_68177e364546c', 'sadikshya munankarmi', 'sadikshyamunankarmi7@gmail.com', '9819721210', '20:33', '2025-05-04 20:33:22'),
(31, 'order_6828d86748c99', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '21:22', '2025-05-18 00:26:43'),
(32, 'order_6828f089c947e', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '11:11', '2025-05-18 02:09:41'),
(33, 'order_6828f22e3a46a', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '11:11', '2025-05-18 02:16:42'),
(34, 'order_6828f31b43700', 'sadish', 'sadikshyamunankarmi7@gmail.com', '9819721210', '11:11', '2025-05-18 02:20:39'),
(35, 'order_6828f389c759d', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '11:11', '2025-05-18 02:22:29'),
(36, 'order_6828f5d22f9dc', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '11:11', '2025-05-18 02:32:14'),
(37, 'order_68296ed9a6772', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '11:20', '2025-05-18 11:08:37'),
(38, 'order_68297097716bb', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '11:27', '2025-05-18 11:16:03'),
(39, 'order_68297331667a7', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '11:38', '2025-05-18 11:27:09'),
(40, 'order_6829748e4d6cf', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '11:44', '2025-05-18 11:32:58'),
(41, 'order_682910d9e50c8', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '11:11', '2025-05-18 04:27:33'),
(42, 'order_6830a52678474', 'samir', 'samirxtha098@gmail.com', '9819721210', '11:11', '2025-05-23 22:26:10'),
(43, 'order_68300cfdc9209', 'samir', 'samirxtha098@gmail.com', '9819721210', '11:49', '2025-05-23 11:36:57'),
(44, 'order_68300dea0c4aa', 'samir shrestha', 'samirxtha098@gmail.com', '9819721210', '11:57', '2025-05-23 11:40:54'),
(45, 'order_68300e1db4e1d', 'samir', 'samirxtha098@gmail.com', '9819721210', '11:58', '2025-05-23 11:41:45'),
(46, 'order_683013b819d5c', 'samir ', 'samirxtha098@gmail.com', '9819721210', '12:18', '2025-05-23 12:05:40'),
(47, 'order_683013e426958', 'samir', 'samirxtha098@gmail.com', '9819721210', '12:50', '2025-05-23 12:06:24'),
(48, 'order_68301437db99a', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '13:21', '2025-05-23 12:07:47'),
(49, 'order_68301482b4601', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '12:30', '2025-05-23 12:09:02'),
(50, 'order_683014b449ba2', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '14:02', '2025-05-23 12:09:52'),
(51, 'order_683015047bd10', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '13:01', '2025-05-23 12:11:12'),
(52, 'order_683015dd7994e', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '12:30', '2025-05-23 12:14:49'),
(53, 'order_68301621ea8fd', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '12:54', '2025-05-23 12:15:57'),
(54, 'order_68301663a9357', 'samir shrestha', 'samirxtha098@gmail.com', '9838742218', '11:01', '2025-05-23 12:17:03'),
(55, 'order_68301717d0d97', 'samir', 'samirxtha098@gmail.com', '9819731210', '11:11', '2025-05-23 12:20:03'),
(56, 'order_683018707909a', 'samirxtha098@gmail.com', 'samirxtha098@gmail.com', '9819721210', '11:01', '2025-05-23 12:25:48'),
(57, 'order_68311dc5d7d40', 'Samir', 'samirxtha098@gmail.com', '9819721210', '11:30', '2025-05-24 07:00:49'),
(58, 'order_68318c1ab13a3', 'Samir Shrestha', 'samirxtha098@gmail.com', '9861044040', '15:10', '2025-05-24 14:51:34'),
(59, 'order_68318e90806b7', 'Samir Shrestha', 'samirxtha098@gmail.com', '9861044040', '15:15', '2025-05-24 15:02:04'),
(60, 'order_68319013c8946', 'Samir Shrestha', 'samirxtha098@gmail.com', '9861044040', '15:20', '2025-05-24 15:08:31'),
(61, 'order_683198bb689fc', 'Samir Shrestha', 'samirxtha098@gmail.com', '9861044040', '15:59', '2025-05-24 15:45:27'),
(62, 'order_6831a333cb2fd', 'Samir Shrestha', 'samirxtha098@gmail.com', '9861044040', '16:42', '2025-05-24 16:30:07'),
(63, 'order_6831aa454ed91', 'Samir Shrestha', 'samirxtha098@gmail.com', '9861044040', '17:10', '2025-05-24 17:00:17'),
(64, 'order_6831ad7201cde', 'Samir Shrestha', 'samirxtha098@gmail.com', '9861044040', '17:20', '2025-05-24 17:13:50'),
(65, 'order_6831ae155ec8f', 'Samir Shrestha', 'samirxtha098@gmail.com', '9861044040', '17:30', '2025-05-24 17:16:33');

-- --------------------------------------------------------

--
-- Table structure for table `takeout_order_items`
--

CREATE TABLE `takeout_order_items` (
  `order_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` enum('pending','verified','completed','cancelled') NOT NULL DEFAULT 'pending',
  `order_date` datetime DEFAULT current_timestamp(),
  `order_group_id` varchar(50) DEFAULT NULL,
  `pickup_notification_sent` tinyint(1) DEFAULT 0,
  `pickup_time` varchar(50) DEFAULT NULL,
  `preparation_notification_sent` tinyint(1) DEFAULT 0,
  `ready_notification_sent` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `takeout_order_items`
--

INSERT INTO `takeout_order_items` (`order_id`, `item_name`, `quantity`, `price`, `email`, `status`, `order_date`, `order_group_id`, `pickup_notification_sent`, `pickup_time`, `preparation_notification_sent`, `ready_notification_sent`) VALUES
(1, 'Sel Roti', 1, 100.00, 'samirxtha098@gmail.com', 'verified', '2025-05-18 02:32:14', 'order_6828f5d22f9dc', 0, '11:11', 1, 0),
(2, 'Pizza Roll', 2, 230.00, 'samirxtha098@gmail.com', '', '2025-05-18 11:08:37', 'order_68296ed9a6772', 0, '11:20', 1, 0),
(3, 'Sel Roti', 1, 100.00, 'samirxtha098@gmail.com', '', '2025-05-18 11:16:03', 'order_68297097716bb', 0, '11:27', 1, 0),
(4, 'Sel Roti', 1, 100.00, 'samirxtha098@gmail.com', '', '2025-05-18 11:27:09', 'order_68297331667a7', 0, '11:38', 1, 0),
(5, 'Sel Roti', 1, 100.00, 'samirxtha098@gmail.com', 'verified', '2025-05-18 11:32:58', 'order_6829748e4d6cf', 0, '11:44', 1, 0),
(6, 'Pizza Roll', 2, 230.00, 'samirxtha098@gmail.com', '', '2025-05-18 04:27:33', 'order_682910d9e50c8', 0, '11:11', 1, 0),
(8, 'Pizza Roll', 1, 270.00, 'samirxtha098@gmail.com', '', '2025-05-23 11:36:57', 'order_68300cfdc9209', 0, '11:49', 1, 0),
(9, 'Pizza Roll', 1, 270.00, 'samirxtha098@gmail.com', '', '2025-05-23 11:40:54', 'order_68300dea0c4aa', 0, '11:57', 1, 0),
(10, 'Pizza Roll', 1, 270.00, 'samirxtha098@gmail.com', '', '2025-05-23 11:41:45', 'order_68300e1db4e1d', 0, '11:58', 1, 0),
(11, 'Buff Khaja Set', 1, 200.00, 'samirxtha098@gmail.com', '', '2025-05-23 12:05:40', 'order_683013b819d5c', 0, '12:18', 1, 0),
(12, 'Pizza Roll', 1, 335.00, 'samirxtha098@gmail.com', '', '2025-05-23 12:05:40', 'order_683013b819d5c', 0, '12:18', 1, 0),
(13, 'Buff Khaja Set', 1, 200.00, 'samirxtha098@gmail.com', '', '2025-05-23 12:06:24', 'order_683013e426958', 0, '12:50', 1, 0),
(14, 'Pizza Roll', 1, 335.00, 'samirxtha098@gmail.com', '', '2025-05-23 12:06:24', 'order_683013e426958', 0, '12:50', 1, 0),
(15, 'Buff Khaja Set', 1, 200.00, 'samirxtha098@gmail.com', '', '2025-05-23 12:07:47', 'order_68301437db99a', 0, '13:21', 1, 0),
(16, 'Pizza Roll', 1, 335.00, 'samirxtha098@gmail.com', '', '2025-05-23 12:07:47', 'order_68301437db99a', 0, '13:21', 1, 0),
(17, 'Buff Khaja Set', 1, 200.00, 'samirxtha098@gmail.com', '', '2025-05-23 12:09:02', 'order_68301482b4601', 0, '12:30', 1, 0),
(18, 'Pizza Roll', 1, 335.00, 'samirxtha098@gmail.com', '', '2025-05-23 12:09:02', 'order_68301482b4601', 0, '12:30', 1, 0),
(19, 'Veg Thali Set', 1, 200.00, 'samirxtha098@gmail.com', '', '2025-05-23 12:09:52', 'order_683014b449ba2', 0, '14:02', 1, 0),
(20, 'Buff Khaja Set', 1, 200.00, 'samirxtha098@gmail.com', '', '2025-05-23 12:11:12', 'order_683015047bd10', 0, '13:01', 1, 0),
(21, 'Chicken Burger', 1, 440.00, 'samirxtha098@gmail.com', '', '2025-05-23 12:11:12', 'order_683015047bd10', 0, '13:01', 1, 0),
(22, 'Buff Khaja Set', 2, 200.00, 'samirxtha098@gmail.com', '', '2025-05-23 12:14:49', 'order_683015dd7994e', 0, '12:30', 1, 0),
(23, 'Buff Khaja Set', 2, 200.00, 'samirxtha098@gmail.com', 'pending', '2025-05-23 12:15:57', 'order_68301621ea8fd', 0, NULL, 0, 0),
(24, 'Buff Khaja Set', 2, 200.00, 'samirxtha098@gmail.com', '', '2025-05-23 12:17:03', 'order_68301663a9357', 0, '11:01', 1, 0),
(25, 'Lassi', 1, 80.00, 'samirxtha098@gmail.com', '', '2025-05-23 12:20:03', 'order_68301717d0d97', 0, '11:11', 1, 0),
(26, 'Buff Khaja Set', 1, 200.00, 'samirxtha098@gmail.com', '', '2025-05-23 12:25:48', 'order_683018707909a', 0, '11:01', 1, 0),
(27, 'Veg-Burger', 1, 405.00, 'samirxtha098@gmail.com', '', '2025-05-23 12:25:48', 'order_683018707909a', 0, '11:01', 1, 0),
(28, 'Buff Khaja Set', 1, 200.00, 'samirxtha098@gmail.com', '', '2025-05-24 07:00:49', 'order_68311dc5d7d40', 0, '11:30', 1, 0),
(29, 'Cheese Pizza', 2, 640.00, 'samirxtha098@gmail.com', '', '2025-05-24 07:00:49', 'order_68311dc5d7d40', 0, '11:30', 1, 0),
(30, 'Veg Thali Set', 1, 200.00, 'samirxtha098@gmail.com', 'cancelled', '2025-05-24 14:51:34', 'order_68318c1ab13a3', 0, '15:10', 0, 0),
(31, 'Veg Thali Set', 1, 200.00, 'samirxtha098@gmail.com', 'cancelled', '2025-05-24 15:02:04', 'order_68318e90806b7', 0, '15:15', 0, 0),
(32, 'Chicken-Momo', 1, 150.00, 'samirxtha098@gmail.com', 'cancelled', '2025-05-24 15:02:04', 'order_68318e90806b7', 0, '15:15', 0, 0),
(33, 'Veg Thali Set', 1, 200.00, 'samirxtha098@gmail.com', '', '2025-05-24 15:08:31', 'order_68319013c8946', 0, '15:20', 1, 0),
(34, 'Chicken-Momo', 1, 150.00, 'samirxtha098@gmail.com', '', '2025-05-24 15:08:31', 'order_68319013c8946', 0, '15:20', 1, 0),
(35, 'Buff Khaja Set', 1, 200.00, 'samirxtha098@gmail.com', 'verified', '2025-05-24 15:45:27', 'order_683198bb689fc', 0, '15:59', 0, 0),
(36, 'Samosa', 2, 30.00, 'samirxtha098@gmail.com', 'verified', '2025-05-24 16:30:07', 'order_6831a333cb2fd', 0, '16:42', 0, 0),
(37, 'Newari Khaja Set', 1, 320.00, 'samirxtha098@gmail.com', '', '2025-05-24 17:00:17', 'order_6831aa454ed91', 1, '17:10', 1, 0),
(38, 'Buff Khaja Set', 1, 200.00, 'samirxtha098@gmail.com', '', '2025-05-24 17:13:50', 'order_6831ad7201cde', 0, '17:20', 1, 0),
(39, 'Chicken Burger', 1, 545.00, 'samirxtha098@gmail.com', '', '2025-05-24 17:13:50', 'order_6831ad7201cde', 0, '17:20', 1, 0),
(40, 'Buff Khaja Set', 1, 200.00, 'samirxtha098@gmail.com', '', '2025-05-24 17:16:33', 'order_6831ae155ec8f', 1, '17:30', 1, 0),
(41, 'Chicken Burger', 1, 545.00, 'samirxtha098@gmail.com', '', '2025-05-24 17:16:33', 'order_6831ae155ec8f', 1, '17:30', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone_number`, `is_verified`, `profile_image`) VALUES
(7, 'sadikshya munankarmi', 'sadikshyamunankarmi7@gmail.com', '$2y$10$AnfdxqOK5oXipmQ3lBLRcuzFH.wtngSZ3cfG80oU6GkeWUmwG59J.', '9861044040', 1, 'images/profile/profile_67f10fbc600e5_1746111968.png'),
(8, 'Samir Shrestha', 'samirxtha098@gmail.com', '$2y$10$4OmmJYvWqa/HQYQNN.LwLu3N3TPN51KUFf5Y6LJzrZYxuszdvIdpi', '9861044040', 1, 'assets/images/profile/profile_67f113f0eb9b5_1747467070.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`);

--
-- Indexes for table `contact_message`
--
ALTER TABLE `contact_message`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `id_documents`
--
ALTER TABLE `id_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_category`
--
ALTER TABLE `menu_category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `menu_item`
--
ALTER TABLE `menu_item`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`offer_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `saved_customizations`
--
ALTER TABLE `saved_customizations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `takeout_customers`
--
ALTER TABLE `takeout_customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `takeout_order_items`
--
ALTER TABLE `takeout_order_items`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_message`
--
ALTER TABLE `contact_message`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `id_documents`
--
ALTER TABLE `id_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `menu_category`
--
ALTER TABLE `menu_category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `menu_item`
--
ALTER TABLE `menu_item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `offer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `saved_customizations`
--
ALTER TABLE `saved_customizations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `takeout_customers`
--
ALTER TABLE `takeout_customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `takeout_order_items`
--
ALTER TABLE `takeout_order_items`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68309;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contact_message`
--
ALTER TABLE `contact_message`
  ADD CONSTRAINT `contact_message_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_item`
--
ALTER TABLE `menu_item`
  ADD CONSTRAINT `menu_item_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `menu_category` (`category_id`);

--
-- Constraints for table `saved_customizations`
--
ALTER TABLE `saved_customizations`
  ADD CONSTRAINT `saved_customizations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `saved_customizations_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `menu_item` (`item_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
