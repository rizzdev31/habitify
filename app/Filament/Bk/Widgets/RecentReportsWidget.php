<?php

namespace App\Filament\Bk\Widgets;

use App\Infrastructure\Persistence\Models\Report;
use Domain\Enums\ReportStatus;
use Domain\Enums\ReportType;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentReportsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = '📋 Laporan Terbaru Menunggu Validasi';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Report::query()
                    ->with(['pelapor'])
                    ->where('status', ReportStatus::PENDING)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->formatStateUsing(fn ($state) => '#' . str_pad($state, 5, '0', STR_PAD_LEFT))
                    ->color('gray'),

                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (ReportType $state): string => $state->color()),

                Tables\Columns\TextColumn::make('laporan_text')
                    ->label('Isi Laporan')
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\TextColumn::make('pelapor.name')
                    ->label('Pelapor')
                    ->icon('heroicon-m-user'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since()
                    ->color('gray'),
            ])
            ->actions([
                Tables\Actions\Action::make('validasi')
                    ->label('Validasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->url(fn (Report $record): string => 
                        route('filament.bk.resources.reports.edit', $record)
                    ),
            ])
            ->emptyStateHeading('Tidak ada laporan pending')
            ->emptyStateDescription('Semua laporan sudah divalidasi!')
            ->emptyStateIcon('heroicon-o-document-check');
    }
}