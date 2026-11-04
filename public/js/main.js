/**
 * WorkPulse — Main Application Controller & Visual Analytics
 * Clinical-grade Work Culture and Burnout Risk Assessment Platform
 */

// ═══════════════════════════════════════════════════════════════
//  GLOBAL STATE
// ═══════════════════════════════════════════════════════════════
const AppState = {
  sidebarOpen:  true,
  currentUser:  null,
  chartsInit:   {},
  startupData:  [],
  filteredData: [],
  currentPage:  1,
  pageSize:     6,
  activeFilter: 'semua',

  // Total populasi startup terdaftar
  get totalStartup() {
    const dbCount = (this.startupData || []).filter(x => x.isNew).length;
    return 120 + dbCount;
  },
  // Backward compatibility alias
  get totalUmkm() {
    return this.totalStartup;
  },
  get umkmData() {
    return this.startupData;
  },
  set umkmData(v) {
    this.startupData = v;
  }
};

// ═══════════════════════════════════════════════════════════════
//  SIDEBAR TOGGLE
// ═══════════════════════════════════════════════════════════════
function toggleSidebar() {
  AppState.sidebarOpen = !AppState.sidebarOpen;
  const sb = document.getElementById('sidebar');
  const tb = document.getElementById('topbar');
  const mn = document.getElementById('main');
  const ov = document.getElementById('sidebarOverlay');

  if (AppState.sidebarOpen) {
    sb?.classList.remove('collapsed');
    tb?.classList.remove('sidebar-closed');
    mn?.classList.remove('sidebar-closed');
    ov?.classList.remove('show');
  } else {
    sb?.classList.add('collapsed');
    tb?.classList.add('sidebar-closed');
    mn?.classList.add('sidebar-closed');
    if (window.innerWidth < 900) ov?.classList.add('show');
  }
}

function closeSidebar() {
  AppState.sidebarOpen = false;
  document.getElementById('sidebar')?.classList.add('collapsed');
  document.getElementById('topbar')?.classList.add('sidebar-closed');
  document.getElementById('main')?.classList.add('sidebar-closed');
  document.getElementById('sidebarOverlay')?.classList.remove('show');
}

// ═══════════════════════════════════════════════════════════════
//  NAVIGATION
// ═══════════════════════════════════════════════════════════════
function navigate(page) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.getElementById('page-' + page)?.classList.add('active');
  document.querySelector(`[data-page="${page}"]`)?.classList.add('active');
  window.scrollTo(0, 0);

  if (page === 'home')          { if (!AppState.chartsInit.home)         initHomeCharts(); }
  if (page === 'factor')        { if (!AppState.chartsInit.factor)       initSpiderChart(); }
  if (page === 'distribution')  { if (!AppState.chartsInit.distribution) initDistCharts(); }
  if (page === 'comparison')    { if (!AppState.chartsInit.comparison)   initCompCharts(); }
  if (page === 'formdata')      { if (typeof resetAndBuildForm === 'function') resetAndBuildForm(); }
  if (page === 'umkmrec')       { renderStartupTable(); }
  if (page === 'profile')       { renderProfilePage(); }
  if (page === 'notifications') { renderNotifications(); }

  applyDynamicData();

  if (['home', 'factor', 'distribution', 'comparison'].includes(page)) {
    loadDashboardData();
  }
}

// ═══════════════════════════════════════════════════════════════
//  USER DISPLAY & TOPBAR
// ═══════════════════════════════════════════════════════════════
function updateUserDisplay() {
  const user = AppState.currentUser;
  if (!user) return;

  const displayName = user.startup_name || user.umkm_name || user.name || user.email?.split('@')[0] || 'Startup Member';
  AppState.currentUser.displayName = displayName;

  const initial = displayName[0].toUpperCase();
  ['topbarAvatar', 'notifAvatar', 'helpAvatar'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.textContent = initial;
  });

  const tn = document.getElementById('topbarName');
  if (tn) tn.textContent = displayName;

  document.querySelectorAll('.welcome-name').forEach(el => {
    el.textContent = displayName;
  });

  const rb = document.getElementById('topbarRole');
  if (rb) {
    const isStaf = user.role === 'staf' || user.role === 'employee';
    rb.textContent = isStaf ? '👤 Staf' : '👔 C-Level';
  }
}

