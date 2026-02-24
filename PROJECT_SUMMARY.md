# 📚 SISTEM PERPUSTAKAAN SEKOLAH - PROJECT SUMMARY

## 1. PROJECT OVERVIEW

**Nama:** Sistem Informasi Peminjaman Buku Perpustakaan Sekolah
**Tipe:** Web Application (PHP Native + MySQL)
**Deskripsi:** Aplikasi berbasis web untuk manajemen peminjaman buku di perpustakaan sekolah dengan fitur dual-role (Admin & Siswa)

### Tujuan
- Memudahkan siswa dalam peminjaman & pengembalian buku
- Mempermudah admin dalam pengelolaan data buku, anggota, & transaksi
- Mencatat riwayat peminjaman & denda keterlambatan
- Digitalisasi proses perpustakaan (paperless)

---

## 2. PROJECT STRUCTURE

```
peminjaman/
├── admin/                          # Panel Admin
│   ├── dashboard.php               # Dashboard Admin (statistik & notifikasi)
│   ├── laporan.php                 # Laporan Sistem
│   ├── anggota/
│   │   ├── index.php               # List Anggota
│   │   ├── tambah.php              # Tambah Anggota
│   │   ├── edit.php                # Edit Anggota
│   │   └── hapus.php               # Hapus Anggota
│   ├── buku/
│   │   ├── index.php               # List Buku
│   │   ├── tambah.php              # Tambah Buku
│   │   ├── edit.php                # Edit Buku
│   │   └── hapus.php               # Hapus Buku
│   └── transaksi/
│       ├── pinjam.php              # Manual Peminjaman
│       ├── kembali.php             # Manual Pengembalian
│       ├── approval-pinjam.php      # Approve Request Pinjam
│       ├── approval-kembali.php     # Approve Request Kembali
│       └── riwayat.php             # Riwayat Transaksi
│
├── siswa/                          # Panel Siswa
│   ├── dashboard.php               # Dashboard Siswa (buku dipinjam & statistik)
│   ├── buku.php                    # Katalog Buku
│   ├── pinjamanku.php              # Riwayat Peminjaman Pribadi
│   ├── profil.php                  # Profil Siswa
│   ├── ubah-password.php           # Ubah Password
│   ├── register.php                # Form Registrasi
│   ├── register-proses.php         # Proses Registrasi
│   ├── request-pinjam.php          # Request Peminjaman
│   ├── request-kembali.php         # Request Pengembalian
│   └── request-saya.php            # Lihat Request Saya
│
├── includes/                       # Shared Components
│   ├── header.php                  # HTML Header & Session Check
│   ├── footer.php                  # HTML Footer
│   ├── sidebar_admin.php           # Menu Sidebar Admin
│   ├── sidebar_siswa.php           # Menu Sidebar Siswa
│   └── fungsi.php                  # Helper Functions
│
├── assets/
│   ├── css/
│   │   ├── bootstrap.min.css       # Bootstrap Framework
│   │   └── style.css               # Custom Styles
│   ├── js/
│   │   ├── bootstrap.bundle.min.js # Bootstrap JS
│   │   └── script.js               # Custom Scripts
│   └── images/
│       └── buku/                   # Folder Upload Gambar Buku
│
├── koneksi.php                     # Database Connection + Session Start
├── index.php                       # Login Page
├── login-proses.php                # Login Processing
├── logout.php                      # Logout Processing
├── seed-admin.php                  # Setup Admin User & Kategori
└── PROJECT_SUMMARY.md              # File ini - Dokumentasi Lengkap
```

---

## 3. DATABASE SCHEMA

### 3.1 Tabel Utama

