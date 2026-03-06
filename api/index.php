<?php
/**
 * Vercel PHP Gateway
 */
ob_start();

$uri = $_SERVER['REQUEST_URI'];

$path = parse_url($uri, PHP_URL_PATH);
$path = ltrim($path, '/');

// Handle Root / Index
if ($path === '' || $path === 'index.php') {
    header("Location: /login");
    exit;
}

// Security: No config/includes access
if (preg_match('/^(config|includes)\//', $path)) {
    http_response_code(403);
    die('Akses Dilarang');
}

// Determine target file
$targetFile = __DIR__ . '/' . $path;

// Check if it's a directory
if (is_dir($targetFile)) {
    $targetFile = rtrim($targetFile, '/') . '/index.php';
}

// Try appending .php if not exists
if (!file_exists($targetFile) && file_exists($targetFile . '.php')) {
    $targetFile .= '.php';
}

if (file_exists($targetFile) && is_file($targetFile)) {
    // IMPORTANT: Avoid recursion if by any chance it still points to this file
    if (realpath($targetFile) === realpath(__FILE__)) {
        header("Location: /login");
        exit;
    }
    
    // Set CWD for relative paths in included files
    chdir(dirname($targetFile));
    require $targetFile;
} else {
    // Fallback or 404
    http_response_code(404);
    echo "<h1>404 Not Found</h1>";
    echo "<p>Tidak dapat menemukan: " . htmlspecialchars($path) . "</p>";
}
