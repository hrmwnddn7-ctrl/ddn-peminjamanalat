<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: /login");
    exit;
}

$id = $_GET['id'];
$alat_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM alat WHERE id_alat = $id"));
$cats = mysqli_query($conn, "SELECT * FROM kategori");

if (!$alat_data) {
    header("Location: manage_alat.php");
    exit;
}

$msg = '';

if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_alat']);
    $id_kategori = $_POST['id_kategori'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $stok = $_POST['stok'];
    $gambar = $alat_data['gambar'];

    // Handle Image Upload if new file provided
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/assets/img/";
        
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $new_gambar = time() . "_" . basename($_FILES["gambar"]["name"]);
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_dir . $new_gambar)) {
            $gambar = $new_gambar;
        }
    }

    $sql = "UPDATE alat SET id_kategori='$id_kategori', nama_alat='$nama', deskripsi='$deskripsi', stok='$stok', gambar='$gambar' WHERE id_alat=$id";
    
    if (mysqli_query($conn, $sql)) {
        $msg = '<div class="alert alert-success">Alat berhasil diperbarui! <a href="manage_alat.php">Kembali</a></div>';
        // Refresh data
        $alat_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM alat WHERE id_alat = $id"));
    } else {
        $msg = '<div class="alert alert-danger">Gagal memperbarui alat!</div>';
    }
}

include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="index.php" class="list-group-item list-group-item-action">Dashboard</a>
            <a href="manage_kategori.php" class="list-group-item list-group-item-action">Data Kategori</a>
            <a href="manage_alat.php" class="list-group-item list-group-item-action active">Data Alat</a>
            <a href="manage_users.php" class="list-group-item list-group-item-action">Data User</a>
            <a href="manage_peminjaman.php" class="list-group-item list-group-item-action">Data Peminjaman</a>
            <a href="/logout" class="list-group-item list-group-item-action text-danger">Logout</a>
        </div>
    </div>
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Alat</h2>
            <a href="manage_alat.php" class="btn btn-secondary btn-sm">Kembali</a>
        </div>
        
        <?php echo $msg; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3 text-center">
                        <img src="/assets/img/<?php echo $alat_data['gambar']; ?>" class="img-thumbnail mb-2" style="max-height: 200px;">
                        <br>
                        <small class="text-muted">Gambar Saat Ini</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Alat</label>
                        <input type="text" name="nama_alat" class="form-control" value="<?php echo htmlspecialchars($alat_data['nama_alat']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="id_kategori" class="form-select" required>
                            <?php while($c = mysqli_fetch_assoc($cats)): ?>
                                <option value="<?php echo $c['id_kategori']; ?>" <?php if($c['id_kategori'] == $alat_data['id_kategori']) echo 'selected'; ?>>
                                    <?php echo $c['nama_kategori']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"><?php echo htmlspecialchars($alat_data['deskripsi']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stok" class="form-control" value="<?php echo $alat_data['stok']; ?>" required min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ganti Gambar (Kosongkan jika tidak ingin ganti)</label>
                        <input type="file" name="gambar" class="form-control">
                    </div>
                    <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
