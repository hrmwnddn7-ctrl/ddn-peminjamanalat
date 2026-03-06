<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Peminjaman Alat</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background-color: #343a40; color: white; }
        .sidebar a { color: white; display: block; padding: 10px 15px; }
        .sidebar a:hover { background-color: #495057; }
        .content { padding: 20px; }
    </style>
</head>
<body>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Basic checks can go here or in individual files
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand" href="#">Peminjaman Alat</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if(isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
                <span class="nav-link text-white">Halo, <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?> (<?php echo ucfirst($_SESSION['role']); ?>)</span>
            </li>
            <li class="nav-item">
                <a class="nav-link btn btn-danger btn-sm text-white px-3 ms-2" href="/logout">Logout</a>
            </li>
        <?php else: ?>
            <li class="nav-item">
                <a class="nav-link" href="/login">Login</a>
            </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Start Container for Content - Closed in footer -->
<div class="container">