#### `admin` - Akun Admin
```sql
CREATE TABLE admin (
    id_admin INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,        -- bcrypt hash
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    nohp VARCHAR(15),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### `anggota` - Data Siswa (Peminjam)
```sql
CREATE TABLE anggota (
    id_anggota INT PRIMARY KEY AUTO_INCREMENT,
    nis_nisn VARCHAR(20) UNIQUE,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,        -- bcrypt hash
    nama VARCHAR(100) NOT NULL,
    kelas_jurusan VARCHAR(50),
    jenis_kelamin CHAR(1),                 -- 'L' atau 'P'
    alamat TEXT,
    nohp VARCHAR(15),
    status_aktif TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### `buku` - Data Buku
```sql
CREATE TABLE buku (
    id_buku INT PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(150) NOT NULL,
    pengarang VARCHAR(100),
    penerbit VARCHAR(100),
    isbn VARCHAR(20) UNIQUE,
    kategori_id INT,
    tahun_terbit YEAR,
    stok_total INT DEFAULT 0,
    stok_tersedia INT DEFAULT 0,           -- Updated when borrowed
    lokasi_rak VARCHAR(50),
    gambar VARCHAR(255),                   -- File path
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id_kategori)
);
```

#### `transaksi` - Riwayat Peminjaman & Pengembalian
```sql
CREATE TABLE transaksi (
    id_transaksi INT PRIMARY KEY AUTO_INCREMENT,
    id_anggota INT NOT NULL,
    id_buku INT NOT NULL,
    id_admin INT,                          -- Admin yang approve
    tanggal_pinjam DATE NOT NULL,
    batas_kembali DATE NOT NULL,           -- Due date
    tanggal_kembali DATE,                  -- Actual return date
    status ENUM('dipinjam', 'dikembalikan') DEFAULT 'dipinjam',
    denda DECIMAL(10,2) DEFAULT 0,         -- Fine for late return
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_anggota) REFERENCES anggota(id_anggota),
    FOREIGN KEY (id_buku) REFERENCES buku(id_buku),
    FOREIGN KEY (id_admin) REFERENCES admin(id_admin)
);
```

#### `request_peminjaman` - Request Pinjam dari Siswa
```sql
CREATE TABLE request_peminjaman (
    id_request INT PRIMARY KEY AUTO_INCREMENT,
    id_anggota INT NOT NULL,
    id_buku INT NOT NULL,
    lama_pinjam INT DEFAULT 7,             -- Days
    keterangan TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    id_admin INT,                          -- Admin yang approve
    alasan_reject TEXT,
    id_transaksi INT,                      -- Link ke transaksi jika approved
    tanggal_request TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tanggal_response DATETIME,
    FOREIGN KEY (id_anggota) REFERENCES anggota(id_anggota),
    FOREIGN KEY (id_buku) REFERENCES buku(id_buku),
    FOREIGN KEY (id_admin) REFERENCES admin(id_admin),
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi)
);
```

#### `request_pengembalian` - Request Kembali dari Siswa
```sql
CREATE TABLE request_pengembalian (
    id_request INT PRIMARY KEY AUTO_INCREMENT,
    id_transaksi INT NOT NULL,
    id_anggota INT NOT NULL,
    keterangan TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    id_admin INT,                          -- Admin yang approve
    alasan_reject TEXT,
    tanggal_request TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tanggal_response DATETIME,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi),
    FOREIGN KEY (id_anggota) REFERENCES anggota(id_anggota),
    FOREIGN KEY (id_admin) REFERENCES admin(id_admin)
);
```

#### `kategori` - Kategori Buku
```sql
CREATE TABLE kategori (
    id_kategori INT PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### `log_aktivitas` - Activity Log untuk Audit Trail
```sql
CREATE TABLE log_aktivitas (
    id_log INT PRIMARY KEY AUTO_INCREMENT,
    id_admin INT,                          -- NULL if siswa
    id_anggota INT,                        -- NULL if admin
    aktivitas VARCHAR(100),                -- 'login', 'approve_peminjaman', etc
    deskripsi TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_admin) REFERENCES admin(id_admin),
    FOREIGN KEY (id_anggota) REFERENCES anggota(id_anggota)
);
```

---

## 4. AUTHENTICATION & AUTHORIZATION

### 4.1 Login Flow

**File:** `login-proses.php`

```
1. User submit username, password, role (admin/siswa)
   ↓
2. Cek database sesuai role
   - Admin: SELECT * FROM admin WHERE username
   - Siswa: SELECT * FROM anggota WHERE username
   ↓
3. Verify password dengan password_verify()
   └─ Fail → $_SESSION['error'] → redirect index.php
   ├─ Success → SET $_SESSION variables
   │   - user_id
   │   - username
   │   - nama
   │   - role
   ├─ Log aktivitas ke log_aktivitas
   └─ Redirect ke dashboard/admin atau siswa
```

### 4.2 Authorization Check

**File:** `includes/fungsi.php`

```php
// Check user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

// Check user is siswa
function isSiswa() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'siswa';
}

