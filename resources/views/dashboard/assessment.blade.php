@extends('layouts.app')

@section('title', 'Formulir Asesmen Budaya Kerja – WorkPulse')

@push('styles')
<style>
    .as-sticky-bar {
        position: sticky;
        top: 0;
        z-index: 40;
        background-color: var(--bg-page);
        border-bottom: 1px solid var(--border-hairline);
    }

    .as-sticky-inner {
        max-width: 48rem;
        margin-inline: auto;
        padding: 0.9375rem 1.25rem 0.75rem;
    }

    .as-sticky-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 0.625rem;
        flex-wrap: wrap;
    }

    .as-back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--ink-secondary);
        text-decoration: none;
        transition: color 150ms ease;
    }

    .as-back-link:hover {
        color: var(--ink-primary);
    }

    .as-meta-right {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        font-size: 0.8125rem;
    }

    .as-mode-pill {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        background-color: var(--bg-subtle);
        border: 1px solid var(--border-hairline);
        color: var(--ink-primary);
        font-weight: 600;
    }

    .as-progress-label {
        font-weight: 600;
        color: var(--ink-primary);
    }

    .as-progress-track {
        height: 4px;
        width: 100%;
        background-color: var(--bg-track);
        border-radius: 9999px;
        overflow: hidden;
    }

    .as-progress-fill {
        height: 100%;
        width: 0%;
        background-color: var(--ink-primary);
        border-radius: 9999px;
        transition: width 150ms ease;
    }

    /* Main Reading Container */
    .as-main {
        max-width: 48rem;
        margin-inline: auto;
        padding: 2.75rem 1.25rem 5rem;
    }

    .as-intro-card {
        background-color: var(--bg-surface);
        border: 1px solid var(--border-hairline);
        border-radius: var(--radius-xl);
        padding: 2rem;
        margin-bottom: 1.75rem;
        box-shadow: var(--shadow-editorial);
    }

    .as-title {
        font-family: var(--font-serif);
        font-size: clamp(1.75rem, 3vw, 2.25rem);
        font-weight: 600;
        line-height: 1.15;
        letter-spacing: -0.02em;
        color: var(--ink-primary);
        margin-bottom: 0.75rem;
    }

    .as-intro-text {
        font-size: 0.9375rem;
        color: var(--ink-secondary);
        line-height: 1.65;
    }

    /* Pillar Section Card */
    .as-pillar-card {
        background-color: var(--bg-surface);
        border: 1px solid var(--border-hairline);
        border-radius: var(--radius-xl);
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-editorial);
    }

    .as-pillar-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border-hairline);
        margin-bottom: 1.5rem;
    }

    .as-pillar-title {
        font-family: var(--font-serif);
        font-size: 1.3125rem;
        font-weight: 600;
        color: var(--ink-primary);
        line-height: 1.25;
    }

    .as-pillar-code {
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.06em;
        color: var(--ink-muted);
        background-color: var(--bg-subtle);
        border: 1px solid var(--border-hairline);
        padding: 0.25rem 0.625rem;
        border-radius: 9999px;
        flex-shrink: 0;
    }

    .as-questions-list {
        display: flex;
        flex-direction: column;
        gap: 1.75rem;
    }

    .as-question-fieldset {
        border: 0;
        padding: 0 0 1.5rem 0;
        margin: 0;
        border-bottom: 1px solid var(--border-hairline);
    }

    .as-question-fieldset:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }

    .as-question-legend {
        font-size: 0.9375rem;
        font-weight: 600;
        color: var(--ink-primary);
        line-height: 1.55;
        margin-bottom: 1rem;
        padding: 0;
    }

    .as-likert-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 0.5rem;
    }

    .as-likert-option {
        position: relative;
        display: block;
        cursor: pointer;
    }

    .as-likert-segment {
        min-height: 52px;
        padding: 0.5rem 0.25rem;
        border: 1px solid var(--border-control);
        border-radius: var(--radius-md);
        background-color: var(--bg-surface);
        color: var(--ink-primary);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.125rem;
        text-align: center;
        transition: background-color 150ms ease, border-color 150ms ease, color 150ms ease;
    }

    .as-likert-segment:hover {
        background-color: var(--bg-subtle);
        border-color: var(--border-control-hover);
    }

    .as-likert-num {
        font-size: 0.9375rem;
        font-weight: 700;
        line-height: 1.1;
    }

    .as-likert-tag {
        font-size: 0.6875rem;
        font-weight: 500;
        color: var(--ink-muted);
        line-height: 1.1;
    }

    .as-likert-input:checked + .as-likert-segment {
        background-color: var(--ink-primary);
        border-color: var(--ink-primary);
        color: var(--bg-surface);
    }

    .as-likert-input:checked + .as-likert-segment .as-likert-tag {
        color: var(--bg-subtle);
    }

    .as-likert-input:focus-visible + .as-likert-segment {
        outline: 2px solid var(--ink-primary);
        outline-offset: 2px;
    }

    .as-scale-caption {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
        color: var(--ink-muted);
        margin-top: 0.5rem;
    }

    /* Footer Action Row */
    .as-submit-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-hairline);
    }

    .as-footer-status {
        font-size: 0.875rem;
        color: var(--ink-secondary);
    }

    .as-submit-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    @media (max-width: 600px) {
        .as-intro-card,
        .as-pillar-card {
            padding: 1.375rem;
        }
        .as-likert-tag {
            font-size: 0.625rem;
        }
        .as-submit-bar {
            flex-direction: column;
            align-items: stretch;
        }
        .as-submit-actions {
            justify-content: flex-end;
        }
    }
</style>
@endpush

