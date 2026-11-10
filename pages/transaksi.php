<?php
$customers = $pdo->query('SELECT id_pelanggan, nama_pelanggan, kota FROM tbl_pelanggan ORDER BY nama_pelanggan')->fetchAll();
$products = $pdo->query('SELECT id_produk, nama_produk, harga, stok FROM tbl_produk ORDER BY nama_produk')->fetchAll();
$recent = $pdo->query(
    'SELECT t.id_transaksi, t.tanggal, p.nama_pelanggan, t.total_belanja
     FROM tbl_transaksi t
     LEFT JOIN tbl_pelanggan p ON p.id_pelanggan = t.id_pelanggan
     ORDER BY t.id_transaksi DESC LIMIT 8'
)->fetchAll();
?>
<section class="page-heading"><div><span class="eyebrow">PENJUALAN</span><h1>Transaksi Penjualan</h1><p>Buat transaksi baru menggunakan Stored Procedure Bab 6.</p></div></section>
<section class="transaction-layout">
    <div class="panel transaction-panel">
        <div class="panel-heading"><div><h2>Form Transaksi Baru</h2><p>Pilih pelanggan, produk, jumlah, dan diskon.</p></div><span class="panel-icon">＋</span></div>
        <?php if (!$customers || !$products): ?>
            <div class="notice-box">Data pelanggan dan produk harus tersedia sebelum transaksi dibuat. Tambahkan melalui menu Pelanggan dan Produk.</div>
        <?php else: ?>
        <form method="post" class="stack-form" id="transactionForm">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="transaction_create">
            <div class="form-row"><label>Tanggal transaksi<input type="date" name="tanggal" value="<?= e(date('Y-m-d')) ?>" required></label><label>Pelanggan<select name="id_pelanggan" required><option value="">Pilih pelanggan</option><?php foreach ($customers as $customer): ?><option value="<?= e($customer['id_pelanggan']) ?>"><?= e($customer['nama_pelanggan']) ?> — <?= e($customer['kota']) ?></option><?php endforeach; ?></select></label></div>
            <label>Produk<select name="id_produk" id="productSelect" required><option value="">Pilih produk</option><?php foreach ($products as $product): ?><option value="<?= e($product['id_produk']) ?>" data-price="<?= e($product['harga']) ?>" data-stock="<?= e($product['stok']) ?>"><?= e($product['nama_produk']) ?> — <?= e(rupiah($product['harga'])) ?> (Stok: <?= e($product['stok']) ?>)</option><?php endforeach; ?></select></label>
            <div class="form-row"><label>Jumlah<input type="number" name="jumlah" id="quantityInput" min="1" step="1" value="1" required></label><label>Diskon (%)<input type="number" name="diskon" id="discountInput" min="0" max="100" step="0.01" value="0" required></label></div>
            <div class="transaction-summary">
                <div><span>Harga satuan</span><strong id="unitPrice">Rp 0</strong></div>
                <div><span>Subtotal</span><strong id="subtotal">Rp 0</strong></div>
                <div><span>Diskon</span><strong id="discountValue">Rp 0</strong></div>
                <div class="summary-total"><span>Perkiraan total</span><strong id="grandTotal">Rp 0</strong></div>
                <small id="stockHint">Pilih produk untuk melihat stok.</small>
            </div>
            <button class="button button-primary button-wide" type="submit">Simpan Transaksi <span>→</span></button>
            <p class="form-note">Sistem akan menjalankan <code>sp_tambah_transaksi_bab6</code>. Perhitungan akhir, validasi stok, penyimpanan detail, dan pengurangan stok ditangani oleh database.</p>
        </form>
        <?php endif; ?>
    </div>
    <aside class="panel transaction-info"><div class="panel-heading"><div><h2>Alur Pemrosesan</h2><p>Yang dikerjakan database saat transaksi disimpan.</p></div></div>
        <ol class="process-list"><li><span>1</span><div><strong>Validasi data</strong><small>Memeriksa pelanggan, produk, jumlah, dan diskon.</small></div></li><li><span>2</span><div><strong>Hitung total</strong><small>Menggunakan Stored Function untuk total dan diskon.</small></div></li><li><span>3</span><div><strong>Simpan transaksi</strong><small>Stored Procedure menulis data transaksi dan detail.</small></div></li><li><span>4</span><div><strong>Perbarui stok</strong><small>Stok produk dikurangi sesuai jumlah pembelian.</small></div></li><li><span>5</span><div><strong>Catat aktivitas</strong><small>Trigger audit menambahkan log transaksi.</small></div></li></ol>
    </aside>
</section>
<section class="panel panel-table section-gap"><div class="panel-heading"><div><h2>Transaksi Terbaru</h2><p>Transaksi terakhir yang sudah tersimpan.</p></div><a class="text-link" href="index.php?page=laporan">Lihat semua →</a></div><div class="table-wrap"><table><thead><tr><th>ID Transaksi</th><th>Tanggal</th><th>Pelanggan</th><th class="text-right">Total</th></tr></thead><tbody><?php foreach ($recent as $row): ?><tr><td class="mono">#<?= e($row['id_transaksi']) ?></td><td><?= e(date('d/m/Y', strtotime($row['tanggal']))) ?></td><td class="cell-strong"><?= e($row['nama_pelanggan'] ?? 'Pelanggan dihapus') ?></td><td class="text-right amount"><?= e(rupiah($row['total_belanja'])) ?></td></tr><?php endforeach; ?><?php if (!$recent): ?><tr><td colspan="4"><div class="table-empty">Belum ada transaksi.</div></td></tr><?php endif; ?></tbody></table></div></section>
