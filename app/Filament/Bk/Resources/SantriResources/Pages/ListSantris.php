<?php
// ListSantris.php
namespace App\Filament\Bk\Resources\SantriResource\Pages;

use App\Filament\Bk\Resources\SantriResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSantris extends ListRecords
{
    protected static string $resource = SantriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}