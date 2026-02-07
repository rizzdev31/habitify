<?php

namespace App\Filament\Bk\Widgets;

use App\Infrastructure\Persistence\Models\Report;
use App\Infrastructure\Persistence\Models\ExpertExecution;
use App\Infrastructure\Persistence\Models\SantriProfile;
use App\Infrastructure\Persistence\Models\SantriCounseling;
use Domain\Enums\ReportStatus;
use Domain\Enums\DiagnosisStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $pendingReports = Report::pending()->count();
        $pendingDiagnosis = ExpertExecution::pending()->count();
        $totalSantri = SantriProfile::active()->count();
        $counselingToday = SantriCounseling::whereDate('tanggal_konseling', today())->count();

        return [
            Stat::make('Laporan Pending', $pendingReports)
                ->description('Menunggu validasi')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([7, 3, 4, 5, 6, 3, $pendingReports])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "redirectToReports",
                ]),

            Stat::make('Diagnosis Baru', $pendingDiagnosis)
                ->description('Butuh konseling')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($pendingDiagnosis > 0 ? 'danger' : 'success')
                ->chart([2, 4, 6, 3, 5, 4, $pendingDiagnosis]),

            Stat::make('Total Santri Aktif', $totalSantri)
                ->description('Santri terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Konseling Hari Ini', $counselingToday)
                ->description('Sesi konseling')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('success'),
        ];
    }

    public function redirectToReports(): void
    {
        $this->redirect(route('filament.bk.resources.reports.index'));
    }
}