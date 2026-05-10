-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 20, 2026 lúc 07:58 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `db_btl5`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_stories`
--

CREATE TABLE `user_stories` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `story_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `user_stories`
--

INSERT INTO `user_stories` (`id`, `user_id`, `story_id`, `created_at`) VALUES
(14, 2, 344, '2025-12-19 04:46:09'),
(15, 2, 345, '2025-12-19 05:51:07'),
(16, 2, 354, '2025-12-19 07:02:28'),
(17, 2, 346, '2025-12-19 07:20:47'),
(18, 2, 347, '2025-12-20 02:03:34'),
(19, 2, 72, '2025-12-20 02:07:50'),
(20, 2, 248, '2025-12-20 02:07:54'),
(21, 2, 147, '2025-12-20 15:42:02'),
(22, 2, 73, '2025-12-20 16:03:15'),
(23, 7, 346, '2025-12-21 02:01:25');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `user_stories`
--
ALTER TABLE `user_stories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `story_id` (`story_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `user_stories`
--
ALTER TABLE `user_stories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `user_stories`
--
ALTER TABLE `user_stories`
  ADD CONSTRAINT `user_stories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_stories_ibfk_2` FOREIGN KEY (`story_id`) REFERENCES `stories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
