-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 14, 2025 at 02:34 PM
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
-- Database: `db_perpus`
--

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `id_buku` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `pengarang` varchar(255) DEFAULT NULL,
  `penerbit` varchar(255) DEFAULT NULL,
  `tahun_terbit` year(4) DEFAULT NULL,
  `id_kategori` int(11) NOT NULL,
  `stok` int(11) NOT NULL,
  `id_rak` int(11) NOT NULL,
  `tanggal_ditambahkan` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`id_buku`, `judul`, `pengarang`, `penerbit`, `tahun_terbit`, `id_kategori`, `stok`, `id_rak`, `tanggal_ditambahkan`) VALUES
(1, 'Harry Potter', 'J.K. Rowling', 'Bloomsbury', '1997', 1, 10, 1, '2025-01-11'),
(2, 'Sapiens', 'Yuval Noah Harari', 'Harper', '2011', 2, 5, 2, '2025-01-11'),
(3, 'Buku Matematika Dasar', 'Tim Edukasi', 'Pustaka Belajar', '2020', 3, 8, 3, '2025-01-11'),
(4, 'Doraemon', 'Fujiko F. Fujio', 'Shogakukan', '1970', 4, 15, 4, '2025-01-11'),
(5, 'test', 'test', 'penerbittes', '2025', 2, 12, 3, '2025-01-14'),
(6, 'kuingin bisa', 'taktahu', 'penerbittes', '2025', 1, 100, 1, '2025-01-14'),
(7, 'bukubaru', 'kukiii', 'penerbittes', '2001', 2, 13, 2, '2025-01-14'),
(8, 'tutiupdate', 'tuti5tes', 'tutitestes', '2016', 4, 13, 4, '2025-01-14'),
(9, 'kopi susu', 'pelaut', 'skok', '2024', 1, 7, 1, '2025-01-14'),
(10, 'lagi1', 'lqgi', 'lagi3', '2039', 4, 22, 4, '2025-01-14'),
(11, 'misal', 'misal', 'misal', '2001', 1, 1, 1, '2025-01-14'),
(12, 'loh', 'loh', 'loh', '2015', 4, 2, 4, '2025-01-14'),
(13, 'loh', 'kenapa', 'sih', '2003', 4, 2, 4, '2025-01-14');

-- --------------------------------------------------------

--
-- Table structure for table `denda`
--

