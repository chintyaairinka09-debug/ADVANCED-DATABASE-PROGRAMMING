<?php
/**
 * Koneksi PDO untuk database db_tokoku.
 * Sesuaikan DB_USER dan DB_PASS jika MySQL XAMPP kamu memakai kredensial berbeda.
 */

$host = '127.0.0.1';
$dbname = 'db_tokoku';
$username = 'root';
$password = '';
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $exception) {
    http_response_code(500);
    $connectionError = $exception->getMessage();
    require __DIR__ . '/../includes/database_error.php';
    exit;
}
