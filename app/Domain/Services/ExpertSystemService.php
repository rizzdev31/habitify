<?php

declare(strict_types=1);

namespace Domain\Services;

use App\Infrastructure\Persistence\Models\KbRule;
use App\Infrastructure\Persistence\Models\KbDiagnosis;
use App\Infrastructure\Persistence\Models\KbKonsekuensi;
use App\Infrastructure\Persistence\Models\KbReward;
use App\Infrastructure\Persistence\Models\SantriProfile;
use App\Infrastructure\Persistence\Models\SantriFact;
use App\Infrastructure\Persistence\Models\SantriPoint;
use App\Infrastructure\Persistence\Models\ExpertExecution;
use App\Infrastructure\Persistence\Models\KonsekuensiExecution;
use App\Infrastructure\Persistence\Models\RewardExecution;
use Domain\Enums\DiagnosisStatus;
use Illuminate\Support\Collection;

class ExpertSystemService
{
    /**
     * Main entry point: Check and execute rules for a santri
     */
    public function processRulesForSantri(int $santriId, ?int $reportId = null): ExpertSystemResult
    {
        // Step 1: Load Working Memory (active facts)
        $workingMemory = $this->loadWorkingMemory($santriId);

        // Step 2: Load all active rules ordered by priority
        $rules = $this->loadRules();

        // Step 3: Match rules against working memory
        $matchedRules = $this->matchRules($rules, $workingMemory);

        // Step 4: Filter out already executed rules
        $newRules = $this->filterExecutedRules($matchedRules, $santriId);

        // Step 5: Fire new rules
        $firedRules = [];
        foreach ($newRules as $rule) {
            $execution = $this->fireRule($rule, $santriId, $reportId, $workingMemory);
            if ($execution) {
                $firedRules[] = $execution;
            }
        }

        // Step 6: Check point thresholds
        $thresholdResults = $this->checkThresholds($santriId);

        return new ExpertSystemResult(
            santriId: $santriId,
            workingMemory: $workingMemory,
            matchedRules: $matchedRules->pluck('kode')->toArray(),
            firedRules: $firedRules,
            thresholdResults: $thresholdResults
        );
    }

    /**
     * Load active facts for a santri (Working Memory)
     */
    public function loadWorkingMemory(int $santriId): array
    {
        return SantriFact::where('santri_id', $santriId)
            ->active()
            ->pluck('fact_code')
            ->unique()
            ->toArray();
    }

    /**
     * Load all active rules ordered by priority
     */
    public function loadRules(): Collection
    {
        return KbRule::active()
            ->ordered()
            ->get();
    }

    /**
     * Match rules against working memory using Forward Chaining
     */
    public function matchRules(Collection $rules, array $workingMemory): Collection
    {
        return $rules->filter(function (KbRule $rule) use ($workingMemory) {
            return $rule->matchesFacts($workingMemory);
        });
    }

    /**
     * Filter out rules that have already been executed for this santri
     */
    public function filterExecutedRules(Collection $matchedRules, int $santriId): Collection
    {
        $executedRuleCodes = ExpertExecution::where('santri_id', $santriId)
            ->pluck('rule_kode')
            ->toArray();

        return $matchedRules->filter(function (KbRule $rule) use ($executedRuleCodes) {
            return !in_array($rule->kode, $executedRuleCodes);
        });
    }

    /**
     * Fire a rule and create execution record
     */
    public function fireRule(KbRule $rule, int $santriId, ?int $reportId, array $workingMemory): ?ExpertExecution
    {
        $diagnosis = KbDiagnosis::findByKode($rule->diagnosis_kode);

        if (!$diagnosis) {
            return null;
        }

        $matchedConditions = $rule->getMatchedConditions($workingMemory);

        return ExpertExecution::create([
            'santri_id' => $santriId,
            'report_id' => $reportId,
            'rule_kode' => $rule->kode,
            'diagnosis_kode' => $rule->diagnosis_kode,
            'matched_conditions' => $matchedConditions,
            'status' => DiagnosisStatus::PENDING,
        ]);
    }

    /**
     * Add facts to working memory for a santri
     */
    public function addFacts(int $santriId, array $factCodes, string $factType, ?int $reportId = null): array
    {
        $addedFacts = [];
        $expiresAt = now()->addMonths(config('expert_system.fact_expiry_months', 6));

        foreach ($factCodes as $code) {
            // Check if fact already exists and is active
            $existingFact = SantriFact::where('santri_id', $santriId)
                ->where('fact_code', $code)
                ->where('is_active', true)
                ->first();

            if (!$existingFact) {
                $fact = SantriFact::create([
                    'santri_id' => $santriId,
                    'fact_code' => $code,
                    'fact_type' => $factType,
                    'source_report_id' => $reportId,
                    'is_active' => true,
                    'expires_at' => $expiresAt,
                ]);

                $addedFacts[] = $fact;
            }
        }

        return $addedFacts;
    }

