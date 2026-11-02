<?php
declare(strict_types=1);

// Pengganti fungsi PHP 8 supaya web tetap jalan di PHP 7.3 (XAMPP versi lama).
if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }
}

function config(string $key, $default = null)
{
    return $GLOBALS['config'][$key] ?? $default;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(config('db_dsn'), config('db_user'), config('db_pass'), [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $ex) {
            db_unavailable($ex);
        }
    }
    return $pdo;
}

/** Halaman rapi saat database tidak bisa dihubungi (tanpa membocorkan detail ke pengunjung). */
function db_unavailable(PDOException $ex): void
{
    error_log('[web-xl-satu] Gagal konek database: ' . $ex->getMessage());
    $host = strtolower(explode(':', $_SERVER['HTTP_HOST'] ?? '')[0]);
    $lokal = in_array($host, ['localhost', '127.0.0.1'], true) || preg_match('/^(192\.168|10)\./', $host);
    if (!headers_sent()) {
        http_response_code(503);
        header('Content-Type: text/html; charset=utf-8');
        header('Retry-After: 120');
    }
    $wa = 'https://wa.me/' . preg_replace('/\D/', '', (string) config('wa_number'));
    echo '<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
        . '<meta name="robots" content="noindex"><title>Sedang ada gangguan</title>'
        . '<style>body{margin:0;font-family:system-ui,sans-serif;background:#f5f7fb;color:#1a2233;display:grid;place-items:center;min-height:100vh;padding:16px}'
        . '.box{max-width:520px;background:#fff;border-radius:16px;padding:28px;box-shadow:0 10px 30px rgba(11,61,145,.1)}h1{font-size:1.4rem;margin:0 0 8px}'
        . 'a.btn{display:inline-block;margin-top:12px;background:#25d366;color:#fff;text-decoration:none;font-weight:700;padding:12px 20px;border-radius:999px}'
        . '.dev{margin-top:18px;padding:12px 14px;background:#fff4d6;border-radius:10px;font-size:.92rem}code{font-size:.85rem;word-break:break-all}</style></head><body><div class="box">'
        . '<h1>Website sedang ada gangguan sebentar</h1><p>Silakan coba lagi beberapa menit lagi, atau langsung hubungi sales kami lewat WhatsApp.</p>'
        . '<a class="btn" href="' . htmlspecialchars($wa, ENT_QUOTES) . '">Chat WhatsApp</a>';
    if ($lokal) {
        echo '<div class="dev"><b>Untuk kamu (hanya tampil di komputer sendiri):</b><br>Database tidak bisa dihubungi. '
            . 'Pastikan <b>MySQL di XAMPP sudah Start (hijau)</b> dan data database di <code>config.php</code> benar.'
            . '<br><br><code>' . htmlspecialchars($ex->getMessage(), ENT_QUOTES) . '</code></div>';
    }
    echo '</div></body></html>';
    exit;
}

/** Escape teks untuk HTML. */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Path dasar web (mendukung web yang dipasang di subfolder). */
function base_path(): string
{
    $path = parse_url((string) config('base_url', ''), PHP_URL_PATH) ?: '';
    return rtrim($path, '/');
}

/** Link internal, contoh: url('paket') -> /paket */
function url(string $path = ''): string
{
    return base_path() . '/' . ltrim($path, '/');
}

/** Link lengkap (untuk canonical, sitemap, Open Graph). */
function absolute_url(string $path = ''): string
{
    return rtrim((string) config('base_url', ''), '/') . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    $file = __DIR__ . '/../' . ltrim($path, '/');
    $version = is_file($file) ? (string) filemtime($file) : '1';
    return url($path) . '?v=' . $version;
}

/** Link WhatsApp dengan pesan yang sudah terisi. */
function wa_link(string $message = ''): string
{
    $link = 'https://wa.me/' . preg_replace('/\D/', '', (string) config('wa_number'));
    if ($message !== '') {
        $link .= '?text=' . rawurlencode($message);
    }
    return $link;
}

function rupiah($amount): string
{
    return 'Rp' . number_format((float) $amount, 0, ',', '.');
}

/** Normalisasi nomor HP Indonesia ke format 62xxxxxxxxxx. */
function normalize_phone(string $phone): string
{
    $digits = preg_replace('/\D/', '', $phone);
    if (str_starts_with($digits, '0')) {
        $digits = '62' . substr($digits, 1);
    } elseif (str_starts_with($digits, '8')) {
        $digits = '62' . $digits;
    }
    return $digits;
}

/** 6281234567890 -> 0812 3456 7890 (mudah dibaca & tidak diubah Excel jadi angka). */
function format_phone(string $phone): string
{
    $local = str_starts_with($phone, '62') ? '0' . substr($phone, 2) : $phone;
    return trim(implode(' ', [substr($local, 0, 4), substr($local, 4, 4), substr($local, 8)]));
}

/** Link telepon: '0817 0123 442' -> 'tel:08170123442' */
function tel_link(string $number): string
{
    return 'tel:' . preg_replace('/[^\d+]/', '', $number);
}

