<?php
// Fungsi untuk membuat hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Fungsi untuk verifikasi password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Fungsi untuk cek login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fungsi untuk cek role admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

// Fungsi untuk cek role siswa
function isSiswa() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'siswa';
}

// Fungsi untuk redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Fungsi untuk format tanggal
function formatTanggal($tanggal) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

// Fungsi untuk hitung denda
function hitungDenda($tanggal_kembali, $batas_kembali) {
    $date1 = new DateTime($batas_kembali);
    $date2 = new DateTime($tanggal_kembali);
    $diff = $date1->diff($date2);
    
    if ($diff->days > 0 && $date2 > $date1) {
        return $diff->days * 1000; // Denda Rp 1000 per hari
    }
    return 0;
}

// Fungsi untuk upload gambar
function uploadGambar($file, $folder = 'buku') {
    // Gunakan absolute path dari document root
    $doc_root = $_SERVER['DOCUMENT_ROOT'];
    $base_path = str_replace('\\', '/', dirname(dirname(__FILE__))); // Path aplikasi
    
    // Buat path relatif dari document root
    $relative_path = str_replace($doc_root, '', $base_path);
    $target_dir = $doc_root . $relative_path . "/assets/images/$folder/";
    
    // Cek dan buat folder jika belum ada
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $newFileName = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $newFileName;
    
    // Cek apakah file adalah gambar
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return array('status' => false, 'message' => 'File bukan gambar');
    }
    
    // Cek ukuran file (max 5MB)
    if ($file["size"] > 5000000) {
        return array('status' => false, 'message' => 'Ukuran file terlalu besar');
    }
    
    // Cek format file
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        return array('status' => false, 'message' => 'Hanya file JPG, JPEG, PNG & GIF yang diizinkan');
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return array('status' => true, 'filename' => $newFileName);
    } else {
        return array('status' => false, 'message' => 'Gagal upload file: ' . $target_file);
    }
}

// Fungsi untuk sanitize input
function clean($data) {
    global $koneksi;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($koneksi, $data);
    return $data;
}

// Fungsi untuk log aktivitas
function logAktivitas($koneksi, $aktivitas, $deskripsi = '', $id_admin = null, $id_anggota = null) {
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
    $sql = "INSERT INTO log_aktivitas (id_admin, id_anggota, aktivitas, deskripsi, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "iissss", $id_admin, $id_anggota, $aktivitas, $deskripsi, $ip_address, $user_agent);
    mysqli_stmt_execute($stmt);
}
?>