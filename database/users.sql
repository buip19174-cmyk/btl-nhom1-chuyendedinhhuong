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
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `sdt` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `sdt`, `email`, `password`) VALUES
(1, 'admin', '0123456789', 'admin@example.com', '$2y$10$fpdXEXlRkMeF8wDKbvqAluupLTQPK0Eo6zx9v/h/Re/QwYtqcyALm'),
(2, 'Bùi Anh', '0899420684', '123@gmai.com', '$2y$10$aIdeIaOYZBoITJq/q.lDreWlCMsAMTh9c8Dxkn3jq.kju/K8ZzXLm'),
(3, 'Anh', '0357968042', 'newmail@gmail.com', '$2y$10$HcLC/uyEuBr5tAEAnCk/eeNJYZeYhn2PcGQqi7XcBr8pRpXViFFDK'),
(5, 'AN', '0365237822', '122@gmail.com', '$2y$10$wFt6Pf6y20bkF1X0hsgaXODtybEnVKw8QWNTnX58K5yAs7bQz5wuO'),
(6, 'Minh', '0348532395', 'min@gmail.com', '$2y$10$8sNBVdtyf9mAuTkzmkllZeYRmbVriU6.OuEg8nEO5EYwIsd1xHjLK'),
(7, 'Anhh', '0899420685', '234@gmail.com', '$2y$10$6rE2WuIxrL/wCSsfs2ReneFqjomwpaHlwPedjKue0EJ5qm3ywSqpa');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
