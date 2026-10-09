<?php
require_once __DIR__ . '/../../app/helpers/bootstrap.php';
require_role('admin');
if($_SERVER['REQUEST_METHOD']!=='POST') redirect('/sistem-kasir/admin/barang/index.php');
verify_csrf();
$id=(int)($_POST['id']??0);
$stmt=$pdo->prepare('UPDATE barang SET aktif=0 WHERE id=?');$stmt->execute([$id]);
set_flash($stmt->rowCount()?'success':'warning',$stmt->rowCount()?'Barang berhasil dinonaktifkan.':'Barang tidak ditemukan.');
redirect('/sistem-kasir/admin/barang/index.php');
