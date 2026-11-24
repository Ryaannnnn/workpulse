@extends('layouts.app')

@section('title', 'Dashboard – WorkPulse')

@push('styles')
<style>
    .db-shell {
        display: flex;
        min-height: 100vh;
        background-color: var(--bg-page);
    }

    /* Sidebar */
    .db-sidebar {
        width: 256px;
        flex-shrink: 0;
        background-color: var(--bg-page);
        border-right: 1px solid var(--border-hairline);
        display: flex;
        flex-direction: column;
        position: sticky;
        top: 0;
        height: 100vh;
    }

    .db-sidebar-brand {
        padding: 1.5rem 1.375rem;
        border-bottom: 1px solid var(--border-hairline);
    }

    .db-sidebar-tagline {
        font-size: 0.75rem;
        color: var(--ink-muted);
        margin-top: 0.375rem;
    }

    .db-nav {
        padding: 1rem 0.75rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .db-nav-link {
        display: flex;
        align-items: center;
        gap: 0.6875rem;
        padding: 0.625rem 0.875rem;
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--ink-secondary);
        text-decoration: none;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
        font-family: inherit;
        cursor: pointer;
        transition: background-color 150ms ease, color 150ms ease;
    }

    .db-nav-link:hover {
        background-color: var(--bg-subtle);
        color: var(--ink-primary);
    }

    .db-nav-link[aria-current="page"] {
        background-color: var(--bg-subtle);
        color: var(--ink-primary);
        font-weight: 700;
        box-shadow: inset 3px 0 0 var(--ink-primary);
    }

    .db-nav-link.db-logout {
        color: var(--status-tinggi-text);
    }

    .db-nav-link.db-logout:hover {
        background-color: var(--status-tinggi-bg);
    }

    .db-sidebar-footer {
        padding: 1rem 0.75rem;
        border-top: 1px solid var(--border-hairline);
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    /* Main Column */
    .db-main-col {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
    }

    .db-topbar {
        background-color: var(--bg-page);
        border-bottom: 1px solid var(--border-hairline);
        padding: 0.9375rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .db-topbar-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .db-menu-btn {
        display: none;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-control);
        background-color: var(--bg-surface);
        color: var(--ink-primary);
        cursor: pointer;
    }

    .db-topbar-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--ink-secondary);
    }

    .db-topbar-meta {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .db-role-pill {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--ink-primary);
        background-color: var(--bg-subtle);
        border: 1px solid var(--border-hairline);
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
    }

    .db-startup-name {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--ink-primary);
    }

    .db-content {
        width: 100%;
        max-width: 1160px;
        padding: 2.25rem 2rem 3.5rem;
    }

    /* Welcome Header */
    .db-welcome {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1.5rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .db-welcome-title {
        font-family: var(--font-serif);
        font-size: clamp(1.75rem, 2.8vw, 2.25rem);
        font-weight: 600;
        line-height: 1.15;
        letter-spacing: -0.02em;
        color: var(--ink-primary);
        margin-bottom: 0.375rem;
    }

    .db-welcome-sub {
        font-size: 0.9375rem;
        color: var(--ink-secondary);
    }

    /* 4 KPI Cards */
    .db-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.75rem;
    }

    .db-kpi-card {
        background-color: var(--bg-surface);
        border: 1px solid var(--border-hairline);
        border-radius: 14px;
        padding: 1.375rem 1.5rem;
        box-shadow: var(--shadow-editorial);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 0.625rem;
    }

    .db-kpi-label {
        font-size: 0.8125rem;
        font-weight: 500;
        color: var(--ink-secondary);
    }

    .db-kpi-value {
        font-size: 1.875rem;
        font-weight: 700;
        line-height: 1.15;
        color: var(--ink-primary);
    }

    .db-kpi-sub {
        font-size: 0.8125rem;
        color: var(--ink-muted);
    }

    /* Analytics 2-Column Grid */
    .db-analytics-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr);
        gap: 1.25rem;
        margin-bottom: 1.75rem;
    }

    .db-panel {
        background-color: var(--bg-surface);
        border: 1px solid var(--border-hairline);
        border-radius: var(--radius-xl);
        padding: 1.75rem;
        box-shadow: var(--shadow-editorial);
    }

    .db-panel-title {
        font-family: var(--font-serif);
        font-size: 1.25rem;
        font-weight: 600;
        line-height: 1.25;
        letter-spacing: -0.015em;
        color: var(--ink-primary);
        margin-bottom: 0.25rem;
    }

    .db-panel-sub {
        font-size: 0.875rem;
        color: var(--ink-secondary);
        margin-bottom: 1.5rem;
    }

    .db-pillars-list {
        display: flex;
        flex-direction: column;
        gap: 1.125rem;
    }

    .db-pillar-row-head {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.4375rem;
    }

    .db-pillar-name {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--ink-primary);
    }

    .db-pillar-score {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--ink-primary);
    }

    .db-progress-track {
        height: 8px;
        background-color: var(--bg-track);
        border-radius: 9999px;
        overflow: hidden;
    }

    .db-progress-fill {
        height: 100%;
        background-color: var(--ink-primary);
        border-radius: 9999px;
    }

    .db-chart-box {
        position: relative;
        min-height: 250px;
        flex: 1;
    }

    /* Responsive Table */
    .wp-table-wrapper {
        overflow: auto;
        max-inline-size: 100%;
        max-block-size: min(560px, 75vh);
        container-type: inline-size;
    }

    .wp-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        text-align: left;
        font-size: 0.875rem;
        --label-2: "Lokasi Hub";
        --label-3: "Sektor";
        --label-4: "Peran";
        --label-5: "Skor Budaya";
        --label-6: "Status Risiko";
    }

    .wp-table thead {
        position: sticky;
        inset-block-start: 0;
        z-index: 1;
    }

    .wp-table thead th {
        background-color: var(--bg-subtle);
        color: var(--ink-secondary);
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        padding: 0.875rem 1.25rem;
        border-top: 1px solid var(--border-hairline);
        border-bottom: 1px solid var(--border-hairline);
        white-space: nowrap;
    }

    .wp-table tbody th,
    .wp-table tbody td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border-hairline);
        vertical-align: middle;
        background-color: var(--bg-surface);
    }

    .wp-table tbody th {
        font-weight: 600;
        color: var(--ink-primary);
    }

    .wp-table tbody td {
        color: var(--ink-secondary);
    }

    .wp-table tbody tr:last-child th,
    .wp-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .db-role-chip {
        display: inline-flex;
        align-items: center;
        padding: 0.1875rem 0.625rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        background-color: var(--bg-subtle);
        color: var(--ink-primary);
        border: 1px solid var(--border-hairline);
    }

    @container (width < 640px) {
        .wp-table,
        .wp-table thead,
        .wp-table tbody,
        .wp-table tr,
        .wp-table th,
        .wp-table td {
            display: block;
        }

        .wp-table thead {
            display: none;
        }

        .wp-table tbody tr {
            border: 1px solid var(--border-hairline);
            border-radius: var(--radius-lg);
            margin-block-end: 0.875rem;
            overflow: hidden;
        }

        .wp-table tbody th {
            background-color: var(--bg-subtle);
            padding: 0.875rem 1rem;
            border-bottom: 1px solid var(--border-hairline);
        }

        .wp-table tbody td {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.6875rem 1rem;
            border-bottom: 1px solid var(--border-hairline);
        }

        .wp-table tbody td:nth-child(2)::before { content: var(--label-2); font-weight: 600; color: var(--ink-muted); font-size: 0.75rem; }
        .wp-table tbody td:nth-child(3)::before { content: var(--label-3); font-weight: 600; color: var(--ink-muted); font-size: 0.75rem; }
        .wp-table tbody td:nth-child(4)::before { content: var(--label-4); font-weight: 600; color: var(--ink-muted); font-size: 0.75rem; }
        .wp-table tbody td:nth-child(5)::before { content: var(--label-5); font-weight: 600; color: var(--ink-muted); font-size: 0.75rem; }
        .wp-table tbody td:nth-child(6)::before { content: var(--label-6); font-weight: 600; color: var(--ink-muted); font-size: 0.75rem; }
    }

    @media (max-width: 1080px) {
        .db-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .db-analytics-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 900px) {
        .db-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            z-index: 60;
            transform: translateX(-100%);
            transition: transform 150ms ease;
        }
        .db-sidebar.is-open {
            transform: translateX(0);
        }
        .db-menu-btn {
            display: inline-flex;
        }
        .db-content {
            padding: 1.5rem 1.25rem 2.5rem;
        }
        .db-topbar {
            padding: 0.875rem 1.25rem;
        }
    }

    @media (max-width: 560px) {
        .db-kpi-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
@php
    $statusBadgeClass = function (?string $status): string {
        return match ($status) {
            'Budaya Kerja Positif'   => 'wp-status-positif',
            'Kondisi Stabil'         => 'wp-status-stabil',
            'Waspada Gejala Burnout' => 'wp-status-waspada',
            'Risiko Burnout Tinggi'  => 'wp-status-tinggi',
            default                  => 'wp-status-stabil',
        };
    };

    $pillars = [
        ['label' => 'Visi & Transparansi (VT)', 'score' => $averages['VT'] ?? 0],
        ['label' => 'Kualitas Kepemimpinan (KK)', 'score' => $averages['KK'] ?? 0],
        ['label' => 'Fasilitas & Infrastruktur (FI)', 'score' => $averages['FI'] ?? 0],
        ['label' => 'Beban Kerja / Workload (BK)', 'score' => $averages['BK'] ?? 0],
        ['label' => 'Kesejahteraan Mental (KM)', 'score' => $averages['KM'] ?? 0],
        ['label' => 'Kompensasi & Karir (KP)', 'score' => $averages['KP'] ?? 0],
    ];

    $hasAssessments = ($totalAssessments ?? 0) > 0;
    $chartCounts = [
        $hasAssessments ? ($statusCounts['Budaya Kerja Positif'] ?? 0) : 1,
        $hasAssessments ? ($statusCounts['Kondisi Stabil'] ?? 0) : 1,
        $hasAssessments ? ($statusCounts['Waspada Gejala Burnout'] ?? 0) : 0,
        $hasAssessments ? ($statusCounts['Risiko Burnout Tinggi'] ?? 0) : 0,
    ];
@endphp

<div class="db-shell">
    <!-- Sidebar -->
    <aside id="db-sidebar" class="db-sidebar" aria-label="Navigasi dashboard">
        <div class="db-sidebar-brand">
            <a href="{{ route('dashboard') }}" class="wp-brand">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
                <span class="wp-brand-text" style="font-size: 1.25rem;">WorkPulse</span>
            </a>
            <div class="db-sidebar-tagline">Startup Culture &amp; Burnout Index</div>
        </div>

        <nav class="db-nav" aria-label="Menu utama dashboard">
            <a href="{{ route('dashboard') }}" class="db-nav-link" aria-current="page">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Ringkasan Ekosistem
            </a>
            <a href="{{ route('assessment') }}" class="db-nav-link">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="12" y1="18" x2="12" y2="12"/>
                    <line x1="9" y1="15" x2="15" y2="15"/>
                </svg>
                Form Asesmen Baru
            </a>
            <a href="#pilar-section" class="db-nav-link">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="18" y1="20" x2="18" y2="10"/>
                    <line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
                Profil 6 Pilar
            </a>
            <a href="#tabel-startup" class="db-nav-link">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <line x1="3" y1="9" x2="21" y2="9"/>
                    <line x1="9" y1="21" x2="9" y2="9"/>
                </svg>
                Daftar Startup
            </a>
        </nav>

        <div class="db-sidebar-footer">
            <a href="{{ route('home') }}" class="db-nav-link">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="2" y1="12" x2="22" y2="12"/>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                </svg>
                Halaman Beranda
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="db-nav-link db-logout">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Keluar Akun
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Column -->
    <div class="db-main-col">
        <header class="db-topbar">
            <div class="db-topbar-left">
                <button
                    type="button"
                    id="db-sidebar-toggle"
                    class="db-menu-btn"
                    aria-expanded="false"
                    aria-controls="db-sidebar"
                    aria-label="Buka menu navigasi"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <span class="db-topbar-title">Dashboard Ekosistem</span>
            </div>

            <div class="db-topbar-meta">
                <span class="db-role-pill">
                    {{ $user->role === 'clevel' ? 'Founder (C-Level)' : 'Karyawan (Staf)' }}
                </span>
                <span class="db-startup-name">{{ $user->startup_name ?? 'Startup Member' }}</span>
            </div>
        </header>

        <main class="db-content">
            @if (session('success'))
                <div class="wp-alert-success" role="status">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;" aria-hidden="true">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Welcome Bar -->
            <div class="db-welcome">
                <div>
                    <h1 class="db-welcome-title">Halo, {{ $user->name ?? $user->startup_name }}</h1>
                    <p class="db-welcome-sub">Ringkasan kesehatan budaya kerja dan pemantauan risiko burnout startup Anda.</p>
                </div>
                <a href="{{ route('assessment') }}" class="wp-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Isi Form Asesmen Baru
                </a>
            </div>

            <!-- 4 KPI Cards -->
            <section class="db-kpi-grid" aria-label="Ringkasan metrik utama">
                <div class="db-kpi-card">
                    <div class="db-kpi-label">Total Startup Terdata</div>
                    <div class="db-kpi-value tabular-nums">{{ $totalStartups }}</div>
                    <div class="db-kpi-sub">Entitas ekosistem aktif</div>
                </div>

                <div class="db-kpi-card">
                    <div class="db-kpi-label">Total Pengisian Asesmen</div>
                    <div class="db-kpi-value tabular-nums">{{ $totalAssessments }}</div>
                    <div class="db-kpi-sub">Evaluasi masuk tersimpan</div>
                </div>

                <div class="db-kpi-card">
                    <div class="db-kpi-label">Skor Terakhir Anda</div>
                    <div class="db-kpi-value tabular-nums">
                        {{ $latestSubmission ? number_format($latestSubmission->final_score, 2) . ' / 5.0' : 'Belum Ada' }}
                    </div>
                    <div>
                        @if ($latestSubmission)
                            <span class="wp-status-badge {{ $statusBadgeClass($latestSubmission->burnout_status) }}">
                                {{ $latestSubmission->burnout_status }}
                            </span>
                        @else
                            <span class="db-kpi-sub">Silakan isi asesmen</span>
                        @endif
                    </div>
                </div>

                <div class="db-kpi-card">
                    <div class="db-kpi-label">Proporsi Budaya Sehat</div>
                    <div class="db-kpi-value tabular-nums">{{ $healthyPercentage }}%</div>
                    <div class="db-kpi-sub">Skor komposit &ge; 3.50</div>
                </div>
            </section>

            <!-- 6 Pillars & Burnout Distribution -->
            <section id="pilar-section" class="db-analytics-grid" aria-label="Analitik enam pilar dan distribusi risiko">
                <div class="db-panel">
                    <h2 class="db-panel-title">Rata-rata 6 Pilar Budaya Organisasi</h2>
                    <p class="db-panel-sub">Skala skor 1.00 – 5.00 berdasarkan agregasi seluruh evaluasi ekosistem.</p>

                    <div class="db-pillars-list">
                        @foreach ($pillars as $pillar)
                            @php
                                $pct = max(0, min(100, ($pillar['score'] / 5) * 100));
                            @endphp
                            <div>
                                <div class="db-pillar-row-head">
                                    <span class="db-pillar-name">{{ $pillar['label'] }}</span>
                                    <span class="db-pillar-score tabular-nums">{{ number_format($pillar['score'], 2) }} / 5.0</span>
                                </div>
                                <div class="db-progress-track">
                                    <div class="db-progress-fill" style="width: {{ $pct }}%;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="db-panel" style="display: flex; flex-direction: column;">
                    <h2 class="db-panel-title">Distribusi Risiko Burnout</h2>
                    <p class="db-panel-sub">Proporsi empat kategori status klinis ekosistem.</p>
                    <div class="db-chart-box">
                        <canvas id="burnoutPieChart" aria-label="Grafik distribusi risiko burnout"></canvas>
                    </div>
                </div>
            </section>

            <!-- Startup Evaluation Table -->
            <section id="tabel-startup" class="db-panel" style="padding: 0; overflow: hidden;">
                <div style="padding: 1.5rem 1.75rem 1.25rem;">
                    <h2 id="startup-table-heading" class="db-panel-title">Daftar Startup &amp; Evaluasi Terkini</h2>
                    <p class="db-panel-sub" style="margin-bottom: 0;">Rekapitulasi benchmark budaya kerja dan klasifikasi status burnout terbaru.</p>
                </div>

                <div tabindex="0" role="region" aria-labelledby="startup-table-heading" class="wp-table-wrapper">
                    <table class="wp-table">
                        <thead>
                            <tr>
                                <th scope="col">Nama Startup</th>
                                <th scope="col">Lokasi Hub</th>
                                <th scope="col">Sektor</th>
                                <th scope="col">Peran</th>
                                <th scope="col">Skor Budaya</th>
                                <th scope="col">Status Risiko</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentStartups as $st)
                                <tr>
                                    <th scope="row">{{ $st['startup_name'] }}</th>
                                    <td>{{ $st['lokasi'] }}</td>
                                    <td>{{ $st['sektor_teknologi'] }}</td>
                                    <td>
                                        <span class="db-role-chip">
                                            {{ $st['role'] === 'clevel' ? 'C-Level' : 'Staf' }}
                                        </span>
                                    </td>
                                    <td class="tabular-nums" style="font-weight: 600; color: var(--ink-primary);">
                                        {{ number_format($st['score_on_5'], 2) }}
                                    </td>
                                    <td>
                                        <span class="wp-status-badge {{ $statusBadgeClass($st['health_category'] ?? null) }}">
                                            {{ $st['health_category'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 2rem 1.25rem; color: var(--ink-muted);">
                                        Belum ada data evaluasi startup. Silakan isi form asesmen pertama Anda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('db-sidebar');
    const toggleBtn = document.getElementById('db-sidebar-toggle');
    if (sidebar && toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            const isOpen = sidebar.classList.toggle('is-open');
            toggleBtn.setAttribute('aria-expanded', String(isOpen));
        });

        document.addEventListener('click', function (e) {
            if (window.innerWidth <= 900 && sidebar.classList.contains('is-open')) {
                if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                    sidebar.classList.remove('is-open');
                    toggleBtn.setAttribute('aria-expanded', 'false');
                }
            }
        });
    }

    const canvas = document.getElementById('burnoutPieChart');
    const ctx = canvas ? canvas.getContext('2d') : null;
    if (ctx && typeof Chart !== 'undefined') {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Budaya Positif', 'Kondisi Stabil', 'Waspada Burnout', 'Risiko Tinggi'],
                datasets: [{
                    data: @json($chartCounts),
                    backgroundColor: ['#1F5E4B', '#4A7C9B', '#B8863B', '#A64444'],
                    borderWidth: 2,
                    borderColor: '#FFFFFF',
                    hoverOffset: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '66%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#5C5855',
                            boxWidth: 10,
                            boxHeight: 10,
                            padding: 16,
                            font: {
                                family: "'Plus Jakarta Sans', system-ui, sans-serif",
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1A1817',
                        titleColor: '#FFFFFF',
                        bodyColor: '#FAF8F5',
                        padding: 10,
                        cornerRadius: 8
                    }
                }
            }
        });
    }
});
</script>
@endpush