// ═══════════════════════════════════════════════════════════════
//  MASTER STARTUP DATASET (Benchmark Data & Dynamic DB Rows)
// ═══════════════════════════════════════════════════════════════
const DUMMY_STARTUPS = [
  { id:'001', nama:'PulseTech Labs',       lokasi:'Jakarta',   sektor:'SaaS',          omzet:'45 M',  skor:88, faktor:'Kekuatan: Visi & Transparansi', rek:'Optimalisasi Retensi Staf' },
  { id:'002', nama:'FinWise Nusantara',    lokasi:'Jakarta',   sektor:'FinTech',       omzet:'120 M', skor:62, faktor:'Kelemahan: Beban Kerja',       rek:'Audit Jam Lembur Tim Dev' },
  { id:'003', nama:'EduNova Solusindo',    lokasi:'Bandung',   sektor:'EdTech',        omzet:'15 M',  skor:79, faktor:'Kekuatan: Kepemimpinan',       rek:'Peningkatan Tunjangan Remote' },
  { id:'004', nama:'MedikaAI Analytics',   lokasi:'Jakarta',   sektor:'HealthTech',    omzet:'80 M',  skor:91, faktor:'Kekuatan: Kesejahteraan Mental',rek:'Benchmark Industri Positif' },
  { id:'005', nama:'OmniCommerce Platform',lokasi:'Surabaya',  sektor:'E-Commerce',    omzet:'210 M', skor:41, faktor:'Kelemahan: Beban Kerja Kronis',rek:'Intervensi Manajemen Stres' },
  { id:'006', nama:'CloudOps System',      lokasi:'Yogyakarta',sektor:'Enterprise',    omzet:'30 M',  skor:84, faktor:'Kekuatan: Infrastruktur',      rek:'Program Mentoring Karir' },
  { id:'007', nama:'AgriFlow IoT Data',    lokasi:'Bandung',   sektor:'AgriTech',      omzet:'18 M',  skor:73, faktor:'Kekuatan: Budaya Suportif',    rek:'Keseimbangan Ritme Sprint' },
  { id:'008', nama:'LogisChain Global',    lokasi:'Jakarta',   sektor:'LogisTech',     omzet:'95 M',  skor:53, faktor:'Kelemahan: Kelelahan Mental',  rek:'Pemberian Wellness Stipend' },
  { id:'009', nama:'TalentaMatch AI',      lokasi:'Jakarta',   sektor:'AI & ML',       omzet:'60 M',  skor:86, faktor:'Kekuatan: Kompensasi & Karir', rek:'Perluasan Skema ESOP' },
  { id:'010', nama:'SecurePay Gateway',    lokasi:'Bali',      sektor:'FinTech',       omzet:'140 M', skor:68, faktor:'Kelemahan: Tekanan Deadline',  rek:'Penataan Ulang Sprint Target' },
  { id:'011', nama:'BelajarKoding Hub',    lokasi:'Malang',    sektor:'EdTech',        omzet:'12 M',  skor:82, faktor:'Kekuatan: Psychological Safety',rek:'Penguatan Retensi Senior Dev' },
  { id:'012', nama:'KlinikCloud Telemedika',lokasi:'Semarang', sektor:'HealthTech',    omzet:'25 M',  skor:76, faktor:'Kekuatan: Fasilitas Modern',   rek:'Pelatihan Empati Manajerial' },
  { id:'013', nama:'DataMesh Solutions',   lokasi:'Bandung',   sektor:'SaaS',          omzet:'40 M',  skor:48, faktor:'Kelemahan: Jam Kerja Overtime',rek:'Pemberlakuan Jam Hening (No-Meeting)' },
  { id:'014', nama:'B2B SupplyID',         lokasi:'Jakarta',   sektor:'E-Commerce',    omzet:'175 M', skor:64, faktor:'Kelemahan: Kesejahteraan Mental',rek:'Konsultasi Psikolog Internal' },
  { id:'015', nama:'CyberShield Nusantara',lokasi:'Jakarta',   sektor:'Enterprise',    omzet:'110 M', skor:89, faktor:'Kekuatan: Transparansi Founder',rek:'Penguatan Budaya Inklusif' },
  { id:'016', nama:'MicroLend Digital',    lokasi:'Medan',     sektor:'FinTech',       omzet:'85 M',  skor:38, faktor:'Kelemahan: Risiko Burnout Tinggi',rek:'Intervensi Manajemen Darurat' },
  { id:'017', nama:'SmartFarm Agritech',   lokasi:'Bogor',     sektor:'AgriTech',      omzet:'22 M',  skor:77, faktor:'Kekuatan: Kepemimpinan Hangat',rek:'Fasilitas Tooling Cloud Baru' },
  { id:'018', nama:'FleetTrack ID',        lokasi:'Surabaya',  sektor:'LogisTech',     omzet:'50 M',  skor:59, faktor:'Kelemahan: Kompensasi Terbatas',rek:'Evaluasi Ulang Grading Karir' },
  { id:'019', nama:'GenAI Studio Asia',    lokasi:'Bali',      sektor:'AI & ML',       omzet:'75 M',  skor:87, faktor:'Kekuatan: Otonomi Kerja Tinggi',rek:'Program Hackathon & Riset' },
  { id:'020', nama:'SaaSFlow HRMS',        lokasi:'Jakarta',   sektor:'SaaS',          omzet:'35 M',  skor:78, faktor:'Kekuatan: Kualitas Infrastruktur',rek:'Standarisasi Onboarding Staf' },
  { id:'021', nama:'DokterSiaga Mobile',   lokasi:'Yogyakarta',sektor:'HealthTech',    omzet:'28 M',  skor:71, faktor:'Kelemahan: On-Call berlebihan',rek:'Rotasi Shift On-Call Teratur' },
  { id:'022', nama:'PasarDigital B2C',     lokasi:'Depok',     sektor:'E-Commerce',    omzet:'65 M',  skor:52, faktor:'Kelemahan: Ekspektasi Lembur',  rek:'Pemberian Hari Cuti Mental' },
  { id:'023', nama:'SkillUp Academy',      lokasi:'Bandung',   sektor:'EdTech',        omzet:'16 M',  skor:85, faktor:'Kekuatan: Budaya Kolaboratif', rek:'Dukungan Sertifikasi Karyawan' },
  { id:'024', nama:'CryptoVault ID',       lokasi:'Jakarta',   sektor:'FinTech',       omzet:'190 M', skor:65, faktor:'Kelemahan: Stress Volatilitas', rek:'Peningkatan Bonus Kinerja' },
  { id:'025', nama:'Robotika Industri',    lokasi:'Tangerang', sektor:'Enterprise',    omzet:'80 M',  skor:81, faktor:'Kekuatan: Kejelasan Roadmap',  rek:'Peningkatan Tunjangan Kesehatan' },
  { id:'026', nama:'AutoLogistics Hub',    lokasi:'Bekasi',    sektor:'LogisTech',     omzet:'45 M',  skor:44, faktor:'Kelemahan: Beban Sprint Berat', rek:'Penyesuaian Kapasitas Tim' },
  { id:'027', nama:'FarmDirect Indonesia', lokasi:'Malang',    sektor:'AgriTech',      omzet:'20 M',  skor:75, faktor:'Kekuatan: Kehangatan Tim',     rek:'Otomatisasi Laporan Manual' },
  { id:'028', nama:'VisionAI Scanner',     lokasi:'Jakarta',   sektor:'AI & ML',       omzet:'92 M',  skor:90, faktor:'Kekuatan: Kesejahteraan Holistik',rek:'Mempertahankan Standar Sehat' },
  { id:'029', nama:'RetensiKaryawan.com',  lokasi:'Jakarta',   sektor:'SaaS',          omzet:'38 M',  skor:83, faktor:'Kekuatan: Kepemimpinan Empatik',rek:'Penyempurnaan Review 360' },
  { id:'030', nama:'QuickDelivery Express',lokasi:'Surabaya',  sektor:'LogisTech',     omzet:'130 M', skor:35, faktor:'Kelemahan: Kelelahan Akut Tim',rek:'Audit Komprehensif Beban Kerja' },
];

