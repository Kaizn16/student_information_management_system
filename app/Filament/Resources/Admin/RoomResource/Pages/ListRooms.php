<?php

namespace App\Filament\Resources\Admin\RoomResource\Pages;

use App\Filament\Resources\Admin\RoomResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRooms extends ListRecords
{
    protected static string $resource = RoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
