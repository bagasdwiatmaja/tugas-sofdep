<?php
require_once __DIR__ . '/../app/helpers/bootstrap.php';
require_role('kasir');
$pageTitle='Dashboard Kasir';
$stmt=$pdo->prepare('SELECT COUNT(*) AS jumlah,COALESCE(SUM(total),0) AS total FROM transaksi WHERE user_id=? AND DATE(created_at)=CURDATE() AND status="selesai"');$stmt->execute([$_SESSION['user']['id']]);$today=$stmt->fetch();
$products=(int)$pdo->query('SELECT COUNT(*) FROM barang b JOIN stok s ON s.barang_id=b.id WHERE b.aktif=1 AND s.stok_etalase>0')->fetchColumn();
$recent=$pdo->prepare('SELECT id,kode_transaksi,total,created_at FROM transaksi WHERE user_id=? AND status="selesai" ORDER BY created_at DESC LIMIT 5');$recent->execute([$_SESSION['user']['id']]);$rows=$recent->fetchAll();
require __DIR__ . '/../partials/header.php';
?>
<div class="welcome-row"><div><span class="eyebrow">AREA KASIR</span><h2>Siap melayani pelanggan? 👋</h2><p class="muted">Mulai transaksi baru atau cek transaksi sebelumnya.</p></div><a class="btn btn-primary" href="/sistem-kasir/kasir/transaksi.php">＋ Transaksi baru</a></div>
<div class="stats-grid stats-three"><div class="stat-card"><div class="stat-icon purple">▣</div><span>Transaksi hari ini</span><strong><?= (int)$today['jumlah'] ?></strong><small>Transaksi Anda</small></div><div class="stat-card"><div class="stat-icon green">↗</div><span>Penjualan hari ini</span><strong><?= rupiah($today['total']) ?></strong><small>Total transaksi selesai</small></div><div class="stat-card"><div class="stat-icon blue">▤</div><span>Barang tersedia</span><strong><?= $products ?></strong><small>Stok etalase lebih dari nol</small></div></div>
<section class="panel"><div class="panel-heading"><div><h3>Transaksi terakhir</h3><p>Riwayat transaksi yang Anda proses.</p></div><a class="text-link" href="riwayat.php">Lihat riwayat →</a></div><div class="table-wrap"><table><thead><tr><th>Kode transaksi</th><th>Tanggal</th><th>Total</th><th>Aksi</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><span class="code-chip"><?= e($r['kode_transaksi']) ?></span></td><td><?= date('d/m/Y H:i',strtotime($r['created_at'])) ?></td><td><strong><?= rupiah($r['total']) ?></strong></td><td><a class="text-link" href="struk.php?id=<?= (int)$r['id'] ?>">Lihat struk →</a></td></tr><?php endforeach; ?><?php if(!$rows): ?><tr><td colspan="4">Belum ada transaksi.</td></tr><?php endif; ?></tbody></table></div></section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