// Used di setiap halaman protected:
if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}
```

### 4.3 Session Management

- Session dimulai di `koneksi.php`: `session_start()` (dengan conditional check)
- Session dihancurkan di `logout.php`: `session_destroy()`
- Aktivitas logout di-log ke `log_aktivitas`

---

## 5. FEATURE LIST & ENDPOINTS

### 5.1 Public Pages (No Auth Required)

| Endpoint | Method | Deskripsi |
|----------|--------|-----------|
| `/index.php` | GET | Login Page |
| `/siswa/register.php` | GET | Registration Form |
| `/siswa/register-proses.php` | POST | Process Registration |

### 5.2 Admin Features

#### Dashboard
- **`/admin/dashboard.php`** - Statistics (buku, anggota, dipinjam, terlambat) + Pending requests

#### Manajemen Buku
- **`/admin/buku/index.php`** - List buku dengan CRUD
- **`/admin/buku/tambah.php`** - Add book (dengan upload gambar)
- **`/admin/buku/edit.php`** - Edit book
- **`/admin/buku/hapus.php`** - Delete book

#### Manajemen Anggota
- **`/admin/anggota/index.php`** - List anggota dengan filter
- **`/admin/anggota/tambah.php`** - Add member
- **`/admin/anggota/edit.php`** - Edit member
- **`/admin/anggota/hapus.php`** - Delete member (with check active transactions)

#### Manajemen Transaksi
- **`/admin/transaksi/pinjam.php`** - Manual peminjaman (create transaksi directly)
- **`/admin/transaksi/kembali.php`** - Manual pengembalian & hitung denda
- **`/admin/transaksi/approval-pinjam.php`** - Approve/reject request peminjaman dari siswa
- **`/admin/transaksi/approval-kembali.php`** - Approve/reject request pengembalian dari siswa
- **`/admin/transaksi/riwayat.php`** - Lihat riwayat semua transaksi

#### Reports
- **`/admin/laporan.php`** - Laporan bulanan/tahunan

### 5.3 Student Features

#### Dashboard & Profile
- **`/siswa/dashboard.php`** - Personal statistics (sedang dipinjam, terlambat, denda)
- **`/siswa/profil.php`** - View & update profile ⚠️ **HAS SQL INJECTION**
- **`/siswa/ubah-password.php`** - Change password

#### Peminjaman Buku
- **`/siswa/buku.php`** - Browse katalog buku
- **`/siswa/request-pinjam.php`** - Submit request peminjaman (tunggu approval admin)

#### Pengembalian Buku
- **`/siswa/pinjamanku.php`** - Lihat buku yang sedang dipinjam
- **`/siswa/request-kembali.php`** - Submit request pengembalian (dari buku yang sedang dipinjam)
- **`/siswa/request-saya.php`** - Lihat status request pinjam & kembali

---

## 6. KEY FUNCTIONS (File: `includes/fungsi.php`)

### 6.1 Password & Security

```php
// Encrypt password dengan bcrypt
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Sanitize input (untuk prevent HTML/SQL injection)
function clean($data) {
    global $koneksi;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($koneksi, $data);
    return $data;
}
```

### 6.2 Authorization

```php
function isLoggedIn() { return isset($_SESSION['user_id']); }
function isAdmin() { return isset($_SESSION['role']) && $_SESSION['role'] == 'admin'; }
function isSiswa() { return isset($_SESSION['role']) && $_SESSION['role'] == 'siswa'; }
```

### 6.3 Utility

```php
// Redirect dengan header
function redirect($url) {
    header("Location: $url");
    exit();
}

// Format date untuk display
function formatTanggal($tanggal) {
    // Convert: 2025-02-20 → 20 Februari 2025
}

// Calculate fine untuk keterlambatan
function hitungDenda($tanggal_kembali, $batas_kembali) {
    // Denda Rp 1000 per hari terlambat
}

// Upload & validate gambar
function uploadGambar($file, $folder = 'buku') {
    // Validate: format jpg/png/gif, max 5MB
    // Generate unique filename
    // Return: ['status' => true/false, 'filename' => '...']
}

