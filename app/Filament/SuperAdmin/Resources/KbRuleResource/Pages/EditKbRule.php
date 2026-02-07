<?php

namespace App\Filament\SuperAdmin\Resources\KbRuleResource\Pages;

use App\Filament\SuperAdmin\Resources\KbRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKbRule extends EditRecord
{
    protected static string $resource = KbRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}