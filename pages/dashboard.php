<?php
$stats = [
    'customers' => (int)$pdo->query('SELECT COUNT(*) FROM tbl_pelanggan')->fetchColumn(),
    'products'  => (int)$pdo->query('SELECT COUNT(*) FROM tbl_produk')->fetchColumn(),
    'sales'     => (int)$pdo->query('SELECT COUNT(*) FROM tbl_transaksi')->fetchColumn(),
    'revenue'   => (float)$pdo->query('SELECT COALESCE(SUM(total_belanja), 0) FROM tbl_transaksi')->fetchColumn(),
];
$recent = $pdo->query(
    'SELECT t.id_transaksi, t.tanggal, p.nama_pelanggan, t.total_belanja
     FROM tbl_transaksi t
     LEFT JOIN tbl_pelanggan p ON p.id_pelanggan = t.id_pelanggan
     ORDER BY t.id_transaksi DESC LIMIT 6'
)->fetchAll();
$lowStock = $pdo->query('SELECT nama_produk, stok FROM tbl_produk ORDER BY stok ASC, nama_produk ASC LIMIT 5')->fetchAll();
?>
<section class="page-heading">
    <div><span class="eyebrow">RINGKASAN TOKO</span><h1>Dashboard</h1><p>Selamat datang. Berikut ringkasan data penjualan tokomu.</p></div>
    <a class="button button-primary" href="index.php?page=transaksi">＋ Buat Transaksi</a>
</section>

<section class="stats-grid">
    <article class="stat-card"><div class="stat-top"><span class="stat-icon green">♙</span><span class="stat-caption">DATA MASTER</span></div><div class="stat-value"><?= number_format($stats['customers']) ?></div><div class="stat-label">Total Pelanggan</div><div class="stat-foot">Pelanggan terdaftar</div></article>
    <article class="stat-card"><div class="stat-top"><span class="stat-icon mint">▣</span><span class="stat-caption">INVENTARIS</span></div><div class="stat-value"><?= number_format($stats['products']) ?></div><div class="stat-label">Total Produk</div><div class="stat-foot">Jenis produk tersedia</div></article>
    <article class="stat-card"><div class="stat-top"><span class="stat-icon amber">▤</span><span class="stat-caption">PENJUALAN</span></div><div class="stat-value"><?= number_format($stats['sales']) ?></div><div class="stat-label">Total Transaksi</div><div class="stat-foot">Seluruh transaksi tersimpan</div></article>
    <article class="stat-card"><div class="stat-top"><span class="stat-icon teal">Rp</span><span class="stat-caption">PENDAPATAN</span></div><div class="stat-value stat-currency"><?= e(rupiah($stats['revenue'])) ?></div><div class="stat-label">Total Penjualan</div><div class="stat-foot">Akumulasi nilai transaksi</div></article>
</section>

<section class="dashboard-grid">
    <div class="panel">
        <div class="panel-heading"><div><h2>Transaksi Terbaru</h2><p>Enam transaksi terakhir yang tercatat.</p></div><a class="text-link" href="index.php?page=laporan">Lihat laporan →</a></div>
        <?php if (!$recent): ?>
            <div class="empty-state"><span>▤</span><strong>Belum ada transaksi</strong><p>Transaksi yang dibuat akan muncul di sini.</p></div>
        <?php else: ?>
            <div class="table-wrap"><table><thead><tr><th>ID</th><th>Tanggal</th><th>Pelanggan</th><th class="text-right">Total</th></tr></thead><tbody>
            <?php foreach ($recent as $row): ?>
                <tr><td class="mono">#<?= e($row['id_transaksi']) ?></td><td><?= e(date('d/m/Y', strtotime($row['tanggal']))) ?></td><td class="cell-strong"><?= e($row['nama_pelanggan'] ?? 'Pelanggan dihapus') ?></td><td class="text-right amount"><?= e(rupiah($row['total_belanja'])) ?></td></tr>
            <?php endforeach; ?>
            </tbody></table></div>
        <?php endif; ?>
    </div>

    <div class="panel">
        <div class="panel-heading"><div><h2>Informasi Stok</h2><p>Produk dengan stok paling sedikit.</p></div><a class="text-link" href="index.php?page=produk">Kelola →</a></div>
        <?php if (!$lowStock): ?>
            <div class="empty-state"><span>▣</span><strong>Belum ada produk</strong><p>Tambahkan produk untuk melihat stok.</p></div>
        <?php else: ?>
            <div class="stock-list">
                <?php foreach ($lowStock as $item): ?>
                    <div class="stock-item"><div class="stock-product"><span class="product-dot"></span><strong><?= e($item['nama_produk']) ?></strong></div><span class="stock-count <?= (int)$item['stok'] <= 5 ? 'stock-low' : '' ?>"><?= number_format((int)$item['stok']) ?> unit</span></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="tip-box"><strong>Tips</strong><p>Perbarui jumlah stok secara berkala agar data persediaan tetap akurat.</p></div>
    </div>
</section>
