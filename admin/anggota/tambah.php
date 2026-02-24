<?php
session_start();
require_once '../../koneksi.php';
require_once '../../includes/fungsi.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$title = 'Tambah Anggota';
$base_url = '../../';
include '../../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nis_nisn = clean($_POST['nis_nisn']);
    $nama = clean($_POST['nama']);
    $kelas_jurusan = clean($_POST['kelas_jurusan']);
    $jenis_kelamin = clean($_POST['jenis_kelamin']);
    $alamat = clean($_POST['alamat']);
    $nohp = clean($_POST['nohp']);
    $email = clean($_POST['email']);
    $username = clean($_POST['username']);
    $password = hashPassword($_POST['password']);
    
    // Cek username
    $cek = mysqli_query($koneksi, "SELECT * FROM anggota WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        $error = 'Username sudah digunakan!';
    } else {
        $sql = "INSERT INTO anggota (nis_nisn, nama, kelas_jurusan, jenis_kelamin, alamat, nohp, email, username, password) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "sssssssss", $nis_nisn, $nama, $kelas_jurusan, $jenis_kelamin, $alamat, $nohp, $email, $username, $password);
        
        if (mysqli_stmt_execute($stmt)) {
            logAktivitas($koneksi, 'tambah_anggota', 'Menambah anggota: ' . $nama, $_SESSION['user_id'], null);
            $_SESSION['success'] = 'Anggota berhasil ditambahkan!';
            redirect('index.php');
        } else {
            $error = 'Gagal menambahkan anggota!';
        }
    }
}
?>

<div class="d-flex">
    <?php include '../../includes/sidebar_admin.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Tambah Anggota</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIS/NISN</label>
                            <input type="text" name="nis_nisn" class="form-control">
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
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="index.php" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>