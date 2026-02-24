<?php
require_once '../../koneksi.php';
require_once '../../includes/fungsi.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$title = 'Data Buku';
$base_url = '../../';
include '../../includes/header.php';

// Ambil data buku dengan kategori
$query = "SELECT b.*, k.nama_kategori 
          FROM buku b 
          LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
          ORDER BY b.created_at DESC";
$result = mysqli_query($koneksi, $query);
?>

<div class="d-flex">
    <?php include '../../includes/sidebar_admin.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Data Buku</h2>
            <a href="tambah.php" class="btn btn-primary">Tambah Buku</a>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php 
                echo $_SESSION['success']; 
                unset($_SESSION['success']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Cover</th>
                                <th>ISBN</th>
                                <th>Judul</th>
                                <th>Pengarang</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)): 
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <?php if ($row['cover_path']): ?>
                                        <img src="<?php echo $base_url; ?>assets/images/buku/<?php echo $row['cover_path']; ?>" 
                                             alt="Cover" style="width: 50px; height: 70px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 70px; font-size: 10px;">No Image</div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $row['isbn']; ?></td>
                                <td><?php echo $row['judul']; ?></td>
                                <td><?php echo $row['pengarang']; ?></td>
                                <td><?php echo $row['nama_kategori'] ?? '-'; ?></td>
                                <td>
                                    <span class="badge bg-info"><?php echo $row['stok_tersedia']; ?></span> / 
                                    <?php echo $row['stok_total']; ?>
                                </td>
                                <td>
                                    <a href="edit.php?id=<?php echo $row['id_buku']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="hapus.php?id=<?php echo $row['id_buku']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Yakin ingin menghapus buku ini?')">Hapus</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>