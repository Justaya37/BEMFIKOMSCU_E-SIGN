<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'jabatan',
        'signing_pin',
        'signature_image_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'signing_pin',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isSekretaris(): bool
    {
        return $this->role === 'sekretaris';
    }

    public function isKetua(): bool
    {
        return $this->role === 'ketua';
    }

    public function isWakilKetua(): bool
    {
        return $this->role === 'wakil_ketua';
    }

    public function canSign(): bool
    {
        return in_array($this->role, ['ketua', 'wakil_ketua'], true);
    }

    public function hasSignatureImage(): bool
    {
        return !empty($this->signature_image_path);
    }

    public function hasPin(): bool
    {
        return !empty($this->signing_pin);
    }

    public function submittedLetters(): HasMany
    {
        return $this->hasMany(Letter::class, 'submitted_by');
    }

    public function approvedLetters(): HasMany
    {
        return $this->hasMany(Letter::class, 'approved_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}
