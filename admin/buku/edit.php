<?php
session_start();
require_once '../../koneksi.php';
require_once '../../includes/fungsi.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$title = 'Edit Buku';
$base_url = '../../';
include '../../includes/header.php';

$id_buku = $_GET['id'] ?? 0;

// Ambil data buku
$query = mysqli_query($koneksi, "SELECT * FROM buku WHERE id_buku = $id_buku");
$buku = mysqli_fetch_assoc($query);

if (!$buku) {
    redirect('index.php');
}

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
    
    // Hitung stok tersedia berdasarkan perubahan stok total
    $selisih_stok = $stok_total - $buku['stok_total'];
    $stok_tersedia = $buku['stok_tersedia'] + $selisih_stok;
    
    // Upload cover jika ada
    $cover_path = $buku['cover_path'];
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] == 0) {
        $upload_result = uploadGambar($_FILES['cover'], 'buku');
        if ($upload_result['status']) {
            // Hapus cover lama jika ada
            if ($buku['cover_path'] && file_exists('../../assets/images/buku/' . $buku['cover_path'])) {
                unlink('../../assets/images/buku/' . $buku['cover_path']);
            }
            $cover_path = $upload_result['filename'];
        }
    }
    
    $sql = "UPDATE buku SET isbn = ?, judul = ?, pengarang = ?, penerbit = ?, tahun_terbit = ?, 
            id_kategori = ?, stok_tersedia = ?, stok_total = ?, lokasi_rak = ?, cover_path = ?, deskripsi = ? 
            WHERE id_buku = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "sssssiiisssi", $isbn, $judul, $pengarang, $penerbit, $tahun_terbit, 
                           $id_kategori, $stok_tersedia, $stok_total, $lokasi_rak, $cover_path, $deskripsi, $id_buku);
    
    if (mysqli_stmt_execute($stmt)) {
        logAktivitas($koneksi, 'edit_buku', 'Mengedit buku: ' . $judul, $_SESSION['user_id'], null);
        $_SESSION['success'] = 'Buku berhasil diupdate!';
        redirect('index.php');
    } else {
        $error = 'Gagal mengupdate buku!';
    }
}
?>

<div class="d-flex">
    <?php include '../../includes/sidebar_admin.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Edit Buku</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ISBN</label>
                            <input type="text" name="isbn" class="form-control" value="<?php echo $buku['isbn']; ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Judul Buku <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" value="<?php echo $buku['judul']; ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pengarang <span class="text-danger">*</span></label>
                            <input type="text" name="pengarang" class="form-control" value="<?php echo $buku['pengarang']; ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Penerbit</label>
                            <input type="text" name="penerbit" class="form-control" value="<?php echo $buku['penerbit']; ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tahun Terbit</label>
                            <input type="number" name="tahun_terbit" class="form-control" value="<?php echo $buku['tahun_terbit']; ?>" min="1900" max="2099">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="id_kategori" class="form-select">
                                <option value="">-- Pilih Kategori --</option>
                                <?php 
                                mysqli_data_seek($kategori, 0);
                                while ($kat = mysqli_fetch_assoc($kategori)): 
                                ?>
                                    <option value="<?php echo $kat['id_kategori']; ?>" 
                                        <?php echo ($kat['id_kategori'] == $buku['id_kategori']) ? 'selected' : ''; ?>>
                                        <?php echo $kat['nama_kategori']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stok Total <span class="text-danger">*</span></label>
                            <input type="number" name="stok_total" class="form-control" value="<?php echo $buku['stok_total']; ?>" min="<?php echo $buku['stok_total'] - $buku['stok_tersedia']; ?>" required>
                            <small class="text-muted">Tersedia: <?php echo $buku['stok_tersedia']; ?>, Dipinjam: <?php echo $buku['stok_total'] - $buku['stok_tersedia']; ?></small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lokasi Rak</label>
                            <input type="text" name="lokasi_rak" class="form-control" value="<?php echo $buku['lokasi_rak']; ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cover Buku</label>
                            <input type="file" name="cover" class="form-control" accept="image/*">
                            <?php if ($buku['cover_path']): ?>
                                <small class="text-muted">Cover saat ini:</small><br>
                                <img src="<?php echo $base_url; ?>assets/images/buku/<?php echo $buku['cover_path']; ?>" 
                                     alt="Cover" style="width: 100px; height: 140px; object-fit: cover; margin-top: 5px;">
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="4"><?php echo $buku['deskripsi']; ?></textarea>
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