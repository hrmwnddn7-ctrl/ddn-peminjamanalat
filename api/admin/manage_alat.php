<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$msg = '';

// Handle Create
if (isset($_POST['add'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_alat']);
    $kategori = $_POST['id_kategori'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $stok = $_POST['stok'];
    
    // Simple Image Upload
    // Image Upload Logic
    $gambar = 'default.jpg';
    if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0){
        // Gunakan path absolut dari project root untuk upload
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/assets/img/";
        
        // Pastikan folder ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $gambar = time() . "_" . basename($_FILES["gambar"]["name"]);
        move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_dir . $gambar);
    }

    $sql = "INSERT INTO alat (id_kategori, nama_alat, deskripsi, stok, gambar) VALUES ('$kategori', '$nama', '$deskripsi', '$stok', '$gambar')";
    if(mysqli_query($conn, $sql)) {
        $msg = '<div class="alert alert-success">Alat berhasil ditambahkan!</div>';
    } else {
        $msg = '<div class="alert alert-danger">Error: '.mysqli_error($conn).'</div>';
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM alat WHERE id_alat = $id");
    header("Location: manage_alat.php");
    exit;
}

$alat = mysqli_query($conn, "SELECT alat.*, kategori.nama_kategori FROM alat JOIN kategori ON alat.id_kategori = kategori.id_kategori ORDER BY id_alat DESC");
$cats = mysqli_query($conn, "SELECT * FROM kategori");

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
            <a href="../logout.php" class="list-group-item list-group-item-action text-danger">Logout</a>
        </div>
    </div>
    <div class="col-md-9">
        <h2 class="mb-4">Kelola Alat</h2>
        <?php echo $msg; ?>
        
        <!-- Add Form -->
        <div class="card mb-4">
            <div class="card-header">Tambah Alat</div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data" class="row g-3">
                    <div class="col-md-6">
                        <label>Nama Alat</label>
                        <input type="text" name="nama_alat" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Kategori</label>
                        <select name="id_kategori" class="form-select" required>
                            <?php foreach($cats as $c): ?>
                                <option value="<?php echo $c['id_kategori']; ?>"><?php echo $c['nama_kategori']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label>Stok</label>
                        <input type="number" name="stok" class="form-control" required min="0">
                    </div>
                    <div class="col-md-6">
                        <label>Gambar</label>
                        <input type="file" name="gambar" class="form-control">
                    </div>
                    <div class="col-12 mt-3">
                        <button type="submit" name="add" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                   
                    <th>Gambar</th>
                    <th>Nama Alat</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($alat)): ?>
                <tr>
                    <td><img src="/assets/img/<?php echo $row['gambar']; ?>" width="50" class="img-thumbnail" alt="img"></td>
                    <td><?php echo $row['nama_alat']; ?></td>
                    <td><?php echo $row['nama_kategori']; ?></td>
                    <td><?php echo $row['stok']; ?></td>
                    <td>
                        <a href="?delete=<?php echo $row['id_alat']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
