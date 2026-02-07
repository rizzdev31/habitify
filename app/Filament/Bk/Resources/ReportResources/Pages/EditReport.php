<?php

namespace App\Filament\Bk\Resources\ReportResource\Pages;

use App\Filament\Bk\Resources\ReportResource;
use App\Application\Actions\ProcessReportAction;
use Domain\Enums\ReportStatus;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditReport extends EditRecord
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['status'])) {
            $data['validated_by'] = auth()->id();
            $data['validated_at'] = now();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        // If approved, trigger preprocessing and expert system
        if ($record->status === ReportStatus::APPROVED) {
            try {
                $action = app(ProcessReportAction::class);
                $result = $action->execute($record->id);

                if ($result['success']) {
                    Notification::make()
                        ->success()
                        ->title('Laporan Berhasil Diproses')
                        ->body("Terdeteksi: " . implode(', ', $result['detected_codes']))
                        ->send();
                }
            } catch (\Exception $e) {
                Notification::make()
                    ->warning()
                    ->title('Laporan Disetujui')
                    ->body('Namun preprocessing gagal: ' . $e->getMessage())
                    ->send();
            }
        } elseif ($record->status === ReportStatus::REJECTED) {
            Notification::make()
                ->warning()
                ->title('Laporan Ditolak')
                ->body('Laporan telah ditolak dengan alasan yang diberikan.')
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}