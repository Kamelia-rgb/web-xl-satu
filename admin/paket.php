<?php
require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/admin.php';
require_admin();
ensure_paket_jenis();

$empty = [
    'id' => 0, 'jenis' => 'Fiber', 'kategori' => 'Internet Only', 'nama' => '', 'kecepatan' => '', 'kecepatan_coret' => '',
    'harga' => '', 'harga_coret' => '', 'periode_bulan' => 1, 'ppn_termasuk' => 1, 'biaya_instalasi' => 0,
    'maks_perangkat' => '', 'ideal_untuk' => '', 'benefit' => '', 'bonus' => '', 'label' => '',
    'unggulan' => 0, 'aktif' => 1, 'urutan' => 0,
];
$errors = [];
$editing = null;

$money = function (string $key): int {
    return (int) preg_replace('/\D/', '', (string) ($_POST[$key] ?? ''));
};
$text = function (string $key): string {
    return trim((string) ($_POST[$key] ?? ''));
};

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
        'jenis'           => isset(PAKET_JENIS[$_POST['jenis'] ?? '']) ? $_POST['jenis'] : 'Fiber',
        'kategori'        => $text('kategori') ?: 'Internet Only',
        'nama'            => $text('nama'),
        'kecepatan'       => (int) ($_POST['kecepatan'] ?? 0),
        'kecepatan_coret' => (int) ($_POST['kecepatan_coret'] ?? 0) ?: null,
        'harga'           => $money('harga'),
        'harga_coret'     => $money('harga_coret') ?: null,
        'periode_bulan'   => max(1, min(24, (int) ($_POST['periode_bulan'] ?? 1))),
        'ppn_termasuk'    => !empty($_POST['ppn_termasuk']) ? 1 : 0,
        'biaya_instalasi' => $money('biaya_instalasi'),
        'maks_perangkat'  => (int) ($_POST['maks_perangkat'] ?? 0) ?: null,
        'ideal_untuk'     => $text('ideal_untuk') ?: null,
        'benefit'         => $text('benefit'),
        'bonus'           => $text('bonus') ?: null,
        'label'           => $text('label') ?: null,
        'unggulan'        => !empty($_POST['unggulan']) ? 1 : 0,
        'aktif'           => !empty($_POST['aktif']) ? 1 : 0,
        'urutan'          => (int) ($_POST['urutan'] ?? 0),
    ];

    if ($data['nama'] === '' || mb_strlen($data['nama']) > 100) $errors[] = 'Nama paket wajib diisi (maks. 100 karakter).';
    if (mb_strlen($data['kategori']) > 40) $errors[] = 'Kategori maksimal 40 karakter.';
    if ($data['kecepatan'] <= 0) $errors[] = 'Kecepatan harus lebih dari 0.';
    if ($data['harga'] <= 0) $errors[] = 'Harga harus diisi.';
    if ($data['label'] !== null && mb_strlen($data['label']) > 40) $errors[] = 'Label maksimal 40 karakter.';
    if (mb_strlen((string) $data['ideal_untuk']) > 255 || mb_strlen((string) $data['bonus']) > 255) $errors[] = 'Teks "Ideal untuk" dan "Bonus" maksimal 255 karakter.';

    if (!$errors) {
        if ($data['unggulan']) {
            // Hanya satu paket unggulan yang disorot di halaman depan.
            db()->prepare('UPDATE paket SET unggulan = 0 WHERE id <> ?')->execute([$id]);
        }
        $cols = array_keys($data);
        if ($id > 0) {
            $sql = 'UPDATE paket SET ' . implode(', ', array_map(function ($c) { return "$c = ?"; }, $cols)) . ' WHERE id = ?';
            db()->prepare($sql)->execute(array_merge(array_values($data), [$id]));
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
    $kategoriList = array_values(array_unique(array_merge(PAKET_KATEGORI, array_column(get_paket(false), 'kategori'))));
?>
<div class="admin-title">
  <h1><?= $editing['id'] ? 'Ubah Paket' : 'Tambah Paket' ?></h1>
  <a class="btn btn-outline btn-sm" href="<?= e(url('admin/paket.php')) ?>">← Kembali</a>
</div>
<?php if ($errors): ?><div class="alert alert-error"><?= implode('<br>', array_map('e', $errors)) ?></div><?php endif; ?>
<form class="form-card admin-form" method="post">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= (int) $editing['id'] ?>">

  <div class="form-row">
    <label for="jenis">Jenis produk</label>
    <select id="jenis" name="jenis">
      <?php foreach (PAKET_JENIS as $key => $info): ?>
        <option value="<?= e($key) ?>"<?= paket_jenis($editing) === $key ? ' selected' : '' ?>><?= e($info['label']) ?></option>
      <?php endforeach; ?>
    </select>
    <p class="hint">Fiber = lewat jaringan kabel. FWA = tanpa kabel, pakai modem (untuk area yang belum tercover kabel).</p>
  </div>

  <div class="form-row-2">
    <div class="form-row">
      <label for="kategori">Kategori</label>
      <input type="text" id="kategori" name="kategori" value="<?= e($editing['kategori']) ?>" list="kategori-list" maxlength="40" required>
      <datalist id="kategori-list"><?php foreach ($kategoriList as $k): ?><option value="<?= e($k) ?>"><?php endforeach; ?></datalist>
      <p class="hint">Pilih atau ketik kategori baru. Dipakai untuk tab filter di halaman paket.</p>
    </div>
    <div class="form-row"><label for="nama">Nama paket</label><input type="text" id="nama" name="nama" value="<?= e($editing['nama']) ?>" required maxlength="100" placeholder="XL Satu Spark"></div>
  </div>

  <div class="form-row-2">
    <div class="form-row"><label for="kecepatan">Kecepatan (Mbps)</label><input type="number" id="kecepatan" name="kecepatan" value="<?= e($editing['kecepatan']) ?>" required min="1"></div>
    <div class="form-row"><label for="kecepatan_coret">Kecepatan sebelum Speed Booster (opsional)</label><input type="number" id="kecepatan_coret" name="kecepatan_coret" value="<?= e($editing['kecepatan_coret']) ?>" min="1"><p class="hint">Diisi bila ada upgrade kecepatan, tampil dicoret.</p></div>
  </div>

  <div class="form-row-2">
    <div class="form-row"><label for="harga">Harga (Rp)</label><input type="text" inputmode="numeric" id="harga" name="harga" value="<?= e($editing['harga']) ?>" required placeholder="254000"></div>
    <div class="form-row"><label for="harga_coret">Harga coret / normal (opsional)</label><input type="text" inputmode="numeric" id="harga_coret" name="harga_coret" value="<?= e($editing['harga_coret']) ?>"></div>
  </div>

  <div class="form-row-2">
    <div class="form-row"><label for="periode_bulan">Harga untuk berapa bulan</label><input type="number" id="periode_bulan" name="periode_bulan" value="<?= e($editing['periode_bulan']) ?>" min="1" max="24"><p class="hint">1 = per bulan. Isi 3 untuk "/3 bulan".</p></div>
    <div class="form-row"><label for="biaya_instalasi">Biaya instalasi (Rp)</label><input type="text" inputmode="numeric" id="biaya_instalasi" name="biaya_instalasi" value="<?= e($editing['biaya_instalasi']) ?>"><p class="hint">0 = Gratis Biaya Instalasi.</p></div>
  </div>

  <div class="form-row-2">
    <div class="form-row"><label for="maks_perangkat">Maksimal perangkat (opsional)</label><input type="number" id="maks_perangkat" name="maks_perangkat" value="<?= e($editing['maks_perangkat']) ?>" min="1"><p class="hint">Kosongkan untuk dihitung otomatis dari kecepatan.</p></div>
    <div class="form-row"><label for="label">Label pita (opsional)</label><input type="text" id="label" name="label" value="<?= e($editing['label']) ?>" maxlength="40" placeholder="Terlaris"></div>
  </div>

  <div class="form-row">
    <label for="ideal_untuk">Ideal untuk (opsional, pisahkan dengan koma)</label>
    <input type="text" id="ideal_untuk" name="ideal_untuk" value="<?= e($editing['ideal_untuk']) ?>" maxlength="255" placeholder="Browsing, Sosial Media, Streaming 4K, Game Online">
    <p class="hint">Kosongkan untuk diisi otomatis sesuai kecepatan.</p>
  </div>
  <div class="form-row">
    <label for="benefit">Keterangan tambahan (satu per baris, opsional)</label>
    <textarea id="benefit" name="benefit" rows="3" placeholder="Bulan ke-4 dst Rp219.000/bulan"><?= e($editing['benefit']) ?></textarea>
  </div>
  <div class="form-row">
    <label for="bonus">Bonus (opsional)</label>
    <input type="text" id="bonus" name="bonus" value="<?= e($editing['bonus']) ?>" maxlength="255" placeholder="Bonus nonton 3 bulan: Vidio Lite & Catchplay+">
  </div>
  <div class="form-row-2">
    <div class="form-row"><label for="urutan">Urutan tampil</label><input type="number" id="urutan" name="urutan" value="<?= e($editing['urutan']) ?>"><p class="hint">Angka kecil tampil lebih dulu. 6 paket teratas tampil di beranda.</p></div>
  </div>
  <div class="form-row checks">
    <label><input type="checkbox" name="ppn_termasuk" value="1"<?= $editing['ppn_termasuk'] ? ' checked' : '' ?>> Harga sudah termasuk PPN 11%</label>
    <label><input type="checkbox" name="aktif" value="1"<?= $editing['aktif'] ? ' checked' : '' ?>> Tampilkan di website</label>
    <label><input type="checkbox" name="unggulan" value="1"<?= $editing['unggulan'] ? ' checked' : '' ?>> Jadikan paket unggulan (disorot di bagian atas beranda)</label>
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
    <thead><tr><th>Urutan</th><th>Jenis</th><th>Kategori</th><th>Paket</th><th>Harga</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($paket as $p): ?>
      <tr>
        <td><?= (int) $p['urutan'] ?></td>
        <td><span class="badge <?= paket_jenis($p) === 'FWA' ? 'status-baru' : 'status-dihubungi' ?>"><?= e(paket_jenis($p)) ?></span></td>
        <td><span class="badge"><?= e($p['kategori']) ?></span></td>
        <td><strong><?= e(paket_judul($p)) ?></strong><?= $p['label'] ? ' <span class="badge status-baru">' . e($p['label']) . '</span>' : '' ?><?= $p['unggulan'] ? ' <span class="badge status-terpasang">Unggulan</span>' : '' ?></td>
        <td><?= e(rupiah($p['harga']) . periode_label($p)) ?><?= $p['harga_coret'] ? '<br><s class="note">' . e(rupiah($p['harga_coret'])) . '</s>' : '' ?></td>
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
    <?php if (!$paket): ?><tr><td colspan="7">Belum ada paket.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php
endif;
admin_footer();
