-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 23, 2025 at 07:35 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lotso`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `type` enum('product','post') NOT NULL DEFAULT 'product',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`, `status`, `type`, `created_at`, `updated_at`) VALUES
(1, 'Giày thể thao', 'giay-the-thao', 'Chuyên các loại giày thể thao', 'sport.jpg', 1, 'product', '2025-04-22 13:47:13', '2025-04-22 15:33:02'),
(2, 'Giày công sở', 'giay-cong-so', 'Chuyên các loại giày công sở', 'office.jpg', 1, 'product', '2025-04-22 13:47:13', '2025-04-22 13:47:13'),
(3, 'Giày sneakers', 'giay-sneakers', 'Các mẫu sneakers hot nhất', 'sneakers.jpg', 1, 'product', '2025-04-22 13:47:13', '2025-04-22 13:47:13'),
(4, 'dép crocs', 'dep-crocs', 'mẫu giày dép đa năng, năng động\r\n', NULL, 1, 'product', '2025-04-22 15:35:46', '2025-04-22 15:42:22'),
(5, 'dép xỏ ngón ', 'dep-xo-ngon', 'dép xỏ ngón thoáng mát, thời thượng ', NULL, 1, 'product', '2025-04-22 15:41:54', '2025-04-22 15:41:54'),
(6, 'Dép lê bé gái', 'dep-le-be-gai', 'danh mục dép lê cho bé gái ', NULL, 1, 'product', '2025-04-22 15:52:57', '2025-04-22 15:52:57'),
(7, 'Dép đế xuông ', 'dep-de-xuong', 'cổ điển, lịch thiệp', NULL, 1, 'product', '2025-04-22 15:58:24', '2025-04-22 15:58:24'),
(8, 'Dép lông mùa đông ', 'dep-long-mua-dong', 'Ấm áp dễ thương', NULL, 1, 'product', '2025-04-22 15:58:46', '2025-04-22 15:58:46'),
(9, 'Phụ kiện lotso', 'phu-kien-lotso', 'Phụ kiện đi kèm của lotso', NULL, 1, 'product', '2025-04-22 16:01:34', '2025-04-22 16:01:34'),
(10, 'Dép bánh mì lotso', 'ep-banh-mi-lotso', 'Dép lê êm ái', NULL, 1, 'product', '2025-04-22 16:08:40', '2025-04-22 19:12:12'),
(12, 'Lotso', 'otso', 'bài viết về lotso', NULL, 1, 'post', '2025-04-22 19:12:35', '2025-04-22 19:12:35');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int NOT NULL,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `status` enum('pending','approved','spam') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `user_id`, `user_name`, `email`, `content`, `status`, `created_at`) VALUES
(1, 1, 2, 'John Doe', 'john@example.com', 'Bài viết rất hữu ích!', 'approved', '2025-04-22 13:47:13'),
(2, 2, 3, 'Jane Smith', 'jane@example.com', 'Mình thích phong cách sneaker!', 'approved', '2025-04-22 13:47:13');

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `id` int NOT NULL,
  `code` varchar(50) NOT NULL,
  `type` enum('percentage','fixed') NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `used` int NOT NULL DEFAULT '0',
  `minimum_order` decimal(10,2) NOT NULL DEFAULT '0.00',
  `description` text,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `discounts`
--

