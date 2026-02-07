<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Models;

use Domain\Enums\SantriRole;
use Domain\Enums\DiagnosisStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/*
|--------------------------------------------------------------------------
| Report Preprocessing Model
|--------------------------------------------------------------------------
*/

class ReportPreprocessing extends Model
{
    use HasFactory;

    protected $table = 'report_preprocessing';

    protected $fillable = [
        'report_id',
        'text_original',
        'text_cleaned',
        'tokens',
        'tokens_stemmed',
        'detected_codes',
        'detected_entities',
        'confidence_score',
        'matching_details',
    ];

    protected function casts(): array
    {
        return [
            'tokens' => 'array',
            'tokens_stemmed' => 'array',
            'detected_codes' => 'array',
            'detected_entities' => 'array',
            'matching_details' => 'array',
            'confidence_score' => 'decimal:2',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}

/*
|--------------------------------------------------------------------------
| Report Entity Model
|--------------------------------------------------------------------------
*/

class ReportEntity extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'santri_id',
        'role',
        'detected_name',
        'match_confidence',
        'is_confirmed',
    ];

    protected function casts(): array
    {
        return [
            'role' => SantriRole::class,
            'match_confidence' => 'decimal:2',
            'is_confirmed' => 'boolean',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(SantriProfile::class, 'santri_id');
    }

    public function scopePelaku($query)
    {
        return $query->where('role', SantriRole::PELAKU);
    }

    public function scopeKorban($query)
    {
        return $query->where('role', SantriRole::KORBAN);
    }
}

/*
|--------------------------------------------------------------------------
| Report Match Model
|--------------------------------------------------------------------------
*/

class ReportMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'kata_ditemukan',
        'kata_stem',
        'kode_referensi',
        'tipe',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}

/*
|--------------------------------------------------------------------------
| Santri Point Model
|--------------------------------------------------------------------------
*/

class SantriPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'santri_id',
        'total_poin_pelanggaran',
        'total_poin_apresiasi',
        'current_konsekuensi_kode',
        'current_reward_kode',
        'last_konsekuensi_at',
        'last_reward_at',
    ];

    protected function casts(): array
    {
        return [
            'total_poin_pelanggaran' => 'integer',
            'total_poin_apresiasi' => 'integer',
            'last_konsekuensi_at' => 'datetime',
            'last_reward_at' => 'datetime',
        ];
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(SantriProfile::class, 'santri_id');
    }

    public function addPelanggaran(int $poin): void
    {
        $this->increment('total_poin_pelanggaran', $poin);
    }

    public function addApresiasi(int $poin): void
    {
        $this->increment('total_poin_apresiasi', $poin);
    }
}

/*
|--------------------------------------------------------------------------
| Santri Fact Model (Working Memory)
|--------------------------------------------------------------------------
*/

class SantriFact extends Model
{
    use HasFactory;

    protected $fillable = [
        'santri_id',
        'fact_code',
        'fact_type',
        'source_report_id',
        'is_active',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(SantriProfile::class, 'santri_id');
    }

    public function sourceReport(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'source_report_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(function ($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
}

/*
|--------------------------------------------------------------------------
| Santri Violation Model
|--------------------------------------------------------------------------
*/

class SantriViolation extends Model
{
    use HasFactory;

    protected $fillable = [
        'santri_id',
        'report_id',
        'pelanggaran_kode',
        'pelanggaran_nama',
        'poin',
        'konsekuensi',
        'konsekuensi_selesai',
        'konsekuensi_selesai_at',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'poin' => 'integer',
            'konsekuensi_selesai' => 'boolean',
            'konsekuensi_selesai_at' => 'datetime',
        ];
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(SantriProfile::class, 'santri_id');
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'konsekuensi_selesai' => true,
            'konsekuensi_selesai_at' => now(),
        ]);
    }
}

/*
|--------------------------------------------------------------------------
| Santri Appreciation Model
|--------------------------------------------------------------------------
*/

class SantriAppreciation extends Model
{
    use HasFactory;

    protected $fillable = [
        'santri_id',
        'report_id',
        'apresiasi_kode',
        'apresiasi_nama',
        'poin',
        'reward',
        'reward_diberikan',
        'reward_diberikan_at',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'poin' => 'integer',
            'reward_diberikan' => 'boolean',
            'reward_diberikan_at' => 'datetime',
        ];
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(SantriProfile::class, 'santri_id');
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function markAsGiven(): void
    {
        $this->update([
            'reward_diberikan' => true,
            'reward_diberikan_at' => now(),
        ]);
    }
}

/*
|--------------------------------------------------------------------------
| Santri Counseling Model
|--------------------------------------------------------------------------
*/

class SantriCounseling extends Model
{
    use HasFactory;

    protected $table = 'santri_counseling';

    protected $fillable = [
        'santri_id',
        'bk_id',
        'report_id',
        'diagnosis_kode',
        'diagnosis_nama',
        'tanggal_konseling',
        'waktu_mulai',
        'waktu_selesai',
        'catatan_konseling',
        'rekomendasi_tindak_lanjut',
        'jadwal_follow_up',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_konseling' => 'date',
            'jadwal_follow_up' => 'date',
        ];
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(SantriProfile::class, 'santri_id');
    }

    public function bk(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bk_id');
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }
}

