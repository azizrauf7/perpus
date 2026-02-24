<?php
require_once '../koneksi.php';
require_once '../includes/fungsi.php';

if (!isLoggedIn() || !isSiswa()) {
    redirect('../index.php');
}

$title = 'Ajukan Peminjaman';
$base_url = '../';
include '../includes/header.php';

$id_buku = $_GET['id_buku'] ?? 0;
$id_anggota = $_SESSION['user_id'];

// Ambil data buku
$buku = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM buku WHERE id_buku = $id_buku"));

if (!$buku) {
    $_SESSION['error'] = 'Buku tidak ditemukan!';
    redirect('buku.php');
}

// Cek stok
if ($buku['stok_tersedia'] < 1) {
    $_SESSION['error'] = 'Stok buku tidak tersedia!';
    redirect('buku.php');
}

// Cek apakah sudah pernah request yang masih pending
$cek_request = mysqli_query($koneksi, "
    SELECT * FROM request_peminjaman 
    WHERE id_anggota = $id_anggota 
    AND id_buku = $id_buku 
    AND status = 'pending'
");

if (mysqli_num_rows($cek_request) > 0) {
    $_SESSION['error'] = 'Anda sudah mengajukan peminjaman buku ini. Tunggu persetujuan admin.';
    redirect('buku.php');
}

// Cek apakah sedang meminjam buku yang sama
$cek_pinjam = mysqli_query($koneksi, "
    SELECT * FROM transaksi 
    WHERE id_anggota = $id_anggota 
    AND id_buku = $id_buku 
    AND status = 'dipinjam'
");

if (mysqli_num_rows($cek_pinjam) > 0) {
    $_SESSION['error'] = 'Anda masih meminjam buku ini!';
    redirect('buku.php');
}

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lama_pinjam = clean($_POST['lama_pinjam']);
    $keterangan = clean($_POST['keterangan']);
    
    $sql = "INSERT INTO request_peminjaman (id_anggota, id_buku, lama_pinjam, keterangan) 
            VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "iiis", $id_anggota, $id_buku, $lama_pinjam, $keterangan);
    
    if (mysqli_stmt_execute($stmt)) {
        logAktivitas($koneksi, 'request_peminjaman', 'Request peminjaman buku: ' . $buku['judul'], null, $id_anggota);
        $_SESSION['success'] = 'Request peminjaman berhasil diajukan! Tunggu persetujuan admin.';
        redirect('request-saya.php');
    } else {
        $error = 'Gagal mengajukan request!';
    }
}
?>

<div class="d-flex">
    <?php include '../includes/sidebar_siswa.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Ajukan Peminjaman Buku</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Form Request Peminjaman</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Lama Peminjaman (Hari)</label>
                                <select name="lama_pinjam" class="form-select" required>
                                    <option value="3">3 Hari</option>
                                    <option value="7" selected>7 Hari (1 Minggu)</option>
                                    <option value="14">14 Hari (2 Minggu)</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Keterangan (Opsional)</label>
                                <textarea name="keterangan" class="form-control" rows="3" 
                                          placeholder="Tuliskan alasan atau keperluan meminjam buku ini..."></textarea>
                            </div>
                            
                            <div class="alert alert-info">
                                <strong>ℹ️ Informasi:</strong><br>
                                • Request Anda akan diproses oleh admin<br>
                                • Anda akan mendapat notifikasi jika di-approve atau di-reject<br>
                                • Cek status di menu "Request Saya"
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    📤 Kirim Request
                                </button>
                                <a href="buku.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Detail Buku</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($buku['cover_path']): ?>
                            <img src="<?php echo $base_url; ?>assets/images/buku/<?php echo $buku['cover_path']; ?>" 
                                 class="img-fluid mb-3" alt="Cover">
                        <?php endif; ?>
                        
                        <h6><?php echo $buku['judul']; ?></h6>
                        <p class="mb-1"><small class="text-muted">Pengarang: <?php echo $buku['pengarang']; ?></small></p>
                        <p class="mb-1"><small class="text-muted">Penerbit: <?php echo $buku['penerbit']; ?></small></p>
                        <p class="mb-1"><small class="text-muted">ISBN: <?php echo $buku['isbn']; ?></small></p>
                        <p class="mb-2">
                            <span class="badge bg-success">Stok: <?php echo $buku['stok_tersedia']; ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>