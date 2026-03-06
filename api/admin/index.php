<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Get stats
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users"))['c'];
$total_alat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM alat"))['c'];
$total_kategori = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM kategori"))['c'];
$active_loans = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM peminjaman WHERE status = 'disetujui'"))['c'];

include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="index.php" class="list-group-item list-group-item-action active">Dashboard</a>
            <a href="manage_kategori.php" class="list-group-item list-group-item-action">Data Kategori</a>
            <a href="manage_alat.php" class="list-group-item list-group-item-action">Data Alat</a>
            <a href="manage_users.php" class="list-group-item list-group-item-action">Data User</a>
            <a href="manage_peminjaman.php" class="list-group-item list-group-item-action">Data Peminjaman</a>
            <a href="../logout.php" class="list-group-item list-group-item-action text-danger">Logout</a>
        </div>
    </div>
    <div class="col-md-9">
        <h2 class="mb-4">Admin Dashboard</h2>
        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Users</h5>
                        <p class="card-text display-4"><?php echo $total_users; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Alat</h5>
                        <p class="card-text display-4"><?php echo $total_alat; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Kategori</h5>
                        <p class="card-text display-4"><?php echo $total_kategori; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Pinjaman Aktif</h5>
                        <p class="card-text display-4"><?php echo $active_loans; ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <h4>Selamat Datang, Administrator!</h4>
            <p>Silahkan kelola data aplikasi melalui menu disamping.</p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