// Logging for audit trail
function logAktivitas($koneksi, $aktivitas, $deskripsi, $id_admin, $id_anggota) {
    // INSERT ke log_aktivitas dengan IP address & user agent
}
```

---

## 7. CRITICAL SECURITY ISSUES

### 🔴 ISSUE #1: SQL Injection in 6 Queries

**Affected Files:**
- `siswa/profil.php:15`
- `admin/transaksi/pinjam.php:24,29`
- `admin/transaksi/kembali.php:20`
- `admin/anggota/hapus.php:13,21`

**Problem:**
```php
// ❌ VULNERABLE
$anggota = mysqli_fetch_assoc(mysqli_query($koneksi, 
    "SELECT * FROM anggota WHERE id_anggota = $id_anggota"
));
```

**Solution:**
```php
// ✅ SECURE
$sql = "SELECT * FROM anggota WHERE id_anggota = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_anggota);
mysqli_stmt_execute($stmt);
$anggota = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
```

---

### 🔴 ISSUE #2: CSRF Vulnerability in Approve/Reject

**File:** `admin/transaksi/approval-pinjam.php:15`

**Problem:**
```php
if (isset($_GET['action']) && isset($_GET['id'])) {
    if ($action == 'approve') { ... }
}
```

URLs seperti `?action=approve&id=5` bisa dimanipulasi:
- Di-share dalam email/chat
- CSRF attack dari web lain
- Accidental double-click

**Solution:** Gunakan POST + CSRF token

```php
// Form
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <input type="hidden" name="action" value="approve">
    <button type="submit">Approve</button>
</form>

// Process
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF token invalid');
}
```

---

### 🟠 ISSUE #3: No Input Validation

```php
// ❌ ONLY SANITIZE, tidak validate
$username = clean($_POST['username']);

// ✅ SHOULD VALIDATE
if (strlen($username) < 3 || strlen($username) > 50) {
    die('Username harus 3-50 karakter');
}
if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    die('Username hanya boleh alfanumerik & underscore');
}
```

---

### 🟠 ISSUE #4: No Session Timeout

Session bisa permanent jika browser tidak ditutup. Seharusnya:

```php
// coneksi.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timeout session jika idle > 30 menit
$timeout = 1800; // 30 minutes
if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > $timeout) {
        session_destroy();
        redirect('index.php');
    }
}
$_SESSION['last_activity'] = time();
```

---

### 🟡 ISSUE #5: Weak Password Policy

- Tidak ada minimum length requirement
- Tidak ada complexity requirement (uppercase, number, special char)
- Bisa regist dengan password "123"

---

### 🟡 ISSUE #6: No Pagination

```php
// ❌ Load semua anggota ke memory
$result = mysqli_query($koneksi, "SELECT * FROM anggota");

// ✅ Should paginate
$limit = 20;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;
$result = mysqli_query($koneksi, "SELECT * FROM anggota LIMIT ? OFFSET ?");
```

---

## 8. CODE PATTERNS & BEST PRACTICES USED

### 8.1 Prepared Statements (✓ Partial)

Used in:
- `login-proses.php` - login queries
- `admin/transaksi/approval-pinjam.php` - create transaksi
- `seed-admin.php` - initial insert
- `siswa/register-proses.php` - register

Not used in:
- `siswa/profil.php` - profile query
- `admin/transaksi/pinjam.php` - pinjam operations
- 4+ more files

### 8.2 Password Hashing (✓ Full)

Consistent use of:
```php
password_hash($password, PASSWORD_DEFAULT)  // Store
password_verify($password, $hash)           // Verify
```

### 8.3 Activity Logging (✓ Good)

Events logged:
- Login/logout
- Approve/reject peminjaman
- Create/update/delete records
- File uploads

### 8.4 Session-Based Authorization (✓ Good)

Check at top of protected pages:
```php
if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}
```

### 8.5 File Upload Validation (✓ Good)

Checks:
- File type (jpg, png, gif)
- File size (max 5MB)
- Image validation dengan getimagesize()
- Unique filename generation

### 8.6 Error Handling (⚠️ Inconsistent)

- Some pages use `$_SESSION['error']` + redirect
- Some pages echo directly
- Some pages silent fail
- No try-catch for exceptions

---

## 9. SETUP INSTRUCTIONS

### 9.1 Prerequisites

```
- PHP 7.4+
- MySQL 5.7+
- Apache (XAMPP recommended)
- Browser (Chrome, Firefox, Safari)
```

### 9.2 Installation Steps

```bash
# 1. Copy project ke C:\xampp\htdocs\peminjaman
# 2. Create database
CREATE DATABASE db_perpustakaan;

# 3. Create tables (gunakan SQL dari section 3.1)

