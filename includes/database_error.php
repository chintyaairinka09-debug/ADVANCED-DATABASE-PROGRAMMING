<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koneksi Database Bermasalah</title>
    <style>
        body{font-family:Arial,sans-serif;background:#f3f7f3;color:#173b2b;display:grid;place-items:center;min-height:100vh;margin:0;padding:24px;box-sizing:border-box}
        main{max-width:620px;background:#fff;border:1px solid #dce9df;border-radius:18px;padding:28px;box-shadow:0 15px 40px #173b2b12}
        h1{margin-top:0;color:#166534}code{background:#eff6f0;padding:3px 6px;border-radius:5px}pre{white-space:pre-wrap;overflow-wrap:anywhere;background:#f8faf8;padding:12px;border-radius:8px;color:#7f1d1d}
    </style>
</head>
<body>
<main>
    <h1>Belum bisa terhubung ke database</h1>
    <p>Periksa apakah MySQL di XAMPP sudah berjalan dan database <code>db_tokoku</code> sudah tersedia.</p>
    <p>Pastikan juga pengaturan di <code>config/database.php</code> sesuai dengan username dan password MySQL di komputermu.</p>
    <details><summary>Detail pesan dari database</summary><pre><?= htmlspecialchars($connectionError ?? 'Tidak ada detail.', ENT_QUOTES, 'UTF-8') ?></pre></details>
</main>
</body>
</html>
