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

/**
 * AUTO SETUP: Buat tabel jika belum ada
 * Ini sangat membantu saat pertama kali deploy ke Vercel/Aiven
 */
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
if (mysqli_num_rows($check_table) == 0) {
    // Jalankan query pembuatan tabel
    $queries = [
        "CREATE TABLE IF NOT EXISTS users (
            id_user INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            nama_lengkap VARCHAR(100) NOT NULL,
            role ENUM('admin', 'petugas', 'peminjam') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS kategori (
            id_kategori INT AUTO_INCREMENT PRIMARY KEY,
            nama_kategori VARCHAR(50) NOT NULL
        )",
        "CREATE TABLE IF NOT EXISTS alat (
            id_alat INT AUTO_INCREMENT PRIMARY KEY,
            id_kategori INT NOT NULL,
            nama_alat VARCHAR(100) NOT NULL,
            deskripsi TEXT,
            stok INT NOT NULL DEFAULT 0,
            gambar VARCHAR(255),
            FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori) ON DELETE CASCADE
        )",
        "CREATE TABLE IF NOT EXISTS peminjaman (
            id_peminjaman INT AUTO_INCREMENT PRIMARY KEY,
            id_user INT NOT NULL,
            id_alat INT NOT NULL,
            tanggal_pinjam DATE NOT NULL,
            tanggal_kembali_rencana DATE NOT NULL,
            tanggal_kembali_real DATE,
            status ENUM('menunggu', 'disetujui', 'ditolak', 'kembali') DEFAULT 'menunggu',
            denda INT DEFAULT 0,
            petugas_acc_id INT,
            FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
            FOREIGN KEY (id_alat) REFERENCES alat(id_alat) ON DELETE CASCADE
        )",
        "INSERT IGNORE INTO users (username, password, nama_lengkap, role) VALUES 
        ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin'),
        ('petugas', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Petugas 1', 'petugas'),
        ('peminjam', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'User Peminjam', 'peminjam')"
    ];

    foreach ($queries as $q) {
        mysqli_query($conn, $q);
    }
}
?>


