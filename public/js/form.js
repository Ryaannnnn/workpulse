/**
 * WorkPulse — Questionnaire Form & Realtime Assessment Logic
 * 6 Core Factors of Startup Health & Burnout Risk
 */

const CLEVEL_QUESTIONS = {
  VT: {
    key: 'VT',
    label: 'Visi & Transparansi (VT)',
    color: '#6366F1',
    bgColor: '#EEF2FF',
    icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6366F1" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>`,
    description: 'Kejelasan arah strategis, komunikasi runway, dan transparansi keputusan founder.',
    type: 'likert5',
    maxVal: 5,
    questions: [
      { id: 'vt_wp1', text: 'Founder dan jajaran C-Level mengomunikasikan arah strategis dan runway perusahaan secara transparan kepada tim.' },
      { id: 'vt_wp2', text: 'Setiap divisi memiliki target OKR/KPI yang terukur, realistis, dan selaras dengan visi jangka panjang startup.' },
      { id: 'vt_wp3', text: 'Perubahan prioritas bisnis atau pivot strategi disampaikan dengan alasan yang jelas dan terbuka.' },
      { id: 'vt_wp4', text: 'Manajemen menerapkan kebijakan keterbukaan metrik performa produk dan pertumbuhan bisnis secara berkala.' },
      { id: 'vt_wp5', text: 'Keputusan penting organisasi diambil melalui pertimbangan data yang objektif dan transparan bagi pemangku kepentingan.' },
      { id: 'vt_wp6', text: 'Karyawan di seluruh tingkatan memahami peran kontribusi kerja mereka terhadap pencapaian misi startup.' },
    ]
  },
  FI: {
    key: 'FI',
    label: 'Fasilitas & Infrastruktur (FI)',
    color: '#0EA5E9',
    bgColor: '#E0F2FE',
    icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0EA5E9" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>`,
    description: 'Kecukupan hardware, lisensi software/cloud, stabilitas CI/CD, dan fasilitas kerja.',
    type: 'likert5',
    maxVal: 5,
    questions: [
      { id: 'fi_wp1', text: 'Perusahaan menyediakan perangkat kerja (laptop/hardware) dengan spesifikasi yang memadai untuk kebutuhan tim.' },
      { id: 'fi_wp2', text: 'Perusahaan berinvestasi pada lisensi perangkat lunak, cloud platform, dan tooling modern pendukung produktivitas.' },
      { id: 'fi_wp3', text: 'Infrastruktur teknologi dan sistem dokumentasi pengetahuan (knowledge base) internal tertata dengan rapi.' },
      { id: 'fi_wp4', text: 'Lingkungan kerja (kantor fisik maupun tunjangan remote/hybrid) dirancang ergonomis dan nyaman untuk bekerja.' },
      { id: 'fi_wp5', text: 'Tersedia alokasi anggaran yang cukup untuk keamanan data, compliance, dan kesinambungan infrastruktur teknologi.' },
      { id: 'fi_wp6', text: 'Sistem otomatisasi alur kerja (CI/CD, project management tools) membantu meminimalkan beban tugas manual yang berulang.' },
    ]
  },
  KP: {
    key: 'KP',
    label: 'Kompensasi & Karir (KP)',
    color: '#F59E0B',
    bgColor: '#FEF3C7',
    icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>`,
    description: 'Daya saing kompensasi, transparansi insentif/ESOP, dan kejelasan jenjang karir tim.',
    type: 'likert5',
    maxVal: 5,
    questions: [
      { id: 'kp_wp1', text: 'Struktur gaji pokok dan tunjangan di startup ini kompetitif jika dibandingkan dengan standar industri teknologi sejenis.' },
      { id: 'kp_wp2', text: 'Perusahaan memiliki skema kepemilikan saham (ESOP) atau bonus kinerja yang terstruktur secara transparan.' },
      { id: 'kp_wp3', text: 'Tersedia jalur jenjang karir (career ladder) dan kriteria evaluasi promosi yang terdefinisi dengan jelas.' },
      { id: 'kp_wp4', text: 'Perusahaan mengalokasikan anggaran untuk pelatihan, sertifikasi teknis, atau program peningkatan keahlian tim.' },
      { id: 'kp_wp5', text: 'Peninjauan kompensasi berkala dilakukan secara objektif berdasarkan kinerja dan kontribusi nyata individu.' },
      { id: 'kp_wp6', text: 'Manajemen memiliki strategi retensi talenta yang baik untuk mencegah tingkat pergantian karyawan (turnover) tinggi.' },
    ]
  }
};

const STAF_QUESTIONS = {
  KK: {
    key: 'KK',
    label: 'Kualitas Kepemimpinan (KK)',
    color: '#10B981',
    bgColor: '#D1FAE5',
    icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>`,
    description: 'Empati manajerial, bimbingan objektif, dan keterbukaan pimpinan menerima masukan staf.',
    type: 'likert5',
    maxVal: 5,
    questions: [
      { id: 'kk_wp1', text: 'Pimpinan langsung saya memberikan arahan kerja yang jelas tanpa melakukan micromanagement berlebihan.' },
      { id: 'kk_wp2', text: 'Pimpinan bersikap suportif dan mendengarkan kendala operasional yang dihadapi anggota tim.' },
      { id: 'kk_wp3', text: 'Pimpinan memberikan apresiasi dan pengakuan yang layak atas kontribusi serta keberhasilan anggota tim.' },
      { id: 'kk_wp4', text: 'Pimpinan mampu mengelola konflik tim secara objektif, adil, dan tanpa perlakuan pilih kasih.' },
      { id: 'kk_wp5', text: 'Pimpinan terbuka menerima kritik membangun serta saran inovasi dari staf teknis.' },
      { id: 'kk_wp6', text: 'Pimpinan menunjukkan keteladanan profesional yang menginspirasi rasa percaya dan loyalitas staf.' },
    ]
  },
  BK: {
    key: 'BK',
    label: 'Beban Kerja (Workload) (BK)',
    color: '#F43F5E',
    bgColor: '#FFE4E6',
    icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#F43F5E" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>`,
    description: 'Volume sprint mingguan, mitigasi lembur kronis, dan keseimbangan ritme kerja.',
    type: 'likert5',
    maxVal: 5,
    questions: [
      { id: 'bk_wp1', text: 'Volume tugas dan beban sprint mingguan dapat diselesaikan dalam jam kerja normal tanpa tuntutan lembur terus-menerus.' },
      { id: 'bk_wp2', text: 'Tenggat waktu (deadline) proyek direncanakan dengan mempertimbangkan kapasitas riil kapasitas tim pengembang.' },
      { id: 'bk_wp3', text: 'Saya jarang merasa dituntut untuk merespons pesan kerja darurat di luar jam kerja resmi atau saat akhir pekan.' },
      { id: 'bk_wp4', text: 'Distribusi pekerjaan di dalam tim berlangsung seimbang dan tidak terpusat pada segelintir individu kunci saja.' },
      { id: 'bk_wp5', text: 'Saya memiliki waktu istirahat yang cukup untuk pulih kembali setelah fase sprint atau perilisan fitur besar.' },
      { id: 'bk_wp6', text: 'Beban multi-tasking dalam menangani beberapa proyek sekaligus tidak menimbulkan stres ekstrem yang mengganggu konsentrasi.' },
    ]
  },
  KM: {
    key: 'KM',
    label: 'Kesejahteraan Mental (KM)',
    color: '#8B5CF6',
    bgColor: '#EDE9FE',
    icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#8B5CF6" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>`,
    description: 'Psychological safety, iklim kerja bebas toxic, dan perlindungan dari kelelahan mental.',
    type: 'likert5',
    maxVal: 5,
    questions: [
      { id: 'km_wp1', text: 'Saya merasa aman secara psikologis untuk mengakui kesalahan atau bertanya tanpa khawatir dihakimi atau disalahkan.' },
      { id: 'km_wp2', text: 'Lingkungan kerja startup kami bebas dari budaya toxic, intimidasi, maupun tekanan emosional negatif.' },
      { id: 'km_wp3', text: 'Saya tidak merasakan gejala kelelahan fisik dan mental (exhaustion) yang berkepanjangan akibat pekerjaan.' },
      { id: 'km_wp4', text: 'Rekan-rekan kerja saling mendukung secara empati saat ada anggota tim yang mengalami tekanan pekerjaan berat.' },
      { id: 'km_wp5', text: 'Perusahaan peduli terhadap kesehatan mental staf dan menyediakan ruang atau inisiatif kesejahteraan emosional.' },
      { id: 'km_wp6', text: 'Saya tetap merasa antusias dan termotivasi saat memulai rutinitas kerja harian di startup ini.' },
    ]
  }
};

