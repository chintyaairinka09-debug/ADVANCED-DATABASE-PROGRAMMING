-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 09, 2026 at 06:56 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_tokoku`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `CariPelanggan` (IN `p_id` INT)   BEGIN
    SELECT *
    FROM tbl_pelanggan
    WHERE id_pelanggan = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `HitungPelanggan` (OUT `jumlah` INT)   BEGIN
    SELECT COUNT(*)
    INTO jumlah
    FROM tbl_pelanggan;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `LaporanTransaksi` (IN `p_id_pelanggan` INT)   BEGIN
    SELECT
        t.id_transaksi,
        t.tanggal,
        p.id_pelanggan,
        p.nama_pelanggan,
        p.kota,
        t.total_belanja
    FROM tbl_transaksi t
    INNER JOIN tbl_pelanggan p
        ON t.id_pelanggan = p.id_pelanggan
    WHERE p.id_pelanggan = p_id_pelanggan;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_tambah_transaksi_bab6` (IN `p_id_pelanggan` INT, IN `p_tanggal` DATE, IN `p_id_produk` INT, IN `p_jumlah` INT, IN `p_diskon` DECIMAL(5,2), OUT `p_id_transaksi` INT, OUT `p_total` DECIMAL(12,2))   BEGIN

    DECLARE v_jumlah INT DEFAULT 0;
    DECLARE v_nama_produk VARCHAR(100);
    DECLARE v_harga DECIMAL(12,2);
    DECLARE v_stok INT;
    DECLARE v_subtotal DECIMAL(12,2);
    DECLARE v_total DECIMAL(12,2);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    IF p_jumlah IS NULL OR p_jumlah <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Jumlah produk harus lebih dari nol';
    END IF;

    IF p_diskon IS NULL OR p_diskon < 0 OR p_diskon > 100 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Persentase diskon tidak valid';
    END IF;

    SELECT COUNT(*)
    INTO v_jumlah
    FROM tbl_pelanggan
    WHERE id_pelanggan = p_id_pelanggan;

    IF v_jumlah = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Pelanggan tidak ditemukan';
    END IF;

    SELECT COUNT(*)
    INTO v_jumlah
    FROM tbl_produk
    WHERE id_produk = p_id_produk;

    IF v_jumlah = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Produk tidak ditemukan';
    END IF;

    SELECT nama_produk, harga, stok
    INTO v_nama_produk, v_harga, v_stok
    FROM tbl_produk
    WHERE id_produk = p_id_produk;

    IF v_stok < p_jumlah THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stok produk tidak mencukupi';
    END IF;

    SET v_subtotal = fn_total_harga_bab6(
        v_harga,
        p_jumlah
    );

    SET v_total = fn_harga_setelah_diskon_bab6(
        v_subtotal,
        p_diskon
    );

    INSERT INTO tbl_transaksi
    (tanggal, id_pelanggan, total_belanja)
    VALUES
    (p_tanggal, p_id_pelanggan, v_total);

    SET p_id_transaksi = LAST_INSERT_ID();

    INSERT INTO tbl_detail_transaksi
    (
        id_transaksi,
        id_produk,
        nama_produk,
        jumlah,
        harga
    )
    VALUES
    (
        p_id_transaksi,
        p_id_produk,
        v_nama_produk,
        p_jumlah,
        v_harga
    );

    UPDATE tbl_produk
    SET stok = stok - p_jumlah
    WHERE id_produk = p_id_produk;

    COMMIT;

    SET p_total = v_total;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `TambahPelanggan` (IN `p_nama` VARCHAR(100), IN `p_kota` VARCHAR(50))   BEGIN
    INSERT INTO tbl_pelanggan
    (nama_pelanggan, kota)
    VALUES
    (p_nama, p_kota);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `TampilPelanggan` ()   BEGIN
    SELECT * FROM tbl_pelanggan;
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_harga_setelah_diskon_bab6` (`p_total` DECIMAL(12,2), `p_diskon` DECIMAL(5,2)) RETURNS DECIMAL(12,2) DETERMINISTIC NO SQL BEGIN
    RETURN p_total - (p_total * p_diskon / 100);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_total_harga_bab6` (`p_harga` DECIMAL(12,2), `p_jumlah` INT) RETURNS DECIMAL(12,2) DETERMINISTIC NO SQL BEGIN
    RETURN p_harga * p_jumlah;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `HitungDiskon` (`p_harga` DECIMAL(12,2), `p_diskon` DECIMAL(5,2)) RETURNS DECIMAL(12,2) DETERMINISTIC NO SQL BEGIN
    RETURN p_harga - (p_harga * p_diskon / 100);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `HitungPajak` (`p_harga` DECIMAL(12,2)) RETURNS DECIMAL(12,2) DETERMINISTIC NO SQL BEGIN
    RETURN p_harga * 0.11;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `HitungTotal` (`p_harga` DECIMAL(12,2), `p_jumlah` INT) RETURNS DECIMAL(12,2) DETERMINISTIC NO SQL BEGIN
    RETURN p_harga * p_jumlah;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `KategoriBelanja` (`p_total` DECIMAL(12,2)) RETURNS VARCHAR(30) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC NO SQL BEGIN
    RETURN CASE
        WHEN p_total >= 1000000 THEN 'Pelanggan Gold'
        WHEN p_total >= 500000 THEN 'Pelanggan Silver'
        ELSE 'Pelanggan Reguler'
    END;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `KonversiNilai` (`p_nilai` INT) RETURNS VARCHAR(10) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC NO SQL BEGIN
    RETURN CASE
        WHEN p_nilai >= 90 THEN 'A'
        WHEN p_nilai >= 80 THEN 'B'
        WHEN p_nilai >= 70 THEN 'C'
        WHEN p_nilai >= 60 THEN 'D'
        ELSE 'E'
    END;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `log_pelanggan`
