<?php

namespace App\Filament\Bk\Resources\SantriResource\Pages;

use App\Filament\Bk\Resources\SantriResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSantri extends ViewRecord
{
    protected static string $resource = SantriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}