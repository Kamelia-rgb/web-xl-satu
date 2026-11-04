<?php
/** Menerima formulir dari jendela "Chat Sales via WhatsApp", membalas JSON berisi link WhatsApp. */
require __DIR__ . '/includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false]);
    exit;
}
$token = $_POST['csrf'] ?? '';
if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'Sesi kedaluwarsa. Muat ulang halaman lalu coba lagi.']);
    exit;
}

$paketById = array_column(get_paket(), null, 'id');
$hasil = proses_lead($_POST, $paketById, 'form WA');
if ($hasil['errors']) {
    echo json_encode(['ok' => false, 'errors' => $hasil['errors']]);
    exit;
}
echo json_encode(['ok' => true, 'wa' => wa_link(wa_message_lead($hasil['lead']))], JSON_UNESCAPED_SLASHES);
