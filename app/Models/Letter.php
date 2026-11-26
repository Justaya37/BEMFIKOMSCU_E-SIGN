<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Letter extends Model
{
    use HasFactory;

    protected $fillable = [
        'verification_token',
        'letter_number',
        'title',
        'category',
        'letter_date',
        'event_name',
        'draft_file_path',
        'signed_file_path',
        'file_hash',
        'status',
        'submitted_by',
        'approved_by',
        'approved_at',
        'rejection_or_revoke_reason',
        'stamp_page',
        'stamp_position',
        'qr_position',
        'cap_x',
        'cap_y',
        'cap_width',
        'include_signature_img',
    ];

    protected $attributes = [
        'stamp_page' => 1,
        'cap_x' => 28.0,
        'cap_y' => 195.0,
        'cap_width' => 38.0,
        'qr_position' => 'bottom_left',
        'include_signature_img' => false,
    ];

    protected function casts(): array
    {
        return [
            'letter_date' => 'date',
            'approved_at' => 'datetime',
            'stamp_page' => 'integer',
            'cap_x' => 'float',
            'cap_y' => 'float',
            'cap_width' => 'float',
            'include_signature_img' => 'boolean',
        ];
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(LetterRecipient::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class)->orderByDesc('created_at');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isRevoked(): bool
    {
        return $this->status === 'revoked';
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'surat_izin' => 'Surat Izin',
            'surat_tugas' => 'Surat Tugas',
            'surat_undangan' => 'Surat Undangan',
            'surat_keterangan' => 'Surat Keterangan',
            default => 'Lainnya',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Pengesahan',
            'approved' => 'Sah / Disahkan',
            'rejected' => 'Ditolak',
            'revoked' => 'Pengesahan Dicabut',
            default => ucfirst($this->status),
        };
    }
}