// ── Form State & Helpers ───────────────────────────────────────
let _formStartTime = null;

function getCurrentRole() {
  const r = AppState?.currentUser?.role || 'clevel';
  return (r === 'staf' || r === 'employee') ? 'staf' : 'clevel';
}

function getActiveGroups() {
  const r = getCurrentRole();
  return r === 'staf'
    ? [STAF_QUESTIONS.KK, STAF_QUESTIONS.BK, STAF_QUESTIONS.KM]
    : [CLEVEL_QUESTIONS.VT, CLEVEL_QUESTIONS.FI, CLEVEL_QUESTIONS.KP];
}

// ── Build sidebar calculation indicators ───────────────────────
function buildCalcRows() {
  const c = document.getElementById('calcRows');
  if (!c) return;

  c.innerHTML = getActiveGroups().map(g => `
    <div style="margin-bottom:14px">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
        <span style="font-size:12px;font-weight:600;color:var(--gray-600)">${g.label.split(' (')[0]}</span>
        <span style="font-size:13px;font-weight:700;color:${g.color}" id="calc-${g.key}">--</span>
      </div>
      <div style="height:6px;background:var(--gray-100);border-radius:99px;overflow:hidden">
        <div id="bar-${g.key}" style="height:100%;width:0%;background:${g.color};border-radius:99px;transition:width 0.4s ease"></div>
      </div>
    </div>`).join('') + `
    <div style="border-top:1px solid var(--gray-200);padding-top:14px;margin-top:12px;display:flex;justify-content:space-between;align-items:center">
      <span style="font-size:12px;font-weight:600;color:var(--gray-700)">Indeks Budaya Rata-rata</span>
      <span style="font-size:18px;font-weight:800;color:var(--primary)" id="calc-avg">--</span>
    </div>`;
}