--

CREATE TABLE `log_pelanggan` (
  `id_log` int(11) NOT NULL,
  `aktivitas` varchar(50) DEFAULT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_pelanggan`
--

INSERT INTO `log_pelanggan` (`id_log`, `aktivitas`, `id_pelanggan`, `waktu`) VALUES
(1, 'INSERT PELANGGAN', 13, '2026-11-09 04:24:01');

-- --------------------------------------------------------

--
-- Table structure for table `log_perubahan_pelanggan`
--

CREATE TABLE `log_perubahan_pelanggan` (
  `id_log` int(11) NOT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `nama_lama` varchar(100) DEFAULT NULL,
  `nama_baru` varchar(100) DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_perubahan_pelanggan`
--

INSERT INTO `log_perubahan_pelanggan` (`id_log`, `id_pelanggan`, `nama_lama`, `nama_baru`, `waktu`) VALUES
(1, 13, 'Pelanggan Uji Bab 5', 'Pelanggan Update Bab 5', '2026-11-09 04:25:12');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_cabang`
--

CREATE TABLE `tbl_cabang` (
  `id_cabang` int(11) NOT NULL,
  `nama_cabang` varchar(100) DEFAULT NULL,
  `kota` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_cabang`
--

INSERT INTO `tbl_cabang` (`id_cabang`, `nama_cabang`, `kota`) VALUES
(1, 'Cabang Ponorogo', 'Ponorogo'),
(2, 'Cabang Jakarta', 'Jakarta'),
(3, 'Cabang Bandung', 'Bandung'),
(4, 'Cabang Kediri', 'Kediri');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_detail_transaksi`
--

CREATE TABLE `tbl_detail_transaksi` (
  `id_detail` int(11) NOT NULL,
  `id_transaksi` int(11) DEFAULT NULL,
  `nama_produk` varchar(100) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `harga` decimal(12,2) DEFAULT NULL,
  `id_produk` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_detail_transaksi`
--

INSERT INTO `tbl_detail_transaksi` (`id_detail`, `id_transaksi`, `nama_produk`, `jumlah`, `harga`, `id_produk`) VALUES
(1, 1, 'Beras 5 Kg', 2, 75000.00, 1),
(2, 1, 'Minyak Goreng', 1, 25000.00, 2),
(3, 2, 'Gula Pasir', 3, 15000.00, 3),
(4, 2, 'Teh Celup', 2, 12000.00, 6),
(5, 3, 'Kopi Bubuk', 2, 18000.00, 7),
(6, 4, 'Susu Kotak', 5, 10000.00, 8),
(7, 5, 'Mi Instan', 10, 3500.00, 9),
(8, 6, 'Sabun Mandi', 4, 8000.00, 10),
(9, 7, 'Telur Ayam', 2, 30000.00, 5),
(10, 8, 'Tepung Terigu', 3, 12000.00, 4),
(11, 21, 'Beras 5 Kg', 2, 75000.00, 1),
(12, 22, 'Beras 5 Kg', 2, 75000.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_kategori`
--

CREATE TABLE `tbl_kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_kategori`
--

INSERT INTO `tbl_kategori` (`id_kategori`, `nama_kategori`) VALUES
(4, 'Kebersihan'),
(3, 'Makanan'),
(2, 'Minuman'),
(1, 'Sembako');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_log`
--

CREATE TABLE `tbl_log` (
  `id_log` int(11) NOT NULL,
  `nama_tabel` varchar(50) DEFAULT NULL,
  `id_data` int(11) DEFAULT NULL,
  `aktivitas` varchar(50) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_log`
--

INSERT INTO `tbl_log` (`id_log`, `nama_tabel`, `id_data`, `aktivitas`, `keterangan`, `waktu`) VALUES
(1, 'tbl_transaksi', 22, 'INSERT', 'Transaksi baru dengan total belanja 135000.00', '2026-11-09 05:49:55'),
(2, 'tbl_transaksi', 22, 'UPDATE', 'Total lama: 135000.00 | Total baru: 145000.00', '2026-11-09 05:50:34'),
(4, 'tbl_log', 0, 'UJI COMMIT', 'Percobaan penyimpanan transaksi', '2026-11-09 05:51:20');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_pelanggan`
--

CREATE TABLE `tbl_pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `nama_pelanggan` varchar(100) DEFAULT NULL,
  `kota` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_pelanggan`
--

INSERT INTO `tbl_pelanggan` (`id_pelanggan`, `nama_pelanggan`, `kota`) VALUES
(1, 'Andi', 'Ponorogo'),
(2, 'Budi', 'Madiun'),
(3, 'Citra', 'Surabaya'),
(4, 'Joko', 'Solo'),
(5, 'Rizky', 'Malang'),
(6, 'Dewi', 'Semarang'),
(7, 'Nina', 'Denpasar'),
(8, 'Rahma', 'Yogyakarta'),
(9, 'Fajar', 'Jakarta'),
(10, 'Putri', 'Medan'),
(11, 'Salsa', 'Bandung'),
(12, 'Dewi', 'Ponorogo'),
(13, 'Pelanggan Update Bab 5', 'Ponorogo');

--
-- Triggers `tbl_pelanggan`
--
DELIMITER $$
CREATE TRIGGER `after_delete_pelanggan` AFTER DELETE ON `tbl_pelanggan` FOR EACH ROW BEGIN

    INSERT INTO log_pelanggan
    (aktivitas, id_pelanggan)
    VALUES
    ('DELETE PELANGGAN', OLD.id_pelanggan);

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_insert_pelanggan` AFTER INSERT ON `tbl_pelanggan` FOR EACH ROW BEGIN

    INSERT INTO log_pelanggan
    (aktivitas, id_pelanggan)
    VALUES
    ('INSERT PELANGGAN', NEW.id_pelanggan);

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_update_pelanggan` AFTER UPDATE ON `tbl_pelanggan` FOR EACH ROW BEGIN

    INSERT INTO log_perubahan_pelanggan
    (
        id_pelanggan,
        nama_lama,
        nama_baru
    )
    VALUES
    (
        OLD.id_pelanggan,
        OLD.nama_pelanggan,
        NEW.nama_pelanggan
    );

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_produk`
--

CREATE TABLE `tbl_produk` (
  `id_produk` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `harga` decimal(12,2) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_produk`
--

INSERT INTO `tbl_produk` (`id_produk`, `id_kategori`, `nama_produk`, `harga`, `stok`) VALUES
(1, 1, 'Beras 5 Kg', 75000.00, 96),
(2, 1, 'Minyak Goreng', 25000.00, 100),
(3, 1, 'Gula Pasir', 15000.00, 100),
(4, 1, 'Tepung Terigu', 12000.00, 100),
(5, 1, 'Telur Ayam', 30000.00, 100),
(6, 2, 'Teh Celup', 12000.00, 100),
(7, 2, 'Kopi Bubuk', 18000.00, 100),
(8, 2, 'Susu Kotak', 10000.00, 100),
(9, 3, 'Mi Instan', 3500.00, 100),
(10, 4, 'Sabun Mandi', 8000.00, 100);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_transaksi`
--

CREATE TABLE `tbl_transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `total_belanja` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_transaksi`
--

INSERT INTO `tbl_transaksi` (`id_transaksi`, `tanggal`, `id_pelanggan`, `total_belanja`) VALUES
(1, '2026-10-01', 1, 250000.00),
(2, '2026-10-02', 2, 350000.00),
(3, '2026-10-03', 1, 150000.00),
(4, '2026-10-04', 3, 500000.00),
(5, '2026-10-05', 4, 275000.00),
(6, '2026-10-06', 5, 425000.00),
(7, '2026-10-07', 6, 180000.00),
(8, '2026-10-08', 7, 650000.00),
(9, '2026-10-09', 8, 320000.00),
(10, '2026-10-10', 9, 550000.00),
(11, '2026-10-11', 10, 750000.00),
(12, '2026-10-12', 1, 200000.00),
(13, '2026-10-13', 2, 375000.00),
(14, '2026-10-14', 3, 825000.00),
(15, '2026-10-15', 4, 150000.00),
(16, '2026-10-16', 5, 290000.00),
(17, '2026-10-17', 6, 475000.00),
(18, '2026-10-18', 7, 900000.00),
(19, '2026-10-19', 8, 225000.00),
(20, '2026-10-20', 9, 600000.00),
(21, '2026-11-09', 1, 135000.00),
(22, '2026-11-09', 1, 145000.00);

--
-- Triggers `tbl_transaksi`
--
DELIMITER $$
CREATE TRIGGER `before_insert_transaksi` BEFORE INSERT ON `tbl_transaksi` FOR EACH ROW BEGIN

    IF NEW.total_belanja < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT =
        'Total belanja tidak boleh negatif';
    END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_log_transaksi_insert_bab6` AFTER INSERT ON `tbl_transaksi` FOR EACH ROW BEGIN

    INSERT INTO tbl_log
    (nama_tabel, id_data, aktivitas, keterangan)
    VALUES
    (
        'tbl_transaksi',
        NEW.id_transaksi,
        'INSERT',
        CONCAT(
            'Transaksi baru dengan total belanja ',
            NEW.total_belanja
        )
    );

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_log_transaksi_update_bab6` AFTER UPDATE ON `tbl_transaksi` FOR EACH ROW BEGIN

    INSERT INTO tbl_log
    (nama_tabel, id_data, aktivitas, keterangan)
    VALUES
    (
        'tbl_transaksi',
        NEW.id_transaksi,
        'UPDATE',
        CONCAT(
            'Total lama: ',
            OLD.total_belanja,
            ' | Total baru: ',
            NEW.total_belanja
        )
    );

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_laporan_penjualan_bab6`
-- (See below for the actual view)
--
CREATE TABLE `view_laporan_penjualan_bab6` (
`id_transaksi` int(11)
,`tanggal` date
,`nama_pelanggan` varchar(100)
,`jumlah_jenis_produk` bigint(21)
,`total_barang` decimal(32,0)
,`total_belanja` decimal(12,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_pelanggan`
-- (See below for the actual view)
--
CREATE TABLE `view_pelanggan` (
`id_pelanggan` int(11)
,`nama_pelanggan` varchar(100)
,`kota` varchar(50)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_total_belanja`
-- (See below for the actual view)
--
CREATE TABLE `view_total_belanja` (
`id_pelanggan` int(11)
,`nama_pelanggan` varchar(100)
,`total_transaksi` bigint(21)
,`total_belanja` decimal(34,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_transaksi_pelanggan`
-- (See below for the actual view)
--
CREATE TABLE `view_transaksi_pelanggan` (
`id_transaksi` int(11)
,`tanggal` date
,`nama_pelanggan` varchar(100)
,`kota` varchar(50)
,`total_belanja` decimal(12,2)
);

-- --------------------------------------------------------

--
-- Structure for view `view_laporan_penjualan_bab6`
--
DROP TABLE IF EXISTS `view_laporan_penjualan_bab6`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_laporan_penjualan_bab6`  AS SELECT `t`.`id_transaksi` AS `id_transaksi`, `t`.`tanggal` AS `tanggal`, `p`.`nama_pelanggan` AS `nama_pelanggan`, count(`d`.`id_detail`) AS `jumlah_jenis_produk`, coalesce(sum(`d`.`jumlah`),0) AS `total_barang`, `t`.`total_belanja` AS `total_belanja` FROM ((`tbl_transaksi` `t` join `tbl_pelanggan` `p` on(`t`.`id_pelanggan` = `p`.`id_pelanggan`)) left join `tbl_detail_transaksi` `d` on(`t`.`id_transaksi` = `d`.`id_transaksi`)) GROUP BY `t`.`id_transaksi`, `t`.`tanggal`, `p`.`nama_pelanggan`, `t`.`total_belanja` ;

-- --------------------------------------------------------

--
-- Structure for view `view_pelanggan`
--
DROP TABLE IF EXISTS `view_pelanggan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_pelanggan`  AS SELECT `tbl_pelanggan`.`id_pelanggan` AS `id_pelanggan`, `tbl_pelanggan`.`nama_pelanggan` AS `nama_pelanggan`, `tbl_pelanggan`.`kota` AS `kota` FROM `tbl_pelanggan` ;

-- --------------------------------------------------------

--
-- Structure for view `view_total_belanja`
--
DROP TABLE IF EXISTS `view_total_belanja`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_total_belanja`  AS SELECT `p`.`id_pelanggan` AS `id_pelanggan`, `p`.`nama_pelanggan` AS `nama_pelanggan`, count(`t`.`id_transaksi`) AS `total_transaksi`, coalesce(sum(`t`.`total_belanja`),0) AS `total_belanja` FROM (`tbl_pelanggan` `p` left join `tbl_transaksi` `t` on(`p`.`id_pelanggan` = `t`.`id_pelanggan`)) GROUP BY `p`.`id_pelanggan`, `p`.`nama_pelanggan` ;

-- --------------------------------------------------------

--
-- Structure for view `view_transaksi_pelanggan`
--
DROP TABLE IF EXISTS `view_transaksi_pelanggan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_transaksi_pelanggan`  AS SELECT `t`.`id_transaksi` AS `id_transaksi`, `t`.`tanggal` AS `tanggal`, `p`.`nama_pelanggan` AS `nama_pelanggan`, `p`.`kota` AS `kota`, `t`.`total_belanja` AS `total_belanja` FROM (`tbl_transaksi` `t` join `tbl_pelanggan` `p` on(`t`.`id_pelanggan` = `p`.`id_pelanggan`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `log_pelanggan`
--
ALTER TABLE `log_pelanggan`
  ADD PRIMARY KEY (`id_log`);

--
-- Indexes for table `log_perubahan_pelanggan`
--
ALTER TABLE `log_perubahan_pelanggan`
  ADD PRIMARY KEY (`id_log`);

--
-- Indexes for table `tbl_cabang`
--
ALTER TABLE `tbl_cabang`
  ADD PRIMARY KEY (`id_cabang`);

--
-- Indexes for table `tbl_detail_transaksi`
--
ALTER TABLE `tbl_detail_transaksi`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `fk_detail_produk_bab6` (`id_produk`),
  ADD KEY `idx_detail_transaksi_produk_bab6` (`id_transaksi`,`id_produk`);

--
-- Indexes for table `tbl_kategori`
--
ALTER TABLE `tbl_kategori`
  ADD PRIMARY KEY (`id_kategori`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indexes for table `tbl_log`
--
ALTER TABLE `tbl_log`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `idx_log_waktu_bab6` (`waktu`);

--
-- Indexes for table `tbl_pelanggan`
--
ALTER TABLE `tbl_pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indexes for table `tbl_produk`
--
ALTER TABLE `tbl_produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD UNIQUE KEY `nama_produk` (`nama_produk`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `tbl_transaksi`
--
ALTER TABLE `tbl_transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_pelanggan` (`id_pelanggan`),
  ADD KEY `idx_transaksi_tanggal_bab6` (`tanggal`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `log_pelanggan`
--
ALTER TABLE `log_pelanggan`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `log_perubahan_pelanggan`
--
ALTER TABLE `log_perubahan_pelanggan`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_cabang`
--
ALTER TABLE `tbl_cabang`
  MODIFY `id_cabang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_detail_transaksi`
--
ALTER TABLE `tbl_detail_transaksi`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_kategori`
--
ALTER TABLE `tbl_kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_log`
--
ALTER TABLE `tbl_log`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_pelanggan`
--
ALTER TABLE `tbl_pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tbl_produk`
--
ALTER TABLE `tbl_produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_transaksi`
--
ALTER TABLE `tbl_transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_detail_transaksi`
--
ALTER TABLE `tbl_detail_transaksi`
  ADD CONSTRAINT `fk_detail_produk_bab6` FOREIGN KEY (`id_produk`) REFERENCES `tbl_produk` (`id_produk`),
  ADD CONSTRAINT `tbl_detail_transaksi_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `tbl_transaksi` (`id_transaksi`);

--
-- Constraints for table `tbl_produk`
--
ALTER TABLE `tbl_produk`
  ADD CONSTRAINT `tbl_produk_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `tbl_kategori` (`id_kategori`);

--
-- Constraints for table `tbl_transaksi`
--
ALTER TABLE `tbl_transaksi`
  ADD CONSTRAINT `tbl_transaksi_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `tbl_pelanggan` (`id_pelanggan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