async function loadStartupData() {
  AppState.startupData = [...DUMMY_STARTUPS];

  // Lengkapi data benchmark hingga 120 startup teknologi
  const sectors = ['SaaS', 'FinTech', 'EdTech', 'HealthTech', 'AI & ML', 'E-Commerce', 'LogisTech', 'Enterprise'];
  const locations = ['Jakarta', 'Bandung', 'Yogyakarta', 'Surabaya', 'Bali', 'Malang', 'Semarang', 'Medan'];
  const factorsList = [
    'Kekuatan: Visi & Transparansi',
    'Kekuatan: Kepemimpinan Empatik',
    'Kekuatan: Fasilitas Modern',
    'Kekuatan: Kesejahteraan Mental',
    'Kekuatan: Kompensasi Kompetitif',
    'Kelemahan: Beban Kerja Kronis',
    'Kelemahan: Ekspektasi Lembur',
    'Kelemahan: Kurang Apresiasi',
    'Kelemahan: Risiko Burnout Tinggi'
  ];
  const recs = [
    'Audit Jam Lembur Tim Dev',
    'Pemberlakuan No-Meeting Day',
    'Program Konseling Mental Health',
    'Optimalisasi Skema ESOP',
    'Peningkatan Tunjangan Remote',
    'Pelatihan Empati Manajer',
    'Penyesuaian Kapasitas Sprint',
    'Pemberian Cuti Recovery'
  ];

  let seed = 42;
  function random() {
    let x = Math.sin(seed++) * 10000;
    return x - Math.floor(x);
  }

  for (let i = 31; i <= 120; i++) {
    const sec = sectors[Math.floor(random() * sectors.length)];
    const loc = locations[Math.floor(random() * locations.length)];
    const skor = Math.floor(random() * 55) + 40; // 40 - 95
    const factor = factorsList[Math.floor(random() * factorsList.length)];
    const rek = recs[Math.floor(random() * recs.length)];
    const omzet = (Math.floor(random() * 150) + 15) + ' M';

    AppState.startupData.push({
      id: String(i).padStart(3, '0'),
      nama: `Startup ${sec} ${loc} ${i}`,
      lokasi: loc,
      sektor: sec,
      omzet: omzet,
      skor: skor,
      faktor: factor,
      rek: rek
    });
  }

  // Tarik data asli dari database MySQL WorkPulse
  try {
    const res = await DashboardAPI.getStartupList({ limit: 1000 });
    if (res?.ok && Array.isArray(res.data?.rows)) {
      const dbEntries = res.data.rows.map((r, idx) => {
        const rawScore = r.score_on_5 ? Math.round((r.score_on_5 / 5) * 100) : 0;
        let fundingDisplay = '-';
        if (r.pendanaan) {
          const num = parseInt(r.pendanaan);
          if (!isNaN(num) && num > 0) {
            fundingDisplay = num >= 1000000000
              ? (num / 1000000000).toFixed(1) + ' M'
              : (num / 1000000).toFixed(0) + ' Jt';
          }
        }

        return {
          id:     String(120 + idx + 1).padStart(3, '0'),
          nama:   r.startup_name || 'Startup Baru',
          lokasi: r.lokasi || 'Indonesia',
          sektor: r.sektor_teknologi || 'Teknologi',
          omzet:  fundingDisplay,
          skor:   rawScore,
          faktor: rawScore >= 75 ? 'Kekuatan: Budaya Organisasi Sehat' : 'Kelemahan: Waspada Burnout',
          rek:    r.health_category === 'Budaya Kerja Positif' ? 'Pertahankan Budaya Sehat'
                : r.health_category === 'Kondisi Stabil'       ? 'Tingkatkan Apresiasi Tim'
                : r.health_category === 'Waspada Gejala Burnout'? 'Audit Beban Kerja & Lembur'
                : 'Intervensi Manajemen Segera',
          isNew:  true
        };
      });
      AppState.startupData = [...AppState.startupData, ...dbEntries];
    }
  } catch(e) {
    console.warn('[WorkPulse] Load DB items failed:', e);
  }

  AppState.filteredData = [...AppState.startupData];
  AppState.currentPage  = 1;
}

// Backward compatibility alias
const loadUmkmData = loadStartupData;

