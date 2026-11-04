-- ============================================================
--  WorkPulse Database Schema
--  Clinical-grade Work Culture & Burnout Risk Assessment for Tech Startups
--  Engine : MySQL 8.0+ / MariaDB 10.5+
--  Charset: utf8mb4_unicode_ci
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+07:00';

CREATE DATABASE IF NOT EXISTS `workpulse` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `workpulse`;

-- ============================================================
--  1. TABEL USERS (Akun Founder/C-Level & Staf Startup)
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id`               INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `startup_name`     VARCHAR(150)    NOT NULL                    COMMENT 'Nama resmi Startup',
  `email`            VARCHAR(180)    NOT NULL                    COMMENT 'Email kerja / bisnis (unik)',
  `password_hash`    VARCHAR(255)    NOT NULL                    COMMENT 'bcrypt hash kata sandi',
  `role`             ENUM('clevel','staf') NOT NULL              COMMENT 'Peran pengguna: C-Level / Staf',
  `lokasi`           VARCHAR(100)    DEFAULT NULL                COMMENT 'Kota / Hub operasional',
  `sektor_teknologi` VARCHAR(80)     DEFAULT NULL                COMMENT 'Sektor teknologi (SaaS, FinTech, EdTech, dll)',
  `pendanaan`        BIGINT UNSIGNED DEFAULT NULL                COMMENT 'Rata-rata pendanaan / funding (IDR)',
  `is_active`        TINYINT(1)      NOT NULL DEFAULT 1,
  `created_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`),
  KEY `idx_role` (`role`),
  KEY `idx_sektor` (`sektor_teknologi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Akun pengguna WorkPulse (C-Level & Staf Startup)';


