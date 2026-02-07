<?php

declare(strict_types=1);

namespace Domain\Services;

use App\Infrastructure\Persistence\Models\KbPelanggaran;
use App\Infrastructure\Persistence\Models\KbApresiasi;
use App\Infrastructure\Persistence\Models\SantriProfile;
use App\Infrastructure\Persistence\Models\SantriPoint;
use App\Infrastructure\Persistence\Models\SantriViolation;
use App\Infrastructure\Persistence\Models\SantriAppreciation;
use Illuminate\Support\Facades\DB;

class PointService
{
    private ExpertSystemService $expertSystem;

    public function __construct(ExpertSystemService $expertSystem)
    {
        $this->expertSystem = $expertSystem;
    }

    /**
     * Add pelanggaran points to a santri
     */
    public function addPelanggaran(
        int $santriId,
        string $pelanggaranKode,
        ?int $reportId = null,
        ?int $verifiedBy = null
    ): SantriViolation {
        $pelanggaran = KbPelanggaran::findByKode($pelanggaranKode);

        if (!$pelanggaran) {
            throw new \InvalidArgumentException("Pelanggaran code not found: {$pelanggaranKode}");
        }

        return DB::transaction(function () use ($santriId, $pelanggaran, $reportId, $verifiedBy) {
            // Create violation record
            $violation = SantriViolation::create([
                'santri_id' => $santriId,
                'report_id' => $reportId,
                'pelanggaran_kode' => $pelanggaran->kode,
                'pelanggaran_nama' => $pelanggaran->nama,
                'poin' => $pelanggaran->poin,
                'konsekuensi' => $pelanggaran->konsekuensi,
                'verified_by' => $verifiedBy,
            ]);

            // Update total points
            $this->updatePoinPelanggaran($santriId, $pelanggaran->poin);

            // Check thresholds
            $this->expertSystem->checkThresholds($santriId);

            return $violation;
        });
    }

    /**
     * Add apresiasi points to a santri
     */
    public function addApresiasi(
        int $santriId,
        string $apresiasiKode,
        ?int $reportId = null,
        ?int $verifiedBy = null
    ): SantriAppreciation {
        $apresiasi = KbApresiasi::findByKode($apresiasiKode);

        if (!$apresiasi) {
            throw new \InvalidArgumentException("Apresiasi code not found: {$apresiasiKode}");
        }

        return DB::transaction(function () use ($santriId, $apresiasi, $reportId, $verifiedBy) {
            // Create appreciation record
            $appreciation = SantriAppreciation::create([
                'santri_id' => $santriId,
                'report_id' => $reportId,
                'apresiasi_kode' => $apresiasi->kode,
                'apresiasi_nama' => $apresiasi->nama,
                'poin' => $apresiasi->poin,
                'reward' => $apresiasi->reward,
                'verified_by' => $verifiedBy,
            ]);

            // Update total points
            $this->updatePoinApresiasi($santriId, $apresiasi->poin);

            // Check thresholds
            $this->expertSystem->checkThresholds($santriId);

            return $appreciation;
        });
    }

    /**
     * Update total poin pelanggaran for a santri
     */
    private function updatePoinPelanggaran(int $santriId, int $poin): void
    {
        $santriPoint = SantriPoint::firstOrCreate(
            ['santri_id' => $santriId],
            ['total_poin_pelanggaran' => 0, 'total_poin_apresiasi' => 0]
        );

        $santriPoint->addPelanggaran($poin);
    }

    /**
     * Update total poin apresiasi for a santri
     */
    private function updatePoinApresiasi(int $santriId, int $poin): void
    {
        $santriPoint = SantriPoint::firstOrCreate(
            ['santri_id' => $santriId],
            ['total_poin_pelanggaran' => 0, 'total_poin_apresiasi' => 0]
        );

        $santriPoint->addApresiasi($poin);
    }

    /**
     * Get point summary for a santri
     */
    public function getPointSummary(int $santriId): array
    {
        $santriPoint = SantriPoint::firstOrCreate(
            ['santri_id' => $santriId],
            ['total_poin_pelanggaran' => 0, 'total_poin_apresiasi' => 0]
        );

        $recentViolations = SantriViolation::where('santri_id', $santriId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentAppreciations = SantriAppreciation::where('santri_id', $santriId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return [
            'total_poin_pelanggaran' => $santriPoint->total_poin_pelanggaran,
            'total_poin_apresiasi' => $santriPoint->total_poin_apresiasi,
            'current_konsekuensi' => $santriPoint->current_konsekuensi_kode,
            'current_reward' => $santriPoint->current_reward_kode,
            'recent_violations' => $recentViolations,
            'recent_appreciations' => $recentAppreciations,
            'violation_count' => SantriViolation::where('santri_id', $santriId)->count(),
            'appreciation_count' => SantriAppreciation::where('santri_id', $santriId)->count(),
        ];
    }

    /**
     * Get all santri with high violation points (for monitoring)
     */
    public function getSantriAtRisk(int $threshold = 50): \Illuminate\Support\Collection
    {
        return SantriPoint::with('santri')
            ->where('total_poin_pelanggaran', '>=', $threshold)
            ->orderBy('total_poin_pelanggaran', 'desc')
            ->get();
    }

    /**
     * Get leaderboard for apresiasi
     */
    public function getApresiasiLeaderboard(int $limit = 10): \Illuminate\Support\Collection
    {
        return SantriPoint::with('santri')
            ->where('total_poin_apresiasi', '>', 0)
            ->orderBy('total_poin_apresiasi', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Reset points for a santri (admin function)
     */
    public function resetPoints(int $santriId, bool $resetPelanggaran = true, bool $resetApresiasi = true): void
    {
        $santriPoint = SantriPoint::where('santri_id', $santriId)->first();

        if (!$santriPoint) {
            return;
        }

        $updates = [];

        if ($resetPelanggaran) {
            $updates['total_poin_pelanggaran'] = 0;
            $updates['current_konsekuensi_kode'] = null;
        }

        if ($resetApresiasi) {
            $updates['total_poin_apresiasi'] = 0;
            $updates['current_reward_kode'] = null;
        }

        $santriPoint->update($updates);
    }

    /**
     * Calculate point statistics for dashboard
     */
    public function getStatistics(): array
    {
        return [
            'total_santri_with_violations' => SantriPoint::where('total_poin_pelanggaran', '>', 0)->count(),
            'total_santri_with_appreciations' => SantriPoint::where('total_poin_apresiasi', '>', 0)->count(),
            'average_violation_points' => SantriPoint::avg('total_poin_pelanggaran') ?? 0,
            'average_appreciation_points' => SantriPoint::avg('total_poin_apresiasi') ?? 0,
            'max_violation_points' => SantriPoint::max('total_poin_pelanggaran') ?? 0,
            'max_appreciation_points' => SantriPoint::max('total_poin_apresiasi') ?? 0,
            'violations_this_month' => SantriViolation::whereMonth('created_at', now()->month)->count(),
            'appreciations_this_month' => SantriAppreciation::whereMonth('created_at', now()->month)->count(),
        ];
    }
}