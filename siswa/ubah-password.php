<?php
session_start();
require_once '../koneksi.php';
require_once '../includes/fungsi.php';

if (!isLoggedIn() || !isSiswa()) {
    redirect('../index.php');
}

$title = 'Ubah Password';
$base_url = '../';
include '../includes/header.php';

$id_anggota = $_SESSION['user_id'];
$anggota = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM anggota WHERE id_anggota = $id_anggota"));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    
    // Verifikasi password lama
    if (!verifyPassword($password_lama, $anggota['password'])) {
        $error = 'Password lama tidak sesuai!';
    } elseif ($password_baru !== $konfirmasi_password) {
        $error = 'Konfirmasi password tidak sesuai!';
    } elseif (strlen($password_baru) < 6) {
        $error = 'Password minimal 6 karakter!';
    } else {
        $password_hash = hashPassword($password_baru);
        $sql = "UPDATE anggota SET password = ? WHERE id_anggota = ?";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "si", $password_hash, $id_anggota);
        
        if (mysqli_stmt_execute($stmt)) {
            logAktivitas($koneksi, 'ubah_password', 'Siswa mengubah password', null, $id_anggota);
            $_SESSION['success'] = 'Password berhasil diubah!';
            redirect('ubah-password.php');
        } else {
            $error = 'Gagal mengubah password!';
        }
    }
}
?>

<div class="d-flex">
    <?php include '../includes/sidebar_siswa.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Ubah Password</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Ubah Password</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Password Lama <span class="text-danger">*</span></label>
                                <input type="password" name="password_lama" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                                <input type="password" name="password_baru" class="form-control" required minlength="6">
                                <small class="text-muted">Minimal 6 karakter</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                <input type="password" name="konfirmasi_password" class="form-control" required minlength="6">
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Ubah Password</button>
                        </form>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-body">
                        <h6>Tips Keamanan Password:</h6>
                        <ul class="mb-0">
                            <li>Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol</li>
                            <li>Jangan gunakan password yang mudah ditebak</li>
                            <li>Jangan bagikan password Anda kepada siapapun</li>
                            <li>Ubah password secara berkala</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>