<?php

namespace App\Filament\Resources\Admin;

use Filament\Forms;
use Filament\Tables;
use App\Models\Subject;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Admin\SubjectResource\Pages;
use App\Filament\Resources\Admin\SubjectResource\RelationManagers;
use App\Filament\Resources\Admin\SubjectResource\Pages\EditSubject;
use App\Filament\Resources\Admin\SubjectResource\Pages\ViewSubject;
use App\Filament\Resources\Admin\SubjectResource\Pages\ListSubjects;
use App\Filament\Resources\Admin\SubjectResource\Pages\CreateSubject;
use App\Models\Strand;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $slug = 'subjects';
    protected static ?string $navigationGroup = 'Class Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('subject_code')
                    ->label('Subject Code')
                    ->required(),
                TextInput::make('subject_title')
                    ->label('Subject Title')
                    ->required()
                    ->maxLength(100),
                Select::make('strand_id')
                    ->label('Strand')
                    ->options(Strand::pluck('strand_name', 'strand_id'))
                    ->searchable(),
                RichEditor::make('subject_description')
                    ->label('Description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateIcon('heroicon-o-book-open')
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(), 
                TextColumn::make('subject_code')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('subject_title')
                    ->searchable(),
                TextColumn::make('strand.strand_name')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->strand->strand_name ?? 'N/A'),
            ])
            ->filters([
                SelectFilter::make('strands')
                    ->relationship('strand', titleAttribute: 'strand_name') 
                    ->label('Strand')
                    ->placeholder('All strands'),
                TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
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
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'view' => Pages\ViewSubject::route('/{record}'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
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
