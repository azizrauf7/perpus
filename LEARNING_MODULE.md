# 🎓 MODUL BELAJAR CODING UNTUK PEMULA - Web Development Fundamentals

**Berdasarkan Project Perpustakaan Sekolah**

---

## DAFTAR ISI

1. [Persiapan & Setup](#1-persiapan--setup)
2. [PHP Basics](#2-php-basics)
3. [HTML & Form](#3-html--form)
4. [Database & MySQL](#4-database--mysql)
5. [Session & Authentication](#5-session--authentication)
6. [CRUD Operations](#6-crud-operations)
7. [File Upload](#7-file-upload)
8. [Security Basics](#8-security-basics)
9. [Error Handling](#9-error-handling)
10. [Mini Projects](#10-mini-projects)

---

## 1. PERSIAPAN & SETUP

### 1.1 Tools yang Dibutuhkan

Untuk belajar web development, Anda butuh:

1. **Editor Code**
   - VS Code (Recommended) - gratis, ringan, powerful
   - Sublime Text - ringan, simple
   - PHPStorm - professional tapi berbayar

2. **Local Server Environment**
   - XAMPP (Windows/Mac/Linux) - include Apache + MySQL + PHP
   - Download: https://www.apachefriends.org/

3. **Database Manager**
   - phpMyAdmin (included in XAMPP)
   - Atau: MySQL Workbench

4. **Browser**
   - Chrome, Firefox, atau Safari (untuk testing)

### 1.2 Setup XAMPP

```bash
# 1. Download & install XAMPP
# 2. Buka XAMPP Control Panel
# 3. Klik "Start" untuk Apache & MySQL
# 4. Tunggu sampai "Running"

# 5. Test di browser:
# http://localhost/
# Seharusnya muncul XAMPP home page

# 6. Buka phpmyadmin:
# http://localhost/phpmyadmin
```

### 1.3 Membuat Project Folder

```bash
# Project disimpan di:
# C:\xampp\htdocs\[nama-project]

# Contoh path lengkap:
# C:\xampp\htdocs\belajar-perpustakaan\

# Jika sudah, akses di browser:
# http://localhost/belajar-perpustakaan/
```

### 1.4 Testing Setup

Buat file `test.php`:

```php
<?php
echo "Hello, PHP World!";
?>
```

Akses di browser: `http://localhost/[project]/test.php`

Jika muncul "Hello, PHP World!" → Setup berhasil ✓

---

## 2. PHP BASICS

### 2.1 Apa itu PHP?

**PHP** = server-side scripting language

```
Browser Request (HTTP)
        ↓
   [Server PHP]  ← PHP code berjalan di sini, bukan di browser
        ↓
Browser Response (HTML)
```

**Contoh dari project:**
```php
<?php
// File: index.php (Login page)

// Cek apakah user sudah login
if (isLoggedIn()) {
    // Jika sudah → redirect ke dashboard
    redirect('admin/dashboard.php');
}

// Jika belum → tampilkan login form
echo "Silakan login";
?>
```

### 2.2 Syntax Dasar PHP

#### 2.2.1 Variables (Penyimpan Data)

```php
<?php
// String (text)
$nama = "Andi";
$judul_buku = "Harry Potter";

// Integer (angka)
$jumlah_buku = 50;
$tahun_terbit = 2025;

// Boolean (true/false)
$is_admin = true;
$status_aktif = false;

// Array (kumpulan data)
$list_buku = ["Harry Potter", "Laskar Pelangi", "Ayah"];
$buku = [
    "judul" => "Percy Jackson",
    "pengarang" => "Rick Riordan",
    "tahun" => 2010
];

// Display variable
echo $nama;              // Output: Andi
echo $jumlah_buku * 2;  // Output: 100
?>
```

**Best Practice dari project:**
```php
<?php
// ✓ Good naming
$nama_lengkap = "Budi Santoso";
$id_anggota = 5;
$status_aktif = true;

// ❌ Bad naming (avoid)
$n = "Budi";
$x = 5;
$a = true;
?>
```

#### 2.2.2 Data Types

| Type | Example | Used for |
|------|---------|----------|
| String | `"Hello"`, `'World'` | Text, names |
| Integer | `42`, `-10`, `0` | Numbers, counts, IDs |
| Float | `3.14`, `10.5` | Decimal numbers, prices |
| Boolean | `true`, `false` | Yes/no conditions |
| Array | `[1, 2, 3]` | Collections |
| Null | `null` | Empty/undefined |

```php
<?php
// Type checking
var_dump("Hello");      // string(5) "Hello"
var_dump(42);           // int(42)
var_dump(3.14);         // float(3.14)
var_dump(true);         // bool(true)
var_dump([1, 2, 3]);    // array(3) { [0]=>1 [1]=>2 [2]=>3 }
?>
```

#### 2.2.3 String Operations (Manipulasi Text)

```php
<?php
$nama = "Budi";
$kota = "Jakarta";

// Concatenation (menggabung string)
$kalimat = $nama . " dari " . $kota;
echo $kalimat;  // Output: Budi dari Jakarta

// String interpolation (dengan kutip ganda)
echo "Nama saya: $nama";           // Output: Nama saya: Budi
echo "Nama saya: " . $nama . "";   // Sama seperti di atas

// String functions (dari fungsi.php di project)
strlen($nama);           // Panjang string: 4
strtoupper($nama);       // Uppercase: BUDI
strtolower($nama);       // Lowercase: budi
trim("  hello  ");       // Hapus whitespace: "hello"
htmlspecialchars("<p>"); // Escape HTML: "&lt;p&gt;"
?>
```

**Real example dari project:**
```php
<?php
// Dari: includes/fungsi.php
function clean($data) {
    $data = trim($data);              // Hapus spasi
    $data = stripslashes($data);      // Hapus backslash
    $data = htmlspecialchars($data);  // Escape HTML
    return $data;
}

// Penggunaan:
$username = clean($_POST['username']);  // "budi" → "budi" (bersih)
?>
```

#### 2.2.4 Array Operations

```php
<?php
// Array indexed (urutan)
$buku = ["Harry Potter", "Laskar Pelangi", "Ayah"];
echo $buku[0];      // Output: Harry Potter
echo $buku[1];      // Output: Laskar Pelangi

// Array associative (key -> value)
$siswa = [
    "nama" => "Budi",
    "kelas" => "XII RPL 1",
    "nis" => "2021001"
];
echo $siswa["nama"];  // Output: Budi

// Loop array dengan foreach
foreach ($buku as $judul) {
    echo $judul . ", ";
}
// Output: Harry Potter, Laskar Pelangi, Ayah,

// Loop dengan key
foreach ($siswa as $key => $value) {
    echo "$key: $value\n";
}
// Output:
// nama: Budi
// kelas: XII RPL 1
// nis: 2021001

// Array functions
count($buku);           // Jumlah items: 3
array_push($buku, "Seri A");  // Tambah item di akhir
in_array("Ayah", $buku);      // Cek ada atau tidak: true
array_keys($siswa);     // Ambil semua keys: ["nama", "kelas", "nis"]
array_values($siswa);   // Ambil semua values: ["Budi", "XII RPL 1", "2021001"]
?>
```

**Real example dari project:**
```php
<?php
// Dari: siswa/request-saya.php
// Fetch data dari database (return array)
$request = mysqli_fetch_assoc($result_query);

// Akses data dari array
echo $request['tanggal_request'];  // Ambil tanggal
echo $request['status'];           // Ambil status (pending/approved/rejected)

// Loop multiple records
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['nama'] . " - " . $row['status'] . "<br>";
}
?>
```

### 2.3 Control Flow (If, Loop)

#### 2.3.1 Conditional (If/Else)

```php
<?php
$nilai = 85;
$status = "siswa";

// If statement
if ($nilai >= 85) {
    echo "Nilai Anda: A";
}

// If-else
if ($nilai >= 80) {
    echo "Grade: A";
} else {
    echo "Grade: B";
}

// If-else if-else
if ($nilai >= 90) {
    echo "Grade: A+";
} elseif ($nilai >= 85) {
    echo "Grade: A";
} elseif ($nilai >= 80) {
    echo "Grade: B+";
} else {
    echo "Grade: C";
}

// Ternary operator (one-liner)
$result = ($nilai >= 80) ? "Lulus" : "Tidak Lulus";

// Logical operators
if ($nilai >= 80 AND $status == "siswa") {
    echo "Siswa dengan nilai bagus";
}

if ($nilai >= 80 OR $status == "admin") {
    echo "Eligible";
}

if (!($status == "nonaktif")) {
    echo "Status aktif";
}
?>
```

**Real example dari project:**
```php
<?php
// Dari: admin/dashboard.php

// Conditional rendering
if ($total_request_pinjam > 0) {
    echo "Ada $total_request_pinjam request menunggu";
}

// Badge class based on status
if ($row['status'] == 'dipinjam') {
    $badge_class = strtotime($row['batas_kembali']) < time() ? 'danger' : 'warning';
} elseif ($row['status'] == 'dikembalikan') {
    $badge_class = 'success';
}
echo "<span class='badge bg-$badge_class'>$row['status']</span>";
?>
```

#### 2.3.2 Loops

```php
<?php
// While loop
$i = 1;
while ($i <= 5) {
    echo "Nomor: $i\n";
    $i++;
}
// Output: Nomor: 1, Nomor: 2, ... Nomor: 5

// For loop
for ($i = 1; $i <= 5; $i++) {
    echo "Nomor: $i\n";
}

// Foreach (untuk array)
$bulan = ["Januari", "Februari", "Maret"];
foreach ($bulan as $nama_bulan) {
    echo $nama_bulan . "\n";
}

// Break (stop loop)
for ($i = 1; $i <= 10; $i++) {
    if ($i == 3) break;  // Stop saat i=3
    echo "$i ";
}
// Output: 1 2

// Continue (skip iterasi)
for ($i = 1; $i <= 5; $i++) {
    if ($i == 3) continue;  // Skip saat i=3
    echo "$i ";
}
// Output: 1 2 4 5
?>
```

**Real example dari project:**
```php
<?php
// Dari: admin/anggota/index.php

// Loop hasil query database
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['nama'] . "</td>";
    echo "<td>" . $row['kelas_jurusan'] . "</td>";
    echo "<td>" . $row['nohp'] . "</td>";
    echo "</tr>";
}
?>
```

### 2.4 Functions (Fungsi)

Function adalah blok code yang bisa di-reuse.

```php
<?php
// Define function
function salam($nama) {
    echo "Halo, " . $nama;
}

// Call function
salam("Budi");  // Output: Halo, Budi
salam("Ani");   // Output: Halo, Ani

// Function with return value
function hitung_denda($hari_terlambat) {
    $denda = $hari_terlambat * 1000;
    return $denda;
}

$hasil = hitung_denda(5);
echo "Denda Anda: Rp " . $hasil;  // Output: Denda Anda: Rp 5000

// Function dengan multiple parameters
function pinjam_buku($nama_siswa, $judul_buku, $durasi_hari) {
    echo "$nama_siswa meminjam $judul_buku selama $durasi_hari hari";
}

pinjam_buku("Budi", "Harry Potter", 7);
// Output: Budi meminjam Harry Potter selama 7 hari

// Function dengan default parameter
function format_harga($harga, $mata_uang = "Rp") {
    return $mata_uang . number_format($harga, 0, ',', '.');
}

echo format_harga(100000);           // Output: Rp 100.000
echo format_harga(100000, "USD");    // Output: USD 100.000

// Scope: global vs local
$uang_global = 1000;

function beli() {
    global $uang_global;  // Akses variable luar
    $uang_global -= 100;
}

beli();
echo $uang_global;  // Output: 900
?>
```

**Real example dari project:**
```php
<?php
// Dari: includes/fungsi.php

// Helper function untuk hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Helper untuk verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Helper untuk format tanggal
function formatTanggal($tanggal) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

// Penggunaan
$tanggal = "2025-02-20";
echo formatTanggal($tanggal);  // Output: 20 Februari 2025

// Helper untuk redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Helper untuk cek login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>
```

---

## 3. HTML & FORM

### 3.1 Basic HTML Structure

HTML adalah struktur halaman web. PHP menghasilkan HTML, lalu dikirim ke browser.

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Halaman Saya</title>
    <style>
        body { font-family: Arial; }
    </style>
</head>
<body>
    <h1>Selamat Datang</h1>
    <p>Ini adalah halaman web saya</p>
</body>
</html>
```

### 3.2 Forms (Input User)

Form digunakan untuk menerima input dari user.

```html
<form action="proses.php" method="POST">
    <!-- Text input -->
    <label for="nama">Nama:</label>
    <input type="text" id="nama" name="nama" required>
    
    <!-- Password input -->
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    
    <!-- Email input -->
    <label for="email">Email:</label>
    <input type="email" id="email" name="email">
    
    <!-- Select dropdown -->
    <label for="role">Pilih Role:</label>
    <select id="role" name="role" required>
        <option value="">-- Pilih --</option>
        <option value="admin">Admin</option>
        <option value="siswa">Siswa</option>
    </select>
    
    <!-- Radio buttons -->
    <label>Jenis Kelamin:</label>
    <input type="radio" name="jk" value="L"> Laki-laki
    <input type="radio" name="jk" value="P"> Perempuan
    
    <!-- Checkbox -->
    <label>Setuju dengan syarat & ketentuan:</label>
    <input type="checkbox" name="agree" value="yes"> Saya setuju
    
    <!-- Textarea -->
    <label for="keterangan">Keterangan:</label>
    <textarea id="keterangan" name="keterangan" rows="4"></textarea>
    
    <!-- Submit button -->
    <button type="submit">Kirim</button>
</form>
```

### 3.3 Processing Form with PHP

**File: proses.php**

```php
<?php
// Cek form dikrimm dengan method POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama = $_POST['nama'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $jk = $_POST['jk'];
    $keterangan = $_POST['keterangan'];
    
    // Validasi
    if (empty($nama)) {
        echo "Nama tidak boleh kosong!";
    } elseif (strlen($nama) < 3) {
        echo "Nama minimal 3 karakter!";
    } elseif (strlen($password) < 8) {
        echo "Password minimal 8 karakter!";
    } else {
        // Data valid → process
        echo "Data diterima!<br>";
        echo "Nama: " . $nama . "<br>";
        echo "Email: " . $email . "<br>";
        echo "Role: " . $role . "<br>";
    }
} else {
    // Jika tidak POST request, arahkan ke form
    header('Location: form.html');
}
?>
```

**Real example dari project:**
```php
<?php
// Dari: login-proses.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean($_POST['username']);  // Sanitize
    $password = $_POST['password'];
    $role = clean($_POST['role']);
    
    if ($role == 'admin') {
        // Query database
        $sql = "SELECT * FROM admin WHERE username = ? AND is_active = 1";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            // Verify password
            if (verifyPassword($password, $row['password'])) {
                // Set session & redirect
                $_SESSION['user_id'] = $row['id_admin'];
                $_SESSION['role'] = 'admin';
                redirect('admin/dashboard.php');
            } else {
                $_SESSION['error'] = 'Password salah!';
                redirect('index.php');
            }
        } else {
            $_SESSION['error'] = 'Username tidak ditemukan!';
            redirect('index.php');
        }
    }
}
?>
```

---

## 4. DATABASE & MYSQL

### 4.1 Apa itu Database?

Database adalah tempat menyimpan data terstruktur.

```
Spreadsheet approach (manual)     Database approach (efficient)
┌─────────────────────┐          ┌──────────────────┐
│ ID │ Nama │ Kelas  │          │  buku table      │
├─────────────────────┤          ├──────────────────┤
│ 1  │ Budi │ 12-A   │          │ Auto-indexed     │
│ 2  │ Ani  │ 12-B   │          │ Secure           │
│ 3  │ Cici │ 12-A   │          │ Fast queries     │
└─────────────────────┘          │ Multiple users   │
                                 └──────────────────┘
```

### 4.2 Creating Database & Tables

**Di phpMyAdmin:**

1. Buka http://localhost/phpmyadmin
2. Klik "New" → Create database → nama: `db_perpustakaan`
3. Klik database yang baru
4. Klik "SQL" → paste SQL:

```sql
CREATE TABLE buku (
    id_buku INT PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(150) NOT NULL,
    pengarang VARCHAR(100),
    stok INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE anggota (
    id_anggota INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE transaksi (
    id_transaksi INT PRIMARY KEY AUTO_INCREMENT,
    id_anggota INT NOT NULL,
    id_buku INT NOT NULL,
    tanggal_pinjam DATE,
    batas_kembali DATE,
    status ENUM('dipinjam', 'dikembalikan') DEFAULT 'dipinjam',
    FOREIGN KEY (id_anggota) REFERENCES anggota(id_anggota),
    FOREIGN KEY (id_buku) REFERENCES buku(id_buku)
);
```

### 4.3 Database Connection

**File: koneksi.php**

```php
<?php
// Database credentials
$host = 'localhost';
$user = 'root';
$pass = '';          // Default XAMPP password kosong
$db   = 'db_perpustakaan';

// Connect
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set character set to UTF-8
mysqli_set_charset($koneksi, "utf8");

// Usage di file lain:
require_once 'koneksi.php';  // Include di awal
?>
```

### 4.4 Basic Database Operations

#### 4.4.1 SELECT (Baca Data)

```php
<?php
$koneksi = mysqli_connect('localhost', 'root', '', 'db_perpustakaan');

// SELECT semua buku
$query = "SELECT * FROM buku";
$result = mysqli_query($koneksi, $query);

// Fetch one row
$buku = mysqli_fetch_assoc($result);  // Ambil 1 baris
echo $buku['judul'];

// Fetch all rows
$query = "SELECT * FROM buku ORDER BY created_at DESC";
$result = mysqli_query($koneksi, $query);

while ($row = mysqli_fetch_assoc($result)) {
    echo $row['judul'] . " - " . $row['pengarang'] . "<br>";
}

// Count results
$query = "SELECT COUNT(*) as total FROM buku";
$result = mysqli_fetch_assoc(mysqli_query($koneksi, $query));
echo "Total buku: " . $result['total'];

// SELECT dengan kondisi
$query = "SELECT * FROM buku WHERE stok > 0";
$result = mysqli_query($koneksi, $query);

// SELECT dengan JOIN (menggabung tabel)
$query = "
    SELECT t.*, a.nama as nama_anggota, b.judul as judul_buku
    FROM transaksi t
    JOIN anggota a ON t.id_anggota = a.id_anggota
    JOIN buku b ON t.id_buku = b.id_buku
    WHERE t.status = 'dipinjam'
";
$result = mysqli_query($koneksi, $query);
?>
```

**Real example dari project:**
```php
<?php
// Dari: admin/dashboard.php

// Get statistics
$total_buku = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT COUNT(*) as total FROM buku")
)['total'];

$total_dipinjam = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT COUNT(*) as total FROM transaksi WHERE status = 'dipinjam'")
)['total'];

echo "Total buku: $total_buku";
echo "Sedang dipinjam: $total_dipinjam";
?>
```

#### 4.4.2 INSERT (Tambah Data)

```php
<?php
$koneksi = mysqli_connect('localhost', 'root', '', 'db_perpustakaan');

// Simple INSERT
$judul = "Harry Potter";
$pengarang = "J.K. Rowling";
$stok = 5;

$query = "
    INSERT INTO buku (judul, pengarang, stok)
    VALUES ('$judul', '$pengarang', $stok)
";

if (mysqli_query($koneksi, $query)) {
    echo "Buku berhasil ditambahkan!";
    $id_buku = mysqli_insert_id($koneksi);  // Ambil ID yg baru
} else {
    echo "Error: " . mysqli_error($koneksi);
}

// ❌ RAWAN SQL INJECTION - JANGAN PAKAI!
// ✓ GUNAKAN PREPARED STATEMENT INI:

$sql = "INSERT INTO buku (judul, pengarang, stok) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "ssi", $judul, $pengarang, $stok);

if (mysqli_stmt_execute($stmt)) {
    echo "Buku berhasil ditambahkan!";
} else {
    echo "Error: " . mysqli_stmt_error($stmt);
}
?>
```

**Real example dari project:**
```php
<?php
// Dari: admin/buku/tambah.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = clean($_POST['judul']);
    $pengarang = clean($_POST['pengarang']);
    $stok = intval($_POST['stok']);
    
    $sql = "INSERT INTO buku (judul, pengarang, stok) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $judul, $pengarang, $stok);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = 'Buku berhasil ditambahkan!';
        redirect('index.php');
    } else {
        echo "Error: " . mysqli_stmt_error($stmt);
    }
}
?>
```

#### 4.4.3 UPDATE (Ubah Data)

```php
<?php
// Update stok buku
$id_buku = 1;
$stok_baru = 10;

$sql = "UPDATE buku SET stok = ? WHERE id_buku = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "ii", $stok_baru, $id_buku);

if (mysqli_stmt_execute($stmt)) {
    echo "Stok berhasil diupdate!";
} else {
    echo "Error: " . mysqli_stmt_error($stmt);
}

// Update multiple columns
$sql = "
    UPDATE buku 
    SET judul = ?, pengarang = ?, stok = ?
    WHERE id_buku = ?
";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "ssii", $judul, $pengarang, $stok, $id_buku);
mysqli_stmt_execute($stmt);
?>
```

#### 4.4.4 DELETE (Hapus Data)

```php
<?php
$id_buku = 1;

$sql = "DELETE FROM buku WHERE id_buku = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_buku);

if (mysqli_stmt_execute($stmt)) {
    echo "Buku berhasil dihapus!";
} else {
    echo "Error: " . mysqli_stmt_error($stmt);
}

// DELETE dengan kondisi
$sql = "DELETE FROM transaksi WHERE status = 'selesai'";
mysqli_query($koneksi, $sql);
?>
```

---

## 5. SESSION & AUTHENTICATION

### 5.1 Apa itu Session?

Session menyimpan data user selama mereka logged in.

```
Browser                 Server
─────────────────────────────────
User login
   │
   ├─> username+password
         │
         └─> Compare dengan database
               │
               └─> Cocok → Set $_SESSION → Send Session ID
   │
   ├─ Terima Session ID (simpan di cookie)
         │
         ├─> GET /siswa/dashboard.php
         │   (kirim session ID)
         │       │
         │       ├─> Check $_SESSION['user_id'] → Ada
         │       │   → Tampilkan dashboard
         │       └─ Response: Dashboard HTML
   │
   └─ $_SESSION tetap hidup selama session belum di-destroy
```

### 5.2 Starting & Using Session

```php
<?php
// SELALU di awal file (sebelum output apapun)
if (session_status() === PHP_SESSION_NONE) {
    session_start();  // Start session
}

// Set session pada login
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'budi';
$_SESSION['nama'] = 'Budi Santoso';
$_SESSION['role'] = 'admin';

// Cek session
if (isset($_SESSION['user_id'])) {
    echo "User: " . $_SESSION['nama'];
} else {
    echo "Belum login";
}

// Unset session specific
unset($_SESSION['user_id']);

// Destroy entire session (logout)
session_destroy();

// Cek session baru
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');  // Redirect ke login
}
?>
```

**Real example dari project:**
```php
<?php
// Dari: index.php (login page)

if (isLoggedIn()) {
    // User sudah login → arahkan ke dashboard
    if (isAdmin()) {
        redirect('admin/dashboard.php');
    } else {
        redirect('siswa/dashboard.php');
    }
}

// User belum login → tampilkan login form
?>

<!-- HTML form -->
<form action="login-proses.php" method="POST">
    <input type="text" name="username">
    <input type="password" name="password">
    <select name="role">
        <option value="admin">Admin</option>
        <option value="siswa">Siswa</option>
    </select>
    <button type="submit">Login</button>
</form>
```

### 5.3 Authentication Flow (Login Process)

```php
<?php
// Dari: login-proses.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean($_POST['username']);
    $password = $_POST['password'];
    $role = clean($_POST['role']);
    
    // 1. Cek di database sesuai role
    if ($role == 'admin') {
        $sql = "SELECT * FROM admin WHERE username = ? AND is_active = 1";
    } else {
        $sql = "SELECT * FROM anggota WHERE username = ? AND status_aktif = 1";
    }
    
    // 2. Prepare statement (prevent SQL injection)
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    // 3. Check jika ada record
    if ($row = mysqli_fetch_assoc($result)) {
        // 4. Verify password (bcrypt)
        if (verifyPassword($password, $row['password'])) {
            // 5. Password cocok → set session
            $_SESSION['user_id'] = $row['id_admin'];  // or id_anggota
            $_SESSION['username'] = $row['username'];
            $_SESSION['nama'] = $row['nama_lengkap']; // or nama
            $_SESSION['role'] = $role;
            
            // 6. Log activity
            logAktivitas($koneksi, 'login', 'User login', null, null);
            
            // 7. Redirect ke dashboard
            redirect('admin/dashboard.php');
        } else {
            // Password salah
            $_SESSION['error'] = 'Password salah!';
            redirect('index.php');
        }
    } else {
        // User tidak ketemu
        $_SESSION['error'] = 'Username tidak ditemukan!';
        redirect('index.php');
    }
}
?>
```

### 5.4 Authorization Check

Setiap halaman protected harus check session:

```php
<?php
// Dari: admin/dashboard.php

// Include koneksi + fungsi
require_once '../koneksi.php';
require_once '../includes/fungsi.php';

// 1. Cek apakah user login
if (!isLoggedIn()) {
    redirect('../index.php');  // Belum login → arahkan ke login
}

// 2. Cek apakah role = admin
if (!isAdmin()) {
    redirect('../index.php');  // Bukan admin → arahkan keluar
}

// Jika sampai sini → user adalah admin yang valid
// Tampilkan dashboard
include '../includes/header.php';
echo "Dashboard Admin";
?>
```

---

## 6. CRUD OPERATIONS

CRUD = Create, Read, Update, Delete

Ini adalah operasi dasar pada database.

### 6.1 Project Structure untuk CRUD

```
admin/buku/
├── index.php    ← READ (list semua buku)
├── tambah.php   ← CREATE (form + process)
├── edit.php     ← UPDATE (form + process)
└── hapus.php    ← DELETE (process)
```

### 6.2 READ - List Page

**File: admin/buku/index.php**

```php
<?php
require_once '../../koneksi.php';
require_once '../../includes/fungsi.php';

// Auth check
if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

// Fetch data
$query = "
    SELECT * FROM buku
    ORDER BY created_at DESC
";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($koneksi));
}

include '../../includes/header.php';
?>

<div class="container">
    <h2>Data Buku</h2>
    <a href="tambah.php" class="btn btn-primary">Tambah Buku</a>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Judul</th>
                <th>Pengarang</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['id_buku']; ?></td>
                <td><?php echo $row['judul']; ?></td>
                <td><?php echo $row['pengarang']; ?></td>
                <td><?php echo $row['stok']; ?></td>
                <td>
                    <a href="edit.php?id=<?php echo $row['id_buku']; ?>" 
                       class="btn btn-sm btn-warning">Edit</a>
                    <a href="hapus.php?id=<?php echo $row['id_buku']; ?>"
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Yakin?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/footer.php'; ?>
```

### 6.3 CREATE - Add Page

**File: admin/buku/tambah.php**

```php
<?php
require_once '../../koneksi.php';
require_once '../../includes/fungsi.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$error = '';

// Process form jika POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = clean($_POST['judul']);
    $pengarang = clean($_POST['pengarang']);
    $stok = intval($_POST['stok']);
    
    // Validasi
    if (empty($judul)) {
        $error = "Judul tidak boleh kosong!";
    } elseif (strlen($judul) < 3) {
        $error = "Judul minimal 3 karakter!";
    } elseif ($stok < 0) {
        $error = "Stok tidak boleh negatif!";
    } else {
        // Insert ke database
        $sql = "INSERT INTO buku (judul, pengarang, stok) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $judul, $pengarang, $stok);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = 'Buku berhasil ditambahkan!';
            redirect('index.php');
        } else {
            $error = "Error: " . mysqli_stmt_error($stmt);
        }
    }
}

