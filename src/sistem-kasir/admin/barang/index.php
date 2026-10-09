<?php
require_once __DIR__ . '/../../app/helpers/bootstrap.php';
require_role('admin');
$pageTitle = 'Data Barang';
$q = trim((string)($_GET['q'] ?? ''));
$stmt = $pdo->prepare('SELECT b.*,s.stok_gudang,s.stok_etalase FROM barang b JOIN stok s ON s.barang_id=b.id WHERE b.aktif=1 AND (b.id_barang LIKE ? OR b.nama LIKE ? OR b.merek LIKE ?) ORDER BY b.created_at DESC');
$like = '%' . $q . '%'; $stmt->execute([$like,$like,$like]); $items=$stmt->fetchAll();
require __DIR__ . '/../../partials/header.php';
?>
<div class="page-actions"><div><p class="muted">Kelola daftar produk, harga jual, dan jumlah stok.</p></div><a class="btn btn-primary" href="tambah.php">＋ Tambah barang</a></div>
<section class="panel">
<form class="search-form" method="get"><div class="search-input"><span>⌕</span><input name="q" value="<?= e($q) ?>" placeholder="Cari ID, nama, atau merek barang..."></div><button class="btn btn-secondary">Cari barang</button><?php if($q): ?><a class="btn btn-ghost" href="index.php">Reset</a><?php endif; ?></form>
<div class="table-wrap"><table><thead><tr><th>ID Barang</th><th>Nama & Merek</th><th>Harga</th><th>Gudang</th><th>Etalase</th><th>Aksi</th></tr></thead><tbody>
<?php foreach($items as $b): ?><tr><td><span class="code-chip"><?= e($b['id_barang']) ?></span></td><td><strong><?= e($b['nama']) ?></strong><small class="cell-sub"><?= e($b['merek'] ?: 'Tanpa merek') ?></small></td><td><?= rupiah($b['harga']) ?></td><td><?= (int)$b['stok_gudang'] ?></td><td><?= (int)$b['stok_etalase'] ?></td><td><div class="action-group"><a class="btn btn-small btn-secondary" href="edit.php?id=<?= (int)$b['id'] ?>">Edit</a><form method="post" action="hapus.php" data-confirm="Nonaktifkan barang ini?"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>"><button class="btn btn-small btn-danger-soft">Hapus</button></form></div></td></tr><?php endforeach; ?>
<?php if(!$items): ?><tr><td colspan="6"><div class="empty-state">Barang tidak ditemukan.</div></td></tr><?php endif; ?>
</tbody></table></div><div class="table-foot">Menampilkan <?= count($items) ?> barang</div>
</section>
<?php require __DIR__ . '/../../partials/footer.php'; ?>
