<?php

namespace App\Filament\Resources\Admin;

use Filament\Forms;
use App\Models\Room;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Admin\RoomResource\Pages;
use App\Filament\Resources\Admin\RoomResource\RelationManagers;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $slug = 'rooms';
    protected static ?string $navigationGroup = 'Class Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('room_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('max_seat')
                    ->integer()
                    ->numeric(),
                TextInput::make('building_name')
                    ->maxLength(255)
                    ->default(null),
                Textarea::make('building_description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('room_name')
                    ->searchable(),
                TextColumn::make('building_name')
                    ->searchable(),
                TextColumn::make('max_seat')
                    ->label('Max Seat')
                    ->icon('heroicon-o-user'),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'view' => Pages\ViewRoom::route('/{record}'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
