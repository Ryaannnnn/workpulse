/**
 * WorkPulse — Auth Logic (Login, Register, Role Switching)
 */

// ── Auth state ─────────────────────────────────────────────────
let selectedRole    = 'clevel';
let currentAuthView = 'landing';
let _regTemp        = {};

// ── View switcher ──────────────────────────────────────────────
function showView(view, push = true) {
  currentAuthView = view;
  ['landing-page','auth-page','dashboard-app'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.classList.remove('active');
  });

  if (view === 'landing') {
    document.getElementById('landing-page')?.classList.add('active');
    if (push && window.location.hash) {
      history.pushState({ view: 'landing' }, '', window.location.pathname);
    }
  } else if (['login','register','register2'].includes(view)) {
    document.getElementById('auth-page')?.classList.add('active');
    renderAuthView(view);
    if (push) {
      history.pushState({ view }, '', '#' + view);
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
  } else if (view === 'dashboard') {
    document.getElementById('dashboard-app')?.classList.add('active');
    if (push) {
      history.pushState({ view: 'dashboard' }, '', '#dashboard');
    }
    if (typeof initDashboard === 'function') initDashboard();
    if (typeof navigate === 'function') navigate('home');
  } else if (view === 'logout') {
    AuthAPI.logout();
    if (typeof AppState !== 'undefined') {
      AppState.currentUser = null;
      AppState.chartsInit = {};
    }
    selectedRole = 'clevel';
    showView('landing', push);
  }
}

// ── Browser Back / Forward Synchronization ─────────────────────
window.addEventListener('popstate', function() {
  const hash = window.location.hash.replace('#', '');
  if (hash === 'login' || hash === 'register' || hash === 'register2') {
    showView(hash, false);
  } else if (hash === 'dashboard' && AuthAPI.isLoggedIn()) {
    showView('dashboard', false);
  } else {
    showView('landing', false);
    if (['fitur', 'pilar', 'metodologi'].includes(hash)) {
      setTimeout(() => {
        document.getElementById(hash)?.scrollIntoView({ behavior: 'smooth' });
      }, 50);
    }
  }
});

// Initial hash routing on page load
window.addEventListener('DOMContentLoaded', function() {
  const hash = window.location.hash.replace('#', '');
  if (['login', 'register'].includes(hash)) {
    showView(hash, false);
  } else if (['fitur', 'pilar', 'metodologi'].includes(hash)) {
    showView('landing', false);
    setTimeout(() => {
      document.getElementById(hash)?.scrollIntoView({ behavior: 'smooth' });
    }, 150);
  }
});

// ── Render form container ──────────────────────────────────────
function renderAuthView(view) {
  const c = document.getElementById('auth-form-container');
  if (!c) return;
  if (view === 'login')     c.innerHTML = renderLoginForm();
  if (view === 'register')  c.innerHTML = renderRegisterForm();
  if (view === 'register2') c.innerHTML = renderRegisterForm2();
}

// ── Role cards HTML ────────────────────────────────────────────
function roleCardsHTML() {
  const isClevel = selectedRole === 'clevel';
  const isStaf   = selectedRole === 'staf';

  return `
    <div class="role-selector">
      <div class="role-card ${isClevel ? 'selected' : ''}" onclick="selectRole('clevel')">
        <div class="role-card-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="${isClevel ? '#fff' : '#64748B'}" stroke-width="2">
            <path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"/>
            <path d="M3 9V7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v2"/>
          </svg>
        </div>
        <div class="role-card-title">C-Level</div>
        <div class="role-card-desc">Founder &amp; Manajemen</div>
      </div>
      <div class="role-card ${isStaf ? 'selected' : ''}" onclick="selectRole('staf')">
        <div class="role-card-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="${isStaf ? '#fff' : '#64748B'}" stroke-width="2">
            <circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/>
          </svg>
        </div>
        <div class="role-card-title">Staf</div>
        <div class="role-card-desc">Karyawan &amp; Engineer</div>
      </div>
    </div>`;
}

