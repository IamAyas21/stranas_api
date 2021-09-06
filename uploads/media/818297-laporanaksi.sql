-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 28 Agu 2021 pada 21.10
-- Versi Server: 10.1.13-MariaDB
-- PHP Version: 5.6.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `stratone_stranas`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporanaksi`
--

CREATE TABLE `laporanaksi` (
  `Id` int(11) NOT NULL,
  `Category` varchar(50) NOT NULL,
  `Title` varchar(150) NOT NULL,
  `FileName` varchar(250) NOT NULL,
  `FileUrl` longtext NOT NULL,
  `Summary` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `laporanaksi`
--

INSERT INTO `laporanaksi` (`Id`, `Category`, `Title`, `FileName`, `FileUrl`, `Summary`) VALUES
(1, 'Laporan Aksi Tahunan', 'Kokom', 'jajdjasjd dajdjs', 'www.ccn.co.id', 'dadsad dad adasdsa dsad sadsadsa'),
(2, 'Laporan Aksi Tahunan', 'dIMAS', 'jajdjasjd dajdjs', 'www.ccn.co.id', 'dadsad dad adasdsa dsad sadsadsa');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `laporanaksi`
--
ALTER TABLE `laporanaksi`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `laporanaksi`
--
ALTER TABLE `laporanaksi`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
