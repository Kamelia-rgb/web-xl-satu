<?php
require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/admin.php';

if (is_admin()) {
    redirect('admin/');
}

if ((int) db()->query('SELECT COUNT(*) FROM admin_users')->fetchColumn() === 0) {
    redirect('admin/setup.php');
}

$error = '';
$username = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    // Batasi percobaan login yang salah.
    $attempts = $_SESSION['login_attempts'] ?? ['count' => 0, 'since' => time()];
    if (time() - $attempts['since'] > 900) {
        $attempts = ['count' => 0, 'since' => time()];
    }

    if ($attempts['count'] >= 5) {
        $error = 'Terlalu banyak percobaan. Coba lagi 15 menit lagi.';
    } else {
        $stmt = db()->prepare('SELECT id, password_hash FROM admin_users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int) $user['id'];
            $_SESSION['admin_username'] = $username;
            unset($_SESSION['login_attempts']);
            redirect('admin/');
        }
        $attempts['count']++;
        $error = 'Username atau password salah.';
    }
    $_SESSION['login_attempts'] = $attempts;
}

admin_header('Masuk');
?>
<div class="auth-card">
  <h1>Masuk Admin</h1>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <?= csrf_field() ?>
    <div class="form-row"><label for="username">Username</label><input type="text" id="username" name="username" value="<?= e($username) ?>" required autocomplete="username" autofocus></div>
    <div class="form-row"><label for="password">Password</label><input type="password" id="password" name="password" required autocomplete="current-password"></div>
    <button class="btn btn-primary btn-block" type="submit">Masuk</button>
  </form>
  <p class="note mt-2"><a href="<?= e(url()) ?>">← Kembali ke website</a></p>
</div>
<?php admin_footer(); ?>
