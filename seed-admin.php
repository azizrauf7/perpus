<?php
require_once 'koneksi.php';
require_once 'includes/fungsi.php';

// Data admin default
$username = 'admin';
$password = hashPassword('admin123'); // Password: admin123
$nama_lengkap = 'Administrator';
$email = 'admin@perpustakaan.com';
$nohp = '081234567890';

// Cek apakah admin sudah ada
$check = mysqli_query($koneksi, "SELECT * FROM admin WHERE username = '$username'");

if (mysqli_num_rows($check) > 0) {
    echo "Admin sudah ada!";
} else {
    // Insert admin
    $sql = "INSERT INTO admin (username, password, nama_lengkap, email, nohp) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $username, $password, $nama_lengkap, $email, $nohp);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "Admin berhasil dibuat!<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
    } else {
        echo "Gagal membuat admin!";
    }
}

// Insert beberapa kategori
$kategori = [
    'Fiksi',
    'Non-Fiksi',
    'Sains',
    'Teknologi',
    'Sejarah',
    'Biografi',
    'Komik',
    'Novel'
];

foreach ($kategori as $kat) {
    $check_kat = mysqli_query($koneksi, "SELECT * FROM kategori WHERE nama_kategori = '$kat'");
    if (mysqli_num_rows($check_kat) == 0) {
        mysqli_query($koneksi, "INSERT INTO kategori (nama_kategori) VALUES ('$kat')");
        echo "Kategori '$kat' berhasil ditambahkan!<br>";
    }
}
?>