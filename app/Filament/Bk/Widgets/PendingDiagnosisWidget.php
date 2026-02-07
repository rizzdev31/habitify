<?php

namespace App\Filament\Bk\Widgets;

use App\Infrastructure\Persistence\Models\ExpertExecution;
use Domain\Enums\DiagnosisStatus;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingDiagnosisWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = '🚨 Diagnosis Menunggu Konseling';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ExpertExecution::query()
                    ->with(['santri', 'diagnosis'])
                    ->where('status', DiagnosisStatus::PENDING)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('santri.nama_lengkap')
                    ->label('Nama Santri')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('santri.kelas')
                    ->label('Kelas')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('diagnosis.nama')
                    ->label('Diagnosis')
                    ->wrap()
                    ->limit(30),

                Tables\Columns\TextColumn::make('diagnosis.severity')
                    ->label('Severity')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'success',
                        'medium' => 'warning',
                        'high' => 'danger',
                        'critical' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdeteksi')
                    ->since()
                    ->color('gray'),
            ])
            ->actions([
                Tables\Actions\Action::make('mulai_konseling')
                    ->label('Mulai')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('primary')
                    ->url(fn (ExpertExecution $record): string => 
                        route('filament.bk.resources.expert-executions.edit', $record)
                    ),
            ])
            ->emptyStateHeading('Tidak ada diagnosis pending')
            ->emptyStateDescription('Semua santri dalam kondisi baik!')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}