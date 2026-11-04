<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\FactorDefinition;
use App\Models\QuestionDefinition;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WorkPulseController extends Controller
{
    /**
     * Konfigurasi 6 Faktor Kesehatan Budaya & Risiko Burnout
     */
    protected array $roleFactors = [
        'clevel' => ['VT', 'FI', 'KP'], // Visi & Transparansi, Fasilitas & Infrastruktur, Kompensasi & Karir
        'staf'   => ['KK', 'BK', 'KM'], // Kualitas Kepemimpinan, Beban Kerja (Workload), Kesejahteraan Mental
    ];

    /* ============================================================
     *  WEB BLADE ROUTES & CONTROLLERS
     * ============================================================ */

    /**
     * Tampilan Landing Page
     */
    public function landing()
    {
        return view('landing');
    }

    /**
     * Halaman Login Web
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Proses Login Web
     */
    public function handleWebLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Email kerja resmi wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('success', 'Selamat datang kembali di WorkPulse!');
        }

        return back()->withErrors([
            'email' => 'Kombinasi email dan kata sandi tidak cocok.',
        ])->onlyInput('email');
    }

    /**
     * Halaman Register Web
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    /**
     * Proses Register Web
     */
    public function handleWebRegister(Request $request)
    {
        $request->validate([
            'startup_name'     => 'required|string|max:120',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|string|min:6|confirmed',
            'role'             => 'required|in:clevel,staf',
            'lokasi'           => 'nullable|string|max:100',
            'sektor_teknologi' => 'nullable|string|max:100',
            'pendanaan'        => 'nullable|string|max:100',
        ], [
            'startup_name.required' => 'Nama startup wajib diisi.',
            'email.unique'          => 'Email kerja ini sudah terdaftar di sistem.',
            'password.min'          => 'Kata sandi minimal harus 6 karakter.',
            'password.confirmed'    => 'Konfirmasi kata sandi tidak cocok.',
            'role.in'               => 'Peran harus C-Level atau Staf.',
        ]);

        $startupName = trim($request->startup_name);
        $name = $startupName ?: explode('@', $request->email)[0];

        $user = User::create([
            'name'             => $name,
            'email'            => strtolower(trim($request->email)),
            'password'         => Hash::make($request->password),
            'startup_name'     => $startupName,
            'lokasi'           => $request->lokasi,
            'sektor_teknologi' => $request->sektor_teknologi,
            'pendanaan'        => $request->pendanaan,
            'role'             => $request->role,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Akun startup berhasil didaftarkan! Selamat datang di WorkPulse.');
    }

    /**
     * Proses Logout Web
     */
    public function handleWebLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah berhasil keluar akun.');
    }

    /**
     * Tampilan Dashboard Web
     */
    public function showDashboard(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            $user = User::first() ?? new User([
                'name'         => 'Founder Zenith',
                'startup_name' => 'Zenith AI Analytics',
                'role'         => 'clevel',
                'lokasi'       => 'Jakarta',
                'sektor_teknologi' => 'SaaS',
            ]);
        }

        $totalStartups = User::distinct('startup_name')->count('startup_name');
        $totalAssessments = Submission::count();
        $latestSubmission = Submission::where('user_id', $user->id ?? 0)->latest('submitted_at')->first();

        $statusCounts = Submission::select('burnout_status', DB::raw('count(*) as count'))
            ->groupBy('burnout_status')
            ->pluck('count', 'burnout_status')
            ->toArray();

        $healthyCount = ($statusCounts['Budaya Kerja Positif'] ?? 0) + ($statusCounts['Kondisi Stabil'] ?? 0);
        $healthyPercentage = $totalAssessments > 0 ? round(($healthyCount / $totalAssessments) * 100) : 100;

        $averages = [
            'VT' => round(Submission::whereNotNull('score_vt')->avg('score_vt') ?? 4.10, 2),
            'KK' => round(Submission::whereNotNull('score_kk')->avg('score_kk') ?? 3.85, 2),
            'FI' => round(Submission::whereNotNull('score_fi')->avg('score_fi') ?? 4.25, 2),
            'BK' => round(Submission::whereNotNull('score_bk')->avg('score_bk') ?? 3.40, 2),
            'KM' => round(Submission::whereNotNull('score_km')->avg('score_km') ?? 3.65, 2),
            'KP' => round(Submission::whereNotNull('score_kp')->avg('score_kp') ?? 3.90, 2),
        ];

        $recentStartups = Submission::with('user')
            ->latest('submitted_at')
            ->take(10)
            ->get()
            ->map(function ($s) {
                return [
                    'startup_name'     => $s->user->startup_name ?? '-',
                    'lokasi'           => $s->user->lokasi ?? 'Indonesia',
                    'sektor_teknologi' => $s->user->sektor_teknologi ?? '-',
                    'role'             => $s->role,
                    'score_on_5'       => $s->final_score,
                    'health_category'  => $s->burnout_status,
                ];
            });

        return view('dashboard.index', compact(
            'user',
            'totalStartups',
            'totalAssessments',
            'latestSubmission',
            'statusCounts',
            'healthyPercentage',
            'averages',
            'recentStartups'
        ));
    }

    /**
     * Tampilan Formulir Asesmen Web
     */
    public function showAssessment(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            $user = User::first() ?? new User(['role' => 'clevel', 'startup_name' => 'Demo Startup']);
        }

        $role = $user->role ?? 'clevel';
        $questions = QuestionDefinition::where('role_target', $role)
            ->orderBy('factor_code')
            ->orderBy('question_index')
            ->get();

        $factorNames = [
            'VT' => 'Visi & Transparansi (VT)',
            'KK' => 'Kualitas Kepemimpinan (KK)',
            'FI' => 'Fasilitas & Infrastruktur (FI)',
            'BK' => 'Beban Kerja / Workload (BK)',
            'KM' => 'Kesejahteraan Mental (KM)',
            'KP' => 'Kompensasi & Karir (KP)',
        ];

        $groupedQuestions = [];
        foreach ($questions as $q) {
            if (!isset($groupedQuestions[$q->factor_code])) {
                $groupedQuestions[$q->factor_code] = [
                    'factor_name' => $factorNames[$q->factor_code] ?? $q->factor_code,
                    'items'       => [],
                ];
            }
            $groupedQuestions[$q->factor_code]['items'][] = $q;
        }

        return view('dashboard.assessment', compact('user', 'groupedQuestions'));
    }

    /**
     * Proses Submit Formulir Asesmen Web
     */
    public function handleWebAssessment(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            $user = User::first();
            if (!$user) {
                return redirect()->route('login')->withErrors(['email' => 'Silakan login terlebih dahulu.']);
            }
        }

        $role = $user->role;
        $activeFactors = $this->roleFactors[$role] ?? ['KK', 'BK', 'KM'];
        $answersData = $request->input('answers', []);

        $factorScores = [];
        $validatedAnswers = [];

        foreach ($activeFactors as $fCode) {
            if (!isset($answersData[$fCode]) || !is_array($answersData[$fCode])) {
                return back()->withErrors(["Seluruh indikator untuk pilar [{$fCode}] wajib dijawab."])->withInput();
            }

            $scores = $answersData[$fCode];
            $validInts = [];
            foreach ($scores as $idx => $sc) {
                $val = (int)$sc;
                $validInts[] = $val;
                $validatedAnswers[] = [
                    'factor_key'     => $fCode,
                    'question_index' => $idx,
                    'score'          => $val,
                ];
            }
            $factorScores[$fCode] = round(array_sum($validInts) / max(1, count($validInts)), 2);
        }

        $finalScore = round(array_sum($factorScores) / max(1, count($factorScores)), 2);
        $burnoutStatus = $this->evaluateBurnoutStatus($finalScore);

        DB::beginTransaction();
        try {
            $submission = Submission::create([
                'user_id'        => $user->id,
                'role'           => $role,
                'score_vt'       => $factorScores['VT'] ?? null,
                'score_kk'       => $factorScores['KK'] ?? null,
                'score_fi'       => $factorScores['FI'] ?? null,
                'score_bk'       => $factorScores['BK'] ?? null,
                'score_km'       => $factorScores['KM'] ?? null,
                'score_kp'       => $factorScores['KP'] ?? null,
                'final_score'    => $finalScore,
                'burnout_status' => $burnoutStatus,
                'raw_scores'     => $factorScores,
                'submitted_at'   => now(),
            ]);

            foreach ($validatedAnswers as $ans) {
                $ans['submission_id'] = $submission->id;
                $ans['created_at'] = now();
                $ans['updated_at'] = now();
                Answer::create($ans);
            }

            DB::commit();

            // Send assessment data to N8N webhook if configured
            $this->sendToN8nWebhook($user, $role, $validatedAnswers, $factorScores, $finalScore, $burnoutStatus);

            return redirect()->route('dashboard')->with('success', "Asesmen berhasil dievaluasi! Skor Budaya Kerja: {$finalScore} / 5.0 ({$burnoutStatus})");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['Terjadi kendala saat menyimpan asesmen: ' . $e->getMessage()]);
        }
    }

    /* ============================================================
     *  REST API HANDLERS (Untuk Integrasi & Kompatibilitas)
     * ============================================================ */

    /**
     * Register User Baru (C-Level atau Staf)
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'             => 'nullable|string|max:100',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|string|min:6',
            'startup_name'     => 'required|string|max:120',
            'sektor_teknologi' => 'nullable|string|max:100',
            'pendanaan'        => 'nullable',
            'lokasi'           => 'nullable|string|max:100',
            'role'             => 'required|in:clevel,staf',
        ], [
            'email.unique'         => 'Email ini sudah terdaftar dalam sistem WorkPulse.',
            'password.min'         => 'Password minimal harus 6 karakter.',
            'startup_name.required'=> 'Nama startup wajib diisi.',
            'role.in'              => 'Peran harus dipilih antara C-Level (Founder) atau Staf.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $startupName = trim($request->input('startup_name', ''));
        $name = trim($request->input('name', '')) ?: ($startupName ?: explode('@', $request->input('email', ''))[0]);
        $pendanaan = $request->input('pendanaan');
        if (is_numeric($pendanaan)) {
            $pendanaan = 'IDR ' . number_format((float)$pendanaan, 0, ',', '.');
        }

        $user = User::create([
            'name'             => $name,
            'email'            => strtolower(trim($request->email)),
            'password'         => Hash::make($request->password),
            'startup_name'     => $startupName,
            'lokasi'           => trim($request->input('lokasi', '')),
            'sektor_teknologi' => trim($request->input('sektor_teknologi', '')),
            'pendanaan'        => $pendanaan ? (string)$pendanaan : null,
            'role'             => $request->role,
        ]);

        $token = bin2hex(random_bytes(32));

        return response()->json([
            'status'  => 'success',
            'success' => true,
            'message' => 'Registrasi akun WorkPulse berhasil!',
            'data'    => [
                'token'            => $token,
                'user_id'          => $user->id,
                'name'             => $user->name,
                'startup_name'     => $user->startup_name,
                'email'            => $user->email,
                'role'             => $user->role,
                'lokasi'           => $user->lokasi,
                'sektor_teknologi' => $user->sektor_teknologi,
                'pendanaan'        => $user->pendanaan,
                'user'             => [
                    'id'               => $user->id,
                    'name'             => $user->name,
                    'email'            => $user->email,
                    'startup_name'     => $user->startup_name,
                    'lokasi'           => $user->lokasi,
                    'sektor_teknologi' => $user->sektor_teknologi,
                    'pendanaan'        => $user->pendanaan,
                    'role'             => $user->role,
                ],
            ],
        ], 201);
    }

    /**
     * Login User
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'success' => false,
                'message' => 'Format email atau password tidak valid.',
            ], 422);
        }

        $user = User::where('email', strtolower(trim($request->email)))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'success' => false,
                'message' => 'Kombinasi email dan password tidak cocok.',
            ], 401);
        }

        $token = bin2hex(random_bytes(32));

        return response()->json([
            'status'  => 'success',
            'success' => true,
            'message' => 'Selamat datang kembali di WorkPulse!',
            'data'    => [
                'token'            => $token,
                'user_id'          => $user->id,
                'name'             => $user->name,
                'startup_name'     => $user->startup_name,
                'email'            => $user->email,
                'role'             => $user->role,
                'lokasi'           => $user->lokasi,
                'sektor_teknologi' => $user->sektor_teknologi,
                'pendanaan'        => $user->pendanaan,
                'user'             => [
                    'id'               => $user->id,
                    'name'             => $user->name,
                    'email'            => $user->email,
                    'startup_name'     => $user->startup_name,
                    'lokasi'           => $user->lokasi,
                    'sektor_teknologi' => $user->sektor_teknologi,
                    'pendanaan'        => $user->pendanaan,
                    'role'             => $user->role,
                ],
            ],
        ]);
    }

    /**
     * Dapatkan Daftar Pertanyaan Berdasarkan Peran
     */
    public function getQuestions(Request $request): JsonResponse
    {
        $role = $request->query('role', 'staf');
        if (!in_array($role, ['clevel', 'staf'])) {
            $role = 'staf';
        }

        $questions = QuestionDefinition::where('role_target', $role)
            ->with('factor')
            ->orderBy('factor_code')
            ->orderBy('question_index')
            ->get();

        return response()->json([
            'status' => 'success',
            'role'   => $role,
            'count'  => $questions->count(),
            'data'   => $questions,
        ]);
    }

    /**
     * Submit Asesmen Budaya Kerja & Burnout Risk
     */
    public function submitAssessment(Request $request): JsonResponse
    {
        $user = Auth::user() ?? User::find($request->user_id);

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Sesi pengguna tidak valid. Silakan login kembali.',
            ], 401);
        }

        $role = $user->role;
        $activeFactors = $this->roleFactors[$role] ?? ['KK', 'BK', 'KM'];
        $answersData = $request->input('answers', []);

        // Validasi kelengkapan jawaban: masing-masing 3 faktor harus memiliki 6 jawaban (skala Likert 1-5)
        $factorScores = [];
        $validatedAnswers = [];

        foreach ($activeFactors as $fCode) {
            if (!isset($answersData[$fCode]) || !is_array($answersData[$fCode])) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Seluruh indikator untuk pilar [{$fCode}] wajib diisi.",
                ], 422);
            }

            $scores = $answersData[$fCode];
            if (count($scores) < 6) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Pilar [{$fCode}] membutuhkan 6 butir respon penilaian lengkap.",
                ], 422);
            }

            $validInts = [];
            foreach ($scores as $idx => $sc) {
                $val = (int)$sc;
                if ($val < 1 || $val > 5) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => "Nilai indikator harus dalam rentang skala Likert 1 hingga 5.",
                    ], 422);
                }
                $validInts[] = $val;
                $validatedAnswers[] = [
                    'factor_key'     => $fCode,
                    'question_index' => $idx,
                    'score'          => $val,
                ];
            }

            // Hitung rata-rata faktor
            $factorScores[$fCode] = round(array_sum($validInts) / count($validInts), 2);
        }

        // Hitung skor akhir peranan
        $finalScore = round(array_sum($factorScores) / count($factorScores), 2);

        // Tentukan kategori risiko burnout
        $burnoutStatus = $this->evaluateBurnoutStatus($finalScore);

        DB::beginTransaction();
        try {
            $submission = Submission::create([
                'user_id'        => $user->id,
                'role'           => $role,
                'score_vt'       => $factorScores['VT'] ?? null,
                'score_kk'       => $factorScores['KK'] ?? null,
                'score_fi'       => $factorScores['FI'] ?? null,
                'score_bk'       => $factorScores['BK'] ?? null,
                'score_km'       => $factorScores['KM'] ?? null,
                'score_kp'       => $factorScores['KP'] ?? null,
                'final_score'    => $finalScore,
                'burnout_status' => $burnoutStatus,
                'raw_scores'     => $factorScores,
                'submitted_at'   => now(),
            ]);

            foreach ($validatedAnswers as $ans) {
                $ans['submission_id'] = $submission->id;
                $ans['created_at'] = now();
                $ans['updated_at'] = now();
                Answer::create($ans);
            }

            // Catat log validasi berhasil
            DB::table('validation_log')->insert([
                'user_id'    => $user->id,
                'ip_address' => $request->ip(),
                'status'     => 'success',
                'message'    => "Asesmen sukses diserahkan. Skor: {$finalScore} ({$burnoutStatus})",
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            // Kirim asesmen ke N8N Webhook (diaktifkan untuk API juga)
            $this->sendToN8nWebhook($user, $role, $validatedAnswers, $factorScores, $finalScore, $burnoutStatus);

            return response()->json([
                'status'  => 'success',
                'message' => 'Asesmen budaya kerja dan risiko burnout berhasil dievaluasi!',
                'data'    => [
                    'submission_id'  => $submission->id,
                    'final_score'    => $finalScore,
                    'burnout_status' => $burnoutStatus,
                    'factor_scores'  => $factorScores,
                    'status_meta'    => $submission->status_meta,
                    'submitted_at'   => $submission->submitted_at->toIso8601String(),
                ],
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kendala teknis saat menyimpan asesmen: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper Terpusat untuk Mengirim Data ke N8N Webhook
     */
    protected function sendToN8nWebhook($user, $role, $validatedAnswers, $factorScores, $finalScore, $burnoutStatus)
    {
        $webhookUrl = config('services.n8n_webhook') ?: env('N8N_WEBHOOK_URL');
        if (!$webhookUrl) {
            return;
        }

        try {
            $payload = [
                'user_id'        => $user->id,
                'user_name'      => $user->name,
                'email'          => $user->email,
                'startup_name'   => $user->startup_name,
                'role'           => $role,
                'answers'        => $validatedAnswers,
                'scores'         => $factorScores,
                'final_score'    => $finalScore,
                'burnout_status' => $burnoutStatus,
                'submitted_at'   => now()->toIso8601String(),
            ];

            Log::info('WorkPulse: Mengirim asesmen ke N8N webhook', [
                'url'     => $webhookUrl,
                'user_id' => $user->id,
            ]);

            $response = Http::timeout(10)
                ->withHeaders([
                    'Bypass-Tunnel-Reminder' => 'true',
                    'User-Agent'             => 'WorkPulse-Webhook/1.0',
                ])
                ->post($webhookUrl, $payload);

            if ($response->successful()) {
                Log::info('WorkPulse: Sukses terkirim ke N8N', [
                    'status' => $response->status(),
                    'body'   => $response->json() ?? $response->body(),
                ]);
            } else {
                Log::warning('WorkPulse: N8N merespons status non-200', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Throwable $ex) {
            Log::error('WorkPulse: Gagal menghubungi webhook N8N', [
                'error' => $ex->getMessage(),
            ]);
        }
    }

    /**
     * Dashboard Data Aggregator
     */
    public function getDashboard(Request $request): JsonResponse
    {
        $user = Auth::user() ?? User::find($request->user_id);

        $latestSubmission = null;
        if ($user) {
            $latestSubmission = Submission::where('user_id', $user->id)
                ->latest('submitted_at')
                ->first();
        }

        // Distribusi Status Burnout Global
        $statusCounts = Submission::select('burnout_status', DB::raw('count(*) as count'))
            ->groupBy('burnout_status')
            ->pluck('count', 'burnout_status')
            ->toArray();

        // Rata-rata 6 Pilar
        $averages = [
            'VT' => round(Submission::whereNotNull('score_vt')->avg('score_vt') ?? 4.10, 2),
            'KK' => round(Submission::whereNotNull('score_kk')->avg('score_kk') ?? 3.85, 2),
            'FI' => round(Submission::whereNotNull('score_fi')->avg('score_fi') ?? 4.25, 2),
            'BK' => round(Submission::whereNotNull('score_bk')->avg('score_bk') ?? 3.40, 2),
            'KM' => round(Submission::whereNotNull('score_km')->avg('score_km') ?? 3.65, 2),
            'KP' => round(Submission::whereNotNull('score_kp')->avg('score_kp') ?? 3.90, 2),
        ];

        return response()->json([
            'status' => 'success',
            'data'   => [
                'latest_submission' => $latestSubmission,
                'status_distribution' => [
                    'Budaya Kerja Positif'  => $statusCounts['Budaya Kerja Positif'] ?? 0,
                    'Kondisi Stabil'        => $statusCounts['Kondisi Stabil'] ?? 0,
                    'Waspada Gejala Burnout' => $statusCounts['Waspada Gejala Burnout'] ?? 0,
                    'Risiko Burnout Tinggi'  => $statusCounts['Risiko Burnout Tinggi'] ?? 0,
                ],
                'factor_averages' => $averages,
                'total_startups'  => User::distinct('startup_name')->count('startup_name'),
                'total_assessments' => Submission::count(),
            ],
        ]);
    }

    /**
     * Daftar Startup & Hasil Evaluasi
     */
    public function getStartupList(Request $request): JsonResponse
    {
        $limit = min((int)$request->input('limit', 15), 100);
        $offset = (int)$request->input('offset', 0);
        $search = $request->input('search', $request->input('q', ''));

        $query = Submission::with('user')
            ->whereHas('user', function ($q) use ($search) {
                if ($search) {
                    $q->where('startup_name', 'like', "%{$search}%")
                      ->orWhere('sektor_teknologi', 'like', "%{$search}%");
                }
            })
            ->latest('submitted_at');

        $total = $query->count();
        $submissions = $query->skip($offset)->take($limit)->get();

        $rows = $submissions->map(function ($s) {
            return [
                'submission_id'   => $s->id,
                'startup_name'    => $s->user->startup_name ?? '-',
                'lokasi'          => $s->user->lokasi ?? 'Indonesia',
                'sektor_teknologi'=> $s->user->sektor_teknologi ?? '-',
                'role'            => $s->role,
                'score_composite' => round($s->final_score / 5, 4),
                'score_on_5'      => $s->final_score,
                'health_category' => $s->burnout_status,
                'submitted_at'    => $s->submitted_at ? $s->submitted_at->toDateTimeString() : null,
                'pendanaan'       => $s->user->pendanaan ?? '-',
            ];
        });

        return response()->json([
            'status'  => 'success',
            'success' => true,
            'data'    => [
                'rows'   => $rows,
                'total'  => $total,
                'limit'  => $limit,
                'offset' => $offset,
            ],
        ]);
    }

    /**
     * Rata-rata 6 Pilar Budaya
     */
    public function getFactorAverages(): JsonResponse
    {
        $data = [
            'avg_vt' => round(Submission::whereNotNull('score_vt')->avg('score_vt') ?? 4.10, 2),
            'avg_kk' => round(Submission::whereNotNull('score_kk')->avg('score_kk') ?? 3.85, 2),
            'avg_fi' => round(Submission::whereNotNull('score_fi')->avg('score_fi') ?? 4.25, 2),
            'avg_bk' => round(Submission::whereNotNull('score_bk')->avg('score_bk') ?? 3.40, 2),
            'avg_km' => round(Submission::whereNotNull('score_km')->avg('score_km') ?? 3.65, 2),
            'avg_kp' => round(Submission::whereNotNull('score_kp')->avg('score_kp') ?? 3.90, 2),
        ];

        return response()->json([
            'status'  => 'success',
            'success' => true,
            'data'    => $data,
        ]);
    }

    /**
     * Unified Action Router untuk kompatibilitas penuh dengan Frontend Client
     */
    public function handleAction(Request $request)
    {
        $action = $request->query('action', $request->input('action', ''));
        if ($action === 'umkm_list') $action = 'startup_list';

        return match ($action) {
            'register'       => $this->register($request),
            'login'          => $this->login($request),
            'submit'         => $this->submitAssessment($request),
            'questions'      => $this->getQuestions($request),
            'dashboard'      => $this->getDashboard($request),
            'startup_list'   => $this->getStartupList($request),
            'factor_averages'=> $this->getFactorAverages(),
            default          => response()->json(['success' => false, 'message' => "Endpoint action '{$action}' tidak ditemukan."], 404),
        };
    }

    /**
     * Evaluasi Status Burnout Berdasarkan Skor 1.00 - 5.00
     */
    protected function evaluateBurnoutStatus(float $score): string
    {
        if ($score >= 4.20) {
            return 'Budaya Kerja Positif';
        }
        if ($score >= 3.50) {
            return 'Kondisi Stabil';
        }
        if ($score >= 2.80) {
            return 'Waspada Gejala Burnout';
        }
        return 'Risiko Burnout Tinggi';
    }
}