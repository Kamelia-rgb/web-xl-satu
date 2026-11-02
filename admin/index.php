<?php
require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/admin.php';
require_admin();

// ---------- Simpan perubahan status / catatan sales ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($action === 'update') {
        $status = (string) ($_POST['status'] ?? '');
        if (isset(LEAD_STATUS[$status])) {
            $stmt = db()->prepare('UPDATE leads SET status = ?, catatan_sales = ? WHERE id = ?');
            $stmt->execute([$status, mb_substr(trim((string) ($_POST['catatan_sales'] ?? '')), 0, 2000) ?: null, $id]);
            flash('success', 'Data calon pelanggan diperbarui.');
        }
    } elseif ($action === 'delete') {
        db()->prepare('DELETE FROM leads WHERE id = ?')->execute([$id]);
        flash('success', 'Data calon pelanggan dihapus.');
    }
    redirect('admin/?' . http_build_query(array_filter(['status' => $_GET['status'] ?? '', 'q' => $_GET['q'] ?? ''])));
}

// ---------- Filter ----------
$status = (string) ($_GET['status'] ?? '');
$q = trim((string) ($_GET['q'] ?? ''));
$where = [];
$params = [];
if (isset(LEAD_STATUS[$status])) {
    $where[] = 'status = ?';
    $params[] = $status;
}
if ($q !== '') {
    $where[] = '(nama LIKE ? OR no_wa LIKE ? OR alamat LIKE ? OR kota LIKE ?)';
    array_push($params, "%$q%", "%$q%", "%$q%", "%$q%");
}
$whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$stmt = db()->prepare('SELECT * FROM leads' . $whereSql . ' ORDER BY created_at DESC, id DESC LIMIT 300');
$stmt->execute($params);
$leads = $stmt->fetchAll();

$counts = array_fill_keys(array_keys(LEAD_STATUS), 0);
foreach (db()->query('SELECT status, COUNT(*) AS n FROM leads GROUP BY status') as $row) {
    $counts[$row['status']] = (int) $row['n'];
}
$total = array_sum($counts);
$today = (int) db()->query('SELECT COUNT(*) FROM leads WHERE created_at >= ' . db()->quote(date('Y-m-d 00:00:00')))->fetchColumn();

admin_header('Calon Pelanggan', 'index.php');
?>
<div class="admin-title">
  <h1>Calon Pelanggan</h1>
  <a class="btn btn-outline btn-sm" href="<?= e(url('admin/export.php?' . http_build_query(array_filter(['status' => $status, 'q' => $q])))) ?>">⬇ Export Excel (CSV)</a>
</div>

<div class="stats">
  <div class="stat"><span>Masuk hari ini</span><strong><?= $today ?></strong></div>
  <div class="stat"><span>Baru (belum dihubungi)</span><strong><?= $counts['baru'] ?></strong></div>
  <div class="stat"><span>Terpasang</span><strong><?= $counts['terpasang'] ?></strong></div>
  <div class="stat"><span>Total</span><strong><?= $total ?></strong></div>
</div>

<form class="filters" method="get">
  <div class="chips">
    <a class="chip" href="<?= e(url('admin/' . ($q !== '' ? '?q=' . urlencode($q) : ''))) ?>"<?= $status === '' ? ' aria-current="true"' : '' ?>>Semua (<?= $total ?>)</a>
    <?php foreach (LEAD_STATUS as $key => $label): ?>
      <a class="chip status-<?= e($key) ?>" href="<?= e(url('admin/?' . http_build_query(array_filter(['status' => $key, 'q' => $q])))) ?>"<?= $status === $key ? ' aria-current="true"' : '' ?>><?= e($label) ?> (<?= $counts[$key] ?>)</a>
    <?php endforeach; ?>
  </div>
  <?php if ($status !== ''): ?><input type="hidden" name="status" value="<?= e($status) ?>"><?php endif; ?>
  <div class="search">
    <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari nama, nomor, alamat…">
    <button class="btn btn-primary btn-sm" type="submit">Cari</button>
  </div>
</form>

<?php if (!$leads): ?>
  <div class="alert alert-info">Belum ada data calon pelanggan<?= $where ? ' untuk filter ini' : '' ?>.</div>
<?php endif; ?>

<div class="lead-list">
<?php foreach ($leads as $l):
    $waMsg = 'Halo ' . $l['nama'] . ', saya dari ' . config('site_name') . '. Terima kasih sudah mendaftar' . ($l['paket_nama'] ? ' paket ' . $l['paket_nama'] : '') . '. Boleh kirim share location alamat pemasangannya untuk kami cek area?';
?>
  <article class="lead">
    <div class="lead-main">
      <div class="lead-head">
        <h2><?= e($l['nama']) ?></h2>
        <span class="badge status-<?= e($l['status']) ?>"><?= e(LEAD_STATUS[$l['status']] ?? $l['status']) ?></span>
      </div>
      <p class="lead-meta"><?= e(date('d/m/Y H:i', strtotime($l['created_at']))) ?><?= $l['sumber'] ? ' · dari ' . e($l['sumber']) : '' ?></p>
      <dl>
        <dt>WhatsApp</dt><dd><a href="<?= e('https://wa.me/' . $l['no_wa'] . '?text=' . rawurlencode($waMsg)) ?>" target="_blank" rel="noopener">+<?= e($l['no_wa']) ?></a></dd>
        <dt>Alamat</dt><dd><?= nl2br(e($l['alamat'])) ?><?= $l['kota'] ? ', ' . e($l['kota']) : '' ?></dd>
        <dt>Paket</dt><dd><?= e($l['paket_nama'] ?: 'Belum memilih') ?></dd>
        <?php if ($l['catatan']): ?><dt>Catatan</dt><dd><?= nl2br(e($l['catatan'])) ?></dd><?php endif; ?>
      </dl>
    </div>
    <form class="lead-form" method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int) $l['id'] ?>">
      <label for="status-<?= (int) $l['id'] ?>">Status</label>
      <select id="status-<?= (int) $l['id'] ?>" name="status">
        <?php foreach (LEAD_STATUS as $key => $label): ?>
          <option value="<?= e($key) ?>"<?= $l['status'] === $key ? ' selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
      <label for="cs-<?= (int) $l['id'] ?>">Catatan sales</label>
      <textarea id="cs-<?= (int) $l['id'] ?>" name="catatan_sales" rows="2"><?= e($l['catatan_sales']) ?></textarea>
      <div class="lead-actions">
        <button class="btn btn-primary btn-sm" type="submit" name="action" value="update">Simpan</button>
        <a class="btn btn-wa btn-sm" href="<?= e('https://wa.me/' . $l['no_wa'] . '?text=' . rawurlencode($waMsg)) ?>" target="_blank" rel="noopener">Chat</a>
        <button class="btn btn-sm link-danger" type="submit" name="action" value="delete" onclick="return confirm('Hapus data <?= e(addslashes($l['nama'])) ?>?')">Hapus</button>
      </div>
    </form>
  </article>
<?php endforeach; ?>
</div>
<?php admin_footer(); ?>
