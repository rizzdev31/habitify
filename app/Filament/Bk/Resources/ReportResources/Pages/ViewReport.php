<?php

namespace App\Filament\Bk\Resources\ReportResource\Pages;

use App\Filament\Bk\Resources\ReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewReport extends ViewRecord
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => $this->record->isPending()),
        ];
    }
}