// ── Login form ─────────────────────────────────────────────────
function renderLoginForm() {
  return `
    <div class="auth-form-wrap">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
        <button type="button" onclick="showView('landing')" style="display:inline-flex;align-items:center;gap:6px;background:none;border:none;color:#64748B;font-size:13px;font-weight:600;cursor:pointer;padding:6px 0;transition:color 0.2s">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
          Kembali ke Beranda
        </button>
        <div class="auth-header-brand" onclick="showView('landing')" style="cursor:pointer;display:inline-flex;align-items:center;gap:6px;font-size:18px;font-weight:800;color:var(--wp-primary)">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
          WorkPulse
        </div>
      </div>
      <h1 class="auth-title">Masuk ke WorkPulse</h1>
      <p class="auth-subtitle">Akses dasbor asesmen budaya kerja &amp; pemantauan risiko burnout startup Anda.</p>
      ${roleCardsHTML()}
      <div class="form-group">
        <label class="field-label">Email Kerja</label>
        <div class="field-wrap">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <input type="email" id="loginEmail" placeholder="nama@startup.com" autocomplete="email">
        </div>
      </div>
      <div class="form-group">
        <div class="forgot-row">
          <label class="field-label">Kata Sandi</label>
          <a class="forgot-link" onclick="showToast('Tautan atur ulang sandi telah dikirim ke email kerja Anda','success')">Lupa Kata Sandi?</a>
        </div>
        <div class="field-wrap">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input type="password" id="loginPassword" placeholder="••••••••">
          <span class="field-eye" onclick="togglePwd('loginPassword')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </span>
        </div>
      </div>
      <button class="btn-auth" id="btnLogin" onclick="handleLogin()">Masuk Akun</button>
      <p class="auth-link-text">Startup Anda belum terdaftar? <a onclick="showView('register')">Daftarkan di sini</a></p>
    </div>`;
}

// ── Register step 1 ────────────────────────────────────────────
function renderRegisterForm() {
  return `
    <div class="auth-form-wrap">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
        <button type="button" onclick="showView('landing')" style="display:inline-flex;align-items:center;gap:6px;background:none;border:none;color:#64748B;font-size:13px;font-weight:600;cursor:pointer;padding:6px 0;transition:color 0.2s">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
          Kembali ke Beranda
        </button>
        <div class="auth-header-brand" onclick="showView('landing')" style="cursor:pointer;display:inline-flex;align-items:center;gap:6px;font-size:18px;font-weight:800;color:var(--wp-primary)">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
          WorkPulse
        </div>
      </div>
      <h1 class="auth-title">Daftarkan Startup Anda</h1>
      <p class="auth-subtitle">Mulai asesmen risiko kelelahan dan ciptakan budaya startup yang sehat dan berkelanjutan.</p>
      ${roleCardsHTML()}
      <div class="form-group">
        <label class="field-label">Nama Startup</label>
        <div class="field-wrap">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 22 22 7 12 2"/></svg>
          <input type="text" id="regName" placeholder="Contoh: PulseTech Labs" value="${_regTemp.name || ''}">
        </div>
      </div>
      <div class="form-group">
        <label class="field-label">Email Kerja Resmi</label>
        <div class="field-wrap">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <input type="email" id="regEmail" placeholder="founder@pulsetech.id" value="${_regTemp.email || ''}">
        </div>
      </div>
      <div class="form-group-half">
        <div>
          <label class="field-label">Kata Sandi</label>
          <div class="field-wrap">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <input type="password" id="regPwd" placeholder="Min. 8 karakter">
          </div>
        </div>
        <div>
          <label class="field-label">Ulangi Kata Sandi</label>
          <div class="field-wrap">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <input type="password" id="regPwdConfirm" placeholder="Konfirmasi sandi">
          </div>
        </div>
      </div>
      <div class="checkbox-wrap">
        <input type="checkbox" id="regAgree">
        <label class="checkbox-label" for="regAgree">Saya menyetujui <a>Ketentuan Layanan</a> dan <a>Kebijakan Privasi</a> WorkPulse.</label>
      </div>
      <button class="btn-auth" id="btnReg1" onclick="handleRegisterStep1()">Lanjutkan Profil Startup →</button>
      <p class="auth-link-text">Sudah memiliki akun? <a onclick="showView('login')">Masuk di sini</a></p>
    </div>`;
}