function redirect(string $path): void
{
    header('Location: ' . (preg_match('#^https?://#', $path) ? $path : url($path)));
    exit;
}

// ---------- Keamanan formulir ----------

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    $sent = $_POST['csrf'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(400);
        exit('Sesi formulir kedaluwarsa. Silakan muat ulang halaman dan coba lagi.');
    }
}

// ---------- Pesan sekali tampil ----------

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function flashes(): array
{
    $items = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $items;
}

// ---------- Admin ----------

function is_admin(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_admin(): void
{
    if (!is_admin()) {
        redirect('admin/login.php');
    }
}

// ---------- Data ----------

/** Jenis produk: Fiber (jaringan kabel) atau FWA (tanpa kabel, pakai modem). */
const PAKET_JENIS = [
    'Fiber' => ['label' => 'Fiber (Jaringan Kabel)'],
    'FWA'   => ['label' => 'FWA (Tanpa Kabel)'],
];

function paket_jenis(array $p): string
{
    $j = $p['jenis'] ?? '';
    return isset(PAKET_JENIS[$j]) ? $j : 'Fiber';
}

/**
 * Menambahkan kolom "jenis" otomatis untuk database yang di-import sebelum
 * kolom ini ada, jadi tidak perlu menjalankan SQL manual.
 */
function ensure_paket_jenis(): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    try {
        if (!db()->query("SHOW COLUMNS FROM paket LIKE 'jenis'")->fetch()) {
            db()->exec("ALTER TABLE paket ADD COLUMN jenis VARCHAR(20) NOT NULL DEFAULT 'Fiber' AFTER id");
            db()->exec("UPDATE paket SET jenis = 'FWA' WHERE nama LIKE 'FWA%'");
        }
    } catch (PDOException $ex) {
        // Diabaikan: tampilan tetap jalan, semua paket dianggap Fiber.
    }
}

/** Tambah kolom leads.lokasi otomatis untuk database lama. */
function ensure_leads_lokasi(): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    try {
        if (!db()->query("SHOW COLUMNS FROM leads LIKE 'lokasi'")->fetch()) {
            db()->exec("ALTER TABLE leads ADD COLUMN lokasi VARCHAR(255) NULL AFTER kota");
        }
    } catch (PDOException $ex) {
        // Diabaikan: formulir tetap jalan tanpa kolom lokasi.
    }
}

/**
 * Rapikan isian lokasi: koordinat "-6.39, 106.82" jadi link Google Maps,
 * link http(s) dibiarkan, selain itu dianggap tidak valid.
 * Mengembalikan '' bila kosong, null bila tidak valid.
 */
function normalize_lokasi(string $text): ?string
{
    $text = trim($text);
    if ($text === '') {
        return '';
    }
    if (preg_match('/^(-?\d{1,2}\.\d+)\s*,\s*(-?\d{1,3}\.\d+)$/', $text, $m)) {
        return 'https://maps.google.com/?q=' . $m[1] . ',' . $m[2];
    }
    if (preg_match('#^https?://\S+$#i', $text) && strlen($text) <= 255) {
        return $text;
    }
    return null;
}

/** Ambil koordinat dari link lokasi (untuk peta kecil di admin). */
function lokasi_koordinat(?string $url): ?string
{
    if ($url && preg_match('/(-?\d{1,2}\.\d{3,})\s*,\s*(-?\d{1,3}\.\d{3,})/', $url, $m)) {
        return $m[1] . ',' . $m[2];
    }
    return null;
}

function get_paket(bool $onlyActive = true): array
{
    ensure_paket_jenis();
    $sql = 'SELECT * FROM paket' . ($onlyActive ? ' WHERE aktif = 1' : '') . ' ORDER BY urutan ASC, harga ASC';
    return db()->query($sql)->fetchAll();
}

function get_promo(bool $onlyActive = true): array
{
    $sql = 'SELECT * FROM promo';
    if ($onlyActive) {
        $sql .= ' WHERE aktif = 1 AND (berlaku_sampai IS NULL OR berlaku_sampai >= ' . db()->quote(date('Y-m-d')) . ')';
    }
    $sql .= ' ORDER BY urutan ASC, id DESC';
    $rows = db()->query($sql)->fetchAll();
    // Abaikan gambar yang filenya sudah tidak ada, supaya tidak tampil gambar rusak.
    foreach ($rows as &$row) {
        if (!empty($row['gambar']) && !preg_match('#^https?://#', $row['gambar']) && !is_file(__DIR__ . '/../' . $row['gambar'])) {
            $row['gambar'] = null;
        }
    }
    unset($row);
    return $rows;
}

/** Benefit disimpan satu per baris. */
function lines(string $text): array
{
    return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $text))));
}

function tanggal_indo(string $date): string
{
    $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $ts = strtotime($date);
    return date('j', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
}

const LEAD_STATUS = [
    'baru'      => 'Baru',
    'dihubungi' => 'Sudah dihubungi',
    'survey'    => 'Jadwal survei',
    'terpasang' => 'Terpasang',
    'batal'     => 'Batal',
];
