<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    // === RELASI ===
    public function lecturer()
    {
        return $this->hasOne(Lecturer::class);
    }

    // === HELPERS ROLE ===
    public function isDekan(): bool
    {
        return $this->role === 'dekan';
    }

    public function isKaprodi(): bool
    {
        return $this->role === 'kaprodi';
    }

    public function isDosen(): bool
    {
        return $this->role === 'dosen';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['dekan', 'kaprodi']);
    }
}
