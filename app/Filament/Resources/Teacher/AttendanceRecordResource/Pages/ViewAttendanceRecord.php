<?php

namespace App\Filament\Resources\Teacher\AttendanceRecordResource\Pages;

use App\Filament\Resources\Teacher\AttendanceRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAttendanceRecord extends ViewRecord
{
    protected static string $resource = AttendanceRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
