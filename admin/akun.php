<?php
require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/admin.php';
require_admin();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';

    if ($action === 'password') {
        $stmt = db()->prepare('SELECT password_hash FROM admin_users WHERE id = ?');
        $stmt->execute([$_SESSION['admin_id']]);
        $hash = (string) $stmt->fetchColumn();
        $new = (string) ($_POST['baru'] ?? '');
        if (!password_verify((string) ($_POST['lama'] ?? ''), $hash)) {
            $errors[] = 'Password lama salah.';
        } elseif (strlen($new) < 8) {
            $errors[] = 'Password baru minimal 8 karakter.';
        } elseif ($new !== ($_POST['baru2'] ?? '')) {
            $errors[] = 'Konfirmasi password baru tidak sama.';
        } else {
            db()->prepare('UPDATE admin_users SET password_hash = ? WHERE id = ?')->execute([password_hash($new, PASSWORD_DEFAULT), $_SESSION['admin_id']]);
            flash('success', 'Password berhasil diganti.');
            redirect('admin/akun.php');
        }
    } elseif ($action === 'tambah') {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if (!preg_match('/^[a-zA-Z0-9_.]{3,50}$/', $username)) {
            $errors[] = 'Username 3–50 karakter, hanya huruf, angka, titik, atau garis bawah.';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Password minimal 8 karakter.';
        } else {
            $exists = db()->prepare('SELECT COUNT(*) FROM admin_users WHERE username = ?');
            $exists->execute([$username]);
            if ($exists->fetchColumn()) {
                $errors[] = 'Username sudah dipakai.';
            } else {
                db()->prepare('INSERT INTO admin_users (username, password_hash) VALUES (?, ?)')->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
                flash('success', 'Akun ' . $username . ' ditambahkan.');
                redirect('admin/akun.php');
            }
        }
    } elseif ($action === 'hapus') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id === (int) $_SESSION['admin_id']) {
            $errors[] = 'Tidak bisa menghapus akun yang sedang dipakai.';
        } else {
            db()->prepare('DELETE FROM admin_users WHERE id = ?')->execute([$id]);
            flash('success', 'Akun dihapus.');
            redirect('admin/akun.php');
        }
    }
}

$users = db()->query('SELECT id, username, created_at FROM admin_users ORDER BY id')->fetchAll();

admin_header('Akun', 'akun.php');
?>
<div class="admin-title"><h1>Akun Admin &amp; Sales</h1></div>
<?php if ($errors): ?><div class="alert alert-error"><?= implode('<br>', array_map('e', $errors)) ?></div><?php endif; ?>

<div class="grid grid-2">
  <form class="form-card" method="post">
    <?= csrf_field() ?><input type="hidden" name="action" value="password">
    <h2 class="h3">Ganti password saya</h2>
    <p class="note">Masuk sebagai <strong><?= e($_SESSION['admin_username'] ?? '') ?></strong></p>
    <div class="form-row"><label for="lama">Password lama</label><input type="password" id="lama" name="lama" required autocomplete="current-password"></div>
    <div class="form-row"><label for="baru">Password baru</label><input type="password" id="baru" name="baru" required minlength="8" autocomplete="new-password"></div>
    <div class="form-row"><label for="baru2">Ulangi password baru</label><input type="password" id="baru2" name="baru2" required minlength="8" autocomplete="new-password"></div>
    <button class="btn btn-primary" type="submit">Simpan Password</button>
  </form>

  <div class="form-card">
    <h2 class="h3">Akun yang bisa masuk</h2>
    <p class="note">Tambahkan akun untuk tiap sales supaya bisa melihat dan mengubah status calon pelanggan.</p>
    <ul class="user-list">
      <?php foreach ($users as $u): ?>
        <li>
          <span><strong><?= e($u['username']) ?></strong></span>
          <?php if ((int) $u['id'] !== (int) $_SESSION['admin_id']): ?>
            <form method="post" class="inline" onsubmit="return confirm('Hapus akun ini?')">
              <?= csrf_field() ?><input type="hidden" name="action" value="hapus"><input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
              <button class="btn btn-sm link-danger" type="submit">Hapus</button>
            </form>
          <?php else: ?><span class="note">(kamu)</span><?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
    <form method="post" class="mt-2">
      <?= csrf_field() ?><input type="hidden" name="action" value="tambah">
      <div class="form-row"><label for="username">Username baru</label><input type="text" id="username" name="username" required autocomplete="off"></div>
      <div class="form-row"><label for="password">Password</label><input type="password" id="password" name="password" required minlength="8" autocomplete="new-password"></div>
      <button class="btn btn-outline" type="submit">+ Tambah Akun</button>
    </form>
  </div>
</div>
<?php admin_footer(); ?>