include '../../includes/header.php';
?>

<div class="container">
    <h2>Tambah Buku</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" style="max-width: 500px;">
        <div class="mb-3">
            <label>Judul Buku:</label>
            <input type="text" name="judul" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label>Pengarang:</label>
            <input type="text" name="pengarang" class="form-control">
        </div>
        
        <div class="mb-3">
            <label>Stok:</label>
            <input type="number" name="stok" class="form-control" value="0">
        </div>
        
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>
```

### 6.4 UPDATE - Edit Page

**File: admin/buku/edit.php**

```php
<?php
require_once '../../koneksi.php';
require_once '../../includes/fungsi.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$error = '';
$id_buku = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch data to populate form
$sql = "SELECT * FROM buku WHERE id_buku = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_buku);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$buku = mysqli_fetch_assoc($result);

if (!$buku) {
    $_SESSION['error'] = 'Buku tidak ditemukan!';
    redirect('index.php');
}

// Process form jika POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = clean($_POST['judul']);
    $pengarang = clean($_POST['pengarang']);
    $stok = intval($_POST['stok']);
    
    if (empty($judul)) {
        $error = "Judul tidak boleh kosong!";
    } else {
        // Update database
        $sql = "UPDATE buku SET judul = ?, pengarang = ?, stok = ? WHERE id_buku = ?";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "ssii", $judul, $pengarang, $stok, $id_buku);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = 'Buku berhasil diupdate!';
            redirect('index.php');
        } else {
            $error = "Error: " . mysqli_stmt_error($stmt);
        }
    }
}