// ── Register step 2 ────────────────────────────────────────────
function renderRegisterForm2() {
  return `
    <div class="auth-form-wrap">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
        <button type="button" onclick="showView('register')" style="display:inline-flex;align-items:center;gap:6px;background:none;border:none;color:#64748B;font-size:13px;font-weight:600;cursor:pointer;padding:6px 0;transition:color 0.2s">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
          Langkah Sebelumnya
        </button>
        <div class="auth-header-brand" onclick="showView('landing')" style="cursor:pointer;display:inline-flex;align-items:center;gap:6px;font-size:18px;font-weight:800;color:var(--wp-primary)">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
          WorkPulse
        </div>
      </div>
      <h1 class="auth-title">Profil Ekosistem Startup</h1>
      <p class="auth-subtitle">Informasi ini membantu WorkPulse membandingkan metrik budaya kerja Anda dengan benchmark industri teknologi.</p>
      <div class="form-group">
        <label class="field-label">Nama Startup</label>
        <div class="field-wrap">
          <input type="text" id="reg2Name" placeholder="Nama resmi startup" value="${_regTemp.name || ''}">
        </div>
      </div>
      <div class="form-group-half">
        <div>
          <label class="field-label">Lokasi Kantor / Hub</label>
          <div class="field-wrap">
            <select id="reg2Lokasi">
              <option value="">Pilih Kota / Lokasi</option>
              <option>Jakarta (Jabodetabek)</option>
              <option>Bandung</option>
              <option>Yogyakarta</option>
              <option>Surabaya</option>
              <option>Bali (Tech Nomad Hub)</option>
              <option>Medan</option>
              <option>Semarang</option>
              <option>Malang</option>
              <option>Makassar</option>
              <option>Remote / Terdistribusi Penuh</option>
            </select>
          </div>
        </div>
        <div>
          <label class="field-label">Sektor Teknologi</label>
          <div class="field-wrap">
            <select id="reg2Sektor">
              <option value="">Pilih Sektor Teknologi</option>
              <option>SaaS (Software-as-a-Service)</option>
              <option>FinTech (Financial Technology)</option>
              <option>EdTech (Education Technology)</option>
              <option>HealthTech &amp; BioTech</option>
              <option>AI &amp; Machine Learning</option>
              <option>E-Commerce &amp; Quick-Commerce</option>
              <option>LogisTech &amp; Supply Chain</option>
              <option>AgriTech &amp; FoodTech</option>
              <option>Enterprise Software &amp; Cloud</option>
              <option>Lainnya</option>
            </select>
          </div>
        </div>
      </div>
      <div class="form-group">
        <label class="field-label">Rata-rata Pendanaan (Funding)</label>
        <div class="field-number-wrap">
          <span class="field-prefix">IDR</span>
          <input type="number" id="reg2Omzet" placeholder="0" min="0">
        </div>
        <div class="field-hint">Estimasi total pendanaan eksternal atau akumulasi modal kerja (contoh: 2500000000 untuk 2.5 Milyar).</div>
      </div>
      <button class="btn-auth" id="btnReg2" onclick="handleRegisterStep2()">Selesaikan &amp; Buka Dashboard →</button>
      <p class="auth-link-text"><a onclick="showView('register')">← Kembali ke langkah sebelumnya</a></p>
    </div>`;
}

// ── Select role ────────────────────────────────────────────────
function selectRole(role) {
  selectedRole = role;
  renderAuthView(currentAuthView);
}

function togglePwd(id) {
  const el = document.getElementById(id);
  if (el) el.type = el.type === 'password' ? 'text' : 'password';
}

// ── Set button loading ─────────────────────────────────────────
function setLoading(btnId, loading, text = '') {
  const btn = document.getElementById(btnId);
  if (!btn) return;
  btn.disabled = loading;
  btn.textContent = loading ? 'Memproses data...' : text;
}

// ── HANDLE LOGIN ───────────────────────────────────────────────
async function handleLogin() {
  const email = (document.getElementById('loginEmail')?.value || '').trim();
  const pwd   = (document.getElementById('loginPassword')?.value || '').trim();
  if (!email || !pwd) {
    showToast('Harap lengkapi email kerja dan kata sandi.', 'error');
    return;
  }

  setLoading('btnLogin', true, 'Masuk Akun');
  try {
    const res = await AuthAPI.login({ email, password: pwd, role: selectedRole });
    if (!res.ok) {
      showToast(res.message || 'Login gagal. Periksa kembali kredensial Anda.', 'error');
      setLoading('btnLogin', false, 'Masuk Akun');
      return;
    }

    const safeName = res.data?.startup_name || res.data?.umkm_name || res.data?.email?.split('@')[0] || 'Startup User';
    AppState.currentUser = {
      name:             safeName,
      email:            res.data?.email            || email,
      role:             res.data?.role             || selectedRole,
      user_id:          res.data?.user_id,
      startup_name:     safeName,
      umkm_name:        safeName,
      lokasi:           res.data?.lokasi           || '',
      sektor_teknologi: res.data?.sektor_teknologi || res.data?.sektor || '',
      pendanaan:        res.data?.pendanaan        ?? res.data?.omzet ?? null,
    };

    showToast(`Login berhasil! Selamat datang di WorkPulse, ${safeName}.`, 'success');
    setTimeout(() => showView('dashboard'), 400);
  } catch(e) {
    showToast('Koneksi ke server WorkPulse terputus.', 'error');
    setLoading('btnLogin', false, 'Masuk Akun');
  }
}

