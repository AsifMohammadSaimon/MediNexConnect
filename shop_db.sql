-- MediNexConnect Database
-- Updated: Passwords are MD5 hashes (will auto-upgrade to bcrypt on first login)
-- phpMyAdmin SQL Dump | MariaDB 10.4+ | PHP 8.0+

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Database: `shop_db`

-- --------------------------------------------------------
-- Table: cart
-- --------------------------------------------------------
CREATE TABLE `cart` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `pid` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(100) NOT NULL,
  `quantity` int(100) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: message
-- --------------------------------------------------------
CREATE TABLE `message` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `number` varchar(12) NOT NULL,
  `message` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: orders
-- --------------------------------------------------------
CREATE TABLE `orders` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `number` varchar(12) NOT NULL,
  `email` varchar(100) NOT NULL,
  `method` varchar(50) NOT NULL,
  `address` varchar(500) NOT NULL,
  `total_products` varchar(1000) NOT NULL,
  `total_price` int(100) NOT NULL,
  `placed_on` varchar(50) NOT NULL,
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `orders` (`id`, `user_id`, `name`, `number`, `email`, `method`, `address`, `total_products`, `total_price`, `placed_on`, `payment_status`) VALUES
(12, 31, 'Joya', '01234567890', 'joya@gmail.com', 'credit card', 'flat no. flat2 mojaffar nagar chittagong  bangladesh - 1234', ', banana ( 1 )', 50, '02-Sep-2023', 'completed'),
(13, 31, 'rosni', '01234567891', 'rosni@gmail.com', 'cash on delivery', 'flat no. flat3 agrabad chittagong bangladesh - 1234', ', banana ( 1 )', 50, '02-Sep-2023', 'completed'),
(14, 33, 'Fardin', '01234567893', 'fardin@gmail.com', 'cash on delivery', 'flat no. dc road chawkbazar chittagong bangladesh - 12345', ', banana ( 2 )', 100, '02-Sep-2023', 'pending');

-- --------------------------------------------------------
-- Table: products
-- --------------------------------------------------------
CREATE TABLE `products` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(20) NOT NULL,
  `details` varchar(500) NOT NULL,
  `price` int(100) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `products` (`id`, `name`, `category`, `details`, `price`, `image`) VALUES
(24, 'banana', 'fruits', 'khubi tasty banana, ekdom vejal mukto', 50, 'banana.png');

-- --------------------------------------------------------
-- Table: users
-- NOTE: Passwords below are MD5 hashes (legacy).
-- The updated login.php will auto-upgrade them to bcrypt on first login.
-- Default passwords: nadia=12345, rosni=1234, fardin=1234
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` varchar(20) NOT NULL DEFAULT 'user',
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `name`, `email`, `password`, `user_type`, `image`) VALUES
(31, 'nadia', 'nadia@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 'user', 'girl1.jpg'),
(32, 'rosni', 'rosni@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 'admin', 'girl2.jpg'),
(33, 'Fardin', 'fardin@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 'user', 'Screenshot_1.jpg');

-- --------------------------------------------------------
-- Table: wishlist
-- --------------------------------------------------------
CREATE TABLE `wishlist` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `pid` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(100) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Indexes
ALTER TABLE `cart` ADD PRIMARY KEY (`id`);
ALTER TABLE `message` ADD PRIMARY KEY (`id`);
ALTER TABLE `orders` ADD PRIMARY KEY (`id`);
ALTER TABLE `products` ADD PRIMARY KEY (`id`);
ALTER TABLE `users` ADD PRIMARY KEY (`id`);
ALTER TABLE `wishlist` ADD PRIMARY KEY (`id`);

-- AUTO_INCREMENT
ALTER TABLE `cart` MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;
ALTER TABLE `message` MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
ALTER TABLE `orders` MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
ALTER TABLE `products` MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
ALTER TABLE `users` MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
ALTER TABLE `wishlist` MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
