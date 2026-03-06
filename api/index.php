<?php
/**
 * Vercel PHP Router
 * This file acts as a bridge for a traditional PHP application.
 */

$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Normalize path (remove leading slash)
$path = ltrim($path, '/');

// Default to index.php if empty
if ($path === '' || $path === '/') {
    $path = 'index.php';
}

// Check if it's a directory (and index.php exists there)
if (is_dir(__DIR__ . '/../' . $path)) {
    $path = rtrim($path, '/') . '/index.php';
}

// In some cases, clean URLs might not have .php extension
if (!file_exists(__DIR__ . '/../' . $path) && file_exists(__DIR__ . '/../' . $path . '.php')) {
    $path .= '.php';
}


// Security: Prevent direct access to config/includes via here
if (preg_match('/^(config|includes)\//', $path)) {
    http_response_code(403);
    die('Access Forbidden');
}

$target = __DIR__ . '/../' . $path;

if (file_exists($target) && is_file($target)) {
    // Set current working directory to where the file is 
    // This helps with relative includes inside the target files.
    chdir(dirname($target));
    require $target;
} else {
    http_response_code(404);
    echo "404 - File Not Found: " . htmlspecialchars($path);
}
