<?php
require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/admin.php';
require_admin();

$empty = ['id' => 0, 'judul' => '', 'deskripsi' => '', 'gambar' => '', 'berlaku_sampai' => '', 'tampil_banner' => 1, 'aktif' => 1, 'urutan' => 0];
$errors = [];
$editing = null;

function find_promo(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM promo WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? 'save';
    $id = (int) ($_POST['id'] ?? 0);
    $current = $id ? find_promo($id) : null;

    if ($action === 'delete') {
        if ($current) {
            db()->prepare('DELETE FROM promo WHERE id = ?')->execute([$id]);
            delete_uploaded_image($current['gambar']);
        }
        flash('success', 'Promo dihapus.');
        redirect('admin/promo.php');
    }

    $data = [
        'judul'          => trim((string) ($_POST['judul'] ?? '')),
        'deskripsi'      => trim((string) ($_POST['deskripsi'] ?? '')),
        'gambar'         => $current['gambar'] ?? null,
        'berlaku_sampai' => preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($_POST['berlaku_sampai'] ?? '')) ? $_POST['berlaku_sampai'] : null,
        'tampil_banner'  => !empty($_POST['tampil_banner']) ? 1 : 0,
        'aktif'          => !empty($_POST['aktif']) ? 1 : 0,
        'urutan'         => (int) ($_POST['urutan'] ?? 0),
    ];
    if ($data['judul'] === '' || mb_strlen($data['judul']) > 150) {
        $errors[] = 'Judul promo wajib diisi (maks. 150 karakter).';
    }

    if (!$errors) {
        try {
            $newImage = save_uploaded_image('gambar');
            if ($newImage) {
                delete_uploaded_image($data['gambar']);
                $data['gambar'] = $newImage;
            } elseif (!empty($_POST['hapus_gambar'])) {
                delete_uploaded_image($data['gambar']);
                $data['gambar'] = null;
            }
        } catch (RuntimeException $ex) {
            $errors[] = $ex->getMessage();
        }
    }

    if (!$errors) {
        $cols = array_keys($data);
        if ($current) {
            $sql = 'UPDATE promo SET ' . implode(', ', array_map(function ($c) { return "$c = ?"; }, $cols)) . ' WHERE id = ?';
            db()->prepare($sql)->execute(array_merge(array_values($data), [$id]));
            flash('success', 'Promo diperbarui.');
        } else {
            $sql = 'INSERT INTO promo (' . implode(', ', $cols) . ') VALUES (' . implode(', ', array_fill(0, count($cols), '?')) . ')';
            db()->prepare($sql)->execute(array_values($data));
            flash('success', 'Promo ditambahkan.');
        }
        redirect('admin/promo.php');
    }
    $editing = ['id' => $id] + $data;
}

if (!$editing && isset($_GET['edit'])) {
    $editing = find_promo((int) $_GET['edit']);
}
if (!$editing && isset($_GET['baru'])) {
    $editing = $empty;
}

admin_header('Promo & Banner', 'promo.php');

if ($editing):
?>
<div class="admin-title">
  <h1><?= $editing['id'] ? 'Ubah Promo' : 'Tambah Promo' ?></h1>
  <a class="btn btn-outline btn-sm" href="<?= e(url('admin/promo.php')) ?>">← Kembali</a>
</div>
<?php if ($errors): ?><div class="alert alert-error"><?= implode('<br>', array_map('e', $errors)) ?></div><?php endif; ?>
<form class="form-card admin-form" method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= (int) $editing['id'] ?>">
  <div class="form-row"><label for="judul">Judul promo</label><input type="text" id="judul" name="judul" value="<?= e($editing['judul']) ?>" required maxlength="150"></div>
  <div class="form-row"><label for="deskripsi">Deskripsi / syarat &amp; ketentuan</label><textarea id="deskripsi" name="deskripsi" rows="4"><?= e($editing['deskripsi']) ?></textarea></div>
  <div class="form-row">
    <label for="gambar">Gambar banner</label>
    <?php if (!empty($editing['gambar'])): ?>
      <img class="thumb-lg" src="<?= e(url($editing['gambar'])) ?>" alt="">
      <label class="inline-check"><input type="checkbox" name="hapus_gambar" value="1"> Hapus gambar ini</label>
    <?php endif; ?>
    <input type="file" id="gambar" name="gambar" accept="image/jpeg,image/png,image/webp">
    <p class="hint">JPG/PNG/WEBP, maks. 3 MB. Ukuran ideal 1200 × 450 piksel (perbandingan 8:3).</p>
  </div>
  <div class="form-row-2">
    <div class="form-row"><label for="berlaku_sampai">Berlaku sampai (opsional)</label><input type="date" id="berlaku_sampai" name="berlaku_sampai" value="<?= e($editing['berlaku_sampai']) ?>"><p class="hint">Setelah tanggal ini promo otomatis disembunyikan.</p></div>
    <div class="form-row"><label for="urutan">Urutan tampil</label><input type="number" id="urutan" name="urutan" value="<?= e($editing['urutan']) ?>"></div>
  </div>
  <div class="form-row checks">
    <label><input type="checkbox" name="aktif" value="1"<?= $editing['aktif'] ? ' checked' : '' ?>> Tampilkan di website</label>
    <label><input type="checkbox" name="tampil_banner" value="1"<?= $editing['tampil_banner'] ? ' checked' : '' ?>> Tampilkan sebagai banner slider di halaman depan</label>
  </div>
  <button class="btn btn-primary" type="submit">Simpan Promo</button>
</form>
<?php
else:
    $promo = get_promo(false);
?>
<div class="admin-title">
  <h1>Promo &amp; Banner</h1>
  <a class="btn btn-primary btn-sm" href="<?= e(url('admin/promo.php?baru=1')) ?>">+ Tambah Promo</a>
</div>
<div class="table-wrap">
  <table class="table">
    <thead><tr><th>Banner</th><th>Promo</th><th>Berlaku sampai</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($promo as $p):
        $expired = $p['berlaku_sampai'] && $p['berlaku_sampai'] < date('Y-m-d'); ?>
      <tr>
        <td><?php if ($p['gambar']): ?><img class="thumb" src="<?= e(url($p['gambar'])) ?>" alt=""><?php endif; ?></td>
        <td><strong><?= e($p['judul']) ?></strong></td>
        <td><?= $p['berlaku_sampai'] ? e(tanggal_indo($p['berlaku_sampai'])) : '—' ?></td>
        <td><?= !$p['aktif'] ? '<span class="note">Disembunyikan</span>' : ($expired ? '<span class="note">Kedaluwarsa</span>' : 'Tampil') ?></td>
        <td class="actions">
          <a class="btn btn-outline btn-sm" href="<?= e(url('admin/promo.php?edit=' . (int) $p['id'])) ?>">Ubah</a>
          <form method="post" class="inline" onsubmit="return confirm('Hapus promo ini?')">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
            <button class="btn btn-sm link-danger" type="submit" name="action" value="delete">Hapus</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$promo): ?><tr><td colspan="5">Belum ada promo.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php
endif;
admin_footer();