/*
|--------------------------------------------------------------------------
| Expert Execution Model
|--------------------------------------------------------------------------
*/

class ExpertExecution extends Model
{
    use HasFactory;

    protected $fillable = [
        'santri_id',
        'report_id',
        'rule_kode',
        'diagnosis_kode',
        'matched_conditions',
        'status',
        'handled_by',
        'handled_at',
        'counseling_notes',
        'follow_up_notes',
        'follow_up_date',
        'wali_notified',
        'wali_notified_at',
    ];

    protected function casts(): array
    {
        return [
            'matched_conditions' => 'array',
            'status' => DiagnosisStatus::class,
            'handled_at' => 'datetime',
            'follow_up_date' => 'date',
            'wali_notified' => 'boolean',
            'wali_notified_at' => 'datetime',
        ];
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(SantriProfile::class, 'santri_id');
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(KbRule::class, 'rule_kode', 'kode');
    }

    public function diagnosis(): BelongsTo
    {
        return $this->belongsTo(KbDiagnosis::class, 'diagnosis_kode', 'kode');
    }

    public function scopePending($query)
    {
        return $query->where('status', DiagnosisStatus::PENDING);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', DiagnosisStatus::IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', DiagnosisStatus::COMPLETED);
    }

    public function isPending(): bool
    {
        return $this->status === DiagnosisStatus::PENDING;
    }

    public function startCounseling(int $bkId): void
    {
        $this->update([
            'status' => DiagnosisStatus::IN_PROGRESS,
            'handled_by' => $bkId,
            'handled_at' => now(),
        ]);
    }

    public function completeCounseling(string $notes, ?string $followUpNotes = null, ?string $followUpDate = null): void
    {
        $this->update([
            'status' => DiagnosisStatus::COMPLETED,
            'counseling_notes' => $notes,
            'follow_up_notes' => $followUpNotes,
            'follow_up_date' => $followUpDate,
        ]);
    }

    public function markWaliNotified(): void
    {
        $this->update([
            'wali_notified' => true,
            'wali_notified_at' => now(),
        ]);
    }
}

/*
|--------------------------------------------------------------------------
| Konsekuensi Execution Model
|--------------------------------------------------------------------------
*/

class KonsekuensiExecution extends Model
{
    use HasFactory;

    protected $fillable = [
        'santri_id',
        'konsekuensi_kode',
        'poin_saat_trigger',
        'tindakan',
        'status',
        'handled_by',
        'handled_at',
        'catatan',
        'wali_notified',
    ];

    protected function casts(): array
    {
        return [
            'poin_saat_trigger' => 'integer',
            'handled_at' => 'datetime',
            'wali_notified' => 'boolean',
        ];
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(SantriProfile::class, 'santri_id');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}

/*
|--------------------------------------------------------------------------
| Reward Execution Model
|--------------------------------------------------------------------------
*/

class RewardExecution extends Model
{
    use HasFactory;

    protected $fillable = [
        'santri_id',
        'reward_kode',
        'poin_saat_trigger',
        'reward',
        'status',
        'given_by',
        'given_at',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'poin_saat_trigger' => 'integer',
            'given_at' => 'datetime',
        ];
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(SantriProfile::class, 'santri_id');
    }

    public function giver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'given_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function markAsGiven(int $giverId, ?string $catatan = null): void
    {
        $this->update([
            'status' => 'given',
            'given_by' => $giverId,
            'given_at' => now(),
            'catatan' => $catatan,
        ]);
    }
}

/*
|--------------------------------------------------------------------------
| Profile Models
|--------------------------------------------------------------------------
*/

class PengajarProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nip',
        'nama_lengkap',
        'jenis_kelamin',
        'no_hp',
        'bidang_studi',
        'jabatan',
        'tanggal_bergabung',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_bergabung' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

class BkProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nip',
        'nama_lengkap',
        'jenis_kelamin',
        'no_hp',
        'spesialisasi',
        'sertifikasi',
        'tanggal_bergabung',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_bergabung' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function counselings()
    {
        return $this->hasMany(SantriCounseling::class, 'bk_id', 'user_id');
    }
}

class WaliProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'santri_id',
        'nama_lengkap',
        'hubungan',
        'no_hp',
        'no_whatsapp',
        'pekerjaan',
        'alamat',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(SantriProfile::class, 'santri_id');
    }
}

/*
|--------------------------------------------------------------------------
| Logging Models
|--------------------------------------------------------------------------
*/

class NotificationLog extends Model
{
    use HasFactory;

    protected $table = 'notifications_log';

    protected $fillable = [
        'user_id',
        'santri_id',
        'type',
        'channel',
        'recipient',
        'message',
        'metadata',
        'status',
        'error_message',
        'sent_at',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(SantriProfile::class, 'santri_id');
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $error,
        ]);
    }
}

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(
        string $action,
        ?int $userId = null,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): self {
        return static::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}