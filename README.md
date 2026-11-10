# 🛒 SISTEM INFORMASI PENJUALAN TOKO

**Aplikasi Manajemen Penjualan dan Pengelolaan Data Toko Berbasis Web**

---

## 👩‍💻 Tentang Aplikasi

Sistem Informasi Penjualan Toko merupakan aplikasi berbasis web yang dirancang untuk membantu proses pengelolaan data dan aktivitas penjualan toko secara lebih praktis, terstruktur, dan efisien.

Aplikasi ini menyediakan berbagai fitur untuk mengelola data pelanggan, kategori produk, stok barang, transaksi penjualan, laporan, serta riwayat aktivitas database.

Aplikasi dikembangkan menggunakan teknologi web dasar dengan PHP sebagai backend dan MariaDB sebagai sistem manajemen database.

## ✨ Fitur dan Fungsionalitas

### 1. Dashboard
- Menampilkan ringkasan informasi toko.
- Menampilkan jumlah pelanggan dan produk.
- Menampilkan jumlah transaksi.
- Menampilkan total penjualan.
- Menampilkan daftar transaksi terbaru.

### 2. Manajemen Pelanggan
- Menampilkan seluruh data pelanggan.
- Menambahkan pelanggan baru.
- Mengedit informasi pelanggan.
- Menghapus data pelanggan.
- Mencari pelanggan berdasarkan nama.

### 3. Manajemen Kategori
- Menampilkan daftar kategori produk.
- Menambahkan kategori baru.
- Mengedit nama kategori.
- Menghapus kategori.

### 4. Manajemen Produk
- Menampilkan daftar produk.
- Menambahkan produk baru.
- Mengelola kategori produk.
- Mengatur harga produk.
- Memperbarui stok barang.
- Mengedit dan menghapus produk.

### 5. Transaksi Penjualan
- Memilih pelanggan.
- Memilih produk yang akan dibeli.
- Menentukan jumlah barang.
- Menghitung subtotal dan diskon.
- Menghitung total pembayaran.
- Menyimpan transaksi ke database.
- Memperbarui stok produk secara otomatis.

*Catatan: Versi awal mendukung satu jenis produk dalam setiap transaksi.*

### 6. Riwayat dan Laporan Penjualan
- Menampilkan riwayat transaksi.
- Melihat informasi transaksi.
- Menampilkan detail pembelian.
- Menyaring laporan berdasarkan tanggal.

### 7. Log Aktivitas
- Mencatat aktivitas penambahan transaksi.
- Mencatat perubahan data transaksi.
- Menampilkan riwayat aktivitas database.
- Memanfaatkan Database Trigger untuk pencatatan otomatis.

## 🛠️ Teknologi yang Digunakan

| Teknologi | Kegunaan |
|---|---|
| HTML5 | Struktur halaman aplikasi |
| CSS3 | Desain dan tata letak antarmuka |
| JavaScript | Interaksi dan fungsi dinamis |
| PHP | Backend dan pengolahan data |
| MariaDB | Sistem manajemen database |
| MySQLi | Koneksi PHP dengan database |
| Google Fonts – Lexend | Tipografi aplikasi |
| XAMPP | Lingkungan server lokal |

**Framework:** Tidak menggunakan framework. Aplikasi dikembangkan menggunakan PHP Native, HTML, CSS, dan JavaScript.

## 🗄️ Database

Aplikasi menggunakan database bernama `db_tokoku`.

Tabel utama yang digunakan:
- `tbl_pelanggan`
- `tbl_produk`
- `tbl_kategori`
- `tbl_transaksi`
- `tbl_detail_transaksi`
- `tbl_log`

Aplikasi juga memanfaatkan beberapa konsep SQL lanjutan, yaitu:

- Database View
- JOIN dan Subquery
- Stored Procedure
- Stored Function
- Database Trigger
- Transaction Control Language (COMMIT dan ROLLBACK)
- Indexing

## 🎨 Konsep Desain UI/UX

Aplikasi menggunakan konsep **Modern Minimalist Dashboard** dengan pendekatan desain yang sederhana, bersih, dan mudah digunakan.

Karakteristik desain:
- **Warna utama:** Hijau dan putih.
- **Navigasi:** Sidebar di sebelah kiri.
- **Tipografi:** Lexend dari Google Fonts.
- **Layout:** Dashboard berbasis kartu informasi dan tabel data.
- **Tampilan:** Minimalis, rapi, dan responsif.
- **Pengalaman pengguna:** Navigasi sederhana dan konsisten pada setiap halaman.

Desain ini bertujuan memberikan pengalaman penggunaan yang nyaman sekaligus menjaga informasi tetap mudah dibaca dan dipahami.

## 🎯 Tujuan Pengembangan

Aplikasi ini dikembangkan sebagai proyek pembelajaran dan penerapan kompetensi Rekayasa Perangkat Lunak (RPL), khususnya dalam pengembangan aplikasi berbasis web dan pengelolaan database.

Melalui aplikasi ini, konsep database yang telah dipelajari dapat diterapkan secara langsung ke dalam sistem yang memiliki fungsi nyata.

## 💻 Persyaratan Sistem

- Windows atau sistem operasi yang mendukung XAMPP.
- XAMPP (Apache dan MariaDB/MySQL).
- PHP yang kompatibel dengan versi XAMPP.
- Browser modern seperti Google Chrome.
- Database `db_tokoku`.

## 🚀 Instalasi

1. Download dan ekstrak folder aplikasi.
2. Pindahkan folder `penjualan_toko` ke direktori `C:\xampp\htdocs`.
3. Jalankan Apache dan MySQL melalui XAMPP Control Panel.
4. Pastikan database `db_tokoku` sudah tersedia.
5. Sesuaikan konfigurasi database pada `config/database.php`.
6. Buka browser dan akses:

   `http://localhost/penjualan_toko/`

## 👩‍🎓 Informasi Pengembang

**Dikembangkan oleh:**

### Chintya Airin

Pelajar Rekayasa Perangkat Lunak (RPL)

Proyek: Sistem Informasi Penjualan Toko

## 📌 Penutup

Sistem Informasi Penjualan Toko diharapkan dapat menjadi contoh penerapan teknologi web dan database dalam membantu pengelolaan aktivitas penjualan.

Aplikasi ini masih dapat dikembangkan lebih lanjut dengan penambahan fitur, peningkatan keamanan, dan penyempurnaan antarmuka pengguna.

---

**© 2026 Chintya Airin | Sistem Informasi Penjualan Toko**