<?php
/**
 * Membuat akun admin pertama. Hanya bisa dipakai selama belum ada akun admin.
 */
require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/admin.php';

try {
    $count = (int) db()->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
} catch (PDOException $ex) {
    admin_header('Setup');
    echo '<div class="auth-card"><h1>Database belum siap</h1><p>Pastikan data database di <code>config.php</code> sudah benar dan file <code>database.sql</code> sudah di-import lewat phpMyAdmin.</p></div>';
    admin_footer();
    exit;
}

if ($count > 0) {
    redirect('admin/login.php');
}

$error = '';
$username = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['password2'] ?? '');

    if (!preg_match('/^[a-zA-Z0-9_.]{3,50}$/', $username)) {
        $error = 'Username 3–50 karakter, hanya huruf, angka, titik, atau garis bawah.';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif ($password !== $confirm) {
        $error = 'Konfirmasi password tidak sama.';
    } else {
        $stmt = db()->prepare('INSERT INTO admin_users (username, password_hash) VALUES (?, ?)');
        $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
        flash('success', 'Akun admin berhasil dibuat. Silakan masuk.');
        redirect('admin/login.php');
    }
}

admin_header('Buat Akun Admin');
?>
<div class="auth-card">
  <h1>Buat Akun Admin</h1>
  <p class="note">Langkah ini hanya muncul sekali. Simpan username dan password dengan aman.</p>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <?= csrf_field() ?>
    <div class="form-row"><label for="username">Username</label><input type="text" id="username" name="username" value="<?= e($username) ?>" required autocomplete="username"></div>
    <div class="form-row"><label for="password">Password</label><input type="password" id="password" name="password" required minlength="8" autocomplete="new-password"></div>
    <div class="form-row"><label for="password2">Ulangi password</label><input type="password" id="password2" name="password2" required minlength="8" autocomplete="new-password"></div>
    <button class="btn btn-primary btn-block" type="submit">Buat Akun</button>
  </form>
</div>
<?php admin_footer(); ?>
