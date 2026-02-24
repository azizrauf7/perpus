<?php
session_start();
require_once '../../koneksi.php';
require_once '../../includes/fungsi.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$title = 'Tambah Buku';
$base_url = '../../';
include '../../includes/header.php';

// Ambil data kategori
$kategori = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $isbn = clean($_POST['isbn']);
    $judul = clean($_POST['judul']);
    $pengarang = clean($_POST['pengarang']);
    $penerbit = clean($_POST['penerbit']);
    $tahun_terbit = clean($_POST['tahun_terbit']);
    $id_kategori = clean($_POST['id_kategori']);
    $stok_total = clean($_POST['stok_total']);
    $lokasi_rak = clean($_POST['lokasi_rak']);
    $deskripsi = clean($_POST['deskripsi']);
    $stok_tersedia = $stok_total;
    
    // Upload cover
    $cover_path = '';
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] == 0) {
        $upload_result = uploadGambar($_FILES['cover'], 'buku');
        if ($upload_result['status']) {
            $cover_path = $upload_result['filename'];
        } else {
            $error = $upload_result['message'];
        }
    }
    
    // Jika tidak ada error upload, lanjutkan insert
    if (!isset($error)) {
        $sql = "INSERT INTO buku (isbn, judul, pengarang, penerbit, tahun_terbit, id_kategori, stok_tersedia, stok_total, lokasi_rak, cover_path, deskripsi) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "sssssiiisss", $isbn, $judul, $pengarang, $penerbit, $tahun_terbit, $id_kategori, $stok_tersedia, $stok_total, $lokasi_rak, $cover_path, $deskripsi);
        
        if (mysqli_stmt_execute($stmt)) {
            logAktivitas($koneksi, 'tambah_buku', 'Menambah buku: ' . $judul, $_SESSION['user_id'], null);
            $_SESSION['success'] = 'Buku berhasil ditambahkan!';
            redirect('index.php');
        } else {
            $error = 'Gagal menambahkan buku!';
        }
    }
}
?>

<div class="d-flex">
    <?php include '../../includes/sidebar_admin.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Tambah Buku</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ISBN</label>
                            <input type="text" name="isbn" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Judul Buku <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pengarang <span class="text-danger">*</span></label>
                            <input type="text" name="pengarang" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Penerbit</label>
                            <input type="text" name="penerbit" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tahun Terbit</label>
                            <input type="number" name="tahun_terbit" class="form-control" min="1900" max="2099">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="id_kategori" class="form-select">
                                <option value="">-- Pilih Kategori --</option>
                                <?php while ($kat = mysqli_fetch_assoc($kategori)): ?>
                                    <option value="<?php echo $kat['id_kategori']; ?>"><?php echo $kat['nama_kategori']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stok Total <span class="text-danger">*</span></label>
                            <input type="number" name="stok_total" class="form-control" min="0" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lokasi Rak</label>
                            <input type="text" name="lokasi_rak" class="form-control" placeholder="Contoh: Rak-03-B">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cover Buku</label>
                            <input type="file" name="cover" class="form-control" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, GIF. Max: 5MB</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="4"></textarea>
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