include '../../includes/header.php';
?>

<div class="container">
    <h2>Edit Buku</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" style="max-width: 500px;">
        <div class="mb-3">
            <label>Judul Buku:</label>
            <input type="text" name="judul" class="form-control" 
                   value="<?php echo $buku['judul']; ?>" required>
        </div>
        
        <div class="mb-3">
            <label>Pengarang:</label>
            <input type="text" name="pengarang" class="form-control"
                   value="<?php echo $buku['pengarang']; ?>">
        </div>
        
        <div class="mb-3">
            <label>Stok:</label>
            <input type="number" name="stok" class="form-control"
                   value="<?php echo $buku['stok']; ?>">
        </div>
        
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>
```

### 6.5 DELETE - Delete Page

**File: admin/buku/hapus.php**

```php
<?php
require_once '../../koneksi.php';
require_once '../../includes/fungsi.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$id_buku = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check jika buku ada
$sql = "SELECT * FROM buku WHERE id_buku = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_buku);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$buku = mysqli_fetch_assoc($result);

if (!$buku) {
    $_SESSION['error'] = 'Buku tidak ditemukan!';
    redirect('index.php');
}

// Delete
$sql = "DELETE FROM buku WHERE id_buku = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_buku);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = 'Buku berhasil dihapus!';
} else {
    $_SESSION['error'] = 'Gagal menghapus buku!';
}

