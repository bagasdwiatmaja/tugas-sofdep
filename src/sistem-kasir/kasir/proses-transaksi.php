<?php
require_once __DIR__ . '/../app/helpers/bootstrap.php';
require_role('kasir');
if($_SERVER['REQUEST_METHOD']!=='POST')redirect('/sistem-kasir/kasir/transaksi.php');
verify_csrf();
$raw=json_decode((string)($_POST['items']??''),true);
$bayar=filter_var($_POST['bayar']??null,FILTER_VALIDATE_INT);
if(!is_array($raw)||!count($raw)||$bayar===false||$bayar<0){set_flash('danger','Keranjang atau jumlah pembayaran tidak valid.');redirect('/sistem-kasir/kasir/transaksi.php');}
$items=[];
foreach($raw as $item){$id=filter_var($item['id']??null,FILTER_VALIDATE_INT);$qty=filter_var($item['qty']??null,FILTER_VALIDATE_INT);if(!$id||!$qty||$qty<1||$qty>10000){set_flash('danger','Data barang pada keranjang tidak valid.');redirect('/sistem-kasir/kasir/transaksi.php');}$items[$id]=($items[$id]??0)+$qty;}
try{
    $pdo->beginTransaction();$details=[];$total=0;
    $stockStmt=$pdo->prepare('SELECT b.id,b.id_barang,b.nama,b.harga,s.stok_etalase FROM barang b JOIN stok s ON s.barang_id=b.id WHERE b.id=? AND b.aktif=1 FOR UPDATE');
    foreach($items as $id=>$qty){$stockStmt->execute([$id]);$p=$stockStmt->fetch();if(!$p)throw new RuntimeException('Ada barang yang tidak tersedia.');if((int)$p['stok_etalase']<$qty)throw new RuntimeException('Stok etalase '.$p['nama'].' tidak mencukupi.');$subtotal=(int)$p['harga']*$qty;$total+=$subtotal;$details[]=['product'=>$p,'qty'=>$qty,'subtotal'=>$subtotal];}
    if($bayar<$total)throw new RuntimeException('Uang pembayaran kurang dari total belanja.');
    $kode='TRX-'.date('Ymd-His').'-'.strtoupper(bin2hex(random_bytes(2)));
    $stmt=$pdo->prepare('INSERT INTO transaksi (kode_transaksi,user_id,total,bayar,kembalian,status) VALUES (?,?,?,?,?,"selesai")');$stmt->execute([$kode,$_SESSION['user']['id'],$total,$bayar,$bayar-$total]);$transaksiId=(int)$pdo->lastInsertId();
    foreach($details as $d){$p=$d['product'];$pdo->prepare('INSERT INTO detail_transaksi (transaksi_id,barang_id,nama_barang,harga,qty,subtotal) VALUES (?,?,?,?,?,?)')->execute([$transaksiId,$p['id'],$p['nama'],$p['harga'],$d['qty'],$d['subtotal']]);$pdo->prepare('UPDATE stok SET stok_etalase=stok_etalase-? WHERE barang_id=?')->execute([$d['qty'],$p['id']]);$pdo->prepare('INSERT INTO riwayat_stok (barang_id,user_id,lokasi,jenis,jumlah,catatan) VALUES (?,?,?,?,?,?)')->execute([$p['id'],$_SESSION['user']['id'],'etalase','keluar',$d['qty'],'Penjualan '.$kode]);}
    $pdo->commit();redirect('/sistem-kasir/kasir/struk.php?id='.$transaksiId);
}catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();set_flash('danger',$e instanceof RuntimeException?$e->getMessage():'Transaksi gagal diproses. Silakan coba kembali.');redirect('/sistem-kasir/kasir/transaksi.php');}
