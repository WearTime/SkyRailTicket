-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2025 at 07:23 PM
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
-- Database: `tiketskyrail`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `id` int(11) NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `pembayaranId` int(11) DEFAULT NULL,
  `ticketId` int(11) DEFAULT NULL,
  `Status` enum('Diterima','Ditolak','Menunggu') NOT NULL,
  `tanggalBooking` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `host`
--

CREATE TABLE `host` (
  `id` int(11) NOT NULL,
  `nameHost` varchar(255) DEFAULT NULL,
  `tipeHost` enum('Pesawat','Kereta','Bus','Kapal') DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `host`
--

INSERT INTO `host` (`id`, `nameHost`, `tipeHost`, `logo`) VALUES
(1, 'Air Asia', 'Pesawat', 'AirAsia_New_Logo.svg.png');

-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

CREATE TABLE `ticket` (
  `id` int(11) NOT NULL,
  `namaTicket` varchar(255) DEFAULT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `luarNegeri` tinyint(1) DEFAULT NULL,
  `tipeTicket` varchar(255) DEFAULT NULL,
  `kelasTicket` varchar(255) DEFAULT NULL,
  `hostTicket` int(11) DEFAULT NULL,
  `harga` varchar(255) DEFAULT NULL,
  `destinasi` varchar(255) DEFAULT NULL,
  `tempatBerangkat` varchar(255) DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  `stok` int(11) DEFAULT NULL,
  `penumpangMax` varchar(255) DEFAULT NULL,
  `imageTujuan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticket`
--

INSERT INTO `ticket` (`id`, `namaTicket`, `deskripsi`, `luarNegeri`, `tipeTicket`, `kelasTicket`, `hostTicket`, `harga`, `destinasi`, `tempatBerangkat`, `tanggal`, `stok`, `penumpangMax`, `imageTujuan`) VALUES
(2, 'Luar NEgeri 1', 'dada', 0, 'Pesawat', 'Ekonomi', 1, '7000000', 'Singapura', 'Jakarta', '2025-05-03 23:48:00', 1, '1', '681990602230f_1746505824.jpg'),
(3, 'Luar Negeri 2', 'kokok', 1, 'Pesawat', 'Ekonomi', 1, '9000000', 'China', 'Jakarta', '0000-00-00 00:00:00', 1, '4', '6819953242050_1746507058.jpeg'),
(4, 'JAWA JAWA', 'ini adalah JAWA JAWA JAWA JAWA JAWA', 0, 'Pesawat', 'Bisnis', 1, '10000000', 'Jawa', 'Bali', '2025-05-07 01:51:00', 5, '3', '681abd1d7b5d5_1746582813.jpg'),
(5, 'PADANG', 'Ini Ticket Ke Padang', 1, 'Pesawat', 'First Class', 1, '7000000', 'PADANG', 'JAKARTA BARAT', '2025-05-15 00:08:00', 3, '3', '6825933fd256e_1747292991.jpeg'),
(6, 'Test1', 'hhahhh', 1, 'Pesawat', 'Ekonomi', 1, '500000', 'ACEH BARAT', 'ACEH BARAT', '2025-05-12 18:57:00', 1, '4', '682aba416ea72_1747630657.jpeg'),
(7, 'luar negeri mamak', 'sdadada', 1, 'Pesawat', 'Ekonomi', 1, '7000000', 'Malaysia', 'JAKARTA PUSAT', '2025-05-19 14:28:00', 2, '4', '682adda522deb_1747639717.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `noHp` varchar(255) DEFAULT NULL,
  `role` enum('Admin','Member') DEFAULT NULL,
  `profile_img` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `noHp`, `role`, `profile_img`, `created_at`) VALUES
(1, 'admin', '$2y$10$R7nQ4uBtrIdqUL.HplG/1erGeliz/ypKXOfJkbSQ8fVGDIWSINBHK', 'admin@gmail.com', NULL, 'Admin', NULL, '2025-05-06 04:08:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`),
  ADD KEY `ticketId` (`ticketId`);

--
-- Indexes for table `host`
--
ALTER TABLE `host`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hostTicket` (`hostTicket`);

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
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `host`
--
ALTER TABLE `host`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`pembayaranId`) REFERENCES `pembayaran` (`id`),
  ADD CONSTRAINT `booking_ibfk_3` FOREIGN KEY (`ticketId`) REFERENCES `ticket` (`id`);

--
-- Constraints for table `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `ticket_ibfk_1` FOREIGN KEY (`hostTicket`) REFERENCES `host` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
