<?php
require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/admin.php';
require_admin();

$empty = ['id' => 0, 'nama' => '', 'kecepatan' => '', 'harga' => '', 'harga_coret' => '', 'benefit' => '', 'label' => '', 'unggulan' => 0, 'aktif' => 1, 'urutan' => 0];
$errors = [];
$editing = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? 'save';
    $id = (int) ($_POST['id'] ?? 0);

    if ($action === 'delete') {
        db()->prepare('DELETE FROM paket WHERE id = ?')->execute([$id]);
        flash('success', 'Paket dihapus.');
        redirect('admin/paket.php');
    }

    $data = [
        'nama'        => trim((string) ($_POST['nama'] ?? '')),
        'kecepatan'   => (int) ($_POST['kecepatan'] ?? 0),
        'harga'       => (int) preg_replace('/\D/', '', (string) ($_POST['harga'] ?? '')),
        'harga_coret' => (int) preg_replace('/\D/', '', (string) ($_POST['harga_coret'] ?? '')) ?: null,
        'benefit'     => trim((string) ($_POST['benefit'] ?? '')),
        'label'       => trim((string) ($_POST['label'] ?? '')) ?: null,
        'unggulan'    => !empty($_POST['unggulan']) ? 1 : 0,
        'aktif'       => !empty($_POST['aktif']) ? 1 : 0,
        'urutan'      => (int) ($_POST['urutan'] ?? 0),
    ];

    if ($data['nama'] === '' || mb_strlen($data['nama']) > 100) $errors[] = 'Nama paket wajib diisi (maks. 100 karakter).';
    if ($data['kecepatan'] <= 0) $errors[] = 'Kecepatan harus lebih dari 0.';
    if ($data['harga'] <= 0) $errors[] = 'Harga harus diisi.';
    if ($data['label'] !== null && mb_strlen($data['label']) > 40) $errors[] = 'Label maksimal 40 karakter.';

    if (!$errors) {
        if ($data['unggulan']) {
            // Hanya satu paket unggulan yang tampil di halaman depan.
            db()->prepare('UPDATE paket SET unggulan = 0 WHERE id <> ?')->execute([$id]);
        }
        $cols = array_keys($data);
        if ($id > 0) {
            $sql = 'UPDATE paket SET ' . implode(', ', array_map(fn ($c) => "$c = ?", $cols)) . ' WHERE id = ?';
            db()->prepare($sql)->execute([...array_values($data), $id]);
            flash('success', 'Paket diperbarui.');
        } else {
            $sql = 'INSERT INTO paket (' . implode(', ', $cols) . ') VALUES (' . implode(', ', array_fill(0, count($cols), '?')) . ')';
            db()->prepare($sql)->execute(array_values($data));
            flash('success', 'Paket ditambahkan.');
        }
        redirect('admin/paket.php');
    }
    $editing = ['id' => $id] + $data;
}

if (!$editing && isset($_GET['edit'])) {
    $stmt = db()->prepare('SELECT * FROM paket WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $editing = $stmt->fetch() ?: null;
}
if (!$editing && isset($_GET['baru'])) {
    $editing = $empty;
}

admin_header('Paket Internet', 'paket.php');

if ($editing):
?>
<div class="admin-title">
  <h1><?= $editing['id'] ? 'Ubah Paket' : 'Tambah Paket' ?></h1>
  <a class="btn btn-outline btn-sm" href="<?= e(url('admin/paket.php')) ?>">← Kembali</a>
</div>
<?php if ($errors): ?><div class="alert alert-error"><?= implode('<br>', array_map('e', $errors)) ?></div><?php endif; ?>
<form class="form-card admin-form" method="post">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= (int) $editing['id'] ?>">
  <div class="form-row-2">
    <div class="form-row"><label for="nama">Nama paket</label><input type="text" id="nama" name="nama" value="<?= e($editing['nama']) ?>" required maxlength="100" placeholder="Paket Keluarga"></div>
    <div class="form-row"><label for="kecepatan">Kecepatan (Mbps)</label><input type="number" id="kecepatan" name="kecepatan" value="<?= e($editing['kecepatan']) ?>" required min="1"></div>
  </div>
  <div class="form-row-2">
    <div class="form-row"><label for="harga">Harga per bulan (Rp)</label><input type="text" inputmode="numeric" id="harga" name="harga" value="<?= e($editing['harga']) ?>" required placeholder="289000"></div>
    <div class="form-row"><label for="harga_coret">Harga coret / normal (opsional)</label><input type="text" inputmode="numeric" id="harga_coret" name="harga_coret" value="<?= e($editing['harga_coret']) ?>" placeholder="339000"></div>
  </div>
  <div class="form-row">
    <label for="benefit">Benefit (satu per baris)</label>
    <textarea id="benefit" name="benefit" rows="5"><?= e($editing['benefit']) ?></textarea>
  </div>
  <div class="form-row-2">
    <div class="form-row"><label for="label">Label pita (opsional)</label><input type="text" id="label" name="label" value="<?= e($editing['label']) ?>" maxlength="40" placeholder="Terlaris"></div>
    <div class="form-row"><label for="urutan">Urutan tampil</label><input type="number" id="urutan" name="urutan" value="<?= e($editing['urutan']) ?>"><p class="hint">Angka kecil tampil lebih dulu.</p></div>
  </div>
  <div class="form-row checks">
    <label><input type="checkbox" name="aktif" value="1"<?= $editing['aktif'] ? ' checked' : '' ?>> Tampilkan di website</label>
    <label><input type="checkbox" name="unggulan" value="1"<?= $editing['unggulan'] ? ' checked' : '' ?>> Jadikan paket unggulan (disorot di halaman depan)</label>
  </div>
  <button class="btn btn-primary" type="submit">Simpan Paket</button>
</form>
<?php
else:
    $paket = get_paket(false);
?>
<div class="admin-title">
  <h1>Paket Internet</h1>
  <a class="btn btn-primary btn-sm" href="<?= e(url('admin/paket.php?baru=1')) ?>">+ Tambah Paket</a>
</div>
<div class="table-wrap">
  <table class="table">
    <thead><tr><th>Urutan</th><th>Paket</th><th>Kecepatan</th><th>Harga</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($paket as $p): ?>
      <tr>
        <td><?= (int) $p['urutan'] ?></td>
        <td><strong><?= e($p['nama']) ?></strong><?= $p['label'] ? ' <span class="badge">' . e($p['label']) . '</span>' : '' ?><?= $p['unggulan'] ? ' <span class="badge status-terpasang">Unggulan</span>' : '' ?></td>
        <td><?= (int) $p['kecepatan'] ?> Mbps</td>
        <td><?= e(rupiah($p['harga'])) ?><?= $p['harga_coret'] ? '<br><s class="note">' . e(rupiah($p['harga_coret'])) . '</s>' : '' ?></td>
        <td><?= $p['aktif'] ? 'Tampil' : '<span class="note">Disembunyikan</span>' ?></td>
        <td class="actions">
          <a class="btn btn-outline btn-sm" href="<?= e(url('admin/paket.php?edit=' . (int) $p['id'])) ?>">Ubah</a>
          <form method="post" class="inline" onsubmit="return confirm('Hapus paket ini?')">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
            <button class="btn btn-sm link-danger" type="submit" name="action" value="delete">Hapus</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$paket): ?><tr><td colspan="6">Belum ada paket.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php
endif;
admin_footer();
