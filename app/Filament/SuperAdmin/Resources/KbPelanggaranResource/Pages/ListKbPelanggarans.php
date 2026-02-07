<?php
// ListKbPelanggarans.php
namespace App\Filament\SuperAdmin\Resources\KbPelanggaranResource\Pages;

use App\Filament\SuperAdmin\Resources\KbPelanggaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKbPelanggarans extends ListRecords
{
    protected static string $resource = KbPelanggaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}