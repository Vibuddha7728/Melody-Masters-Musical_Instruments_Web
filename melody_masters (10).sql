-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 02, 2026 at 03:55 PM
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
-- Database: `melody_masters`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `parent_id`, `created_at`) VALUES
(1, 'Guitars', NULL, '2026-02-24 13:18:20'),
(2, 'Keyboards', NULL, '2026-02-26 05:58:20'),
(3, 'Drums', NULL, '2026-02-26 05:58:20'),
(4, 'Wind Instruments', NULL, '2026-02-26 05:58:20'),
(7, 'Digital Sheet Music', NULL, '2026-02-26 05:58:20');

-- --------------------------------------------------------

--
-- Table structure for table `digital_products`
--

CREATE TABLE `digital_products` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `max_downloads` int(11) DEFAULT 5,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `digital_products`
--

INSERT INTO `digital_products` (`id`, `product_id`, `file_path`, `max_downloads`, `created_at`) VALUES
(4, 19, 'digital.pdf', 10, '2026-02-27 08:58:47');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `shipping_cost` decimal(10,2) DEFAULT 0.00,
  `status` varchar(20) DEFAULT 'pending',
  `tracking_no` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` double(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `shipping_cost`, `status`, `tracking_no`, `created_at`, `total_amount`, `payment_method`, `address`) VALUES
(40, 7, 0.00, 0.00, 'completed', NULL, '2026-02-27 16:13:59', 1500.00, NULL, NULL),
(42, 10, 0.00, 0.00, 'completed', NULL, '2026-02-28 09:39:15', 1500.00, NULL, NULL),
(43, 11, 0.00, 0.00, 'Completed', '', '2026-02-28 09:44:46', 55000.00, NULL, NULL),
(44, 10, 0.00, 0.00, 'pending', NULL, '2026-02-28 09:48:57', 95500.00, NULL, NULL),
(45, 12, 0.00, 0.00, 'Completed', '', '2026-02-28 09:54:41', 75000.00, NULL, NULL),
(46, 13, 0.00, 0.00, 'Completed', 'MM12345678', '2026-02-28 10:54:26', 150000.00, NULL, NULL),
(47, 14, 0.00, 0.00, 'pending', NULL, '2026-02-28 11:05:58', 150000.00, NULL, NULL),
(48, 15, 0.00, 0.00, 'pending', NULL, '2026-03-02 14:39:49', 75000.00, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(44, 40, 19, 1, 1500.00),
(46, 42, 19, 1, 1500.00),
(47, 43, 20, 1, 55000.00),
(48, 44, 18, 1, 95500.00),
(49, 45, 15, 1, 75000.00),
(50, 46, 15, 2, 75000.00),
(51, 47, 15, 2, 75000.00),
(52, 48, 15, 1, 75000.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `category_id` int(11) DEFAULT NULL,
  `type` enum('physical','digital') DEFAULT 'physical',
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_type` varchar(50) DEFAULT 'physical',
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `brand`, `price`, `category`, `stock`, `category_id`, `type`, `image`, `created_at`, `product_type`, `file_path`) VALUES
(15, 'VAD716 V-Drums Acoustic Design', NULL, NULL, 75000.00, NULL, 50, 3, 'physical', 'drum.jpg', '2026-02-27 08:06:38', 'physical', NULL),
(16, 'Yamaha FG800', NULL, NULL, 59999.00, NULL, 75, 1, 'physical', '1772180074_lineup-3_202202_73dacb1d40c9ed2e474ae826c377f8c3.avif', '2026-02-27 08:14:34', 'physical', NULL),
(17, 'Yamaha PSR-E373', NULL, NULL, 7500.00, NULL, 40, 2, 'physical', '1772180204_v-stage_76_main.jpg', '2026-02-27 08:16:44', 'physical', NULL),
(18, 'Baritone Saxophone', NULL, NULL, 95500.00, NULL, 10, 1, 'physical', '1772180546_255301.jpg', '2026-02-27 08:22:26', 'physical', NULL),
(19, 'Electric Guitar Solo Pack', NULL, NULL, 1500.00, NULL, 15, 7, 'physical', 'digital_default.png', '2026-02-27 08:58:47', 'digital', NULL),
(20, 'Melody Grand X1', NULL, NULL, 55000.00, NULL, 400, 2, 'physical', '1772210652_gp_color_picker_bk.jpg', '2026-02-27 16:44:12', 'physical', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `product_id`, `rating`, `comment`, `created_at`) VALUES
(6, 10, 19, 5, 'The audio samples and digital sheet music are of exceptional quality. No corrupted files or broken links—everything worked perfectly right after the download. This is now my go-to site for high-quality digital music resources.', '2026-02-28 09:40:51'),
(7, 11, 20, 3, 'Excellent customer support! They helped me choose the right digital piano for my home studio. The staff was very knowledgeable and the pricing is much more reasonable compared to other stores. Highly recommended!', '2026-02-28 09:47:23'),
(8, 12, 15, 4, '\"The sound of my new electric guitar is amazing. It is a real brand and very high quality. This is the best place to buy instruments for a good price. I will buy from here again!', '2026-02-28 09:58:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('customer','staff','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `email`, `password`, `phone`, `address`, `role`, `created_at`) VALUES
(2, 'Vibuddha Vibodha', '', 'vibuddha7728@gmail.com', '$2y$10$S0254BvD1UZlkvY/s4AdauMdepBArhqbtQw9z/SP4zFPQtIxkRRia', NULL, NULL, 'admin', '2026-02-22 12:40:13'),
(6, 'chamodi sandeepani', '', 'chamo@gmail.com', '$2y$10$yRS37cIBioFdJ0zCB74EXOD1r9NrgT6z20F.IW6vaLU4PACn3bL/G', NULL, NULL, 'staff', '2026-02-25 06:49:31'),
(7, 'Rasodha Sewmini', '', 'raso@gmail.com', '$2y$10$njng2lhkTvib7cyQFwST1udrW94hXblw2WGnr4t4hm.JENsRtkQZa', NULL, '68,malinda,, Gampaha, 11000', 'customer', '2026-02-25 14:25:59'),
(8, 'pawan ', '', 'pawan@gmail.com', '$2y$10$ijjL1K3XkeF9blihCC3maOmHd4/DU846PPr/aLGyKRMjhdVBHLqLy', NULL, '68,malinda,, Gampaha, 11000', 'customer', '2026-02-26 06:26:04'),
(10, 'Binada Niduwara', '', 'Binada@gmail.com', '$2y$10$OCvgrtYGrEhVWvxqTiYlOeUCjmhYHo3VRh1D7PD2GCC9qqTXsGcyO', NULL, NULL, 'customer', '2026-02-28 09:38:11'),
(11, 'Ishan Jayawardena', '', 'ishan@gmail.com', '$2y$10$bGZAA17WqZPdLt0TyZqlYep0Y5mTsGEGska6pRop.LVu5UGR.IXa.', NULL, NULL, 'customer', '2026-02-28 09:42:53'),
(12, 'Nethmi mohotti', '', 'nethmi@gmail.com', '$2y$10$Ex5u2fzGaK9xDhE7XTx70O3u4Us1JygFvl.Rsd78optFWr9nA90Fa', NULL, NULL, 'customer', '2026-02-28 09:53:14'),
(13, 'kavindu Subash', '', 'kavindu@gmail.com', '$2y$10$c.ht/zTR27LNkbl37etSaeO.Ip/8CFjl/pRtpZ/8daNrwSeLPJjDS', NULL, NULL, 'customer', '2026-02-28 10:52:06'),
(14, 'Eshanthi Aththanayaka', '', 'eshandi@gmail.com', '$2y$10$XH05raAWue3uY2g2mNVPeuxjYopxOLGCe3u/CIblPq6s57/G.chEm', NULL, NULL, 'customer', '2026-02-28 11:03:40'),
(15, 'Sandeepa Wijerathna', '', 'sandeepa@gmaail.com', '$2y$10$WXhGb6PEcQiY2Hmamy7BrO8kTUHjS55XWKGWAozVHK9uvVfe93Ila', NULL, NULL, 'customer', '2026-03-02 14:37:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_parent_category` (`parent_id`);

--
-- Indexes for table `digital_products`
--
ALTER TABLE `digital_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_digital_product` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_user` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orderitem_order` (`order_id`),
  ADD KEY `fk_orderitem_product` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_category` (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_review_user` (`user_id`),
  ADD KEY `fk_review_product` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `digital_products`
--
ALTER TABLE `digital_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_parent_category` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `digital_products`
--
ALTER TABLE `digital_products`
  ADD CONSTRAINT `fk_digital_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_orderitem_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_orderitem_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_review_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_review_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
