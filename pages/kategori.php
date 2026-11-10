<?php
$editId = (int)($_GET['edit'] ?? 0);
$editCategory = null;
if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM tbl_kategori WHERE id_kategori = ?');
    $stmt->execute([$editId]);
    $editCategory = $stmt->fetch() ?: null;
}
$categories = $pdo->query('SELECT k.id_kategori, k.nama_kategori, COUNT(p.id_produk) AS jumlah_produk FROM tbl_kategori k LEFT JOIN tbl_produk p ON p.id_kategori = k.id_kategori GROUP BY k.id_kategori, k.nama_kategori ORDER BY k.id_kategori DESC')->fetchAll();
?>
<section class="page-heading"><div><span class="eyebrow">DATA MASTER</span><h1>Data Kategori</h1><p>Atur kelompok kategori untuk setiap produk toko.</p></div></section>
<section class="content-grid content-grid-form">
    <div class="panel form-panel"><div class="panel-heading"><div><h2><?= $editCategory ? 'Edit Kategori' : 'Tambah Kategori' ?></h2><p>Nama kategori digunakan untuk mengelompokkan produk.</p></div><span class="panel-icon">▧</span></div>
        <form method="post" class="stack-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="category_save"><input type="hidden" name="id_kategori" value="<?= e($editCategory['id_kategori'] ?? 0) ?>"><label>Nama kategori<input type="text" name="nama_kategori" maxlength="100" placeholder="Contoh: Sembako" value="<?= e($editCategory['nama_kategori'] ?? '') ?>" required></label><div class="form-actions"><button class="button button-primary" type="submit"><?= $editCategory ? 'Simpan Perubahan' : 'Simpan Kategori' ?></button><?php if ($editCategory): ?><a class="button button-light" href="index.php?page=kategori">Batal</a><?php endif; ?></div></form>
    </div>
    <div class="panel panel-table"><div class="panel-heading"><div><h2>Daftar Kategori</h2><p><?= count($categories) ?> kategori terdaftar.</p></div></div><div class="table-wrap"><table><thead><tr><th>ID</th><th>Nama Kategori</th><th>Jumlah Produk</th><th class="text-right">Aksi</th></tr></thead><tbody>
    <?php foreach ($categories as $category): ?><tr><td class="mono">#<?= e($category['id_kategori']) ?></td><td class="cell-strong"><?= e($category['nama_kategori']) ?></td><td><span class="soft-badge"><?= number_format((int)$category['jumlah_produk']) ?> produk</span></td><td class="text-right"><div class="action-group"><a class="action-link" href="index.php?page=kategori&edit=<?= e($category['id_kategori']) ?>">Edit</a><form method="post" class="inline-form" data-confirm="Hapus kategori ini? Kategori yang masih dipakai produk tidak dapat dihapus."><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="category_delete"><input type="hidden" name="id_kategori" value="<?= e($category['id_kategori']) ?>"><button class="action-link danger-link" type="submit">Hapus</button></form></div></td></tr><?php endforeach; ?>
    <?php if (!$categories): ?><tr><td colspan="4"><div class="table-empty">Belum ada kategori.</div></td></tr><?php endif; ?>
    </tbody></table></div></div>
</section>
