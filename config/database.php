<?php
// Set timezone Indonesia
date_default_timezone_set('Asia/Jakarta');

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'event_rental');

// Konfigurasi Base Path - untuk konsistensi di local dan hosting
if (!defined('BASE_PATH')) {
    // Deteksi direktori root aplikasi
    if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) {
        define('BASE_PATH', '../');
    } elseif (strpos($_SERVER['PHP_SELF'], '/petugas/') !== false) {
        define('BASE_PATH', '../');
    } elseif (strpos($_SERVER['PHP_SELF'], '/peminjam/') !== false) {
        define('BASE_PATH', '../');
    } elseif (strpos($_SERVER['PHP_SELF'], '/auth/') !== false) {
        define('BASE_PATH', '../');
    } else {
        define('BASE_PATH', './');
    }
}

// Koneksi Database
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8");

// Sinkronkan timezone MySQL dengan PHP
mysqli_query($conn, "SET time_zone = '+07:00'");

// Auto-create tables jika belum ada
function autoCreateTables() {
    global $conn;
    
    // Cek tabel log_aktivitas
    $check = @mysqli_query($conn, "SHOW TABLES LIKE 'log_aktivitas'");
    if (mysqli_num_rows($check) == 0) {
        $query = "CREATE TABLE log_aktivitas (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            aktivitas TEXT NOT NULL,
            keterangan TEXT,
            ip_address VARCHAR(45),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )";
        @mysqli_query($conn, $query);
    }

    // Cek kolom merk di tabel alat
    $check_merk = @mysqli_query($conn, "SHOW COLUMNS FROM alat LIKE 'merk'");
    if ($check_merk && mysqli_num_rows($check_merk) == 0) {
        @mysqli_query($conn, "ALTER TABLE alat ADD COLUMN merk VARCHAR(100) AFTER nama_alat");
    }

    // Cek kolom deleted_at di tabel alat
    $check_deleted = @mysqli_query($conn, "SHOW COLUMNS FROM alat LIKE 'deleted_at'");
    if ($check_deleted && mysqli_num_rows($check_deleted) == 0) {
        @mysqli_query($conn, "ALTER TABLE alat ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL");
    }

    // Perbesar kolom total_biaya dan subtotal agar tidak out of range
    @mysqli_query($conn, "ALTER TABLE peminjaman MODIFY COLUMN total_biaya DECIMAL(15,2) DEFAULT 0");
    @mysqli_query($conn, "ALTER TABLE peminjaman MODIFY COLUMN denda DECIMAL(15,2) DEFAULT 0");
    @mysqli_query($conn, "ALTER TABLE detail_peminjaman MODIFY COLUMN subtotal DECIMAL(15,2) DEFAULT 0");
    @mysqli_query($conn, "ALTER TABLE detail_peminjaman MODIFY COLUMN harga_satuan DECIMAL(15,2) DEFAULT 0");
}

// Jalankan auto-create saat pertama kali load
autoCreateTables();

// Fungsi untuk mencegah SQL Injection
function clean($data) {
    global $conn;
    if ($data === null || $data === '') {
        return '';
    }
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

// Fungsi untuk cek login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fungsi untuk cek role
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Fungsi untuk log aktivitas
function logActivity($user_id, $aktivitas, $keterangan = '') {
    global $conn;
    
    // Validasi user_id
    if (empty($user_id) || !is_numeric($user_id)) {
        return;
    }
    
    // Cek apakah user_id valid
    $check = @mysqli_query($conn, "SELECT id FROM users WHERE id = " . (int)$user_id);
    if (!$check || mysqli_num_rows($check) == 0) {
        return; // User tidak ditemukan, skip log
    }
    
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    $aktivitas = mysqli_real_escape_string($conn, htmlspecialchars(trim($aktivitas)));
    $keterangan = mysqli_real_escape_string($conn, htmlspecialchars(trim($keterangan)));
    
    $query = "INSERT INTO log_aktivitas (user_id, aktivitas, keterangan, ip_address) 
              VALUES (" . (int)$user_id . ", '$aktivitas', '$keterangan', '$ip')";
    @mysqli_query($conn, $query);
}
?>
