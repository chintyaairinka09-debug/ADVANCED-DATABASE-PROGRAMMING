<?php
$currentTitle = page_title($page);
$navigation = [
    ['page' => 'dashboard', 'icon' => '▦', 'label' => 'Dashboard'],
    ['page' => 'pelanggan', 'icon' => '♙', 'label' => 'Pelanggan'],
    ['page' => 'kategori', 'icon' => '▧', 'label' => 'Kategori'],
    ['page' => 'produk', 'icon' => '▣', 'label' => 'Produk'],
    ['page' => 'transaksi', 'icon' => '＋', 'label' => 'Transaksi'],
    ['page' => 'laporan', 'icon' => '▤', 'label' => 'Laporan Penjualan'],
    ['page' => 'log', 'icon' => '◷', 'label' => 'Log Aktivitas'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#14532d">
    <title><?= e($currentTitle) ?> | Penjualan Toko</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="index.php">
            <span class="brand-mark">PT</span>
            <span class="brand-copy"><strong>Penjualan Toko</strong><small>Admin Panel</small></span>
        </a>

        <div class="nav-label">MENU UTAMA</div>
        <nav class="main-nav" aria-label="Navigasi utama">
            <?php foreach ($navigation as $item): ?>
                <a class="nav-link<?= is_active($page, $item['page']) ?>" href="index.php?page=<?= e($item['page']) ?>">
                    <span class="nav-icon" aria-hidden="true"><?= e($item['icon']) ?></span>
                    <span><?= e($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="sidebar-bottom">
            <div class="sidebar-note">
                <span class="note-dot"></span>
                <div><strong>Database terhubung</strong><small>db_tokoku</small></div>
            </div>
            <small class="sidebar-version">Sistem Informasi Penjualan · v1.0</small>
        </div>
    </aside>

    <div class="main-area">
        <header class="topbar">
            <button class="icon-button menu-toggle" id="menuToggle" type="button" aria-label="Buka menu">☰</button>
            <div class="breadcrumb"><span>Penjualan Toko</span><span class="crumb-separator">/</span><strong><?= e($currentTitle) ?></strong></div>
            <div class="topbar-right">
                <span class="today-label"><?= e(date('d M Y')) ?></span>
                <div class="avatar" title="Admin">A</div>
            </div>
        </header>

        <main class="content">
            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?>" role="status">
                    <span><?= $flash['type'] === 'success' ? '✓' : '!' ?></span>
                    <div><?= e($flash['message']) ?></div>
                    <button class="alert-close" type="button" aria-label="Tutup pemberitahuan">×</button>
                </div>
            <?php endif; ?>
