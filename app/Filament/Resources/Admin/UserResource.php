<?php

namespace App\Filament\Resources\Admin;

use Filament\Forms;
use App\Models\Role;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Admin\UserResource\Pages;
use App\Filament\Resources\Admin\UserResource\Pages\EditUser;
use App\Filament\Resources\Admin\UserResource\Pages\ViewUser;
use App\Filament\Resources\Admin\UserResource\Pages\ListUsers;
use App\Filament\Resources\Admin\UserResource\Pages\CreateUser;
use App\Filament\Resources\Admin\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $slug = 'users';

    protected static ?string $navigationGroup = 'Account Management';

    protected static bool $shouldRegisterNavigation = true;

    // public static function canAccess(): bool
    // {
    //     return false;
    // }
    
    public static function form(Form $form): Form
    {
        return $form
        ->schema([
                Select::make('role_id')
                    ->relationship('role', 'role_type')
                    ->default(1)
                    ->disabled(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->required()
                    ->email(),
                TextInput::make('username')
                    ->required()
                    ->minLength(6)
                    ->maxLength(255),
                TextInput::make('password')
                    ->revealable()
                    ->required()
                    ->password()
                    ->minLength(6),
                TextInput::make('confirm_password')
                    ->revealable()
                    ->password()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('username')
                    ->searchable(),
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email'),
                TextColumn::make('role.role_type')
                    ->label('Role')
                    ->extraAttributes(['style' => 'text-transform: uppercase;'])
                    ->sortable(),
            ])->defaultSort('name')
            ->filters([
                SelectFilter::make('roles')
                    ->relationship('role', titleAttribute: 'role_type') 
                    ->label('Role')
                    ->placeholder('All Roles'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()->hidden(fn ($record) => $record->user_id === Auth::id()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
