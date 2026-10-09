<?php
require_once __DIR__ . '/../../app/helpers/bootstrap.php';
require_role('admin');
$pageTitle='Pengguna Kasir';$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();$nama=trim((string)($_POST['nama']??''));$username=trim((string)($_POST['username']??''));$password=(string)($_POST['password']??'');
    if($nama===''||$username==='')$errors[]='Nama dan username wajib diisi.';
    if(strlen($password)<8)$errors[]='Password minimal 8 karakter.';
    if(!$errors){try{$s=$pdo->prepare("INSERT INTO users (nama,username,password,role) VALUES (?,?,?,'kasir')");$s->execute([$nama,$username,password_hash($password,PASSWORD_DEFAULT)]);set_flash('success','Akun kasir berhasil dibuat.');redirect('/sistem-kasir/admin/users/index.php');}catch(PDOException $e){$errors[]=$e->getCode()==='23000'?'Username sudah digunakan.':'Akun gagal dibuat.';}}
}
$users=$pdo->query("SELECT id,nama,username,aktif,created_at FROM users WHERE role='kasir' ORDER BY created_at DESC")->fetchAll();
require __DIR__ . '/../../partials/header.php';
?>
<div class="content-grid user-grid"><section class="panel"><div class="panel-heading"><div><h3>Tambah akun kasir</h3><p>Buat akses login untuk staf kasir.</p></div></div><?php foreach($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
<form method="post" class="form-stack"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Nama lengkap<input name="nama" required value="<?= e($_POST['nama']??'') ?>" placeholder="Nama kasir"></label><label>Username<input name="username" required value="<?= e($_POST['username']??'') ?>" placeholder="username_kasir"></label><label>Password<input type="password" name="password" minlength="8" required placeholder="Minimal 8 karakter"></label><button class="btn btn-primary btn-full">＋ Buat akun kasir</button></form></section>
<section class="panel"><div class="panel-heading"><div><h3>Daftar kasir</h3><p><?= count($users) ?> akun terdaftar.</p></div></div><div class="table-wrap"><table><thead><tr><th>Nama</th><th>Username</th><th>Status</th></tr></thead><tbody><?php foreach($users as $u): ?><tr><td><strong><?= e($u['nama']) ?></strong></td><td><?= e($u['username']) ?></td><td><span class="badge <?= $u['aktif']?'badge-success':'badge-muted' ?>"><?= $u['aktif']?'Aktif':'Nonaktif' ?></span></td></tr><?php endforeach; ?><?php if(!$users): ?><tr><td colspan="3">Belum ada akun kasir.</td></tr><?php endif; ?></tbody></table></div></section></div>
<?php require __DIR__ . '/../../partials/footer.php'; ?>
