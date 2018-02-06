-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 05, 2018 at 07:56 PM
-- Server version: 10.1.28-MariaDB
-- PHP Version: 5.6.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `trimm`
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
  `version` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `downloads` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bundles`
--

INSERT INTO `bundles` (`id`, `user`, `bundleName`, `description`, `hash`, `version`, `created_at`, `updated_at`, `downloads`) VALUES
(1, 'kyle', 'car', NULL, 'no hash yet', 0, '2018-01-30 03:18:57', '0000-00-00 00:00:00', 0),
(2, 'emaadali', 'penguin', NULL, 'no hash yet', 0, '2018-01-30 03:19:03', '0000-00-00 00:00:00', 0),
(3, 'kyle', 'toy', NULL, 'askjfdaf', 0, '2018-01-30 16:57:13', '0000-00-00 00:00:00', 0),
(4, 'emaadali', 'car', NULL, '', 0, '2018-01-30 16:59:08', '0000-00-00 00:00:00', 0),
(5, 'emaadali', 'i have a really big car', NULL, '', 0, '2018-01-30 16:59:26', '0000-00-00 00:00:00', 0),
(6, 'emaadali', '', NULL, '52b08d0621e783815ce1a10ecde68f3c', 1, '2018-01-31 05:12:28', '2018-01-31 05:12:28', 0),
(7, 'emaadali', 'createIndex_code', NULL, 'cf5e541e2bf7548ef287178beec37d06', 1, '2018-01-31 05:13:28', '2018-01-31 05:13:28', 0),
(8, 'emaadali', 'sketch', NULL, 'bea5a6c26a9d2c610fda610c66226009', 1, '2018-01-31 05:16:41', '2018-01-31 05:16:41', 0),
(9, 'emaadali', 'createIndex_code', NULL, '3b6c334d3f82de8353442a6ff1a5055b', 1, '2018-01-31 06:26:14', '2018-01-31 06:26:14', 0),
(10, 'emaadali', 'createIndex_code', NULL, '99c5e0afa5a7359c96a136dbad6db5eb', 1, '2018-01-31 06:26:50', '2018-01-31 06:26:50', 0),
(11, 'emaadali', 'createIndex_code', NULL, '89ef0b2e45d1e62018bbe4e2540f2fba', 1, '2018-01-31 06:26:54', '2018-01-31 06:26:54', 0),
(12, 'emaadali', 'createIndex_code', NULL, 'c8e3bc477692d7ac75d8d52746489d46', 1, '2018-01-31 06:27:05', '2018-01-31 06:27:05', 0),
(13, 'emaadali', 'createIndex_code', NULL, '60331e98717c95123dade40bb68021e8', 1, '2018-01-31 06:27:58', '2018-01-31 06:27:58', 0),
(14, 'emaadali', 'availableHours (1)', 'Test upload', '556741100ebf6ba2dc9e4c599b2c0d59', 1, '2018-01-31 06:30:26', '2018-01-31 06:30:26', 0);

-- --------------------------------------------------------

--
-- Table structure for table `SocialMedia`
--

CREATE TABLE `SocialMedia` (
  `id` int(11) NOT NULL,
  `user` varchar(500) NOT NULL,
  `link` text NOT NULL,
  `platform` varchar(500) NOT NULL,
  `software` text NOT NULL,
  `created_At` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_At` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `SocialMedia`
--

INSERT INTO `SocialMedia` (`id`, `user`, `link`, `platform`, `software`, `created_At`, `updated_At`) VALUES
(1, 'ricky', 'cachemonet.com', 'dsfsf', 'asdfsfd', '2018-02-02 17:43:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Software`
--

CREATE TABLE `Software` (
  `id` int(11) NOT NULL,
  `user` varchar(500) NOT NULL,
  `software` text NOT NULL,
  `created_At` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_At` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Software`
--

INSERT INTO `Software` (`id`, `user`, `software`, `created_At`, `updated_At`) VALUES
(1, 'ricky', 'sfdfdasfa', '2018-02-02 18:23:23', NULL);

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
  `bio` text NOT NULL,
  `img` varchar(64) NOT NULL DEFAULT 'https://tinyurl.com/yctexzpl',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `name`, `username`, `password`, `bio`, `img`, `created_at`, `updated_at`) VALUES
(1, 'aliem@bc.edu', 'Emaad Ali', 'emaadali', '$2y$10$QtDAANODnzxIfseevnWEI.YdIgGERvuF17wAOJLGqVIf6zEdMY7b6', '', 'https://tinyurl.com/yctexzpl', '2018-01-27 23:45:50', '2018-01-30 08:16:08'),
(2, 'kyle@efds.com', 'earefdc', 'kyle', 'sdfgx', '', 'https://tinyurl.com/yctexzpl', '2018-01-30 00:55:50', NULL),
(3, 'yangud@bc.edu', 'Ricky', 'ricky', '$2y$10$Z107nRsJcY5wDraclxFf1OERzDP7iO897jHx2Hvo6EGbWYn2mzouG', 'Hijkldfsjklsdfjlkfdsljkfdsjklfdsjks dfjsklf jsdlf jsdlkf jlsdkfj lskdj flksdjd ldsf f', 'https://avatars1.githubusercontent.com/u/30324472?s=460&v=4', '2018-01-31 23:13:47', '2018-01-31 23:13:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bundles`
--
ALTER TABLE `bundles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `SocialMedia`
--
ALTER TABLE `SocialMedia`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Software`
--
ALTER TABLE `Software`
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
-- AUTO_INCREMENT for table `SocialMedia`
--
ALTER TABLE `SocialMedia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Software`
--
ALTER TABLE `Software`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
