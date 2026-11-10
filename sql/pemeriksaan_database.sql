-- Pemeriksaan non-destruktif untuk aplikasi Penjualan Toko.
-- Jalankan setelah memilih database db_tokoku.

USE db_tokoku;

SELECT DATABASE() AS database_aktif;
SHOW TABLES;

DESCRIBE tbl_pelanggan;
DESCRIBE tbl_produk;
DESCRIBE tbl_kategori;
DESCRIBE tbl_transaksi;
DESCRIBE tbl_detail_transaksi;
DESCRIBE tbl_log;

SHOW CREATE VIEW view_laporan_penjualan_bab6;
SHOW CREATE PROCEDURE sp_tambah_transaksi_bab6;
SHOW CREATE FUNCTION fn_total_harga_bab6;
SHOW CREATE FUNCTION fn_harga_setelah_diskon_bab6;
SHOW TRIGGERS;

-- Pastikan kolom id_produk telah ditambahkan pada tbl_detail_transaksi.
SHOW COLUMNS FROM tbl_detail_transaksi LIKE 'id_produk';

-- Cek jumlah data dasar.
SELECT COUNT(*) AS jumlah_pelanggan FROM tbl_pelanggan;
SELECT COUNT(*) AS jumlah_produk FROM tbl_produk;
SELECT COUNT(*) AS jumlah_kategori FROM tbl_kategori;
SELECT COUNT(*) AS jumlah_transaksi FROM tbl_transaksi;
SELECT COUNT(*) AS jumlah_log FROM tbl_log;
