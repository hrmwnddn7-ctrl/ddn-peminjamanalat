<?php
/**
 * Vercel PHP Gateway
 * Menangani semua rute aplikasi secara dinamis
 */

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Bersihkan path
$path = ltrim($path, '/');
if ($path === '') $path = 'index.php';

// Jika rute adalah folder (misal: /admin), arahkan ke index.php di folder tersebut
if ($path !== 'index.php' && $path !== '' && is_dir(__DIR__ . '/' . $path)) {
    $path = rtrim($path, '/') . '/index.php';
}

// Jika request tidak punya .php, tambahkan (untuk clean URLs)
$targetFile = __DIR__ . '/' . $path;
if (!file_exists($targetFile) && file_exists($targetFile . '.php')) {
    $targetFile .= '.php';
    $path .= '.php';
}

// Security: Larang akses ke config/includes dari luar
if (preg_match('/^(config|includes)\//', $path)) {
    http_response_code(403);
    die('Akses Dilarang');
}

if (file_exists($targetFile) && is_file($targetFile)) {
    // Jalankan file yang dituju
    require $targetFile;
} else {
    // Jika tetap tidak ketemu, coba cari di root (fall-through)
    http_response_code(404);
    echo "<h1>404 - Halaman Tidak Ditemukan</h1>";
    echo "<p>Path: /api/" . htmlspecialchars($path) . "</p>";
}
