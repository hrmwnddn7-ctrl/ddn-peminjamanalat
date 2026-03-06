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
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $role = $_POST['role'];

    // Check duplicate
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if(mysqli_num_rows($check) > 0){
        $msg = '<div class="alert alert-danger">Username sudah dipakai!</div>';
    } else {
        $sql = "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('$username', '$password', '$nama', '$role')";
        if(mysqli_query($conn, $sql)) {
            $msg = '<div class="alert alert-success">User berhasil ditambahkan!</div>';
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if($id != $_SESSION['user_id']){ // Prevent self-delete
        mysqli_query($conn, "DELETE FROM users WHERE id_user = $id");
    }
    header("Location: manage_users.php");
    exit;
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id_user DESC");

include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="index.php" class="list-group-item list-group-item-action">Dashboard</a>
            <a href="manage_kategori.php" class="list-group-item list-group-item-action">Data Kategori</a>
            <a href="manage_alat.php" class="list-group-item list-group-item-action">Data Alat</a>
            <a href="manage_users.php" class="list-group-item list-group-item-action active">Data User</a>
            <a href="manage_peminjaman.php" class="list-group-item list-group-item-action">Data Peminjaman</a>
            <a href="../logout.php" class="list-group-item list-group-item-action text-danger">Logout</a>
        </div>
    </div>
    <div class="col-md-9">
        <h2 class="mb-4">Kelola User</h2>
        <?php echo $msg; ?>
        
        <!-- Add Form -->
        <div class="card mb-4">
            <div class="card-header">Tambah User</div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <div class="col-md-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label>Role</label>
                        <select name="role" class="form-select" required>
                            <option value="peminjam">Peminjam</option>
                            <option value="petugas">Petugas</option>
                            <option value="admin">Admin</option>
                        </select>
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
                    <th>Username</th>
                    <th>Nama</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($users)): ?>
                <tr>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['nama_lengkap']; ?></td>
                    <td><span class="badge bg-secondary"><?php echo $row['role']; ?></span></td>
                    <td>
                        <?php if($row['id_user'] != $_SESSION['user_id']): ?>
                        <a href="?delete=<?php echo $row['id_user']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        <?php else: ?>
                            <span class="text-muted">Current</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
