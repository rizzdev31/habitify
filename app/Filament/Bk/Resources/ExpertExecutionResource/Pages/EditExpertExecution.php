<?php

namespace App\Filament\Bk\Resources\ExpertExecutionResource\Pages;

use App\Filament\Bk\Resources\ExpertExecutionResource;
use App\Infrastructure\Persistence\Models\SantriCounseling;
use Domain\Enums\DiagnosisStatus;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditExpertExecution extends EditRecord
{
    protected static string $resource = ExpertExecutionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\Action::make('notify_wali')
                ->label('Kirim Notifikasi Wali')
                ->icon('heroicon-o-bell')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    // TODO: Implement WhatsApp notification
                    $this->record->update([
                        'wali_notified' => true,
                        'wali_notified_at' => now(),
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Notifikasi Terkirim')
                        ->body('Wali santri telah diberitahu via WhatsApp')
                        ->send();
                })
                ->visible(fn () => !$this->record->wali_notified),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $oldStatus = $this->record->status;
        $newStatus = DiagnosisStatus::from($data['status']);

        // If status changed to in_progress, set handler
        if ($oldStatus === DiagnosisStatus::PENDING && $newStatus === DiagnosisStatus::IN_PROGRESS) {
            $data['handled_by'] = auth()->id();
            $data['handled_at'] = now();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        // If completed, create counseling record
        if ($record->status === DiagnosisStatus::COMPLETED && $record->counseling_notes) {
            SantriCounseling::create([
                'santri_id' => $record->santri_id,
                'bk_id' => auth()->id(),
                'report_id' => $record->report_id,
                'diagnosis_kode' => $record->diagnosis_kode,
                'diagnosis_nama' => $record->diagnosis?->nama,
                'tanggal_konseling' => now()->toDateString(),
                'catatan_konseling' => $record->counseling_notes,
                'rekomendasi_tindak_lanjut' => $record->follow_up_notes,
                'jadwal_follow_up' => $record->follow_up_date,
                'status' => 'completed',
            ]);

            Notification::make()
                ->success()
                ->title('Konseling Selesai')
                ->body('Data konseling telah disimpan.')
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}