CREATE TABLE `denda` (
  `id_denda` int(11) NOT NULL,
  `id_peminjaman` int(11) NOT NULL,
  `jumlah_denda` decimal(10,2) NOT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `status_pembayaran` enum('Belum Dibayar','Dibayar') DEFAULT 'Belum Dibayar'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `denda`
--

INSERT INTO `denda` (`id_denda`, `id_peminjaman`, `jumlah_denda`, `tanggal_bayar`, `status_pembayaran`) VALUES
(1, 1, 5000.00, '2025-01-05', 'Dibayar'),
(3, 1, 10000.00, '2025-01-12', 'Dibayar'),
(5, 3, 20000.00, '2025-01-14', 'Dibayar'),
(6, 4, 5000.00, '2025-01-15', 'Belum Dibayar'),
(7, 5, 12000.00, '2025-01-16', 'Dibayar');

-- --------------------------------------------------------

--
-- Table structure for table `detailpeminjaman`
--

CREATE TABLE `detailpeminjaman` (
  `id_detail` int(11) NOT NULL,
  `id_peminjaman` int(11) NOT NULL,
  `id_buku` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detailpeminjaman`
--

INSERT INTO `detailpeminjaman` (`id_detail`, `id_peminjaman`, `id_buku`, `jumlah`) VALUES
(1, 1, 1, 1),
(2, 8, 2, 1),
(3, 3, 3, 2),
(4, 2, 4, 1),
(5, 1, 1, 1),
(6, 2, 2, 1),
(7, 3, 3, 2),
(8, 4, 4, 1),
(9, 5, 1, 1),
(10, 9, 1, 1),
(11, 10, 2, 1),
(12, 11, 3, 2),
(13, 12, 4, 1),
(14, 13, 1, 1),
(15, 14, 2, 1),
(16, 15, 3, 2),
(17, 16, 4, 1),
(18, 17, 1, 1),
(19, 18, 2, 1),
(20, 49, 1, 1),
(21, 49, 2, 1),
(22, 49, 3, 1),
(23, 49, 4, 1),
(24, 49, 1, 1),
(25, 49, 2, 1),
(26, 49, 3, 1),
(27, 49, 4, 1),
(28, 49, 1, 1),
(29, 49, 2, 1),
(30, 49, 3, 1),
(31, 49, 4, 1),
(32, 49, 1, 1),
(33, 49, 2, 1),
(34, 49, 3, 1),
(35, 49, 4, 1),
(36, 49, 1, 1),
(37, 49, 2, 1),
(38, 49, 3, 1),
(39, 49, 4, 1),
(40, 49, 1, 1),
(41, 49, 2, 1),
(42, 49, 3, 1),
(43, 49, 4, 1),
(44, 49, 1, 1),
(45, 49, 2, 1),
(46, 49, 3, 1),
(47, 49, 4, 1),
(48, 49, 1, 1),
(49, 49, 2, 1),
(50, 49, 3, 1),
(51, 49, 4, 1),
(52, 49, 1, 1),
(53, 49, 2, 1),
(54, 49, 3, 1),
(55, 49, 4, 1),
(56, 49, 1, 1),
(57, 49, 2, 1),
(58, 49, 3, 1),
(59, 49, 4, 1),
(60, 49, 1, 1),
(61, 49, 2, 1),
(62, 49, 3, 1),
(63, 49, 4, 1),
(64, 49, 1, 1),
(65, 49, 2, 1),
(66, 49, 3, 1),
(67, 49, 4, 1),
(68, 49, 1, 1),
(69, 49, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Fiksi'),
(2, 'Non-Fiksi'),
(3, 'Edukasi'),
(4, 'Komik');

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id_laporan` int(11) NOT NULL,
  `periode` varchar(50) NOT NULL,
  `total_peminjaman` int(11) DEFAULT 0,
  `total_pengembalian` int(11) DEFAULT 0,
  `total_denda` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan`
--

INSERT INTO `laporan` (`id_laporan`, `periode`, `total_peminjaman`, `total_pengembalian`, `total_denda`) VALUES
(1, '2025-01', 2, 1, 15000.00);

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_peminjaman` int(11) NOT NULL,
  `id_anggota` int(11) NOT NULL,
  `id_petugas` int(11) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `status` enum('Dipinjam','Dikembalikan','Denda') DEFAULT 'Dipinjam'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id_peminjaman`, `id_anggota`, `id_petugas`, `tanggal_pinjam`, `tanggal_kembali`, `status`) VALUES
(1, 3, 2, '2025-01-01', '2025-01-05', 'Dikembalikan'),
(2, 3, 2, '2025-01-03', '2025-01-17', 'Dipinjam'),
(3, 4, 2, '2025-01-10', '2025-01-17', 'Dipinjam'),
(4, 5, 2, '2025-01-09', '2025-01-16', 'Dipinjam'),
(5, 6, 2, '2025-01-08', '2025-01-15', 'Dipinjam'),
(6, 7, 2, '2025-01-07', '2025-01-14', 'Dipinjam'),
(7, 8, 2, '2025-01-06', '2025-01-13', 'Dipinjam'),
(8, 7, 2, '2025-01-12', '2025-01-19', 'Dipinjam'),
(9, 4, 2, '2025-01-12', '2025-01-19', 'Dipinjam'),
(10, 5, 2, '2025-01-13', '2025-01-20', 'Dipinjam'),
(11, 6, 2, '2025-01-14', '2025-01-21', 'Dipinjam'),
(12, 7, 2, '2025-01-15', '2025-01-22', 'Dipinjam'),
(13, 8, 2, '2025-01-16', '2025-01-23', 'Dipinjam'),
(14, 9, 2, '2025-01-17', '2025-01-24', 'Dipinjam'),
(15, 4, 2, '2025-01-18', '2025-01-25', 'Dipinjam'),
(16, 5, 2, '2025-01-19', '2025-01-26', 'Dipinjam'),
(17, 6, 2, '2025-01-20', '2025-01-27', 'Dipinjam'),
(18, 7, 2, '2025-01-21', '2025-01-28', 'Dipinjam'),
(19, 4, 2, '2025-01-12', '2025-01-19', 'Dipinjam'),
(20, 4, 2, '2025-01-13', '2025-01-20', 'Dipinjam'),
(21, 4, 2, '2025-01-14', '2025-01-21', 'Dipinjam'),
(22, 4, 2, '2025-01-15', '2025-01-22', 'Dipinjam'),
(23, 4, 2, '2025-01-16', '2025-01-23', 'Dipinjam'),
(24, 5, 2, '2025-01-17', '2025-01-24', 'Dipinjam'),
(25, 5, 2, '2025-01-18', '2025-01-25', 'Dipinjam'),
(26, 5, 2, '2025-01-19', '2025-01-26', 'Dipinjam'),
(27, 5, 2, '2025-01-20', '2025-01-27', 'Dipinjam'),
(28, 5, 2, '2025-01-21', '2025-01-28', 'Dipinjam'),
(29, 6, 2, '2025-01-22', '2025-01-29', 'Dipinjam'),
(30, 6, 2, '2025-01-23', '2025-01-30', 'Dipinjam'),
(31, 6, 2, '2025-01-24', '2025-01-31', 'Dipinjam'),
(32, 6, 2, '2025-01-25', '2025-02-01', 'Dipinjam'),
(33, 6, 2, '2025-01-26', '2025-02-02', 'Dipinjam'),
(34, 7, 2, '2025-01-27', '2025-02-03', 'Dipinjam'),
(35, 7, 2, '2025-01-28', '2025-02-04', 'Dipinjam'),
(36, 7, 2, '2025-01-29', '2025-02-05', 'Dipinjam'),
(37, 7, 2, '2025-01-30', '2025-02-06', 'Dipinjam'),
(38, 7, 2, '2025-01-31', '2025-02-07', 'Dipinjam'),
(39, 8, 2, '2025-02-01', '2025-02-08', 'Dipinjam'),
(40, 8, 2, '2025-02-02', '2025-02-09', 'Dipinjam'),
(41, 8, 2, '2025-02-03', '2025-02-10', 'Dipinjam'),
(42, 8, 2, '2025-02-04', '2025-02-11', 'Dipinjam'),
(43, 8, 2, '2025-02-05', '2025-02-12', 'Dipinjam'),
(44, 9, 2, '2025-02-06', '2025-02-13', 'Dipinjam'),
(45, 9, 2, '2025-02-07', '2025-02-14', 'Dipinjam'),
(46, 9, 2, '2025-02-08', '2025-02-15', 'Dipinjam'),
(47, 9, 2, '2025-02-09', '2025-02-16', 'Dipinjam'),
(48, 9, 2, '2025-02-10', '2025-02-17', 'Dipinjam'),
(49, 4, 2, '2025-01-12', '2025-01-19', 'Dipinjam'),
(50, 4, 2, '2025-01-13', '2025-01-20', 'Dipinjam'),
(51, 4, 2, '2025-01-14', '2025-01-21', 'Dipinjam'),
(52, 4, 2, '2025-01-15', '2025-01-22', 'Dipinjam'),
(53, 4, 2, '2025-01-16', '2025-01-23', 'Dipinjam'),
(54, 5, 2, '2025-01-17', '2025-01-24', 'Dipinjam'),
(55, 5, 2, '2025-01-18', '2025-01-25', 'Dipinjam'),
(56, 5, 2, '2025-01-19', '2025-01-26', 'Dipinjam'),
(57, 5, 2, '2025-01-20', '2025-01-27', 'Dipinjam'),
(58, 5, 2, '2025-01-21', '2025-01-28', 'Dipinjam'),
(59, 6, 2, '2025-01-22', '2025-01-29', 'Dipinjam'),
(60, 6, 2, '2025-01-23', '2025-01-30', 'Dipinjam'),
(61, 6, 2, '2025-01-24', '2025-01-31', 'Dipinjam'),
(62, 6, 2, '2025-01-25', '2025-02-01', 'Dipinjam'),
(63, 6, 2, '2025-01-26', '2025-02-02', 'Dipinjam'),
(64, 7, 2, '2025-01-27', '2025-02-03', 'Dipinjam'),
(65, 7, 2, '2025-01-28', '2025-02-04', 'Dipinjam'),
(66, 7, 2, '2025-01-29', '2025-02-05', 'Dipinjam'),
(67, 7, 2, '2025-01-30', '2025-02-06', 'Dipinjam'),
(68, 7, 2, '2025-01-31', '2025-02-07', 'Dipinjam'),
(69, 8, 2, '2025-02-01', '2025-02-08', 'Dipinjam'),
(70, 8, 2, '2025-02-02', '2025-02-09', 'Dipinjam'),
(71, 8, 2, '2025-02-03', '2025-02-10', 'Dipinjam'),
(72, 8, 2, '2025-02-04', '2025-02-11', 'Dipinjam'),
(73, 8, 2, '2025-02-05', '2025-02-12', 'Dipinjam'),
(74, 9, 2, '2025-02-06', '2025-02-13', 'Dipinjam'),
(75, 9, 2, '2025-02-07', '2025-02-14', 'Dipinjam'),
(76, 9, 2, '2025-02-08', '2025-02-15', 'Dipinjam'),
(77, 9, 2, '2025-02-09', '2025-02-16', 'Dipinjam'),
(78, 9, 2, '2025-02-10', '2025-02-17', 'Dipinjam');

-- --------------------------------------------------------

--
-- Table structure for table `rak`
--

CREATE TABLE `rak` (
  `id_rak` int(11) NOT NULL,
  `kode_rak` varchar(50) NOT NULL,
  `nama_rak` varchar(100) NOT NULL,
  `lokasi_rak` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rak`
--

INSERT INTO `rak` (`id_rak`, `kode_rak`, `nama_rak`, `lokasi_rak`) VALUES
(1, 'A1', 'Rak Fiksi', 'Lantai 1 - Sisi Barat'),
(2, 'B2', 'Rak Non-Fiksi', 'Lantai 1 - Sisi Timur'),
(3, 'C3', 'Rak Edukasi', 'Lantai 2 - Tengah'),
(4, 'D4', 'Rak Komik', 'Lantai 2 - Sudut');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('anggota','petugas','admin') NOT NULL,
  `tanggal_daftar` date DEFAULT curdate(),
  `alamat` varchar(255) DEFAULT NULL,
  `nomor_hp` varchar(15) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama`, `username`, `password`, `role`, `tanggal_daftar`, `alamat`, `nomor_hp`, `email`) VALUES
(1, 'admin', 'adminuser', '$2y$10$HFu3eUwev1aj9uTkTZSp7utOaMIyqc8JsLAVVJGAQ0AtbDkvEt5Ym', 'admin', '2025-01-15', 'Jl. Contoh Alamat No.1', '081234567890', 'adminuser@gmail.com'),
(2, 'petugas', 'petugasuser', '$2y$10$UmUWGY4tiAJS5KXckCY9te3wjzi4Ji2SU5NMyLXInTSY/jwImrF9O', 'petugas', '2024-12-26', 'Jl. Contoh Alamat No.2', '081234567891', 'petugasuser@gmail.com'),
(3, 'anggotauser', 'anggotauser', 'jadi bingung', 'anggota', '2024-12-26', 'Jl. Contoh Alamat No.3', '081234567892', 'anggotauser@gmail.com'),
(4, 'anggota1', 'anggota1', '7c6a180b36896a0a8c02787eeafb0e4c', 'anggota', '2025-01-11', 'Jl. Anggota 1', '081234567893', 'anggota1@gmail.com'),
(5, 'anggotabaru', 'anggotabaru', 'f425edfb827a469cb125a0af99fe6ad1', 'anggota', '0000-00-00', 'Jl. Anggota Baru', '081234567894', 'anggotabaru@gmail.com'),
(6, 'anggota2', 'anggota2', '6cb75f652a9b52798eb6cf2201057c73', 'anggota', '2025-01-11', 'Jl. Anggota 2', '081234567895', 'anggota2@gmail.com'),
(7, 'anggota3', 'anggota3', '819b0643d6b89dc9b579fdfc9094f28e', 'anggota', '2025-01-11', 'Jl. Anggota 3', '081234567896', 'anggota3@gmail.com'),
(8, 'anggota4', 'anggota4', '34cc93ece0ba9e3f6f235d4af979b16c', 'anggota', '2025-01-11', 'Jl. Anggota 4', '081234567897', 'anggota4@gmail.com'),
(9, 'anggota5', 'anggota5', 'db0edd04aaac4506f7edab03ac855d56', 'anggota', '2025-01-11', 'Jl. Anggota 5', '081234567898', 'anggota5@gmail.com'),
(19, 'testimoni', 'test', 'kjhhk', 'anggota', '2025-01-14', 'plis deh lo', '08987856565', 'est123@gmail.com'),
(21, 'hts', 'hts', '', 'anggota', '2025-01-14', 'hts', '08887', 'hts@mail.com'),
(22, 'tambah', 'tambah', '', 'anggota', '2025-01-14', 'tambah', '0876543456', 'tambah@gmail.com'),
(23, 'petugas2', 'petugas', '', 'petugas', '2025-01-14', 'contoh alamat petugas', '0863552387', 'petugas@gmail.com'),
(24, 'Petugas shift ', 'petugasshift', '', 'petugas', '2025-01-14', 'Yogyakarta', '038726352', 'petugasshift@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id_buku`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `id_rak` (`id_rak`);

--
-- Indexes for table `denda`
--
ALTER TABLE `denda`
  ADD PRIMARY KEY (`id_denda`),
  ADD KEY `id_peminjaman` (`id_peminjaman`);

--
-- Indexes for table `detailpeminjaman`
--
ALTER TABLE `detailpeminjaman`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_peminjaman` (`id_peminjaman`),
  ADD KEY `id_buku` (`id_buku`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id_laporan`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_peminjaman`),
  ADD KEY `id_anggota` (`id_anggota`),
  ADD KEY `id_petugas` (`id_petugas`);

--
-- Indexes for table `rak`
--
ALTER TABLE `rak`
  ADD PRIMARY KEY (`id_rak`),
  ADD UNIQUE KEY `kode_rak` (`kode_rak`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `buku`
--
ALTER TABLE `buku`
  MODIFY `id_buku` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `denda`
--
ALTER TABLE `denda`
  MODIFY `id_denda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `detailpeminjaman`
--
ALTER TABLE `detailpeminjaman`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `rak`
--
ALTER TABLE `rak`
  MODIFY `id_rak` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `buku`
--
ALTER TABLE `buku`
  ADD CONSTRAINT `buku_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`),
  ADD CONSTRAINT `buku_ibfk_2` FOREIGN KEY (`id_rak`) REFERENCES `rak` (`id_rak`);

--
-- Constraints for table `denda`
--
ALTER TABLE `denda`
  ADD CONSTRAINT `denda_ibfk_1` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`);

--
-- Constraints for table `detailpeminjaman`
--
