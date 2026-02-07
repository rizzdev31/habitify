<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Models;

use Domain\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Filament Methods
    |--------------------------------------------------------------------------
    */

    public function canAccessPanel(Panel $panel): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return match ($panel->getId()) {
            'superadmin' => $this->role === UserRole::SUPER_ADMIN,
            'bk' => $this->role === UserRole::BK,
            'pengajar' => $this->role === UserRole::PENGAJAR,
            'wali' => $this->role === UserRole::WALI,
            'santri' => $this->role === UserRole::SANTRI,
            default => false,
        };
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function santriProfile(): HasOne
    {
        return $this->hasOne(SantriProfile::class);
    }

    public function pengajarProfile(): HasOne
    {
        return $this->hasOne(PengajarProfile::class);
    }

    public function bkProfile(): HasOne
    {
        return $this->hasOne(BkProfile::class);
    }

    public function waliProfile(): HasOne
    {
        return $this->hasOne(WaliProfile::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'pelapor_id');
    }

    public function validatedReports(): HasMany
    {
        return $this->hasMany(Report::class, 'validated_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getProfileAttribute()
    {
        return match ($this->role) {
            UserRole::SANTRI => $this->santriProfile,
            UserRole::PENGAJAR => $this->pengajarProfile,
            UserRole::BK => $this->bkProfile,
            UserRole::WALI => $this->waliProfile,
            default => null,
        };
    }

    public function getRoleLabelAttribute(): string
    {
        return $this->role->label();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, UserRole $role)
    {
        return $query->where('role', $role);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SUPER_ADMIN;
    }

    public function isBk(): bool
    {
        return $this->role === UserRole::BK;
    }

    public function isPengajar(): bool
    {
        return $this->role === UserRole::PENGAJAR;
    }

    public function isWali(): bool
    {
        return $this->role === UserRole::WALI;
    }

    public function isSantri(): bool
    {
        return $this->role === UserRole::SANTRI;
    }
}