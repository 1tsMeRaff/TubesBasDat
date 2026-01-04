-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 04, 2026 at 08:32 AM
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
-- Database: `hijabstore_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `ID_Detail` int(11) NOT NULL,
  `No_Transaksi` varchar(20) NOT NULL,
  `Kode_SKU` varchar(30) NOT NULL,
  `Jumlah_Beli` int(11) NOT NULL,
  `Harga_Satuan_Snapshot` int(11) NOT NULL,
  `Subtotal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_transaksi`
--

INSERT INTO `detail_transaksi` (`ID_Detail`, `No_Transaksi`, `Kode_SKU`, `Jumlah_Beli`, `Harga_Satuan_Snapshot`, `Subtotal`) VALUES
(1, 'TRX-001', 'MDL-01-BLK', 2, 45000, 90000),
(2, 'TRX-002', 'MDL-03-NVY', 2, 35000, 70000),
(3, 'TRX-003', 'MDL-04-SGE', 1, 85000, 85000),
(4, 'TRX-004', 'MDL-01-BLK', 1, 45000, 45000),
(5, 'TRX-005', 'MDL-03-MOC', 1, 35000, 35000),
(6, 'TRX-005', 'MDL-04-DPK', 1, 85000, 85000),
(23, 'TRX-20260104-8557', 'MDL-06-BLK', 1, 25000, 25000),
(24, 'TRX-20260104-8557', 'MDL-08-BLK', 1, 135000, 135000);

-- --------------------------------------------------------

--
-- Table structure for table `master_bahan`
--

CREATE TABLE `master_bahan` (
  `ID_Bahan` int(11) NOT NULL,
  `Nama_Bahan` varchar(50) NOT NULL,
  `Deskripsi_Bahan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_bahan`
--

INSERT INTO `master_bahan` (`ID_Bahan`, `Nama_Bahan`, `Deskripsi_Bahan`) VALUES
(1, 'Ceruty Babydoll', NULL),
(2, 'Voal Premium', NULL),
(3, 'Polycotton', NULL),
(4, 'Jersey Premium', 'Bahan melar, adem, cocok untuk olahraga'),
(5, 'Silk Motif', 'Bahan licin, mengkilap, dan terlihat mewah');

-- --------------------------------------------------------

--
-- Table structure for table `master_kategori`
--

CREATE TABLE `master_kategori` (
  `ID_Kategori` int(11) NOT NULL,
  `Nama_Kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_kategori`
--

INSERT INTO `master_kategori` (`ID_Kategori`, `Nama_Kategori`) VALUES
(5, 'Bergo'),
(6, 'Hijab Sport'),
(3, 'Instan'),
(4, 'Khimar'),
(2, 'Pashmina'),
(1, 'Segi Empat');

-- --------------------------------------------------------

--
-- Table structure for table `master_warna`
--

CREATE TABLE `master_warna` (
  `ID_Warna` int(11) NOT NULL,
  `Nama_Warna` varchar(50) NOT NULL,
  `Kode_Hex` varchar(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_warna`
--