# 4. Setup admin user
# Buka di browser: http://localhost/peminjaman/seed-admin.php
# Output: Admin berhasil dibuat! Username: admin, Password: admin123

# 5. Test login
# http://localhost/peminjaman/
# Admin: admin / admin123 (role: admin)
# Siswa: buat account di register page
```

### 9.3 Database Connection

**File:** `koneksi.php`

```php
$host = 'localhost';
$user = 'root';              // Change if different
$pass = '';                  // Add password if exists
$db   = 'db_perpustakaan';   // Change database name if needed
```

---

## 10. TIPS UNTUK AI LEARNING

### 10.1 Recommended Study Order

1. **Start with:** `koneksi.php` → `includes/fungsi.php` → `index.php`
   - Learn: Connection setup, helper functions, auth flow

2. **Then:** `login-proses.php` → `logout.php`
   - Learn: Login logic, password hashing, session management

3. **Then:** `admin/dashboard.php` → `siswa/dashboard.php`
   - Learn: Authorization checks, database queries, UI patterns

4. **Then:** `admin/anggota/` & `admin/buku/` folders
   - Learn: CRUD operations, file uploads, form handling

5. **Finally:** `admin/transaksi/` folder
   - Learn: Complex business logic, multiple table joins, status management

### 10.2 Reusable Code Patterns

**Pattern 1: CRUD List Page**
```php
<?php
require_once '../../koneksi.php';
require_once '../../includes/fungsi.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$title = 'Data Buku';
$base_url = '../../';
include '../../includes/header.php';

// Fetch all records
$result = mysqli_query($koneksi, "SELECT * FROM buku ORDER BY created_at DESC");
?>
<div class="d-flex">
    <?php include '../../includes/sidebar_admin.php'; ?>
    <div class="flex-grow-1 p-4">
        <!-- Table dengan action buttons (edit/delete) -->
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
```

**Pattern 2: Status-Based UI**
```php
<?php
$badge_class = 'secondary';
if ($status == 'dipinjam') {
    $badge_class = (strtotime($due_date) < time()) ? 'danger' : 'warning';
} elseif ($status == 'dikembalikan') {
    $badge_class = 'success';
}
?>
<span class="badge bg-<?php echo $badge_class; ?>">
    <?php echo ucfirst($status); ?>
</span>
```

**Pattern 3: Form with Validation**
```php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $field = clean($_POST['field']);
    
    if (empty($field)) {
        $_SESSION['error'] = 'Field tidak boleh kosong!';
    } else {
        // Insert/Update logic
        $_SESSION['success'] = 'Data berhasil disimpan!';
        redirect('index.php');
    }
}
```

---

## 11. FUTURE IMPROVEMENTS

### Priority 1: Security (CRITICAL)
- [ ] Convert all queries ke prepared statements
- [ ] Implement CSRF token protection
- [ ] Add input validation (min/max length, format)
- [ ] Add session timeout (30 menit idle)
- [ ] Add password complexity rules

### Priority 2: Features (HIGH)
- [ ] Add pagination (untuk >50 items)
- [ ] Add search/filter functionality
- [ ] Add email notifications (request approved)
- [ ] Password reset functionality
- [ ] Extend return date feature

### Priority 3: Infrastructure (MEDIUM)
- [ ] Error logging to file
- [ ] Database backup automation
- [ ] Captcha on register/login
- [ ] Rate limiting on API endpoints
- [ ] IP whitelisting for admin

### Priority 4: UX (NICE-TO-HAVE)
- [ ] Export reports to PDF/Excel
- [ ] Dashboard charts & graphs
- [ ] Mobile responsive improvements
- [ ] Dark mode toggle
- [ ] Book reservation system
- [ ] Fine payment system
- [ ] API for mobile app

---

## 12. FILE DEPENDENCY GRAPH

```
index.php (login page)
    ├── koneksi.php (DB connection)
    ├── includes/fungsi.php (helper functions)
    ├── includes/header.php (HTML head + session check)
    └── includes/footer.php (HTML footer)
         └── assets/css/bootstrap.min.css
         └── assets/js/bootstrap.bundle.min.js

login-proses.php (login processing)
    ├── koneksi.php
    ├── includes/fungsi.php
    └── redirect to admin/dashboard.php or siswa/dashboard.php

admin/dashboard.php
    ├── koneksi.php
    ├── includes/fungsi.php ← isLoggedIn(), isAdmin()
    ├── includes/header.php
    ├── includes/sidebar_admin.php
    └── includes/footer.php