redirect('index.php');
?>
```

---

## 7. FILE UPLOAD

Fitur untuk upload file (gambar buku, dokumen, dll).

### 7.1 File Upload Form

```html
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="file_upload" accept="image/*" required>
    <button type="submit">Upload</button>
</form>
```

**Key points:**
- `enctype="multipart/form-data"` - WAJIB untuk file upload!
- `accept="image/*"` - Hanya terima gambar
- Input type `file`

### 7.2 File Upload Processing

```php
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $file = $_FILES['file_upload'];  // Akses uploaded file
    
    // File info
    $nama_file = $file['name'];          // "gambar.jpg"
    $tmp_path = $file['tmp_name'];       // Lokasi temporary
    $file_size = $file['size'];          // Ukuran dalam bytes
    $file_type = $file['type'];          // "image/jpeg"
    $error_code = $file['error'];        // Error code (0 = no error)
    
    // 1. Cek error
    if ($error_code !== UPLOAD_ERR_OK) {
        echo "Error uploading file!";
        exit();
    }
    
    // 2. Validasi size (max 5MB)
    $max_size = 5 * 1024 * 1024;  // 5MB
    if ($file_size > $max_size) {
        echo "File terlalu besar! Max 5MB";
        exit();
    }
    
    // 3. Validasi format
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file_type, $allowed_types)) {
        echo "Format file tidak diizinkan!";
        exit();
    }
    
    // 4. Validasi dengan getimagesize()
    if (getimagesize($tmp_path) === false) {
        echo "File bukan gambar valid!";
        exit();
    }
    
    // 5. Generate unique filename
    $extension = pathinfo($nama_file, PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $extension;  // "60a4b3c1f.jpg"
    
    // 6. Set upload path
    $upload_dir = '../assets/images/buku/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $upload_path = $upload_dir . $new_filename;
    
    // 7. Move file dari tmp ke permanent location
    if (move_uploaded_file($tmp_path, $upload_path)) {
        echo "File berhasil diupload: " . $new_filename;
        
        // 8. Save filename to database
        // $sql = "INSERT INTO buku (gambar) VALUES (?)";
    } else {
        echo "Gagal mengupload file!";
    }
}
?>
```

**Real example dari project:**
```php
<?php
// Dari: includes/fungsi.php

