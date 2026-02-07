<?php

namespace App\Filament\Bk\Resources\SantriResource\Pages;

use App\Filament\Bk\Resources\SantriResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSantri extends CreateRecord
{
    protected static string $resource = SantriResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}