@extends('layouts.app')

@section('title', 'Masuk ke Akun – WorkPulse')

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

    .auth-card {
        width: 100%;
        max-width: 460px;
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

    .auth-forgot-btn {
        border: none;
        background: transparent;
        padding: 0;
        font-family: inherit;
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--ink-secondary);
        text-decoration: underline;
        text-underline-offset: 3px;
        cursor: pointer;
    }

    .auth-forgot-btn:hover {
        color: var(--ink-primary);
    }

    .auth-forgot-note {
        margin-top: 0.5rem;
        padding: 0.625rem 0.75rem;
        border-radius: var(--radius-md);
        background-color: var(--bg-subtle);
        border: 1px solid var(--border-hairline);
        font-size: 0.8125rem;
        color: var(--ink-secondary);
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

    @media (max-width: 520px) {
        .auth-card {
            padding: 1.75rem 1.375rem;
        }
    }
</style>
@endpush

@section('content')
<main class="auth-shell">
    <div class="auth-card">
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

        <h1 class="auth-heading">Masuk ke WorkPulse</h1>
        <p class="auth-sub">Akses dasbor analitik budaya kerja dan pemantauan risiko burnout startup Anda.</p>

        @if ($errors->any())
            <div class="wp-alert-error" role="alert">
                <strong>Terjadi kendala:</strong>
                <ul>
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="wp-alert-success" role="status">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="login-form">
            @csrf

            <fieldset class="wp-fieldset-plain">
                <legend class="wp-legend">Peran Anda</legend>
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
                <label for="email" class="wp-label">Email Kerja Resmi</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="wp-input"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    spellcheck="false"
                    enterkeyhint="next"
                    placeholder="nama@startup.com"
                >
            </div>

            <div class="wp-field" style="margin-bottom: 1.5rem;">
                <div class="wp-label-row">
                    <label for="password" class="wp-label">Kata Sandi</label>
                    <button
                        type="button"
                        id="forgot-password-btn"
                        class="auth-forgot-btn"
                        aria-expanded="false"
                        aria-controls="forgot-password-note"
                    >
                        Lupa sandi?
                    </button>
                </div>
                <div class="wp-input-wrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="wp-input wp-input-with-toggle"
                        required
                        autocomplete="current-password"
                        enterkeyhint="done"
                        placeholder="Masukkan kata sandi"
                    >
                    <button
                        type="button"
                        id="toggle-login-password"
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
                <div id="forgot-password-note" class="auth-forgot-note" hidden>
                    Hubungi administrator tim Anda untuk menyetel ulang kata sandi akun startup.
                </div>
            </div>

            <button type="submit" id="login-submit-btn" class="wp-btn-primary wp-btn-full">
                Masuk Akun
            </button>

            <p class="auth-foot">
                Startup Anda belum terdaftar? <a href="{{ route('register') }}">Daftarkan di sini</a>
            </p>
        </form>
    </div>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pwdInput = document.getElementById('password');
    const toggleBtn = document.getElementById('toggle-login-password');
    if (pwdInput && toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            const isHidden = pwdInput.type === 'password';
            pwdInput.type = isHidden ? 'text' : 'password';
            toggleBtn.setAttribute('aria-pressed', String(isHidden));
            toggleBtn.setAttribute('aria-label', isHidden ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        });
    }

    const forgotBtn = document.getElementById('forgot-password-btn');
    const forgotNote = document.getElementById('forgot-password-note');
    if (forgotBtn && forgotNote) {
        forgotBtn.addEventListener('click', function () {
            const willShow = forgotNote.hidden;
            forgotNote.hidden = !willShow;
            forgotBtn.setAttribute('aria-expanded', String(willShow));
        });
    }

    const loginForm = document.getElementById('login-form');
    const submitBtn = document.getElementById('login-submit-btn');
    if (loginForm && submitBtn) {
        loginForm.addEventListener('submit', function () {
            if (loginForm.checkValidity()) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Memproses...';
            }
        });
    }
});
</script>
@endpush
