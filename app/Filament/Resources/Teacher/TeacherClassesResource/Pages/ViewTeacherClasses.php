<?php

namespace App\Filament\Resources\Teacher\TeacherClassesResource\Pages;

use App\Filament\Resources\Teacher\TeacherClassesResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTeacherClasses extends ViewRecord
{
    protected static string $resource = TeacherClassesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
