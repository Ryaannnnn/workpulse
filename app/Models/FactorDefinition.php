<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactorDefinition extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'role_target',
        'weight',
        'order_num',
    ];

    protected $casts = [
        'weight' => 'float',
        'order_num' => 'integer',
    ];

    public function questions()
    {
        return $this->hasMany(QuestionDefinition::class, 'factor_code', 'code');
    }
}

