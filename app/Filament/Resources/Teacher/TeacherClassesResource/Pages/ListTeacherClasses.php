<?php

namespace App\Filament\Resources\Teacher\TeacherClassesResource\Pages;

use App\Filament\Resources\Teacher\TeacherClassesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeacherClasses extends ListRecords
{
    protected static string $resource = TeacherClassesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
