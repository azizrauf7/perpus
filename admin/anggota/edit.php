<?php
session_start();
require_once '../../koneksi.php';
require_once '../../includes/fungsi.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$title = 'Edit Anggota';
$base_url = '../../';
include '../../includes/header.php';

$id_anggota = $_GET['id'] ?? 0;
$anggota = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM anggota WHERE id_anggota = $id_anggota"));

if (!$anggota) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nis_nisn = clean($_POST['nis_nisn']);
    $nama = clean($_POST['nama']);
    $kelas_jurusan = clean($_POST['kelas_jurusan']);
    $jenis_kelamin = clean($_POST['jenis_kelamin']);
    $alamat = clean($_POST['alamat']);
    $nohp = clean($_POST['nohp']);
    $email = clean($_POST['email']);
    $username = clean($_POST['username']);
    $status_aktif = clean($_POST['status_aktif']);
    
    // Update password jika diisi
    if (!empty($_POST['password'])) {
        $password = hashPassword($_POST['password']);
        $sql = "UPDATE anggota SET nis_nisn = ?, nama = ?, kelas_jurusan = ?, jenis_kelamin = ?, 
                alamat = ?, nohp = ?, email = ?, username = ?, password = ?, status_aktif = ? 
                WHERE id_anggota = ?";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "sssssssssii", $nis_nisn, $nama, $kelas_jurusan, $jenis_kelamin, 
                               $alamat, $nohp, $email, $username, $password, $status_aktif, $id_anggota);
    } else {
        $sql = "UPDATE anggota SET nis_nisn = ?, nama = ?, kelas_jurusan = ?, jenis_kelamin = ?, 
                alamat = ?, nohp = ?, email = ?, username = ?, status_aktif = ? 
                WHERE id_anggota = ?";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssssii", $nis_nisn, $nama, $kelas_jurusan, $jenis_kelamin, 
                               $alamat, $nohp, $email, $username, $status_aktif, $id_anggota);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        logAktivitas($koneksi, 'edit_anggota', 'Mengedit anggota: ' . $nama, $_SESSION['user_id'], null);
        $_SESSION['success'] = 'Anggota berhasil diupdate!';
        redirect('index.php');
    } else {
        $error = 'Gagal mengupdate anggota!';
    }
}
?>

<div class="d-flex">
    <?php include '../../includes/sidebar_admin.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Edit Anggota</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIS/NISN</label>
                            <input type="text" name="nis_nisn" class="form-control" value="<?php echo $anggota['nis_nisn']; ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" value="<?php echo $anggota['nama']; ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kelas/Jurusan</label>
                            <input type="text" name="kelas_jurusan" class="form-control" value="<?php echo $anggota['kelas_jurusan']; ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="L" <?php echo $anggota['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="P" <?php echo $anggota['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2"><?php echo $anggota['alamat']; ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. HP</label>
                            <input type="text" name="nohp" class="form-control" value="<?php echo $anggota['nohp']; ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $anggota['email']; ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" value="<?php echo $anggota['username']; ?>" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status_aktif" class="form-select" required>
                                <option value="1" <?php echo $anggota['status_aktif'] == 1 ? 'selected' : ''; ?>>Aktif</option>
                                <option value="0" <?php echo $anggota['status_aktif'] == 0 ? 'selected' : ''; ?>>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="index.php" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>