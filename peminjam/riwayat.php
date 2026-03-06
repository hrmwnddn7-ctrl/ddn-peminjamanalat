<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'peminjam') {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['user_id'];
$sql = "SELECT peminjaman.*, alat.nama_alat 
        FROM peminjaman 
        JOIN alat ON peminjaman.id_alat = alat.id_alat 
        WHERE peminjaman.id_user = $id_user 
        ORDER BY id_peminjaman DESC";
$history = mysqli_query($conn, $sql);

include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="index.php" class="list-group-item list-group-item-action">Daftar Alat</a>
            <a href="riwayat.php" class="list-group-item list-group-item-action active">Riwayat Peminjaman</a>
            <a href="../logout.php" class="list-group-item list-group-item-action text-danger">Logout</a>
        </div>
    </div>
    <div class="col-md-9">
        <h2 class="mb-4">Riwayat Peminjaman Saya</h2>
        
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Alat</th>
                    <th>Tgl Pinjam</th>
                    <th>Tgl Rencana Kembali</th>
                    <th>Status</th>
                    <th>Denda</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($history)): ?>
                <tr>
                    <td><?php echo $row['nama_alat']; ?></td>
                    <td><?php echo $row['tanggal_pinjam']; ?></td>
                    <td><?php echo $row['tanggal_kembali_rencana']; ?></td>
                    <td>
                        <?php 
                        $badge = 'secondary';
                        if($row['status'] == 'menunggu') $badge = 'warning';
                        if($row['status'] == 'disetujui') $badge = 'primary';
                        if($row['status'] == 'ditolak') $badge = 'danger';
                        if($row['status'] == 'kembali') $badge = 'success';
                        ?>
                        <span class="badge bg-<?php echo $badge; ?>"><?php echo ucfirst($row['status']); ?></span>
                    </td>
                    <td>Rp <?php echo number_format($row['denda'], 0, ',', '.'); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
