<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../login.php");
    exit;
}

$sql = "SELECT peminjaman.*, users.nama_lengkap, alat.nama_alat 
        FROM peminjaman 
        JOIN users ON peminjaman.id_user = users.id_user 
        JOIN alat ON peminjaman.id_alat = alat.id_alat 
        WHERE status = 'kembali' 
        ORDER BY tanggal_kembali_real DESC";

if(isset($_GET['filter_start']) && isset($_GET['filter_end'])){
    $start = $_GET['filter_start'];
    $end = $_GET['filter_end'];
    $sql = "SELECT peminjaman.*, users.nama_lengkap, alat.nama_alat 
        FROM peminjaman 
        JOIN users ON peminjaman.id_user = users.id_user 
        JOIN alat ON peminjaman.id_alat = alat.id_alat 
        WHERE status = 'kembali' 
        AND tanggal_kembali_real BETWEEN '$start' AND '$end'
        ORDER BY tanggal_kembali_real DESC";
}

$reports = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peminjaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body class="p-5">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Laporan Pengembalian Barang</h2>
    <div class="no-print">
        <a href="index.php" class="btn btn-secondary">Kembali</a>
        <button onclick="window.print()" class="btn btn-primary">Cetak PDF</button>
    </div>
</div>

<form class="no-print row g-3 mb-4" method="GET">
    <div class="col-auto">
        <input type="date" name="filter_start" class="form-control" required>
    </div>
    <div class="col-auto">
        <input type="date" name="filter_end" class="form-control" required>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">Filter Tanggal</button>
    </div>
    <div class="col-auto">
        <a href="laporan.php" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>No</th>
            <th>Peminjam</th>
            <th>Alat</th>
            <th>Tgl Pinjam</th>
            <th>Tgl Kembali Real</th>
            <th>Denda</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        while($row = mysqli_fetch_assoc($reports)): 
        ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $row['nama_lengkap']; ?></td>
            <td><?php echo $row['nama_alat']; ?></td>
            <td><?php echo $row['tanggal_pinjam']; ?></td>
            <td><?php echo $row['tanggal_kembali_real']; ?></td>
            <td>Rp <?php echo number_format($row['denda'], 0, ',', '.'); ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