// ═══════════════════════════════════════════════════════════════
//  SEARCH & FILTERING
// ═══════════════════════════════════════════════════════════════
function applyFilter(data, filter) {
  if (!filter || filter === 'semua') return data;
  return data.filter(u => u.sektor.toLowerCase().includes(filter.toLowerCase()));
}

function handleSearch(val) {
  const q = val.trim().toLowerCase();
  if (!q) {
    AppState.filteredData = applyFilter(AppState.startupData, AppState.activeFilter);
    AppState.currentPage  = 1;
    renderStartupTable();
    return;
  }
  const base = applyFilter(AppState.startupData, AppState.activeFilter);
  AppState.filteredData = base.filter(u =>
    u.nama.toLowerCase().includes(q)    ||
    u.lokasi.toLowerCase().includes(q)  ||
    u.sektor.toLowerCase().includes(q)  ||
    u.id.toLowerCase().includes(q)      ||
    u.rek.toLowerCase().includes(q)
  );
  AppState.currentPage = 1;
  renderStartupTable();

  if (!document.getElementById('page-umkmrec')?.classList.contains('active')) {
    navigate('umkmrec');
  }
}

function filterByKategori(kategori) {
  AppState.activeFilter = kategori;
  const search = document.getElementById('searchInput')?.value?.trim()?.toLowerCase() || '';
  let base = applyFilter(AppState.startupData, kategori);
  if (search) {
    base = base.filter(u =>
      u.nama.toLowerCase().includes(search) ||
      u.lokasi.toLowerCase().includes(search) ||
      u.sektor.toLowerCase().includes(search)
    );
  }
  AppState.filteredData = base;
  AppState.currentPage  = 1;
  renderStartupTable();

  if (!document.getElementById('page-umkmrec')?.classList.contains('active')) {
    navigate('umkmrec');
  }
  document.querySelectorAll('.dropdown-menu').forEach(d => d.classList.remove('show'));
  showToast(`Sektor aktif: ${kategori === 'semua' ? 'Semua Sektor' : kategori}`);
}

const filterUmkmTable = filterByKategori;

// ═══════════════════════════════════════════════════════════════
//  RENDER STARTUP TABLE & PAGINATION
// ═══════════════════════════════════════════════════════════════
function renderStartupTable() {
  if (!AppState.startupData.length) {
    loadStartupData().then(() => renderStartupTable());
    return;
  }

  const data       = AppState.filteredData;
  const total      = data.length;
  const pageSize   = AppState.pageSize;
  const page       = AppState.currentPage;
  const totalPages = Math.max(1, Math.ceil(total / pageSize));
  const start      = (page - 1) * pageSize;
  const slice      = data.slice(start, start + pageSize);

  const tbody = document.getElementById('umkmTableBody');
  if (!tbody) return;

  if (!slice.length) {
    tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:32px;color:var(--gray-400)">Tidak ada data startup yang cocok</td></tr>`;
  } else {
    tbody.innerHTML = slice.map(u => {
      const scoreClass = u.skor >= 80 ? 'score-great' : u.skor >= 65 ? 'score-good' : u.skor >= 50 ? 'score-mid' : 'score-bad';
      const sec = u.sektor.toLowerCase();
      const sektorClass = sec.includes('saas') ? 'sector-saas'
                        : sec.includes('fintech') ? 'sector-fintech'
                        : sec.includes('edtech') ? 'sector-edtech'
                        : sec.includes('health') ? 'sector-health'
                        : sec.includes('ai') ? 'sector-ai'
                        : 'sector-general';
      const faktorClass = u.faktor.includes('Kekuatan') ? 'kekuatan' : 'kelemahan';

      return `<tr>
        <td class="umkm-id" onclick="showStartupDetail('${u.id}')">${u.id}</td>
        <td class="umkm-name"><strong>${u.nama}</strong></td>
        <td>${u.lokasi}</td>
        <td><span class="sector-badge ${sektorClass}">${u.sektor.toUpperCase()}</span></td>
        <td>${u.omzet}</td>
        <td><div class="health-score ${scoreClass}">${u.skor}</div></td>
        <td class="${faktorClass}">${u.faktor}</td>
        <td>${u.rek}</td>
      </tr>`;
    }).join('');
  }

  const pInfo = document.getElementById('paginationInfo');
  if (pInfo) pInfo.textContent = `Halaman ${page} dari ${totalPages} (${total} startup)`;

  renderPaginationBtns(page, totalPages);

  const avgSkor = data.length ? Math.round(data.reduce((a, b) => a + b.skor, 0) / data.length) : 0;
  safeSet('stat-total-rec', total);
  safeSet('stat-avg-rec', avgSkor + '/100');
}

const renderUmkmTable = renderStartupTable;

function renderPaginationBtns(current, total) {
  const wrap = document.getElementById('paginationBtns');
  if (!wrap) return;

  let btns = `<button class="page-btn" onclick="goToPage(${current - 1})" ${current <= 1 ? 'disabled' : ''}>‹</button>`;
  const pages = [];

  if (total <= 7) {
    for (let i = 1; i <= total; i++) pages.push(i);
  } else {
    pages.push(1);
    if (current > 3) pages.push('...');
    for (let i = Math.max(2, current - 1); i <= Math.min(total - 1, current + 1); i++) pages.push(i);
    if (current < total - 2) pages.push('...');
    pages.push(total);
  }

  pages.forEach(p => {
    if (p === '...') {
      btns += `<button class="page-btn" disabled>...</button>`;
    } else {
      btns += `<button class="page-btn ${p === current ? 'active' : ''}" onclick="goToPage(${p})">${p}</button>`;
    }
  });

  btns += `<button class="page-btn" onclick="goToPage(${current + 1})" ${current >= total ? 'disabled' : ''}>›</button>`;
  wrap.innerHTML = btns;
}

