<?php

declare(strict_types=1);

require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/config/database.php';

$allowedPages = ['dashboard', 'pelanggan', 'kategori', 'produk', 'transaksi', 'laporan', 'log'];
$page = $_GET['page'] ?? 'dashboard';
if (!in_array($page, $allowedPages, true)) {
    $page = 'dashboard';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        verify_csrf();
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'customer_save':
                $id = (int)($_POST['id_pelanggan'] ?? 0);
                $name = trim((string)($_POST['nama_pelanggan'] ?? ''));
                $city = trim((string)($_POST['kota'] ?? ''));
                if ($name === '' || $city === '') {
                    throw new RuntimeException('Nama pelanggan dan kota wajib diisi.');
                }
                if ($id > 0) {
                    $stmt = $pdo->prepare('UPDATE tbl_pelanggan SET nama_pelanggan = ?, kota = ? WHERE id_pelanggan = ?');
                    $stmt->execute([$name, $city, $id]);
                    set_flash('success', 'Data pelanggan berhasil diperbarui.');
                } else {
                    $stmt = $pdo->prepare('INSERT INTO tbl_pelanggan (nama_pelanggan, kota) VALUES (?, ?)');
                    $stmt->execute([$name, $city]);
                    set_flash('success', 'Pelanggan baru berhasil ditambahkan.');
                }
                redirect('index.php?page=pelanggan');

            case 'customer_delete':
                $id = (int)($_POST['id_pelanggan'] ?? 0);
                if ($id < 1) {
                    throw new RuntimeException('ID pelanggan tidak valid.');
                }
                $stmt = $pdo->prepare('DELETE FROM tbl_pelanggan WHERE id_pelanggan = ?');
                $stmt->execute([$id]);
                set_flash('success', 'Data pelanggan berhasil dihapus.');
                redirect('index.php?page=pelanggan');

            case 'category_save':
                $id = (int)($_POST['id_kategori'] ?? 0);
                $name = trim((string)($_POST['nama_kategori'] ?? ''));
                if ($name === '') {
                    throw new RuntimeException('Nama kategori wajib diisi.');
                }
                if ($id > 0) {
                    $stmt = $pdo->prepare('UPDATE tbl_kategori SET nama_kategori = ? WHERE id_kategori = ?');
                    $stmt->execute([$name, $id]);
                    set_flash('success', 'Kategori berhasil diperbarui.');
                } else {
                    $stmt = $pdo->prepare('INSERT INTO tbl_kategori (nama_kategori) VALUES (?)');
                    $stmt->execute([$name]);
                    set_flash('success', 'Kategori berhasil ditambahkan.');
                }
                redirect('index.php?page=kategori');

            case 'category_delete':
                $id = (int)($_POST['id_kategori'] ?? 0);
                if ($id < 1) {
                    throw new RuntimeException('ID kategori tidak valid.');
                }
                $stmt = $pdo->prepare('DELETE FROM tbl_kategori WHERE id_kategori = ?');
                $stmt->execute([$id]);
                set_flash('success', 'Kategori berhasil dihapus.');
                redirect('index.php?page=kategori');

            case 'product_save':
                $id = (int)($_POST['id_produk'] ?? 0);
                $categoryId = (int)($_POST['id_kategori'] ?? 0);
                $name = trim((string)($_POST['nama_produk'] ?? ''));
                $price = filter_var($_POST['harga'] ?? null, FILTER_VALIDATE_FLOAT);
                $stock = filter_var($_POST['stok'] ?? null, FILTER_VALIDATE_INT);
                if ($categoryId < 1 || $name === '' || $price === false || $price < 0 || $stock === false || $stock < 0) {
                    throw new RuntimeException('Lengkapi data produk dengan benar. Harga dan stok tidak boleh negatif.');
                }
                if ($id > 0) {
                    $stmt = $pdo->prepare('UPDATE tbl_produk SET id_kategori = ?, nama_produk = ?, harga = ?, stok = ? WHERE id_produk = ?');
                    $stmt->execute([$categoryId, $name, $price, $stock, $id]);
                    set_flash('success', 'Data produk berhasil diperbarui.');
                } else {
                    $stmt = $pdo->prepare('INSERT INTO tbl_produk (id_kategori, nama_produk, harga, stok) VALUES (?, ?, ?, ?)');
                    $stmt->execute([$categoryId, $name, $price, $stock]);
                    set_flash('success', 'Produk berhasil ditambahkan.');
                }
                redirect('index.php?page=produk');

            case 'product_delete':
                $id = (int)($_POST['id_produk'] ?? 0);
                if ($id < 1) {
                    throw new RuntimeException('ID produk tidak valid.');
                }
                $stmt = $pdo->prepare('DELETE FROM tbl_produk WHERE id_produk = ?');
                $stmt->execute([$id]);
                set_flash('success', 'Produk berhasil dihapus.');
                redirect('index.php?page=produk');

            case 'transaction_create':
                $customerId = (int)($_POST['id_pelanggan'] ?? 0);
                $productId = (int)($_POST['id_produk'] ?? 0);
                $quantity = filter_var($_POST['jumlah'] ?? null, FILTER_VALIDATE_INT);
                $discount = filter_var($_POST['diskon'] ?? '0', FILTER_VALIDATE_FLOAT);
                $date = trim((string)($_POST['tanggal'] ?? date('Y-m-d')));
                $dateObject = DateTime::createFromFormat('Y-m-d', $date);
                if ($customerId < 1 || $productId < 1 || $quantity === false || $quantity < 1 || $discount === false || $discount < 0 || $discount > 100 || !$dateObject || $dateObject->format('Y-m-d') !== $date) {
                    throw new RuntimeException('Periksa kembali pelanggan, produk, tanggal, jumlah, dan diskon.');
                }

                // Procedure Bab 6 menangani validasi stok, perhitungan, penyimpanan, dan COMMIT.
                $stmt = $pdo->prepare('CALL sp_tambah_transaksi_bab6(?, ?, ?, ?, ?, @id_transaksi_baru, @total_belanja_baru)');
                $stmt->execute([$customerId, $date, $productId, $quantity, $discount]);
                while ($stmt->nextRowset()) {
                    // Kosongkan seluruh result set dari CALL sebelum query berikutnya.
                }
                $stmt->closeCursor();

                $result = $pdo->query('SELECT @id_transaksi_baru AS id_transaksi, @total_belanja_baru AS total_belanja')->fetch();
                if (!$result || empty($result['id_transaksi'])) {
                    throw new RuntimeException('Procedure selesai, tetapi ID transaksi tidak berhasil dibaca. Periksa definisi Procedure.');
                }
                set_flash('success', 'Transaksi #' . $result['id_transaksi'] . ' berhasil disimpan. Total: ' . rupiah($result['total_belanja']) . '.');
                redirect('index.php?page=transaksi');

            default:
                throw new RuntimeException('Aksi tidak dikenali.');
        }
    } catch (Throwable $exception) {
        set_flash('error', $exception->getMessage());
        $fallbackPage = $_GET['page'] ?? 'dashboard';
        if (!in_array($fallbackPage, $allowedPages, true)) {
            $fallbackPage = 'dashboard';
        }
        redirect('index.php?page=' . rawurlencode($fallbackPage));
    }
}

$flash = get_flash();
require __DIR__ . '/includes/header.php';

$pageFile = __DIR__ . '/pages/' . $page . '.php';
require $pageFile;

require __DIR__ . '/includes/footer.php';
