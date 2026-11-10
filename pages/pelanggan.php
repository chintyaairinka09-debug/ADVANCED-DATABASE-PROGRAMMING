<?php
$editId = (int)($_GET['edit'] ?? 0);
$editCustomer = null;
if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM tbl_pelanggan WHERE id_pelanggan = ?');
    $stmt->execute([$editId]);
    $editCustomer = $stmt->fetch() ?: null;
}
$search = trim((string)($_GET['q'] ?? ''));
if ($search !== '') {
    $stmt = $pdo->prepare('SELECT * FROM tbl_pelanggan WHERE nama_pelanggan LIKE ? OR kota LIKE ? ORDER BY id_pelanggan DESC');
    $term = '%' . $search . '%';
    $stmt->execute([$term, $term]);
    $customers = $stmt->fetchAll();
} else {
    $customers = $pdo->query('SELECT * FROM tbl_pelanggan ORDER BY id_pelanggan DESC')->fetchAll();
}
?>
<section class="page-heading"><div><span class="eyebrow">DATA MASTER</span><h1>Data Pelanggan</h1><p>Kelola informasi pelanggan yang terdaftar di toko.</p></div></section>
<section class="content-grid content-grid-form">
    <div class="panel form-panel">
        <div class="panel-heading"><div><h2><?= $editCustomer ? 'Edit Pelanggan' : 'Tambah Pelanggan' ?></h2><p><?= $editCustomer ? 'Perbarui informasi pelanggan.' : 'Masukkan data pelanggan baru.' ?></p></div><span class="panel-icon">♙</span></div>
        <form method="post" class="stack-form">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="customer_save">
            <input type="hidden" name="id_pelanggan" value="<?= e($editCustomer['id_pelanggan'] ?? 0) ?>">
            <label>Nama pelanggan<input type="text" name="nama_pelanggan" value="<?= e($editCustomer['nama_pelanggan'] ?? '') ?>" placeholder="Contoh: Andi" maxlength="100" required></label>
            <label>Kota<input type="text" name="kota" value="<?= e($editCustomer['kota'] ?? '') ?>" placeholder="Contoh: Ponorogo" maxlength="50" required></label>
            <div class="form-actions"><button class="button button-primary" type="submit"><?= $editCustomer ? 'Simpan Perubahan' : 'Simpan Pelanggan' ?></button><?php if ($editCustomer): ?><a class="button button-light" href="index.php?page=pelanggan">Batal</a><?php endif; ?></div>
        </form>
    </div>
    <div class="panel panel-table">
        <div class="panel-heading"><div><h2>Daftar Pelanggan</h2><p><?= count($customers) ?> data ditampilkan.</p></div></div>
        <form class="search-form" method="get"><input type="hidden" name="page" value="pelanggan"><input type="search" name="q" value="<?= e($search) ?>" placeholder="Cari nama atau kota..."><button class="button button-light" type="submit">Cari</button><?php if ($search !== ''): ?><a class="clear-search" href="index.php?page=pelanggan">Reset</a><?php endif; ?></form>
        <div class="table-wrap"><table><thead><tr><th>ID</th><th>Nama Pelanggan</th><th>Kota</th><th class="text-right">Aksi</th></tr></thead><tbody>
        <?php foreach ($customers as $customer): ?>
            <tr><td class="mono">#<?= e($customer['id_pelanggan']) ?></td><td class="cell-strong"><?= e($customer['nama_pelanggan']) ?></td><td><?= e($customer['kota']) ?></td><td class="text-right"><div class="action-group"><a class="action-link" href="index.php?page=pelanggan&edit=<?= e($customer['id_pelanggan']) ?>">Edit</a><form method="post" class="inline-form" data-confirm="Hapus pelanggan ini? Jika pelanggan masih memiliki transaksi, database dapat menolak penghapusan."><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="customer_delete"><input type="hidden" name="id_pelanggan" value="<?= e($customer['id_pelanggan']) ?>"><button class="action-link danger-link" type="submit">Hapus</button></form></div></td></tr>
        <?php endforeach; ?>
        <?php if (!$customers): ?><tr><td colspan="4"><div class="table-empty">Data pelanggan tidak ditemukan.</div></td></tr><?php endif; ?>
        </tbody></table></div>
    </div>
</section>
