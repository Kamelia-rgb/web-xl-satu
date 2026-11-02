<?php
/** Tampilan & helper khusus halaman admin. */

function admin_header(string $title, string $active = ''): void
{
    $menu = ['index.php' => 'Calon Pelanggan', 'paket.php' => 'Paket', 'promo.php' => 'Promo', 'akun.php' => 'Akun'];
    ?><!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($title) ?> – Admin <?= e(config('site_name')) ?></title>
<link rel="icon" href="<?= e(url('assets/img/favicon.png')) ?>" type="image/png">
<link rel="apple-touch-icon" href="<?= e(url('assets/img/apple-touch-icon.png')) ?>">
<link rel="stylesheet" href="<?= e(asset('assets/css/style.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('assets/css/admin.css')) ?>">
</head>
<body class="admin">
<?php if (is_admin()): ?>
<header class="admin-header">
  <div class="container admin-header-inner">
    <a class="brand" href="<?= e(url('admin/')) ?>"><img class="brand-logo" src="<?= e(asset('assets/img/logo-xl-satu-putih.png')) ?>" alt="<?= e(config('site_name')) ?>" width="102" height="30"> <span class="admin-tag">Admin</span></a>
    <nav class="admin-nav">
      <?php foreach ($menu as $file => $label): ?>
        <a href="<?= e(url('admin/' . $file)) ?>"<?= $active === $file ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
      <?php endforeach; ?>
      <a href="<?= e(url()) ?>" target="_blank" rel="noopener">Lihat Web ↗</a>
      <form method="post" action="<?= e(url('admin/logout.php')) ?>" class="inline">
        <?= csrf_field() ?><button type="submit" class="link-btn">Keluar</button>
      </form>
    </nav>
  </div>
</header>
<?php endif; ?>
<main class="admin-main container">
<?php foreach (flashes() as $f): ?>
  <div class="alert alert-<?= e($f['type']) ?>"><?= e($f['message']) ?></div>
<?php endforeach;
}

function admin_footer(): void
{
    ?>
</main>
</body>
</html>
<?php
}

/**
 * Simpan gambar upload ke uploads/promo dan kembalikan path-nya.
 * Mengembalikan null bila tidak ada file. Melempar RuntimeException bila file tidak valid.
 */
function save_uploaded_image(string $field): ?string
{
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Gagal mengunggah gambar (kode ' . (int) $file['error'] . '). Ukuran maksimal biasanya 2 MB.');
    }
    if ($file['size'] > 3 * 1024 * 1024) {
        throw new RuntimeException('Ukuran gambar maksimal 3 MB.');
    }
    $info = @getimagesize($file['tmp_name']);
    $allowed = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    if (!$info || !isset($allowed[$info[2]])) {
        throw new RuntimeException('Format gambar harus JPG, PNG, atau WEBP.');
    }
    $name = date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$info[2]];
    $dir = __DIR__ . '/../uploads/promo';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
        throw new RuntimeException('Gagal menyimpan gambar. Cek izin folder uploads/promo.');
    }
    return 'uploads/promo/' . $name;
}

/** Hapus file gambar hasil upload (gambar contoh bawaan tidak dihapus). */
function delete_uploaded_image(?string $path): void
{
    if ($path && str_starts_with($path, 'uploads/promo/')) {
        $full = __DIR__ . '/../' . $path;
        if (is_file($full)) {
            unlink($full);
        }
    }
}
