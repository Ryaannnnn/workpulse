@extends('layouts.app')

@section('title', 'Dashboard – WorkPulse')

@section('content')
<div id="dashboard-app" class="active" style="display:flex;min-height:100vh;background:#F8FAFC">

  <!-- Sidebar -->
  <nav id="sidebar" style="width:250px;background:#FFFFFF;border-right:1px solid #E2E8F0;display:flex;flex-direction:column;flex-shrink:0">
    <div class="sidebar-logo" style="padding:24px 20px;border-bottom:1px solid #E2E8F0">
      <a href="{{ route('dashboard') }}" style="font-size:20px;font-weight:800;color:#6366F1;display:flex;align-items:center;gap:6px;text-decoration:none">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#6366F1" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        WorkPulse
      </a>
      <div style="font-size:11px;color:#64748B;font-weight:600;margin-top:2px">Startup Culture &amp; Burnout Index</div>
    </div>
    <div class="sidebar-nav" style="padding:16px 12px;flex:1;display:flex;flex-direction:column;gap:4px">
      <a href="{{ route('dashboard') }}" class="nav-item active" style="text-decoration:none">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Ringkasan Ekosistem
      </a>
      <a href="{{ route('assessment') }}" class="nav-item" style="text-decoration:none">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
        Form Asesmen Baru
      </a>
      <a href="#pilar-section" class="nav-item" style="text-decoration:none">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
        Profil 6 Pilar
      </a>
      <a href="#tabel-startup" class="nav-item" style="text-decoration:none">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Daftar Startup
      </a>
    </div>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer" style="padding:16px 12px;border-top:1px solid #E2E8F0">
      <a href="{{ route('home') }}" class="nav-item" style="text-decoration:none;margin-bottom:4px">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
        Halaman Beranda
      </a>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="nav-item" style="width:100%;text-align:left;background:none;border:none;color:#EF4444;cursor:pointer;display:flex;align-items:center;gap:10px;font-family:inherit;font-size:13.5px">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Keluar Akun
        </button>
      </form>
    </div>
  </nav>

  <!-- Content Area -->
  <div style="flex:1;display:flex;flex-direction:column;min-width:0;overflow-y:auto">

    <!-- Topbar -->
    <header id="topbar" style="background:#FFFFFF;border-bottom:1px solid #E2E8F0;padding:14px 28px;display:flex;align-items:center;justify-content:space-between">
      <div style="display:flex;align-items:center;gap:12px">
        <h3 style="font-size:16px;font-weight:700;color:#0F172A">Dashboard Ekosistem</h3>
      </div>
      <div style="display:flex;align-items:center;gap:14px">
        <div style="font-size:12px;font-weight:700;background:#EEF2FF;color:#4F46E5;padding:4px 12px;border-radius:20px;border:1px solid #C7D2FE">
          {{ $user->role === 'clevel' ? '👔 Founder (C-Level)' : '💻 Karyawan (Staf)' }}
        </div>
        <div style="font-size:13px;font-weight:600;color:#334155">
          {{ $user->startup_name ?? 'Startup Member' }}
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main style="padding:32px 28px;flex:1">

      <!-- Alert Notifikasi -->
      @if (session('success'))
        <div style="background:#ECFDF5;border:1px solid #A7F3D0;color:#065F46;padding:14px 18px;border-radius:12px;margin-bottom:24px;font-size:13.5px;display:flex;align-items:center;gap:8px">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          {{ session('success') }}
        </div>
      @endif

      <!-- Welcome Banner -->
      <div class="welcome-bar" style="background:#FFFFFF;border:1px solid #E2E8F0;border-radius:16px;padding:24px 28px;display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;box-shadow:var(--shadow-sm)">
        <div>
          <h2 style="font-size:22px;font-weight:800;color:#0F172A">Halo, {{ $user->name ?? $user->startup_name }}! 👋</h2>
          <p style="font-size:13.5px;color:#64748B;margin-top:4px">Berikut adalah hasil asesmen kesehatan organisasi dan status risiko burnout startup Anda.</p>
        </div>
        <div>
          <a href="{{ route('assessment') }}" class="btn-primary" style="text-decoration:none;padding:11px 20px;font-size:13.5px;display:inline-flex;align-items:center;gap:6px">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Isi Form Asesmen Baru
          </a>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="stats-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:28px">
        <div class="stat-card">
          <div>
            <div class="stat-label">Total Startup Terdata</div>
            <div class="stat-value">{{ $totalStartups }}</div>
            <div class="stat-sub up">&uarr; Ekosistem Aktif</div>
          </div>
          <div class="stat-icon blue">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
          </div>
        </div>

        <div class="stat-card">
          <div>
            <div class="stat-label">Total Pengisian Asesmen</div>
            <div class="stat-value">{{ $totalAssessments }}</div>
            <div class="stat-sub gray">Hasil Evaluasi Masuk</div>
          </div>
          <div class="stat-icon green">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          </div>
        </div>

        <div class="stat-card">
          <div>
            <div class="stat-label">Skor Terakhir Anda</div>
            <div class="stat-value" style="color:{{ $latestSubmission && $latestSubmission->final_score >= 3.5 ? '#10B981' : '#F59E0B' }}">
              {{ $latestSubmission ? number_format($latestSubmission->final_score, 2) . ' / 5.0' : 'Belum Ada' }}
            </div>
            <div class="stat-sub gray">
              {{ $latestSubmission ? $latestSubmission->burnout_status : 'Silakan isi asesmen' }}
            </div>
          </div>
          <div class="stat-icon yellow">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="12 6 12 12 16 14"/></svg>
          </div>
        </div>

        <div class="stat-card">
          <div>
            <div class="stat-label">Proporsi Budaya Sehat</div>
            <div class="stat-value" style="color:#10B981">{{ $healthyPercentage }}%</div>
            <div class="stat-sub up">Skor &ge; 3.50</div>
          </div>
          <div class="stat-icon green">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/></svg>
          </div>
        </div>
      </div>

      <!-- Charts & 6 Pilar Breakdown -->
      <div id="pilar-section" style="display:grid;grid-template-columns:1.4fr 1fr;gap:20px;margin-bottom:28px">
        <!-- 6 Pillars Score Card -->
        <div class="card" style="padding:24px">
          <h3 style="font-size:16px;font-weight:700;color:#0F172A;margin-bottom:6px">Rata-rata 6 Pilar Budaya Organisasi</h3>
          <p style="font-size:12.5px;color:#64748B;margin-bottom:20px">Skala skor 1.00 – 5.00 berdasarkan komparasi data ekosistem.</p>

          <div style="display:flex;flex-direction:column;gap:14px">
            <div>
              <div style="display:flex;justify-content:space-between;font-size:13px;font-weight:600;margin-bottom:4px">
                <span>Visi &amp; Transparansi (VT)</span>
                <span style="color:#6366F1">{{ number_format($averages['VT'], 2) }} / 5.0</span>
              </div>
              <div style="height:8px;background:#F1F5F9;border-radius:4px;overflow:hidden">
                <div style="height:100%;width:{{ ($averages['VT']/5)*100 }}%;background:#6366F1;border-radius:4px"></div>
              </div>
            </div>

            <div>
              <div style="display:flex;justify-content:space-between;font-size:13px;font-weight:600;margin-bottom:4px">
                <span>Kualitas Kepemimpinan (KK)</span>
                <span style="color:#10B981">{{ number_format($averages['KK'], 2) }} / 5.0</span>
              </div>
              <div style="height:8px;background:#F1F5F9;border-radius:4px;overflow:hidden">
                <div style="height:100%;width:{{ ($averages['KK']/5)*100 }}%;background:#10B981;border-radius:4px"></div>
              </div>
            </div>

            <div>
              <div style="display:flex;justify-content:space-between;font-size:13px;font-weight:600;margin-bottom:4px">
                <span>Fasilitas &amp; Infrastruktur (FI)</span>
                <span style="color:#0EA5E9">{{ number_format($averages['FI'], 2) }} / 5.0</span>
              </div>
              <div style="height:8px;background:#F1F5F9;border-radius:4px;overflow:hidden">
                <div style="height:100%;width:{{ ($averages['FI']/5)*100 }}%;background:#0EA5E9;border-radius:4px"></div>
              </div>
            </div>

            <div>
              <div style="display:flex;justify-content:space-between;font-size:13px;font-weight:600;margin-bottom:4px">
                <span>Beban Kerja / Workload (BK)</span>
                <span style="color:#F43F5E">{{ number_format($averages['BK'], 2) }} / 5.0</span>
              </div>
              <div style="height:8px;background:#F1F5F9;border-radius:4px;overflow:hidden">
                <div style="height:100%;width:{{ ($averages['BK']/5)*100 }}%;background:#F43F5E;border-radius:4px"></div>
              </div>
            </div>

            <div>
              <div style="display:flex;justify-content:space-between;font-size:13px;font-weight:600;margin-bottom:4px">
                <span>Kesejahteraan Mental (KM)</span>
                <span style="color:#8B5CF6">{{ number_format($averages['KM'], 2) }} / 5.0</span>
              </div>
              <div style="height:8px;background:#F1F5F9;border-radius:4px;overflow:hidden">
                <div style="height:100%;width:{{ ($averages['KM']/5)*100 }}%;background:#8B5CF6;border-radius:4px"></div>
              </div>
            </div>

            <div>
              <div style="display:flex;justify-content:space-between;font-size:13px;font-weight:600;margin-bottom:4px">
                <span>Kompensasi &amp; Karir (KP)</span>
                <span style="color:#F59E0B">{{ number_format($averages['KP'], 2) }} / 5.0</span>
              </div>
              <div style="height:8px;background:#F1F5F9;border-radius:4px;overflow:hidden">
                <div style="height:100%;width:{{ ($averages['KP']/5)*100 }}%;background:#F59E0B;border-radius:4px"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Burnout Distribution Card -->
        <div class="card" style="padding:24px;display:flex;flex-direction:column">
          <h3 style="font-size:16px;font-weight:700;color:#0F172A;margin-bottom:6px">Distribusi Risiko Burnout</h3>
          <p style="font-size:12.5px;color:#64748B;margin-bottom:20px">Persentase status klinis ekosistem teknologi.</p>
          <div style="flex:1;position:relative;min-height:220px">
            <canvas id="burnoutPieChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Tabel Startup Terdaftar -->
      <div id="tabel-startup" class="card" style="padding:24px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
          <div>
            <h3 style="font-size:16px;font-weight:700;color:#0F172A">Daftar Startup &amp; Evaluasi Terkini</h3>
            <p style="font-size:12.5px;color:#64748B">Rekapitulasi benchmark budaya kerja dan indeks burnout.</p>
          </div>
        </div>

        <div style="overflow-x:auto">
          <table style="width:100%;border-collapse:collapse;font-size:13px;text-align:left">
            <thead>
              <tr style="border-bottom:1px solid #E2E8F0;background:#F8FAFC">
                <th style="padding:12px 14px;color:#475569;font-weight:700">Nama Startup</th>
                <th style="padding:12px 14px;color:#475569;font-weight:700">Lokasi Hub</th>
                <th style="padding:12px 14px;color:#475569;font-weight:700">Sektor</th>
                <th style="padding:12px 14px;color:#475569;font-weight:700">Peran</th>
                <th style="padding:12px 14px;color:#475569;font-weight:700">Skor Budaya</th>
                <th style="padding:12px 14px;color:#475569;font-weight:700">Status Risiko</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($recentStartups as $st)
                <tr style="border-bottom:1px solid #F1F5F9">
                  <td style="padding:14px;font-weight:700;color:#0F172A">{{ $st['startup_name'] }}</td>
                  <td style="padding:14px;color:#64748B">{{ $st['lokasi'] }}</td>
                  <td style="padding:14px;color:#64748B">{{ $st['sektor_teknologi'] }}</td>
                  <td style="padding:14px">
                    <span style="font-size:11.5px;font-weight:700;padding:3px 8px;border-radius:12px;background:{{ $st['role'] === 'clevel' ? '#EEF2FF' : '#F1F5F9' }};color:{{ $st['role'] === 'clevel' ? '#4F46E5' : '#475569' }}">
                      {{ $st['role'] === 'clevel' ? 'C-Level' : 'Staf' }}
                    </span>
                  </td>
                  <td style="padding:14px;font-weight:700;color:#0F172A">{{ number_format($st['score_on_5'], 2) }}</td>
                  <td style="padding:14px">
                    <span style="font-size:12px;font-weight:700;color:{{ $st['score_on_5'] >= 3.5 ? '#10B981' : ($st['score_on_5'] >= 2.8 ? '#F59E0B' : '#EF4444') }}">
                      {{ $st['health_category'] }}
                    </span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" style="padding:24px;text-align:center;color:#94A3B8">Belum ada data evaluasi startup. Jadilah yang pertama mengisi asesmen!</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const ctx = document.getElementById('burnoutPieChart')?.getContext('2d');
  if (ctx) {
    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Budaya Positif', 'Kondisi Stabil', 'Waspada Burnout', 'Risiko Tinggi'],
        datasets: [{
          data: [
            {{ $statusCounts['Budaya Kerja Positif'] ?? 1 }},
            {{ $statusCounts['Kondisi Stabil'] ?? 1 }},
            {{ $statusCounts['Waspada Gejala Burnout'] ?? 0 }},
            {{ $statusCounts['Risiko Burnout Tinggi'] ?? 0 }}
          ],
          backgroundColor: ['#10B981', '#2563EB', '#F59E0B', '#EF4444'],
          borderWidth: 2,
          borderColor: '#FFFFFF'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: { font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 } }
          }
        }
      }
    });
  }
});
</script>
@endsection

