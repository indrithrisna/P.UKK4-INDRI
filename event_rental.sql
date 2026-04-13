-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 08, 2026 at 02:17 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `event_rental`
--

-- --------------------------------------------------------

--
-- Table structure for table `alat`
--

CREATE TABLE `alat` (
  `id` int NOT NULL,
  `nama_alat` varchar(100) NOT NULL,
  `merk` varchar(100) DEFAULT NULL,
  `kategori_id` int DEFAULT NULL,
  `jumlah_total` int NOT NULL,
  `jumlah_tersedia` int NOT NULL,
  `kondisi` enum('baik','rusak ringan','rusak berat') DEFAULT 'baik',
  `harga_sewa` decimal(10,2) DEFAULT NULL,
  `deskripsi` text,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `alat`
--

INSERT INTO `alat` (`id`, `nama_alat`, `merk`, `kategori_id`, `jumlah_total`, `jumlah_tersedia`, `kondisi`, `harga_sewa`, `deskripsi`, `foto`, `created_at`, `deleted_at`) VALUES
(1, 'Speaker Aktif 15 inch', 'JBL', 1, 60, 20, 'baik', 20000.00, 'Speaker aktif berkualitas tinggi', NULL, '2026-02-11 00:12:40', NULL),
(2, 'Microphone Wireless', 'JBL', 1, 50, 30, 'baik', 70000.00, 'Microphone wireless profesional', NULL, '2026-02-11 00:12:40', NULL),
(3, 'Lampu Par LED', NULL, 2, 30, 30, 'baik', 75000.00, 'Lampu LED warna-warni', NULL, '2026-02-11 00:12:40', NULL),
(4, 'Kursi Tiffany', '', 4, 100, 100, 'baik', 10000.00, 'Kursi tiffany untuk acara formal', NULL, '2026-02-11 00:12:40', NULL),
(5, 'Meja Bulat', NULL, 4, 20, 20, 'baik', 50000.00, 'Meja bulat diameter 120cm', NULL, '2026-02-11 00:12:40', NULL),
(6, 'Mixer Audio 8 Channel', NULL, 1, 5, 5, 'baik', 200000.00, 'Mixer audio untuk kontrol suara', NULL, '2026-02-11 02:16:55', NULL),
(7, 'Amplifier 1000 Watt', NULL, 1, 8, 8, 'baik', 175000.00, 'Amplifier daya besar', NULL, '2026-02-11 02:16:55', NULL),
(8, 'Lampu Moving Head', NULL, 2, 15, 5, 'baik', 250000.00, 'Lampu moving head otomatis', NULL, '2026-02-11 02:16:55', NULL),
(9, 'Lampu Strobo', NULL, 2, 10, 10, 'baik', 100000.00, 'Lampu strobo untuk efek', NULL, '2026-02-11 02:16:55', NULL),
(10, 'Smoke Machine', NULL, 2, 5, 5, 'baik', 150000.00, 'Mesin asap untuk efek panggung', NULL, '2026-02-11 02:16:55', NULL),
(11, 'Backdrop Kain 3x4m', NULL, 3, 20, 10, 'baik', 100000.00, 'Backdrop kain polos berbagai warna', NULL, '2026-02-11 02:16:55', NULL),
(12, 'Standing Banner', NULL, 3, 30, 30, 'baik', 50000.00, 'Standing banner untuk promosi', NULL, '2026-02-11 02:16:55', NULL),
(13, 'Balon Gate', NULL, 3, 10, 10, 'baik', 200000.00, 'Gerbang balon untuk entrance', NULL, '2026-02-11 02:16:55', NULL),
(14, 'Panggung Portable 2x2m', NULL, 3, 8, 4, 'baik', 300000.00, 'Panggung portable modular', NULL, '2026-02-11 02:16:55', NULL),
(15, 'Kursi Futura', NULL, 4, 150, 150, 'baik', 10000.00, 'Kursi plastik standar', NULL, '2026-02-11 02:16:55', NULL),
(16, 'Meja Kotak Panjang', NULL, 4, 25, 25, 'baik', 45000.00, 'Meja kotak 180x80cm', NULL, '2026-02-11 02:16:55', NULL),
(17, 'Tenda Sarnafil 5x5m', NULL, 4, 10, 10, 'baik', 500000.00, 'Tenda sarnafil untuk outdoor', NULL, '2026-02-11 02:16:55', NULL),
(18, 'Tenda Kerucut', NULL, 4, 8, 8, 'baik', 400000.00, 'Tenda kerucut dekoratif', NULL, '2026-02-11 02:16:55', NULL),
(19, 'Karpet Merah', NULL, 3, 50, 50, 'baik', 30000.00, 'Karpet merah per meter', NULL, '2026-02-11 02:16:55', NULL),
(20, 'Proyektor LCD', NULL, 1, 5, 5, 'baik', 300000.00, 'Proyektor untuk presentasi', NULL, '2026-02-11 02:16:55', NULL),
(21, 'Layar Proyektor 3x2m', NULL, 1, 5, 5, 'baik', 150000.00, 'Layar proyektor tripod', NULL, '2026-02-11 02:16:55', NULL),
(22, 'Genset 5000 Watt', NULL, 1, 3, 3, 'baik', 500000.00, 'Generator listrik portable', NULL, '2026-02-11 02:16:55', NULL),
(23, 'Kipas Angin Berdiri', NULL, 4, 20, 20, 'baik', 25000.00, 'Kipas angin standing fan', NULL, '2026-02-11 02:16:55', NULL),
(24, 'AC Portable', NULL, 4, 5, 5, 'baik', 350000.00, 'AC portable untuk ruangan', NULL, '2026-02-11 02:16:55', NULL),
(25, 'Podium Mimbar', NULL, 4, 10, 10, 'baik', 75000.00, 'Podium kayu untuk pembicara', NULL, '2026-02-11 02:16:55', NULL),
(26, 'Microphone Wireless Shure', NULL, 1, 30, 30, 'baik', 100000.00, '', NULL, '2026-02-11 04:44:59', NULL),
(27, 'Kursi Tiffany', 'JBL', 4, 60, 40, 'baik', 100000.00, '', NULL, '2026-04-08 04:03:14', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `detail_peminjaman`
--

CREATE TABLE `detail_peminjaman` (
  `id` int NOT NULL,
  `peminjaman_id` int NOT NULL,
  `alat_id` int NOT NULL,
  `jumlah` int NOT NULL,
  `harga_satuan` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_peminjaman`
--

INSERT INTO `detail_peminjaman` (`id`, `peminjaman_id`, `alat_id`, `jumlah`, `harga_satuan`, `subtotal`) VALUES
(1, 1, 1, 1, 150000.00, 450000.00),
(2, 2, 1, 2, 150000.00, 600000.00),
(3, 3, 2, 3, 50000.00, 450000.00),
(4, 4, 1, 3, 150000.00, 1350000.00),
(5, 5, 2, 5, 50000.00, 750000.00),
(6, 6, 8, 10, 250000.00, 7500000.00),
(7, 7, 14, 4, 300000.00, 3600000.00),
(8, 8, 1, 7, 150000.00, 3150000.00),
(9, 9, 1, 7, 150000.00, 3150000.00),
(10, 10, 1, 7, 150000.00, 3150000.00),
(12, 12, 3, 8, 75000.00, 1800000.00);

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `deskripsi` text,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`, `deskripsi`, `deleted_at`) VALUES
(1, 'Sound System', 'Peralatan musik', NULL),
(2, 'Lighting', 'Peralatan pencahayaan', NULL),
(3, 'Dekorasi', 'Peralatan dekorasi event', NULL),
(4, 'Furniture', 'Meja, kursi, dan furniture lainnya', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `aktivitas` text NOT NULL,
  `keterangan` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`id`, `user_id`, `aktivitas`, `keterangan`, `ip_address`, `created_at`) VALUES
(1, 1, 'Update Label Role', 'Mengubah label role: peminjam', '::1', '2026-02-11 01:01:25'),
(2, 1, 'Update Label Role', 'Mengubah label role: admin', '::1', '2026-02-11 01:01:38'),
(3, 1, 'Update Label Role', 'Mengubah label role: admin', '::1', '2026-02-11 01:01:45'),
(4, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-02-11 01:43:46'),
(5, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-11 01:43:57'),
(6, 1, 'Update Profile', 'Mengubah data profile', '::1', '2026-02-11 01:44:28'),
(7, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-02-11 01:44:36'),
(8, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-11 01:45:06'),
(9, 2, 'Update Profile', 'Mengubah data profile', '::1', '2026-02-11 01:45:56'),
(10, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-02-11 01:46:04'),
(11, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-11 01:46:24'),
(12, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-02-11 01:46:42'),
(13, 3, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-11 01:46:53'),
(14, 3, 'Update Profile', 'Mengubah data profile', '::1', '2026-02-11 01:47:45'),
(15, 3, 'Buat Peminjaman', 'Membuat peminjaman baru ID: 1 dengan total biaya Rp 450.000', '::1', '2026-02-11 01:48:36'),
(16, 3, 'Logout', 'User keluar dari sistem', '::1', '2026-02-11 02:12:19'),
(17, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-11 02:12:36'),
(18, 2, 'Approve Peminjaman', 'Menyetujui peminjaman ID: 1', '::1', '2026-02-11 02:28:21'),
(19, 2, 'Update Status Peminjaman', 'Mengubah status peminjaman ID: 1 menjadi dipinjam', '::1', '2026-02-11 02:47:43'),
(20, 2, 'Pengembalian Alat', 'Menyelesaikan peminjaman ID: 1', '::1', '2026-02-11 02:48:06'),
(21, 2, 'Update Label Role', 'Mengubah label role: admin', '::1', '2026-02-11 03:32:56'),
(22, 2, 'Update Label Role', 'Mengubah label role: admin', '::1', '2026-02-11 03:33:01'),
(23, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-02-11 03:40:23'),
(24, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-11 03:40:40'),
(25, 1, 'Update Pengaturan Denda', 'Mengubah pengaturan denda sistem', '::1', '2026-02-11 03:50:29'),
(26, 1, 'Update User', 'Mengubah data user: Aliee', '::1', '2026-02-11 03:55:38'),
(27, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-02-11 03:58:51'),
(28, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-11 03:59:05'),
(29, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-02-11 04:36:23'),
(30, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-11 04:36:42'),
(31, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-11 04:39:50'),
(32, 1, 'Update Profile', 'Mengubah data profile', '::1', '2026-02-11 04:42:52'),
(33, 1, 'Update User', 'Mengubah data user: Runa  love sunghoon', '::1', '2026-02-11 04:43:10'),
(34, 1, 'Tambah Alat', 'Menambah alat baru: Microphone Wireless Shure', '::1', '2026-02-11 04:44:59'),
(35, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-02-11 04:45:59'),
(36, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-11 04:50:54'),
(38, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-11 11:52:41'),
(39, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-11 11:58:18'),
(40, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-02-11 11:58:41'),
(41, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-11 11:58:53'),
(42, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-02-11 12:02:53'),
(43, 3, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-11 12:03:31'),
(44, 3, 'Update Profile', 'Mengubah data profile', '::1', '2026-02-11 17:04:02'),
(45, 3, 'Update Profile', 'Mengubah data profile', '::1', '2026-02-11 17:15:58'),
(46, 3, 'Update Profile', 'Mengubah data profile', '::1', '2026-02-11 17:16:01'),
(47, 3, 'Update Profile', 'Mengubah data profile', '::1', '2026-02-11 17:16:26'),
(48, 3, 'Update Profile', 'Mengubah data profile', '::1', '2026-02-11 17:16:41'),
(49, 3, 'Logout', 'User keluar dari sistem', '::1', '2026-02-11 17:25:31'),
(50, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-11 17:26:14'),
(51, 1, 'Update Profile', 'Mengubah data profile', '::1', '2026-02-11 17:27:12'),
(52, 1, 'Update Profile', 'Mengubah data profile', '::1', '2026-02-11 17:27:41'),
(53, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-02-11 17:33:44'),
(54, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-11 17:36:43'),
(55, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-02-11 17:37:46'),
(56, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-11 23:52:24'),
(57, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-02-11 23:53:19'),
(58, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-11 23:53:33'),
(59, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-02-11 23:53:49'),
(60, 3, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-11 23:59:58'),
(61, 3, 'Update Profile', 'Mengubah data profile', '::1', '2026-02-12 00:01:16'),
(62, 3, 'Logout', 'User keluar dari sistem', '::1', '2026-02-12 00:02:03'),
(63, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-12 00:02:37'),
(64, 1, 'Update Alat', 'Mengubah data alat: Backdrop Kain 3x4m', '::1', '2026-02-12 00:44:21'),
(65, 1, 'Update Alat', 'Mengubah data alat: Backdrop Kain 3x4m', '::1', '2026-02-12 00:47:02'),
(66, 1, 'Update Alat', 'Mengubah data alat: Microphone Wireless', '::1', '2026-02-12 00:47:24'),
(67, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-02-12 00:52:29'),
(68, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-12 00:52:40'),
(69, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-02-12 00:54:17'),
(70, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-12 00:54:34'),
(71, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-02-12 00:59:14'),
(72, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-12 00:59:23'),
(73, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-02-12 00:59:47'),
(74, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-12 00:59:59'),
(75, 2, 'Approve Peminjaman', 'Menyetujui peminjaman ID: 2', '::1', '2026-02-12 01:00:16'),
(76, 2, 'Update Status Peminjaman', 'Mengubah status peminjaman ID: 2 menjadi dipinjam', '::1', '2026-02-12 01:00:19'),
(77, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-12 02:27:45'),
(78, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-02-14 07:35:50'),
(79, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-14 07:36:00'),
(80, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-14 07:47:28'),
(81, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-14 07:59:37'),
(82, 1, 'Tambah User', 'Menambah user baru: kay (peminjam)', '::1', '2026-02-14 08:01:08'),
(83, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-02-14 08:12:58'),
(84, 5, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-14 08:13:19'),
(85, 5, 'Update Profile', 'Mengubah data profile', '::1', '2026-02-14 08:31:53'),
(86, 5, 'Logout', 'User keluar dari sistem', '::1', '2026-02-14 08:44:35'),
(87, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-14 08:44:43'),
(88, 2, 'Approve Peminjaman', 'Menyetujui peminjaman ID: 3', '::1', '2026-02-14 08:45:11'),
(89, 2, 'Update Status Peminjaman', 'Mengubah status peminjaman ID: 3 menjadi dipinjam', '::1', '2026-02-14 08:45:13'),
(90, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-14 13:15:04'),
(91, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-02-14 14:01:28'),
(92, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-14 14:01:50'),
(93, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-02-14 14:05:34'),
(94, 3, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-14 14:05:44'),
(95, 3, 'Logout', 'User keluar dari sistem', '::1', '2026-02-14 14:06:06'),
(96, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-14 14:06:48'),
(97, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-02-14 14:21:30'),
(98, 3, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-14 14:21:42'),
(99, 3, 'Logout', 'User keluar dari sistem', '::1', '2026-02-14 14:23:40'),
(100, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-14 14:23:54'),
(101, 1, 'Pengembalian Alat', 'Memproses pengembalian ID: 2 dengan denda Rp 0', '::1', '2026-02-14 15:47:39'),
(102, 1, 'Pengembalian Alat', 'Memproses pengembalian ID: 3 dengan denda Rp 50.000', '::1', '2026-02-14 15:54:43'),
(103, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-02-14 15:55:02'),
(104, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-14 15:55:17'),
(105, 2, 'Update Profile', 'Mengubah data profile', '::1', '2026-02-14 16:07:29'),
(106, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-02-14 16:11:35'),
(107, 3, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-14 16:11:47'),
(108, 3, 'Logout', 'User keluar dari sistem', '::1', '2026-02-14 16:38:32'),
(109, 5, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-14 16:38:42'),
(110, 5, 'Logout', 'User keluar dari sistem', '::1', '2026-02-14 16:39:39'),
(111, 3, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-14 16:39:50'),
(112, 3, 'Logout', 'User keluar dari sistem', '::1', '2026-02-14 16:39:56'),
(113, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-14 16:40:04'),
(114, 5, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-16 10:07:09'),
(115, 5, 'Logout', 'User keluar dari sistem', '::1', '2026-02-16 10:07:14'),
(118, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-16 11:41:08'),
(119, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-18 11:24:44'),
(120, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-21 10:02:31'),
(121, 1, 'Tambah User', 'Menambah user baru: sasa (peminjam)', '::1', '2026-02-21 10:04:14'),
(122, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-02-21 10:04:22'),
(125, 3, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-21 10:12:43'),
(126, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-23 00:50:03'),
(127, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-02-23 01:11:42'),
(128, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-23 01:11:54'),
(129, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-23 01:17:13'),
(130, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-02-23 01:17:21'),
(131, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-23 01:17:27'),
(132, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-02-23 01:48:30'),
(133, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-23 01:48:35'),
(134, 1, 'Update Profile', 'Mengubah data profile', '::1', '2026-02-23 01:48:51'),
(135, 1, 'Update Profile', 'Mengubah data profile', '::1', '2026-02-23 01:49:09'),
(136, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-02-23 01:52:00'),
(137, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-23 01:52:07'),
(138, 1, 'Update User', 'Mengubah data user: runaa', '::1', '2026-02-23 01:54:26'),
(139, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-02-23 02:06:51'),
(140, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-23 02:06:57'),
(141, 1, 'Update Profile', 'Mengubah data profile', '::1', '2026-02-23 02:07:12'),
(142, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-02-23 05:13:27'),
(143, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-23 05:13:34'),
(144, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-02-23 05:15:30'),
(145, 3, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-23 05:15:37'),
(146, 3, 'Logout', 'User keluar dari sistem', '::1', '2026-02-23 05:17:34'),
(147, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-23 05:17:40'),
(148, 2, 'Approve Peminjaman', 'Menyetujui peminjaman ID: 4', '::1', '2026-02-23 05:17:48'),
(149, 2, 'Update Status Peminjaman', 'Mengubah status peminjaman ID: 4 menjadi dipinjam', '::1', '2026-02-23 05:17:50'),
(150, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-02-23 05:19:13'),
(151, 3, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-23 05:19:19'),
(152, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-23 08:03:18'),
(153, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-02-23 08:03:27'),
(154, 3, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-23 08:03:51'),
(155, 3, 'Logout', 'User keluar dari sistem', '::1', '2026-02-23 08:04:00'),
(156, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-23 08:04:08'),
(157, 2, 'Approve Peminjaman', 'Menyetujui peminjaman ID: 5', '::1', '2026-02-23 08:04:19'),
(158, 2, 'Update Status Peminjaman', 'Mengubah status peminjaman ID: 5 menjadi dipinjam', '::1', '2026-02-23 08:10:34'),
(159, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-02-23 08:33:39'),
(160, 3, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-23 08:33:46'),
(161, 3, 'Update Profile', 'Mengubah data profile', '::1', '2026-02-23 08:33:56'),
(162, 3, 'Logout', 'User keluar dari sistem', '::1', '2026-02-23 08:34:58'),
(163, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-23 08:35:04'),
(164, 2, 'Approve Peminjaman', 'Menyetujui peminjaman ID: 6', '::1', '2026-02-23 08:35:17'),
(165, 2, 'Update Status Peminjaman', 'Mengubah status peminjaman ID: 6 menjadi dipinjam', '::1', '2026-02-23 08:35:19'),
(166, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-02-23 08:41:25'),
(167, 3, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-23 08:41:49'),
(168, 3, 'Logout', 'User keluar dari sistem', '::1', '2026-02-23 08:42:29'),
(169, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-02-23 08:42:39'),
(170, 2, 'Approve Peminjaman', 'Menyetujui peminjaman ID: 7', '::1', '2026-02-23 08:42:51'),
(171, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-03-10 16:55:13'),
(172, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-03-10 16:55:22'),
(173, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-03-10 17:09:49'),
(174, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-03-10 17:10:08'),
(175, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-03-10 17:15:01'),
(176, 3, 'Login', 'User berhasil login ke sistem', '::1', '2026-03-10 17:15:14'),
(177, 3, 'Logout', 'User keluar dari sistem', '::1', '2026-03-10 17:17:05'),
(178, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-03-10 17:17:20'),
(179, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-03-11 05:33:26'),
(180, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-03-11 13:11:46'),
(181, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-03-11 13:12:03'),
(182, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-03-11 13:16:30'),
(183, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-03-11 14:14:22'),
(184, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-03-11 14:14:37'),
(185, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-03-11 14:30:56'),
(186, 3, 'Login', 'User berhasil login ke sistem', '::1', '2026-03-11 14:31:03'),
(187, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-04-01 00:11:33'),
(188, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-04-01 01:16:47'),
(189, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-04-01 01:16:53'),
(190, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-04-02 01:27:23'),
(191, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-04-02 07:18:59'),
(192, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-04-02 07:19:25'),
(193, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-04-02 07:19:39'),
(194, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-04-02 07:19:47'),
(195, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-04-07 00:10:41'),
(196, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-04-07 00:10:48'),
(197, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-04-07 00:11:00'),
(198, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-04-07 01:52:19'),
(199, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-04-07 01:52:25'),
(200, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-04-07 02:32:06'),
(201, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-04-07 03:12:35'),
(202, 3, 'Login', 'User berhasil login ke sistem', '::1', '2026-04-07 14:31:19'),
(203, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-04-08 00:58:47'),
(204, 1, 'Hapus User', 'Menghapus user: nna', '::1', '2026-04-08 01:42:24'),
(205, 1, 'Hapus User', 'Menghapus user: ndiena', '::1', '2026-04-08 01:44:44'),
(206, 1, 'Hapus User', 'Menghapus user: sasa', '::1', '2026-04-08 01:53:15'),
(207, 1, 'Hapus User', 'Menghapus user: naejii', '::1', '2026-04-08 01:53:22'),
(208, 1, 'Pengembalian Alat', 'Memproses pengembalian ID: 5 dengan denda Rp 820.000', '::1', '2026-04-08 01:58:27'),
(209, 1, 'Pengembalian Alat', 'Memproses pengembalian ID: 4 dengan denda Rp 850.000', '::1', '2026-04-08 01:58:49'),
(210, 1, 'Update Profile', 'Mengubah data profile', '::1', '2026-04-08 02:03:41'),
(211, 1, 'Update Profile', 'Mengubah data profile', '::1', '2026-04-08 02:04:27'),
(212, 1, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 11', '::1', '2026-04-08 02:05:25'),
(213, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-04-08 02:05:42'),
(214, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-04-08 02:05:51'),
(215, 2, 'Update Alat', 'Mengubah alat: Speaker Aktif 15 inch', '::1', '2026-04-08 02:15:39'),
(216, 2, 'Update Alat', 'Mengubah alat: Microphone Wireless', '::1', '2026-04-08 02:17:59'),
(217, 2, 'Dipinjam', 'Alat peminjaman ID: 8 sedang dipinjam', '::1', '2026-04-08 02:27:25'),
(218, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-04-08 02:30:37'),
(219, 3, 'Login', 'User berhasil login ke sistem', '::1', '2026-04-08 02:30:52'),
(220, 3, 'Logout', 'User keluar dari sistem', '::1', '2026-04-08 02:53:48'),
(221, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-04-08 02:54:01'),
(222, 1, 'Tambah Kategori', 'Menambah kategori: kendaraan', '::1', '2026-04-08 03:14:13'),
(223, 1, 'Hapus Kategori', 'Menghapus kategori: kendaraan', '::1', '2026-04-08 03:14:21'),
(224, 1, 'Tambah Alat', 'Menambah alat: Kursi Tiffany', '::1', '2026-04-08 04:03:14'),
(225, 1, 'Update Alat', 'Mengubah alat: Kursi Tiffany', '::1', '2026-04-08 04:03:33'),
(226, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-04-08 04:11:17'),
(227, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-04-08 04:11:26'),
(228, 2, 'Update Alat', 'Mengubah alat: Speaker Aktif 15 inch', '::1', '2026-04-08 04:11:54'),
(229, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-04-08 04:14:05'),
(230, 3, 'Login', 'User berhasil login ke sistem', '::1', '2026-04-08 04:14:15'),
(231, 3, 'Logout', 'User keluar dari sistem', '::1', '2026-04-08 04:16:34'),
(232, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-04-08 04:16:48'),
(233, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-04-08 05:21:57'),
(234, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-04-08 05:22:02'),
(235, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-04-08 11:47:52'),
(236, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-04-08 11:48:05'),
(237, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-04-08 11:48:50'),
(238, 2, 'Login', 'User berhasil login ke sistem', '::1', '2026-04-08 11:48:57'),
(239, 2, 'Logout', 'User keluar dari sistem', '::1', '2026-04-08 11:49:43'),
(240, 3, 'Login', 'User berhasil login ke sistem', '::1', '2026-04-08 11:49:53'),
(241, 3, 'Logout', 'User keluar dari sistem', '::1', '2026-04-08 13:40:21'),
(242, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-04-08 13:49:33'),
(243, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-04-08 13:49:36'),
(244, 1, 'Login', 'User berhasil login ke sistem', '::1', '2026-04-08 14:09:10'),
(245, 1, 'Logout', 'User keluar dari sistem', '::1', '2026-04-08 14:10:26');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` int NOT NULL,
  `nama_alat` varchar(50) NOT NULL,
  `peminjam_id` int NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `tanggal_pengembalian` datetime DEFAULT NULL,
  `kondisi_pengembalian` enum('baik','rusak ringan','rusak berat','hilang') DEFAULT 'baik',
  `catatan_pengembalian` text,
  `status` enum('pending','disetujui','ditolak','dipinjam','selesai') DEFAULT 'pending',
  `total_biaya` decimal(10,2) DEFAULT NULL,
  `denda` decimal(10,2) DEFAULT '0.00',
  `keterangan` text,
  `petugas_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id`, `nama_alat`, `peminjam_id`, `tanggal_pinjam`, `tanggal_kembali`, `tanggal_pengembalian`, `kondisi_pengembalian`, `catatan_pengembalian`, `status`, `total_biaya`, `denda`, `keterangan`, `petugas_id`, `created_at`, `deleted_at`) VALUES
(1, '', 3, '2026-02-11', '2026-02-14', '2026-02-11 22:54:09', 'baik', NULL, 'selesai', 450000.00, 0.00, 'acara keluarga', 2, '2026-02-11 01:48:36', NULL),
(2, '', 3, '2026-02-12', '2026-02-14', '2026-02-14 22:54:09', 'baik', '', 'selesai', 600000.00, 0.00, 'adalah', 2, '2026-02-11 12:07:34', NULL),
(3, 'Microphone Wireless', 5, '2026-02-14', '2026-02-17', '2026-02-14 22:54:43', 'rusak ringan', '', 'selesai', 450000.00, 50000.00, 'sekolah', 2, '2026-02-14 08:44:13', NULL),
(4, 'Speaker Aktif 15 inch', 3, '2026-02-24', '2026-02-27', '2026-04-08 08:58:49', 'rusak ringan', '', 'selesai', 1350000.00, 850000.00, 'sekolah', 2, '2026-02-23 05:17:22', NULL),
(5, 'Microphone Wireless', 3, '2026-02-23', '2026-02-26', '2026-04-08 08:58:27', 'baik', '', 'selesai', 750000.00, 820000.00, 'sekolah', 2, '2026-02-23 05:20:02', NULL),
(6, 'Lampu Moving Head', 3, '2026-02-23', '2026-02-26', NULL, 'baik', NULL, 'dipinjam', 7500000.00, 0.00, 'nikahan', 2, '2026-02-23 08:34:49', NULL),
(7, 'Panggung Portable 2x2m', 3, '2026-02-25', '2026-02-28', NULL, 'baik', NULL, 'dipinjam', 3600000.00, 0.00, 'ada ajja', 2, '2026-02-23 08:42:27', NULL),
(8, 'Speaker Aktif 15 inch', 3, '2026-03-11', '2026-03-14', NULL, 'baik', NULL, 'dipinjam', 3150000.00, 0.00, 'sekolah', 2, '2026-03-10 17:16:25', NULL),
(9, 'Speaker Aktif 15 inch', 3, '2026-04-09', '2026-04-12', NULL, 'baik', NULL, 'pending', 3150000.00, 0.00, 'adalah', NULL, '2026-04-07 14:35:42', NULL),
(10, 'Speaker Aktif 15 inch', 3, '2026-04-11', '2026-04-14', NULL, 'baik', NULL, 'pending', 3150000.00, 0.00, 'sekolah', NULL, '2026-04-07 14:42:39', NULL),
(12, 'Lampu Par LED', 3, '2026-04-17', '2026-04-20', NULL, 'baik', NULL, 'pending', 1800000.00, 0.00, 'sekolah', NULL, '2026-04-08 02:48:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan_denda`
--

CREATE TABLE `pengaturan_denda` (
  `id` int NOT NULL,
  `denda_per_hari` decimal(10,2) NOT NULL DEFAULT '10000.00',
  `denda_rusak_ringan` decimal(10,2) NOT NULL DEFAULT '50000.00',
  `denda_rusak_berat` decimal(10,2) NOT NULL DEFAULT '100000.00',
  `denda_hilang_persen` int NOT NULL DEFAULT '100',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengaturan_denda`
--

INSERT INTO `pengaturan_denda` (`id`, `denda_per_hari`, `denda_rusak_ringan`, `denda_rusak_berat`, `denda_hilang_persen`, `updated_at`) VALUES
(1, 20000.00, 50000.00, 100000.00, 100, '2026-02-11 03:50:29');

-- --------------------------------------------------------

--
-- Table structure for table `role_labels`
--

CREATE TABLE `role_labels` (
  `id` int NOT NULL,
  `role_key` varchar(20) NOT NULL,
  `label_singular` varchar(50) NOT NULL,
  `label_plural` varchar(50) NOT NULL,
  `deskripsi` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `role_labels`
--

INSERT INTO `role_labels` (`id`, `role_key`, `label_singular`, `label_plural`, `deskripsi`) VALUES
(1, 'admin', 'Administrator', 'runa', 'Pengelola sistem dengan akses penuh'),
(2, 'petugas', 'Petugas', 'Petugas', 'Staff yang mengelola peminjaman dan pengembalian'),
(3, 'peminjam', 'Peminjam', 'alie', 'Pengguna yang meminjam alat event');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `foto_profile` varchar(255) DEFAULT NULL,
  `role` enum('admin','petugas','peminjam') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama`, `email`, `telepon`, `foto_profile`, `role`, `created_at`, `deleted_at`) VALUES
(1, 'admin', '$2y$10$xhlDiiC3PmGSg1Le/G4MD.7mH5ShFq8qPAeD6Jf1FIBwb9ZnTOK86', 'runz', 'admin@gmail.com', '089542120504', 'uploads/profiles/profile_1_1775613821.jpg', 'admin', '2026-02-11 00:12:40', NULL),
(2, 'petugas1', '$2y$10$WLjGGvvci8p4m17fupov4uCsC272BVMgGsKJAiMJB/CnhKB7wedDe', 'jai', 'petugas@event.com', '08954459098', NULL, 'petugas', '2026-02-11 00:12:40', NULL),
(3, 'peminjam1', '$2y$10$b0zol2slVOhsKdeCs3fkeOEaYrIGMDSgIbCnsbypFyMLG5a3ldgJK', 'al', 'peminjam@gmail.com', '08954459098', 'uploads/profiles/profile_3_1770830201.jpg', 'peminjam', '2026-02-11 00:12:40', NULL),
(5, 'peminjam2', '$2y$10$Nv8OlqubWNqwTi91h4XeRuvZEMIAOhRPml0OYFuQoTtOfHNXgpose', 'kayla', 'peminjam2@gmail.com', '089675342290', NULL, 'peminjam', '2026-02-14 08:01:08', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alat`
--
ALTER TABLE `alat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kategori_id` (`kategori_id`);

--
-- Indexes for table `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `peminjaman_id` (`peminjaman_id`),
  ADD KEY `alat_id` (`alat_id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `peminjam_id` (`peminjam_id`),
  ADD KEY `petugas_id` (`petugas_id`);

--
-- Indexes for table `pengaturan_denda`
--
ALTER TABLE `pengaturan_denda`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_labels`
--
ALTER TABLE `role_labels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_key` (`role_key`);

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
-- AUTO_INCREMENT for table `alat`
--
ALTER TABLE `alat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=246;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `pengaturan_denda`
--
ALTER TABLE `pengaturan_denda`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `role_labels`
--
ALTER TABLE `role_labels`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alat`
--
ALTER TABLE `alat`
  ADD CONSTRAINT `alat_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`);

--
-- Constraints for table `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  ADD CONSTRAINT `detail_peminjaman_ibfk_1` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjaman` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_peminjaman_ibfk_2` FOREIGN KEY (`alat_id`) REFERENCES `alat` (`id`);

--
-- Constraints for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `log_aktivitas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`peminjam_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`petugas_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
