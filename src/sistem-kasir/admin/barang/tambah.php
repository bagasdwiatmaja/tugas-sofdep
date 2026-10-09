<?php
require_once __DIR__ . '/../../app/helpers/bootstrap.php';
require_role('admin');
$pageTitle = 'Tambah Barang';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $idBarang = trim((string)($_POST['id_barang'] ?? ''));
    $nama = trim((string)($_POST['nama'] ?? ''));
    $merek = trim((string)($_POST['merek'] ?? ''));
    $harga = filter_var($_POST['harga'] ?? null, FILTER_VALIDATE_INT);
    $gudang = filter_var($_POST['stok_gudang'] ?? null, FILTER_VALIDATE_INT);
    $etalase = filter_var($_POST['stok_etalase'] ?? null, FILTER_VALIDATE_INT);
    if ($idBarang === '' || strlen($idBarang)>50) $errors[]='ID barang wajib diisi (maksimal 50 karakter).';
    if ($nama === '') $errors[]='Nama barang wajib diisi.';
    if ($harga === false || $harga < 0) $errors[]='Harga harus berupa angka nol atau lebih.';
    if ($gudang === false || $gudang < 0 || $etalase === false || $etalase < 0) $errors[]='Stok harus berupa bilangan bulat nol atau lebih.';
    if (!$errors) {
        try {
            $pdo->beginTransaction();
            $stmt=$pdo->prepare('INSERT INTO barang (id_barang,nama,merek,harga) VALUES (?,?,?,?)');
            $stmt->execute([$idBarang,$nama,$merek,$harga]);
            $barangId=(int)$pdo->lastInsertId();
            $pdo->prepare('INSERT INTO stok (barang_id,stok_gudang,stok_etalase) VALUES (?,?,?)')->execute([$barangId,$gudang,$etalase]);
            $pdo->prepare('INSERT INTO riwayat_stok (barang_id,user_id,lokasi,jenis,jumlah,catatan) VALUES (?,?,?,?,?,?)')->execute([$barangId,$_SESSION['user']['id'],'gudang','masuk',$gudang,'Stok awal saat barang dibuat']);
            $pdo->prepare('INSERT INTO riwayat_stok (barang_id,user_id,lokasi,jenis,jumlah,catatan) VALUES (?,?,?,?,?,?)')->execute([$barangId,$_SESSION['user']['id'],'etalase','masuk',$etalase,'Stok awal saat barang dibuat']);
            $pdo->commit();
            set_flash('success','Barang berhasil ditambahkan.');
            redirect('/sistem-kasir/admin/barang/index.php');
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $errors[] = $e->getCode()==='23000' ? 'ID barang sudah digunakan.' : 'Barang gagal disimpan.';
        }
    }
}
require __DIR__ . '/../../partials/header.php';
?>
<div class="form-page"><a class="back-link" href="index.php">← Kembali ke data barang</a><section class="panel form-panel"><div class="panel-heading"><div><h3>Informasi barang</h3><p>Lengkapi data produk yang akan didaftarkan.</p></div><span class="pill">FORM BARANG</span></div>
<?php foreach($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
<form method="post" class="form-grid"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<label>ID Barang <span class="required">*</span><input name="id_barang" value="<?= e($_POST['id_barang'] ?? '') ?>" placeholder="Contoh: BRG-001" required></label>
<label>Nama Barang <span class="required">*</span><input name="nama" value="<?= e($_POST['nama'] ?? '') ?>" placeholder="Nama produk" required></label>
<label>Merek<input name="merek" value="<?= e($_POST['merek'] ?? '') ?>" placeholder="Merek produk"></label>
<label>Harga Jual (Rp) <span class="required">*</span><input type="number" name="harga" min="0" step="1" value="<?= e($_POST['harga'] ?? '') ?>" placeholder="15000" required></label>
<div class="form-section-title">Pengaturan stok awal</div>
<label>Stok Gudang <span class="required">*</span><input type="number" name="stok_gudang" min="0" step="1" value="<?= e($_POST['stok_gudang'] ?? '0') ?>" required><small>Barang yang tersedia di penyimpanan.</small></label>
<label>Stok Etalase <span class="required">*</span><input type="number" name="stok_etalase" min="0" step="1" value="<?= e($_POST['stok_etalase'] ?? '0') ?>" required><small>Barang yang siap dijual kasir.</small></label>
<div class="form-actions"><a class="btn btn-ghost" href="index.php">Batal</a><button class="btn btn-primary" type="submit">Simpan barang →</button></div></form></section></div>
<?php require __DIR__ . '/../../partials/footer.php'; ?>
