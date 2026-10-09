<?php
require_once __DIR__ . '/../app/helpers/bootstrap.php';

if (!empty($_SESSION['user'])) {
    redirect($_SESSION['user']['role'] === 'admin' ? '/sistem-kasir/admin/dashboard.php' : '/sistem-kasir/kasir/dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    $stmt = $pdo->prepare('SELECT id, nama, username, password, role FROM users WHERE username = ? AND aktif = 1 LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'nama' => $user['nama'],
            'username' => $user['username'],
            'role' => $user['role'],
        ];
        redirect($user['role'] === 'admin' ? '/sistem-kasir/admin/dashboard.php' : '/sistem-kasir/kasir/dashboard.php');
    }
    $error = 'Username atau password tidak sesuai.';
}
$pageTitle = 'Login';
require __DIR__ . '/../partials/header.php';
?>
<div class="login-layout">
    <section class="login-art">
        <div class="login-logo"><span class="brand-mark">K</span> KasirKu</div>
        <div class="login-art-content">
            <span class="pill pill-white">POINT OF SALE SYSTEM</span>
            <h2>Kelola penjualan<br>lebih <em>mudah.</em></h2>
            <p>Satu tempat untuk transaksi, inventori, dan laporan usaha Anda.</p>
            <div class="art-stats"><div><strong>01</strong><span>Terintegrasi</span></div><div><strong>02</strong><span>Terstruktur</span></div><div><strong>03</strong><span>Efisien</span></div></div>
        </div>
        <div class="art-orb orb-one"></div><div class="art-orb orb-two"></div>
        <small class="login-copyright">KASIRKU · INTERNAL BUSINESS TOOLS</small>
    </section>
    <section class="login-form-wrap">
        <div class="login-form">
            <span class="eyebrow">SELAMAT DATANG KEMBALI</span>
            <h1>Masuk ke akun</h1>
            <p class="muted">Masukkan akun Anda untuk melanjutkan.</p>
            <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
            <form method="post" class="form-stack">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <label>Username<input name="username" autocomplete="username" placeholder="Masukkan username" required autofocus></label>
                <label>Password<input type="password" name="password" autocomplete="current-password" placeholder="Masukkan password" required></label>
                <button class="btn btn-primary btn-full" type="submit">Masuk ke dashboard <span>→</span></button>
            </form>
            <div class="login-help"><span class="help-icon">i</span><p>Akun awal admin dibuat melalui langkah instalasi di README.md. Demi keamanan, ganti password setelah login pertama.</p></div>
        </div>
    </section>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
