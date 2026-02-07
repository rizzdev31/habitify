<?php

namespace App\Filament\Bk\Resources\ExpertExecutionResource\Pages;

use App\Filament\Bk\Resources\ExpertExecutionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewExpertExecution extends ViewRecord
{
    protected static string $resource = ExpertExecutionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Mulai Konseling'),
        ];
    }
}