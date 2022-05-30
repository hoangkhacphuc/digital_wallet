CREATE DATABASE IF NOT EXISTS `digital_wallet` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `digital_wallet`;

CREATE TABLE `customer` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user` varchar(25) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT "",
  `email` varchar(255) NOT NULL,
  `date_of_birth` date NOT NULL,
  `address` varchar(255) DEFAULT "",
  `money` int DEFAULT 0,
  `date_created` timestamp,
  `user_type` int DEFAULT 0,
  `is_active` int DEFAULT 1
);

CREATE TABLE `abnormal` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `date_created` timestamp
);

CREATE TABLE `identity_card` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `font_identity_card` varchar(255) DEFAULT "",
  `back_identity_card` varchar(255) DEFAULT "",
  `confirm` int DEFAULT 0
);

CREATE TABLE `withdraw_money` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `amount_of_money` int NOT NULL,
  `card_number` varchar(30) NOT NULL,
  `expiration_date` date NOT NULL,
  `cvv` varchar(3) NOT NULL,
  `note` varchar(255) NOT NULL,
  `date_created` timestamp,
  `confirm` int DEFAULT 0
);

CREATE TABLE `depositing` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `amount_of_money` int NOT NULL,
  `card_number` varchar(30) NOT NULL,
  `expiration_date` date NOT NULL,
  `cvv` varchar(3) NOT NULL,
  `date_created` timestamp
);

CREATE TABLE `recharge_card` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `denominations` int NOT NULL,
  `operator` int DEFAULT 0 COMMENT '0 : Viettel - 1:Mobifone - 2:Vinaphone',
  `code` varchar(15),
  `date_created` timestamp
);

ALTER TABLE `abnormal` ADD FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`);

ALTER TABLE `identity_card` ADD FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`);

ALTER TABLE `withdraw_money` ADD FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`);

ALTER TABLE `depositing` ADD FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`);

ALTER TABLE `recharge_card` ADD FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`);

INSERT INTO `customer` (`id`, `user`, `pass`, `full_name`, `phone_number`, `email`, `date_of_birth`, `address`, `money`, `date_created`, `user_type`, `is_active`) VALUES
(1000000016, '1000000016', '4c61f87237d809d5ae3e09650d7f8407', 'Nguyễn Văn A', '0123123123', 'demo@gmail.com', '2022-02-24', 'Xã Ân Hòa', 112060000, '2022-05-16 20:52:07', 0, 1),
(1000000018, '1000000018', 'e10adc3949ba59abbe56e057f20f883e', 'Nguyễn Văn B', '0123123124', 'demo2@gmail.com', '1111-11-11', 'Xã Ân Hòa', 0, '2022-05-16 20:52:25', 0, 1),
(1000000019, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'ADMIN', '', '', '0000-00-00', '', 0, '2022-05-16 18:16:38', 1, 1);

INSERT INTO `identity_card` (`id`, `customer_id`, `font_identity_card`, `back_identity_card`, `confirm`) VALUES
(12, 1000000016, './Image/upload/cccdTruoc_99c7f328405180615bc5ebd2f1dd3992.jpg', './Image/upload/cccdSau_0UTgrJfye8.jpg', 2),
(14, 1000000018, './Image/upload/cccdTruoc_99c7f328405180615bc5ebd2f1dd3992.jpg', './Image/upload/cccdSau_cac994ae67d6410bfa47e0ed386d3838.jpg', 2);

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
(11, 1000000016, 105000, '111111', '2022-10-10', '411', 'chuyen tien', '2022-05-16 16:31:46', 1),
(12, 1000000016, 105000, '111111', '2022-10-10', '411', '', '2022-05-16 16:32:21', 1),
(13, 1000000016, 105000, '111111', '2022-10-10', '411', 'chuyen tien', '2022-05-16 16:32:58', 1),
(14, 1000000016, 105000, '111111', '2022-10-10', '411', '', '2022-05-16 16:40:24', 1),
(15, 1000000016, 105000, '111111', '2022-10-10', '411', '', '2022-05-16 18:01:58', 2),
(16, 1000000016, 105000, '111111', '2022-10-10', '411', '', '2022-05-16 16:42:26', 1),
(17, 1000000016, 105000, '111111', '2022-10-10', '411', '', '2022-05-16 16:42:41', 1);