function goToPage(p) {
  const total = Math.ceil(AppState.filteredData.length / AppState.pageSize);
  if (p < 1 || p > total) return;
  AppState.currentPage = p;
  renderStartupTable();
  document.getElementById('page-umkmrec')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function showStartupDetail(id) {
  const u = AppState.startupData.find(x => x.id === id);
  if (!u) return;
  showToast(`Startup: ${u.nama} — Skor Budaya: ${u.skor}/100 — Rekomendasi: ${u.rek}`);
}

const showUmkmDetail = showStartupDetail;

// ═══════════════════════════════════════════════════════════════
//  EXPORT DATA (CSV)
// ═══════════════════════════════════════════════════════════════
function exportData() {
  const data = AppState.filteredData.length ? AppState.filteredData : AppState.startupData;
  const headers = ['ID', 'Nama Startup', 'Lokasi', 'Sektor Teknologi', 'Funding Pendanaan', 'Skor Budaya', 'Indikator Utama', 'Rekomendasi'];
  const rows = data.map(u => [u.id, u.nama, u.lokasi, u.sektor, u.omzet, u.skor, u.faktor, u.rek]
    .map(v => `"${String(v).replace(/"/g, '""')}"`).join(','));
  const csv = [headers.join(','), ...rows].join('\n');
  const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
  const url  = URL.createObjectURL(blob);
  const a    = document.createElement('a');
  a.href     = url;
  a.download = `WorkPulse_Data_${new Date().toISOString().slice(0, 10)}.csv`;
  a.click();
  URL.revokeObjectURL(url);
  showToast('Dataset evaluasi budaya kerja berhasil diekspor (CSV)', 'success');
}

// ═══════════════════════════════════════════════════════════════
//  PROTOKOL INTERVENSI STRATEGIS MODAL
// ═══════════════════════════════════════════════════════════════
function showProtokol(jenis) {
  const protokols = {
    bk: {
      title: 'Protokol Mitigasi: Beban Kerja & Overtime Kronis (BK)',
      priority: 'PRIORITAS KRITIS',
      color: '#EF4444',
      steps: [
        'Lakukan audit story-point sprint vs estimasi jam kerja tim developer',
        'Terapkan kebijakan "No-Meeting Friday" untuk deep-work tanpa distraksi',
        'Batasi komunikasi Slack/WhatsApp di luar jam kerja resmi (Right to Disconnect)',
        'Susun ulang tenggat waktu rilis fitur dengan buffer pemulihan yang realistis',
        'Pantau heatmap jam commit mingguan untuk mendeteksi lembur tersembunyi'
      ],
      eta: 'Implementasi 48 Jam'
    },
    km: {
      title: 'Protokol Intervensi: Kesejahteraan Mental & Burnout (KM)',
      priority: 'PRIORITAS TINGGI',
      color: '#F59E0B',
      steps: [
        'Buka sesi one-on-one terbuka berfokus pada psychological safety',
        'Sediakan tunjangan konseling profesional atau psikolog kerja',
        'Hilangkan budaya menyalahkan (blameless post-mortem) saat insiden sistem',
        'Beri hak "Mental Health Day" ekstra setelah fase peluncuran besar',
        'Survei denyut tim (pulse survey) berkala tiap akhir pekan sprint'
      ],
      eta: '7 Hari Evaluasi'
    },
    vt: {
      title: 'Protokol Penyelarasan: Visi & Transparansi Founder (VT)',
      priority: 'PEMELIHARAAN',
      color: '#10B981',
      steps: [
        'Gelar Townhall bulanan untuk memaparkan metrik runway dan capaian OKR',
        'Buat dokumentasi terbuka mengenai pertimbangan keputusan pivot strategi',
        'Buka kanal AMA (Ask Me Anything) langsung bersama jajaran C-Level',
        'Pastikan setiap engineer memahami dampak kodenya terhadap pertumbuhan bisnis'
      ],
      eta: 'Rutin Berkelanjutan'
    }
  };

  const p = protokols[jenis] || protokols.bk;
  const popup = document.createElement('div');
  popup.style.cssText = 'position:fixed;inset:0;background:rgba(15,23,42,0.6);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;animation:wpFadeIn 0.2s ease';
  popup.innerHTML = `
    <div style="background:#FFFFFF;border-radius:16px;padding:28px;max-width:480px;width:100%;box-shadow:0 20px 40px -15px rgba(0,0,0,0.15);border:1px solid #E2E8F0">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px">
        <div>
          <span style="font-size:11px;font-weight:700;background:${p.color}15;color:${p.color};padding:3px 10px;border-radius:20px">${p.priority}</span>
          <h2 style="font-size:17px;font-weight:800;color:#0F172A;margin-top:8px">${p.title}</h2>
        </div>
        <button onclick="this.closest('[style]').remove()" style="width:32px;height:32px;border:none;background:#F1F5F9;border-radius:8px;cursor:pointer;font-size:16px;color:#64748B">✕</button>
      </div>
      <div style="margin-bottom:20px">
        ${p.steps.map((s, i) => `
          <div style="display:flex;align-items:flex-start;gap:12px;padding:10px 0;border-bottom:1px solid #F1F5F9">
            <div style="width:22px;height:22px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0">${i + 1}</div>
            <span style="font-size:13px;color:#334155;line-height:1.4">${s}</span>
          </div>`).join('')}
      </div>
      <div style="display:flex;align-items:center;justify-content:space-between;background:#F8FAFC;border:1px solid #E2E8F0;border-radius:10px;padding:12px 16px">
        <span style="font-size:12px;color:#64748B">Target Waktu Implementasi</span>
        <span style="font-size:13px;font-weight:700;color:${p.color}">${p.eta}</span>
      </div>
      <button onclick="this.closest('[style]').remove();showToast('Protokol ditandai sebagai rencana aksi aktif','success')"
        style="width:100%;margin-top:16px;padding:12px;background:var(--primary);border:none;border-radius:10px;color:#fff;font-weight:700;font-size:14px;cursor:pointer;font-family:inherit">
        Terapkan Protokol di Startup
      </button>
    </div>`;

  document.body.appendChild(popup);
  popup.addEventListener('click', e => {
    if (e.target === popup) popup.remove();
  });
}

// ═══════════════════════════════════════════════════════════════
//  CHARTS & DASHBOARD DATA AGGREGATION
// ═══════════════════════════════════════════════════════════════
async function loadDashboardData() {
  try {
    const res = await DashboardAPI.getStats();
    if (!res?.ok || !res.data) return;

    AppState.dashboardStats = res.data.stats;
    AppState.factorAverages = res.data.factor_averages;

    applyDynamicData();
  } catch(e) {
    console.warn('[WorkPulse] Load dashboard data error:', e);
  }
}

function applyDynamicData() {
  const total = AppState.totalStartup;

  updateTotalDisplay(total);
  updateDonutLegend(total);

  const stats = AppState.dashboardStats;
  const fa    = AppState.factorAverages;

  if (stats) {
    const sehatCount = parseInt(stats.positif_count) || Math.round(total * 0.38);
    const sehatPct   = Math.round((sehatCount / total) * 100) + '%';
    safeSet('stat-sehat-pct',   sehatPct);
    safeSet('stat-best-score',  stats.best_score || '4.82');
    safeSet('stat-worst-score', stats.worst_score || '1.85');
  }

  // Update Donut Chart
  if (window.donutChartInst) {
    const burnoutTinggi = Math.round(total * 0.08);
    const waspada       = Math.round(total * 0.22);
    const stabil        = Math.round(total * 0.42);
    const positif       = total - burnoutTinggi - waspada - stabil;

    window.donutChartInst.data.datasets[0].data = [burnoutTinggi, waspada, stabil, positif];
    window.donutChartInst.update('none');
  }

  // Update Bar Chart (6 Faktor Budaya & Burnout)
  if (window.barChartInst) {
    const vals = fa ? [
      fa.avg_vt_5 || 3.82,
      fa.avg_kk_5 || 3.45,
      fa.avg_fi_5 || 3.65,
      fa.avg_bk_5 || 2.45,
      fa.avg_km_5 || 2.78,
      fa.avg_kp_5 || 3.30
    ] : [3.82, 3.45, 3.65, 2.45, 2.78, 3.30];

    window.barChartInst.data.datasets[0].data = vals;
    window.barChartInst.update();
  }

  // Update Radar / Spider Chart
  if (window.spiderChartInst) {
    const vals = fa ? [
      fa.avg_vt_5 || 3.82,
      fa.avg_kk_5 || 3.45,
      fa.avg_fi_5 || 3.65,
      fa.avg_bk_5 || 2.45,
      fa.avg_km_5 || 2.78,
      fa.avg_kp_5 || 3.30
    ] : [3.82, 3.45, 3.65, 2.45, 2.78, 3.30];

    window.spiderChartInst.data.datasets[0].data = vals;
    window.spiderChartInst.update();
  }

  // Update Bottom Metrik Dashboard
  safeSet('rec-compliance', '94.8%');
  safeSet('rec-critical', '3 Peringatan');
  safeSet('rec-resp-time', '8.4 Min');
  safeSet('rec-next-scan', '18:00 WIB');
}

function updateTotalDisplay(total) {
  safeSet('stat-total-umkm', total);
  safeSet('donut-total', total);
  safeSet('rec-total-count', total);
  safeSet('dist-populasi', `Populasi: ${total}`);
}

function updateDonutLegend(total) {
  const tinggi  = Math.round(total * 0.08);
  const waspada = Math.round(total * 0.22);
  const stabil  = Math.round(total * 0.42);
  const positif = total - tinggi - waspada - stabil;

  safeSet('legend-burnout-tinggi', `Risiko Tinggi (${tinggi})`);
  safeSet('legend-waspada',        `Waspada (${waspada})`);
  safeSet('legend-stabil',         `Kondisi Stabil (${stabil})`);
  safeSet('legend-positif',        `Budaya Positif (${positif})`);
}

// ═══════════════════════════════════════════════════════════════
//  CHART.JS INITIALIZATION (Clean Light Mode Aesthetic)
// ═══════════════════════════════════════════════════════════════
function initHomeCharts() {
  const donutEl = document.getElementById('donutChart');
  if (donutEl) {
    const ctx = donutEl.getContext('2d');
    window.donutChartInst = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Risiko Burnout Tinggi', 'Waspada Gejala Burnout', 'Kondisi Stabil', 'Budaya Kerja Positif'],
        datasets: [{
          data: [10, 26, 50, 34],
          backgroundColor: ['#EF4444', '#F59E0B', '#3B82F6', '#10B981'],
          borderWidth: 2,
          borderColor: '#FFFFFF',
          hoverOffset: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '76%',
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#0F172A',
            padding: 10,
            cornerRadius: 8,
            titleFont: { family: 'Plus Jakarta Sans', size: 12 },
            bodyFont: { family: 'Plus Jakarta Sans', size: 12 }
          }
        }
      }
    });
  }

  const barEl = document.getElementById('barChart');
  if (barEl) {
    const ctx = barEl.getContext('2d');
    window.barChartInst = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: [
          'Visi & Transparansi',
          'Kualitas Kepemimpinan',
          'Fasilitas & Infra',
          'Beban Kerja (Workload)',
          'Kesejahteraan Mental',
          'Kompensasi & Karir'
        ],
        datasets: [{
          label: 'Skor Indeks (0-5)',
          data: [3.82, 3.45, 3.65, 2.45, 2.78, 3.30],
          backgroundColor: [
            '#6366F1',
            '#10B981',
            '#0EA5E9',
            '#F43F5E',
            '#8B5CF6',
            '#F59E0B'
          ],
          borderRadius: 8,
          maxBarThickness: 38
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            max: 5,
            grid: { color: '#F1F5F9' },
            ticks: { color: '#64748B', font: { family: 'Plus Jakarta Sans', size: 11 } }
          },
          x: {
            grid: { display: false },
            ticks: { color: '#64748B', font: { family: 'Plus Jakarta Sans', size: 11 } }
          }
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#0F172A',
            padding: 10,
            cornerRadius: 8,
            callbacks: {
              label: (ctx) => ` Indeks: ${ctx.parsed.y} / 5.0`
            }
          }
        }
      }
    });
  }

  AppState.chartsInit.home = true;
}