function uploadGambar($file, $folder = 'buku') {
    // Validate
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return array('status' => false, 'message' => 'File bukan gambar');
    }
    
    // Check size
    if ($file["size"] > 5000000) {
        return array('status' => false, 'message' => 'Ukuran file terlalu besar');
    }
    
    // Check format
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        return array('status' => false, 'message' => 'Format tidak diizinkan');
    }
    
    // Generate unique name
    $newFileName = uniqid() . '.' . $imageFileType;
    $target_file = "../../assets/images/$folder/" . $newFileName;
    
    // Move file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return array('status' => true, 'filename' => $newFileName);
    } else {
        return array('status' => false, 'message' => 'Gagal upload');
    }
}

// Usage
if ($_FILES['gambar']) {
    $result = uploadGambar($_FILES['gambar']);
    if ($result['status']) {
        $filename = $result['filename'];
        // Save ke database
    } else {
        echo $result['message'];
    }
}
?>
```

---

## 8. SECURITY BASICS

### 8.1 SQL Injection & Prevention

**SQL Injection** = Input user digunakan langsung di SQL query.

```php
// ❌ VULNERABLE
$username = $_POST['username'];
$query = "SELECT * FROM users WHERE username = '$username'";

// Jika user input: admin' OR '1'='1
// Query menjadi: SELECT * FROM users WHERE username = 'admin' OR '1'='1'
// Selalu TRUE → ambil semua users!
```

**Solution: Prepared Statements**

```php
// ✓ SAFE
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// User input tidak dianggap SQL code, hanya data
```

### 8.2 XSS (Cross-Site Scripting)

Attacker inject JavaScript ke halaman Anda.

```php
// ❌ VULNERABLE
echo "Halo " . $_POST['nama'];