// ── Build question sections ────────────────────────────────────
function buildFormSections() {
  const c = document.getElementById('formMainContent');
  if (!c) return;
  const role = getCurrentRole();
  const groups = getActiveGroups();

  const isClevel = role === 'clevel';
  const roleBadgeText = isClevel ? '👔 Kuesioner C-Level &amp; Founder' : '👤 Kuesioner Staf &amp; Engineer';
  const roleDesc = isClevel
    ? 'Fokus evaluasi: Visi & Transparansi, Fasilitas & Infrastruktur, serta Kompensasi & Karir.'
    : 'Fokus evaluasi: Kualitas Kepemimpinan, Beban Kerja (Workload), dan Kesejahteraan Mental.';

  let html = `
    <div style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:12px;padding:16px 20px;display:flex;align-items:center;gap:14px;margin-bottom:20px">
      <div style="width:40px;height:40px;border-radius:10px;background:var(--primary);display:flex;align-items:center;justify-content:center;color:#fff;flex-shrink:0">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      </div>
      <div>
        <div style="font-size:14px;font-weight:700;color:var(--gray-900)">${roleBadgeText}</div>
        <div style="font-size:12px;color:var(--gray-500);margin-top:2px">${roleDesc} Jawaban Anda dijamin anonim dan dievaluasi secara agregat.</div>
      </div>
    </div>`;

  groups.forEach(g => {
    html += buildSectionHTML(g);
  });
  c.innerHTML = html;

  c.querySelectorAll('input[type="radio"]').forEach(r => {
    r.addEventListener('change', updateLiveCalc);
  });

  c.addEventListener('change', () => {
    if (!_formStartTime) _formStartTime = Date.now();
  }, { once: true });
}

function buildSectionHTML(cfg) {
  const likertHeader = `
    <div style="display:flex;justify-content:flex-end;margin-bottom:8px;padding-bottom:10px;border-bottom:1px solid var(--gray-100)">
      <div style="display:flex;align-items:center;gap:14px;font-size:11px;font-weight:600;color:var(--gray-400)">
        <span style="margin-right:6px">Sangat Tidak Setuju (1)</span>
        ${[1,2,3,4,5].map(v => `<span style="width:26px;text-align:center">${v}</span>`).join('')}
        <span style="margin-left:6px">Sangat Setuju (5)</span>
      </div>
    </div>`;

  const rows = cfg.questions.map((q, i) => buildRow(q, i)).join('');

  return `
    <div class="form-section" style="border-top:3px solid ${cfg.color};margin-bottom:24px">
      <div class="section-header" style="margin-bottom:14px">
        <div class="section-icon" style="background:${cfg.bgColor}">${cfg.icon}</div>
        <div>
          <div class="section-title" style="color:var(--gray-900)">${cfg.label}</div>
          <div class="section-sub" style="color:var(--gray-500)">${cfg.description}</div>
        </div>
      </div>
      ${likertHeader}
      ${rows}
    </div>`;
}

