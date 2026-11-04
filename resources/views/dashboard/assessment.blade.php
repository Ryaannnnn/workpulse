@extends('layouts.app')

@section('title', 'Formulir Asesmen Budaya Kerja – WorkPulse')

@section('content')
<div style="min-height:100vh;background:#F8FAFC;padding:40px 24px">
  <div style="max-width:880px;margin:0 auto">

    <!-- Header Navigation -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px">
      <a href="{{ route('dashboard') }}" style="display:inline-flex;align-items:center;gap:6px;color:#64748B;font-size:13.5px;font-weight:600;text-decoration:none">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        Kembali ke Dashboard
      </a>
      <div style="font-size:12px;font-weight:700;background:#EEF2FF;color:#4F46E5;padding:6px 14px;border-radius:20px;border:1px solid #C7D2FE">
        Asesmen Mode: {{ $user->role === 'clevel' ? '👔 C-Level / Founder' : '💻 Staf / Karyawan' }}
      </div>
    </div>

    <!-- Title Card -->
    <div class="card" style="padding:32px;margin-bottom:28px;background:#FFFFFF;border-radius:16px;border:1px solid #E2E8F0;box-shadow:var(--shadow-sm)">
      <h1 style="font-size:26px;font-weight:800;color:#0F172A;letter-spacing:-0.02em;margin-bottom:8px">Asesmen Budaya Kerja &amp; Burnout Risk</h1>
      <p style="font-size:14px;color:#64748B;line-height:1.6">
        Jawablah pertanyaan berikut secara objektif sesuai kondisi riil di startup <strong>{{ $user->startup_name }}</strong>. 
        Penilaian menggunakan skala Likert 1 (Sangat Tidak Setuju) hingga 5 (Sangat Setuju).
      </p>
    </div>

    @if ($errors->any())
      <div style="background:#FEF2F2;border:1px solid #FCA5A5;color:#991B1B;padding:16px;border-radius:12px;margin-bottom:24px;font-size:13.5px">
        <strong>Perhatian:</strong>
        <ul style="margin:6px 0 0 18px;padding:0">
          @foreach ($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('assessment.submit') }}">
      @csrf

      @foreach ($groupedQuestions as $factorCode => $group)
        <div class="card" style="padding:28px;margin-bottom:24px;background:#FFFFFF;border-radius:16px;border:1px solid #E2E8F0;box-shadow:var(--shadow-sm)">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;border-bottom:1px solid #F1F5F9;padding-bottom:12px">
            <span style="font-size:12px;font-weight:800;background:#EEF2FF;color:#6366F1;padding:4px 10px;border-radius:8px">
              Pilar {{ $factorCode }}
            </span>
            <h3 style="font-size:17px;font-weight:700;color:#0F172A">
              {{ $group['factor_name'] ?? 'Indikator Pilar' }}
            </h3>
          </div>

          <div style="display:flex;flex-direction:column;gap:22px">
            @foreach ($group['items'] as $q)
              <div style="padding-bottom:18px;border-bottom:1px dashed #F1F5F9">
                <div style="font-size:14px;font-weight:600;color:#1E293B;margin-bottom:12px;line-height:1.5">
                  {{ $loop->iteration }}. {{ $q->text }}
                </div>
                
                <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
                  <span style="font-size:12px;color:#94A3B8;margin-right:4px">STS</span>
                  @for ($i = 1; $i <= 5; $i++)
                    <label style="cursor:pointer;display:inline-flex;align-items:center">
                      <input type="radio" 
                             name="answers[{{ $factorCode }}][{{ $q->question_index }}]" 
                             value="{{ $i }}" 
                             required
                             style="display:none" 
                             onchange="highlightScale(this)">
                      <div class="scale-btn" style="width:38px;height:38px;border:1px solid #CBD5E1;border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#475569;background:#FFFFFF;transition:all 0.15s">
                        {{ $i }}
                      </div>
                    </label>
                  @endfor
                  <span style="font-size:12px;color:#94A3B8;margin-left:4px">SS</span>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endforeach

      <!-- Submit Section -->
      <div style="display:flex;justify-content:flex-end;gap:14px;margin-top:32px">
        <a href="{{ route('dashboard') }}" class="btn-secondary" style="text-decoration:none;padding:12px 24px;font-size:14px">Batal</a>
        <button type="submit" class="btn-primary" style="padding:12px 32px;font-size:14px">
          Kirim &amp; Hitung Skor Asesmen
        </button>
      </div>
    </form>

  </div>
</div>

<script>
function highlightScale(radio) {
  const parent = radio.closest('div');
  parent.querySelectorAll('.scale-btn').forEach(btn => {
    btn.style.background = '#FFFFFF';
    btn.style.borderColor = '#CBD5E1';
    btn.style.color = '#475569';
  });
  const currentBtn = radio.nextElementSibling;
  currentBtn.style.background = '#6366F1';
  currentBtn.style.borderColor = '#6366F1';
  currentBtn.style.color = '#FFFFFF';
}
</script>
@endsection

