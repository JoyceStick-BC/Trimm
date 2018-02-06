-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 06, 2018 at 03:19 PM
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
(1, 'cus_CH55BkMAcIvPDv', 'acct_1BsX1wLUfpNsykyv', 1500, 'penguin', '2018-02-06 20:19:09', '2018-02-06 20:19:09');

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
(1, 'aliem@bc.edu', 'Emaad Ali', 'emaadali', '$2y$10$QtDAANODnzxIfseevnWEI.YdIgGERvuF17wAOJLGqVIf6zEdMY7b6', 'cus_CH55BkMAcIvPDv', 'acct_1BsX1wLUfpNsykyv', '2018-01-27 23:45:50', '2018-02-06 20:02:44'),
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