INSERT INTO `master_warna` (`ID_Warna`, `Nama_Warna`, `Kode_Hex`) VALUES
(1, 'Hitam', '#000000'),
(2, 'Broken White', '#F4F6F0'),
(3, 'Maroon', '#800000'),
(4, 'Navy', '#000080'),
(5, 'Sage Green', '#9CAF88'),
(6, 'Mocca', '#A38068'),
(7, 'Dusty Pink', '#DCAE96');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `ID_Pelanggan` varchar(15) NOT NULL,
  `Nama_Pelanggan` varchar(100) NOT NULL,
  `No_HP` varchar(15) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Role` enum('customer','admin') DEFAULT 'customer',
  `Alamat_Utama` text DEFAULT NULL,
  `Tanggal_Gabung` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`ID_Pelanggan`, `Nama_Pelanggan`, `No_HP`, `Email`, `Password`, `Role`, `Alamat_Utama`, `Tanggal_Gabung`) VALUES
('ADM-001', 'Admin Sakinah', '081122223333', 'admin@sakinah.com', 'admin123', 'admin', 'Kantor Pusat Sakinah Style', '2025-12-01'),
('PLG-001', 'Teh Anisa', '08123456789', NULL, NULL, 'customer', 'Jl. Sakinah No. 1, Bandung', '2026-01-04'),
('PLG-002', 'Ukhti Sarah', '081299998888', 'sarah@mail.com', 'password123', 'customer', 'Jl. Dago Asri No. 5, Bandung', '2026-01-02'),
('PLG-003', 'Bunda Rina', '081377776666', 'rina@mail.com', 'password123', 'customer', 'Komp. Setiabudi Regency, Bandung', '2026-01-03'),
('PLG-004', 'Siti Aminah', '085755554444', 'siti@mail.com', 'password123', 'customer', 'Jl. Tubagus Ismail, Bandung', '2026-01-04'),
('PLG-2026-001', 'Rafi Saputra', 'atep@gmail.com', 'atep@gmail.com', '$2y$10$LXba6ftdwYF/pT2ZbunhLuLwp843r8xG4rVmrwvyHTPzQnUinP2bu', 'customer', NULL, '2026-01-04');

-- --------------------------------------------------------

--
-- Table structure for table `produk_induk`
--

CREATE TABLE `produk_induk` (
  `ID_Induk` int(11) NOT NULL,
  `Kode_Model` varchar(20) NOT NULL,
  `Nama_Produk` varchar(100) NOT NULL,
  `ID_Kategori` int(11) DEFAULT NULL,
  `ID_Bahan` int(11) DEFAULT NULL,
  `Deskripsi_Lengkap` text DEFAULT NULL,
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk_induk`
--

INSERT INTO `produk_induk` (`ID_Induk`, `Kode_Model`, `Nama_Produk`, `ID_Kategori`, `ID_Bahan`, `Deskripsi_Lengkap`, `Created_At`) VALUES
(1, 'MDL-01', 'Pashmina Ceruty Basic', 2, 1, 'Pashmina jatuh dan mudah dibentuk.', '2026-01-04 06:06:19'),
(2, 'MDL-02', 'Segi Empat Voal Paris', 1, 2, 'Tegak di dahi dan adem.', '2026-01-04 06:06:19'),
(3, 'MDL-03', 'Bergo Sporty Daily', 3, 4, 'Hijab instan menutup dada, cocok untuk sehari-hari.', '2026-01-04 06:15:59'),
(4, 'MDL-04', 'Pashmina Silk Luxury', 2, 5, 'Pashmina kondangan dengan finishing jahit tepi rapi.', '2026-01-04 06:15:59'),
(5, 'MDL-05', 'Khimar Syari Jumbo', 3, 1, 'Khimar panjang menutup tangan, bahan jatuh.', '2026-01-04 06:15:59'),
(6, 'MDL-06', 'Bergo Maryam Tali', 5, 1, 'Bergo tali bahan diamond yang ringan dan tidak menerawang.', '2026-01-04 06:21:18'),
(7, 'MDL-07', 'Hijab Sport Spandex', 6, 4, 'Hijab pendek khusus olahraga, menyerap keringat.', '2026-01-04 06:21:18'),
(8, 'MDL-08', 'French Khimar Basic', 4, 1, 'Khimar model perancis, stylish dan syari.', '2026-01-04 06:21:18'),
(9, 'MDL-09', 'Pashmina Plisket Lidi', 2, 1, 'Pashmina dengan lipatan plisket kecil full lidi.', '2026-01-04 06:21:18'),
(10, 'MDL-10', 'Segi Empat Voal Motif', 1, 2, 'Hijab printing motif bunga eksklusif.', '2026-01-04 06:21:18'),
(11, 'MDL-11', 'Bergo Hamidah Menutup Dagu', 5, 4, 'Bergo jersey menutup bagian bawah dagu (Antem).', '2026-01-04 06:21:18'),
(12, 'MDL-12', 'Khimar Jumbo Pet', 4, 1, 'Khimar 2 layer dengan pet busa yang kokoh.', '2026-01-04 06:21:18'),
(13, 'MDL-13', 'Pashmina Inner 2in1', 2, 1, 'Pashmina sudah menyatu dengan inner ciput.', '2026-01-04 06:21:18');

-- --------------------------------------------------------

--
-- Table structure for table `produk_varian`
--

