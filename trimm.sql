-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 07, 2018 at 05:47 PM
-- Server version: 10.1.25-MariaDB
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bundles`
--

-- --------------------------------------------------------

--
-- Table structure for table `bundles`
--

CREATE TABLE `bundles` (
  `id` int(11) NOT NULL,
  `user` varchar(50) NOT NULL,
  `bundleName` varchar(50) NOT NULL,
  `description` text,
  `hash` varchar(1000) NOT NULL,
  `price` int(11) DEFAULT NULL,
  `version` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bundles`
--

INSERT INTO `bundles` (`id`, `user`, `bundleName`, `description`, `hash`, `price`, `version`, `created_at`, `updated_at`) VALUES
(1, 'kyle', 'car', NULL, 'no hash yet', NULL, 0, '2018-01-30 03:18:57', '0000-00-00 00:00:00'),
(2, 'emaadali', 'penguin', NULL, 'no hash yet', 1500, 0, '2018-02-05 23:23:19', '0000-00-00 00:00:00'),
(3, 'kyle', 'toy', NULL, 'askjfdaf', NULL, 0, '2018-01-30 16:57:13', '0000-00-00 00:00:00'),
(4, 'emaadali', 'car', NULL, '', NULL, 0, '2018-01-30 16:59:08', '0000-00-00 00:00:00'),
(5, 'emaadali', 'i have a really big car', NULL, '', NULL, 0, '2018-01-30 16:59:26', '0000-00-00 00:00:00'),
(6, 'emaadali', '', NULL, '52b08d0621e783815ce1a10ecde68f3c', NULL, 1, '2018-01-31 05:12:28', '2018-01-31 05:12:28'),
(7, 'emaadali', 'createIndex_code', NULL, 'cf5e541e2bf7548ef287178beec37d06', NULL, 1, '2018-01-31 05:13:28', '2018-01-31 05:13:28'),
(8, 'emaadali', 'sketch', NULL, 'bea5a6c26a9d2c610fda610c66226009', NULL, 1, '2018-01-31 05:16:41', '2018-01-31 05:16:41'),
(9, 'emaadali', 'createIndex_code', NULL, '3b6c334d3f82de8353442a6ff1a5055b', NULL, 1, '2018-01-31 06:26:14', '2018-01-31 06:26:14'),
(10, 'emaadali', 'createIndex_code', NULL, '99c5e0afa5a7359c96a136dbad6db5eb', NULL, 1, '2018-01-31 06:26:50', '2018-01-31 06:26:50'),
(11, 'emaadali', 'createIndex_code', NULL, '89ef0b2e45d1e62018bbe4e2540f2fba', NULL, 1, '2018-01-31 06:26:54', '2018-01-31 06:26:54'),
(12, 'emaadali', 'createIndex_code', NULL, 'c8e3bc477692d7ac75d8d52746489d46', NULL, 1, '2018-01-31 06:27:05', '2018-01-31 06:27:05'),
(13, 'emaadali', 'createIndex_code', NULL, '60331e98717c95123dade40bb68021e8', NULL, 1, '2018-01-31 06:27:58', '2018-01-31 06:27:58'),
(14, 'emaadali', 'availableHours (1)', 'Test upload', '556741100ebf6ba2dc9e4c599b2c0d59', NULL, 1, '2018-01-31 06:30:26', '2018-01-31 06:30:26');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `buyer_card_id` varchar(1000) NOT NULL,
  `seller_acct_id` varchar(1000) NOT NULL,
  `amount` int(11) NOT NULL,
  `bundleName` varchar(1000) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`id`, `buyer_card_id`, `seller_acct_id`, `amount`, `bundleName`, `created_at`, `updated_at`) VALUES
(1, 'cus_CH55BkMAcIvPDv', 'acct_1BsX1wLUfpNsykyv', 1500, 'penguin', '2018-02-06 20:19:09', '2018-02-06 20:19:09'),
(2, 'cus_CH55BkMAcIvPDv', 'acct_1BsX1wLUfpNsykyv', 1500, 'penguin', '2018-02-06 20:23:40', '2018-02-06 20:23:40'),
(3, 'cus_CH55BkMAcIvPDv', 'acct_1BsX1wLUfpNsykyv', 1500, 'penguin', '2018-02-07 22:40:35', '2018-02-07 22:40:35'),
(4, 'cus_CH55BkMAcIvPDv', 'acct_1BsX1wLUfpNsykyv', 1500, 'penguin', '2018-02-07 22:41:54', '2018-02-07 22:41:54'),
(5, 'cus_CH55BkMAcIvPDv', 'acct_1BsX1wLUfpNsykyv', 1500, 'penguin', '2018-02-07 22:45:16', '2018-02-07 22:45:16');

