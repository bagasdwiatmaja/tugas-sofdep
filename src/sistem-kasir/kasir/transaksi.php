<?php
require_once __DIR__ . '/../app/helpers/bootstrap.php';
require_role('kasir');
$pageTitle='Transaksi Baru';
$products=$pdo->query('SELECT b.id,b.id_barang,b.nama,b.merek,b.harga,s.stok_etalase FROM barang b JOIN stok s ON s.barang_id=b.id WHERE b.aktif=1 AND s.stok_etalase>0 ORDER BY b.nama')->fetchAll();
require __DIR__ . '/../partials/header.php';
?>
<div class="pos-layout">
<section class="panel pos-products"><div class="panel-heading"><div><h3>Pilih barang</h3><p>Cari barang yang dibeli pelanggan.</p></div><span class="pill"><?= count($products) ?> PRODUK</span></div>
<div class="search-input pos-search"><span>⌕</span><input id="product-search" placeholder="Cari nama, merek, atau ID barang..."></div>
<div class="product-grid" id="product-grid">
<?php foreach($products as $p): ?><button type="button" class="product-card" data-product data-id="<?= (int)$p['id'] ?>" data-code="<?= e($p['id_barang']) ?>" data-name="<?= e($p['nama']) ?>" data-brand="<?= e($p['merek']) ?>" data-price="<?= (int)$p['harga'] ?>" data-stock="<?= (int)$p['stok_etalase'] ?>">
<span class="product-thumb">▤</span><span class="product-name"><?= e($p['nama']) ?></span><small><?= e($p['merek'] ?: $p['id_barang']) ?></small><strong><?= rupiah($p['harga']) ?></strong><span class="stock-label">Stok: <?= (int)$p['stok_etalase'] ?></span><span class="add-product">＋</span></button><?php endforeach; ?>
</div><div id="no-products" class="empty-state hidden">Barang tidak ditemukan atau stok etalase kosong.</div>
</section>
<section class="panel cart-panel"><div class="panel-heading"><div><h3>Keranjang belanja</h3><p id="cart-count">0 barang dipilih</p></div><button type="button" class="text-button" id="clear-cart">Kosongkan</button></div>
<div id="cart-items" class="cart-items"><div class="cart-empty"><div class="cart-empty-icon">🛒</div><strong>Keranjang masih kosong</strong><p>Pilih barang dari daftar untuk memulai transaksi.</p></div></div>
<div class="cart-summary"><div><span>Subtotal</span><strong id="subtotal">Rp 0</strong></div><div class="total-line"><span>Total belanja</span><strong id="total">Rp 0</strong></div>
<form method="post" action="proses-transaksi.php" id="checkout-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="items" id="cart-data"><label>Uang dibayar (Rp)<input type="number" name="bayar" id="payment" min="0" step="1" placeholder="Masukkan nominal pembayaran" required></label><div class="change-line"><span>Kembalian</span><strong id="change">Rp 0</strong></div><button class="btn btn-primary btn-full checkout-btn" type="submit" id="checkout" disabled>Proses pembayaran →</button></form>
<p class="secure-note">Stok etalase diperbarui otomatis setelah transaksi berhasil.</p></div></section>
</div>
<script>window.KASIR_PRODUCTS = <?= json_encode($products, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>;</script>
<?php require __DIR__ . '/../partials/footer.php'; ?>
