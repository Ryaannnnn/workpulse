<?php
/**
 * WorkPulse — Clinical REST API Router
 * Work Culture & Burnout Risk Assessment Platform for Tech Startups
 *
 * Endpoints:
 *   POST   /api.php?action=register          — Registrasi akun C-Level atau Staf
 *   POST   /api.php?action=login             — Autentikasi akun & penerbitan JWT
 *   POST   /api.php?action=submit            — Pengiriman kuesioner 6 faktor budaya & burnout [Auth]
 *   GET    /api.php?action=submissions       — Riwayat evaluasi pengguna [Auth]
 *   GET    /api.php?action=dashboard         — Agregat skor kesehatan ekosistem startup [Auth]
 *   GET    /api.php?action=startup_list      — Tabel daftar startup, skor, & risiko burnout [Auth]
 *   GET    /api.php?action=factor_averages   — Rata-rata 6 pilar budaya kerja [Auth]
 */

require_once __DIR__ . '/php/config.php';
require_once __DIR__ . '/php/db.php';
require_once __DIR__ . '/php/validator.php';

// ── Header CORS ───────────────────────────────────────────────
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: '  . CORS_ORIGIN);
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ── Response & Body Helpers ────────────────────────────────────
function respond(bool $success, $data = null, string $message = '', int $code = 200): void {
    http_response_code($code);
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}

function bodyJson(): array {
    $raw = file_get_contents('php://input');
    return $raw ? (json_decode($raw, true) ?? []) : [];
}

// ── JWT Helpers ────────────────────────────────────────────────
function b64UrlEncode(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function b64UrlDecode(string $data): string {
    $pad = strlen($data) % 4;
    if ($pad) $data .= str_repeat('=', 4 - $pad);
    return base64_decode(strtr($data, '-_', '+/'));
}

function jwtEncode(array $payload): string {
    $header         = b64UrlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload['exp'] = time() + JWT_EXPIRE;
    $body           = b64UrlEncode(json_encode($payload));
    $sig            = b64UrlEncode(hash_hmac('sha256', "$header.$body", JWT_SECRET, true));
    return "$header.$body.$sig";
}

function jwtDecode(string $token): ?array {
    $parts = explode('.', trim($token));
    if (count($parts) !== 3) return null;
    [$header, $body, $sig] = $parts;
    $expectedSig = b64UrlEncode(hash_hmac('sha256', "$header.$body", JWT_SECRET, true));
    if (!hash_equals($expectedSig, $sig)) return null;
    $data = json_decode(b64UrlDecode($body), true);
    if (!$data || ($data['exp'] ?? 0) < time()) return null;
    return $data;
}

function requireAuth(): array {
    $authHeader = '';
    if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    } elseif (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    } elseif (function_exists('apache_request_headers')) {
        $hdrs = apache_request_headers();
        $authHeader = $hdrs['Authorization'] ?? $hdrs['authorization'] ?? '';
    }

    $token = trim(preg_replace('/^Bearer\s+/i', '', $authHeader));
    $payload = $token ? jwtDecode($token) : null;
    if (!$payload) {
        respond(false, null, 'Sesi autentikasi tidak valid atau telah berakhir. Silakan login kembali.', 401);
    }
    return $payload;
}

// ── Router ────────────────────────────────────────────────────
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Dukung backward compatibility jika client lama memanggil umkm_list
if ($action === 'umkm_list') {
    $action = 'startup_list';
}

