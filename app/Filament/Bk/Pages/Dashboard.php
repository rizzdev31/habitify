<?php

namespace App\Filament\Bk\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static string $view = 'filament.bk.pages.dashboard';

    protected static ?string $title = 'Dashboard BK';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = 1;

    public function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Bk\Widgets\StatsOverviewWidget::class,
            \App\Filament\Bk\Widgets\PendingDiagnosisWidget::class,
            \App\Filament\Bk\Widgets\RecentReportsWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 2;
    }
}