// Jika user input: <script>alert('hacked')</script>
// Script akan dijalankan!

// ✓ SAFE - Escape HTML
echo "Halo " . htmlspecialchars($_POST['nama']);

// Output: Halo &lt;script&gt;alert('hacked')&lt;/script&gt;
// Script tidak jalan
```

### 8.3 Password Storage

**❌ JANGAN:**
```php
$password = $_POST['password'];
// Simpan langsung ke database - RAWAN!
```

**✓ GUNAKAN HASHING:**
```php
// Store password
$hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
// Simpan $hash ke database

// Verify password
$password = $_POST['password'];
if (password_verify($password, $hash_dari_db)) {
    // Password cocok
}
```

### 8.4 CSRF (Cross-Site Request Forgery)

Attacker buat action (approve, delete, dll) tanpa izin user.

**❌ VULNERABLE:**
```html
<a href="approve.php?id=5">Approve</a>
<!-- Attacker buat link: -->
<!-- <a href="approve.php?id=5">Click me!</a> -->
<!-- User click → action jalan tanpa tahu -->
```

**✓ SAFE - Gunakan Token:**
```php
// Generate token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Form
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <button type="submit">Approve</button>
</form>

// Verify
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF token invalid');
}
```

### 8.5 Input Validation

Validasi INPUT sebelum pakai:

```php
<?php
// Username: 3-20 characters, alphanumeric + underscore
if (strlen($username) < 3 || strlen($username) > 20) {
    die('Username harus 3-20 karakter');
}
if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    die('Username hanya boleh alfanumerik & underscore');
}

