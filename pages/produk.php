<?php
$editId = (int)($_GET['edit'] ?? 0);
$editProduct = null;
if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM tbl_produk WHERE id_produk = ?');
    $stmt->execute([$editId]);
    $editProduct = $stmt->fetch() ?: null;
}
$categories = $pdo->query('SELECT id_kategori, nama_kategori FROM tbl_kategori ORDER BY nama_kategori')->fetchAll();
$search = trim((string)($_GET['q'] ?? ''));
$sql = 'SELECT p.*, k.nama_kategori FROM tbl_produk p JOIN tbl_kategori k ON k.id_kategori = p.id_kategori';
if ($search !== '') {
    $stmt = $pdo->prepare($sql . ' WHERE p.nama_produk LIKE ? OR k.nama_kategori LIKE ? ORDER BY p.id_produk DESC');
    $term = '%' . $search . '%';
    $stmt->execute([$term, $term]);
    $products = $stmt->fetchAll();
} else {
    $products = $pdo->query($sql . ' ORDER BY p.id_produk DESC')->fetchAll();
}
?>
<section class="page-heading"><div><span class="eyebrow">INVENTARIS TOKO</span><h1>Data Produk</h1><p>Kelola daftar produk, harga, kategori, dan stok barang.</p></div></section>
<section class="content-grid content-grid-form">
    <div class="panel form-panel"><div class="panel-heading"><div><h2><?= $editProduct ? 'Edit Produk' : 'Tambah Produk' ?></h2><p>Masukkan informasi produk toko.</p></div><span class="panel-icon">▣</span></div>
        <?php if (!$categories): ?><div class="notice-box">Buat kategori terlebih dahulu sebelum menambahkan produk.</div><?php else: ?>
        <form method="post" class="stack-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="product_save"><input type="hidden" name="id_produk" value="<?= e($editProduct['id_produk'] ?? 0) ?>">
            <label>Nama produk<input type="text" name="nama_produk" maxlength="100" value="<?= e($editProduct['nama_produk'] ?? '') ?>" placeholder="Contoh: Beras 5 Kg" required></label>
            <label>Kategori<select name="id_kategori" required><option value="">Pilih kategori</option><?php foreach ($categories as $category): ?><option value="<?= e($category['id_kategori']) ?>" <?= (int)($editProduct['id_kategori'] ?? 0) === (int)$category['id_kategori'] ? 'selected' : '' ?>><?= e($category['nama_kategori']) ?></option><?php endforeach; ?></select></label>
            <div class="form-row"><label>Harga (Rp)<input type="number" name="harga" min="0" step="0.01" value="<?= e($editProduct['harga'] ?? '') ?>" placeholder="0" required></label><label>Stok<input type="number" name="stok" min="0" step="1" value="<?= e($editProduct['stok'] ?? '0') ?>" required></label></div>
            <div class="form-actions"><button class="button button-primary" type="submit"><?= $editProduct ? 'Simpan Perubahan' : 'Simpan Produk' ?></button><?php if ($editProduct): ?><a class="button button-light" href="index.php?page=produk">Batal</a><?php endif; ?></div>
        </form><?php endif; ?>
    </div>
    <div class="panel panel-table"><div class="panel-heading"><div><h2>Daftar Produk</h2><p><?= count($products) ?> produk ditampilkan.</p></div></div>
        <form class="search-form" method="get"><input type="hidden" name="page" value="produk"><input type="search" name="q" value="<?= e($search) ?>" placeholder="Cari produk atau kategori..."><button class="button button-light" type="submit">Cari</button><?php if ($search !== ''): ?><a class="clear-search" href="index.php?page=produk">Reset</a><?php endif; ?></form>
        <div class="table-wrap"><table><thead><tr><th>Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th><th class="text-right">Aksi</th></tr></thead><tbody>
        <?php foreach ($products as $product): ?><tr><td><div class="cell-title"><?= e($product['nama_produk']) ?></div><small class="muted">ID #<?= e($product['id_produk']) ?></small></td><td><?= e($product['nama_kategori']) ?></td><td class="amount"><?= e(rupiah($product['harga'])) ?></td><td><span class="stock-pill <?= (int)$product['stok'] <= 5 ? 'stock-low' : '' ?>"><?= number_format((int)$product['stok']) ?></span></td><td class="text-right"><div class="action-group"><a class="action-link" href="index.php?page=produk&edit=<?= e($product['id_produk']) ?>">Edit</a><form method="post" class="inline-form" data-confirm="Hapus produk ini? Produk yang sudah dipakai pada transaksi mungkin tidak dapat dihapus."><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="product_delete"><input type="hidden" name="id_produk" value="<?= e($product['id_produk']) ?>"><button class="action-link danger-link" type="submit">Hapus</button></form></div></td></tr><?php endforeach; ?>
        <?php if (!$products): ?><tr><td colspan="5"><div class="table-empty">Produk tidak ditemukan.</div></td></tr><?php endif; ?>
        </tbody></table></div>
    </div>
</section>
