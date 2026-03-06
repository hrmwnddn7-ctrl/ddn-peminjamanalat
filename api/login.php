<?php
session_start();
include 'config/database.php';

if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    // Redirect based on role if already logged in
    $role = $_SESSION['role'];
    if ($role == 'admin') {
        header("Location: admin/index.php");
        exit;
    } elseif ($role == 'petugas') {
        header("Location: petugas/index.php");
        exit;
    } elseif ($role == 'peminjam') {
        header("Location: peminjam/index.php");
        exit;
    }
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];

        if ($user['role'] == 'admin') header("Location: admin/index.php");
        elseif ($user['role'] == 'petugas') header("Location: petugas/index.php");
        elseif ($user['role'] == 'peminjam') header("Location: peminjam/index.php");
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Peminjaman Alat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { display: flex; align-items: center; justify-content: center; height: 100vh; background-color: #f0f2f5; }
        .login-card { width: 100%; max-width: 400px; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); background: white; }
    </style>
</head>
<body>

<div class="login-card">
    <h3 class="text-center mb-4 text-primary">Login App</h3>
    
    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required placeholder="admin / petugas / peminjam">
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required placeholder="123456">
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
    <div class="mt-3 text-center text-muted">
        <small>Default Pass: 123456</small>
    </div>
</div>

</body>
</html>
