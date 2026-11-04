<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'factor_key',
        'question_index',
        'score',
    ];

    protected $casts = [
        'question_index' => 'integer',
        'score' => 'integer',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}

