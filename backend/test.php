<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>WorkPulse — Diagnostik Backend & Database</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root {
    --bg-page: #F8FAFC;
    --card: #FFFFFF;
    --border: #E2E8F0;
    --text-primary: #0F172A;
    --text-secondary: #64748B;
    --primary: #6366F1;
    --success: #10B981;
    --warning: #F59E0B;
    --danger: #EF4444;
  }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
    background: var(--bg-page);
    color: var(--text-primary);
    padding: 40px 20px;
    max-width: 860px;
    margin: 0 auto;
    line-height: 1.5;
  }
  .header {
    margin-bottom: 28px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .brand {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 22px;
    font-weight: 800;
    color: var(--primary);
  }
  h2 {
    color: var(--text-secondary);
    font-size: 11px;
    margin: 28px 0 12px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-weight: 800;
  }
  .check {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 12px;
    padding: 14px 18px;
    border-radius: 12px;
    background: var(--card);
    border: 1px solid var(--border);
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
  }
  .ok { border-left: 4px solid var(--success); }
  .fail { border-left: 4px solid var(--danger); }
  .warn { border-left: 4px solid var(--warning); }
  .icon { font-size: 18px; flex-shrink: 0; margin-top: 1px; }
  .label { font-weight: 700; color: var(--text-primary); font-size: 14px; margin-bottom: 2px; }
  .detail { color: var(--text-secondary); font-size: 13px; }
  .code {
    background: #F1F5F9;
    padding: 8px 12px;
    border-radius: 8px;
    margin-top: 8px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 12px;
    color: #334155;
    white-space: pre-wrap;
    word-break: break-all;
    border: 1px solid #E2E8F0;
  }
  a { color: var(--primary); text-decoration: none; font-weight: 600; }
  a:hover { text-decoration: underline; }
</style>
</head>
<body>

<div class="header">
  <div class="brand">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
    WorkPulse
  </div>
  <div style="font-size:12px;color:var(--text-secondary);font-weight:600">Diagnostik Sistem Backend</div>
</div>

<?php
function check(string $label, bool $ok, string $detail = '', string $code = '', string $level = ''): void {
    $cls  = $level ?: ($ok ? 'ok' : 'fail');
    $icon = $cls === 'ok' ? '✅' : ($cls === 'warn' ? '⚠️' : '❌');
    echo "<div class='check $cls'>";
    echo "<div class='icon'>$icon</div>";
    echo "<div style='flex:1'><div class='label'>$label</div>";
    if ($detail) echo "<div class='detail'>$detail</div>";
    if ($code)   echo "<div class='code'>$code</div>";
    echo "</div></div>";
}

// ─── 1. PHP Version & Extensions ──────────────────────────────
$phpVer = PHP_VERSION;
$phpOk  = version_compare($phpVer, '7.4.0', '>=');
check('Versi PHP Runtime', $phpOk,
    $phpOk ? "PHP $phpVer — Kompatibel & Siap Digunakan" : "PHP $phpVer — Diperlukan versi PHP 7.4+");

check('Ekstensi PDO PHP', extension_loaded('pdo'),
    extension_loaded('pdo') ? 'PDO aktif dan tersedia' : 'PDO tidak aktif — aktifkan pdo di php.ini');

check('Ekstensi PDO MySQL Driver', extension_loaded('pdo_mysql'),
    extension_loaded('pdo_mysql') ? 'pdo_mysql aktif dan siap terhubung ke database' : 'pdo_mysql tidak aktif — aktifkan di php.ini');

// ─── 2. File Modul Backend ───────────────────────────────────
echo "<h2>Integritas File Modul Backend</h2>";
$files = [
    'php/config.php'    => 'Konfigurasi DB (config.php)',
    'php/db.php'        => 'PDO Database Wrapper (db.php)',
    'php/validator.php' => 'Validator 6 Pilar Budaya & Burnout (validator.php)',
    'api.php'           => 'Router API WorkPulse (api.php)',
    'database.sql'      => 'Skema DDL & Data Awal (database.sql)',
];
foreach ($files as $path => $label) {
    $full = __DIR__ . '/' . $path;
    $exists = file_exists($full);
    check($label, $exists,
        $exists ? 'File tersedia di direktori backend' : "File TIDAK ditemukan di: $full");
}

// ─── 3. Koneksi Database ─────────────────────────────────────
echo "<h2>Status Koneksi Database MySQL</h2>";
$configOk = false;
try {
    require_once __DIR__ . '/php/config.php';
    $configOk = true;
    check('Pemuatan Konfigurasi config.php', true,
        'DB_HOST=' . DB_HOST . ' | DB_NAME=' . DB_NAME . ' | DB_USER=' . DB_USER);
} catch (Throwable $e) {
    check('Pemuatan Konfigurasi config.php', false, $e->getMessage());
}

if ($configOk) {
    try {
        require_once __DIR__ . '/php/db.php';
        $pdo = DB::pdo();
        $ver = DB::fetchOne('SELECT VERSION() AS v')['v'] ?? '?';
        check('Koneksi Database MySQL', true, "Terhubung ke MySQL $ver — Database: " . DB_NAME);
        
        // ─── 4. Tabel WorkPulse ───────────────────────────────
        echo "<h2>Pemeriksaan Tabel Skema WorkPulse</h2>";
        $required = ['users', 'submissions', 'answers', 'factor_definitions', 'question_definitions', 'validation_log'];
        foreach ($required as $tbl) {
            $row   = DB::fetchOne("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?", [DB_NAME, $tbl]);
            $exists = !empty($row);
            $count  = $exists ? (DB::fetchOne("SELECT COUNT(*) AS c FROM `$tbl`")['c'] ?? 0) : 0;
            check(
                "Tabel `$tbl`",
                $exists,
                $exists ? "Aktif — memuat $count baris data" : "Belum dibuat — impor file `backend/database.sql` ke database " . DB_NAME
            );
        }

    } catch (Throwable $e) {
        check('Koneksi Database MySQL', false,
            'Gagal terhubung ke database. Pastikan MySQL service berjalan dan database `' . DB_NAME . '` telah dibuat.',
            $e->getMessage(),
            'fail');
    }

    // ─── 5. Test Endpoint API ────────────────────────────────
    echo "<h2>Pengujian Respons API WorkPulse</h2>";
    $apiUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http')
            . '://' . $_SERVER['HTTP_HOST']
            . str_replace('test.php', 'api.php', $_SERVER['REQUEST_URI']);

    $ctx = stream_context_create([
        'http' => [
            'method'        => 'POST',
            'header'        => "Content-Type: application/json\r\n",
            'content'       => json_encode(['email' => 'ping@workpulse.test', 'password' => 'wrongpass', 'role' => 'clevel']),
            'timeout'       => 5,
            'ignore_errors' => true,
        ]
    ]);
    $raw = @file_get_contents($apiUrl . '?action=login', false, $ctx);
    if ($raw !== false) {
        $json = json_decode($raw, true);
        if ($json !== null) {
            check('Endpoint API (api.php)', true,
                'Router API merespons dengan format JSON yang valid.',
                'Respons: ' . ($json['message'] ?? 'Status ' . ($json['status'] ?? '')));
        } else {
            check('Endpoint API (api.php)', false,
                'Format respons bukan JSON valid',
                "Raw: " . substr($raw, 0, 200));
        }
    } else {
        check('Endpoint API (api.php)', false,
            'API tidak dapat dijangkau melalui self-request (normal pada beberapa konfigurasi local CLI/CGI).',
            'Coba buka langsung: ' . $apiUrl . '?action=health', 'warn');
    }
}

// ─── 6. Panduan Akses Frontend ───────────────────────────────
echo "<h2>Akses Aplikasi Frontend</h2>";
$currentUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http')
    . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$appUrl = str_replace('backend/test.php', 'Frontend/index.html', $currentUrl);

check('Tautan Aplikasi WorkPulse', true,
    'Aplikasi siap digunakan. Buka tautan berikut di browser:',
    "<a href='$appUrl' target='_blank'>$appUrl</a>");
?>

</body>
</html>
