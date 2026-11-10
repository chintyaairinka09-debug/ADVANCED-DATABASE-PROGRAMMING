<?php
$start = trim((string)($_GET['mulai'] ?? ''));
$end = trim((string)($_GET['sampai'] ?? ''));
$baseSql = 'SELECT id_transaksi, tanggal, nama_pelanggan, jumlah_jenis_produk, total_barang, total_belanja FROM view_laporan_penjualan_bab6';
$params = [];
$conditions = [];
if ($start !== '') {
    $conditions[] = 'tanggal >= ?';
    $params[] = $start;
}
if ($end !== '') {
    $conditions[] = 'tanggal <= ?';
    $params[] = $end;
}
$sql = $baseSql . ($conditions ? ' WHERE ' . implode(' AND ', $conditions) : '') . ' ORDER BY tanggal DESC, id_transaksi DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reports = $stmt->fetchAll();
$reportTotal = array_sum(array_map(static fn($row) => (float)$row['total_belanja'], $reports));
?>
<section class="page-heading"><div><span class="eyebrow">ANALISIS PENJUALAN</span><h1>Laporan Penjualan</h1><p>Laporan ini mengambil data dari <code>view_laporan_penjualan_bab6</code>.</p></div><button class="button button-light" type="button" onclick="window.print()">Cetak Laporan</button></section>
<section class="stats-grid report-stats"><article class="stat-card"><div class="stat-top"><span class="stat-icon green">▤</span><span class="stat-caption">HASIL FILTER</span></div><div class="stat-value"><?= number_format(count($reports)) ?></div><div class="stat-label">Transaksi Ditampilkan</div></article><article class="stat-card"><div class="stat-top"><span class="stat-icon teal">Rp</span><span class="stat-caption">TOTAL NILAI</span></div><div class="stat-value stat-currency"><?= e(rupiah($reportTotal)) ?></div><div class="stat-label">Total Penjualan pada Laporan</div></article></section>
<section class="panel panel-table"><div class="panel-heading"><div><h2>Data Laporan</h2><p>Gunakan rentang tanggal untuk menyaring laporan.</p></div></div>
    <form class="filter-form" method="get"><input type="hidden" name="page" value="laporan"><label>Dari tanggal<input type="date" name="mulai" value="<?= e($start) ?>"></label><label>Sampai tanggal<input type="date" name="sampai" value="<?= e($end) ?>"></label><button class="button button-primary" type="submit">Terapkan Filter</button><a class="button button-light" href="index.php?page=laporan">Reset</a></form>
    <div class="table-wrap"><table><thead><tr><th>ID Transaksi</th><th>Tanggal</th><th>Pelanggan</th><th>Jenis Produk</th><th>Total Barang</th><th class="text-right">Total Belanja</th></tr></thead><tbody><?php foreach ($reports as $row): ?><tr><td class="mono">#<?= e($row['id_transaksi']) ?></td><td><?= e(date('d/m/Y', strtotime($row['tanggal']))) ?></td><td class="cell-strong"><?= e($row['nama_pelanggan']) ?></td><td><?= number_format((int)$row['jumlah_jenis_produk']) ?></td><td><?= number_format((int)$row['total_barang']) ?></td><td class="text-right amount"><?= e(rupiah($row['total_belanja'])) ?></td></tr><?php endforeach; ?><?php if (!$reports): ?><tr><td colspan="6"><div class="table-empty">Tidak ada data pada rentang tanggal ini.</div></td></tr><?php endif; ?></tbody><tfoot><tr><td colspan="5" class="text-right cell-strong">Total</td><td class="text-right amount"><?= e(rupiah($reportTotal)) ?></td></tr></tfoot></table></div>
</section>
