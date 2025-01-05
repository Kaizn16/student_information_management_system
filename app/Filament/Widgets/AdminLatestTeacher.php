<?php

namespace App\Filament\Widgets;

use App\Models\Teacher;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AdminLatestTeacher extends BaseWidget
{
    protected static ?string $heading = 'Recently Added Teacher';
    protected static ?int $sort = 6;

    public function table(Table $table): Table
    {
        return $table
            ->query(Teacher::query())
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(), 
                TextColumn::make('Teacher Name')
                    ->icon('heroicon-o-user')
                    ->getStateUsing(fn (Teacher $record) => 
                        $record->first_name . ' ' . $record->middle_name . ' ' . $record->last_name
                    ),
                TextColumn::make('created_at')
                    ->label('Date Created')
                    ->dateTime(),
        ]);
    }
}