function initSpiderChart() {
  const spiderEl = document.getElementById('spiderChart');
  if (!spiderEl) return;
  const ctx = spiderEl.getContext('2d');

  window.spiderChartInst = new Chart(ctx, {
    type: 'radar',
    data: {
      labels: [
        'Visi & Transparansi',
        'Kepemimpinan',
        'Fasilitas & Infra',
        'Beban Kerja',
        'Kesejahteraan Mental',
        'Kompensasi & Karir'
      ],
      datasets: [
        {
          label: 'Rata-rata Ekosistem Startup',
          data: [3.82, 3.45, 3.65, 2.45, 2.78, 3.30],
          backgroundColor: 'rgba(99, 102, 241, 0.2)',
          borderColor: '#6366F1',
          pointBackgroundColor: '#6366F1',
          borderWidth: 2,
          pointRadius: 4
        },
        {
          label: 'Benchmark Budaya Positif',
          data: [4.2, 4.0, 4.0, 3.8, 4.0, 3.9],
          backgroundColor: 'rgba(16, 185, 129, 0.08)',
          borderColor: '#10B981',
          borderDash: [4, 4],
          borderWidth: 1.5,
          pointRadius: 3
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        r: {
          beginAtZero: true,
          max: 5,
          ticks: { stepSize: 1, color: '#94A3B8' },
          grid: { color: '#E2E8F0' },
          pointLabels: {
            font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' },
            color: '#334155'
          }
        }
      },
      plugins: {
        legend: {
          position: 'bottom',
          labels: { font: { family: 'Plus Jakarta Sans', size: 12 }, boxWidth: 12 }
        }
      }
    }
  });

  AppState.chartsInit.factor = true;
}

function initDistCharts() {
  const histEl = document.getElementById('histChart');
  if (!histEl) return;
  const ctx = histEl.getContext('2d');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['1.0 - 1.9 (Kritis)', '2.0 - 2.9 (Waspada)', '3.0 - 3.9 (Stabil)', '4.0 - 5.0 (Positif)'],
      datasets: [{
        label: 'Jumlah Startup',
        data: [10, 26, 50, 34],
        backgroundColor: ['#EF4444', '#F59E0B', '#3B82F6', '#10B981'],
        borderRadius: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: { beginAtZero: true, grid: { color: '#F1F5F9' }, ticks: { color: '#64748B' } },
        x: { grid: { display: false }, ticks: { color: '#64748B' } }
      },
      plugins: { legend: { display: false } }
    }
  });

  AppState.chartsInit.distribution = true;
}

