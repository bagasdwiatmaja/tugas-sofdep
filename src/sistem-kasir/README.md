# KasirKu — Sistem Kasir PHP Native + MySQL

Aplikasi kasir sederhana dengan dua role: **admin** dan **kasir**. Dibuat menggunakan PHP native, PDO, MySQL, HTML, CSS, dan JavaScript.

## Fitur

### Admin
- Dashboard ringkasan penjualan dan stok menipis.
- CRUD data barang (ID, nama, merek, harga).
- Stok gudang dan etalase terpisah.
- Menambah/mengurangi stok serta memindahkan stok antar lokasi.
- Membuat akun pengguna kasir.
- Laporan penjualan berdasarkan rentang tanggal.

### Kasir
- Pencarian barang berdasarkan ID, nama, dan merek.
- Keranjang: tambah, kurangi, dan hapus barang.
- Perhitungan total, pembayaran, dan kembalian.
- Checkout dengan validasi stok di server.
- Stok etalase berkurang otomatis setelah transaksi berhasil.
- Cetak struk dan riwayat transaksi.

## Persyaratan
- XAMPP (Apache + MySQL/MariaDB)
- PHP 8.1+ disarankan
- Browser modern
- Git untuk upload ke GitHub

## Instalasi lokal (Windows + XAMPP)

1. Ekstrak folder `sistem-kasir` ke `C:\xampp\htdocs\`.
2. Jalankan **Apache** dan **MySQL** melalui XAMPP Control Panel.
3. Buka `http://localhost/phpmyadmin`.
4. Pilih tab **Import**, pilih file `database/sistem_kasir.sql`, lalu klik **Import/Go**. File SQL juga membuat database `sistem_kasir`.
5. Pastikan pengaturan koneksi di `app/config/database.php` sesuai XAMPP:
   - host: `localhost`
   - database: `sistem_kasir`
   - username: `root`
   - password: kosong (default XAMPP lokal)
6. Buka `http://localhost/sistem-kasir/tools/setup_admin.php` satu kali untuk membuat akun admin awal.
7. Login melalui `http://localhost/sistem-kasir/auth/login.php`.

### Akun admin awal
- Username: `admin`
- Password: `AdminKasir123!`

**Penting:** ini hanya kredensial pengembangan lokal. Setelah akun berhasil dibuat, hapus file `tools/setup_admin.php`. Versi awal ini belum menyediakan halaman ubah password; untuk penggunaan nyata, tambahkan fitur ubah password dan ganti password awal langsung di database menggunakan `password_hash()` atau buat akun admin baru yang aman.

## Cara kerja stok
- Penjualan mengambil stok dari **etalase**, bukan gudang.
- Admin dapat memindahkan barang dari gudang ke etalase.
- Penambahan/pengurangan stok dan transaksi dicatat pada tabel `riwayat_stok`.
- Barang yang dihapus dari daftar dinonaktifkan (soft delete) agar riwayat transaksi tetap terjaga.

## Upload ke GitHub

Jalankan terminal VS Code pada folder `C:\xampp\htdocs\sistem-kasir`:

```bash
git init
git add .
git commit -m "Initial commit: sistem kasir PHP native"
git branch -M main
```

Buat repository baru di GitHub (misalnya `sistem-kasir`), lalu hubungkan remote dari instruksi GitHub:

```bash
git remote add origin https://github.com/USERNAME/sistem-kasir.git
git push -u origin main
```

Ganti `USERNAME` dengan username GitHub Anda. Jangan pernah commit password database asli, token, atau kredensial sensitif.

## Struktur folder

```text
app/                 Konfigurasi database, helper, middleware
admin/               Dashboard dan fitur admin
kasir/               Dashboard, transaksi, struk, riwayat
auth/                Login dan logout
assets/               CSS dan JavaScript
database/             Skema SQL
partials/             Komponen header dan footer
tools/                Setup akun admin lokal (hapus setelah dipakai)
```

## Catatan
- Aplikasi ini merupakan starter project untuk pembelajaran dan belum diaudit untuk deployment produksi.
- Untuk produksi, gunakan konfigurasi environment, HTTPS, pembatasan percobaan login, audit keamanan, dan proses backup.
