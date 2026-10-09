<?php
require_once __DIR__ . '/../app/helpers/bootstrap.php';
require_role('kasir');
$pageTitle='Riwayat Transaksi';
$stmt=$pdo->prepare('SELECT id,kode_transaksi,total,bayar,kembalian,created_at FROM transaksi WHERE user_id=? AND status="selesai" ORDER BY created_at DESC LIMIT 200');$stmt->execute([$_SESSION['user']['id']]);$rows=$stmt->fetchAll();
require __DIR__ . '/../partials/header.php';
?>
<section class="panel"><div class="panel-heading"><div><h3>Transaksi yang pernah diproses</h3><p>Menampilkan maksimal 200 transaksi terbaru.</p></div></div><div class="table-wrap"><table><thead><tr><th>Kode transaksi</th><th>Tanggal</th><th>Total</th><th>Bayar</th><th>Kembalian</th><th>Struk</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><span class="code-chip"><?= e($r['kode_transaksi']) ?></span></td><td><?= date('d/m/Y H:i',strtotime($r['created_at'])) ?></td><td><strong><?= rupiah($r['total']) ?></strong></td><td><?= rupiah($r['bayar']) ?></td><td><?= rupiah($r['kembalian']) ?></td><td><a class="btn btn-small btn-secondary" href="struk.php?id=<?= (int)$r['id'] ?>">Lihat struk</a></td></tr><?php endforeach; ?><?php if(!$rows): ?><tr><td colspan="6"><div class="empty-state">Belum ada transaksi yang diproses.</div></td></tr><?php endif; ?></tbody></table></div></section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
