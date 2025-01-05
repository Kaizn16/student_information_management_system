<?php

namespace App\Filament\Resources\Admin\ClassesResource\Pages;

use App\Filament\Resources\Admin\ClassesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClasses extends ListRecords
{
    protected static string $resource = ClassesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
