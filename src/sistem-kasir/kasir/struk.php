<?php
require_once __DIR__ . '/../app/helpers/bootstrap.php';
require_login();
$id=(int)($_GET['id']??0);
$stmt=$pdo->prepare('SELECT t.*,u.nama AS kasir FROM transaksi t JOIN users u ON u.id=t.user_id WHERE t.id=?');$stmt->execute([$id]);$trx=$stmt->fetch();
if(!$trx){set_flash('danger','Transaksi tidak ditemukan.');redirect($_SESSION['user']['role']==='admin'?'/sistem-kasir/admin/laporan/index.php':'/sistem-kasir/kasir/riwayat.php');}
if($_SESSION['user']['role']!=='admin'&&(int)$trx['user_id']!==(int)$_SESSION['user']['id']){http_response_code(403);exit('Anda tidak memiliki akses ke struk ini.');}
$s=$pdo->prepare('SELECT * FROM detail_transaksi WHERE transaksi_id=?');$s->execute([$id]);$details=$s->fetchAll();
$pageTitle='Struk Transaksi';require __DIR__ . '/../partials/header.php';
?>
<div class="receipt-page"><div class="receipt-actions"><a class="btn btn-secondary" href="<?= $_SESSION['user']['role']==='admin'?'/sistem-kasir/admin/laporan/index.php':'/sistem-kasir/kasir/riwayat.php' ?>">← Kembali</a><button class="btn btn-primary" onclick="window.print()">Cetak struk ⎙</button></div>
<div class="receipt"><div class="receipt-head"><div class="receipt-logo">K</div><h2>KasirKu Store</h2><p>Terima kasih telah berbelanja</p><div class="receipt-dash"></div></div>
<div class="receipt-meta"><div><span>No. transaksi</span><strong><?= e($trx['kode_transaksi']) ?></strong></div><div><span>Tanggal</span><strong><?= date('d/m/Y H:i',strtotime($trx['created_at'])) ?></strong></div><div><span>Kasir</span><strong><?= e($trx['kasir']) ?></strong></div></div><div class="receipt-dash"></div>
<table class="receipt-table"><tbody><?php foreach($details as $d): ?><tr><td><?= e($d['nama_barang']) ?><small><?= (int)$d['qty'] ?> × <?= rupiah($d['harga']) ?></small></td><td><?= rupiah($d['subtotal']) ?></td></tr><?php endforeach; ?></tbody></table><div class="receipt-dash"></div>
<div class="receipt-totals"><div><span>Total</span><strong><?= rupiah($trx['total']) ?></strong></div><div><span>Tunai</span><span><?= rupiah($trx['bayar']) ?></span></div><div><span>Kembalian</span><span><?= rupiah($trx['kembalian']) ?></span></div></div><div class="receipt-dash"></div><div class="receipt-thanks">Barang yang sudah dibeli tidak dapat ditukar kecuali sesuai ketentuan toko.<br><strong>Semoga hari Anda menyenangkan!</strong></div></div></div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
