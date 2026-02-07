<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KbPelanggaran extends Model
{
    use HasFactory;

    protected $table = 'kb_pelanggaran';

    protected $fillable = [
        'kode',
        'nama',
        'poin',
        'konsekuensi',
        'deskripsi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'poin' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByKode($query, string $kode)
    {
        return $query->where('kode', $kode);
    }

    public static function findByKode(string $kode): ?self
    {
        return static::where('kode', $kode)->first();
    }
}

class KbApresiasi extends Model
{
    use HasFactory;

    protected $table = 'kb_apresiasi';

    protected $fillable = [
        'kode',
        'nama',
        'poin',
        'reward',
        'deskripsi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'poin' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function findByKode(string $kode): ?self
    {
        return static::where('kode', $kode)->first();
    }
}

class KbKonselor extends Model
{
    use HasFactory;

    protected $table = 'kb_konselor';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'gejala',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'gejala' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function findByKode(string $kode): ?self
    {
        return static::where('kode', $kode)->first();
    }
}

class KbKonsekuensi extends Model
{
    use HasFactory;

    protected $table = 'kb_konsekuensi';

    protected $fillable = [
        'kode',
        'nama',
        'threshold_min',
        'threshold_max',
        'tindakan',
        'deskripsi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'threshold_min' => 'integer',
            'threshold_max' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getByThreshold(int $poin): ?self
    {
        return static::active()
            ->where('threshold_min', '<=', $poin)
            ->where(function ($query) use ($poin) {
                $query->whereNull('threshold_max')
                      ->orWhere('threshold_max', '>=', $poin);
            })
            ->orderBy('threshold_min', 'desc')
            ->first();
    }
}

class KbReward extends Model
{
    use HasFactory;

    protected $table = 'kb_reward';

    protected $fillable = [
        'kode',
        'nama',
        'threshold_min',
        'threshold_max',
        'reward',
        'deskripsi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'threshold_min' => 'integer',
            'threshold_max' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getByThreshold(int $poin): ?self
    {
        return static::active()
            ->where('threshold_min', '<=', $poin)
            ->where(function ($query) use ($poin) {
                $query->whereNull('threshold_max')
                      ->orWhere('threshold_max', '>=', $poin);
            })
            ->orderBy('threshold_min', 'desc')
            ->first();
    }
}

class KbDiagnosis extends Model
{
    use HasFactory;

    protected $table = 'kb_diagnosis';

    protected $fillable = [
        'kode',
        'nama',
        'penjelasan',
        'rekomendasi',
        'kategori',
        'severity',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByKategori($query, string $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public static function findByKode(string $kode): ?self
    {
        return static::where('kode', $kode)->first();
    }

    public function getSeverityColorAttribute(): string
    {
        return match ($this->severity) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            'critical' => 'danger',
            default => 'secondary',
        };
    }
}

class KbDictionary extends Model
{
    use HasFactory;

    protected $table = 'kb_dictionary';

    protected $fillable = [
        'kata',
        'kode_referensi',
        'tipe',
        'bobot',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'bobot' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByTipe($query, string $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    public static function findByKata(string $kata): ?self
    {
        return static::active()->where('kata', $kata)->first();
    }

    public static function getKatasByKode(string $kode): array
    {
        return static::active()
            ->where('kode_referensi', $kode)
            ->pluck('kata')
            ->toArray();
    }
}

class KbRule extends Model
{
    use HasFactory;

    protected $table = 'kb_rules';

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'conditions',
        'operator',
        'min_match',
        'diagnosis_kode',
        'prioritas',
        'deskripsi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'conditions' => 'array',
            'min_match' => 'integer',
            'prioritas' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function diagnosis()
    {
        return $this->belongsTo(KbDiagnosis::class, 'diagnosis_kode', 'kode');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByKategori($query, string $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('prioritas', 'asc')->orderBy('kode', 'asc');
    }

    public function matchesFacts(array $facts): bool
    {
        $conditions = $this->conditions;
        $matchCount = count(array_intersect($conditions, $facts));

        if ($this->operator === 'AND') {
            // All conditions must be met
            $requiredMatches = $this->min_match > 0 ? $this->min_match : count($conditions);
            return $matchCount >= $requiredMatches;
        }

        // OR operator - at least one match
        return $matchCount > 0;
    }

    public function getMatchedConditions(array $facts): array
    {
        return array_values(array_intersect($this->conditions, $facts));
    }
}