INSERT INTO `discounts` (`id`, `code`, `type`, `value`, `quantity`, `used`, `minimum_order`, `description`, `start_date`, `end_date`, `created_at`) VALUES
(1, 'SUMMER10', 'percentage', 10.00, 100, 0, 0.00, 'Giảm 10% cho mùa hè', '2024-06-01 00:00:00', '2024-06-30 23:59:59', '2025-04-22 13:47:13'),
(2, 'WINTER50', 'fixed', 50.00, 50, 0, 200000.00, 'Giảm 50K cho đơn từ 200K', '2024-12-01 00:00:00', '2024-12-31 23:59:59', '2025-04-22 13:47:13'),
(3, 'W5YSDEE5', 'percentage', 60.00, 10, 0, 0.00, 'tgdfsdfd', '2025-04-22 23:54:00', '2025-04-30 23:54:00', '2025-04-22 16:54:11'),
(4, '7EOL7YS1', 'percentage', 60.00, 10, 0, 5000.00, 'ưefsdf', '2025-04-26 00:13:00', '2025-05-07 00:13:00', '2025-04-22 17:13:47'),
(5, 'RD1E9YV9', 'fixed', 60.00, 10, 0, 5000.00, 'ádasdasasdsa', '2025-04-25 00:56:00', '2025-04-26 00:56:00', '2025-04-22 17:56:27'),
(6, 'JJ8NGFXA', 'percentage', 60.00, 10, 0, 0.00, 'adasd', '2025-04-23 02:11:00', '2025-04-30 02:11:00', '2025-04-22 19:11:51');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `shipping_name` varchar(255) NOT NULL,
  `shipping_phone` varchar(20) NOT NULL,
  `shipping_address` text NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'bank',
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','completed','canceled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `shipping_name`, `shipping_phone`, `shipping_address`, `payment_method`, `total_price`, `status`, `created_at`) VALUES
(1, 2, '', '', '', 'bank', 150.00, 'completed', '2025-04-22 13:47:13'),
(2, 3, '', '', '', 'bank', 180.00, 'pending', '2025-04-22 13:47:13'),
(3, 2, '', '', '', 'bank', 130.00, 'processing', '2025-04-22 13:47:13'),
(4, 6, 'nguyenvana', '0988381713', '66 Hồ Tùng Mậu, Mai Dịch', 'bank', 246997.00, 'pending', '2025-04-22 21:14:18'),
(5, 6, 'nguyenvana', '0988381713', '66 Hồ Tùng Mậu, Mai Dịch', 'bank', 444444.00, 'pending', '2025-04-22 21:19:16'),
(6, 6, 'nguyenvana', '0988381713', '66 Hồ Tùng Mậu, Mai Dịch', 'cod', 444444.00, 'pending', '2025-04-22 21:19:22'),
(7, 6, 'nguyenvana', '0988381713', '66 Hồ Tùng Mậu, Mai Dịch', 'cod', 150.00, 'pending', '2025-04-22 21:24:35'),
(8, 6, 'nguyenvana', '0988381713', '66 Hồ Tùng Mậu, Mai Dịch', 'cod', 690000.00, 'pending', '2025-04-22 21:27:11'),
(9, 6, 'nguyenvana', '0988381713', '66 Hồ Tùng Mậu, Mai Dịch', 'cod', 360000.00, 'pending', '2025-04-22 21:27:33'),
(10, 6, 'nguyenvana', '0988381713', '66 Hồ Tùng Mậu, Mai Dịch', 'cod', 920000.00, 'pending', '2025-04-22 21:29:25'),
(11, 6, 'nguyenvana', '0988381713', '66 Hồ Tùng Mậu, Mai Dịch', 'bank', 100000.00, 'pending', '2025-04-22 21:29:57'),
(12, 6, 'nguyenvana', '0988381713', '66 Hồ Tùng Mậu, Mai Dịch', 'bank', 230000.00, 'pending', '2025-04-22 21:30:49'),
(13, 6, 'nguyenvana', '0988381713', '66 Hồ Tùng Mậu, Mai Dịch', 'bank', 120000.00, 'pending', '2025-04-22 21:38:12'),
(14, 6, 'nguyenvana', '0988381713', '66 Hồ Tùng Mậu, Mai Dịch', 'cod', 230000.00, 'pending', '2025-04-23 06:27:12'),
(15, 6, 'nguyenvana', '0988381713', '66 Hồ Tùng Mậu, Mai Dịch', 'bank', 230000.00, 'pending', '2025-04-23 06:27:23');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 150.00),
(2, 2, 2, 1, 180.00),
(3, 3, 3, 1, 130.00),
(4, 4, 5, 1, 85.00),
(5, 4, 6, 1, 123456.00),
(6, 4, 7, 1, 123456.00),
(9, 7, 1, 1, 150.00),
(10, 8, 20, 3, 230000.00),
(11, 9, 18, 3, 120000.00),
(12, 10, 20, 4, 230000.00),
(13, 11, 17, 1, 100000.00),
(14, 12, 20, 1, 230000.00),
(15, 13, 18, 1, 120000.00),
(16, 14, 20, 1, 230000.00),
(17, 15, 20, 1, 230000.00);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `excerpt` text,
  `category_id` int DEFAULT NULL,
  `author_id` int NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `views` int NOT NULL DEFAULT '0',
  `status` enum('draft','published') DEFAULT 'draft',
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `slug`, `content`, `excerpt`, `category_id`, `author_id`, `image`, `views`, `status`, `meta_title`, `meta_description`, `meta_keywords`, `created_at`) VALUES
(1, 'Hướng dẫn chọn giày thể thao', 'huong-dan-chon-giay-the-thao', 'Nội dung hướng dẫn chọn giày...', 'Tổng hợp các tiêu chí quan trọng khi chọn giày thể thao', 1, 2, 'huong_dan_chon_giay.jpg', 0, 'published', 'Cách chọn giày thể thao phù hợp', 'Hướng dẫn chi tiết cách chọn giày thể thao phù hợp với từng môn thể thao', NULL, '2025-04-22 13:47:13'),
(2, 'Cách phối đồ với giày sneaker', 'cach-phoi-do-voi-giay-sneaker', 'Nội dung cách phối đồ...', 'Những gợi ý phối đồ cùng sneaker cực chất', 3, 3, 'phoi_do_sneaker.jpg', 0, 'published', 'Mix & Match với Sneaker', 'Hướng dẫn phối đồ với giày sneaker cho nam và nữ', NULL, '2025-04-22 13:47:13');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `category_id` int DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `quantity`, `category_id`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Nike Air Max', 'Giày thể thao Nike Air Max nhẹ, êm ái', 150.00, 50, 1, '1745334537_6807b109582fa.jpg', '2025-04-22 13:47:13', '2025-04-22 15:08:57'),
(2, 'Adidas Ultraboost', 'Giày thể thao Adidas Ultraboost hiệu suất cao', 180.00, 30, 1, '1745336691_6807b973a32bd.jpg', '2025-04-22 13:47:13', '2025-04-22 15:44:51'),
(3, 'Vagabond Amina Derby Oxford shoes - Hàng xuất xịn da thật', 'Giày tây lịch lãm dành cho công sở', 200.00, 20, 2, '1745336770_6807b9c29e10c.jpg', '2025-04-22 13:47:13', '2025-04-22 15:46:10'),
(4, 'Dép đế xuông lotso', 'Dép đế xuông, phong cách cổ điển, thanh lịch của lotso\r\n', 90.00, 40, 3, '1745337331_6807bbf3ac8c4.png', '2025-04-22 13:47:13', '2025-04-22 15:55:31'),
(5, 'Converse Chuck Taylor', 'Sneaker huyền thoại Converse Chuck Taylor', 85.00, 25, 3, 'converse_chuck.jpg', '2025-04-22 13:47:13', '2025-04-22 13:47:13'),
(6, 'dép lê lotso cho bé gái', 'ádfsdfa', 123456.00, 123, 6, '1745337145_6807bb399b3bb.png', '2025-04-22 13:55:27', '2025-04-22 15:53:10'),
(7, 'nike new balance', 'ádfsdfa', 123456.00, 123, 1, '1745336851_6807ba132547d.jpg', '2025-04-22 13:57:37', '2025-04-22 15:47:31'),
(8, 'cross sweetie fox', '', 12345.00, 12345, 4, '1745337647_6807bd2f89d70.png', '2025-04-22 14:01:04', '2025-04-22 16:00:47'),
(9, 'crocs lotso cho bé gái ', 'dép crocs dễ thương, năng động ', 12345.00, 12345, 4, '1745337249_6807bba18e489.png', '2025-04-22 14:01:08', '2025-04-22 15:54:09'),
(10, 'Nike Air Max', 'Giày thể thao Nike Air Max nhẹ, êm ái', 150.00, 50, 1, '1745331499_6807a52ba97a6.jpg', '2025-04-22 14:18:19', '2025-04-22 14:18:19'),
(11, 'dép tông sweetie fox ', 'sxdfvgdg', 123987.00, 1234, 5, '1745334563_6807b123acf60.jpg', '2025-04-22 15:09:23', '2025-04-22 15:42:37'),
(12, 'dép xỏ ngón lotso huyền bí', 'dép xỏ ngón huyền bí lotso mang phong cách sạch sẽ dễ thương ', 222222.00, 34, 5, '1745337429_6807bc55f2f5a.png', '2025-04-22 15:57:09', '2025-04-22 15:57:09'),
(13, 'Dép lông lotso', 'dép lông lotso mang đến sự ấm áp cho mùa đông ', 333333.00, 33, 8, '1745337570_6807bce223a03.png', '2025-04-22 15:59:30', '2025-04-22 15:59:30'),
(14, 'Dép lông lotso pro', 'gấp đôi yêu thương ', 555555.00, 555, 8, '1745337625_6807bd1970033.png', '2025-04-22 16:00:25', '2025-04-22 16:00:25'),
(15, 'lego deco lotso ', 'một món phụ kiện độc đáo dễ thương cho không gian của bạn\r\n', 66666.00, 66, 9, '1745337755_6807bd9b735f9.png', '2025-04-22 16:02:35', '2025-04-22 16:02:35'),
(16, 'Dép Crocs Gấu Dâu Nữ ', 'Dép Bánh Mì Gấu Dâu Nữ Nhựa EVA Siêu Nhẹ \r\nĐúc Độn 5cm Êm Chân Chống Nước Chống Trượt - DL221', 854321.00, 12, 4, '1745337851_6807bdfbdbaf2.png', '2025-04-22 16:04:11', '2025-04-22 16:04:11'),
(17, 'Dép công chúa Lotso phong cách hoàng gia Anh', 'Dép Bánh Mì Gấu Dâu Nữ Nhựa EVA Siêu Nhẹ \r\nĐúc Độn 5cm Êm Chân Chống Nước Chống Trượt - DL221', 100000.00, 590, 7, '1745337965_6807be6d1964f.png', '2025-04-22 16:06:05', '2025-04-22 16:06:05'),
(18, 'Dép công chúa lotso tím mộng mơ ', 'chất liệu: nhựa eva siêu nhẹ \r\nĐúc Độn 5cm Êm Chân Chống Nước Chống Trượt', 120000.00, 47, 7, '1745338082_6807bee2b37ea.png', '2025-04-22 16:08:02', '2025-04-22 16:08:02'),
(19, 'Dép bánh mì gấu dâu đa năng', 'dép bánh mì được đúc cao su nguyên khối, đế độn 5cm siêu hack dáng, mang đến sự êm ái cho đôi chân của bạn ', 250000.00, 70, 10, '1745338194_6807bf5227bca.png', '2025-04-22 16:09:54', '2025-04-22 16:09:54'),
(20, 'Dép quai ngang gấu dâu công chúa', 'dép quai ngang lotso được làm từ da tai voi mang đến sự chân thật cho đôi chân của bạn ', 230000.00, 90, 7, '1745338293_6807bfb56e62d.png', '2025-04-22 16:11:33', '2025-04-22 16:11:33'),
(22, '【POSE Pioneer x Disney Lotso】 POSE Pioneer Đế giày EVA thiết kế độc đáo chống trượt đế dàyP2244633', '', 600000.00, 20, 4, '1745347448_6807e378be375.jpg', '2025-04-22 18:44:08', '2025-04-22 18:44:08');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` int NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 2, 5, 'Giày rất thoải mái và đẹp', '2025-04-22 19:26:07'),
(2, 2, 3, 4, 'Chất lượng tốt, đáng giá tiền', '2025-04-22 19:26:07'),
(3, 3, 2, 5, 'Rất hài lòng với sản phẩm', '2025-04-22 19:26:07');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `key` varchar(50) NOT NULL,
  `value` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `address` text,
  `city` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `newsletter` tinyint(1) DEFAULT '0',
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `firstname`, `lastname`, `address`, `city`, `phone`, `newsletter`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'admin', NULL, NULL, NULL, NULL, NULL, 0, 'admin@example.com', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'admin', '2025-04-22 13:47:13'),
(2, 'john_doe', NULL, NULL, NULL, NULL, NULL, 0, 'john@example.com', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'customer', '2025-04-22 13:47:13'),
(3, 'jane_smith', NULL, NULL, NULL, NULL, NULL, 0, 'jane@example.com', '89e01536ac207279409d4de1e5253e01f4a1769e696db0d6062ca9b8f56767c8', 'customer', '2025-04-22 13:47:13'),
(4, 'admin', NULL, NULL, NULL, NULL, NULL, 0, 'admin@lotso.com', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-04-22 16:59:56'),
(5, 'hungbiuu', 'Huy Hùng', 'Phạm', '66 Hồ Tùng Mậu, Mai Dịch', 'Hà Nội', '0988381713', 1, 'hungbiuu@gmail.com', '$2y$10$qX0CfUXQ/jZvsfYwntxfl.maT3h6bixqv8AIbBMBJ0dGgH.eB5CKO', 'customer', '2025-04-22 20:35:27'),
(6, 'nguyenvana', 'Văn A', 'Nguyễn', '66 Hồ Tùng Mậu, Mai Dịch', 'Hà Nội', '0988381713', 1, 'nguyenvana@gmail.com', '$2y$10$IKMFAwupDCNY1NQhOVgA/uAnfYgl1OmqksoRPdLp9oC3KhuPlAGkq', 'customer', '2025-04-22 20:37:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`key`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
