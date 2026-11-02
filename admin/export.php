<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin();

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
$stmt = db()->prepare('SELECT * FROM leads' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY created_at DESC, id DESC');
$stmt->execute($params);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="calon-pelanggan-' . date('Y-m-d') . '.csv"');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // BOM supaya Excel membaca huruf dengan benar
fputcsv($out, ['Tanggal', 'Nama', 'WhatsApp', 'Alamat', 'Kota', 'Paket', 'Catatan', 'Sumber', 'Status', 'Catatan Sales'], ';');

/** Cegah rumus Excel berbahaya dari isian formulir (CSV injection). */
$safe = fn ($v) => preg_match('/^[=+\-@\t\r]/', (string) $v) ? "'" . $v : (string) $v;

while ($l = $stmt->fetch()) {
    fputcsv($out, array_map($safe, [
        date('d/m/Y H:i', strtotime($l['created_at'])),
        $l['nama'],
        format_phone($l['no_wa']),
        $l['alamat'],
        $l['kota'],
        $l['paket_nama'],
        $l['catatan'],
        $l['sumber'],
        LEAD_STATUS[$l['status']] ?? $l['status'],
        $l['catatan_sales'],
    ]), ';');
}
fclose($out);
