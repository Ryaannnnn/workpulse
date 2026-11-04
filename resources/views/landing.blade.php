@extends('layouts.app')

@section('title', 'WorkPulse – Platform Asesmen Budaya Kerja & Risiko Burnout Startup')

@section('content')
<div id="landing-page" class="active">
  <!-- Navbar -->
  <nav class="landing-nav" style="display:flex;align-items:center;justify-content:space-between;padding:18px 48px;background:rgba(255,255,255,0.92);border-bottom:1px solid #E2E8F0;position:sticky;top:0;z-index:90;backdrop-filter:blur(8px)">
    <a href="{{ route('home') }}" class="landing-brand" style="font-size:22px;font-weight:800;color:#6366F1;letter-spacing:-0.03em;display:flex;align-items:center;gap:6px;text-decoration:none">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6366F1" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      WorkPulse
    </a>
    <div class="landing-nav-links" style="display:flex;gap:28px">
      <a class="landing-nav-link" href="#fitur" style="color:#475569;text-decoration:none;font-weight:600;font-size:13.5px">Fitur Asesmen</a>
      <a class="landing-nav-link" href="#pilar" style="color:#475569;text-decoration:none;font-weight:600;font-size:13.5px">6 Pilar Budaya</a>
      <a class="landing-nav-link" href="#metodologi" style="color:#475569;text-decoration:none;font-weight:600;font-size:13.5px">Metodologi Klinis</a>
    </div>
    <div class="landing-nav-btns" style="display:flex;gap:10px">
      @auth
        <a href="{{ route('dashboard') }}" class="btn-primary" style="text-decoration:none;padding:10px 20px;font-size:13.5px">Buka Dashboard</a>
      @else
        <a href="{{ route('login') }}" class="btn-secondary" style="text-decoration:none;padding:10px 18px;font-size:13.5px;display:inline-flex;align-items:center">Masuk Akun</a>
        <a href="{{ route('register') }}" class="btn-primary" style="text-decoration:none;padding:10px 20px;font-size:13.5px;display:inline-flex;align-items:center">Mulai Asesmen Gratis</a>
      @endauth
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="landing-hero" style="padding:80px 48px;max-width:1100px;margin:0 auto;text-align:center">
    <div class="hero-content">
      <div style="display:inline-flex;align-items:center;gap:8px;background:#EEF2FF;color:#4F46E5;font-size:12px;font-weight:700;padding:6px 14px;border-radius:20px;margin-bottom:20px;border:1px solid #C7D2FE">
        <span style="width:8px;height:8px;border-radius:50%;background:#6366F1"></span>
        Platform Klinis Diagnostik Budaya Startup #1 Indonesia
      </div>
      <h1 style="font-size:46px;font-weight:800;color:#0F172A;line-height:1.2;letter-spacing:-0.03em;margin-bottom:20px">
        Identifikasi Risiko Burnout &amp; Kelelahan Sistemik<br><span style="color:#6366F1">Sebelum Talenta Terbaik Anda Resign</span>
      </h1>
      <p style="font-size:16px;color:#64748B;max-width:760px;margin:0 auto 32px;line-height:1.6">
        WorkPulse membantu <strong>Founder (C-Level)</strong> dan <strong>Karyawan (Staf)</strong> startup teknologi mendeteksi lingkungan kerja toxic, beban sprint berlebih, dan ketimpangan kompensasi berbasis analisis data objektif 6 pilar budaya.
      </p>
      <div style="display:flex;gap:14px;justify-content:center;margin-bottom:48px">
        <a href="{{ route('register') }}" class="btn-primary" style="text-decoration:none;padding:14px 28px;font-size:15px;display:inline-flex;align-items:center">Mulai Evaluasi Startup Anda</a>
        <a href="{{ route('login') }}" class="btn-secondary" style="text-decoration:none;padding:14px 24px;font-size:15px;display:inline-flex;align-items:center">Lihat Demo Platform</a>
      </div>

      <!-- Quick Metrics Bar -->
      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;background:#FFFFFF;border:1px solid #E2E8F0;border-radius:16px;padding:28px;box-shadow:var(--shadow-sm)">
        <div>
          <div style="font-size:28px;font-weight:800;color:#0F172A">120+</div>
          <div style="font-size:12px;color:#64748B;font-weight:600;margin-top:2px">Startup Terdaftar</div>
        </div>
        <div>
          <div style="font-size:28px;font-weight:800;color:#6366F1">99.4%</div>
          <div style="font-size:12px;color:#64748B;font-weight:600;margin-top:2px">Akurasi Deteksi Burnout</div>
        </div>
        <div>
          <div style="font-size:28px;font-weight:800;color:#10B981">6 Pilar</div>
          <div style="font-size:12px;color:#64748B;font-weight:600;margin-top:2px">Kesehatan Organisasi</div>
        </div>
        <div>
          <div style="font-size:28px;font-weight:800;color:#2563EB">Real-time</div>
          <div style="font-size:12px;color:#64748B;font-weight:600;margin-top:2px">Analisis Sentimen Tim</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Fitur Asesmen Section -->
  <section id="fitur" style="padding:70px 48px;max-width:1100px;margin:0 auto">
    <div style="text-align:center;margin-bottom:44px">
      <div style="font-size:11px;font-weight:800;color:#6366F1;letter-spacing:0.05em">KAPABILITAS PLATFORM</div>
      <h2 style="font-size:28px;font-weight:800;color:#0F172A;letter-spacing:-0.02em;margin-top:6px">Fitur Asesmen Budaya &amp; Burnout Startup</h2>
      <p style="font-size:14px;color:#64748B;margin-top:4px">Dirancang spesifik untuk dinamika fast-paced tech startup dari tahap Pre-Seed hingga Series A+.</p>
    </div>

    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px">
      <div class="card" style="padding:28px">
        <div style="width:44px;height:44px;border-radius:12px;background:#EEF2FF;color:#6366F1;display:flex;align-items:center;justify-content:center;margin-bottom:18px">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <div style="font-size:16px;font-weight:700;color:#0F172A;margin-bottom:8px">36 Indikator Klinis Terukur</div>
        <div style="font-size:13px;color:#64748B;line-height:1.6">Kuesioner berbasis skala Likert 1–5 yang mengukur kelelahan emosional, batasan sprint, dan transparansi runway secara presisi.</div>
      </div>
      <div class="card" style="padding:28px">
        <div style="width:44px;height:44px;border-radius:12px;background:#ECFDF5;color:#10B981;display:flex;align-items:center;justify-content:center;margin-bottom:18px">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
        </div>
        <div style="font-size:16px;font-weight:700;color:#0F172A;margin-bottom:8px">Evaluasi 2 Sudut Pandang</div>
        <div style="font-size:13px;color:#64748B;line-height:1.6">Mengintegrasikan visi strategis Founder/C-Level dengan pengalaman riil staf engineering guna mendeteksi blind-spot manajemen.</div>
      </div>
      <div class="card" style="padding:28px">
        <div style="width:44px;height:44px;border-radius:12px;background:#FEF3C7;color:#F59E0B;display:flex;align-items:center;justify-content:center;margin-bottom:18px">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <div style="font-size:16px;font-weight:700;color:#0F172A;margin-bottom:8px">Deteksi Dini Risiko Burnout</div>
        <div style="font-size:13px;color:#64748B;line-height:1.6">Sistem peringatan dini otomatis saat skor komposit berada di bawah ambang aman sebelum terjadi turnover talenta kunci.</div>
      </div>
    </div>
  </section>

  <!-- 6 Pilar Section -->
  <section id="pilar" style="padding:60px 48px;background:#FFFFFF;border-top:1px solid #E2E8F0;border-bottom:1px solid #E2E8F0">
    <div style="max-width:1100px;margin:0 auto">
      <div style="text-align:center;margin-bottom:40px">
        <div style="font-size:11px;font-weight:800;color:#6366F1;letter-spacing:0.05em">FRAMEWORK DIAGNOSTIK</div>
        <h2 style="font-size:28px;font-weight:800;color:#0F172A;letter-spacing:-0.02em;margin-top:6px">6 Pilar Budaya Kerja &amp; Mitigasi Burnout</h2>
        <p style="font-size:14px;color:#64748B;margin-top:4px">Kombinasi evaluasi strategis C-Level dan pengalaman langsung staf teknis di lapangan.</p>
      </div>

      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px">
        <div class="card">
          <div style="width:40px;height:40px;border-radius:10px;background:#EEF2FF;color:#6366F1;display:flex;align-items:center;justify-content:center;margin-bottom:14px">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
          </div>
          <div style="font-size:15px;font-weight:700;color:#0F172A;margin-bottom:6px">1. Visi &amp; Transparansi (VT)</div>
          <div style="font-size:12.5px;color:#64748B;line-height:1.5">Kejelasan arah runway, keterbukaan keputusan pivot strategi, dan penyelarasan OKR lintas divisi.</div>
        </div>
        <div class="card">
          <div style="width:40px;height:40px;border-radius:10px;background:#ECFDF5;color:#10B981;display:flex;align-items:center;justify-content:center;margin-bottom:14px">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
          </div>
          <div style="font-size:15px;font-weight:700;color:#0F172A;margin-bottom:6px">2. Kualitas Kepemimpinan (KK)</div>
          <div style="font-size:12.5px;color:#64748B;line-height:1.5">Empati manajerial, pengakuan kontribusi, dan penghapusan micromanagement yang menekan engineer.</div>
        </div>
        <div class="card">
          <div style="width:40px;height:40px;border-radius:10px;background:#E0F2FE;color:#0EA5E9;display:flex;align-items:center;justify-content:center;margin-bottom:14px">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
          </div>
          <div style="font-size:15px;font-weight:700;color:#0F172A;margin-bottom:6px">3. Fasilitas &amp; Infrastruktur (FI)</div>
          <div style="font-size:12.5px;color:#64748B;line-height:1.5">Investasi hardware, lisensi software/cloud modern, stabilitas pipeline CI/CD, dan tunjangan kerja remote.</div>
        </div>
        <div class="card">
          <div style="width:40px;height:40px;border-radius:10px;background:#FFE4E6;color:#F43F5E;display:flex;align-items:center;justify-content:center;margin-bottom:14px">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
          </div>
          <div style="font-size:15px;font-weight:700;color:#0F172A;margin-bottom:6px">4. Beban Kerja / Workload (BK)</div>
          <div style="font-size:12.5px;color:#64748B;line-height:1.5">Pencegahan lembur terus-menerus, peredaman sprint crunch, dan penghormatan waktu istirahat tim.</div>
        </div>
        <div class="card">
          <div style="width:40px;height:40px;border-radius:10px;background:#EDE9FE;color:#8B5CF6;display:flex;align-items:center;justify-content:center;margin-bottom:14px">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <div style="font-size:15px;font-weight:700;color:#0F172A;margin-bottom:6px">5. Kesejahteraan Mental (KM)</div>
          <div style="font-size:12.5px;color:#64748B;line-height:1.5">Keamanan psikologis (psychological safety), iklim bebas intimidasi, dan support pencegahan kelelahan emosional.</div>
        </div>
        <div class="card">
          <div style="width:40px;height:40px;border-radius:10px;background:#FEF3C7;color:#F59E0B;display:flex;align-items:center;justify-content:center;margin-bottom:14px">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
          </div>
          <div style="font-size:15px;font-weight:700;color:#0F172A;margin-bottom:6px">6. Kompensasi &amp; Karir (KP)</div>
          <div style="font-size:12.5px;color:#64748B;line-height:1.5">Daya saing gaji industri, transparansi kepemilikan ESOP, dan jalur jenjang karir yang terukur adil.</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Metodologi Klinis Section -->
  <section id="metodologi" style="padding:70px 48px;max-width:1100px;margin:0 auto">
    <div style="text-align:center;margin-bottom:44px">
      <div style="font-size:11px;font-weight:800;color:#6366F1;letter-spacing:0.05em">STANDAR PENGUKURAN</div>
      <h2 style="font-size:28px;font-weight:800;color:#0F172A;letter-spacing:-0.02em;margin-top:6px">Metodologi &amp; Klasifikasi Risiko Burnout</h2>
      <p style="font-size:14px;color:#64748B;margin-top:4px">Skor komposit dihitung dalam rentang 1.00 – 5.00 dan dipetakan ke 4 tingkatan status klinis.</p>
    </div>

    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px">
      <div class="card" style="padding:22px;border-top:4px solid #10B981">
        <div style="font-size:12px;font-weight:800;color:#10B981;margin-bottom:6px">4.20 – 5.00</div>
        <div style="font-size:15px;font-weight:700;color:#0F172A;margin-bottom:6px">Budaya Kerja Positif</div>
        <div style="font-size:12px;color:#64748B;line-height:1.5">Organisasi sangat sehat, beban terdistribusi merata, dan psychological safety tinggi.</div>
      </div>
      <div class="card" style="padding:22px;border-top:4px solid #2563EB">
        <div style="font-size:12px;font-weight:800;color:#2563EB;margin-bottom:6px">3.50 – 4.19</div>
        <div style="font-size:15px;font-weight:700;color:#0F172A;margin-bottom:6px">Kondisi Stabil</div>
        <div style="font-size:12px;color:#64748B;line-height:1.5">Ritme sprint terkendali, perlu pemantauan berkala saat release cycle tinggi.</div>
      </div>
      <div class="card" style="padding:22px;border-top:4px solid #F59E0B">
        <div style="font-size:12px;font-weight:800;color:#F59E0B;margin-bottom:6px">2.80 – 3.49</div>
        <div style="font-size:15px;font-weight:700;color:#0F172A;margin-bottom:6px">Waspada Gejala Burnout</div>
        <div style="font-size:12px;color:#64748B;line-height:1.5">Terdeteksi kelelahan sistemik awal, jam lembur meningkat, atau friksi komunikasi internal.</div>
      </div>
      <div class="card" style="padding:22px;border-top:4px solid #EF4444">
        <div style="font-size:12px;font-weight:800;color:#EF4444;margin-bottom:6px">1.00 – 2.79</div>
        <div style="font-size:15px;font-weight:700;color:#0F172A;margin-bottom:6px">Risiko Burnout Tinggi</div>
        <div style="font-size:12px;color:#64748B;line-height:1.5">Kelelahan mental akut, risiko turnover tinggi, memerlukan tindakan perbaikan mendesak.</div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section style="padding:60px 48px;text-align:center;background:#F8FAFC">
    <div style="max-width:680px;margin:0 auto">
      <h2 style="font-size:28px;font-weight:800;color:#0F172A;letter-spacing:-0.02em;margin-bottom:12px">Siap Membangun Tim Startup yang Tahan Banting?</h2>
      <p style="font-size:14.5px;color:#64748B;line-height:1.6;margin-bottom:24px">Bergabung dengan ekosistem teknologi yang mengutamakan kesehatan talenta digital untuk pertumbuhan bisnis jangka panjang.</p>
      <a href="{{ route('register') }}" class="btn-primary" style="text-decoration:none;padding:14px 28px;font-size:15px;display:inline-flex;align-items:center">Daftarkan Startup Anda Sekarang</a>
    </div>
  </section>

  <!-- Footer -->
  <footer style="padding:24px 48px;border-top:1px solid #E2E8F0;background:#FFFFFF;display:flex;justify-content:space-between;align-items:center;font-size:12.5px;color:#64748B">
    <div style="font-weight:800;color:#6366F1">WorkPulse</div>
    <div>&copy; {{ date('Y') }} WorkPulse Clinical Analytics. Hak cipta dilindungi.</div>
    <div style="display:flex;gap:18px">
      <a href="#fitur" style="color:#64748B;text-decoration:none">Fitur</a>
      <a href="#pilar" style="color:#64748B;text-decoration:none">6 Pilar</a>
      <a href="#metodologi" style="color:#64748B;text-decoration:none">Metodologi</a>
    </div>
  </footer>
</div>
@endsection

