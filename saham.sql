-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 02, 2024 at 11:12 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `saham`
--

-- --------------------------------------------------------

--
-- Table structure for table `klasifikasi`
--

CREATE TABLE `klasifikasi` (
  `id` int(11) NOT NULL,
  `nilai_f_skore` varchar(300) NOT NULL,
  `kategori` varchar(300) NOT NULL,
  `keterangan` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `klasifikasi`
--

INSERT INTO `klasifikasi` (`id`, `nilai_f_skore`, `kategori`, `keterangan`) VALUES
(1, '0, 1, 2, 3', 'Low Performance', 'Tidak Direkomendasikan Untuk Berinvestasi'),
(2, '4, 5, 6, 7', 'Medium Performance', 'Direkomendasikan Untuk Berinvestasi'),
(3, '8, 9', 'High Performance', 'Sangat Direkomendasikan Untuk Berinvestasi');

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `id` int(11) NOT NULL,
  `kode_saham` varchar(300) NOT NULL,
  `tahun` varchar(300) NOT NULL,
  `net_income` varchar(300) DEFAULT NULL,
  `operating_cashflow` varchar(300) DEFAULT NULL,
  `return_on_asset_previous` varchar(300) DEFAULT NULL,
  `return_on_asset_now` varchar(300) DEFAULT NULL,
  `quality_of_earning` varchar(300) DEFAULT NULL,
  `long_term_debt_to_asset_previous` varchar(300) DEFAULT NULL,
  `long_term_debt_to_asset_now` varchar(300) DEFAULT NULL,
  `current_ratio_previous` varchar(300) DEFAULT NULL,
  `current_ratio_now` varchar(300) DEFAULT NULL,
  `outstanding_shares_previous` varchar(300) DEFAULT NULL,
  `outstanding_shares_now` varchar(300) DEFAULT NULL,
  `gross_margin_previous` varchar(300) DEFAULT NULL,
  `gross_margin_now` varchar(300) DEFAULT NULL,
  `asset_turnover_previous` varchar(300) DEFAULT NULL,
  `asset_turnover_now` varchar(300) DEFAULT NULL,
  `skor` varchar(300) DEFAULT NULL,
  `email` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`) VALUES
(1, 'admin', 'iwanstgb@gmail.com', '$2y$10$iqGGk6taboEK18XJGO//1eYFDwXiRXVNAgnaqnIps9wcNZfYRgs9u', 'admin'),
(2, 'user2', 'user2@mail.com', '$2y$10$BCSBQcyU8eYxP78LlpnwYOZFeF.sIajdKCho26v8zuwPhj4ZM52vi', 'user'),
(3, 'user1', 'user1@mail.com', '$2y$10$9iPjy.m52Dt7dJYapjvAQeYC1/osz8ewkjX0IL3WRlwOQxLnIX8ea', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `klasifikasi`
--
ALTER TABLE `klasifikasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `klasifikasi`
--
ALTER TABLE `klasifikasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