// Email: valid format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Email tidak valid');
}

// Number: harus angka positif
if (!is_numeric($stok) || $stok < 0) {
    die('Stok harus angka positif');
}

// String: harus ada isinya
if (empty(trim($judul))) {
    die('Judul tidak boleh kosong');
}
?>
```

---

## 9. ERROR HANDLING

### 9.1 Basic Error Handling

```php
<?php
// Try-catch (untuk exception)
try {
    if (!$koneksi) {
        throw new Exception("Database connection failed");
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    // Log to file
    error_log($e->getMessage());
}

// Check function success
$result = mysqli_query($koneksi, $query);
if (!$result) {
    die("Query error: " . mysqli_error($koneksi));
}

// Check file operations
if (!file_exists($file)) {
    echo "File tidak ditemukan!";
}
?>
```

### 9.2 User-Friendly Error Messages

```php
<?php
// ❌ Technical error (confusing)
echo mysqli_error($koneksi);
// Output: Duplicate entry '123' for key 'PRIMARY'

// ✓ User-friendly
if (strpos(mysqli_error($koneksi), 'Duplicate') !== false) {
    $_SESSION['error'] = 'Username sudah terdaftar!';
} else {
    $_SESSION['error'] = 'Terjadi kesalahan! Silakan coba lagi.';
}
?>
```

### 9.3 Logging Errors

```php
<?php
// Log error ke file
error_log("Database error: " . mysqli_error($koneksi), 3, "logs/errors.log");

// Custom logging function
function logError($message) {
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] $message\n";
    file_put_contents("logs/error.log", $log_entry, FILE_APPEND);
}

