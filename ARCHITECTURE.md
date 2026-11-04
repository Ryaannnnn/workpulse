# ARCHITECTURE.md — WorkPulse

## 1. Gambaran Umum Platform

**WorkPulse** adalah platform analitik dan asesmen klinis budaya kerja (*work culture*) serta mitigasi risiko *burnout* yang dirancang khusus untuk ekosistem startup teknologi. Platform ini membantu para **Founder & C-Level** serta **Karyawan Teknis (Staf)** mengidentifikasi gejala kelelahan sistemik (*systemic fatigue*), beban *sprint* berlebih (*workload overload*), dan lingkungan kerja tidak sehat (*toxic workplace*) sebelum berdampak pada lonjakan *turnover* talenta kunci.

---

## 2. Framework 6 Pilar Kesehatan Organisasi & Burnout

Penilaian kesehatan organisasi diukur melalui **36 indikator klinis** terstandarisasi berbasis skala Likert (1–5) yang terbagi ke dalam 6 pilar utama dengan segmentasi peran:

| Kode | Pilar Budaya Kerja | Penilai Utama | Bobot | Deskripsi Fokus Asesmen |
|:----:|:---|:---:|:---:|:---|
| **VT** | **Visi & Transparansi** | C-Level | 35% | Kejelasan *runway* finansial, transparansi *roadmap* produk, keterbukaan keputusan *pivot*, dan keamanan menyuarakan aspirasi. |
| **KK** | **Kualitas Kepemimpinan** | Staf | 35% | Empati manajerial, ketiadaan *micromanagement*, keamanan psikologis (*psychological safety*), dan keadilan delegasi tugas. |
| **FI** | **Fasilitas & Infrastruktur** | C-Level | 30% | Kestabilan *dev tools*, otomasi CI/CD, subsidi kerja *remote/hybrid*, dan kelayakan lisensi perangkat lunak. |
| **BK** | **Beban Kerja (Workload)** | Staf | 35% | Frekuensi lembur (*overtime*), batas jam kerja sehat (*work-life boundary*), realisme tenggat *sprint*, dan hak istirahat. |
| **KM** | **Kesejahteraan Mental** | Staf | 30% | Kelelahan emosional, stres kronis, bebas dari intimidasi (*toxic culture*), serta motivasi psikologis harian. |
| **KP** | **Kompensasi & Karir** | C-Level | 35% | Daya saing gaji & ESOP terhadap pasar teknologi, transparansi evaluasi bonus, serta jalur karir spesialis vs manajerial. |

---

## 3. Klasifikasi Risiko Burnout (Burnout Risk Index)

Skor komposit dihitung dalam rentang **1.00 – 5.00** dan diklasifikasikan ke dalam 4 tingkatan status klinis:

```
[ 1.00 ────────── 2.79 ]   Risiko Burnout Tinggi   (Tingkat Kritis / Intervensi Segera)
[ 2.80 ────────── 3.49 ]   Waspada Gejala Burnout  (Deteksi Kelelahan Sistemik Awal)
[ 3.50 ────────── 4.19 ]   Kondisi Stabil          (Budaya Terkelola & Seimbang)
[ 4.20 ────────── 5.00 ]   Budaya Kerja Positif    (Organisasi Sangat Sehat & Resilien)
```

- **Budaya Kerja Positif (Score ≥ 4.20):** Budaya kerja sehat dan suportif, beban terdistribusi merata, *psychological safety* tinggi.
- **Kondisi Stabil (Score 3.50 – 4.19):** Ritme kerja terkendali, kepuasan kerja stabil, memerlukan pemantauan berkala saat *release cycle* tinggi.
- **Waspada Gejala Burnout (Score 2.80 – 3.49):** Terdeteksi gejala kelelahan sistemik, intensitas lembur meningkat, atau friksi komunikasi internal.
- **Risiko Burnout Tinggi (Score < 2.80):** Kelelahan mental akut, risiko tinggi *mass resignation*, krisis kepemimpinan atau kelebihan beban kerja kronis.

---

## 4. Struktur Arsitektur Ganda (Dual-Stack)

WorkPulse menyediakan fleksibilitas arsitektur penuh:
1. **Paket Standalone Lightweight (Native PHP + Vanilla JS):** Cocok untuk server shared-hosting/CPanel/InfinityFree tanpa setup Node/Composer yang rumit.
2. **Paket Enterprise Laravel (Laravel 10/11 Architecture):** Modul resmi migrasi database, seeders, model Eloquent, controller, Blade views, dan rute REST API.

### Pohon Direktori

