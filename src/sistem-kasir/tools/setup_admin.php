<?php
// Jalankan sekali melalui browser, lalu HAPUS file ini demi keamanan.
// URL: http://localhost/sistem-kasir/tools/setup_admin.php
require_once __DIR__ . '/../app/config/database.php';
$nama = 'Administrator';
$username = 'admin';
$password = 'AdminKasir123!';
$stmt = $pdo->prepare('SELECT id FROM users WHERE username=?');
$stmt->execute([$username]);
if ($stmt->fetch()) {
    exit('Akun admin sudah ada. Hapus file tools/setup_admin.php sekarang.');
}
$stmt = $pdo->prepare("INSERT INTO users (nama,username,password,role) VALUES (?,?,?,'admin')");
$stmt->execute([$nama,$username,password_hash($password,PASSWORD_DEFAULT)]);
echo 'Akun admin berhasil dibuat. Username: admin | Password: AdminKasir123!<br>Segera login, ganti password (fitur ubah password belum dibuat pada versi awal), dan hapus file tools/setup_admin.php.';
