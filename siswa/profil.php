<?php
session_start();
require_once '../koneksi.php';
require_once '../includes/fungsi.php';

if (!isLoggedIn() || !isSiswa()) {
    redirect('../index.php');
}

$title = 'Profil Saya';
$base_url = '../';
include '../includes/header.php';

$id_anggota = $_SESSION['user_id'];
$anggota = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM anggota WHERE id_anggota = $id_anggota"));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = clean($_POST['nama']);
    $kelas_jurusan = clean($_POST['kelas_jurusan']);
    $alamat = clean($_POST['alamat']);
    $nohp = clean($_POST['nohp']);
    $email = clean($_POST['email']);
    
    $sql = "UPDATE anggota SET nama = ?, kelas_jurusan = ?, alamat = ?, nohp = ?, email = ? WHERE id_anggota = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "sssssi", $nama, $kelas_jurusan, $alamat, $nohp, $email, $id_anggota);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['nama'] = $nama;
        $_SESSION['success'] = 'Profil berhasil diupdate!';
        redirect('profil.php');
    } else {
        $error = 'Gagal mengupdate profil!';
    }
}
?>

<div class="d-flex">
    <?php include '../includes/sidebar_siswa.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Profil Saya</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Edit Profil</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">NIS/NISN</label>
                                <input type="text" class="form-control" value="<?php echo $anggota['nis_nisn']; ?>" readonly>
                                <small class="text-muted">NIS/NISN tidak dapat diubah</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" value="<?php echo $anggota['nama']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Kelas/Jurusan</label>
                                <input type="text" name="kelas_jurusan" class="form-control" value="<?php echo $anggota['kelas_jurusan']; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <input type="text" class="form-control" value="<?php echo $anggota['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?>" readonly>
                                <small class="text-muted">Jenis kelamin tidak dapat diubah</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="3"><?php echo $anggota['alamat']; ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">No. HP</label>
                                <input type="text" name="nohp" class="form-control" value="<?php echo $anggota['nohp']; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo $anggota['email']; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" value="<?php echo $anggota['username']; ?>" readonly>
                                <small class="text-muted">Username tidak dapat diubah</small>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Akun</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Status Akun:</strong><br>
                            <span class="badge bg-<?php echo $anggota['status_aktif'] ? 'success' : 'danger'; ?>">
                                <?php echo $anggota['status_aktif'] ? 'Aktif' : 'Nonaktif'; ?>
                            </span>
                        </p>
                        <p><strong>Tanggal Daftar:</strong><br>
                            <?php echo formatTanggal($anggota['tanggal_daftar']); ?>
                        </p>
                        <p class="mb-0"><strong>Terakhir Diupdate:</strong><br>
                            <?php echo date('d/m/Y H:i', strtotime($anggota['updated_at'])); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>