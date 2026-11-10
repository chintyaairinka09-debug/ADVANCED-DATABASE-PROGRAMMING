<?php

declare(strict_types=1);

date_default_timezone_set('Asia/Jakarta');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function e(mixed $value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function rupiah(mixed $value): string
{
    return 'Rp ' . number_format((float)$value, 0, ',', '.');
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        throw new RuntimeException('Permintaan tidak valid. Muat ulang halaman, lalu coba lagi.');
    }
}

function page_title(string $page): string
{
    return match ($page) {
        'pelanggan' => 'Data Pelanggan',
        'kategori'  => 'Data Kategori',
        'produk'    => 'Data Produk',
        'transaksi' => 'Transaksi Penjualan',
        'laporan'   => 'Laporan Penjualan',
        'log'       => 'Log Aktivitas',
        default     => 'Dashboard',
    };
}

function is_active(string $current, string $target): string
{
    return $current === $target ? ' active' : '';
}
