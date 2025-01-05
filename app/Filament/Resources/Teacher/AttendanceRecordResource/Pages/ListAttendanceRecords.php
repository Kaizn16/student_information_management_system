<?php

namespace App\Filament\Resources\Teacher\AttendanceRecordResource\Pages;

use App\Filament\Resources\Teacher\AttendanceRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceRecords extends ListRecords
{
    protected static string $resource = AttendanceRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
