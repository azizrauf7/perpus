<?php
require_once 'koneksi.php';
require_once 'includes/fungsi.php';

// Jika sudah login, redirect ke dashboard masing-masing
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('admin/dashboard.php');
    } else {
        redirect('siswa/dashboard.php');
    }
}

$title = 'Login - Perpustakaan';
include 'includes/header.php';
?>

<div class="container-fluid vh-100">
    <div class="row h-100">
        <!-- Left Side - Image -->
        <div class="col-md-6 d-none d-md-block p-0">
            <div class="h-100" style="background: url('assets/images/bg.jpg') center/cover;">
                <div class="h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.5);">
                    <div class="text-center text-white">
                        <h1 class="display-3 fw-bold">Perpustakaan</h1>
                        <p class="lead">Sistem Informasi Peminjaman Buku</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="col-md-6 d-flex align-items-center justify-content-center">
            <div class="w-100" style="max-width: 400px;">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Selamat Datang</h2>
                    <p class="text-muted">Silakan login untuk melanjutkan</p>
                </div>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['error']; 
                        unset($_SESSION['error']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['success']; 
                        unset($_SESSION['success']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <form action="login-proses.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required autofocus>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Pasword</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Login Sebagai</label>
                                <select name="role" class="form-select" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="admin">Admin</option>
                                    <option value="siswa">Siswa</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p class="mb-0">Belum punya akun? <a href="siswa/register.php">Daftar Sekarang</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>