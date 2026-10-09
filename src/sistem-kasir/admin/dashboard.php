<?php
require_once __DIR__ . '/../app/helpers/bootstrap.php';
require_role('admin');
$pageTitle = 'Dashboard';
$barangCount = (int)$pdo->query('SELECT COUNT(*) FROM barang WHERE aktif=1')->fetchColumn();
$userCount = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='kasir' AND aktif=1")->fetchColumn();
$todaySales = (float)$pdo->query('SELECT COALESCE(SUM(total),0) FROM transaksi WHERE DATE(created_at)=CURDATE() AND status="selesai"')->fetchColumn();
$todayCount = (int)$pdo->query('SELECT COUNT(*) FROM transaksi WHERE DATE(created_at)=CURDATE() AND status="selesai"')->fetchColumn();
$lowStock = $pdo->query('SELECT b.id_barang,b.nama,b.merek,s.stok_etalase,s.stok_gudang FROM barang b JOIN stok s ON s.barang_id=b.id WHERE b.aktif=1 AND s.stok_etalase<=5 ORDER BY s.stok_etalase ASC LIMIT 5')->fetchAll();
$recent = $pdo->query('SELECT t.kode_transaksi,t.total,t.created_at,u.nama FROM transaksi t JOIN users u ON u.id=t.user_id WHERE t.status="selesai" ORDER BY t.created_at DESC LIMIT 5')->fetchAll();
require __DIR__ . '/../partials/header.php';
?>
<div class="welcome-row"><div><span class="eyebrow">RINGKASAN TOKO</span><h2>Halo, <?= e($_SESSION['user']['nama']) ?> 👋</h2><p class="muted">Berikut ringkasan aktivitas toko Anda hari ini.</p></div><a class="btn btn-primary" href="/sistem-kasir/admin/barang/tambah.php">＋ Tambah barang</a></div>
<div class="stats-grid">
    <div class="stat-card"><div class="stat-icon purple">▤</div><span>Total barang aktif</span><strong><?= number_format($barangCount) ?></strong><small>Produk terdaftar</small></div>
    <div class="stat-card"><div class="stat-icon green">↗</div><span>Penjualan hari ini</span><strong><?= rupiah($todaySales) ?></strong><small>Pendapatan transaksi selesai</small></div>
    <div class="stat-card"><div class="stat-icon orange">▣</div><span>Transaksi hari ini</span><strong><?= number_format($todayCount) ?></strong><small>Transaksi selesai</small></div>
    <div class="stat-card"><div class="stat-icon blue">♙</div><span>Pengguna kasir</span><strong><?= number_format($userCount) ?></strong><small>Akun aktif</small></div>
</div>
<div class="content-grid">
    <section class="panel"><div class="panel-heading"><div><h3>Stok etalase menipis</h3><p>Barang dengan stok etalase 5 atau kurang</p></div><a class="text-link" href="/sistem-kasir/admin/stok/index.php">Kelola stok →</a></div>
    <?php if (!$lowStock): ?><div class="empty-state">Belum ada barang dengan stok etalase menipis.</div><?php else: ?>
    <div class="table-wrap"><table><thead><tr><th>Barang</th><th>Gudang</th><th>Etalase</th><th>Status</th></tr></thead><tbody>
    <?php foreach ($lowStock as $item): ?><tr><td><strong><?= e($item['nama']) ?></strong><small class="cell-sub"><?= e($item['merek']) ?></small></td><td><?= (int)$item['stok_gudang'] ?></td><td><strong><?= (int)$item['stok_etalase'] ?></strong></td><td><span class="badge badge-warning">Menipis</span></td></tr><?php endforeach; ?>
    </tbody></table></div><?php endif; ?></section>
    <section class="panel"><div class="panel-heading"><div><h3>Transaksi terbaru</h3><p>5 transaksi terakhir</p></div><a class="text-link" href="/sistem-kasir/admin/laporan/index.php">Lihat semua →</a></div>
    <?php if (!$recent): ?><div class="empty-state">Belum ada transaksi.</div><?php else: ?>
    <div class="recent-list"><?php foreach ($recent as $r): ?><div class="recent-item"><div class="recent-icon">↗</div><div class="recent-info"><strong><?= e($r['kode_transaksi']) ?></strong><small><?= e($r['nama']) ?> · <?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></small></div><b><?= rupiah($r['total']) ?></b></div><?php endforeach; ?></div><?php endif; ?></section>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
