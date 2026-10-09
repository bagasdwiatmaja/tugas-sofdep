<?php
require_once __DIR__ . '/../../app/helpers/bootstrap.php';
require_role('admin');
$pageTitle='Laporan Penjualan';
$from=$_GET['from']??date('Y-m-01');$to=$_GET['to']??date('Y-m-d');
if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$from))$from=date('Y-m-01');
if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$to))$to=date('Y-m-d');
$stmt=$pdo->prepare('SELECT t.id,t.kode_transaksi,t.total,t.bayar,t.kembalian,t.created_at,u.nama FROM transaksi t JOIN users u ON u.id=t.user_id WHERE t.status="selesai" AND DATE(t.created_at) BETWEEN ? AND ? ORDER BY t.created_at DESC');
$stmt->execute([$from,$to]);$rows=$stmt->fetchAll();$total=array_sum(array_column($rows,'total'));
require __DIR__ . '/../../partials/header.php';
?>
<section class="panel"><div class="panel-heading"><div><h3>Filter laporan</h3><p>Pilih rentang tanggal transaksi.</p></div></div><form class="filter-row" method="get"><label>Dari tanggal<input type="date" name="from" value="<?= e($from) ?>"></label><label>Sampai tanggal<input type="date" name="to" value="<?= e($to) ?>"></label><button class="btn btn-primary">Tampilkan</button><a class="btn btn-secondary" href="index.php">Hari ini/bulan ini</a></form></section>
<div class="stats-grid stats-three"><div class="stat-card"><span>Total transaksi</span><strong><?= count($rows) ?></strong><small>Dalam rentang terpilih</small></div><div class="stat-card"><span>Total penjualan</span><strong><?= rupiah($total) ?></strong><small>Transaksi selesai</small></div><div class="stat-card"><span>Rata-rata transaksi</span><strong><?= rupiah(count($rows)?$total/count($rows):0) ?></strong><small>Per transaksi</small></div></div>
<section class="panel"><div class="panel-heading"><div><h3>Rincian transaksi</h3><p><?= e($from) ?> sampai <?= e($to) ?></p></div><button class="btn btn-secondary" onclick="window.print()" type="button">Cetak laporan</button></div><div class="table-wrap"><table><thead><tr><th>Kode transaksi</th><th>Tanggal</th><th>Kasir</th><th>Total</th><th>Detail</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><span class="code-chip"><?= e($r['kode_transaksi']) ?></span></td><td><?= date('d/m/Y H:i',strtotime($r['created_at'])) ?></td><td><?= e($r['nama']) ?></td><td><strong><?= rupiah($r['total']) ?></strong></td><td><a class="text-link" href="/sistem-kasir/kasir/struk.php?id=<?= (int)$r['id'] ?>">Lihat struk →</a></td></tr><?php endforeach; ?><?php if(!$rows): ?><tr><td colspan="5"><div class="empty-state">Tidak ada transaksi pada rentang tanggal ini.</div></td></tr><?php endif; ?></tbody></table></div></section>
<?php require __DIR__ . '/../../partials/footer.php'; ?>
