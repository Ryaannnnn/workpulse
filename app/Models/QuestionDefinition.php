<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionDefinition extends Model
{
    use HasFactory;

    protected $fillable = [
        'factor_code',
        'role_target',
        'question_index',
        'text',
        'weight',
    ];

    protected $casts = [
        'question_index' => 'integer',
        'weight' => 'float',
    ];

    public function factor()
    {
        return $this->belongsTo(FactorDefinition::class, 'factor_code', 'code');
    }
}

