<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'peminjam') {
    header("Location: ../login.php");
    exit;
}

// Handle Loan Request
if (isset($_GET['pinjam'])) {
    $id_alat = $_GET['pinjam'];
    $id_user = $_SESSION['user_id'];
    $tgl_pinjam = date('Y-m-d');
    $tgl_kembali = date('Y-m-d', strtotime('+7 days')); // Default 7 days loan

    // Check stok
    $alat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stok FROM alat WHERE id_alat = $id_alat"));
    if ($alat['stok'] > 0) {
        $sql = "INSERT INTO peminjaman (id_user, id_alat, tanggal_pinjam, tanggal_kembali_rencana, status) VALUES ('$id_user', '$id_alat', '$tgl_pinjam', '$tgl_kembali', 'menunggu')";
        if(mysqli_query($conn, $sql)){
            echo "<script>alert('Berhasil mengajukan peminjaman! Tunggu persetujuan petugas.'); window.location='index.php';</script>";
        }
    } else {
        echo "<script>alert('Stok habis!'); window.location='index.php';</script>";
    }
}

$sql = "SELECT alat.*, kategori.nama_kategori FROM alat JOIN kategori ON alat.id_kategori = kategori.id_kategori";
$items = mysqli_query($conn, $sql);

include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="index.php" class="list-group-item list-group-item-action active">Daftar Alat</a>
            <a href="riwayat.php" class="list-group-item list-group-item-action">Riwayat Peminjaman</a>
            <a href="../logout.php" class="list-group-item list-group-item-action text-danger">Logout</a>
        </div>
    </div>
    <div class="col-md-9">
        <h2 class="mb-4">Katalog Alat</h2>
        
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php while($row = mysqli_fetch_assoc($items)): ?>
            <div class="col">
                <div class="card h-100">
                    <img src="../assets/img/<?php echo $row['gambar']; ?>" class="card-img-top" alt="..." style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['nama_alat']; ?></h5>
                        <p class="card-text text-muted"><?php echo $row['nama_kategori']; ?></p>
                        <p class="card-text"><?php echo $row['deskripsi']; ?></p>
                        <p class="card-text"><strong>Stok: <?php echo $row['stok']; ?></strong></p>
                    </div>
                    <div class="card-footer">
                        <?php if($row['stok'] > 0): ?>
                            <a href="?pinjam=<?php echo $row['id_alat']; ?>" class="btn btn-primary w-100" onclick="return confirm('Ajukan peminjaman alat ini?')">Pinjam</a>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100" disabled>Habis</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
