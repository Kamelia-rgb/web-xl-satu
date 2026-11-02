<?php
declare(strict_types=1);

$configFile = __DIR__ . '/../config.php';
if (!is_file($configFile)) {
    http_response_code(500);
    exit('File config.php belum ada. Salin config.example.php menjadi config.php lalu isi datanya.');
}
$GLOBALS['config'] = require $configFile;

date_default_timezone_set($GLOBALS['config']['timezone'] ?? 'Asia/Jakarta');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}

require __DIR__ . '/functions.php';
require __DIR__ . '/partials.php';

// Simpan asal pengunjung (iklan Google/Meta) untuk dicatat di formulir.
if (!empty($_GET['utm_source']) || !empty($_GET['gclid']) || !empty($_GET['fbclid'])) {
    $source = $_GET['utm_source'] ?? (!empty($_GET['gclid']) ? 'google_ads' : 'facebook');
    if (!empty($_GET['utm_campaign']) && is_string($_GET['utm_campaign'])) {
        $source .= ' / ' . $_GET['utm_campaign'];
    }
    $_SESSION['sumber'] = mb_substr(is_string($source) ? $source : '', 0, 100);
}
