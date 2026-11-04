<?php
/**
 * WorkPulse — Clinical Validator & Burnout Scoring Engine
 */

class Validator {
    public static array $FACTOR_SCHEMA = [
        'VT' => [
            'label'     => 'Visi & Transparansi',
            'role'      => 'clevel',
            'weight'    => 0.20,
            'scale_max' => 5,
            'questions' => [
                ['id' => 'vt_wp1', 'code' => 'WP-01', 'text' => 'Arah strategis & runway dikomunikasikan transparan'],
                ['id' => 'vt_wp2', 'code' => 'WP-02', 'text' => 'Target OKR/KPI terukur & selaras visi jangka panjang'],
                ['id' => 'vt_wp3', 'code' => 'WP-03', 'text' => 'Perubahan prioritas bisnis disampaikan terbuka'],
                ['id' => 'vt_wp4', 'code' => 'WP-04', 'text' => 'Keterbukaan metrik performa produk & pertumbuhan'],
                ['id' => 'vt_wp5', 'code' => 'WP-05', 'text' => 'Keputusan diambil berbasis data objektif'],
                ['id' => 'vt_wp6', 'code' => 'WP-06', 'text' => 'Karyawan memahami kontribusi terhadap misi startup'],
            ]
        ],
        'KK' => [
            'label'     => 'Kualitas Kepemimpinan',
            'role'      => 'staf',
            'weight'    => 0.18,
            'scale_max' => 5,
            'questions' => [
                ['id' => 'kk_wp1', 'code' => 'WP-07', 'text' => 'Pimpinan memberi arahan tanpa micromanagement'],
                ['id' => 'kk_wp2', 'code' => 'WP-08', 'text' => 'Pimpinan suportif & mendengarkan kendala operasional'],
                ['id' => 'kk_wp3', 'code' => 'WP-09', 'text' => 'Apresiasi & pengakuan layak atas kontribusi tim'],
                ['id' => 'kk_wp4', 'code' => 'WP-10', 'text' => 'Pimpinan mengelola konflik adil tanpa pilih kasih'],
                ['id' => 'kk_wp5', 'code' => 'WP-11', 'text' => 'Pimpinan terbuka menerima kritik & inovasi teknis'],
                ['id' => 'kk_wp6', 'code' => 'WP-12', 'text' => 'Keteladanan profesional menginspirasi loyalitas staf'],
            ]
        ],
        'FI' => [
            'label'     => 'Fasilitas & Infrastruktur',
            'role'      => 'clevel',
            'weight'    => 0.15,
            'scale_max' => 5,
            'questions' => [
                ['id' => 'fi_wp1', 'code' => 'WP-13', 'text' => 'Perangkat kerja (hardware) memadai untuk kebutuhan tim'],
                ['id' => 'fi_wp2', 'code' => 'WP-14', 'text' => 'Investasi pada lisensi software & cloud tooling modern'],
                ['id' => 'fi_wp3', 'code' => 'WP-15', 'text' => 'Infrastruktur teknologi & knowledge base internal tertata'],
                ['id' => 'fi_wp4', 'code' => 'WP-16', 'text' => 'Lingkungan kerja & tunjangan hybrid/remote ergonomis'],
                ['id' => 'fi_wp5', 'code' => 'WP-17', 'text' => 'Anggaran memadai untuk keamanan data & compliance'],
                ['id' => 'fi_wp6', 'code' => 'WP-18', 'text' => 'Otomatisasi CI/CD & workflow meminimalkan kerja manual'],
            ]
        ],
        'BK' => [
            'label'     => 'Beban Kerja (Workload)',
            'role'      => 'staf',
            'weight'    => 0.17,
            'scale_max' => 5,
            'questions' => [
                ['id' => 'bk_wp1', 'code' => 'WP-19', 'text' => 'Volume sprint selesai dalam jam kerja tanpa lembur kronis'],
                ['id' => 'bk_wp2', 'code' => 'WP-20', 'text' => 'Tenggat waktu proyek realistis sesuai kapasitas tim'],
                ['id' => 'bk_wp3', 'code' => 'WP-21', 'text' => 'Bebas dari ekspektasi respon pesan darurat di luar jam kerja'],
                ['id' => 'bk_wp4', 'code' => 'WP-22', 'text' => 'Distribusi tugas seimbang & tidak sentralistis'],
                ['id' => 'bk_wp5', 'code' => 'WP-23', 'text' => 'Waktu istirahat & recovery cukup setelah fase rilis'],
                ['id' => 'bk_wp6', 'code' => 'WP-24', 'text' => 'Multi-tasking proyek tidak memicu stres berlebihan'],
            ]
        ],
        'KM' => [
            'label'     => 'Kesejahteraan Mental',
            'role'      => 'staf',
            'weight'    => 0.15,
            'scale_max' => 5,
            'questions' => [
                ['id' => 'km_wp1', 'code' => 'WP-25', 'text' => 'Keamanan psikologis (psychological safety) dalam bersuara'],
                ['id' => 'km_wp2', 'code' => 'WP-26', 'text' => 'Lingkungan kerja bebas dari budaya toxic & intimidasi'],
                ['id' => 'km_wp3', 'code' => 'WP-27', 'text' => 'Bebas dari gejala kelelahan fisik-mental kronis (exhaustion)'],
                ['id' => 'km_wp4', 'code' => 'WP-28', 'text' => 'Rekan kerja suportif secara empati saat beban berat'],
                ['id' => 'km_wp5', 'code' => 'WP-29', 'text' => 'Startup memiliki kepedulian terhadap kesehatan mental staf'],
                ['id' => 'km_wp6', 'code' => 'WP-30', 'text' => 'Antusiasme & motivasi terjaga dalam rutinitas harian'],
            ]
        ],
        'KP' => [
            'label'     => 'Kompensasi & Karir',
            'role'      => 'clevel',
            'weight'    => 0.15,
            'scale_max' => 5,
            'questions' => [
                ['id' => 'kp_wp1', 'code' => 'WP-31', 'text' => 'Gaji pokok & tunjangan kompetitif dibanding standar industri'],
                ['id' => 'kp_wp2', 'code' => 'WP-32', 'text' => 'Skema ESOP / bonus kinerja terstruktur & transparan'],
                ['id' => 'kp_wp3', 'code' => 'WP-33', 'text' => 'Jalur karir (career ladder) & kriteria promosi jelas'],
                ['id' => 'kp_wp4', 'code' => 'WP-34', 'text' => 'Alokasi anggaran untuk upskilling & sertifikasi tim'],
                ['id' => 'kp_wp5', 'code' => 'WP-35', 'text' => 'Peninjauan kompensasi berkala berbasis performa objektif'],
                ['id' => 'kp_wp6', 'code' => 'WP-36', 'text' => 'Strategi retensi talenta efektif menekan turnover staf'],
            ]
        ],
    ];