CREATE TABLE `produk_varian` (
  `Kode_SKU` varchar(30) NOT NULL,
  `ID_Induk` int(11) NOT NULL,
  `ID_Warna` int(11) NOT NULL,
  `Harga_Jual` int(11) NOT NULL,
  `Stok` int(11) NOT NULL DEFAULT 0,
  `Foto_Produk` varchar(255) DEFAULT NULL,
  `Is_Active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk_varian`
--

INSERT INTO `produk_varian` (`Kode_SKU`, `ID_Induk`, `ID_Warna`, `Harga_Jual`, `Stok`, `Foto_Produk`, `Is_Active`) VALUES
('MDL-01-BLK', 1, 1, 45000, 50, 'pashmina_ceruty_hitam.jpg', 1),
('MDL-01-MRN', 1, 3, 45000, 4, 'pashmina_ceruty_maroon.jpg', 1),
('MDL-02-NVY', 2, 4, 30000, 100, 'segiempat_voal_navy.jpg', 1),
('MDL-03-BLK', 3, 1, 35000, 100, 'bergo_sport_black.jpg', 1),
('MDL-03-MOC', 3, 6, 35000, 50, 'bergo_sport_mocca.jpg', 1),
('MDL-03-NVY', 3, 4, 35000, 85, 'bergo_sport_navy.jpg', 1),
('MDL-04-DPK', 4, 7, 85000, 30, 'pashmina_silk_dusty.jpg', 1),
('MDL-04-SGE', 4, 5, 85000, 25, 'pashmina_silk_sage.jpg', 1),
('MDL-05-BLK', 5, 1, 120000, 0, 'khimar_syari_black.jpg', 1),
('MDL-06-BLK', 6, 1, 25000, 149, 'bergo_maryam_hitam.jpg', 1),
('MDL-06-MOC', 6, 6, 25000, 80, 'bergo_maryam_mocca.jpg', 1),
('MDL-06-MRN', 6, 3, 25000, 100, 'bergo_maryam_maroon.jpg', 1),
('MDL-07-BLK', 7, 1, 15000, 200, 'sport_spandex_hitam.jpg', 1),
('MDL-07-NVY', 7, 4, 15000, 120, 'sport_spandex_navy.jpg', 1),
('MDL-07-WHT', 7, 2, 15000, 90, 'sport_spandex_white.jpg', 1),
('MDL-08-BLK', 8, 1, 135000, 49, 'french_khimar_hitam.jpg', 1),
('MDL-08-MOC', 8, 6, 135000, 40, 'french_khimar_mocca.jpg', 1),
('MDL-08-SGE', 8, 5, 135000, 35, 'french_khimar_sage.jpg', 1),
('MDL-09-BLK', 9, 1, 40000, 75, 'plisket_hitam.jpg', 1),
('MDL-09-DPK', 9, 7, 40000, 60, 'plisket_dusty.jpg', 1),
('MDL-09-MOC', 9, 6, 40000, 55, 'plisket_mocca.jpg', 1),
('MDL-10-NVY', 10, 4, 55000, 25, 'voal_motif_navy.jpg', 1),
('MDL-10-SGE', 10, 5, 55000, 20, 'voal_motif_sage.jpg', 1),
('MDL-11-BLK', 11, 1, 30000, 100, 'hamidah_hitam.jpg', 1),
('MDL-11-MRN', 11, 3, 30000, 65, 'hamidah_maroon.jpg', 1),
('MDL-11-NVY', 11, 4, 30000, 80, 'hamidah_navy.jpg', 1),
('MDL-12-BLK', 12, 1, 95000, 15, 'khimar_jumbo_hitam.jpg', 1),
('MDL-12-WHT', 12, 2, 95000, 10, 'khimar_jumbo_putih.jpg', 1),
('MDL-13-BLK', 13, 1, 60000, 45, 'pash_inner_hitam.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `No_Transaksi` varchar(20) NOT NULL,
  `ID_Pelanggan` varchar(15) DEFAULT NULL,
  `Tanggal_Transaksi` datetime DEFAULT current_timestamp(),
  `Nama_Penerima` varchar(100) DEFAULT NULL,
  `Alamat_Pengiriman` text DEFAULT NULL,
  `Status_Transaksi` enum('Pending','Paid','Sent','Cancelled') DEFAULT 'Pending',
  `Total_Bayar` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`No_Transaksi`, `ID_Pelanggan`, `Tanggal_Transaksi`, `Nama_Penerima`, `Alamat_Pengiriman`, `Status_Transaksi`, `Total_Bayar`) VALUES
('TRX-001', 'PLG-001', '2026-01-04 13:06:19', 'Anisa', 'Jl. Sakinah No. 1, Bandung', 'Paid', 90000),
('TRX-002', 'PLG-002', '2026-01-04 09:00:00', 'Sarah', 'Jl. Dago Asri No. 5', 'Sent', 70000),
('TRX-003', 'PLG-003', '2026-01-04 10:30:00', 'Rina', 'Komp. Setiabudi Regency', 'Pending', 85000),
('TRX-004', 'PLG-001', '2026-01-04 11:15:00', 'Anisa', 'Kantor Pos Giro', 'Cancelled', 45000),
('TRX-005', 'PLG-004', '2026-01-04 12:00:00', 'Siti', 'Jl. Tubagus Ismail', 'Paid', 120000),
('TRX-20260104-8557', 'PLG-2026-001', '2026-01-04 14:20:41', 'Rafi Saputra', 'jiohinlkn', 'Pending', 170000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`ID_Detail`),
  ADD KEY `No_Transaksi` (`No_Transaksi`),
  ADD KEY `Kode_SKU` (`Kode_SKU`);

--
-- Indexes for table `master_bahan`
--
ALTER TABLE `master_bahan`
  ADD PRIMARY KEY (`ID_Bahan`),
  ADD UNIQUE KEY `Nama_Bahan` (`Nama_Bahan`);

--
-- Indexes for table `master_kategori`
--
ALTER TABLE `master_kategori`
  ADD PRIMARY KEY (`ID_Kategori`),
  ADD UNIQUE KEY `Nama_Kategori` (`Nama_Kategori`);

--
-- Indexes for table `master_warna`
--
ALTER TABLE `master_warna`
  ADD PRIMARY KEY (`ID_Warna`),
  ADD UNIQUE KEY `Nama_Warna` (`Nama_Warna`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`ID_Pelanggan`);

--
-- Indexes for table `produk_induk`
--
ALTER TABLE `produk_induk`
  ADD PRIMARY KEY (`ID_Induk`),
  ADD UNIQUE KEY `Kode_Model` (`Kode_Model`),
  ADD KEY `ID_Kategori` (`ID_Kategori`),
  ADD KEY `ID_Bahan` (`ID_Bahan`);

--
-- Indexes for table `produk_varian`
--
ALTER TABLE `produk_varian`
  ADD PRIMARY KEY (`Kode_SKU`),
  ADD KEY `ID_Induk` (`ID_Induk`),
  ADD KEY `ID_Warna` (`ID_Warna`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`No_Transaksi`),
  ADD KEY `ID_Pelanggan` (`ID_Pelanggan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `ID_Detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `master_bahan`
--
ALTER TABLE `master_bahan`
  MODIFY `ID_Bahan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `master_kategori`
--
ALTER TABLE `master_kategori`
  MODIFY `ID_Kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `master_warna`
--
ALTER TABLE `master_warna`
  MODIFY `ID_Warna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `produk_induk`
--
ALTER TABLE `produk_induk`
  MODIFY `ID_Induk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `detail_transaksi_ibfk_1` FOREIGN KEY (`No_Transaksi`) REFERENCES `transaksi` (`No_Transaksi`),
  ADD CONSTRAINT `detail_transaksi_ibfk_2` FOREIGN KEY (`Kode_SKU`) REFERENCES `produk_varian` (`Kode_SKU`);

--
-- Constraints for table `produk_induk`
--
ALTER TABLE `produk_induk`
  ADD CONSTRAINT `produk_induk_ibfk_1` FOREIGN KEY (`ID_Kategori`) REFERENCES `master_kategori` (`ID_Kategori`),
  ADD CONSTRAINT `produk_induk_ibfk_2` FOREIGN KEY (`ID_Bahan`) REFERENCES `master_bahan` (`ID_Bahan`);

--
-- Constraints for table `produk_varian`
--
ALTER TABLE `produk_varian`
  ADD CONSTRAINT `produk_varian_ibfk_1` FOREIGN KEY (`ID_Induk`) REFERENCES `produk_induk` (`ID_Induk`) ON DELETE CASCADE,
  ADD CONSTRAINT `produk_varian_ibfk_2` FOREIGN KEY (`ID_Warna`) REFERENCES `master_warna` (`ID_Warna`);

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`ID_Pelanggan`) REFERENCES `pelanggan` (`ID_Pelanggan`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