// ── HANDLE REGISTER STEP 1 ─────────────────────────────────────
async function handleRegisterStep1() {
  const name  = (document.getElementById('regName')?.value  || '').trim();
  const email = (document.getElementById('regEmail')?.value || '').trim();
  const pwd   = (document.getElementById('regPwd')?.value   || '').trim();
  const pwdC  = (document.getElementById('regPwdConfirm')?.value || '').trim();
  const agree = document.getElementById('regAgree')?.checked;

  if (!name)  { showToast('Nama startup wajib diisi.', 'error'); return; }
  if (!email || !email.includes('@')) { showToast('Format email kerja tidak valid.', 'error'); return; }
  if (pwd.length < 8) { showToast('Kata sandi minimal 8 karakter.', 'error'); return; }
  if (pwd !== pwdC)   { showToast('Konfirmasi kata sandi tidak cocok.', 'error'); return; }
  if (!agree)         { showToast('Harap setujui syarat & ketentuan layanan.', 'error'); return; }

  _regTemp = { name, email, pwd, role: selectedRole };
  showView('register2');
}

// ── HANDLE REGISTER STEP 2 ────────────────────────────────────
async function handleRegisterStep2() {
  const lokasi = document.getElementById('reg2Lokasi')?.value;
  const sektor = document.getElementById('reg2Sektor')?.value;
  const omzet  = document.getElementById('reg2Omzet')?.value;
  const nameField = (document.getElementById('reg2Name')?.value || '').trim();
  const finalName = nameField || _regTemp.name || '';

  if (!finalName) { showToast('Nama startup wajib diisi.', 'error'); return; }
  if (!lokasi)    { showToast('Pilih lokasi kantor / hub startup.', 'error'); return; }
  if (!sektor)    { showToast('Pilih sektor industri teknologi.', 'error'); return; }

  setLoading('btnReg2', true, 'Selesaikan & Buka Dashboard →');
  try {
    const res = await AuthAPI.register({
      name:             finalName,
      startup_name:     finalName,
      email:            _regTemp.email,
      password:         _regTemp.pwd,
      role:             _regTemp.role || selectedRole,
      lokasi,
      sektor_teknologi: sektor,
      pendanaan:        omzet ? parseInt(omzet) : null,
    });

    if (!res.ok) {
      showToast(res.message || 'Pendaftaran startup gagal.', 'error');
      setLoading('btnReg2', false, 'Selesaikan & Buka Dashboard →');
      return;
    }

    const savedName = res.data?.startup_name || finalName || _regTemp.email?.split('@')[0] || 'Startup Member';

    AppState.currentUser = {
      name:             savedName,
      email:            res.data?.email            || _regTemp.email,
      role:             res.data?.role             || _regTemp.role,
      user_id:          res.data?.user_id,
      startup_name:     savedName,
      umkm_name:        savedName,
      lokasi:           res.data?.lokasi           || lokasi || '',
      sektor_teknologi: res.data?.sektor_teknologi || sektor || '',
      pendanaan:        res.data?.pendanaan        ?? (omzet ? parseInt(omzet) : null),
    };
    _regTemp = {};
    showToast(`Registrasi berhasil! Selamat datang di WorkPulse, ${savedName}.`, 'success');
    setTimeout(() => showView('dashboard'), 400);
  } catch(e) {
    showToast('Terjadi kendala saat menghubungi server WorkPulse.', 'error');
    setLoading('btnReg2', false, 'Selesaikan & Buka Dashboard →');
  }
}

// ── Check session on load ─────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  const user = AuthAPI.getUser();
  if (user && AuthAPI.isLoggedIn()) {
    const safeName = user.startup_name || user.umkm_name || user.name || user.email?.split('@')[0] || 'Pengguna';
    let role = user.role || 'clevel';
    if (role === 'owner') role = 'clevel';
    if (role === 'employee') role = 'staf';

    AppState.currentUser = {
      name:             safeName,
      email:            user.email            || '',
      role:             role,
      user_id:          user.user_id,
      startup_name:     safeName,
      umkm_name:        safeName,
      lokasi:           user.lokasi           || '',
      sektor_teknologi: user.sektor_teknologi || user.sektor || '',
      pendanaan:        user.pendanaan        ?? user.omzet ?? null,
    };
    showView('dashboard');
  } else {
    showView('landing');
  }
});
