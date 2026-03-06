<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../login.php");
    exit;
}

// Handle Actions
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $petugas = $_SESSION['user_id'];
    
    // Decrement stock
    $loan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_alat FROM peminjaman WHERE id_peminjaman = $id"));
    $id_alat = $loan['id_alat'];
    
    // Check stock again for safety
    $alat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stok FROM alat WHERE id_alat = $id_alat"));
    if($alat['stok'] > 0){
        mysqli_query($conn, "UPDATE alat SET stok = stok - 1 WHERE id_alat = $id_alat");
        mysqli_query($conn, "UPDATE peminjaman SET status = 'disetujui', petugas_acc_id = '$petugas' WHERE id_peminjaman = $id");
        echo "<script>alert('Peminjaman disetujui!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Stok habis!'); window.location='index.php';</script>";
    }
}

if (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    mysqli_query($conn, "UPDATE peminjaman SET status = 'ditolak' WHERE id_peminjaman = $id");
    header("Location: index.php");
}

if (isset($_GET['return'])) {
    $id = $_GET['return'];
    // Calculate Fine
    $loan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM peminjaman WHERE id_peminjaman = $id"));
    $tgl_rencana = $loan['tanggal_kembali_rencana'];
    $tgl_real = date('Y-m-d');
    
    $denda = 0;
    if($tgl_real > $tgl_rencana){
        $earlier = new DateTime($tgl_rencana);
        $later = new DateTime($tgl_real);
        $diff = $later->diff($earlier)->format("%a");
        $denda = $diff * 1000; // Rp 1000 per day fine
    }
    
    // Return stock
    $id_alat = $loan['id_alat'];
    mysqli_query($conn, "UPDATE alat SET stok = stok + 1 WHERE id_alat = $id_alat");
    
    mysqli_query($conn, "UPDATE peminjaman SET status = 'kembali', tanggal_kembali_real = '$tgl_real', denda = '$denda' WHERE id_peminjaman = $id");
    echo "<script>alert('Barang dikembalikan. Denda: Rp ".number_format($denda)."'); window.location='index.php';</script>";
}

// Pending Loans
$pending = mysqli_query($conn, "SELECT peminjaman.*, users.nama_lengkap, alat.nama_alat 
                                FROM peminjaman 
                                JOIN users ON peminjaman.id_user = users.id_user 
                                JOIN alat ON peminjaman.id_alat = alat.id_alat 
                                WHERE status = 'menunggu'");

// Active Loans (Approved)
$active = mysqli_query($conn, "SELECT peminjaman.*, users.nama_lengkap, alat.nama_alat 
                               FROM peminjaman 
                               JOIN users ON peminjaman.id_user = users.id_user 
                               JOIN alat ON peminjaman.id_alat = alat.id_alat 
                               WHERE status = 'disetujui'");

// Returned/History
$history = mysqli_query($conn, "SELECT peminjaman.*, users.nama_lengkap, alat.nama_alat 
                                FROM peminjaman 
                                JOIN users ON peminjaman.id_user = users.id_user 
                                JOIN alat ON peminjaman.id_alat = alat.id_alat 
                                WHERE status = 'kembali' OR status = 'ditolak' 
                                ORDER BY id_peminjaman DESC LIMIT 10");

include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="index.php" class="list-group-item list-group-item-action active">Dashboard & Transaksi</a>
            <a href="laporan.php" class="list-group-item list-group-item-action">Cetak Laporan</a>
            <a href="../logout.php" class="list-group-item list-group-item-action text-danger">Logout</a>
        </div>
    </div>
    <div class="col-md-9">
        <h2 class="mb-4">Dashboard Petugas</h2>
        
        <div class="card mb-4 border-warning">
            <div class="card-header bg-warning text-dark">Permintaan Peminjaman (Menunggu)</div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Alat</th>
                            <th>Tgl Pinjam</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($pending)): ?>
                        <tr>
                            <td><?php echo $row['nama_lengkap']; ?></td>
                            <td><?php echo $row['nama_alat']; ?></td>
                            <td><?php echo $row['tanggal_pinjam']; ?></td>
                            <td>
                                <a href="?approve=<?php echo $row['id_peminjaman']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Setujui?')">Acc</a>
                                <a href="?reject=<?php echo $row['id_peminjaman']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tolak?')">Tolak</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-4 border-primary">
            <div class="card-header bg-primary text-white">Sedang Dipinjam (Klik tombol Kembalikan saat user mengembalikan)</div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Alat</th>
                            <th>Tgl Kembali Rencana</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($active)): ?>
                        <tr>
                            <td><?php echo $row['nama_lengkap']; ?></td>
                            <td><?php echo $row['nama_alat']; ?></td>
                            <td><?php echo $row['tanggal_kembali_rencana']; ?></td>
                            <td>
                                <a href="?return=<?php echo $row['id_peminjaman']; ?>" class="btn btn-info btn-sm text-white" onclick="return confirm('Proses pengembalian?')">Kembalikan</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

         <div class="card mb-4 border-success">
            <div class="card-header bg-success text-white">Riwayat Terakhir</div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Alat</th>
                            <th>Status</th>
                             <th>Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($history)): ?>
                        <tr>
                            <td><?php echo $row['nama_lengkap']; ?></td>
                            <td><?php echo $row['nama_alat']; ?></td>
                             <td><?php echo $row['status']; ?></td>
                            <td>Rp <?php echo number_format($row['denda'], 0, ',', '.'); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
