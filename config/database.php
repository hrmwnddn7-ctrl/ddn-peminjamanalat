<?php
// Use environment variables for Vercel deployment, with fallbacks for local XAMPP
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: ''; 
$db   = getenv('DB_NAME') ?: 'db_peminjaman';
$port = getenv('DB_PORT') ?: '3306';

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