-- --------------------------------------------------------

--
-- Table structure for table `publicKeys`
--

CREATE TABLE `publicKeys` (
  `id` int(11) NOT NULL,
  `user` varchar(1000) NOT NULL,
  `privateKey` varchar(1000) NOT NULL,
  `type` varchar(1000) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `publicKeys`
--

INSERT INTO `publicKeys` (`id`, `user`, `privateKey`, `type`, `created_at`, `updated_at`) VALUES
(1, 'emaadali', 'ddec51478d5e86e65cb963020b5efea7', 'charge', '2018-02-07 16:36:25', '2018-02-07 16:36:25'),
(2, 'emaadali', 'e4de0a17aa4ce54b2df49b1a1b633b6a', 'charge', '2018-02-07 16:51:57', '2018-02-07 16:51:57'),
(3, 'emaadali', 'e7aaccb853059f679dccb5fde01e1bcd', 'charge', '2018-02-07 16:52:26', '2018-02-07 16:52:26'),
(4, 'emaadali', '22a8f8cf0d1019d252ce795a7da39cf7', 'charge', '2018-02-07 16:54:26', '2018-02-07 16:54:26'),
(5, 'emaadali', '04f3ec33f80aa6af88f03d8d2775ffc7', 'charge', '2018-02-07 16:54:41', '2018-02-07 16:54:41'),
(6, 'emaadali', '04cd77e7f0946bc359c00ca7161a0e5c', 'charge', '2018-02-07 16:54:57', '2018-02-07 16:54:57'),
(7, 'emaadali', 'c0cad5c918a2bf1b3aeeb4cf20706536', 'charge', '2018-02-07 16:55:37', '2018-02-07 16:55:37'),
(8, 'emaadali', '3cb7a12fc718570dd18cee6396de3ab4', 'charge', '2018-02-07 16:56:20', '2018-02-07 16:56:20'),
(9, 'emaadali', 'ba3b4067bd7d13a64abf5c43a47b1e3c', 'charge', '2018-02-07 16:57:28', '2018-02-07 16:57:28'),
(10, 'emaadali', '081772b8e1ce56ba414e027680a3564e', 'charge', '2018-02-07 16:58:07', '2018-02-07 16:58:07'),
(11, 'emaadali', '6f631433ecb77dbea2c93f3b341ddc4a', 'charge', '2018-02-07 16:58:44', '2018-02-07 16:58:44'),
(12, 'emaadali', '301006582c5233c5da8b4e06dc5d6aaf', 'charge', '2018-02-07 17:04:55', '2018-02-07 17:04:55'),
(13, 'emaadali', '5a0c46d6e5d9997ab98abd9696def61e', 'charge', '2018-02-07 17:09:29', '2018-02-07 17:09:29'),
(14, 'emaadali', 'afde0f4bef777f7420d8fa6e91634b33', 'charge', '2018-02-07 17:25:19', '2018-02-07 17:25:19'),
(15, 'emaadali', '9566eb5c471487a02d7cd30084fa657a', 'charge', '2018-02-07 17:25:28', '2018-02-07 17:25:28'),
(16, 'emaadali', '0eb9c6ea35eb93d9ed425614c264e021', 'charge', '2018-02-07 17:25:55', '2018-02-07 17:25:55'),
(17, 'emaadali', 'e27e50656bd09f48772e6370ee7edae7', 'charge', '2018-02-07 17:26:38', '2018-02-07 17:26:38'),
(18, 'emaadali', '2bfb40e909b850b563a91fcb0ea45a26', 'charge', '2018-02-07 17:26:55', '2018-02-07 17:26:55'),
(19, 'emaadali', '83575fdc26deb035220acfc40d0c4186', 'charge', '2018-02-07 17:27:13', '2018-02-07 17:27:13'),
(20, 'emaadali', '94ad60031fbd7594fc85bdf3397e0bf0', 'charge', '2018-02-07 17:28:08', '2018-02-07 17:28:08'),
(21, 'emaadali', '63320983344ea8c24e4a31685efebc02', 'charge', '2018-02-07 17:28:37', '2018-02-07 17:28:37'),
(22, 'emaadali', '625ea4189f5ac0e717c121ba17a8d735', 'charge', '2018-02-07 17:30:23', '2018-02-07 17:30:23'),
(23, 'emaadali', 'b5a4f1fdeb61125b146c228e689b56d2', 'charge', '2018-02-07 17:30:55', '2018-02-07 17:30:55'),
(24, 'emaadali', '57d8b54d70d8d7d05ae48577af153dea', 'charge', '2018-02-07 17:31:47', '2018-02-07 17:31:47'),
(25, 'emaadali', 'fcac5f0496e785c6fd28153dde93a55d', 'charge', '2018-02-07 17:32:37', '2018-02-07 17:32:37'),
(26, 'emaadali', '87db41bba44361bf34176b27965d8a82', 'charge', '2018-02-07 17:32:41', '2018-02-07 17:32:41'),
(27, 'emaadali', 'de818ee9bb4792ebdec401e1225a43a1', 'charge', '2018-02-07 17:34:52', '2018-02-07 17:34:52'),
(28, 'emaadali', '782102142ddb881cf285b524c5b322eb', 'charge', '2018-02-07 17:35:33', '2018-02-07 17:35:33'),
(29, 'emaadali', '26ca1c534cf26cc7b2d53a3be06cd663', 'charge', '2018-02-07 17:36:20', '2018-02-07 17:36:20'),
(30, 'emaadali', 'ce461fe0450ef8065e50aec325d963d5', 'charge', '2018-02-07 17:36:41', '2018-02-07 17:36:41'),
(31, 'emaadali', 'ae45ec0fe3de94244fd767d0c6473de2', 'charge', '2018-02-07 17:36:49', '2018-02-07 17:36:49'),
(32, 'emaadali', 'bfc8ae07ff12c44373518f414bc38c07', 'charge', '2018-02-07 17:37:41', '2018-02-07 17:37:41'),
(33, 'emaadali', '2603f009ddbaf2f13da54bb2124c01fb', 'charge', '2018-02-07 17:38:49', '2018-02-07 17:38:49'),
(34, 'emaadali', '232948e493bcd8d50176b1a6038428d0', 'charge', '2018-02-07 17:39:08', '2018-02-07 17:39:08'),
(35, 'emaadali', 'c89f4879444fb81a80b83d3248a461ef', 'charge', '2018-02-07 17:39:10', '2018-02-07 17:39:10'),
(36, 'emaadali', '27716c18b8af5882846d514ad966eca6', 'charge', '2018-02-07 17:39:45', '2018-02-07 17:39:45'),
(37, 'emaadali', '0ea7d1aee7c59bcfcaa29156f49a7ab7', 'charge', '2018-02-07 17:39:46', '2018-02-07 17:39:46'),
(38, 'emaadali', '303b42153d219f2f61d744e8b878322e', 'charge', '2018-02-07 17:40:30', '2018-02-07 17:40:30'),
(39, 'emaadali', '5e46fc927fc199898464fd7b430c8651', 'charge', '2018-02-07 17:41:48', '2018-02-07 17:41:48'),
(40, 'emaadali', '7d67b20aeaa3b06e9168f2dc1ac59338', 'charge', '2018-02-07 17:41:50', '2018-02-07 17:41:50'),
(41, 'emaadali', '4c7fa0d923cd9a73ba72eea9c103abf2', 'charge', '2018-02-07 17:43:11', '2018-02-07 17:43:11'),
(42, 'emaadali', 'eda688b8536cf357a5f9786b0a132794', 'charge', '2018-02-07 17:43:46', '2018-02-07 17:43:46'),
(43, 'emaadali', '9754c4931bcc05da99d9e5fd7bfd109a', 'charge', '2018-02-07 17:43:46', '2018-02-07 17:43:46'),
(44, 'emaadali', 'df40597cfe7ab40f688723112c5bcb5b', 'charge', '2018-02-07 17:44:12', '2018-02-07 17:44:12'),
(45, 'emaadali', 'ac86f5ba1983d6a4860d0c22ee2a7679', 'charge', '2018-02-07 17:45:06', '2018-02-07 17:45:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(1000) NOT NULL,
  `name` varchar(1000) NOT NULL,
  `username` varchar(500) NOT NULL,
  `password` varchar(1000) NOT NULL,
  `stripe_card_id` varchar(1000) DEFAULT NULL,
  `stripe_acct_id` varchar(1000) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `name`, `username`, `password`, `stripe_card_id`, `stripe_acct_id`, `created_at`, `updated_at`) VALUES
(1, 'austinbailey1114@gmail.com', 'Emaad Ali', 'emaadali', '$2y$10$QtDAANODnzxIfseevnWEI.YdIgGERvuF17wAOJLGqVIf6zEdMY7b6', 'cus_CH55BkMAcIvPDv', 'acct_1BsX1wLUfpNsykyv', '2018-01-27 23:45:50', '2018-02-06 20:02:44'),
(2, 'kyle@efds.com', 'earefdc', 'kyle', 'sdfgx', NULL, NULL, '2018-01-30 00:55:50', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bundles`
--
ALTER TABLE `bundles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `publicKeys`
--
ALTER TABLE `publicKeys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bundles`
--
ALTER TABLE `bundles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `publicKeys`
--
ALTER TABLE `publicKeys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
