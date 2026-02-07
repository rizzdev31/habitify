<?php

namespace App\Filament\SuperAdmin\Resources\KbPelanggaranResource\Pages;

use App\Filament\SuperAdmin\Resources\KbPelanggaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKbPelanggaran extends EditRecord
{
    protected static string $resource = KbPelanggaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}