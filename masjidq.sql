-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 28, 2021 at 08:42 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `masjidq`
--

-- --------------------------------------------------------

--
-- Table structure for table `jadwal`
--

CREATE TABLE `jadwal` (
  `no` int(11) NOT NULL,
  `id_masjid` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam` time NOT NULL,
  `kegiatan` varchar(100) NOT NULL,
  `keterangan` text NOT NULL,
  `pemateri` text NOT NULL,
  `kebutuhan` int(11) NOT NULL DEFAULT 0,
  `terkumpul` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `jadwal`
--

INSERT INTO `jadwal` (`no`, `id_masjid`, `tanggal`, `jam`, `kegiatan`, `keterangan`, `pemateri`, `kebutuhan`, `terkumpul`) VALUES
(1, 1, '2021-04-12', '18:00:00', 'Kajian Hadits Shalat Dzuhur Setelah Shalat Jum\'at', 'Infaq Masjid Annur<br> BSM: 7098979756 a.n. Masjid An nur Sidoarjo', 'Ust. Dr. H. Zainuddin MZ. Lc. MA.', 0, 0),
(2, 1, '2021-04-10', '18:00:00', 'Pengajian', 'Infaq Masjid Annur<br> BSM: 7098979756 a.n. Masjid An nur Sidoarjo', 'Ust. Dr. H. Zainuddin MZ. Lc. MA.', 0, 0),
(3, 1, '2021-04-12', '18:00:00', 'test', 'tes', 'Ust. Dr. H. Zainuddin MZ. Lc. MA.', 0, 0),
(4, 1, '2021-04-15', '18:00:00', 'AAAAAaaaaaaa', 'aaaaafsadf sa', '', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `masjid`
--

CREATE TABLE `masjid` (
  `id` int(11) NOT NULL,
  `nama` varchar(200) NOT NULL,
  `alamat` text NOT NULL,
  `lat` text NOT NULL,
  `lng` text NOT NULL,
  `telepon` text NOT NULL,
  `website` varchar(100) NOT NULL,
  `keterangan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `masjid`
--

INSERT INTO `masjid` (`id`, `nama`, `alamat`, `lat`, `lng`, `telepon`, `website`, `keterangan`) VALUES
(1, 'Masjid An-Nur Sidoarjo', 'Jl. Majapahit No.666 B, Sidowayah, Celep, Kec. Sidoarjo, Kabupaten Sidoarjo, Jawa Timur 61215', '-7.466504220091927', '112.7173286676407', '08123456789', 'https://masjidannursidoarjo.org;https://youtube.com/annursidoarjo;https://twitter.com/annursidoarjo', 'Luas Tanah : 3.600 m2\r\nStatus Tanah : SHM\r\nTahun Berdiri : 1975'),
(2, 'Masjid Agung Sidoarjo ꦩꦱ꧀ꦗꦶꦝꦒꦸꦁꦱꦶꦢꦴꦗ꧀ꦗꦂ', 'Jalan Sultan Agung No. 36, Magersari, Sidoarjo, Gajah Timur, Magersari, Kec. Sidoarjo, Kabupaten Sidoarjo, Jawa Timur 60294', '-7.4458316982063035', '112.7167680859566', '', '', ''),
(3, 'Masjid Nur Rohmah', 'Jl. Raya Sruni, Dusun Sruni, Sruni, Kec. Gedangan, Kabupaten Sidoarjo, Jawa Timur 61254', '-7.400262161878621', '112.72688710635524', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `username` varchar(100) NOT NULL,
  `password` varchar(40) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `hp` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `id_masjid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`username`, `password`, `nama`, `hp`, `email`, `alamat`, `id_masjid`) VALUES
('admin', 'a4dfc07caecd2f8614158cf4aa71646765e666d1', 'Administrator', '08123456789', 'mail@email.com', 'Jln. Pahlawan Indonesia', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`no`);

--
-- Indexes for table `masjid`
--
ALTER TABLE `masjid`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `masjid`
--
ALTER TABLE `masjid`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
