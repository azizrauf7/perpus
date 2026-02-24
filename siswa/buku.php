<?php
session_start();
require_once '../koneksi.php';
require_once '../includes/fungsi.php';

if (!isLoggedIn() || !isSiswa()) {
    redirect('../index.php');
}

$title = 'Katalog Buku';
$base_url = '../';
include '../includes/header.php';

// Filter
$search = $_GET['search'] ?? '';
$kategori_filter = $_GET['kategori'] ?? '';

$where = "WHERE b.stok_tersedia > 0";
if (!empty($search)) {
    $search = mysqli_real_escape_string($koneksi, $search);
    $where .= " AND (b.judul LIKE '%$search%' OR b.pengarang LIKE '%$search%' OR b.isbn LIKE '%$search%')";
}
if (!empty($kategori_filter)) {
    $where .= " AND b.id_kategori = $kategori_filter";
}

$buku = mysqli_query($koneksi, "
    SELECT b.*, k.nama_kategori 
    FROM buku b
    LEFT JOIN kategori k ON b.id_kategori = k.id_kategori
    $where
    ORDER BY b.judul ASC
");

// Ambil semua kategori
$kategori = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
?>

<div class="d-flex">
    <?php include '../includes/sidebar_siswa.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Katalog Buku</h2>
        
        <!-- Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Cari judul, pengarang, atau ISBN..." value="<?php echo $search; ?>">
                    </div>
                    <div class="col-md-4">
                        <select name="kategori" class="form-select">
                            <option value="">Semua Kategori</option>
                            <?php while ($k = mysqli_fetch_assoc($kategori)): ?>
                                <option value="<?php echo $k['id_kategori']; ?>" <?php echo $kategori_filter == $k['id_kategori'] ? 'selected' : ''; ?>>
                                    <?php echo $k['nama_kategori']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Cari</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Daftar Buku -->
        <div class="row">
            <?php if (mysqli_num_rows($buku) > 0): ?>
                <?php while ($b = mysqli_fetch_assoc($buku)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php if ($b['cover_path']): ?>
                            <img src="<?php echo $base_url; ?>assets/images/buku/<?php echo $b['cover_path']; ?>" 
                                 class="card-img-top" alt="Cover" style="height: 300px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                 style="height: 300px;">
                                <span>No Cover</span>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $b['judul']; ?></h5>
                            <p class="card-text mb-1"><small class="text-muted">Pengarang: <?php echo $b['pengarang']; ?></small></p>
                            <p class="card-text mb-1"><small class="text-muted">Penerbit: <?php echo $b['penerbit'] ?? '-'; ?></small></p>
                            <p class="card-text mb-1"><small class="text-muted">Tahun: <?php echo $b['tahun_terbit'] ?? '-'; ?></small></p>
                            <p class="card-text mb-1"><small class="text-muted">Kategori: <?php echo $b['nama_kategori'] ?? '-'; ?></small></p>
                            <p class="card-text mb-2">
                                <span class="badge bg-info">Stok Tersedia: <?php echo $b['stok_tersedia']; ?></span>
                            </p>
                            <?php if (!empty($b['deskripsi'])): ?>
                                <p class="card-text"><small><?php echo substr($b['deskripsi'], 0, 100) . '...'; ?></small></p>
                            <?php endif; ?>
                            
                            <a href="request-pinjam.php?id_buku=<?php echo $b['id_buku']; ?>" 
                               class="btn btn-primary btn-sm w-100 mt-2">
                                📚 Ajukan Peminjaman
                            </a>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">ISBN: <?php echo $b['isbn']; ?></small>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        Tidak ada buku yang ditemukan.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>