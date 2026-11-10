# Rancangan Aplikasi Sistem Informasi Penjualan Toko

## Identitas

- Nama aplikasi: Sistem Informasi Penjualan Toko
- Database: `db_tokoku`
- Teknologi: HTML, CSS, JavaScript, PHP Native, MariaDB
- Tema: hijau dan putih
- Navigasi: sidebar kiri
- Pengguna versi awal: Admin/Petugas toko (lokal, tanpa login)

## Rancangan halaman

| Halaman | Isi utama | Operasi |
|---|---|---|
| Dashboard | Kartu jumlah pelanggan, produk, transaksi, total penjualan; transaksi terbaru; stok terendah | Baca data |
| Pelanggan | Form nama dan kota; tabel pelanggan; pencarian | Tambah, baca, ubah, hapus |
| Kategori | Form nama kategori; tabel jumlah produk per kategori | Tambah, baca, ubah, hapus |
| Produk | Form nama produk, kategori, harga, stok; tabel produk | Tambah, baca, ubah, hapus |
| Transaksi | Tanggal, pelanggan, produk, jumlah, diskon, preview subtotal dan total | Panggil Stored Procedure |
| Laporan Penjualan | Filter rentang tanggal dan ringkasan transaksi | Baca View laporan |
| Log Aktivitas | Tabel nama tabel, ID data, jenis aktivitas, keterangan, waktu | Baca `tbl_log` |

## Alur transaksi

1. Petugas memilih tanggal dan pelanggan.
2. Petugas memilih satu produk, jumlah, dan diskon.
3. JavaScript menampilkan perkiraan perhitungan di halaman.
4. PHP mengirim data ke `sp_tambah_transaksi_bab6` dengan prepared statement.
5. Stored Procedure memeriksa pelanggan, produk, jumlah, diskon, dan stok.
6. Stored Function menghitung subtotal dan harga setelah diskon.
7. Procedure menyimpan `tbl_transaksi` dan `tbl_detail_transaksi`, mengurangi stok, lalu COMMIT.
8. Trigger transaksi mencatat aktivitas ke `tbl_log`.
9. PHP menampilkan notifikasi hasil transaksi.

## Hubungan dengan kompetensi SQL Bab 1–6

| Kompetensi | Penerapan di aplikasi |
|---|---|
| Database View | Halaman Laporan membaca `view_laporan_penjualan_bab6` |
| JOIN | Dashboard, daftar produk berkategori, laporan dan transaksi menggabungkan tabel |
| Subquery | Dapat digunakan untuk analisis transaksi pada pengembangan laporan lanjutan |
| Stored Procedure | Form Transaksi memanggil `sp_tambah_transaksi_bab6` |
| Stored Function | Procedure memanggil `fn_total_harga_bab6` dan `fn_harga_setelah_diskon_bab6` |
| Trigger | Halaman Log membaca catatan Trigger audit transaksi di `tbl_log` |
| TCL | Procedure menjalankan START TRANSACTION, COMMIT, dan ROLLBACK saat terjadi exception |
| Index | Index di tabel transaksi/detail/log membantu query yang sesuai dengan kolom index |

## Batasan versi pertama

- Satu transaksi berisi satu jenis produk, karena Stored Procedure Bab 6 menerima satu `id_produk` dan satu `jumlah`.
- Tidak ada autentikasi dan pembagian hak akses pada versi awal.
- Aplikasi ditujukan untuk dijalankan secara lokal melalui XAMPP.
- Penghapusan data yang sudah direferensikan transaksi mungkin ditolak oleh foreign key.
- Preview perhitungan pada browser hanya untuk membantu pengguna; hasil final dihitung di database.
