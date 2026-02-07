<?php

namespace App\Filament\Pengajar\Resources\ReportResource\Pages;

use App\Filament\Pengajar\Resources\ReportResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateReport extends CreateRecord
{
    protected static string $resource = ReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['pelapor_id'] = auth()->id();
        $data['status'] = 'pending';
        return $data;
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->success()
            ->title('Laporan Berhasil Dikirim')
            ->body('Laporan Anda sedang menunggu validasi dari Guru BK.')
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}