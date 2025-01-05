<?php

namespace App\Filament\Resources\Admin\SubjectResource\Pages;

use App\Filament\Resources\Admin\SubjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSubject extends ViewRecord
{
    protected static string $resource = SubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