    /**
     * Check point thresholds for konsekuensi and rewards
     */
    public function checkThresholds(int $santriId): array
    {
        $result = [
            'konsekuensi' => null,
            'reward' => null,
        ];

        $santriPoint = SantriPoint::firstOrCreate(
            ['santri_id' => $santriId],
            ['total_poin_pelanggaran' => 0, 'total_poin_apresiasi' => 0]
        );

        // Check Konsekuensi threshold
        $konsekuensi = $this->checkKonsekuensiThreshold($santriId, $santriPoint);
        if ($konsekuensi) {
            $result['konsekuensi'] = $konsekuensi;
        }

        // Check Reward threshold
        $reward = $this->checkRewardThreshold($santriId, $santriPoint);
        if ($reward) {
            $result['reward'] = $reward;
        }

        return $result;
    }

    /**
     * Check if santri has reached a new konsekuensi threshold
     */
    private function checkKonsekuensiThreshold(int $santriId, SantriPoint $santriPoint): ?KonsekuensiExecution
    {
        $poin = $santriPoint->total_poin_pelanggaran;
        $currentKode = $santriPoint->current_konsekuensi_kode;

        $konsekuensi = KbKonsekuensi::getByThreshold($poin);

        if (!$konsekuensi) {
            return null;
        }

        // Only trigger if it's a new threshold level
        if ($konsekuensi->kode === $currentKode) {
            return null;
        }

        // Check if already executed
        $alreadyExecuted = KonsekuensiExecution::where('santri_id', $santriId)
            ->where('konsekuensi_kode', $konsekuensi->kode)
            ->exists();

        if ($alreadyExecuted) {
            return null;
        }

        // Update current konsekuensi level
        $santriPoint->update([
            'current_konsekuensi_kode' => $konsekuensi->kode,
            'last_konsekuensi_at' => now(),
        ]);

        // Create execution record
        return KonsekuensiExecution::create([
            'santri_id' => $santriId,
            'konsekuensi_kode' => $konsekuensi->kode,
            'poin_saat_trigger' => $poin,
            'tindakan' => $konsekuensi->tindakan,
            'status' => 'pending',
        ]);
    }

    /**
     * Check if santri has reached a new reward threshold
     */
    private function checkRewardThreshold(int $santriId, SantriPoint $santriPoint): ?RewardExecution
    {
        $poin = $santriPoint->total_poin_apresiasi;
        $currentKode = $santriPoint->current_reward_kode;

        $reward = KbReward::getByThreshold($poin);

        if (!$reward) {
            return null;
        }

        // Only trigger if it's a new threshold level
        if ($reward->kode === $currentKode) {
            return null;
        }

        // Check if already executed
        $alreadyExecuted = RewardExecution::where('santri_id', $santriId)
            ->where('reward_kode', $reward->kode)
            ->exists();

        if ($alreadyExecuted) {
            return null;
        }

        // Update current reward level
        $santriPoint->update([
            'current_reward_kode' => $reward->kode,
            'last_reward_at' => now(),
        ]);

        // Create execution record
        return RewardExecution::create([
            'santri_id' => $santriId,
            'reward_kode' => $reward->kode,
            'poin_saat_trigger' => $poin,
            'reward' => $reward->reward,
            'status' => 'pending',
        ]);
    }

    /**
     * Get all pending diagnoses for BK dashboard
     */
    public function getPendingDiagnoses(): Collection
    {
        return ExpertExecution::with(['santri', 'diagnosis', 'rule'])
            ->pending()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get diagnosis statistics
     */
    public function getDiagnosisStatistics(): array
    {
        return [
            'pending' => ExpertExecution::pending()->count(),
            'in_progress' => ExpertExecution::inProgress()->count(),
            'completed_today' => ExpertExecution::completed()
                ->whereDate('handled_at', today())
                ->count(),
            'completed_this_week' => ExpertExecution::completed()
                ->whereBetween('handled_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ];
    }

    /**
     * Deactivate old facts (scheduled job)
     */
    public function deactivateExpiredFacts(): int
    {
        return SantriFact::where('is_active', true)
            ->where('expires_at', '<', now())
            ->update(['is_active' => false]);
    }

    /**
     * Re-evaluate all rules for a santri (useful after manual fact changes)
     */
    public function reEvaluateSantri(int $santriId): ExpertSystemResult
    {
        return $this->processRulesForSantri($santriId);
    }
}

/**
 * Value Object for Expert System execution result
 */
class ExpertSystemResult
{
    public function __construct(
        public readonly int $santriId,
        public readonly array $workingMemory,
        public readonly array $matchedRules,
        public readonly array $firedRules,
        public readonly array $thresholdResults,
    ) {}

    public function toArray(): array
    {
        return [
            'santri_id' => $this->santriId,
            'working_memory' => $this->workingMemory,
            'matched_rules' => $this->matchedRules,
            'fired_rules' => array_map(fn($r) => $r->toArray(), $this->firedRules),
            'threshold_results' => $this->thresholdResults,
        ];
    }

    public function hasFiredRules(): bool
    {
        return count($this->firedRules) > 0;
    }

    public function hasNewKonsekuensi(): bool
    {
        return $this->thresholdResults['konsekuensi'] !== null;
    }

    public function hasNewReward(): bool
    {
        return $this->thresholdResults['reward'] !== null;
    }

    public function getDiagnosisCodes(): array
    {
        return array_map(fn($r) => $r->diagnosis_kode, $this->firedRules);
    }
}