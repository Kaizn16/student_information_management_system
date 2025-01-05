<?php

namespace App\Filament\Resources\Teacher\AttendanceRecordResource\Pages;

use App\Filament\Resources\Teacher\AttendanceRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttendanceRecord extends EditRecord
{
    protected static string $resource = AttendanceRecordResource::class;

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