function buildRow(q, idx) {
  return `
    <div class="form-row" style="display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid #F1F5F9;gap:16px">
      <div class="form-label" style="font-size:13px;color:var(--gray-800);line-height:1.5;flex:1">
        <span style="font-weight:700;color:var(--gray-400);margin-right:6px">${idx + 1}.</span>${q.text}
      </div>
      <div style="display:flex;gap:14px;flex-shrink:0;align-items:center">
        ${[1,2,3,4,5].map(v => `
          <label style="cursor:pointer;display:flex;flex-direction:column;align-items:center;position:relative">
            <input type="radio" name="${q.id}" value="${v}" style="position:absolute;opacity:0;width:0;height:0">
            <span class="likert-dot" data-name="${q.id}" data-val="${v}"
              style="width:26px;height:26px;border-radius:50%;border:1.5px solid #CBD5E1;background:#fff;
                     display:flex;align-items:center;justify-content:center;transition:all 0.15s ease;cursor:pointer;font-size:11px;font-weight:600;color:#64748B">
              ${v}
            </span>
          </label>`).join('')}
      </div>
    </div>`;
}

// ── Interactive Likert Radio Click Handler ─────────────────────
document.addEventListener('click', e => {
  const dot = e.target.closest('.likert-dot');
  if (!dot) return;
  const name = dot.dataset.name;
  const val  = dot.dataset.val;
  if (!name || !val) return;

  document.querySelectorAll(`.likert-dot[data-name="${name}"]`).forEach(d => {
    const isSelected = d.dataset.val === val;
    if (isSelected) {
      d.style.background  = 'var(--primary)';
      d.style.borderColor = 'var(--primary)';
      d.style.color       = '#FFFFFF';
      d.style.boxShadow   = '0 0 0 3px rgba(99, 102, 241, 0.2)';
    } else {
      d.style.background  = '#FFFFFF';
      d.style.borderColor = '#CBD5E1';
      d.style.color       = '#64748B';
      d.style.boxShadow   = 'none';
    }
  });

  const radio = document.querySelector(`input[name="${name}"][value="${val}"]`);
  if (radio) {
    radio.checked = true;
    radio.dispatchEvent(new Event('change', { bubbles: true }));
  }
  if (!_formStartTime) _formStartTime = Date.now();
});

// ── Realtime Assessment Calculation ────────────────────────────
function updateLiveCalc() {
  const groups = getActiveGroups();
  let totalNorm = 0, countGroups = 0;

  groups.forEach(g => {
    const ve = document.getElementById(`calc-${g.key}`);
    const be = document.getElementById(`bar-${g.key}`);
    if (!ve || !be) return;

    const vals = g.questions.map(q => {
      const el = document.querySelector(`input[name="${q.id}"]:checked`);
      return el ? parseFloat(el.value) : null;
    }).filter(v => v !== null);

    if (!vals.length) {
      ve.textContent = '--';
      be.style.width = '0%';
      return;
    }

    const avg = vals.reduce((a, b) => a + b, 0) / vals.length;
    const pct = Math.round((avg / g.maxVal) * 100);
    ve.textContent = avg.toFixed(2);

    const col = pct >= 80 ? '#10B981' : pct >= 60 ? '#3B82F6' : pct >= 40 ? '#F59E0B' : '#EF4444';
    ve.style.color = col;
    be.style.width = pct + '%';
    be.style.background = col;

    totalNorm += (avg / g.maxVal);
    countGroups++;
  });

  const ae = document.getElementById('calc-avg');
  const ce = document.getElementById('health-cat');
  const se = document.getElementById('health-sub');

  if (!countGroups) {
    if (ae) ae.textContent = '--';
    if (ce) { ce.textContent = '--'; ce.style.color = '#94A3B8'; }
    return;
  }

  const norm = totalNorm / countGroups;
  const disp = (norm * 5).toFixed(2);
  if (ae) ae.textContent = disp;

  let cat = 'Risiko Burnout Tinggi', catCol = '#EF4444';
  if (norm >= 0.80) {
    cat = 'Budaya Kerja Positif';
    catCol = '#10B981';
  } else if (norm >= 0.60) {
    cat = 'Kondisi Stabil';
    catCol = '#3B82F6';
  } else if (norm >= 0.40) {
    cat = 'Waspada Gejala Burnout';
    catCol = '#F59E0B';
  }

  if (ce) {
    ce.textContent = cat;
    ce.style.color = catCol;
  }
  if (se) {
    se.textContent = `Skor Indeks: ${disp} / 5.0`;
  }
}