@section('content')
@php
    $totalQuestions = collect($groupedQuestions)->sum(fn($g) => count($g['items'] ?? []));
    $scaleShortLabels = [
        1 => 'STS',
        2 => 'TS',
        3 => 'Netral',
        4 => 'S',
        5 => 'SS',
    ];
@endphp

<!-- Sticky Progress Bar -->
<div class="as-sticky-bar">
    <div class="as-sticky-inner">
        <div class="as-sticky-top">
            <a href="{{ route('dashboard') }}" class="as-back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Kembali ke Dashboard
            </a>

            <div class="as-meta-right">
                <span class="as-mode-pill">
                    Mode: {{ $user->role === 'clevel' ? 'C-Level / Founder' : 'Staf / Karyawan' }}
                </span>
                <span id="assessment-progress-text" class="as-progress-label tabular-nums" aria-live="polite">
                    0 / {{ $totalQuestions }} terjawab
                </span>
            </div>
        </div>

        <div class="as-progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="{{ $totalQuestions }}" aria-valuenow="0" id="assessment-progressbar">
            <div id="assessment-progress-fill" class="as-progress-fill"></div>
        </div>
    </div>
</div>

<main class="as-main">
    <div class="as-intro-card">
        <div class="wp-kicker">Kuesioner Evaluasi</div>
        <h1 class="as-title">Asesmen Budaya Kerja &amp; Risiko Burnout</h1>
        <p class="as-intro-text">
            Jawablah setiap pernyataan berikut secara objektif sesuai kondisi riil di <strong>{{ $user->startup_name }}</strong>. Penilaian menggunakan skala Likert 1 (Sangat Tidak Setuju) hingga 5 (Sangat Setuju).
        </p>
    </div>

    @if ($errors->any())
        <div class="wp-alert-error" role="alert">
            <strong>Perhatian:</strong>
            <ul>
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('assessment.submit') }}" id="assessment-form">
        @csrf

        @foreach ($groupedQuestions as $factorCode => $group)
            <section class="as-pillar-card" aria-labelledby="pillar-heading-{{ $factorCode }}">
                <div class="as-pillar-head">
                    <div>
                        <div class="wp-kicker tabular-nums" style="margin-bottom: 0.25rem;">
                            Pilar {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </div>
                        <h2 id="pillar-heading-{{ $factorCode }}" class="as-pillar-title">
                            {{ $group['factor_name'] ?? 'Indikator Pilar' }}
                        </h2>
                    </div>
                    <span class="as-pillar-code">{{ $factorCode }}</span>
                </div>

                <div class="as-questions-list">
                    @foreach ($group['items'] as $q)
                        <fieldset class="as-question-fieldset">
                            <legend class="as-question-legend">
                                <span class="tabular-nums">{{ $loop->iteration }}.</span> {{ $q->text }}
                            </legend>

                            <div class="as-likert-grid">
                                @for ($i = 1; $i <= 5; $i++)
                                    <label class="as-likert-option">
                                        <input
                                            type="radio"
                                            name="answers[{{ $factorCode }}][{{ $q->question_index }}]"
                                            value="{{ $i }}"
                                            class="visually-hidden as-likert-input"
                                            required
                                            {{ (string) old("answers.{$factorCode}.{$q->question_index}") === (string) $i ? 'checked' : '' }}
                                        >
                                        <span class="as-likert-segment">
                                            <span class="as-likert-num tabular-nums">{{ $i }}</span>
                                            <span class="as-likert-tag">{{ $scaleShortLabels[$i] }}</span>
                                        </span>
                                    </label>
                                @endfor
                            </div>

                            <div class="as-scale-caption" aria-hidden="true">
                                <span>1 = Sangat Tidak Setuju</span>
                                <span>5 = Sangat Setuju</span>
                            </div>
                        </fieldset>
                    @endforeach
                </div>
            </section>
        @endforeach

        <div class="as-submit-bar">
            <span id="assessment-footer-status" class="as-footer-status tabular-nums">
                0 dari {{ $totalQuestions }} indikator terjawab
            </span>
            <div class="as-submit-actions">
                <a href="{{ route('dashboard') }}" class="wp-btn-secondary">Batal</a>
                <button type="submit" id="assessment-submit-btn" class="wp-btn-primary">
                    Kirim &amp; Hitung Skor Asesmen
                </button>
            </div>
        </div>
    </form>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('assessment-form');
    const progressText = document.getElementById('assessment-progress-text');
    const progressFill = document.getElementById('assessment-progress-fill');
    const progressBar = document.getElementById('assessment-progressbar');
    const footerStatus = document.getElementById('assessment-footer-status');
    const submitBtn = document.getElementById('assessment-submit-btn');

    if (!form) return;

    const totalQuestions = {{ (int) $totalQuestions }};

    function updateProgress() {
        const checkedCount = form.querySelectorAll('input[type="radio"]:checked').length;
        const pct = totalQuestions > 0 ? Math.round((checkedCount / totalQuestions) * 100) : 0;

        if (progressText) {
            progressText.textContent = checkedCount + ' / ' + totalQuestions + ' terjawab';
        }
        if (progressFill) {
            progressFill.style.width = pct + '%';
        }
        if (progressBar) {
            progressBar.setAttribute('aria-valuenow', String(checkedCount));
        }
        if (footerStatus) {
            footerStatus.textContent = checkedCount + ' dari ' + totalQuestions + ' indikator terjawab';
        }
    }

    form.addEventListener('change', function (e) {
        if (e.target && e.target.matches('input[type="radio"]')) {
            updateProgress();
        }
    });

    form.addEventListener('submit', function () {
        if (form.checkValidity() && submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menghitung Skor...';
        }
    });

    updateProgress();
});
</script>
@endpush