-- ============================================================
--  2. TABEL SUBMISSIONS (Header Pengiriman Evaluasi Budaya Kerja)
-- ============================================================
CREATE TABLE IF NOT EXISTS `submissions` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`         INT UNSIGNED NOT NULL                 COMMENT 'FK -> users.id',
  `role`            ENUM('clevel','staf') NOT NULL        COMMENT 'Peran saat mengisi formulir',
  `status`          ENUM('draft','submitted','validated','rejected')
                                NOT NULL DEFAULT 'submitted',
  `validation_pass` TINYINT(1)  NOT NULL DEFAULT 0        COMMENT '1 = lolos verifikasi backend',
  `submitted_at`    DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `validated_at`    DATETIME    DEFAULT NULL,
  `notes`           TEXT        DEFAULT NULL              COMMENT 'Catatan klinis / asesmen sistem',

  -- Skor per 6 Faktor Budaya & Risiko Burnout (Normalisasi 0.0000 - 1.0000)
  `score_vt`        DECIMAL(5,4) DEFAULT NULL COMMENT 'Visi & Transparansi — VT (C-Level)',
  `score_kk`        DECIMAL(5,4) DEFAULT NULL COMMENT 'Kualitas Kepemimpinan — KK (Staf)',
  `score_fi`        DECIMAL(5,4) DEFAULT NULL COMMENT 'Fasilitas & Infrastruktur — FI (C-Level)',
  `score_bk`        DECIMAL(5,4) DEFAULT NULL COMMENT 'Beban Kerja (Workload) — BK (Staf)',
  `score_km`        DECIMAL(5,4) DEFAULT NULL COMMENT 'Kesejahteraan Mental — KM (Staf)',
  `score_kp`        DECIMAL(5,4) DEFAULT NULL COMMENT 'Kompensasi & Karir — KP (C-Level)',
  `score_composite` DECIMAL(5,4) DEFAULT NULL COMMENT 'Skor komposit tertimbang 0–1',
  `score_on_5`      DECIMAL(4,2) DEFAULT NULL COMMENT 'Skor indeks budaya kerja skala 0–5',
  `health_category` VARCHAR(50)  DEFAULT NULL COMMENT 'Status risiko burnout',

  PRIMARY KEY (`id`),
  KEY `idx_user`   (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_submitted` (`submitted_at`),
  CONSTRAINT `fk_sub_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Header hasil asesmen budaya kerja & risiko burnout WorkPulse';


-- ============================================================
--  3. TABEL ANSWERS (Jawaban Detail per Pertanyaan Asesmen)
-- ============================================================
CREATE TABLE IF NOT EXISTS `answers` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `submission_id` INT UNSIGNED NOT NULL                   COMMENT 'FK -> submissions.id',
  `factor_key`    ENUM('VT','KK','FI','BK','KM','KP') NOT NULL COMMENT 'Kode faktor budaya kerja',
  `question_id`   VARCHAR(20)  NOT NULL                   COMMENT 'ID pertanyaan: vt_wp1 ... kp_wp6',
  `question_code` VARCHAR(10)  NOT NULL                   COMMENT 'Kode codebook: WP-01 ... WP-36',
  `answer_value`  TINYINT      NOT NULL                   COMMENT 'Skor Likert (1–5)',
  `scale_max`     TINYINT      NOT NULL DEFAULT 5         COMMENT 'Batas atas skala (5)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_sub_question` (`submission_id`, `question_id`),
  KEY `idx_factor`   (`factor_key`),
  KEY `idx_qid`      (`question_id`),
  CONSTRAINT `fk_ans_sub` FOREIGN KEY (`submission_id`) REFERENCES `submissions`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_answer_range` CHECK (`answer_value` >= 1 AND `answer_value` <= `scale_max`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Jawaban granular responden per faktor asesmen';


-- ============================================================
--  4. TABEL VALIDATION_LOG (Audit Log Validasi Sistem)
-- ============================================================
CREATE TABLE IF NOT EXISTS `validation_log` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `submission_id` INT UNSIGNED NOT NULL,
  `level`         ENUM('error','warning','info') NOT NULL,
  `factor_key`    VARCHAR(10)  DEFAULT NULL,
  `field_name`    VARCHAR(30)  DEFAULT NULL,
  `error_code`    VARCHAR(40)  NOT NULL,
  `message`       TEXT         NOT NULL,
  `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vlog_sub`   (`submission_id`),
  KEY `idx_vlog_level` (`level`),
  CONSTRAINT `fk_vlog_sub` FOREIGN KEY (`submission_id`) REFERENCES `submissions`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Log integritas dan peringatan disparitas jawaban kuesioner';


-- ============================================================
--  5. TABEL FACTOR_DEFINITIONS (6 Pilar Budaya Kerja & Burnout)
-- ============================================================
CREATE TABLE IF NOT EXISTS `factor_definitions` (
  `key`         VARCHAR(5)   NOT NULL,
  `label`       VARCHAR(80)  NOT NULL,
  `role`        ENUM('clevel','staf') NOT NULL,
  `weight`      DECIMAL(4,2) NOT NULL COMMENT 'Bobot skor komposit (Total = 1.00)',
  `scale_max`   TINYINT      NOT NULL DEFAULT 5,
  `description` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Definisi 6 pilar asesmen budaya organisasi WorkPulse';

INSERT INTO `factor_definitions` (`key`, `label`, `role`, `weight`, `scale_max`, `description`) VALUES
('VT', 'Visi & Transparansi',           'clevel', 0.20, 5, 'Kejelasan arah strategis, visi perusahaan, dan keterbukaan komunikasi operasional founder.'),
('KK', 'Kualitas Kepemimpinan',        'staf',   0.18, 5, 'Efektivitas manajerial, empati pimpinan, dan keadilan dalam perlakuan tim kerja.'),
('FI', 'Fasilitas & Infrastruktur',    'clevel', 0.15, 5, 'Kecukupan tooling modern, stabilitas infrastruktur cloud, dan ergonomi ruang kerja.'),
('BK', 'Beban Kerja (Workload)',       'staf',   0.17, 5, 'Intensitas sprint, ekspektasi jam kerja berlebih, dan mitigasi risiko kelelahan sistemik.'),
('KM', 'Kesejahteraan Mental',         'staf',   0.15, 5, 'Keamanan psikologis (psychological safety), pencegahan stres kronis, dan iklim kerja suportif.'),
('KP', 'Kompensasi & Karir',           'clevel', 0.15, 5, 'Keadilan remunerasi startup, skema insentif/equity, serta transparansi jalur promosi.')
ON DUPLICATE KEY UPDATE `label`=VALUES(`label`), `weight`=VALUES(`weight`), `description`=VALUES(`description`);


-- ============================================================
--  6. TABEL QUESTION_DEFINITIONS (36 Indikator Klinis Kuesioner)
-- ============================================================
CREATE TABLE IF NOT EXISTS `question_definitions` (
  `id`          SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `question_id` VARCHAR(20)  NOT NULL,
  `code`        VARCHAR(10)  NOT NULL,
  `factor_key`  VARCHAR(5)   NOT NULL,
  `text_id`     TEXT         NOT NULL COMMENT 'Teks pertanyaan Bahasa Indonesia',
  `scale_max`   TINYINT      NOT NULL DEFAULT 5,
  `is_required` TINYINT(1)   NOT NULL DEFAULT 1,
  `sort_order`  SMALLINT     NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_qid`  (`question_id`),
  UNIQUE KEY `uq_code` (`code`),
  KEY `idx_factor` (`factor_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Bank 36 pertanyaan kuesioner budaya startup & burnout';

INSERT INTO `question_definitions` (`question_id`,`code`,`factor_key`,`text_id`,`scale_max`,`sort_order`) VALUES
-- VT: Visi & Transparansi (C-Level, Skala 1–5)
('vt_wp1','WP-01','VT','Founder dan jajaran C-Level mengomunikasikan arah strategis dan runway perusahaan secara transparan kepada tim.',5,1),
('vt_wp2','WP-02','VT','Setiap divisi memiliki target OKR/KPI yang terukur, realistis, dan selaras dengan visi jangka panjang startup.',5,2),
('vt_wp3','WP-03','VT','Perubahan prioritas bisnis atau pivot strategi disampaikan dengan alasan yang jelas dan terbuka.',5,3),
('vt_wp4','WP-04','VT','Manajemen menerapkan kebijakan keterbukaan metrik performa produk dan pertumbuhan bisnis secara berkala.',5,4),
('vt_wp5','WP-05','VT','Keputusan penting organisasi diambil melalui pertimbangan data yang objektif dan transparan bagi pemangku kepentingan.',5,5),
('vt_wp6','WP-06','VT','Karyawan di seluruh tingkatan memahami peran kontribusi kerja mereka terhadap pencapaian misi startup.',5,6),

-- KK: Kualitas Kepemimpinan (Staf, Skala 1–5)
('kk_wp1','WP-07','KK','Pimpinan langsung saya memberikan arahan kerja yang jelas tanpa melakukan micromanagement berlebihan.',5,7),
('kk_wp2','WP-08','KK','Pimpinan bersikap suportif dan mendengarkan kendala operasional yang dihadapi anggota tim.',5,8),
('kk_wp3','WP-09','KK','Pimpinan memberikan apresiasi dan pengakuan yang layak atas kontribusi serta keberhasilan anggota tim.',5,9),
('kk_wp4','WP-10','KK','Pimpinan mampu mengelola konflik tim secara objektif, adil, dan tanpa perlakuan pilih kasih.',5,10),
('kk_wp5','WP-11','KK','Pimpinan terbuka menerima kritik membangun serta saran inovasi dari staf teknis.',5,11),
('kk_wp6','WP-12','KK','Pimpinan menunjukkan keteladanan profesional yang menginspirasi rasa percaya dan loyalitas staf.',5,12),

-- FI: Fasilitas & Infrastruktur (C-Level, Skala 1–5)
('fi_wp1','WP-13','FI','Perusahaan menyediakan perangkat kerja (laptop/hardware) dengan spesifikasi yang memadai untuk kebutuhan engineering & tim.',5,13),
('fi_wp2','WP-14','FI','Perusahaan berinvestasi pada lisensi perangkat lunak, cloud platform, dan tooling modern pendukung produktivitas.',5,14),
('fi_wp3','WP-15','FI','Infrastruktur teknologi dan sistem dokumentasi pengetahuan (knowledge base) internal tertata dengan rapi.',5,15),
('fi_wp4','WP-16','FI','Lingkungan kerja (kantor fisik maupun tunjangan remote/hybrid) dirancang ergonomis dan nyaman untuk bekerja.',5,16),
('fi_wp5','WP-17','FI','Tersedia alokasi anggaran yang cukup untuk keamanan data, compliance, dan kesinambungan infrastruktur teknologi.',5,17),
('fi_wp6','WP-18','FI','Sistem otomatisasi alur kerja (CI/CD, project management tools) membantu meminimalkan beban tugas manual yang berulang.',5,18),

-- BK: Beban Kerja / Workload (Staf, Skala 1–5)
('bk_wp1','WP-19','BK','Volume tugas dan beban sprint mingguan dapat diselesaikan dalam jam kerja normal tanpa tuntutan lembur terus-menerus.',5,19),
('bk_wp2','WP-20','BK','Tenggat waktu (deadline) proyek direncanakan dengan mempertimbangkan kapasitas riil kapasitas tim pengembang.',5,20),
('bk_wp3','WP-21','BK','Saya jarang merasa dituntut untuk merespons pesan kerja darurat di luar jam kerja resmi atau saat akhir pekan.',5,21),
('bk_wp4','WP-22','BK','Distribusi pekerjaan di dalam tim berlangsung seimbang dan tidak terpusat pada segelintir individu kunci saja.',5,22),
('bk_wp5','WP-23','BK','Saya memiliki waktu istirahat yang cukup untuk pulih kembali setelah fase sprint atau perilisan fitur besar.',5,23),
('bk_wp6','WP-24','BK','Beban multi-tasking dalam menangani beberapa proyek sekaligus tidak menimbulkan stres ekstrem yang mengganggu konsentrasi.',5,24),

-- KM: Kesejahteraan Mental (Staf, Skala 1–5)
('km_wp1','WP-25','KM','Saya merasa aman secara psikologis untuk mengakui kesalahan atau bertanya tanpa khawatir dihakimi atau disalahkan.',5,25),
('km_wp2','WP-26','KM','Lingkungan kerja startup kami bebas dari budaya toxic, intimidasi, maupun tekanan emosional negatif.',5,26),
('km_wp3','WP-27','KM','Saya tidak merasakan gejala kelelahan fisik dan mental (exhaustion) yang berkepanjangan akibat pekerjaan.',5,27),
('km_wp4','WP-28','KM','Rekan-rekan kerja saling mendukung secara empati saat ada anggota tim yang mengalami tekanan pekerjaan berat.',5,28),
('km_wp5','WP-29','KM','Perusahaan peduli terhadap kesehatan mental staf dan menyediakan ruang atau inisiatif kesejahteraan emosional.',5,29),
('km_wp6','WP-30','KM','Saya tetap merasa antusias dan termotivasi saat memulai rutinitas kerja harian di startup ini.',5,30),

-- KP: Kompensasi & Karir (C-Level, Skala 1–5)
('kp_wp1','WP-31','KP','Struktur gaji pokok dan tunjangan di startup ini kompetitif jika dibandingkan dengan standar industri teknologi sejenis.',5,31),
('kp_wp2','WP-32','KP','Perusahaan memiliki skema kepemilikan saham (ESOP) atau bonus kinerja yang terstruktur secara transparan.',5,32),
('kp_wp3','WP-33','KP','Tersedia jalur jenjang karir (career ladder) dan kriteria evaluasi promosi yang terdefinisi dengan jelas.',5,33),
('kp_wp4','WP-34','KP','Perusahaan mengalokasikan anggaran untuk pelatihan, sertifikasi teknis, atau program peningkatan keahlian tim.',5,34),
('kp_wp5','WP-35','KP','Peninjauan kompensasi berkala dilakukan secara objektif berdasarkan kinerja dan kontribusi nyata individu.',5,35),
('kp_wp6','WP-36','KP','Manajemen memiliki strategi retensi talenta yang baik untuk mencegah tingkat pergantian karyawan (turnover) tinggi.',5,36)
ON DUPLICATE KEY UPDATE `text_id`=VALUES(`text_id`), `sort_order`=VALUES(`sort_order`);


-- ============================================================
--  7. VIEW: v_submission_summary (Agregat Ringkasan Penilaian)
-- ============================================================
CREATE OR REPLACE VIEW `v_submission_summary` AS
SELECT
  s.id                AS submission_id,
  u.id                AS user_id,
  u.startup_name,
  u.email,
  u.lokasi,
  u.sektor_teknologi,
  u.pendanaan,
  s.role,
  s.status,
  s.validation_pass,
  s.submitted_at,
  s.score_vt,
  s.score_kk,
  s.score_fi,
  s.score_bk,
  s.score_km,
  s.score_kp,
  s.score_composite,
  s.score_on_5,
  s.health_category
FROM submissions s
JOIN users u ON u.id = s.user_id;


-- ============================================================
--  8. VIEW: v_factor_averages (Rata-rata 6 Pilar Ekosistem)
-- ============================================================
CREATE OR REPLACE VIEW `v_factor_averages` AS
SELECT
  ROUND(AVG(score_vt), 4)        AS avg_vt,
  ROUND(AVG(score_kk), 4)        AS avg_kk,
  ROUND(AVG(score_fi), 4)        AS avg_fi,
  ROUND(AVG(score_bk), 4)        AS avg_bk,
  ROUND(AVG(score_km), 4)        AS avg_km,
  ROUND(AVG(score_kp), 4)        AS avg_kp,
  ROUND(AVG(score_composite), 4) AS avg_composite,
  COUNT(*)                       AS total_submissions
FROM submissions
WHERE validation_pass = 1;

SET FOREIGN_KEY_CHECKS = 1;
