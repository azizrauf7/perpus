<?php
require_once '../koneksi.php';
require_once '../includes/fungsi.php';

if (!isLoggedIn() || !isSiswa()) {
    redirect('../index.php');
}

$title = 'Ajukan Pengembalian';
$base_url = '../';
include '../includes/header.php';

$id_transaksi = $_GET['id'] ?? 0;
$id_anggota = $_SESSION['user_id'];

// Ambil data transaksi
$transaksi = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT t.*, b.judul, b.pengarang, b.cover_path, b.isbn
    FROM transaksi t
    JOIN buku b ON t.id_buku = b.id_buku
    WHERE t.id_transaksi = $id_transaksi 
    AND t.id_anggota = $id_anggota
    AND t.status = 'dipinjam'
"));

if (!$transaksi) {
    $_SESSION['error'] = 'Transaksi tidak ditemukan atau sudah dikembalikan!';
    redirect('pinjamanku.php');
}

// Cek apakah sudah ada request pending
$cek_request = mysqli_query($koneksi, "
    SELECT * FROM request_pengembalian 
    WHERE id_transaksi = $id_transaksi 
    AND status = 'pending'
");

if (mysqli_num_rows($cek_request) > 0) {
    $_SESSION['error'] = 'Anda sudah mengajukan pengembalian buku ini. Tunggu persetujuan admin.';
    redirect('pinjamanku.php');
}

// Hitung denda jika terlambat
$terlambat = strtotime($transaksi['batas_kembali']) < time();
$denda = 0;
if ($terlambat) {
    $denda = hitungDenda(date('Y-m-d'), $transaksi['batas_kembali']);
}

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kondisi_buku = clean($_POST['kondisi_buku']);
    $keterangan = clean($_POST['keterangan']);
    
    $sql = "INSERT INTO request_pengembalian (id_transaksi, id_anggota, kondisi_buku, keterangan) 
            VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "iiss", $id_transaksi, $id_anggota, $kondisi_buku, $keterangan);
    
    if (mysqli_stmt_execute($stmt)) {
        logAktivitas($koneksi, 'request_pengembalian', 'Request pengembalian buku: ' . $transaksi['judul'], null, $id_anggota);
        $_SESSION['success'] = 'Request pengembalian berhasil diajukan! Tunggu persetujuan admin.';
        redirect('request-saya.php');
    } else {
        $error = 'Gagal mengajukan request!';
    }
}
?>

<div class="d-flex">
    <?php include '../includes/sidebar_siswa.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Ajukan Pengembalian Buku</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($terlambat): ?>
            <div class="alert alert-warning">
                <strong>⚠️ Perhatian!</strong><br>
                Buku Anda sudah terlambat <?php echo round((time() - strtotime($transaksi['batas_kembali'])) / 86400); ?> hari.<br>
                Denda yang harus dibayar: <strong>Rp <?php echo number_format($denda, 0, ',', '.'); ?></strong>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Form Request Pengembalian</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Kondisi Buku <span class="text-danger">*</span></label>
                                <select name="kondisi_buku" class="form-select" required>
                                    <option value="baik">Baik (Tidak ada kerusakan)</option>
                                    <option value="rusak_ringan">Rusak Ringan (Lecet, lipatan kecil)</option>
                                    <option value="rusak_berat">Rusak Berat (Sobek, basah, halaman hilang)</option>
                                    <option value="hilang">Hilang</option>
                                </select>
                                <small class="text-muted">
                                    Pilih kondisi buku dengan jujur. Kerusakan atau kehilangan akan dikenakan biaya tambahan.
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="3" 
                                          placeholder="Jelaskan kondisi buku atau hal lain yang perlu disampaikan..."></textarea>
                            </div>
                            
                            <div class="alert alert-info">
                                <strong>ℹ️ Informasi:</strong><br>
                                • Request akan diproses oleh admin<br>
                                • Anda akan menerima notifikasi setelah admin memproses<br>
                                <?php if ($denda > 0): ?>
                                • Denda <strong>Rp <?php echo number_format($denda, 0, ',', '.'); ?></strong> harus dibayar saat pengembalian<br>
                                <?php endif; ?>
                                • Cek status di menu "Request Saya"
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    📤 Kirim Request
                                </button>
                                <a href="pinjamanku.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Detail Peminjaman</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($transaksi['cover_path']): ?>
                            <img src="<?php echo $base_url; ?>assets/images/buku/<?php echo $transaksi['cover_path']; ?>" 
                                 class="img-fluid mb-3" alt="Cover">
                        <?php endif; ?>
                        
                        <h6><?php echo $transaksi['judul']; ?></h6>
                        <p class="mb-1"><small class="text-muted">Pengarang: <?php echo $transaksi['pengarang']; ?></small></p>
                        <p class="mb-1"><small class="text-muted">ISBN: <?php echo $transaksi['isbn']; ?></small></p>
                        
                        <hr>
                        
                        <p class="mb-1"><strong>Tanggal Pinjam:</strong><br>
                            <?php echo formatTanggal($transaksi['tanggal_pinjam']); ?>
                        </p>
                        <p class="mb-1"><strong>Batas Kembali:</strong><br>
                            <span class="<?php echo $terlambat ? 'text-danger fw-bold' : ''; ?>">
                                <?php echo formatTanggal($transaksi['batas_kembali']); ?>
                            </span>
                        </p>
                        
                        <?php if ($denda > 0): ?>
                            <hr>
                            <p class="mb-0"><strong class="text-danger">Denda:</strong><br>
                                <span class="text-danger fw-bold fs-5">Rp <?php echo number_format($denda, 0, ',', '.'); ?></span>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>