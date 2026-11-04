<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class WorkPulseSeeder extends Seeder
{
    /**
     * Run the database seeds for WorkPulse.
     */
    public function run(): void
    {
        // 1. Seed Factor Definitions (6 Pilar Kesehatan Organisasi)
        $factors = [
            [
                'code' => 'VT',
                'name' => 'Visi & Transparansi',
                'description' => 'Kejelasan roadmap produk, stabilitas runway finansial, dan keterbukaan komunikasi strategis.',
                'role_target' => 'clevel',
                'weight' => 0.35,
                'order_num' => 1,
            ],
            [
                'code' => 'KK',
                'name' => 'Kualitas Kepemimpinan',
                'description' => 'Dukungan manajemen, keadilan delegasi, dan keamanan psikologis (psychological safety).',
                'role_target' => 'staf',
                'weight' => 0.35,
                'order_num' => 2,
            ],
            [
                'code' => 'FI',
                'name' => 'Fasilitas & Infrastruktur',
                'description' => 'Kelayakan stack teknologi, otomasi tools kerja, dan kenyamanan lingkungan kerja (remote/hybrid).',
                'role_target' => 'clevel',
                'weight' => 0.30,
                'order_num' => 3,
            ],
            [
                'code' => 'BK',
                'name' => 'Beban Kerja (Workload)',
                'description' => 'Intensitas deadline, frekuensi overtime/lembur, dan batasan jam kerja sehat (work-life boundaries).',
                'role_target' => 'staf',
                'weight' => 0.35,
                'order_num' => 4,
            ],
            [
                'code' => 'KM',
                'name' => 'Kesejahteraan Mental',
                'description' => 'Tingkat kelelahan emosional, stres kronis, dan ketersediaan ruang pemulihan energi tim.',
                'role_target' => 'staf',
                'weight' => 0.30,
                'order_num' => 5,
            ],
            [
                'code' => 'KP',
                'name' => 'Kompensasi & Karir',
                'description' => 'Kewajaran gaji/ESOP terhadap beban kerja, serta kejelasan jenjang pertumbuhan profesional.',
                'role_target' => 'clevel',
                'weight' => 0.35,
                'order_num' => 6,
            ],
        ];

        foreach ($factors as $factor) {
            DB::table('factor_definitions')->updateOrInsert(
                ['code' => $factor['code']],
                array_merge($factor, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // 2. Seed Question Definitions (36 Indikator: 18 C-Level, 18 Staf)
        $questions = [
            // C-Level: Visi & Transparansi (VT)
            ['VT', 'clevel', 0, 'Roadmap produk dan arah strategis perusahaan dikomunikasikan secara transparan ke seluruh tim.'],
            ['VT', 'clevel', 1, 'Kondisi runway keuangan dan target metrik bisnis dibagikan secara jujur dan berkala.'],
            ['VT', 'clevel', 2, 'Tim memiliki pemahaman yang kuat mengapa prioritas atau pivot bisnis diambil.'],
            ['VT', 'clevel', 3, 'Transparansi target OKR/KPI mampu menjaga komitmen tim tanpa menimbulkan kepanikan.'],
            ['VT', 'clevel', 4, 'Keputusan strategis manajemen puncak selalu konsisten dengan nilai budaya perusahaan.'],
            ['VT', 'clevel', 5, 'Karyawan merasa aman untuk menyuarakan ketidaksepakatan terhadap arah strategi perusahaan.'],

            // C-Level: Fasilitas & Infrastruktur (FI)
            ['FI', 'clevel', 0, 'Infrastruktur cloud, tools otomasi, dan perangkat kerja sangat memadai untuk target engineering.'],
            ['FI', 'clevel', 1, 'Alur kerja CI/CD dan deployment berjalan stabil tanpa menimbulkan tech-debt yang memicu stres.'],
            ['FI', 'clevel', 2, 'Anggaran untuk perangkat pendukung kerja hybrid/remote disediakan dengan layak.'],
            ['FI', 'clevel', 3, 'Sistem keamanan data dan akses dev tools efisien tanpa birokrasi yang menghambat velocity.'],
            ['FI', 'clevel', 4, 'Ruang kerja fisik maupun virtual dirancang mendukung kolaborasi fokus tanpa distraksi berlebih.'],
            ['FI', 'clevel', 5, 'Perusahaan responsif dalam memperbarui software licenses dan peralatan kerja yang usang.'],

            // C-Level: Kompensasi & Karir (KP)
            ['KP', 'clevel', 0, 'Paket kompensasi dan benefit tim kompetitif terhadap standar pasar industri teknologi saat ini.'],
            ['KP', 'clevel', 1, 'Program ESOP/insentif kinerja dipahami dengan baik dan memotivasi retensi talenta kunci.'],
            ['KP', 'clevel', 2, 'Terdapat jalur karir spesialis (individual contributor) yang setara dengan jalur manajerial.'],
            ['KP', 'clevel', 3, 'Penyesuaian remunerasi dan bonus tahunan dievaluasi secara adil berbasis kinerja terukur.'],
            ['KP', 'clevel', 4, 'Perusahaan menyediakan anggaran khusus untuk sertifikasi, pelatihan, dan upskilling tim.'],
            ['KP', 'clevel', 5, 'Karyawan berprestasi mendapatkan promosi dan rekognisi nyata secara transparan.'],

            // Staf: Kualitas Kepemimpinan (KK)
            ['KK', 'staf', 0, 'Lead/manajer saya memberikan arahan tugas yang jelas, terstruktur, dan realistis.'],
            ['KK', 'staf', 1, 'Saya mendapatkan umpan balik (feedback) konstruktif secara berkala dari atasan langsung.'],
            ['KK', 'staf', 2, 'Manajemen menghargai otonomi kerja saya tanpa melakukan micromanagement yang menekan.'],
            ['KK', 'staf', 3, 'Atasan bersikap adil dan tidak membeda-bedakan perlakuan antar anggota tim.'],
            ['KK', 'staf', 4, 'Saya merasa aman secara psikologis untuk mengakui kesalahan teknis tanpa takut disalahkan sepihak.'],
            ['KK', 'staf', 5, 'Pemimpin mendengarkan dan menindaklanjuti keluhan terkait beban kerja yang berlebihan.'],

            // Staf: Beban Kerja (Workload) (BK)
            ['BK', 'staf', 0, 'Volume pekerjaan harian saya berada pada tingkat wajar dan dapat diselesaikan dalam jam kerja normal.'],
            ['BK', 'staf', 1, 'Saya jarang dituntut bekerja lembur (overtime) atau standby pada akhir pekan.'],
            ['BK', 'staf', 2, 'Tenggat waktu sprint/proyek dirancang realistis dengan mempertimbangkan kapasitas riil tim.'],
            ['BK', 'staf', 3, 'Pergantian prioritas mendadak (ad-hoc tasks) jarang mengganggu fokus kerja utama saya.'],
            ['BK', 'staf', 4, 'Saya dapat mengambil cuti atau izin istirahat tanpa rasa bersalah atau diganggu urusan pekerjaan.'],
            ['BK', 'staf', 5, 'Distribusi beban tugas di dalam tim seimbang tanpa ada anggota yang menanggung beban berlebih.'],

            // Staf: Kesejahteraan Mental (KM)
            ['KM', 'staf', 0, 'Saya jarang merasakan kelelahan emosional atau kehabisan energi akibat urusan kantor.'],
            ['KM', 'staf', 1, 'Lingkungan kerja bebas dari intimidasi, toxic behavior, maupun tekanan psikologis negatif.'],
            ['KM', 'staf', 2, 'Saya tetap merasa bersemangat dan termotivasi saat memulai rutinitas kerja di pagi hari.'],
            ['KM', 'staf', 3, 'Perusahaan memberikan perhatian nyata terhadap kesehatan mental dan keseimbangan hidup karyawan.'],
            ['KM', 'staf', 4, 'Saya tidak merasa cemas berlebihan mengenai stabilitas pekerjaan saya di startup ini.'],
            ['KM', 'staf', 5, 'Hubungan antar rekan kerja saling mendukung (supportive) dan tidak sarat intrik politik internal.'],
        ];

        foreach ($questions as $q) {
            DB::table('question_definitions')->updateOrInsert(
                [
                    'factor_code' => $q[0],
                    'role_target' => $q[1],
                    'question_index' => $q[2],
                ],
                [
                    'text' => $q[3],
                    'weight' => 1.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 3. Seed Demo Users
        DB::table('users')->updateOrInsert(
            ['email' => 'founder@workpulse.id'],
            [
                'name' => 'Arya Sena (Founder & CEO)',
                'password' => Hash::make('password123'),
                'startup_name' => 'Zenith AI Analytics',
                'sektor_teknologi' => 'AI/Data Services',
                'pendanaan' => 'Series A',
                'role' => 'clevel',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'engineer@workpulse.id'],
            [
                'name' => 'Dimas Pratama (Senior Software Engineer)',
                'password' => Hash::make('password123'),
                'startup_name' => 'Zenith AI Analytics',
                'sektor_teknologi' => 'AI/Data Services',
                'pendanaan' => 'Series A',
                'role' => 'staf',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}

