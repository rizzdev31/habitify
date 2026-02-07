<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class SantriProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nisn',
        'nama_lengkap',
        'nama_panggilan',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'no_hp',
        'no_whatsapp_wali',
        'nama_wali',
        'kelas',
        'kamar',
        'tahun_masuk',
        'status',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tahun_masuk' => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function waliProfile(): HasOne
    {
        return $this->hasOne(WaliProfile::class, 'santri_id');
    }

    public function points(): HasOne
    {
        return $this->hasOne(SantriPoint::class, 'santri_id');
    }

    public function facts(): HasMany
    {
        return $this->hasMany(SantriFact::class, 'santri_id');
    }

    public function activeFacts(): HasMany
    {
        return $this->hasMany(SantriFact::class, 'santri_id')->where('is_active', true);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(SantriViolation::class, 'santri_id');
    }

    public function appreciations(): HasMany
    {
        return $this->hasMany(SantriAppreciation::class, 'santri_id');
    }

    public function counselings(): HasMany
    {
        return $this->hasMany(SantriCounseling::class, 'santri_id');
    }

    public function reportEntities(): HasMany
    {
        return $this->hasMany(ReportEntity::class, 'santri_id');
    }

    public function expertExecutions(): HasMany
    {
        return $this->hasMany(ExpertExecution::class, 'santri_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(): string
    {
        return $this->nama_lengkap;
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->nama_panggilan ?? $this->nama_lengkap;
    }

    public function getAgeAttribute(): ?int
    {
        if (!$this->tanggal_lahir) {
            return null;
        }
        return $this->tanggal_lahir->age;
    }

    public function getTotalPoinPelanggaranAttribute(): int
    {
        return $this->points?->total_poin_pelanggaran ?? 0;
    }

    public function getTotalPoinApresiasiAttribute(): int
    {
        return $this->points?->total_poin_apresiasi ?? 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeByKelas($query, string $kelas)
    {
        return $query->where('kelas', $kelas);
    }

    public function scopeByKamar($query, string $kamar)
    {
        return $query->where('kamar', $kamar);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nama_lengkap', 'like', "%{$search}%")
              ->orWhere('nama_panggilan', 'like', "%{$search}%")
              ->orWhere('nisn', 'like', "%{$search}%");
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function getActiveFactCodes(): array
    {
        return $this->activeFacts->pluck('fact_code')->toArray();
    }

    public function hasFact(string $factCode): bool
    {
        return $this->activeFacts()->where('fact_code', $factCode)->exists();
    }

    public function isActive(): bool
    {
        return $this->status === 'aktif';
    }
}