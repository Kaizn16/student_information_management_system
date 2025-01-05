<?php

namespace App\Filament\Resources\Admin\RoomResource\Pages;

use App\Filament\Resources\Admin\RoomResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRoom extends ViewRecord
{
    protected static string $resource = RoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
