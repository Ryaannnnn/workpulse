@extends('layouts.app')

@section('title', 'Masuk ke Akun – WorkPulse')

@section('content')
<div class="auth-wrapper" style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:#F8FAFC;padding:40px 20px">
  <div class="auth-form-wrap" style="width:100%;max-width:480px;background:#FFFFFF;padding:40px;border-radius:16px;border:1px solid #E2E8F0;box-shadow:0 10px 25px -5px rgba(15,23,42,0.06)">
    
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

    <h1 style="font-size:24px;font-weight:800;color:#0F172A;letter-spacing:-0.02em;margin-bottom:6px">Masuk ke WorkPulse</h1>
    <p style="font-size:13.5px;color:#64748B;margin-bottom:24px;line-height:1.5">Akses dasbor analitik budaya kerja &amp; pemantauan risiko burnout startup Anda.</p>

    <!-- Error Alert -->
    @if ($errors->any())
      <div style="background:#FEF2F2;border:1px solid #FCA5A5;color:#991B1B;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px">
        <strong>Terjadi kendala:</strong>
        <ul style="margin:4px 0 0 16px;padding:0">
          @foreach ($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if (session('success'))
      <div style="background:#ECFDF5;border:1px solid #A7F3D0;color:#065F46;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px">
        {{ session('success') }}
      </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
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

      <!-- Email Field -->
      <div style="margin-bottom:18px">
        <label for="email" style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px">Email Kerja Resmi</label>
        <div style="position:relative;display:flex;align-items:center">
          <svg style="position:absolute;left:14px;color:#94A3B8;pointer-events:none" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@startup.com" style="width:100%;padding:11px 14px 11px 40px;border:1px solid #CBD5E1;border-radius:10px;font-size:13.5px;font-family:inherit;outline:none;transition:border-color 0.2s">
        </div>
      </div>

      <!-- Password Field -->
      <div style="margin-bottom:20px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
          <label for="password" style="font-size:12px;font-weight:700;color:#475569">Kata Sandi</label>
          <a href="javascript:void(0)" onclick="alert('Silakan hubungi administrator tim Anda untuk menyetel ulang kata sandi.')" style="font-size:12px;color:#6366F1;text-decoration:none;font-weight:600">Lupa sandi?</a>
        </div>
        <div style="position:relative;display:flex;align-items:center">
          <svg style="position:absolute;left:14px;color:#94A3B8;pointer-events:none" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input type="password" id="password" name="password" required placeholder="••••••••" style="width:100%;padding:11px 42px 11px 40px;border:1px solid #CBD5E1;border-radius:10px;font-size:13.5px;font-family:inherit;outline:none;transition:border-color 0.2s">
          <button type="button" onclick="togglePasswordVisibility('password')" style="position:absolute;right:12px;background:none;border:none;cursor:pointer;color:#94A3B8;display:flex;align-items:center">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="btn-auth" style="width:100%;padding:12px;background:#6366F1;color:#FFFFFF;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;transition:background 0.2s;box-shadow:0 1px 2px rgba(0,0,0,0.05)">
        Masuk Akun
      </button>

      <p style="text-align:center;font-size:13px;color:#64748B;margin-top:24px">
        Startup Anda belum terdaftar? <a href="{{ route('register') }}" style="color:#6366F1;font-weight:600;text-decoration:none">Daftarkan di sini</a>
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

function togglePasswordVisibility(id) {
  const el = document.getElementById(id);
  if (el) el.type = el.type === 'password' ? 'text' : 'password';
}
</script>
@endsection

