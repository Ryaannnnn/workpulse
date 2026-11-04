@extends('layouts.app')

@section('title', 'Daftarkan Startup Anda – WorkPulse')

@section('content')
<div class="auth-wrapper" style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:#F8FAFC;padding:40px 20px">
  <div class="auth-form-wrap" style="width:100%;max-width:520px;background:#FFFFFF;padding:40px;border-radius:16px;border:1px solid #E2E8F0;box-shadow:0 10px 25px -5px rgba(15,23,42,0.06)">
    
    <!-- Header: Back & Brand -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
      <a href="{{ route('home') }}" style="display:inline-flex;align-items:center;gap:6px;color:#64748B;font-size:13px;font-weight:600;text-decoration:none">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        Kembali ke Beranda
      </a>
      <a href="{{ route('home') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:18px;font-weight:800;color:#6366F1;text-decoration:none">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        WorkPulse
      </a>
    </div>

    <h1 style="font-size:24px;font-weight:800;color:#0F172A;letter-spacing:-0.02em;margin-bottom:6px">Daftarkan Startup Anda</h1>
    <p style="font-size:13.5px;color:#64748B;margin-bottom:24px;line-height:1.5">Mulai asesmen risiko kelelahan dan bangun budaya kerja startup yang resilien.</p>

    <!-- Error Alert -->
    @if ($errors->any())
      <div style="background:#FEF2F2;border:1px solid #FCA5A5;color:#991B1B;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px">
        <strong>Harap periksa kembali input Anda:</strong>
        <ul style="margin:4px 0 0 16px;padding:0">
          @foreach ($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
      @csrf

      <!-- Role Selection -->
      <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:8px">Pilih Peran Anda</label>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px">
        <label style="cursor:pointer">
          <input type="radio" name="role" value="clevel" {{ old('role', 'clevel') === 'clevel' ? 'checked' : '' }} style="display:none" onchange="updateRoleHighlight(this)">
          <div id="role-box-clevel" style="border:2px solid {{ old('role', 'clevel') === 'clevel' ? '#6366F1' : '#E2E8F0' }};background:{{ old('role', 'clevel') === 'clevel' ? '#EEF2FF' : '#FFFFFF' }};padding:14px;border-radius:12px;text-align:center;transition:all 0.2s">
            <div style="font-size:18px;margin-bottom:4px">👔</div>
            <div style="font-size:13.5px;font-weight:700;color:#0F172A">C-Level</div>
            <div style="font-size:11.5px;color:#64748B">Founder &amp; Manajemen</div>
          </div>
        </label>
        <label style="cursor:pointer">
          <input type="radio" name="role" value="staf" {{ old('role') === 'staf' ? 'checked' : '' }} style="display:none" onchange="updateRoleHighlight(this)">
          <div id="role-box-staf" style="border:2px solid {{ old('role') === 'staf' ? '#6366F1' : '#E2E8F0' }};background:{{ old('role') === 'staf' ? '#EEF2FF' : '#FFFFFF' }};padding:14px;border-radius:12px;text-align:center;transition:all 0.2s">
            <div style="font-size:18px;margin-bottom:4px">💻</div>
            <div style="font-size:13.5px;font-weight:700;color:#0F172A">Staf</div>
            <div style="font-size:11.5px;color:#64748B">Karyawan &amp; Engineer</div>
          </div>
        </label>
      </div>

      <!-- Nama Startup -->
      <div style="margin-bottom:16px">
        <label for="startup_name" style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px">Nama Startup</label>
        <div style="position:relative;display:flex;align-items:center">
          <svg style="position:absolute;left:14px;color:#94A3B8;pointer-events:none" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 22 22 7 12 2"/></svg>
          <input type="text" id="startup_name" name="startup_name" value="{{ old('startup_name') }}" required placeholder="Contoh: PulseTech Labs" style="width:100%;padding:11px 14px 11px 40px;border:1px solid #CBD5E1;border-radius:10px;font-size:13.5px;font-family:inherit;outline:none">
        </div>
      </div>

      <!-- Email Field -->
      <div style="margin-bottom:16px">
        <label for="email" style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px">Email Kerja Resmi</label>
        <div style="position:relative;display:flex;align-items:center">
          <svg style="position:absolute;left:14px;color:#94A3B8;pointer-events:none" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="founder@pulsetech.id" style="width:100%;padding:11px 14px 11px 40px;border:1px solid #CBD5E1;border-radius:10px;font-size:13.5px;font-family:inherit;outline:none">
        </div>
      </div>

      <!-- Password Row -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
        <div>
          <label for="password" style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px">Kata Sandi</label>
          <div style="position:relative;display:flex;align-items:center">
            <input type="password" id="password" name="password" required placeholder="Min. 6 karakter" style="width:100%;padding:11px 14px;border:1px solid #CBD5E1;border-radius:10px;font-size:13.5px;font-family:inherit;outline:none">
          </div>
        </div>
        <div>
          <label for="password_confirmation" style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px">Ulangi Sandi</label>
          <div style="position:relative;display:flex;align-items:center">
            <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Konfirmasi sandi" style="width:100%;padding:11px 14px;border:1px solid #CBD5E1;border-radius:10px;font-size:13.5px;font-family:inherit;outline:none">
          </div>
        </div>
      </div>

      <!-- Lokasi & Sektor Row -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
        <div>
          <label for="lokasi" style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px">Lokasi Kantor / Hub</label>
          <select id="lokasi" name="lokasi" style="width:100%;padding:11px 12px;border:1px solid #CBD5E1;border-radius:10px;font-size:13px;font-family:inherit;outline:none;background:#FFFFFF">
            <option value="">Pilih Kota / Lokasi</option>
            <option {{ old('lokasi') === 'Jakarta (Jabodetabek)' ? 'selected' : '' }}>Jakarta (Jabodetabek)</option>
            <option {{ old('lokasi') === 'Bandung' ? 'selected' : '' }}>Bandung</option>
            <option {{ old('lokasi') === 'Yogyakarta' ? 'selected' : '' }}>Yogyakarta</option>
            <option {{ old('lokasi') === 'Surabaya' ? 'selected' : '' }}>Surabaya</option>
            <option {{ old('lokasi') === 'Bali (Tech Nomad Hub)' ? 'selected' : '' }}>Bali (Tech Nomad Hub)</option>
            <option {{ old('lokasi') === 'Medan' ? 'selected' : '' }}>Medan</option>
            <option {{ old('lokasi') === 'Semarang' ? 'selected' : '' }}>Semarang</option>
            <option {{ old('lokasi') === 'Remote / Terdistribusi' ? 'selected' : '' }}>Remote / Terdistribusi</option>
          </select>
        </div>
        <div>
          <label for="sektor_teknologi" style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px">Sektor Industri</label>
          <select id="sektor_teknologi" name="sektor_teknologi" style="width:100%;padding:11px 12px;border:1px solid #CBD5E1;border-radius:10px;font-size:13px;font-family:inherit;outline:none;background:#FFFFFF">
            <option value="">Pilih Sektor</option>
            <option {{ old('sektor_teknologi') === 'SaaS (Software-as-a-Service)' ? 'selected' : '' }}>SaaS (Software-as-a-Service)</option>
            <option {{ old('sektor_teknologi') === 'FinTech (Financial Technology)' ? 'selected' : '' }}>FinTech (Financial Technology)</option>
            <option {{ old('sektor_teknologi') === 'EdTech (Education Technology)' ? 'selected' : '' }}>EdTech (Education Technology)</option>
            <option {{ old('sektor_teknologi') === 'HealthTech & BioTech' ? 'selected' : '' }}>HealthTech &amp; BioTech</option>
            <option {{ old('sektor_teknologi') === 'AI & Machine Learning' ? 'selected' : '' }}>AI &amp; Machine Learning</option>
            <option {{ old('sektor_teknologi') === 'E-Commerce' ? 'selected' : '' }}>E-Commerce</option>
            <option {{ old('sektor_teknologi') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
          </select>
        </div>
      </div>

      <!-- Pendanaan / Funding Stage -->
      <div style="margin-bottom:20px">
        <label for="pendanaan" style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px">Tahap Pendanaan / Funding</label>
        <select id="pendanaan" name="pendanaan" style="width:100%;padding:11px 12px;border:1px solid #CBD5E1;border-radius:10px;font-size:13px;font-family:inherit;outline:none;background:#FFFFFF">
          <option value="">Pilih Tahap Pendanaan</option>
          <option {{ old('pendanaan') === 'Bootstrapped / Self-Funded' ? 'selected' : '' }}>Bootstrapped / Self-Funded</option>
          <option {{ old('pendanaan') === 'Pre-Seed / Angel Round' ? 'selected' : '' }}>Pre-Seed / Angel Round</option>
          <option {{ old('pendanaan') === 'Seed Stage' ? 'selected' : '' }}>Seed Stage</option>
          <option {{ old('pendanaan') === 'Series A' ? 'selected' : '' }}>Series A</option>
          <option {{ old('pendanaan') === 'Series B+' ? 'selected' : '' }}>Series B+</option>
        </select>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="btn-auth" style="width:100%;padding:12px;background:#6366F1;color:#FFFFFF;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;transition:background 0.2s;box-shadow:0 1px 2px rgba(0,0,0,0.05)">
        Selesaikan &amp; Buka Dashboard
      </button>

      <p style="text-align:center;font-size:13px;color:#64748B;margin-top:24px">
        Sudah memiliki akun? <a href="{{ route('login') }}" style="color:#6366F1;font-weight:600;text-decoration:none">Masuk di sini</a>
      </p>
    </form>
  </div>
</div>

<script>
function updateRoleHighlight(radio) {
  const isClevel = radio.value === 'clevel';
  document.getElementById('role-box-clevel').style.borderColor = isClevel ? '#6366F1' : '#E2E8F0';
  document.getElementById('role-box-clevel').style.backgroundColor = isClevel ? '#EEF2FF' : '#FFFFFF';
  document.getElementById('role-box-staf').style.borderColor = !isClevel ? '#6366F1' : '#E2E8F0';
  document.getElementById('role-box-staf').style.backgroundColor = !isClevel ? '#EEF2FF' : '#FFFFFF';
}
</script>
@endsection