    private static array $ROLE_FACTORS = [
        'clevel' => ['VT', 'FI', 'KP'],
        'staf'   => ['KK', 'BK', 'KM'],
    ];

    public static function validateSubmission(array $data, string $role): array {
        $errors   = [];
        $warnings = [];

        // Validasi profil pengguna
        $name = trim($data['startup_name'] ?? '');
        if ($name && mb_strlen($name) < 2) {
            $warnings[] = self::w('startup_name', 'TOO_SHORT', 'Nama Startup minimal 2 karakter.');
        }

        $email = trim($data['email'] ?? '');
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $warnings[] = self::w('email', 'INVALID_EMAIL', 'Format email tidak valid.');
        }

        if (!in_array($role, ['clevel', 'staf'])) {
            $errors[] = self::e('role', 'INVALID_ROLE', "Peran '$role' tidak valid. Pilih C-Level atau Staf.");
        }

        // Validasi respon 3 faktor wajib sesuai role
        $required = self::$ROLE_FACTORS[$role] ?? [];
        $factorResults = [];
        foreach ($required as $fKey) {
            $fr = self::validateFactor($fKey, $data);
            $factorResults[$fKey] = $fr;
            $errors   = array_merge($errors, $fr['errors']);
            $warnings = array_merge($warnings, $fr['warnings']);
        }

        // Analisis disparitas antar faktor
        $scores = [];
        foreach ($factorResults as $k => $r) {
            if ($r['normScore'] !== null) $scores[$k] = $r['normScore'];
        }
        if (count($scores) >= 2) {
            $range = round(max($scores) - min($scores), 4);
            if ($range > 0.5) {
                $hk = array_keys($scores, max($scores))[0];
                $lk = array_keys($scores, min($scores))[0];
                $warnings[] = self::w(
                    null,
                    'DISPARITY',
                    "Kesenjangan indikator tajam antara pilar $hk (" . round(max($scores) * 100) . "%) dan $lk (" . round(min($scores) * 100) . "%)."
                );
            }
        }

        $composite = self::computeComposite($factorResults);
        $clean     = self::sanitize($data, $role, $required);

