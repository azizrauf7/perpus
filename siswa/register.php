<?php
session_start();
$title = 'Registrasi Anggota - Perpustakaan';
$base_url = '../';
include '../includes/header.php';
?>

<div class="container-fluid vh-100">
    <div class="row h-100">
        <!-- Left Side - Image -->
        <div class="col-md-6 d-none d-md-block p-0">
            <div class="h-100" style="background: url('../assets/images/bg.jpg') center/cover;">
                <div class="h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.5);">
                    <div class="text-center text-white">
                        <h1 class="display-3 fw-bold">Perpustakaan</h1>
                        <p class="lead">Daftar Sebagai Anggota Baru</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Register Form -->
        <div class="col-md-6 d-flex align-items-center justify-content-center py-5">
            <div class="w-100" style="max-width: 500px;">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Registrasi Anggota</h2>
                    <p class="text-muted">Isi data diri Anda dengan lengkap</p>
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

                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <form action="register-proses.php" method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">NIS/NISN</label>
                                    <input type="text" name="nis_nisn" class="form-control" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kelas/Jurusan</label>
                                    <input type="text" name="kelas_jurusan" class="form-control" placeholder="Contoh: XII RPL 1">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="form-select" required>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="2"></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">No. HP</label>
                                    <input type="text" name="nohp" class="form-control">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Daftar Sekarang</button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p class="mb-0">Sudah punya akun? <a href="../index.php">Login Sekarang</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>