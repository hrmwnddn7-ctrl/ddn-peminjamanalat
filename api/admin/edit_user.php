<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: /login");
    exit;
}

$id = $_GET['id'];
$user_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id_user = $id"));

if (!$user_data) {
    header("Location: manage_users.php");
    exit;
}

$msg = '';

if (isset($_POST['update'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $role = $_POST['role'];
    
    // Update basic info
    $sql = "UPDATE users SET username='$username', nama_lengkap='$nama', role='$role' WHERE id_user=$id";
    
    if (mysqli_query($conn, $sql)) {
        // If password is provided, update it too
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE users SET password='$password' WHERE id_user=$id");
        }
        
        // Update session if editing self
        if ($id == $_SESSION['user_id']) {
            $_SESSION['nama_lengkap'] = $nama;
        }
        
        $msg = '<div class="alert alert-success">User berhasil diperbarui! <a href="manage_users.php">Kembali</a></div>';
        // Refresh data
        $user_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id_user = $id"));
    } else {
        $msg = '<div class="alert alert-danger">Gagal memperbarui user!</div>';
    }
}

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
            <a href="/logout" class="list-group-item list-group-item-action text-danger">Logout</a>
        </div>
    </div>
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit User</h2>
            <a href="manage_users.php" class="btn btn-secondary btn-sm">Kembali</a>
        </div>
        
        <?php echo $msg; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?php echo $user_data['username']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" value="<?php echo $user_data['nama_lengkap']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="peminjam" <?php if($user_data['role'] == 'peminjam') echo 'selected'; ?>>Peminjam</option>
                            <option value="petugas" <?php if($user_data['role'] == 'petugas') echo 'selected'; ?>>Petugas</option>
                            <option value="admin" <?php if($user_data['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru (Kosongkan jika tidak ingin ganti)</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