        return [
            'valid'      => empty($errors),
            'errors'     => $errors,
            'warnings'   => $warnings,
            'scores'     => self::formatScores($factorResults),
            'composite'  => $composite,
            'clean_data' => $clean,
            'meta'       => [
                'role'             => $role,
                'required_factors' => $required,
                'total_errors'     => count($errors),
                'total_warnings'   => count($warnings)
            ]
        ];
    }

    private static function validateFactor(string $fKey, array $data): array {
        $factor   = self::$FACTOR_SCHEMA[$fKey];
        $errors   = [];
        $warnings = [];
        $rawVals  = [];
        $answered = 0;

        foreach ($factor['questions'] as $q) {
            $raw = $data[$q['id']] ?? null;
            if ($raw === null || $raw === '') {
                $errors[] = self::e($q['id'], 'MISSING', "{$q['code']}: \"{$q['text']}\" wajib diisi.", $fKey);
                continue;
            }
            if (!is_numeric($raw) || !in_array((int)$raw, range(1, $factor['scale_max']))) {
                $errors[] = self::e($q['id'], 'OUT_OF_RANGE', "{$q['code']}: Skor $raw di luar skala (1–{$factor['scale_max']}).", $fKey);
                continue;
            }
            $rawVals[] = (int)$raw;
            $answered++;
        }

        $total = count($factor['questions']);
        $pct   = $total > 0 ? round($answered / $total, 4) : 0;
        if ($answered > 0 && $pct < 0.8) {
            $warnings[] = self::w($fKey, 'LOW_COMPLETION', "Pilar $fKey baru terisi $answered/$total.", $fKey);
        }
        if ($answered >= 4 && count(array_unique($rawVals)) === 1) {
            $warnings[] = self::w($fKey, 'STRAIGHT_LINE', "Pilar $fKey: semua respon seragam ({$rawVals[0]}).", $fKey);
        }

        $rawMean   = $answered > 0 ? round(array_sum($rawVals) / $answered, 4) : null;
        $normScore = $rawMean !== null ? round($rawMean / $factor['scale_max'], 4) : null;

        return [
            'errors'        => $errors,
            'warnings'      => $warnings,
            'answered'      => $answered,
            'total'         => $total,
            'completionPct' => $pct,
            'rawMean'       => $rawMean,
            'normScore'     => $normScore,
            'rawVals'       => $rawVals
        ];
    }

    public static function computeComposite(array $factorResults): ?array {
        $ws = 0;
        $wt = 0;
        foreach ($factorResults as $fKey => $r) {
            if ($r['normScore'] === null) continue;
            $w   = self::$FACTOR_SCHEMA[$fKey]['weight'];
            $ws += $r['normScore'] * $w;
            $wt += $w;
        }
        if ($wt === 0) return null;

        $comp = round($ws / $wt, 4);
        $s5   = round($comp * 5, 2);

        // Kategori Risiko Burnout & Kesehatan Budaya Organisasi
        $cats = [
            ['label' => 'Budaya Kerja Positif',      'min' => 0.80],
            ['label' => 'Kondisi Stabil',            'min' => 0.60],
            ['label' => 'Waspada Gejala Burnout',    'min' => 0.40],
            ['label' => 'Risiko Burnout Tinggi',     'min' => 0.00],
        ];

        $cat = 'Risiko Burnout Tinggi';
        foreach ($cats as $c) {
            if ($comp >= $c['min']) {
                $cat = $c['label'];
                break;
            }
        }

        return [
            'composite' => $comp,
            'scoreOn5'  => $s5,
            'category'  => $cat
        ];
    }

    private static function sanitize(array $data, string $role, array $factors): array {
        $clean = [
            'startup_name'     => trim($data['startup_name'] ?? ($data['umkm_name'] ?? '')),
            'email'            => strtolower(trim($data['email'] ?? '')),
            'role'             => $role,
            'lokasi'           => trim($data['lokasi'] ?? ''),
            'sektor_teknologi' => trim($data['sektor_teknologi'] ?? ($data['sektor'] ?? '')),
            'pendanaan'        => is_numeric($data['pendanaan'] ?? ($data['omzet'] ?? '')) ? (int)($data['pendanaan'] ?? $data['omzet']) : null,
            'answers'          => []
        ];

        foreach ($factors as $fKey) {
            foreach (self::$FACTOR_SCHEMA[$fKey]['questions'] as $q) {
                $v = $data[$q['id']] ?? null;
                if ($v !== null && $v !== '') {
                    $clean['answers'][$q['id']] = (int)$v;
                }
            }
        }
        return $clean;
    }

    private static function formatScores(array $fr): array {
        $out = [];
        foreach ($fr as $k => $r) {
            $out[$k] = [
                'rawMean'       => $r['rawMean'],
                'normScore'     => $r['normScore'],
                'scoreOn5'      => $r['normScore'] !== null ? round($r['normScore'] * 5, 2) : null,
                'completionPct' => $r['completionPct'],
                'answered'      => $r['answered'],
                'total'         => $r['total']
            ];
        }
        return $out;
    }

    private static function e(string $f, string $c, string $m, string $fk = ''): array {
        return ['field' => $f, 'factor' => $fk, 'code' => $c, 'message' => $m];
    }

    private static function w(?string $f, string $c, string $m, string $fk = ''): array {
        return ['field' => $f, 'factor' => $fk, 'code' => $c, 'message' => $m];
    }
}