function initCompCharts() {
  const trendEl = document.getElementById('trendChart');
  if (!trendEl) return;
  const ctx = trendEl.getContext('2d');

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Q1', 'Q2', 'Q3', 'Q4'],
      datasets: [{
        label: 'Indeks Kesehatan Budaya',
        data: [2.95, 3.10, 3.28, 3.42],
        borderColor: '#6366F1',
        backgroundColor: 'rgba(99, 102, 241, 0.1)',
        fill: true,
        tension: 0.3,
        borderWidth: 2.5
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: { beginAtZero: false, min: 2.0, max: 4.5, grid: { color: '#F1F5F9' } },
        x: { grid: { display: false } }
      },
      plugins: { legend: { display: false } }
    }
  });

  AppState.chartsInit.comparison = true;
}

function refreshAllCharts() {
  if (window.donutChartInst) window.donutChartInst.destroy();
  if (window.barChartInst) window.barChartInst.destroy();
  if (window.spiderChartInst) window.spiderChartInst.destroy();
  AppState.chartsInit = {};
  initHomeCharts();
  applyDynamicData();
}

// ═══════════════════════════════════════════════════════════════
//  NOTIFICATIONS & PROFILE PAGES
// ═══════════════════════════════════════════════════════════════
function renderNotifications() {
  const total = AppState.totalStartup;
  const user  = AppState.currentUser || { displayName: 'Startup Member' };
  safeSet('notif-halo', `Halo, ${user.displayName}!`);

  const listContainer = document.getElementById('notif-list-container');
  if (!listContainer) return;

  listContainer.innerHTML = `
    <div class="notif-item danger">
      <div class="notif-item-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
      </div>
      <div class="notif-item-content">
        <div class="notif-item-type">PERINGATAN SISTEMIK</div>
        <div class="notif-item-title">Sinyal Peningkatan Beban Sprint (Workload Alert)</div>
        <div class="notif-item-desc">3 startup teknologi terdeteksi mengalami indikator jam lembur melampaui ambang batas sehat (skor Beban Kerja &lt; 2.2). Tinjau rekomendasi mitigasi burnout.</div>
      </div>
      <div class="notif-item-time">15 mnt lalu</div>
    </div>

    <div class="notif-item success">
      <div class="notif-item-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
      </div>
      <div class="notif-item-content">
        <div class="notif-item-type">VALIDASI DATA</div>
        <div class="notif-item-title">Asesmen Baru Terverifikasi: ${total} Startup</div>
        <div class="notif-item-desc">Seluruh agregat jawaban kuesioner periode aktif telah divalidasi dengan algoritma validasi silang WorkPulse.</div>
      </div>
      <div class="notif-item-time">1 jam lalu</div>
    </div>

    <div class="notif-item primary">
      <div class="notif-item-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      </div>
      <div class="notif-item-content">
        <div class="notif-item-type">PEMBARUAN PROTOKOL</div>
        <div class="notif-item-title">Rilis Standar Penilaian Burnout Versi 2.0</div>
        <div class="notif-item-desc">Kini 6 faktor budaya organisasi terintegrasi dengan tolok ukur spesifik iklim kerja industri software &amp; startup teknologi.</div>
      </div>
      <div class="notif-item-time">3 jam lalu</div>
    </div>`;
}

