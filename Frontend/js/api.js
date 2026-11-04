/**
 * WorkPulse — Frontend API Client
 * HTTP communication wrapper for WorkPulse REST Backend
 */

// Path ke backend
const API_BASE = (function() {
  if (window.location.pathname.includes('/Frontend/') || window.location.pathname.includes('/html/')) {
    return '../backend/api.php';
  }
  return 'backend/api.php';
})();

// ── Token & User Session Storage ──────────────────────────────
const TokenStore = {
  get()       { return localStorage.getItem('wp_token') || localStorage.getItem('nexa_token'); },
  set(t)      { localStorage.setItem('wp_token', t); },
  clear()     { localStorage.removeItem('wp_token'); localStorage.removeItem('nexa_token'); },
  getUser()   {
    try {
      return JSON.parse(localStorage.getItem('wp_user') || localStorage.getItem('nexa_user') || 'null');
    } catch {
      return null;
    }
  },
  setUser(u)  { localStorage.setItem('wp_user', JSON.stringify(u)); },
  clearUser() { localStorage.removeItem('wp_user'); localStorage.removeItem('nexa_user'); },
};

// ── Base HTTP Fetch Wrapper ───────────────────────────────────
async function apiCall(action, method = 'GET', body = null) {
  const headers = { 'Content-Type': 'application/json' };
  const token = TokenStore.get();
  if (token) headers['Authorization'] = 'Bearer ' + token;

  const opts = { method, headers };
  if (body && method !== 'GET') opts.body = JSON.stringify(body);

  try {
    const res  = await fetch(API_BASE + '?action=' + action, opts);
    const data = await res.json();
    return { ok: res.ok && (data.success === true || data.status === 'success'), status: res.status, ...data };
  } catch (err) {
    console.warn('[WorkPulse API Error]', err);
    return { ok: false, status: 0, message: 'Tidak dapat terhubung ke server WorkPulse.' };
  }
}

// ── AUTH API ──────────────────────────────────────────────────
const AuthAPI = {
  async register(payload) {
    const r = await apiCall('register', 'POST', payload);
    if (r.ok && r.data && r.data.token) {
      TokenStore.set(r.data.token);
      TokenStore.setUser(r.data);
    }
    return r;
  },

  async login(payload) {
    const r = await apiCall('login', 'POST', payload);
    if (r.ok && r.data && r.data.token) {
      TokenStore.set(r.data.token);
      TokenStore.setUser(r.data);
    }
    return r;
  },

  logout() {
    TokenStore.clear();
    TokenStore.clearUser();
  },

  getUser()    { return TokenStore.getUser(); },
  isLoggedIn() { return !!TokenStore.get(); }
};

// ── FORM API ──────────────────────────────────────────────────
const FormAPI = {
  async submit(answers) {
    return apiCall('submit', 'POST', answers);
  }
};

// ── DASHBOARD API ─────────────────────────────────────────────
const DashboardAPI = {
  async getStats() {
    return apiCall('dashboard');
  },
  async getStartupList(opts = {}) {
    const limit  = opts.limit  || 15;
    const offset = opts.offset || 0;
    const q      = opts.q      || '';
    return apiCall('startup_list&limit=' + limit + '&offset=' + offset + '&q=' + encodeURIComponent(q));
  },
  // Backward compatibility alias
  async getUmkmList(opts = {}) {
    return this.getStartupList(opts);
  },
  async getFactorAverages() {
    return apiCall('factor_averages');
  }
};
