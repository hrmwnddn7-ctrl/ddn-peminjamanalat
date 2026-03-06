<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Just List all logs
$sql = "SELECT peminjaman.*, users.nama_lengkap as peminjam, alat.nama_alat 
        FROM peminjaman 
        JOIN users ON peminjaman.id_user = users.id_user 
        JOIN alat ON peminjaman.id_alat = alat.id_alat 
        ORDER BY id_peminjaman DESC";
$loans = mysqli_query($conn, $sql);

include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="index.php" class="list-group-item list-group-item-action">Dashboard</a>
            <a href="manage_kategori.php" class="list-group-item list-group-item-action">Data Kategori</a>
            <a href="manage_alat.php" class="list-group-item list-group-item-action">Data Alat</a>
            <a href="manage_users.php" class="list-group-item list-group-item-action">Data User</a>
            <a href="manage_peminjaman.php" class="list-group-item list-group-item-action active">Data Peminjaman</a>
            <a href="../logout.php" class="list-group-item list-group-item-action text-danger">Logout</a>
        </div>
    </div>
    <div class="col-md-9">
        <h2 class="mb-4">Log Peminjaman (Admin View)</h2>
        
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Peminjam</th>
                    <th>Alat</th>
                    <th>Tgl Pinjam</th>
                    <th>Status</th>
                    <th>Tgl Kembali Real</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($loans)): ?>
                <tr>
                    <td><?php echo $row['id_peminjaman']; ?></td>
                    <td><?php echo $row['peminjam']; ?></td>
                    <td><?php echo $row['nama_alat']; ?></td>
                    <td><?php echo $row['tanggal_pinjam']; ?></td>
                    <td>
                        <?php 
                        $badge = 'secondary';
                        if($row['status'] == 'menunggu') $badge = 'warning';
                        if($row['status'] == 'disetujui') $badge = 'primary';
                        if($row['status'] == 'kembali') $badge = 'success';
                        ?>
                        <span class="badge bg-<?php echo $badge; ?>"><?php echo ucfirst($row['status']); ?></span>
                    </td>
                    <td><?php echo $row['tanggal_kembali_real'] ? $row['tanggal_kembali_real'] : '-'; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