function renderProfilePage() {
  const container = document.getElementById('profile-content');
  if (!container) return;
  const user = AppState.currentUser || {};

  const startupName = user.startup_name || user.umkm_name || 'PulseTech Labs';
  const roleDisplay = (user.role === 'staf' || user.role === 'employee') ? 'Staf & Tim Teknis' : 'Founder & C-Level';
  const sektorText  = user.sektor_teknologi || user.sektor || 'SaaS (Software-as-a-Service)';
  const fundingText = user.pendanaan ? 'IDR ' + parseInt(user.pendanaan).toLocaleString('id-ID') : 'Bootstrapped / Seed';

  container.innerHTML = `
    <div class="card" style="max-width:680px;margin:0 auto;padding:32px">
      <div style="display:flex;align-items:center;gap:20px;margin-bottom:28px">
        <div style="width:72px;height:72px;border-radius:18px;background:linear-gradient(135deg,#6366F1,#4F46E5);color:#fff;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:800">
          ${startupName[0].toUpperCase()}
        </div>
        <div>
          <h2 style="font-size:22px;font-weight:800;color:#0F172A;margin-bottom:4px">${startupName}</h2>
          <div style="display:flex;gap:8px;align-items:center">
            <span style="background:#EEF2FF;color:#6366F1;font-size:12px;font-weight:700;padding:3px 10px;border-radius:20px">${roleDisplay}</span>
            <span style="font-size:12px;color:#64748B">${user.email || 'user@startup.com'}</span>
          </div>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">
        <div style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:12px;padding:16px">
          <div style="font-size:11px;font-weight:600;color:#64748B;margin-bottom:4px">LOKASI HUB</div>
          <div style="font-size:14px;font-weight:700;color:#0F172A">${user.lokasi || 'Jakarta'}</div>
        </div>
        <div style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:12px;padding:16px">
          <div style="font-size:11px;font-weight:600;color:#64748B;margin-bottom:4px">SEKTOR TEKNOLOGI</div>
          <div style="font-size:14px;font-weight:700;color:#0F172A">${sektorText}</div>
        </div>
        <div style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:12px;padding:16px;grid-column:span 2">
          <div style="font-size:11px;font-weight:600;color:#64748B;margin-bottom:4px">RATA-RATA PENDANAAN / MODAL KERJA</div>
          <div style="font-size:16px;font-weight:800;color:#0F172A">${fundingText}</div>
        </div>
      </div>

      <div style="display:flex;gap:12px;justify-content:flex-end">
        <button class="btn-secondary" onclick="showToast('Fitur edit profil tersimpan')">Perbarui Data</button>
        <button class="btn-primary" onclick="navigate('formdata')">Mulai Asesmen Budaya Baru</button>
      </div>
    </div>`;
}

// ── Utility Helpers ───────────────────────────────────────────
function safeSet(id, val) {
  const el = document.getElementById(id);
  if (el) el.textContent = val;
}

function showToast(msg, type = 'info') {
  const t = document.getElementById('toast');
  const m = document.getElementById('toastMsg');
  if (!t || !m) return;
  m.textContent = msg;
  t.className = type;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3500);
}

function showModal(id) {
  document.getElementById(id)?.classList.add('show');
}

function hideModal(id) {
  document.getElementById(id)?.classList.remove('show');
}

function toggleDropdown(id) {
  document.querySelectorAll('.dropdown-menu').forEach(d => {
    if (d.id !== id) d.classList.remove('show');
  });
  document.getElementById(id)?.classList.toggle('show');
}

// Tutup dropdown jika klik di luar
window.addEventListener('click', e => {
  if (!e.target.closest('.dropdown-wrap') && !e.target.closest('[onclick*="toggleDropdown"]')) {
    document.querySelectorAll('.dropdown-menu').forEach(d => d.classList.remove('show'));
  }
});

// ── Inisialisasi Aplikasi Saat Memuat Halaman ──────────────────
function initDashboard() {
  updateUserDisplay();
  loadStartupData().then(() => {
    initHomeCharts();
    applyDynamicData();
    renderStartupTable();
  });
}
