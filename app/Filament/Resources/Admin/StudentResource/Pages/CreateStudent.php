<?php

namespace App\Filament\Resources\Admin\StudentResource\Pages;

use App\Filament\Resources\Admin\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;
}
