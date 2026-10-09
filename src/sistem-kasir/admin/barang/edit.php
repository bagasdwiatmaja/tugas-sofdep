<?php
require_once __DIR__ . '/../../app/helpers/bootstrap.php';
require_role('admin');
$id=(int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt=$pdo->prepare('SELECT * FROM barang WHERE id=? AND aktif=1'); $stmt->execute([$id]); $barang=$stmt->fetch();
if(!$barang){set_flash('danger','Barang tidak ditemukan.');redirect('/sistem-kasir/admin/barang/index.php');}
$pageTitle='Edit Barang'; $errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $idBarang=trim((string)($_POST['id_barang']??''));$nama=trim((string)($_POST['nama']??''));$merek=trim((string)($_POST['merek']??''));$harga=filter_var($_POST['harga']??null,FILTER_VALIDATE_INT);
    if($idBarang===''||$nama==='')$errors[]='ID barang dan nama wajib diisi.';
    if($harga===false||$harga<0)$errors[]='Harga harus angka nol atau lebih.';
    if(!$errors){try{$s=$pdo->prepare('UPDATE barang SET id_barang=?,nama=?,merek=?,harga=? WHERE id=?');$s->execute([$idBarang,$nama,$merek,$harga,$id]);set_flash('success','Data barang berhasil diperbarui.');redirect('/sistem-kasir/admin/barang/index.php');}catch(PDOException $e){$errors[]=$e->getCode()==='23000'?'ID barang sudah digunakan.':'Data gagal diperbarui.';}}
    $barang=array_merge($barang,['id_barang'=>$idBarang,'nama'=>$nama,'merek'=>$merek,'harga'=>$_POST['harga']??'']);
}
require __DIR__ . '/../../partials/header.php';
?>
<div class="form-page"><a class="back-link" href="index.php">← Kembali ke data barang</a><section class="panel form-panel"><div class="panel-heading"><div><h3>Edit informasi barang</h3><p>Perbarui ID, nama, merek, atau harga jual.</p></div></div><?php foreach($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
<form method="post" class="form-grid"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= $id ?>">
<label>ID Barang<input name="id_barang" value="<?= e($barang['id_barang']) ?>" required></label><label>Nama Barang<input name="nama" value="<?= e($barang['nama']) ?>" required></label><label>Merek<input name="merek" value="<?= e($barang['merek']) ?>"></label><label>Harga Jual (Rp)<input type="number" name="harga" min="0" value="<?= e((string)$barang['harga']) ?>" required></label>
<div class="form-actions"><a class="btn btn-ghost" href="index.php">Batal</a><button class="btn btn-primary">Simpan perubahan →</button></div></form></section></div>
<?php require __DIR__ . '/../../partials/footer.php'; ?>
