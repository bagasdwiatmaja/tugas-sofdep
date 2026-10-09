<?php
require_once __DIR__ . '/../../app/helpers/bootstrap.php';
require_role('admin');
$pageTitle='Kelola Stok';$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $barangId=(int)($_POST['barang_id']??0);$lokasi=(string)($_POST['lokasi']??'');$jenis=(string)($_POST['jenis']??'');$jumlah=filter_var($_POST['jumlah']??null,FILTER_VALIDATE_INT);$catatan=trim((string)($_POST['catatan']??''));
    if(!in_array($jenis,['masuk','keluar','pindah_ke_etalase','pindah_ke_gudang'],true))$errors[]='Jenis perubahan stok tidak valid.';
    if(in_array($jenis,['masuk','keluar'],true) && !in_array($lokasi,['gudang','etalase'],true))$errors[]='Lokasi stok tidak valid.';
    if($jumlah===false||$jumlah<1)$errors[]='Jumlah harus minimal 1.';
    if(!$errors){
        try{
            $pdo->beginTransaction();
            $s=$pdo->prepare('SELECT s.*,b.nama FROM stok s JOIN barang b ON b.id=s.barang_id WHERE s.barang_id=? AND b.aktif=1 FOR UPDATE');$s->execute([$barangId]);$stock=$s->fetch();
            if(!$stock)throw new RuntimeException('Barang tidak ditemukan.');
            $field=$lokasi==='gudang'?'stok_gudang':'stok_etalase';
            if(in_array($jenis,['pindah_ke_etalase','pindah_ke_gudang'],true)){
                $from=$jenis==='pindah_ke_etalase'?'stok_gudang':'stok_etalase';$to=$jenis==='pindah_ke_etalase'?'stok_etalase':'stok_gudang';
                if((int)$stock[$from]<$jumlah)throw new RuntimeException('Stok asal tidak mencukupi.');
                $pdo->prepare("UPDATE stok SET {$from}={$from}-?, {$to}={$to}+? WHERE barang_id=?")->execute([$jumlah,$jumlah,$barangId]);
                $fromLabel=$jenis==='pindah_ke_etalase'?'gudang':'etalase';$toLabel=$jenis==='pindah_ke_etalase'?'etalase':'gudang';
                $pdo->prepare('INSERT INTO riwayat_stok (barang_id,user_id,lokasi,jenis,jumlah,catatan) VALUES (?,?,?,?,?,?)')->execute([$barangId,$_SESSION['user']['id'],$fromLabel,'keluar',$jumlah,'Pemindahan ke '.$toLabel.($catatan?': '.$catatan:'')]);
                $pdo->prepare('INSERT INTO riwayat_stok (barang_id,user_id,lokasi,jenis,jumlah,catatan) VALUES (?,?,?,?,?,?)')->execute([$barangId,$_SESSION['user']['id'],$toLabel,'masuk',$jumlah,'Pemindahan dari '.$fromLabel.($catatan?': '.$catatan:'')]);
            }else{
                if($jenis==='keluar'&&(int)$stock[$field]<$jumlah)throw new RuntimeException('Stok di lokasi tersebut tidak mencukupi.');
                $operator=$jenis==='masuk'?'+':'-';
                $pdo->prepare("UPDATE stok SET {$field}={$field}{$operator}? WHERE barang_id=?")->execute([$jumlah,$barangId]);
                $pdo->prepare('INSERT INTO riwayat_stok (barang_id,user_id,lokasi,jenis,jumlah,catatan) VALUES (?,?,?,?,?,?)')->execute([$barangId,$_SESSION['user']['id'],$lokasi,$jenis,$jumlah,$catatan?:'Perubahan stok manual']);
            }
            $pdo->commit();set_flash('success','Stok berhasil diperbarui.');redirect('/sistem-kasir/admin/stok/index.php');
        }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();$errors[]=$e instanceof RuntimeException?$e->getMessage():'Gagal memperbarui stok.';}
    }
}
$items=$pdo->query('SELECT b.id,b.id_barang,b.nama,b.merek,s.stok_gudang,s.stok_etalase FROM barang b JOIN stok s ON s.barang_id=b.id WHERE b.aktif=1 ORDER BY b.nama')->fetchAll();
$history=$pdo->query('SELECT r.*,b.nama,u.nama AS petugas FROM riwayat_stok r JOIN barang b ON b.id=r.barang_id JOIN users u ON u.id=r.user_id ORDER BY r.created_at DESC LIMIT 10')->fetchAll();
require __DIR__ . '/../../partials/header.php';
?>
<div class="content-grid stock-grid"><section class="panel"><div class="panel-heading"><div><h3>Ubah stok barang</h3><p>Tambah, kurangi, atau pindahkan stok antar lokasi.</p></div></div><?php foreach($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
<form method="post" class="form-stack"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Pilih Barang<select name="barang_id" required><option value="">Pilih barang...</option><?php foreach($items as $i): ?><option value="<?= (int)$i['id'] ?>"><?= e($i['id_barang'].' — '.$i['nama']) ?> (G: <?= (int)$i['stok_gudang'] ?> / E: <?= (int)$i['stok_etalase'] ?>)</option><?php endforeach; ?></select></label>
<label>Jenis perubahan<select name="jenis" id="stock-type" required><option value="masuk">Tambah stok</option><option value="keluar">Kurangi stok</option><option value="pindah_ke_etalase">Pindahkan gudang → etalase</option><option value="pindah_ke_gudang">Pindahkan etalase → gudang</option></select></label>
<label>Lokasi stok<select name="lokasi" id="stock-location"><option value="gudang">Gudang</option><option value="etalase">Etalase</option></select><small>Lokasi diabaikan untuk jenis pemindahan.</small></label>
<label>Jumlah<input type="number" name="jumlah" min="1" step="1" required placeholder="Masukkan jumlah"></label>
<label>Catatan (opsional)<textarea name="catatan" rows="2" placeholder="Contoh: restock dari pemasok"></textarea></label>
<button class="btn btn-primary btn-full">Simpan perubahan stok</button></form></section>
<section class="panel"><div class="panel-heading"><div><h3>Posisi stok</h3><p>Stok saat ini per lokasi.</p></div></div><div class="table-wrap"><table><thead><tr><th>Barang</th><th>Gudang</th><th>Etalase</th></tr></thead><tbody><?php foreach($items as $i): ?><tr><td><strong><?= e($i['nama']) ?></strong><small class="cell-sub"><?= e($i['id_barang']) ?></small></td><td><?= (int)$i['stok_gudang'] ?></td><td><?= (int)$i['stok_etalase'] ?></td></tr><?php endforeach; ?><?php if(!$items): ?><tr><td colspan="3">Belum ada barang.</td></tr><?php endif; ?></tbody></table></div></section></div>
<section class="panel section-gap"><div class="panel-heading"><div><h3>Riwayat stok</h3><p>Aktivitas stok terbaru.</p></div></div><div class="table-wrap"><table><thead><tr><th>Waktu</th><th>Barang</th><th>Lokasi</th><th>Aktivitas</th><th>Jumlah</th><th>Petugas</th></tr></thead><tbody><?php foreach($history as $h): ?><tr><td><?= date('d/m/Y H:i',strtotime($h['created_at'])) ?></td><td><?= e($h['nama']) ?></td><td><?= e(ucfirst($h['lokasi'])) ?></td><td><?= e(str_replace('_',' ',ucfirst($h['jenis']))) ?></td><td><?= (int)$h['jumlah'] ?></td><td><?= e($h['petugas']) ?></td></tr><?php endforeach; ?><?php if(!$history): ?><tr><td colspan="6">Belum ada riwayat stok.</td></tr><?php endif; ?></tbody></table></div></section>
<?php require __DIR__ . '/../../partials/footer.php'; ?>
