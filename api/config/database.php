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
 */
$tables = [
    "users" => "CREATE TABLE IF NOT EXISTS users (
        id_user INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        nama_lengkap VARCHAR(100) NOT NULL,
        role ENUM('admin', 'petugas', 'peminjam') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "kategori" => "CREATE TABLE IF NOT EXISTS kategori (
        id_kategori INT AUTO_INCREMENT PRIMARY KEY,
        nama_kategori VARCHAR(50) NOT NULL
    )",
    "alat" => "CREATE TABLE IF NOT EXISTS alat (
        id_alat INT AUTO_INCREMENT PRIMARY KEY,
        id_kategori INT NOT NULL,
        nama_alat VARCHAR(100) NOT NULL,
        deskripsi TEXT,
        stok INT NOT NULL DEFAULT 0,
        gambar VARCHAR(255),
        FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori) ON DELETE CASCADE
    )",
    "peminjaman" => "CREATE TABLE IF NOT EXISTS peminjaman (
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
    )"
];

foreach ($tables as $name => $query) {
    mysqli_query($conn, $query);
}

// Cek dan isi data default jika kosong
$check_users = mysqli_query($conn, "SELECT id_user FROM users LIMIT 1");
if (mysqli_num_rows($check_users) == 0) {
    mysqli_query($conn, "INSERT INTO users (username, password, nama_lengkap, role) VALUES 
        ('admin', '$2y$10$L0A.bcTOIp2/6MTLYEkTxuLi5A.hd9myfSKYo7/Lj7Ok8MWBbeIdG', 'Administrator', 'admin'),
        ('petugas', '$2y$10$L0A.bcTOIp2/6MTLYEkTxuLi5A.hd9myfSKYo7/Lj7Ok8MWBbeIdG', 'Petugas 1', 'petugas'),
        ('peminjam', '$2y$10$L0A.bcTOIp2/6MTLYEkTxuLi5A.hd9myfSKYo7/Lj7Ok8MWBbeIdG', 'User Peminjam', 'peminjam')");
}

$check_cats = mysqli_query($conn, "SELECT id_kategori FROM kategori LIMIT 1");
if (mysqli_num_rows($check_cats) == 0) {
    mysqli_query($conn, "INSERT INTO kategori (id_kategori, nama_kategori) VALUES 
        (1, 'Elektronik'), (2, 'Perkakas'), (3, 'Audio Visual')");
}

$check_alat = mysqli_query($conn, "SELECT id_alat FROM alat LIMIT 1");
if (mysqli_num_rows($check_alat) == 0) {
    mysqli_query($conn, "INSERT INTO alat (id_kategori, nama_alat, deskripsi, stok, gambar) VALUES 
        (1, 'Laptop ASUS ROG', 'Laptop gaming spek tinggi', 5, 'laptop_rog.png'),
        (1, 'Proyektor Epson', 'Proyektor HD untuk presentasi', 2, 'default.jpg'),
        (2, 'Tang Kombinasi', 'Alat pertukangan umum', 10, 'default.jpg'),
        (3, 'Kamera Canon EOS', 'Kamera DSLR 24MP', 3, 'default.jpg')");
}



