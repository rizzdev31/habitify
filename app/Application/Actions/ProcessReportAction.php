<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Infrastructure\Persistence\Models\Report;
use App\Infrastructure\Persistence\Models\ReportPreprocessing;
use App\Infrastructure\Persistence\Models\ReportEntity;
use App\Infrastructure\Persistence\Models\ReportMatch;
use App\Infrastructure\Persistence\Models\SantriFact;
use App\Infrastructure\Persistence\Models\SantriViolation;
use App\Infrastructure\Persistence\Models\SantriAppreciation;
use App\Infrastructure\Persistence\Models\KbPelanggaran;
use App\Infrastructure\Persistence\Models\KbApresiasi;
use Domain\Services\PreprocessingService;
use Domain\Services\ExpertSystemService;
use Domain\Services\PointService;
use Domain\Enums\ReportType;
use Domain\Enums\ReportStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessReportAction
{
    public function __construct(
        private PreprocessingService $preprocessingService,
        private ExpertSystemService $expertSystemService,
        private PointService $pointService,
    ) {}

    public function execute(int $reportId): array
    {
        $report = Report::with(['entities'])->findOrFail($reportId);

        if ($report->status !== ReportStatus::APPROVED) {
            throw new \InvalidArgumentException('Report must be approved before processing');
        }

        return DB::transaction(function () use ($report) {
            // Step 1: Preprocess the report text
            $preprocessingResult = $this->preprocessReport($report);

            // Step 2: Store preprocessing results
            $this->storePreprocessingResults($report, $preprocessingResult);

            // Step 3: Process based on report type
            $this->processReportType($report, $preprocessingResult);

            // Step 4: Add facts to Working Memory for each involved santri
            $this->addFactsToWorkingMemory($report, $preprocessingResult);

            // Step 5: Run Expert System for each santri
            $expertResults = $this->runExpertSystem($report);

            // Step 6: Mark report as processed
            $report->markAsProcessed();

            Log::info('Report processed successfully', [
                'report_id' => $report->id,
                'detected_codes' => $preprocessingResult->detectedCodes,
                'entities_count' => count($report->entities),
            ]);

            return [
                'success' => true,
                'report_id' => $report->id,
                'detected_codes' => $preprocessingResult->detectedCodes,
                'confidence_score' => $preprocessingResult->confidenceScore,
                'expert_results' => $expertResults,
            ];
        });
    }

    private function preprocessReport(Report $report): \Domain\Services\PreprocessingResult
    {
        return $this->preprocessingService->process($report->laporan_text);
    }

    private function storePreprocessingResults(Report $report, \Domain\Services\PreprocessingResult $result): void
    {
        // Store main preprocessing result
        ReportPreprocessing::updateOrCreate(
            ['report_id' => $report->id],
            [
                'text_original' => $result->textOriginal,
                'text_cleaned' => $result->textCleaned,
                'tokens' => $result->tokens,
                'tokens_stemmed' => $result->tokensStemmed,
                'detected_codes' => $result->detectedCodes,
                'detected_entities' => $result->detectedEntities,
                'confidence_score' => $result->confidenceScore,
                'matching_details' => $result->matchingDetails,
            ]
        );

        // Store keyword matches
        foreach ($result->matchingDetails as $match) {
            ReportMatch::create([
                'report_id' => $report->id,
                'kata_ditemukan' => $match['kata_ditemukan'],
                'kata_stem' => $match['kata_stem'],
                'kode_referensi' => $match['kode_referensi'],
                'tipe' => $match['tipe'],
                'position' => $match['position'] ?? null,
            ]);
        }

        // Auto-detect entities if not manually set
        if ($report->entities->isEmpty() && !empty($result->detectedEntities)) {
            $semanticRoles = $this->preprocessingService->detectSemanticRole(
                $result->textCleaned,
                $result->detectedEntities
            );

            foreach ($semanticRoles as $entity) {
                ReportEntity::create([
                    'report_id' => $report->id,
                    'santri_id' => $entity['santri_id'],
                    'role' => $entity['role'],
                    'detected_name' => $entity['nama'],
                    'match_confidence' => $entity['confidence'],
                    'is_confirmed' => false, // Needs BK confirmation
                ]);
            }

            // Reload entities
            $report->load('entities');
        }
    }

    private function processReportType(Report $report, \Domain\Services\PreprocessingResult $result): void
    {
        $confirmedEntities = $report->entities->where('is_confirmed', true);

        // If no confirmed entities, skip point processing
        if ($confirmedEntities->isEmpty()) {
            Log::info('No confirmed entities for report', ['report_id' => $report->id]);
            return;
        }

        foreach ($confirmedEntities as $entity) {
            $santriId = $entity->santri_id;

            if ($report->jenis === ReportType::PELANGGARAN) {
                $this->processPelanggaran($santriId, $report, $result);
            } elseif ($report->jenis === ReportType::APRESIASI) {
                $this->processApresiasi($santriId, $report, $result);
            }
            // KONSELING type only adds facts, no points
        }
    }

    private function processPelanggaran(int $santriId, Report $report, \Domain\Services\PreprocessingResult $result): void
    {
        $pelanggaranCodes = $result->getPelanggaranCodes();

        foreach ($pelanggaranCodes as $code) {
            $pelanggaran = KbPelanggaran::findByKode($code);
            if (!$pelanggaran) continue;

            SantriViolation::create([
                'santri_id' => $santriId,
                'report_id' => $report->id,
                'pelanggaran_kode' => $pelanggaran->kode,
                'pelanggaran_nama' => $pelanggaran->nama,
                'poin' => $pelanggaran->poin,
                'konsekuensi' => $pelanggaran->konsekuensi,
                'verified_by' => auth()->id(),
            ]);

            // Update total points
            $this->pointService->addPelanggaran($santriId, $code, $report->id, auth()->id());
        }
    }

    private function processApresiasi(int $santriId, Report $report, \Domain\Services\PreprocessingResult $result): void
    {
        $apresiasiCodes = $result->getApresiasiCodes();

        foreach ($apresiasiCodes as $code) {
            $apresiasi = KbApresiasi::findByKode($code);
            if (!$apresiasi) continue;

            SantriAppreciation::create([
                'santri_id' => $santriId,
                'report_id' => $report->id,
                'apresiasi_kode' => $apresiasi->kode,
                'apresiasi_nama' => $apresiasi->nama,
                'poin' => $apresiasi->poin,
                'reward' => $apresiasi->reward,
                'verified_by' => auth()->id(),
            ]);

            // Update total points
            $this->pointService->addApresiasi($santriId, $code, $report->id, auth()->id());
        }
    }

    private function addFactsToWorkingMemory(Report $report, \Domain\Services\PreprocessingResult $result): void
    {
        $expiresAt = now()->addMonths(config('habitify.fact_expiry_months', 6));

        foreach ($report->entities as $entity) {
            $santriId = $entity->santri_id;

            // Add all detected codes as facts
            foreach ($result->detectedCodes as $code) {
                $factType = match (true) {
                    str_starts_with($code, 'P') => 'pelanggaran',
                    str_starts_with($code, 'A') => 'apresiasi',
                    str_starts_with($code, 'G') => 'konselor',
                    default => 'konselor',
                };

                // Check if fact already exists
                $existingFact = SantriFact::where('santri_id', $santriId)
                    ->where('fact_code', $code)
                    ->where('is_active', true)
                    ->first();

                if (!$existingFact) {
                    SantriFact::create([
                        'santri_id' => $santriId,
                        'fact_code' => $code,
                        'fact_type' => $factType,
                        'source_report_id' => $report->id,
                        'is_active' => true,
                        'expires_at' => $expiresAt,
                    ]);
                }
            }
        }
    }

    private function runExpertSystem(Report $report): array
    {
        $results = [];

        foreach ($report->entities as $entity) {
            $result = $this->expertSystemService->processRulesForSantri(
                $entity->santri_id,
                $report->id
            );

            $results[$entity->santri_id] = $result->toArray();
        }

        return $results;
    }
}