siswa/request-pinjam.php
    ├── koneksi.php
    ├── includes/fungsi.php ← isLoggedIn(), isSiswa()
    ├── includes/header.php
    ├── includes/sidebar_siswa.php
    └── includes/footer.php

admin/transaksi/approval-pinjam.php
    ├── koneksi.php
    ├── includes/fungsi.php ← logAktivitas()
    ├── includes/header.php
    └── includes/sidebar_admin.php
```

---

## 13. DATABASE QUERY REFERENCE

### Common Queries in Codebase

**1. Login Admin**
```sql
SELECT * FROM admin WHERE username = ? AND is_active = 1
```

**2. Get Pending Requests**
```sql
SELECT COUNT(*) as total FROM request_peminjaman WHERE status = 'pending'
```

**3. Calculate Late Books**
```sql
SELECT COUNT(*) as total FROM transaksi 
WHERE status = 'dipinjam' AND batas_kembali < CURDATE()
```

**4. Get Recent Transactions**
```sql
SELECT t.*, a.nama as nama_anggota, b.judul as judul_buku
FROM transaksi t
JOIN anggota a ON t.id_anggota = a.id_anggota
JOIN buku b ON t.id_buku = b.id_buku
ORDER BY t.created_at DESC
LIMIT 10
```

**5. Create Loan Transaction**
```sql
INSERT INTO transaksi 
(id_anggota, id_buku, id_admin, tanggal_pinjam, batas_kembali, status) 
VALUES (?, ?, ?, ?, ?, 'dipinjam')
```

---

## 14. QUICK TESTING CHECKLIST

```
[ ] Admin can login
[ ] Admin can see dashboard with statistics
[ ] Admin can add/edit/delete books
[ ] Admin can add/edit/delete members
[ ] Admin can approve student loan requests
[ ] Admin can process returns & calculate fines
[ ] Student can register
[ ] Student can login
[ ] Student can view available books
[ ] Student can request to borrow books
[ ] Student can request to return books
[ ] Logout works and session is destroyed
[ ] Late books show red badge
[ ] Fine calculation is correct (1000 per day)
[ ] Image upload validation works
```

---

## 15. COMMON ERRORS & FIXES

### Error: "Koneksi gagal"
- Check if MySQL is running
- Verify `koneksi.php` credentials match your setup
- Check database exists

### Error: "Session activity log not found"
- Make sure `log_aktivitas` table exists
- Check table permissions

### Error: "Gambar bukan gambar"
- File might be corrupted or wrong format
- Only jpg/png/gif allowed
- Max 5MB size

### Error: "localhost redirected you too many times"
- Missing `session_start()` in included files
- `isLoggedIn()` always returns false
- Creates redirect loop
- **FIX:** Ensure `koneksi.php` has `session_start()`

---

## 16. PERFORMANCE OPTIMIZATION

### Current Bottlenecks
1. **N+1 Query Problem** - Loop queries in approval pages
2. **No Pagination** - Loads all records into memory
3. **No Caching** - Kategori buku di-query setiap page load
4. **No Indexes** - Database doesn't optimize foreign keys

### Quick Fixes
```php
// Instead of:
while ($row = mysqli_fetch_assoc($result)) {
    $buku = mysqli_query(...); // N+1 problem
}

// Do:
$result = mysqli_query("SELECT t.*, b.judul FROM transaksi t JOIN buku b ...");
```

---

## 17. SECURITY CHECKLIST

```
Authentication
[ ] Passwords are hashed (bcrypt)
[ ] Session check on protected pages
[ ] Logout destroys session
[ ] Login rate limiting (TODO)

Data Protection
[ ] SQL queries use prepared statements (partial)
[ ] File uploads validated (✓)
[ ] File uploads saved outside webroot (TODO)
[ ] User input is sanitized (partial)

Session Management
[ ] Session timeout implemented (TODO)
[ ] CSRF token protection (TODO)
[ ] Secure cookie flags (TODO)
[ ] SQL injection prevention (partial)

Audit & Logging
[ ] Login/logout logged (✓)
[ ] Admin actions logged (✓)
[ ] Error logging to file (TODO)
```

---

**Document Last Updated:** February 20, 2026
**Project Status:** Development (Ready for testing)
**Next Steps:** Fix security issues, add missing features