// ── Collect Answers ────────────────────────────────────────────
function collectAnswers() {
  const out = {};
  getActiveGroups().forEach(g => {
    g.questions.forEach(q => {
      const el = document.querySelector(`input[name="${q.id}"]:checked`);
      if (el) out[q.id] = parseInt(el.value);
    });
  });
  return out;
}

function countUnanswered() {
  let count = 0;
  getActiveGroups().forEach(g => {
    g.questions.forEach(q => {
      const el = document.querySelector(`input[name="${q.id}"]:checked`);
      if (!el) count++;
    });
  });
  return count;
}

function formatDuration(ms) {
  const s = Math.floor(ms / 1000);
  if (s < 60) return `${s} detik`;
  const m = Math.floor(s / 60), rem = s % 60;
  return rem > 0 ? `${m} menit ${rem} detik` : `${m} menit`;
}

// ── Submit Assessment to Backend ───────────────────────────────
async function saveEvaluasi() {
  const unanswered = countUnanswered();
  if (unanswered > 0) {
    showToast(`Masih ada ${unanswered} indikator belum dinilai. Harap lengkapi semua poin.`, 'error');
    return;
  }

  const btn = document.getElementById('btnSaveEval');
  if (btn) {
    btn.disabled = true;
    btn.textContent = 'Menganalisis & Menyimpan...';
  }

  const answers = collectAnswers();
  const elapsed = _formStartTime ? Date.now() - _formStartTime : 0;

  try {
    const res = await FormAPI.submit(answers);
    if (btn) {
      btn.disabled = false;
      btn.textContent = 'Simpan Evaluasi';
    }

    if (!res.ok) {
      let errMsg = res.message || 'Gagal menyimpan evaluasi.';
      if (res.data?.errors?.length) {
        const factorErrors = res.data.errors.filter(e => e.factor);
        if (factorErrors.length) {
          errMsg = `${factorErrors.length} indikator tidak valid:\n` +
            factorErrors.slice(0, 3).map(e => `• ${e.message}`).join('\n');
        }
      }
      showToast(errMsg, 'error');
      return;
    }

    // Refresh database & UI
    if (typeof loadStartupData === 'function') {
      await loadStartupData();
    }
    if (typeof refreshAllCharts === 'function') {
      refreshAllCharts();
    }

    showFeedbackPopup(res.data, elapsed);
    _formStartTime = null;

  } catch(e) {
    if (btn) {
      btn.disabled = false;
      btn.textContent = 'Simpan Evaluasi';
    }
    showToast('Terjadi kendala saat menghubungi server WorkPulse.', 'error');
  }
}