logError("User login failed for username: $username");
?>
```

---

## 10. MINI PROJECTS

### Project 1: Simple Todo List

**Database:**
```sql
CREATE TABLE todos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    title VARCHAR(100),
    completed BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Features:**
- ✓ Add todo
- ✓ List todos
- ✓ Mark as done
- ✓ Delete todo

**Try:** Build this! (Use CRUD pattern dari section 6)

---

### Project 2: Simple Blog

**Database:**
```sql
CREATE TABLE posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200),
    content TEXT,
    author VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT,
    comment TEXT,
    author VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id)
);
```

**Features:**
- ✓ Create post
- ✓ View posts list
- ✓ View single post + comments
- ✓ Add comment

**Challenge:** Use JOIN untuk fetch post + comments

---

### Project 3: Simple Contact Form

**Features:**
- ✓ Form: name, email, message
- ✓ Validation (email format, not empty)
- ✓ Send email
- ✓ Save to database
- ✓ Admin view all contacts

**Skills:** Form processing, email, database

---

## QUICK REFERENCE

### Common PHP Functions

```php
// String
strlen($str)              // Length
strtoupper($str)          // Uppercase
strtolower($str)          // Lowercase
trim($str)                // Remove whitespace
str_replace($old, $new, $str)
htmlspecialchars($str)    // Escape HTML

// Array
count($arr)               // Count items
in_array($value, $arr)    // Check if exists
array_push($arr, $val)    // Add to end
array_pop($arr)           // Remove last
array_keys($arr)          // Get keys
array_values($arr)        // Get values
implode(",", $arr)        // Join array to string
explode(",", $str)        // Split string to array

// Number
abs($num)                 // Absolute value
round($num, 2)            // Round to 2 decimal
floor($num)               // Round down
ceil($num)                // Round up
rand(1, 100)              // Random number
number_format(1000000, 0) // Format number: 1,000,000

// Date/Time
date('Y-m-d')             // Current date: 2025-02-20
date('H:i:s')             // Current time: 14:30:45
strtotime('-7 days')      // 7 days ago
mktime(0, 0, 0, 2, 20, 2025) // Create date

// Type
gettype($var)             // Get type
is_string($var)           // Check if string
is_numeric($var)          // Check if number
is_array($var)            // Check if array
is_null($var)             // Check if null
isset($var)               // Check if exists
empty($var)               // Check if empty (0, "", null, false)

// File
file_exists($file)        // Check file exists
file_get_contents($file)  // Read entire file
file_put_contents($file, $content)
unlink($file)             // Delete file
```

### Database Cheat Sheet

```php
// CONNECTION
$koneksi = mysqli_connect($host, $user, $pass, $db);

// QUERY
$result = mysqli_query($koneksi, $sql);

// FETCH
$row = mysqli_fetch_assoc($result);    // 1 row
$rows = mysqli_fetch_all($result);     // All rows

// PREPARED STATEMENT
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);  // i=int, s=string
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// USEFUL INFO
mysqli_insert_id($koneksi)    // Last insert ID
mysqli_affected_rows($koneksi) // Rows affected
mysqli_error($koneksi)        // Error message
```

---

**Happy Learning! 🚀**

Start with basic PHP, practice with databases, then build actual projects.
Good luck!

