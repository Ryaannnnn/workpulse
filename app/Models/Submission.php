<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role',
        'score_vt',
        'score_kk',
        'score_fi',
        'score_bk',
        'score_km',
        'score_kp',
        'final_score',
        'burnout_status',
        'raw_scores',
        'submitted_at',
    ];

    protected $casts = [
        'score_vt' => 'float',
        'score_kk' => 'float',
        'score_fi' => 'float',
        'score_bk' => 'float',
        'score_km' => 'float',
        'score_kp' => 'float',
        'final_score' => 'float',
        'raw_scores' => 'array',
        'submitted_at' => 'datetime',
    ];

    /**
     * Get the user that owns this submission.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get answers associated with this submission.
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Helper to get status badge CSS and description.
     */
    public function getStatusMetaAttribute(): array
    {
        return match ($this->burnout_status) {
            'Budaya Kerja Positif' => [
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'color' => '#10B981',
                'desc' => 'Budaya kerja sehat, beban kerja terdistribusi dengan baik, risiko burnout sangat rendah.',
            ],
            'Kondisi Stabil' => [
                'badge' => 'bg-blue-50 text-blue-700 border-blue-200',
                'color' => '#3B82F6',
                'desc' => 'Kondisi organisasi stabil dengan ritme kerja terkelola, namun perlu monitoring berkala.',
            ],
            'Waspada Gejala Burnout' => [
                'badge' => 'bg-amber-50 text-amber-700 border-amber-200',
                'color' => '#F59E0B',
                'desc' => 'Terdeteksi indikasi kelelahan sistemik, jam kerja berlebih, atau ketegangan komunikasi.',
            ],
            'Risiko Burnout Tinggi' => [
                'badge' => 'bg-rose-50 text-rose-700 border-rose-200',
                'color' => '#EF4444',
                'desc' => 'Risiko burnout kritis dan kelelahan mental akut; intervensi manajemen diperlukan segera.',
            ],
            default => [
                'badge' => 'bg-slate-50 text-slate-700 border-slate-200',
                'color' => '#64748B',
                'desc' => 'Status belum dievaluasi.',
            ],
        };
    }
}

