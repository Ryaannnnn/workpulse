<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'startup_name',
        'lokasi',
        'sektor_teknologi',
        'pendanaan',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get all submissions created by this user.
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Get the latest submission of this user.
     */
    public function latestSubmission()
    {
        return $this->hasOne(Submission::class)->latestOfMany();
    }

    /**
     * Helper to check if user is C-Level.
     */
    public function isCLevel(): bool
    {
        return $this->role === 'clevel';
    }

    /**
     * Helper to check if user is Staf.
     */
    public function isStaf(): bool
    {
        return $this->role === 'staf';
    }
}