// ── Feedback Popup Modal ───────────────────────────────────────
function showFeedbackPopup(data, elapsed) {
  const composite = data?.composite || {};
  const scores    = data?.scores || {};
  const duration  = formatDuration(elapsed);

  const factorLabels = {
    VT: 'Visi & Transparansi',
    KK: 'Kualitas Kepemimpinan',
    FI: 'Fasilitas & Infrastruktur',
    BK: 'Beban Kerja (Workload)',
    KM: 'Kesejahteraan Mental',
    KP: 'Kompensasi & Karir'
  };

  const factorColors = {
    VT: '#6366F1',
    KK: '#10B981',
    FI: '#0EA5E9',
    BK: '#F43F5E',
    KM: '#8B5CF6',
    KP: '#F59E0B'
  };

  const factorRows = Object.entries(scores).map(([k, s]) => {
    const pct = s.normScore ? Math.round(s.normScore * 100) : 0;
    const c   = factorColors[k] || 'var(--primary)';
    return `
      <div style="margin-bottom:12px">
        <div style="display:flex;justify-content:space-between;margin-bottom:4px">
          <span style="font-size:12px;font-weight:600;color:#334155">${factorLabels[k] || k}</span>
          <span style="font-size:12px;font-weight:700;color:${c}">${s.scoreOn5 || '--'} / 5.0</span>
        </div>
        <div style="height:6px;background:#F1F5F9;border-radius:99px;overflow:hidden">
          <div style="height:100%;width:${pct}%;background:${c};border-radius:99px;transition:width 0.8s ease"></div>
        </div>
      </div>`;
  }).join('');

  const catColor = composite.category === 'Budaya Kerja Positif' ? '#10B981'
                 : composite.category === 'Kondisi Stabil'       ? '#3B82F6'
                 : composite.category === 'Waspada Gejala Burnout' ? '#F59E0B'
                 : '#EF4444';

  const popup = document.createElement('div');
  popup.id = 'feedbackPopup';
  popup.style.cssText = 'position:fixed;inset:0;background:rgba(15,23,42,0.6);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;animation:wpFadeIn 0.25s ease';
  popup.innerHTML = `
    <div style="background:#FFFFFF;border-radius:16px;padding:32px;max-width:500px;width:100%;box-shadow:0 20px 40px -15px rgba(0,0,0,0.15);animation:wpSlideUp 0.3s cubic-bezier(0.16,1,0.3,1);border:1px solid #E2E8F0">
      
      <div style="text-align:center;margin-bottom:20px">
        <div style="width:64px;height:64px;border-radius:50%;background:#ECFDF5;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;color:#10B981">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <h2 style="font-size:20px;font-weight:800;color:#0F172A;margin-bottom:4px">Evaluasi Berhasil Divalidasi</h2>
        <p style="font-size:13px;color:#64748B">Data penilaian budaya kerja tersimpan aman di database WorkPulse</p>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px">
        <div style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:12px;padding:12px;text-align:center">
          <div style="font-size:11px;font-weight:600;color:#64748B;margin-bottom:2px">WAKTU PENGERJAAN</div>
          <div style="font-size:16px;font-weight:800;color:#0F172A">${duration}</div>
        </div>
        <div style="background:${catColor}15;border:1px solid ${catColor}40;border-radius:12px;padding:12px;text-align:center">
          <div style="font-size:11px;font-weight:600;color:${catColor};margin-bottom:2px">STATUS BUDAYA</div>
          <div style="font-size:14px;font-weight:800;color:${catColor}">${composite.category || '--'}</div>
        </div>
      </div>

      <div style="background:linear-gradient(135deg,#4F46E5,#6366F1);border-radius:12px;padding:18px;margin-bottom:20px;text-align:center;color:#FFFFFF">
        <div style="font-size:11px;font-weight:700;letter-spacing:0.05em;opacity:0.85;margin-bottom:4px">SKOR INDEKS BUDAYA KERJA</div>
        <div style="font-size:36px;font-weight:800;line-height:1">${composite.scoreOn5 || '--'} <span style="font-size:16px;opacity:0.75">/ 5.0</span></div>
      </div>

      <div style="margin-bottom:20px">
        ${factorRows}
      </div>

      ${data?.warnings?.length ? `
        <div style="background:#FEF3C7;border:1px solid #FDE68A;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:12px;color:#92400E">
          <strong>Catatan Sistem:</strong> ${data.warnings[0]?.message || ''}
        </div>` : ''}

      <div style="font-size:11px;color:#94A3B8;text-align:center;margin-bottom:18px">ID Asesmen Klinis: #${data?.submission_id || '--'}</div>

      <div style="display:flex;gap:10px">
        <button onclick="document.getElementById('feedbackPopup').remove();navigate('home')"
          style="flex:1;padding:12px;background:#6366F1;border:none;border-radius:10px;color:#fff;font-weight:700;font-size:14px;cursor:pointer;font-family:inherit">
          Buka Dashboard
        </button>
        <button onclick="document.getElementById('feedbackPopup').remove()"
          style="padding:12px 18px;background:#fff;border:1px solid #E2E8F0;border-radius:10px;color:#475569;font-weight:600;font-size:14px;cursor:pointer;font-family:inherit">
          Tutup
        </button>
      </div>
    </div>
    <style>
      @keyframes wpFadeIn{from{opacity:0}to{opacity:1}}
      @keyframes wpSlideUp{from{opacity:0;transform:translateY(24px) scale(0.96)}to{opacity:1;transform:translateY(0) scale(1)}}
    </style>`;

  document.body.appendChild(popup);
  popup.addEventListener('click', e => {
    if (e.target === popup) popup.remove();
  });
}

function resetAndBuildForm() {
  _formStartTime = null;
  buildCalcRows();
  buildFormSections();
  updateLiveCalc();
}
