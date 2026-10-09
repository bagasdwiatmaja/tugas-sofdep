<?php
$pageTitle = $pageTitle ?? 'Sistem Kasir';
$currentUser = $_SESSION['user'] ?? null;
$flash = get_flash();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | KasirKu</title>
    <link rel="stylesheet" href="/sistem-kasir/assets/css/style.css">
</head>
<body>
<div class="app-shell">
    <?php if ($currentUser): ?>
    <aside class="sidebar">
        <a class="brand" href="<?= $currentUser['role'] === 'admin' ? '/sistem-kasir/admin/dashboard.php' : '/sistem-kasir/kasir/dashboard.php' ?>">
            <span class="brand-mark">K</span><span>Kasir<span class="brand-light">Ku</span><small>POINT OF SALE</small></span>
        </a>
        <div class="profile-mini">
            <div class="avatar"><?= e(strtoupper(substr($currentUser['nama'], 0, 1))) ?></div>
            <div><strong><?= e($currentUser['nama']) ?></strong><small><?= e(ucfirst($currentUser['role'])) ?></small></div>
        </div>
        <nav class="nav-menu">
            <span class="nav-label">MENU UTAMA</span>
            <?php if ($currentUser['role'] === 'admin'): ?>
                <a href="/sistem-kasir/admin/dashboard.php"><span>▦</span> Dashboard</a>
                <a href="/sistem-kasir/admin/barang/index.php"><span>▤</span> Data Barang</a>
                <a href="/sistem-kasir/admin/stok/index.php"><span>⇄</span> Kelola Stok</a>
                <a href="/sistem-kasir/admin/users/index.php"><span>♙</span> Pengguna Kasir</a>
                <a href="/sistem-kasir/admin/laporan/index.php"><span>▥</span> Laporan Penjualan</a>
            <?php else: ?>
                <a href="/sistem-kasir/kasir/dashboard.php"><span>▦</span> Dashboard</a>
                <a href="/sistem-kasir/kasir/transaksi.php"><span>＋</span> Transaksi Baru</a>
                <a href="/sistem-kasir/kasir/riwayat.php"><span>◷</span> Riwayat Transaksi</a>
            <?php endif; ?>
            <span class="nav-label nav-spaced">AKUN</span>
            <a href="/sistem-kasir/auth/logout.php"><span>↪</span> Keluar</a>
        </nav>
        <div class="sidebar-footer">KasirKu v1.0 <span>●</span> Sistem siap</div>
    </aside>
    <?php endif; ?>
    <main class="main-area <?= $currentUser ? '' : 'main-public' ?>">
        <?php if ($currentUser): ?>
        <header class="topbar">
            <div><span class="eyebrow">WORKSPACE</span><h1><?= e($pageTitle) ?></h1></div>
            <div class="topbar-right"><span class="status-dot"></span><span>Online</span><div class="topbar-avatar"><?= e(strtoupper(substr($currentUser['nama'], 0, 1))) ?></div></div>
        </header>
        <?php endif; ?>
        <div class="page-content">
            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
            <?php endif; ?>