```
workpulse/
├── app/
│   ├── Http/Controllers/
│   │   └── WorkPulseController.php     # Controller resmi Laravel (Auth, Validasi, Asesmen, Dashboard)
│   └── Models/
│       ├── User.php                    # Model User (C-Level / Staf, Sektor Startup, Pendanaan)
│       ├── Submission.php              # Model Hasil Asesmen & Status Burnout
│       ├── Answer.php                  # Model Jawaban Indikator Per Pertanyaan
│       ├── FactorDefinition.php        # Model 6 Pilar Kesehatan Organisasi
│       └── QuestionDefinition.php      # Model 36 Indikator Pertanyaan
│
├── database/
│   ├── migrations/
│   │   └── 2026_01_01_000000_create_workpulse_tables.php # Migrasi skema WorkPulse
│   └── seeders/
│       └── WorkPulseSeeder.php         # Seeder 6 faktor, 36 pertanyaan, dan akun demo
│
├── routes/
│   ├── api.php                         # Rute REST API Laravel (v1/register, login, submit, dashboard)
│   └── web.php                         # Rute Web Laravel (Blade dashboard)
│
├── resources/
│   └── views/
│       ├── layouts/app.blade.php       # Layout master Blade Light Mode
│       └── workpulse/index.blade.php   # Blade view aplikasi WorkPulse
│
├── backend/                            # Standalone Native PHP Backend
│   ├── api.php                         # REST API Router native
│   ├── database.sql                    # Skema DDL MySQL lengkap
│   ├── test.php                        # Diagnostik mandiri server & database
│   └── php/
│       ├── config.php                  # Konfigurasi database & JWT
│       ├── db.php                      # PDO Database Wrapper
│       └── validator.php               # Validasi data & perhitungan skor 6 pilar
│
├── Frontend/                           # Frontend Standalone SPA
│   ├── index.html                      # Single Page Application HTML (Modern Light Mode)
│   ├── css/
│   │   ├── auth.css                    # Styling halaman login & registrasi
│   │   └── main.css                    # Master design system Light Mode (Linear/Vercel style)
│   └── js/
│       ├── api.js                      # Fetch client dengan token management
│       ├── auth.js                     # Alur login/register C-Level & Staf
│       ├── form.js                     # Kuesioner dinamis 36 indikator & kalkulator live
│       └── main.js                     # Router visual, visualisasi 5 Chart.js, filter & ekspor CSV
│
├── css/ & js/                          # Folder root (sinkron dengan Frontend/)
└── ARCHITECTURE.md                     # Dokumentasi arsitektur platform
```

---

## 5. Design System: Modern Light Mode SaaS

WorkPulse menerapkan standar estetika **Clean Modern Light Mode** terinspirasi dari UI/UX SaaS modern (seperti Linear, Stripe, dan Vercel Dashboard):

| Elemen Desain | Spesifikasi Token / Nilai | Catatan Implementasi |
|:---|:---|:---|
| **Latar Belakang Utama** | `#F8FAFC` (Slate-50) | Lembut, tidak menyilaukan mata, memberikan kontras natural terhadap kartu. |
| **Latar Belakang Card** | `#FFFFFF` (Pure White) | Kartu terangkat dengan batas halus. |
| **Border & Divider** | `1px solid #E2E8F0` (Slate-200) | Menghindari border hitam tebal / brutalist. |
| **Shadow / Elevasi** | `0 1px 3px rgba(15, 23, 42, 0.05)` | Ambient shadow mikro yang elegan. |
| **Radius Sudut** | `rounded-xl` (12px – 16px) | Memberikan kesan modern dan ramah pengguna. |
| **Tipografi** | `Plus Jakarta Sans` / `Inter` | Hirarki bobot yang tegas (400, 600, 700, 800). |
| **Warna Aksen Primer** | `#6366F1` (Modern Indigo) | Tombol CTA, badge aktif, dan aksen navigasi. |
| **Warna Aksen Sekunder** | `#2563EB` (Clean Royal Blue) | Tautan interaktif, grafik tren, dan metrik stabilitas. |

---

## 6. Panduan Menjalankan Aplikasi

### Opsi A: Menjalankan Menggunakan Laravel Artisan
```bash
# 1. Jalankan migrasi database
php artisan migrate

# 2. Isi database dengan 6 pilar & 36 indikator pertanyaan
php artisan db:seed --class=WorkPulseSeeder

# 3. Jalankan server lokal
php artisan serve
```
Akses di browser: `http://localhost:8000`

### Opsi B: Menjalankan Menggunakan Standalone Native PHP Server
```bash
# 1. Impor database.sql ke MySQL (nama database: workpulse)
# 2. Jalankan built-in PHP server dari root direktori
php -S localhost:8080
```
- Akses Frontend: `http://localhost:8080/Frontend/index.html`
- Diagnostik Backend: `http://localhost:8080/backend/test.php`
