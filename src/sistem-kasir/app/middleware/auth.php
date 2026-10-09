<?php
require_once __DIR__ . '/../helpers/functions.php';

function require_login(): void {
    if (empty($_SESSION['user'])) {
        set_flash('warning', 'Silakan login terlebih dahulu.');
        redirect('/sistem-kasir/auth/login.php');
    }
}

function require_role(string $role): void {
    require_login();
    if (($_SESSION['user']['role'] ?? '') !== $role) {
        set_flash('danger', 'Anda tidak memiliki akses ke halaman tersebut.');
        redirect(($_SESSION['user']['role'] ?? '') === 'admin'
            ? '/sistem-kasir/admin/dashboard.php'
            : '/sistem-kasir/kasir/dashboard.php');
    }
}
