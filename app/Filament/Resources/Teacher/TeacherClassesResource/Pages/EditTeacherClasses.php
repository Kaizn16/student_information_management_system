<?php

namespace App\Filament\Resources\Teacher\TeacherClassesResource\Pages;

use App\Filament\Resources\Teacher\TeacherClassesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeacherClasses extends EditRecord
{
    protected static string $resource = TeacherClassesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
