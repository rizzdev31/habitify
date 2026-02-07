<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Models;

use Domain\Enums\ReportStatus;
use Domain\Enums\ReportType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pelapor_id',
        'jenis',
        'laporan_text',
        'status',
        'validated_by',
        'validated_at',
        'validation_notes',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'jenis' => ReportType::class,
            'status' => ReportStatus::class,
            'validated_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function preprocessing(): HasOne
    {
        return $this->hasOne(ReportPreprocessing::class);
    }

    public function entities(): HasMany
    {
        return $this->hasMany(ReportEntity::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(ReportMatch::class);
    }

    public function expertExecutions(): HasMany
    {
        return $this->hasMany(ExpertExecution::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getPreviewAttribute(): string
    {
        return \Str::limit($this->laporan_text, 100);
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    public function getJenisLabelAttribute(): string
    {
        return $this->jenis->label();
    }

    public function getJenisColorAttribute(): string
    {
        return $this->jenis->color();
    }

    public function getDetectedCodesAttribute(): array
    {
        return $this->preprocessing?->detected_codes ?? [];
    }

    public function getConfidenceScoreAttribute(): float
    {
        return $this->preprocessing?->confidence_score ?? 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status', ReportStatus::PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', ReportStatus::APPROVED);
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', ReportStatus::PROCESSED);
    }

    public function scopeByJenis($query, ReportType $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    public function scopeByPelapor($query, int $pelaporId)
    {
        return $query->where('pelapor_id', $pelaporId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status === ReportStatus::PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === ReportStatus::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === ReportStatus::REJECTED;
    }

    public function isProcessed(): bool
    {
        return $this->status === ReportStatus::PROCESSED;
    }

    public function canBeValidated(): bool
    {
        return $this->isPending();
    }

    public function getPelaku(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->entities()->where('role', 'pelaku')->get();
    }

    public function getKorban(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->entities()->where('role', 'korban')->get();
    }

    public function approve(int $validatorId, ?string $notes = null): void
    {
        $this->update([
            'status' => ReportStatus::APPROVED,
            'validated_by' => $validatorId,
            'validated_at' => now(),
            'validation_notes' => $notes,
        ]);
    }

    public function reject(int $validatorId, string $reason): void
    {
        $this->update([
            'status' => ReportStatus::REJECTED,
            'validated_by' => $validatorId,
            'validated_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function markAsProcessed(): void
    {
        $this->update(['status' => ReportStatus::PROCESSED]);
    }
}