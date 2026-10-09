<?php
require_once __DIR__ . '/app/helpers/bootstrap.php';

$username = 'admin';
$password = 'AdminKasir123!';
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    "SELECT id FROM users WHERE username = ? LIMIT 1"
);
$stmt->execute([$username]);

if ($stmt->fetch()) {
    $stmt = $pdo->prepare(
        "UPDATE users
         SET password = ?, nama = ?, role = ?, aktif = 1
         WHERE username = ?"
    );
    $stmt->execute([
        $hash,
        'Administrator',
        'admin',
        $username
    ]);
} else {
    $stmt = $pdo->prepare(
        "INSERT INTO users
         (nama, username, password, role, aktif)
         VALUES (?, ?, ?, ?, 1)"
    );
    $stmt->execute([
        'Administrator',
        $username,
        $hash,
        'admin'
    ]);
}

echo 'Akun admin berhasil disiapkan.';