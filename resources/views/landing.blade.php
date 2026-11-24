@extends('layouts.app')

@section('title', 'WorkPulse – Platform Asesmen Budaya Kerja & Risiko Burnout Startup')

@push('styles')
<style>
    .lp-header {
        background-color: var(--bg-page);
        border-bottom: 1px solid var(--border-hairline);
    }

    .lp-header-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        min-height: 76px;
    }

    .lp-nav {
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    .lp-nav a {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--ink-secondary);
        text-decoration: none;
        transition: color 150ms ease;
    }

    .lp-nav a:hover {
        color: var(--ink-primary);
    }

    .lp-actions {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .lp-text-link {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--ink-primary);
        text-decoration: none;
        transition: color 150ms ease;
    }

    .lp-text-link:hover {
        color: var(--ink-secondary);
    }

    /* Hero */
    .lp-hero {
        padding-block: 5.5rem 6.5rem;
    }

    .lp-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.15fr) minmax(0, 0.85fr);
        gap: 4rem;
        align-items: center;
    }

    .lp-hero-title {
        font-family: var(--font-serif);
        font-size: clamp(2.5rem, 4.4vw, 3.625rem);
        font-weight: 600;
        line-height: 1.1;
        letter-spacing: -0.025em;
        color: var(--ink-primary);
        margin-bottom: 1.5rem;
    }

    .lp-hero-desc {
        font-size: 1.0625rem;
        line-height: 1.65;
        color: var(--ink-secondary);
        max-width: 34rem;
        margin-bottom: 2rem;
    }

    .lp-hero-cta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 3rem;
    }

    .lp-metrics-strip {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1.5rem;
        padding-top: 1.75rem;
        border-top: 1px solid var(--border-hairline);
    }

    .lp-metric-val {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1.2;
        color: var(--ink-primary);
    }

    .lp-metric-lbl {
        font-size: 0.8125rem;
        color: var(--ink-secondary);
        margin-top: 0.25rem;
    }

    /* Hero Preview Frame */
    .lp-preview-frame {
        background-color: var(--bg-subtle);
        border: 1px solid var(--border-hairline);
        border-radius: var(--radius-2xl);
        padding: 1.5rem;
    }

    .lp-preview-card {
        background-color: var(--bg-surface);
        border: 1px solid var(--border-hairline);
        border-radius: 14px;
        padding: 1.75rem;
        box-shadow: var(--shadow-editorial);
    }

    .lp-preview-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border-hairline);
        margin-bottom: 1.25rem;
    }

    .lp-preview-label {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--ink-secondary);
    }

    .lp-live-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.1875rem 0.625rem;
        border-radius: 9999px;
        background-color: var(--accent-forest-soft);
        color: var(--accent-forest);
        font-size: 0.75rem;
        font-weight: 600;
    }

    .lp-live-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: var(--accent-forest);
    }

    .lp-preview-score-row {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .lp-preview-score {
        font-size: 2.375rem;
        font-weight: 700;
        line-height: 1;
        color: var(--ink-primary);
    }

    .lp-preview-score span {
        font-size: 1rem;
        font-weight: 500;
        color: var(--ink-muted);
    }

    .lp-preview-bars {
        display: flex;
        flex-direction: column;
        gap: 0.9375rem;
    }

    .lp-bar-meta {
        display: flex;
        justify-content: space-between;
        font-size: 0.8125rem;
        font-weight: 500;
        color: var(--ink-primary);
        margin-bottom: 0.375rem;
    }

    .lp-bar-track {
        height: 6px;
        background-color: var(--bg-track);
        border-radius: 9999px;
        overflow: hidden;
    }

    .lp-bar-fill {
        height: 100%;
        background-color: var(--ink-primary);
        border-radius: 9999px;
    }

    .lp-preview-foot {
        margin-top: 1.375rem;
        padding-top: 0.875rem;
        border-top: 1px solid var(--border-hairline);
        font-size: 0.75rem;
        color: var(--ink-muted);
    }

    /* Editorial Sections */
    .lp-section {
        padding-block: 6rem;
        border-top: 1px solid var(--border-hairline);
    }

    .lp-section-surface {
        background-color: var(--bg-surface);
    }

    .lp-section-head {
        max-width: 40rem;
        margin-bottom: 3.25rem;
    }

    .lp-section-title {
        font-family: var(--font-serif);
        font-size: clamp(1.875rem, 3vw, 2.375rem);
        font-weight: 600;
        line-height: 1.18;
        letter-spacing: -0.02em;
        color: var(--ink-primary);
        margin-bottom: 0.875rem;
    }

    .lp-section-sub {
        font-size: 1rem;
        color: var(--ink-secondary);
        line-height: 1.6;
    }

    /* 3-Column Editorial Feature Grid */
    .lp-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 2.25rem;
    }

    .lp-feature-item {
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-control);
    }

    .lp-num {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--ink-muted);
        margin-bottom: 0.875rem;
    }

    .lp-feature-title {
        font-size: 1.0625rem;
        font-weight: 600;
        color: var(--ink-primary);
        margin-bottom: 0.5rem;
    }

    .lp-feature-desc {
        font-size: 0.9375rem;
        color: var(--ink-secondary);
        line-height: 1.6;
    }

    /* 6 Pillars Grid */
    .lp-pillars-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1.5rem;
    }

    .lp-pillar-card {
        background-color: var(--bg-page);
        border: 1px solid var(--border-hairline);
        border-radius: var(--radius-xl);
        padding: 1.75rem;
    }

    .lp-pillar-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 1.125rem;
    }

    .lp-pillar-code {
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.06em;
        color: var(--ink-muted);
    }

    .lp-role-tag {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--ink-secondary);
        background-color: var(--bg-surface);
        border: 1px solid var(--border-hairline);
        padding: 0.1875rem 0.5625rem;
        border-radius: 9999px;
    }

    /* 4 Burnout Categories Grid */
    .lp-grid-4 {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1.25rem;
    }

    .lp-tier-card {
        background-color: var(--bg-surface);
        border: 1px solid var(--border-hairline);
        border-radius: var(--radius-xl);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .lp-tier-range {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--ink-primary);
        margin-top: 0.25rem;
    }

    /* Bottom CTA */
    .lp-cta-box {
        background-color: var(--bg-surface);
        border: 1px solid var(--border-hairline);
        border-radius: var(--radius-2xl);
        padding: 3rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 2rem;
    }

    /* Footer */
    .lp-footer {
        border-top: 1px solid var(--border-hairline);
        padding-block: 2.25rem;
    }

    .lp-footer-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        font-size: 0.8125rem;
        color: var(--ink-muted);
    }

    .lp-footer-links {
        display: flex;
        gap: 1.5rem;
    }

    .lp-footer-links a {
        text-decoration: none;
        transition: color 150ms ease;
    }

    .lp-footer-links a:hover {
        color: var(--ink-primary);
    }

    @media (max-width: 960px) {
        .lp-nav {
            display: none;
        }
        .lp-hero {
            padding-block: 3.5rem 4.5rem;
        }
        .lp-hero-grid {
            grid-template-columns: 1fr;
            gap: 2.75rem;
        }
        .lp-grid-3,
        .lp-pillars-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .lp-grid-4 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .lp-cta-box {
            flex-direction: column;
            align-items: flex-start;
            padding: 2rem;
        }
    }

    @media (max-width: 600px) {
        .lp-section {
            padding-block: 4rem;
        }
        .lp-metrics-strip,
        .lp-grid-3,
        .lp-pillars-grid,
        .lp-grid-4 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div id="landing-page">
    <!-- Header -->
    <header class="lp-header">
        <div class="wp-container lp-header-inner">
            <a href="{{ route('home') }}" class="wp-brand">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
                <span class="wp-brand-text">WorkPulse</span>
            </a>

            <nav class="lp-nav" aria-label="Navigasi utama">
                <a href="#fitur">Fitur Asesmen</a>
                <a href="#pilar">6 Pilar Budaya</a>
                <a href="#metodologi">Metodologi</a>
            </nav>

            <div class="lp-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="wp-btn-primary wp-btn-sm">Buka Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="lp-text-link">Masuk</a>
                    <a href="{{ route('register') }}" class="wp-btn-primary wp-btn-sm">Mulai Asesmen</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        <!-- Asymmetric Hero -->
        <section class="lp-hero">
            <div class="wp-container lp-hero-grid">
                <div>
                    <h1 class="lp-hero-title">
                        Identifikasi risiko burnout sebelum talenta terbaik Anda pergi.
                    </h1>
                    <p class="lp-hero-desc">
                        WorkPulse membantu Founder (C-Level) dan karyawan teknis startup memetakan kesehatan budaya kerja, beban sprint, serta ketimpangan kompensasi melalui 36 indikator klinis yang objektif.
                    </p>
                    <div class="lp-hero-cta">
                        <a href="{{ route('register') }}" class="wp-btn-primary">Mulai Asesmen</a>
                        <a href="{{ route('login') }}" class="wp-btn-secondary">Masuk ke Akun</a>
                    </div>

                    <div class="lp-metrics-strip">
                        <div>
                            <div class="lp-metric-val tabular-nums">120+</div>
                            <div class="lp-metric-lbl">Startup ekosistem</div>
                        </div>
                        <div>
                            <div class="lp-metric-val tabular-nums">36</div>
                            <div class="lp-metric-lbl">Indikator klinis</div>
                        </div>
                        <div>
                            <div class="lp-metric-val tabular-nums">6 Pilar</div>
                            <div class="lp-metric-lbl">Evaluasi dua peran</div>
                        </div>
                    </div>
                </div>

                <!-- Right Editorial UI Preview -->
                <div class="lp-preview-frame" aria-label="Pratinjau skor WorkPulse">
                    <div class="lp-preview-card">
                        <div class="lp-preview-head">
                            <span class="lp-preview-label">Indeks Budaya Organisasi</span>
                            <span class="lp-live-pill">
                                <span class="lp-live-dot" aria-hidden="true"></span>
                                Live
                            </span>
                        </div>

                        <div class="lp-preview-score-row">
                            <div class="lp-preview-score tabular-nums">4.18 <span>/ 5.00</span></div>
                            <span class="wp-status-badge wp-status-stabil">Kondisi Stabil</span>
                        </div>

                        <div class="lp-preview-bars">
                            <div>
                                <div class="lp-bar-meta">
                                    <span>Visi &amp; Transparansi</span>
                                    <span class="tabular-nums">4.30</span>
                                </div>
                                <div class="lp-bar-track"><div class="lp-bar-fill" style="width: 86%"></div></div>
                            </div>
                            <div>
                                <div class="lp-bar-meta">
                                    <span>Kualitas Kepemimpinan</span>
                                    <span class="tabular-nums">4.10</span>
                                </div>
                                <div class="lp-bar-track"><div class="lp-bar-fill" style="width: 82%"></div></div>
                            </div>
                            <div>
                                <div class="lp-bar-meta">
                                    <span>Beban Kerja (Workload)</span>
                                    <span class="tabular-nums">3.42</span>
                                </div>
                                <div class="lp-bar-track"><div class="lp-bar-fill" style="width: 68.4%"></div></div>
                            </div>
                            <div>
                                <div class="lp-bar-meta">
                                    <span>Kesejahteraan Mental</span>
                                    <span class="tabular-nums">3.95</span>
                                </div>
                                <div class="lp-bar-track"><div class="lp-bar-fill" style="width: 79%"></div></div>
                            </div>
                        </div>

                        <div class="lp-preview-foot">
                            Agregasi 2 sudut pandang: C-Level &amp; Staf Teknis
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Kapabilitas / Fitur -->
        <section id="fitur" class="lp-section">
            <div class="wp-container">
                <div class="lp-section-head">
                    <div class="wp-kicker">Kapabilitas Platform</div>
                    <h2 class="lp-section-title">Diagnostik budaya kerja yang terukur untuk tim teknologi</h2>
                    <p class="lp-section-sub">Dirancang khusus mengikuti ritme kerja startup dari tahap Pre-Seed hingga Series A ke atas.</p>
                </div>

                <div class="lp-grid-3">
                    <div class="lp-feature-item">
                        <div class="lp-num tabular-nums">01</div>
                        <h3 class="lp-feature-title">36 Indikator Klinis Skala Likert</h3>
                        <p class="lp-feature-desc">Kuesioner terstandarisasi skala 1–5 yang mengukur kelelahan emosional, batas jam sprint, serta keterbukaan runway secara presisi.</p>
                    </div>
                    <div class="lp-feature-item">
                        <div class="lp-num tabular-nums">02</div>
                        <h3 class="lp-feature-title">Evaluasi Dua Sudut Pandang</h3>
                        <p class="lp-feature-desc">Memisahkan instrumen penilaian C-Level dan Staf agar manajemen dapat mendeteksi kesenjangan persepsi di lapangan.</p>
                    </div>
                    <div class="lp-feature-item">
                        <div class="lp-num tabular-nums">03</div>
                        <h3 class="lp-feature-title">Deteksi Dini Risiko Burnout</h3>
                        <p class="lp-feature-desc">Menghitung indeks komposit otomatis dan memicu alur notifikasi sebelum kelelahan sistemik berdampak pada retensi tim.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6 Pilar Budaya -->
        <section id="pilar" class="lp-section lp-section-surface">
            <div class="wp-container">
                <div class="lp-section-head">
                    <div class="wp-kicker">Framework Diagnostik</div>
                    <h2 class="lp-section-title">Enam pilar kesehatan organisasi</h2>
                    <p class="lp-section-sub">Tiga pilar strategis dievaluasi oleh jajaran C-Level dan tiga pilar operasional dievaluasi oleh staf teknis.</p>
                </div>

                <div class="lp-pillars-grid">
                    <div class="lp-pillar-card">
                        <div class="lp-pillar-top">
                            <span class="lp-pillar-code tabular-nums">01 · VT</span>
                            <span class="lp-role-tag">C-Level</span>
                        </div>
                        <h3 class="lp-feature-title">Visi &amp; Transparansi</h3>
                        <p class="lp-feature-desc">Kejelasan arah runway finansial, keterbukaan keputusan pivot strategi, dan keselarasan OKR lintas tim.</p>
                    </div>

                    <div class="lp-pillar-card">
                        <div class="lp-pillar-top">
                            <span class="lp-pillar-code tabular-nums">02 · KK</span>
                            <span class="lp-role-tag">Staf</span>
                        </div>
                        <h3 class="lp-feature-title">Kualitas Kepemimpinan</h3>
                        <p class="lp-feature-desc">Empati manajerial, keadilan delegasi tugas, umpan balik rutin, dan ketiadaan micromanagement.</p>
                    </div>

                    <div class="lp-pillar-card">
                        <div class="lp-pillar-top">
                            <span class="lp-pillar-code tabular-nums">03 · FI</span>
                            <span class="lp-role-tag">C-Level</span>
                        </div>
                        <h3 class="lp-feature-title">Fasilitas &amp; Infrastruktur</h3>
                        <p class="lp-feature-desc">Kelayakan perangkat kerja, stabilitas pipeline CI/CD, lisensi tooling modern, serta dukungan kerja hybrid.</p>
                    </div>

                    <div class="lp-pillar-card">
                        <div class="lp-pillar-top">
                            <span class="lp-pillar-code tabular-nums">04 · BK</span>
                            <span class="lp-role-tag">Staf</span>
                        </div>
                        <h3 class="lp-feature-title">Beban Kerja (Workload)</h3>
                        <p class="lp-feature-desc">Realisme tenggat sprint, frekuensi lembur di luar jam kerja, dan ruang pemulihan setelah rilis fitur.</p>
                    </div>

                    <div class="lp-pillar-card">
                        <div class="lp-pillar-top">
                            <span class="lp-pillar-code tabular-nums">05 · KM</span>
                            <span class="lp-role-tag">Staf</span>
                        </div>
                        <h3 class="lp-feature-title">Kesejahteraan Mental</h3>
                        <p class="lp-feature-desc">Keamanan psikologis dalam bersuara, lingkungan bebas intimidasi, serta pemantauan kelelahan emosional.</p>
                    </div>

                    <div class="lp-pillar-card">
                        <div class="lp-pillar-top">
                            <span class="lp-pillar-code tabular-nums">06 · KP</span>
                            <span class="lp-role-tag">C-Level</span>
                        </div>
                        <h3 class="lp-feature-title">Kompensasi &amp; Karir</h3>
                        <p class="lp-feature-desc">Daya saing remunerasi dan ESOP terhadap pasar teknologi serta kejelasan jalur karir spesialis maupun manajerial.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Metodologi & Klasifikasi -->
        <section id="metodologi" class="lp-section">
            <div class="wp-container">
                <div class="lp-section-head">
                    <div class="wp-kicker">Standar Pengukuran</div>
                    <h2 class="lp-section-title">Klasifikasi indeks risiko burnout</h2>
                    <p class="lp-section-sub">Setiap pengisian asesmen menghasilkan skor komposit pada rentang 1.00 hingga 5.00 yang dipetakan ke empat status klinis.</p>
                </div>

                <div class="lp-grid-4">
                    <div class="lp-tier-card">
                        <div><span class="wp-status-badge wp-status-positif">Budaya Kerja Positif</span></div>
                        <div class="lp-tier-range tabular-nums">4.20 – 5.00</div>
                        <p class="lp-feature-desc">Organisasi sehat dan suportif, beban terdistribusi merata, dan keamanan psikologis tinggi.</p>
                    </div>
                    <div class="lp-tier-card">
                        <div><span class="wp-status-badge wp-status-stabil">Kondisi Stabil</span></div>
                        <div class="lp-tier-range tabular-nums">3.50 – 4.19</div>
                        <p class="lp-feature-desc">Ritme sprint terkendali, memerlukan pemantauan berkala saat siklus rilis produk meningkat.</p>
                    </div>
                    <div class="lp-tier-card">
                        <div><span class="wp-status-badge wp-status-waspada">Waspada Gejala Burnout</span></div>
                        <div class="lp-tier-range tabular-nums">2.80 – 3.49</div>
                        <p class="lp-feature-desc">Terdeteksi gejala kelelahan sistemik awal, jam lembur meningkat, atau friksi komunikasi tim.</p>
                    </div>
                    <div class="lp-tier-card">
                        <div><span class="wp-status-badge wp-status-tinggi">Risiko Burnout Tinggi</span></div>
                        <div class="lp-tier-range tabular-nums">1.00 – 2.79</div>
                        <p class="lp-feature-desc">Kelelahan mental akut dengan risiko turnover tinggi; membutuhkan intervensi manajemen segera.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bottom CTA -->
        <section class="lp-section" style="padding-top: 0; border-top: 0;">
            <div class="wp-container">
                <div class="lp-cta-box">
                    <div style="max-width: 36rem;">
                        <h2 class="lp-section-title" style="margin-bottom: 0.625rem;">Bangun ritme kerja startup yang berkelanjutan.</h2>
                        <p class="lp-section-sub">Daftarkan tim Anda untuk mulai memantau enam pilar kesehatan organisasi secara berkala.</p>
                    </div>
                    <a href="{{ route('register') }}" class="wp-btn-primary">Daftarkan Startup Anda</a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="lp-footer">
        <div class="wp-container lp-footer-inner">
            <span class="font-serif" style="font-weight: 700; font-size: 1rem; color: var(--ink-primary);">WorkPulse</span>
            <span>&copy; {{ date('Y') }} WorkPulse Clinical Analytics.</span>
            <div class="lp-footer-links">
                <a href="#fitur">Fitur</a>
                <a href="#pilar">6 Pilar</a>
                <a href="#metodologi">Metodologi</a>
            </div>
        </div>
    </footer>
</div>
@endsection
