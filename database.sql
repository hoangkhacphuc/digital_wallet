-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 16, 2022 lúc 10:55 PM
-- Phiên bản máy phục vụ: 10.4.21-MariaDB
-- Phiên bản PHP: 7.4.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `digital_wallet`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `abnormal`
--

CREATE TABLE `abnormal` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customer`
--

CREATE TABLE `customer` (
  `id` int(11) NOT NULL,
  `user` varchar(25) DEFAULT NULL,
  `pass` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT '',
  `email` varchar(255) NOT NULL,
  `date_of_birth` date NOT NULL,
  `address` varchar(255) DEFAULT '',
  `money` int(11) DEFAULT 0,
  `date_created` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_type` int(11) DEFAULT 0,
  `is_active` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `customer`
--

INSERT INTO `customer` (`id`, `user`, `pass`, `full_name`, `phone_number`, `email`, `date_of_birth`, `address`, `money`, `date_created`, `user_type`, `is_active`) VALUES
(1000000016, '1000000016', '4c61f87237d809d5ae3e09650d7f8407', 'Nguyễn Văn A', '0123123123', 'demo@gmail.com', '2022-02-24', 'Xã Ân Hòa', 112060000, '2022-05-16 20:52:07', 0, 1),
(1000000018, '1000000018', 'e10adc3949ba59abbe56e057f20f883e', 'Nguyễn Văn B', '0123123124', 'demo2@gmail.com', '1111-11-11', 'Xã Ân Hòa', 0, '2022-05-16 20:52:25', 0, 1),
(1000000019, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'ADMIN', '', '', '0000-00-00', '', 0, '2022-05-16 18:16:38', 1, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `identity_card`
--

CREATE TABLE `identity_card` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `font_identity_card` varchar(255) DEFAULT '',
  `back_identity_card` varchar(255) DEFAULT '',
  `confirm` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `identity_card`
--

INSERT INTO `identity_card` (`id`, `customer_id`, `font_identity_card`, `back_identity_card`, `confirm`) VALUES
(12, 1000000016, './Image/upload/cccdTruoc_99c7f328405180615bc5ebd2f1dd3992.jpg', './Image/upload/cccdSau_0UTgrJfye8.jpg', 2),
(14, 1000000018, './Image/upload/cccdTruoc_99c7f328405180615bc5ebd2f1dd3992.jpg', './Image/upload/cccdSau_cac994ae67d6410bfa47e0ed386d3838.jpg', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `withdraw_money`
--

CREATE TABLE `withdraw_money` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `amount_of_money` int(11) NOT NULL,
  `card_number` varchar(30) NOT NULL,
  `expiration_date` date NOT NULL,
  `cvv` varchar(3) NOT NULL,
  `note` varchar(255) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `confirm` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `withdraw_money`
--

INSERT INTO `withdraw_money` (`id`, `customer_id`, `amount_of_money`, `card_number`, `expiration_date`, `cvv`, `note`, `date_created`, `confirm`) VALUES
(1, 1000000016, 52500000, '111111', '2022-10-10', '411', '', '2022-05-16 20:42:19', 1),
(2, 1000000016, 52500000, '111111', '2022-10-10', '411', '', '2022-05-16 20:46:46', 1),
(3, 1000000016, 52500000, '111111', '2022-10-10', '411', '', '2022-05-16 20:50:07', 1),
(4, 1000000016, 52500000, '111111', '2022-10-10', '411', '1000000016 chuyen tien', '2022-05-16 20:35:35', 0),
(5, 1000000016, 2100000, '111111', '2022-10-10', '411', '1000000016 chuyen tien', '2022-05-16 16:10:10', 1),
(6, 1000000016, 2100000, '111111', '2022-10-10', '411', '1000000016 chuyen tien', '2022-05-16 16:24:59', 1),
(7, 1000000016, 2100000, '111111', '2022-10-10', '411', '1000000016 chuyen tien', '2022-05-16 16:26:57', 1),
(8, 1000000016, 2100000, '111111', '2022-10-10', '411', '1000000016 chuyen tien', '2022-05-16 16:28:29', 1),
(9, 1000000016, 5250000, '111111', '2022-10-10', '411', '1000000016 chuyen tien', '2022-05-16 20:35:35', 0),
(10, 1000000016, 105000, '111111', '2022-10-10', '411', '1000000016 chuyen tien', '2022-05-16 16:31:20', 1),
(11, 1000000016, 105000, '111111', '2022-10-10', '411', 'Hoàng Khắc Phúc chuyen tien', '2022-05-16 16:31:46', 1),
(12, 1000000016, 105000, '111111', '2022-10-10', '411', '', '2022-05-16 16:32:21', 1),
(13, 1000000016, 105000, '111111', '2022-10-10', '411', 'Hoàng Khắc Phúc chuyen tien', '2022-05-16 16:32:58', 1),
(14, 1000000016, 105000, '111111', '2022-10-10', '411', '', '2022-05-16 16:40:24', 1),
(15, 1000000016, 105000, '111111', '2022-10-10', '411', '', '2022-05-16 18:01:58', 2),
(16, 1000000016, 105000, '111111', '2022-10-10', '411', '', '2022-05-16 16:42:26', 1),
(17, 1000000016, 105000, '111111', '2022-10-10', '411', '', '2022-05-16 16:42:41', 1);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `abnormal`
--
ALTER TABLE `abnormal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Chỉ mục cho bảng `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `identity_card`
--
ALTER TABLE `identity_card`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Chỉ mục cho bảng `withdraw_money`
--
ALTER TABLE `withdraw_money`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `abnormal`
--
ALTER TABLE `abnormal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000000020;

--
-- AUTO_INCREMENT cho bảng `identity_card`
--
ALTER TABLE `identity_card`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `withdraw_money`
--
ALTER TABLE `withdraw_money`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `abnormal`
--
ALTER TABLE `abnormal`
  ADD CONSTRAINT `abnormal_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`);

--
-- Các ràng buộc cho bảng `identity_card`
--
ALTER TABLE `identity_card`
  ADD CONSTRAINT `identity_card_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`);

--
-- Các ràng buộc cho bảng `withdraw_money`
--
ALTER TABLE `withdraw_money`
  ADD CONSTRAINT `withdraw_money_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