try {
    match (true) {

        // ── POST /api.php?action=register ─────────────────────
        $action === 'register' && $method === 'POST' => (function () {
            $body = bodyJson();
            $role = $body['role'] ?? 'clevel';
            // Map legacy fields jika terkirim
            if (!empty($body['umkm_name']) && empty($body['startup_name'])) {
                $body['startup_name'] = $body['umkm_name'];
            }
            if (!empty($body['sektor']) && empty($body['sektor_teknologi'])) {
                $body['sektor_teknologi'] = $body['sektor'];
            }
            if (!empty($body['omzet']) && empty($body['pendanaan'])) {
                $body['pendanaan'] = $body['omzet'];
            }

            if ($role === 'owner') $role = 'clevel';
            if ($role === 'employee') $role = 'staf';

            $result = Validator::validateSubmission($body, $role);

            // Cek error profil wajib
            $profileErrors = array_filter($result['errors'], fn($e) =>
                in_array($e['field'], ['startup_name', 'email', 'role', 'password']));

            if (!empty($profileErrors)) {
                respond(false, ['errors' => array_values($profileErrors)],
                    'Data pendaftaran akun tidak valid.', 422);
            }

            $email = strtolower(trim($body['email'] ?? ''));
            if (!$email) {
                respond(false, null, 'Email bisnis wajib diisi.', 422);
            }

            // Cek email duplikat
            $existing = DB::fetchOne('SELECT id FROM users WHERE email = ?', [$email]);
            if ($existing) {
                respond(false, null, 'Email kerja sudah terdaftar di WorkPulse.', 409);
            }

            // Batasi satu akun C-Level per startup
            if ($role === 'clevel') {
                $existingClevel = DB::fetchOne(
                    'SELECT id FROM users WHERE role = "clevel" AND LOWER(TRIM(startup_name)) = LOWER(TRIM(?))',
                    [$body['startup_name'] ?? '']
                );
                if ($existingClevel) {
                    respond(false, null, 'Startup "' . ($body['startup_name'] ?? '') . '" sudah memiliki akun C-Level / Founder terdaftar.', 409);
                }
            }

            // Simpan akun baru
            $hash = password_hash($body['password'] ?? '', PASSWORD_BCRYPT);
            $id   = DB::execute(
                'INSERT INTO users (startup_name, email, password_hash, role, lokasi, sektor_teknologi, pendanaan)
                 VALUES (?,?,?,?,?,?,?)',
                [
                    trim($body['startup_name'] ?? ''),
                    $email,
                    $hash,
                    $role,
                    trim($body['lokasi'] ?? '') ?: null,
                    trim($body['sektor_teknologi'] ?? '') ?: null,
                    is_numeric($body['pendanaan'] ?? '') ? (int)$body['pendanaan'] : null,
                ]
            );

            $token = jwtEncode([
                'user_id'          => $id,
                'role'             => $role,
                'email'            => $email,
                'startup_name'     => trim($body['startup_name'] ?? ''),
                'sektor_teknologi' => trim($body['sektor_teknologi'] ?? ''),
            ]);

            respond(true, [
                'token'            => $token,
                'user_id'          => $id,
                'role'             => $role,
                'email'            => $email,
                'startup_name'     => trim($body['startup_name'] ?? ''),
                'lokasi'           => trim($body['lokasi'] ?? ''),
                'sektor_teknologi' => trim($body['sektor_teknologi'] ?? ''),
                'pendanaan'        => is_numeric($body['pendanaan'] ?? '') ? (int)$body['pendanaan'] : null,
            ], 'Registrasi akun WorkPulse berhasil.');
        })(),

        // ── POST /api.php?action=login ────────────────────────
        $action === 'login' && $method === 'POST' => (function () {
            $body  = bodyJson();
            $email = strtolower(trim($body['email'] ?? ''));
            $pwd   = $body['password'] ?? '';

            $user = DB::fetchOne(
                'SELECT id, startup_name, email, password_hash, role, is_active,
                        lokasi, sektor_teknologi, pendanaan
                 FROM users WHERE email = ?', [$email]);

            if (!$user || !password_verify($pwd, $user['password_hash'])) {
                respond(false, null, 'Email kerja atau kata sandi tidak sesuai.', 401);
            }
            if (!$user['is_active']) {
                respond(false, null, 'Akun WorkPulse Anda dinonaktifkan. Silakan hubungi admin.', 403);
            }

            $roleInput = $body['role'] ?? '';
            if ($roleInput === 'owner') $roleInput = 'clevel';
            if ($roleInput === 'employee') $roleInput = 'staf';

            if ($roleInput && $roleInput !== $user['role']) {
                respond(false, null, "Akun ini terdaftar sebagai {$user['role']}, bukan $roleInput.", 403);
            }

            $token = jwtEncode([
                'user_id'          => $user['id'],
                'role'             => $user['role'],
                'email'            => $user['email'],
                'startup_name'     => $user['startup_name'],
                'sektor_teknologi' => $user['sektor_teknologi'] ?? '',
            ]);

            respond(true, [
                'token'            => $token,
                'user_id'          => $user['id'],
                'role'             => $user['role'],
                'email'            => $user['email'],
                'startup_name'     => $user['startup_name'],
                'lokasi'           => $user['lokasi'] ?? '',
                'sektor_teknologi' => $user['sektor_teknologi'] ?? '',
                'pendanaan'        => $user['pendanaan'] ?? null,
            ], 'Login berhasil. Selamat datang di WorkPulse.');
        })(),

        // ── POST /api.php?action=submit ───────────────────────
        $action === 'submit' && $method === 'POST' => (function () {
            $auth = requireAuth();
            $body = bodyJson();
            $role = $auth['role'];

            // Cek apakah user sudah pernah mengisi asesmen
            $alreadySubmitted = DB::fetchOne(
                'SELECT id FROM submissions WHERE user_id = ?',
                [$auth['user_id']]
            );
            if ($alreadySubmitted) {
                respond(false, null, 'Anda telah menyelesaikan pengisian kuesioner asesmen budaya kerja.', 403);
            }

            // Gabungkan data sesi
            $submitData = array_merge($body, [
                'email'        => $auth['email']        ?? '',
                'startup_name' => $auth['startup_name'] ?? ($body['startup_name'] ?? 'Startup'),
                'role'         => $role,
            ]);

            $result = Validator::validateSubmission($submitData, $role);

            // Validasi urutan pengisian: Staf hanya dapat mengisi jika C-Level dari startup yang sama sudah mengisi
            $clevelSubmission = null;
            if ($role === 'staf') {
                if (!$result['valid']) {
                    respond(false, [
                        'errors'   => $result['errors'],
                        'warnings' => $result['warnings'],
                    ], 'Kuesioner ditolak: ' . count($result['errors']) . ' respon belum lengkap.', 422);
                }

                $clevelSubmission = DB::fetchOne(
                    'SELECT s.id, s.score_vt, s.score_fi, s.score_kp, s.score_kk, s.score_bk, s.score_km, s.user_id
                     FROM submissions s
                     JOIN users u ON s.user_id = u.id
                     WHERE s.role = "clevel" AND s.validation_pass = 1 AND LOWER(TRIM(u.startup_name)) = LOWER(TRIM(?))
                     ORDER BY s.submitted_at DESC LIMIT 1',
                    [$auth['startup_name']]
                );

                if (!$clevelSubmission) {
                    respond(false, null, 'Founder / C-Level dari "' . $auth['startup_name'] . '" belum menyelesaikan kuesioner dasar. Asesmen staf dibuka setelah C-Level berpartisipasi.', 403);
                }
            }

            $pdo = DB::pdo();
            $pdo->beginTransaction();

            try {
                $factorSchema = [
                    'VT' => ['max' => 5, 'codes' => [
                        'vt_wp1' => 'WP-01', 'vt_wp2' => 'WP-02', 'vt_wp3' => 'WP-03',
                        'vt_wp4' => 'WP-04', 'vt_wp5' => 'WP-05', 'vt_wp6' => 'WP-06'
                    ]],
                    'KK' => ['max' => 5, 'codes' => [
                        'kk_wp1' => 'WP-07', 'kk_wp2' => 'WP-08', 'kk_wp3' => 'WP-09',
                        'kk_wp4' => 'WP-10', 'kk_wp5' => 'WP-11', 'kk_wp6' => 'WP-12'
                    ]],
                    'FI' => ['max' => 5, 'codes' => [
                        'fi_wp1' => 'WP-13', 'fi_wp2' => 'WP-14', 'fi_wp3' => 'WP-15',
                        'fi_wp4' => 'WP-16', 'fi_wp5' => 'WP-17', 'fi_wp6' => 'WP-18'
                    ]],
                    'BK' => ['max' => 5, 'codes' => [
                        'bk_wp1' => 'WP-19', 'bk_wp2' => 'WP-20', 'bk_wp3' => 'WP-21',
                        'bk_wp4' => 'WP-22', 'bk_wp5' => 'WP-23', 'bk_wp6' => 'WP-24'
                    ]],
                    'KM' => ['max' => 5, 'codes' => [
                        'km_wp1' => 'WP-25', 'km_wp2' => 'WP-26', 'km_wp3' => 'WP-27',
                        'km_wp4' => 'WP-28', 'km_wp5' => 'WP-29', 'km_wp6' => 'WP-30'
                    ]],
                    'KP' => ['max' => 5, 'codes' => [
                        'kp_wp1' => 'WP-31', 'kp_wp2' => 'WP-32', 'kp_wp3' => 'WP-33',
                        'kp_wp4' => 'WP-34', 'kp_wp5' => 'WP-35', 'kp_wp6' => 'WP-36'
                    ]],
                ];

                $combinedFactorResults = null;

                if ($role === 'clevel') {
                    $composite = $result['composite'];

                    // 1. Simpan submissions C-Level
                    DB::execute(
                        'INSERT INTO submissions
                           (user_id, role, status, validation_pass,
                            score_vt, score_kk, score_fi, score_bk, score_km, score_kp,
                            score_composite, score_on_5, health_category)
                         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)',
                        [
                            $auth['user_id'],
                            $role,
                            $result['valid'] ? 'submitted' : 'rejected',
                            $result['valid'] ? 1 : 0,
                            $result['scores']['VT']['normScore'] ?? null,
                            null,
                            $result['scores']['FI']['normScore'] ?? null,
                            null,
                            null,
                            $result['scores']['KP']['normScore'] ?? null,
                            $composite['composite'] ?? null,
                            $composite['scoreOn5']  ?? null,
                            $composite['category']  ?? null,
                        ]
                    );
                    $submissionId = (int)$pdo->lastInsertId();

                    // 2. Simpan jawaban individual
                    foreach ($result['clean_data']['answers'] as $qId => $val) {
                        $fKey = strtoupper(explode('_', $qId)[0]);
                        $code = $factorSchema[$fKey]['codes'][$qId] ?? '';
                        $max  = $factorSchema[$fKey]['max']         ?? 5;
                        DB::execute(
                            'INSERT INTO answers (submission_id, factor_key, question_id, question_code, answer_value, scale_max)
                             VALUES (?,?,?,?,?,?)',
                            [$submissionId, $fKey, $qId, $code, $val, $max]
                        );
                    }

                    // 3. Simpan log validasi
                    foreach (array_merge(
                        array_map(fn($e) => array_merge($e, ['level' => 'error']),   $result['errors']),
                        array_map(fn($w) => array_merge($w, ['level' => 'warning']), $result['warnings'])
                    ) as $log) {
                        DB::execute(
                            'INSERT INTO validation_log
                               (submission_id, level, factor_key, field_name, error_code, message)
                             VALUES (?,?,?,?,?,?)',
                            [
                                $submissionId,
                                $log['level'],
                                $log['factor'] ?? null,
                                $log['field']  ?? null,
                                $log['code'],
                                $log['message'],
                            ]
                        );
                    }
                } else {
                    // Staf: Simpan submission staf dan agregasikan ke header C-Level
                    DB::execute(
                        'INSERT INTO submissions
                           (user_id, role, status, validation_pass,
                            score_vt, score_kk, score_fi, score_bk, score_km, score_kp,
                            score_composite, score_on_5, health_category)
                         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)',
                        [
                            $auth['user_id'],
                            $role,
                            'submitted',
                            0,
                            null,
                            $result['scores']['KK']['normScore'] ?? null,
                            null,
                            $result['scores']['BK']['normScore'] ?? null,
                            $result['scores']['KM']['normScore'] ?? null,
                            null,
                            $result['composite']['composite'] ?? null,
                            $result['composite']['scoreOn5']  ?? null,
                            $result['composite']['category']  ?? null,
                        ]
                    );

                    // Hitung rata-rata evaluasi seluruh staf pada startup ini
                    $stafAvg = DB::fetchOne(
                        'SELECT 
                           AVG(s.score_kk) AS avg_kk,
                           AVG(s.score_bk) AS avg_bk,
                           AVG(s.score_km) AS avg_km
                         FROM submissions s
                         JOIN users u ON s.user_id = u.id
                         WHERE LOWER(TRIM(u.startup_name)) = LOWER(TRIM(?)) AND s.role = "staf"',
                        [$auth['startup_name']]
                    );

                    $submissionId = (int)$clevelSubmission['id'];

                    $combinedFactorResults = [
                        'VT' => ['normScore' => $clevelSubmission['score_vt']],
                        'FI' => ['normScore' => $clevelSubmission['score_fi']],
                        'KP' => ['normScore' => $clevelSubmission['score_kp']],
                        'KK' => ['normScore' => $stafAvg['avg_kk'] !== null ? round((float)$stafAvg['avg_kk'], 4) : null],
                        'BK' => ['normScore' => $stafAvg['avg_bk'] !== null ? round((float)$stafAvg['avg_bk'], 4) : null],
                        'KM' => ['normScore' => $stafAvg['avg_km'] !== null ? round((float)$stafAvg['avg_km'], 4) : null],
                    ];

                    $composite = Validator::computeComposite($combinedFactorResults);

                    // Update header submission utama startup
                    DB::execute(
                        'UPDATE submissions
                         SET score_kk = ?,
                             score_bk = ?,
                             score_km = ?,
                             score_composite = ?,
                             score_on_5 = ?,
                             health_category = ?,
                             status = "validated",
                             validation_pass = 1
                         WHERE id = ?',
                        [
                            $combinedFactorResults['KK']['normScore'],
                            $combinedFactorResults['BK']['normScore'],
                            $combinedFactorResults['KM']['normScore'],
                            $composite['composite'] ?? null,
                            $composite['scoreOn5']  ?? null,
                            $composite['category']  ?? null,
                            $submissionId
                        ]
                    );

                    // Simpan detail jawaban staf
                    foreach ($result['clean_data']['answers'] as $qId => $val) {
                        $fKey = strtoupper(explode('_', $qId)[0]);
                        $code = $factorSchema[$fKey]['codes'][$qId] ?? '';
                        $max  = $factorSchema[$fKey]['max']         ?? 5;
                        DB::execute(
                            'INSERT INTO answers (submission_id, factor_key, question_id, question_code, answer_value, scale_max)
                             VALUES (?,?,?,?,?,?)
                             ON DUPLICATE KEY UPDATE answer_value = ?',
                            [$submissionId, $fKey, $qId, $code, $val, $max, $val]
                        );
                    }

                    // Tampilkan skor 6 faktor komprehensif
                    $combinedScores = [
                        'VT' => [
                            'normScore' => $clevelSubmission['score_vt'],
                            'scoreOn5'  => $clevelSubmission['score_vt'] !== null ? round($clevelSubmission['score_vt'] * 5, 2) : null,
                        ],
                        'FI' => [
                            'normScore' => $clevelSubmission['score_fi'],
                            'scoreOn5'  => $clevelSubmission['score_fi'] !== null ? round($clevelSubmission['score_fi'] * 5, 2) : null,
                        ],
                        'KP' => [
                            'normScore' => $clevelSubmission['score_kp'],
                            'scoreOn5'  => $clevelSubmission['score_kp'] !== null ? round($clevelSubmission['score_kp'] * 5, 2) : null,
                        ],
                        'KK' => [
                            'normScore' => $combinedFactorResults['KK']['normScore'],
                            'scoreOn5'  => $combinedFactorResults['KK']['normScore'] !== null ? round($combinedFactorResults['KK']['normScore'] * 5, 2) : null,
                        ],
                        'BK' => [
                            'normScore' => $combinedFactorResults['BK']['normScore'],
                            'scoreOn5'  => $combinedFactorResults['BK']['normScore'] !== null ? round($combinedFactorResults['BK']['normScore'] * 5, 2) : null,
                        ],
                        'KM' => [
                            'normScore' => $combinedFactorResults['KM']['normScore'],
                            'scoreOn5'  => $combinedFactorResults['KM']['normScore'] !== null ? round($combinedFactorResults['KM']['normScore'] * 5, 2) : null,
                        ],
                    ];
                    $result['scores'] = $combinedScores;
                }

                $pdo->commit();
            } catch (Throwable $e) {
                $pdo->rollBack();
                respond(false, null,
                    APP_DEBUG ? 'Database error: ' . $e->getMessage() : 'Gagal memproses dan menyimpan kuesioner.',
                    500);
            }

            if ($role === 'clevel' && !$result['valid']) {
                respond(false, [
                    'errors'        => $result['errors'],
                    'warnings'      => $result['warnings'],
                    'submission_id' => $submissionId,
                ], 'Kuesioner ditolak: ' . count($result['errors']) . ' respon tidak valid.', 422);
            }

            respond(true, [
                'submission_id' => $submissionId,
                'scores'        => $result['scores'],
                'composite'     => $composite ?? $result['composite'],
                'warnings'      => $result['warnings'],
            ], 'Hasil asesmen budaya kerja & risiko burnout berhasil disimpan.');
        })(),

        // ── GET /api.php?action=dashboard ─────────────────────
        $action === 'dashboard' && $method === 'GET' => (function () {
            requireAuth();
            $stats = DB::fetchOne(
                'SELECT
                   COUNT(*) AS total_startup,
                   SUM(validation_pass = 1) AS total_valid,
                   ROUND(AVG(score_composite)*100, 1) AS avg_score_pct,
                   ROUND(MAX(score_composite)*5, 2) AS best_score,
                   ROUND(MIN(score_composite)*5, 2) AS worst_score,
                   SUM(score_composite >= 0.8) AS positif_count,
                   SUM(score_composite < 0.4) AS burnout_high_count
                 FROM submissions WHERE validation_pass = 1'
            );
            $factorAvg = DB::fetchOne('SELECT * FROM v_factor_averages');
            if ($factorAvg) {
                $factorAvg['avg_vt_5'] = round(($factorAvg['avg_vt'] ?? 0) * 5, 2);
                $factorAvg['avg_kk_5'] = round(($factorAvg['avg_kk'] ?? 0) * 5, 2);
                $factorAvg['avg_fi_5'] = round(($factorAvg['avg_fi'] ?? 0) * 5, 2);
                $factorAvg['avg_bk_5'] = round(($factorAvg['avg_bk'] ?? 0) * 5, 2);
                $factorAvg['avg_km_5'] = round(($factorAvg['avg_km'] ?? 0) * 5, 2);
                $factorAvg['avg_kp_5'] = round(($factorAvg['avg_kp'] ?? 0) * 5, 2);
            }
            respond(true, ['stats' => $stats, 'factor_averages' => $factorAvg]);
        })(),

        // ── GET /api.php?action=startup_list ──────────────────
        $action === 'startup_list' && $method === 'GET' => (function () {
            requireAuth();
            $limit  = min((int)($_GET['limit'] ?? PAGE_LIMIT_DEFAULT), PAGE_LIMIT_MAX);
            $offset = (int)($_GET['offset'] ?? 0);
            $search = '%' . ($_GET['search'] ?? ($_GET['q'] ?? '')) . '%';

            $rows = DB::fetchAll(
                'SELECT s.submission_id, s.startup_name, s.lokasi, s.sektor_teknologi, s.role,
                        s.score_composite, s.score_on_5, s.health_category, s.submitted_at, u.pendanaan
                 FROM v_submission_summary s
                 LEFT JOIN users u ON s.user_id = u.id
                 WHERE s.validation_pass = 1
                   AND (s.startup_name LIKE ? OR s.lokasi LIKE ? OR s.sektor_teknologi LIKE ?)
                 ORDER BY s.submitted_at DESC
                 LIMIT ? OFFSET ?',
                [$search, $search, $search, $limit, $offset]
            );
            $total = DB::fetchOne(
                'SELECT COUNT(*) as cnt FROM v_submission_summary
                 WHERE validation_pass = 1
                   AND (startup_name LIKE ? OR lokasi LIKE ? OR sektor_teknologi LIKE ?)',
                [$search, $search, $search]
            );
            respond(true, ['rows' => $rows, 'total' => (int)($total['cnt'] ?? 0), 'limit' => $limit, 'offset' => $offset]);
        })(),

        // ── GET /api.php?action=factor_averages ───────────────
        $action === 'factor_averages' && $method === 'GET' => (function () {
            requireAuth();
            $data = DB::fetchOne('SELECT * FROM v_factor_averages');
            if ($data) {
                $data['avg_vt_5'] = round(($data['avg_vt'] ?? 0) * 5, 2);
                $data['avg_kk_5'] = round(($data['avg_kk'] ?? 0) * 5, 2);
                $data['avg_fi_5'] = round(($data['avg_fi'] ?? 0) * 5, 2);
                $data['avg_bk_5'] = round(($data['avg_bk'] ?? 0) * 5, 2);
                $data['avg_km_5'] = round(($data['avg_km'] ?? 0) * 5, 2);
                $data['avg_kp_5'] = round(($data['avg_kp'] ?? 0) * 5, 2);
            }
            respond(true, $data);
        })(),

        // ── 404 ───────────────────────────────────────────────
        default => respond(false, null, "Endpoint '$action' tidak ditemukan.", 404),
    };

} catch (Throwable $e) {
    respond(false, APP_DEBUG ? ['trace' => $e->getMessage()] : null,
        'Terjadi kendala internal server.', 500);
}
