@extends('layouts.app')

@section('title', 'Daftarkan Startup Anda – WorkPulse')

@push('styles')
<style>
    .auth-shell {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem 1.25rem;
        background-color: var(--bg-page);
    }

    .auth-card-wide {
        width: 100%;
        max-width: 540px;
        background-color: var(--bg-surface);
        border: 1px solid var(--border-hairline);
        border-radius: var(--radius-xl);
        padding: 2.5rem;
        box-shadow: var(--shadow-editorial);
    }

    .auth-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid var(--border-hairline);
        margin-bottom: 1.75rem;
    }

    .auth-back {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--ink-secondary);
        text-decoration: none;
        transition: color 150ms ease;
    }

    .auth-back:hover {
        color: var(--ink-primary);
    }

    .auth-heading {
        font-family: var(--font-serif);
        font-size: 1.875rem;
        font-weight: 600;
        line-height: 1.15;
        letter-spacing: -0.02em;
        color: var(--ink-primary);
        margin-bottom: 0.5rem;
    }

    .auth-sub {
        font-size: 0.9375rem;
        color: var(--ink-secondary);
        line-height: 1.55;
        margin-bottom: 1.75rem;
    }

    .auth-row-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.875rem;
    }

    .auth-section-divider {
        margin: 1.5rem 0 1.25rem;
        padding-top: 1.25rem;
        border-top: 1px solid var(--border-hairline);
    }

    .auth-section-label {
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--ink-muted);
        margin-bottom: 1rem;
    }

    .auth-foot {
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px solid var(--border-hairline);
        font-size: 0.875rem;
        color: var(--ink-secondary);
    }

    .auth-foot a {
        color: var(--ink-primary);
        font-weight: 600;
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    @media (max-width: 560px) {
        .auth-card-wide {
            padding: 1.75rem 1.375rem;
        }
        .auth-row-2 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<main class="auth-shell">
    <div class="auth-card-wide">
        <div class="auth-top">
            <a href="{{ route('home') }}" class="auth-back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Kembali ke Beranda
            </a>
            <a href="{{ route('home') }}" class="wp-brand">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
                <span class="wp-brand-text" style="font-size: 1.125rem;">WorkPulse</span>
            </a>
        </div>

        <h1 class="auth-heading">Daftarkan startup Anda</h1>
        <p class="auth-sub">Mulai asesmen risiko kelelahan dan bangun budaya kerja startup yang resilien.</p>

        @if ($errors->any())
            <div class="wp-alert-error" role="alert">
                <strong>Harap periksa kembali input Anda:</strong>
                <ul>
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" id="register-form">
            @csrf

            <fieldset class="wp-fieldset-plain">
                <legend class="wp-legend">Pilih Peran Anda</legend>
                <div class="wp-role-grid">
                    <label class="wp-role-option">
                        <input
                            type="radio"
                            name="role"
                            value="clevel"
                            class="visually-hidden wp-role-input"
                            {{ old('role', 'clevel') === 'clevel' ? 'checked' : '' }}
                        >
                        <span class="wp-role-card">
                            <span class="wp-role-title">C-Level</span>
                            <span class="wp-role-desc">Founder &amp; Manajemen</span>
                        </span>
                    </label>
                    <label class="wp-role-option">
                        <input
                            type="radio"
                            name="role"
                            value="staf"
                            class="visually-hidden wp-role-input"
                            {{ old('role') === 'staf' ? 'checked' : '' }}
                        >
                        <span class="wp-role-card">
                            <span class="wp-role-title">Staf</span>
                            <span class="wp-role-desc">Karyawan &amp; Engineer</span>
                        </span>
                    </label>
                </div>
            </fieldset>

            <div class="wp-field">
                <label for="startup_name" class="wp-label">Nama Startup</label>
                <input
                    type="text"
                    id="startup_name"
                    name="startup_name"
                    class="wp-input"
                    value="{{ old('startup_name') }}"
                    required
                    autocomplete="organization"
                    enterkeyhint="next"
                    placeholder="Contoh: PulseTech Labs"
                >
            </div>

            <div class="wp-field">
                <label for="email" class="wp-label">Email Kerja Resmi</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="wp-input"
                    value="{{ old('email') }}"
                    required
                    autocomplete="username"
                    spellcheck="false"
                    enterkeyhint="next"
                    placeholder="founder@pulsetech.id"
                >
            </div>

            <div class="auth-row-2">
                <div class="wp-field" style="margin-bottom: 0.375rem;">
                    <label for="password" class="wp-label">Kata Sandi</label>
                    <div class="wp-input-wrap">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="wp-input wp-input-with-toggle"
                            required
                            minlength="6"
                            autocomplete="new-password"
                            aria-describedby="password-help"
                            enterkeyhint="next"
                            placeholder="Minimal 6 karakter"
                        >
                        <button
                            type="button"
                            id="toggle-register-password"
                            class="wp-pwd-toggle"
                            aria-pressed="false"
                            aria-label="Tampilkan kata sandi"
                        >
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="wp-field" style="margin-bottom: 0.375rem;">
                    <label for="password_confirmation" class="wp-label">Ulangi Sandi</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="wp-input"
                        required
                        minlength="6"
                        autocomplete="new-password"
                        enterkeyhint="next"
                        placeholder="Konfirmasi sandi"
                    >
                </div>
            </div>
            <div id="password-help" class="wp-helper" style="margin-bottom: 0.25rem;">
                Minimal 6 karakter. Gunakan kombinasi huruf dan angka yang kuat.
            </div>

            <div class="auth-section-divider">
                <div class="auth-section-label">Profil Ekosistem (Opsional)</div>

                <div class="auth-row-2">
                    <div class="wp-field">
                        <label for="lokasi" class="wp-label">Lokasi Kantor / Hub</label>
                        <select id="lokasi" name="lokasi" class="wp-select">
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

                    <div class="wp-field">
                        <label for="sektor_teknologi" class="wp-label">Sektor Industri</label>
                        <select id="sektor_teknologi" name="sektor_teknologi" class="wp-select">
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

                <div class="wp-field" style="margin-bottom: 1.5rem;">
                    <label for="pendanaan" class="wp-label">Tahap Pendanaan / Funding</label>
                    <select id="pendanaan" name="pendanaan" class="wp-select">
                        <option value="">Pilih Tahap Pendanaan</option>
                        <option {{ old('pendanaan') === 'Bootstrapped / Self-Funded' ? 'selected' : '' }}>Bootstrapped / Self-Funded</option>
                        <option {{ old('pendanaan') === 'Pre-Seed / Angel Round' ? 'selected' : '' }}>Pre-Seed / Angel Round</option>
                        <option {{ old('pendanaan') === 'Seed Stage' ? 'selected' : '' }}>Seed Stage</option>
                        <option {{ old('pendanaan') === 'Series A' ? 'selected' : '' }}>Series A</option>
                        <option {{ old('pendanaan') === 'Series B+' ? 'selected' : '' }}>Series B+</option>
                    </select>
                </div>
            </div>

            <button type="submit" id="register-submit-btn" class="wp-btn-primary wp-btn-full">
                Selesaikan &amp; Buka Dashboard
            </button>

            <p class="auth-foot">
                Sudah memiliki akun? <a href="{{ route('login') }}">Masuk di sini</a>
            </p>
        </form>
    </div>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pwdInput = document.getElementById('password');
    const toggleBtn = document.getElementById('toggle-register-password');
    if (pwdInput && toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            const isHidden = pwdInput.type === 'password';
            pwdInput.type = isHidden ? 'text' : 'password';
            toggleBtn.setAttribute('aria-pressed', String(isHidden));
            toggleBtn.setAttribute('aria-label', isHidden ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        });
    }

    const regForm = document.getElementById('register-form');
    const submitBtn = document.getElementById('register-submit-btn');
    if (regForm && submitBtn) {
        regForm.addEventListener('submit', function () {
            if (regForm.checkValidity()) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Mendaftarkan...';
            }
        });
    }
});
</script>
@endpush
