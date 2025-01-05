<?php

namespace App\Filament\Resources\Admin;

use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Barangay;
use App\Models\Province;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Municipality;
use Illuminate\Support\Carbon;
use App\Helpers\SuffixesHelper;
use Filament\Resources\Resource;
use App\Helpers\NationalityHelper;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Admin\StudentResource\Pages;
use App\Filament\Resources\Admin\StudentResource\RelationManagers;
use App\Filament\Resources\Admin\StudentResource\Pages\EditStudent;
use App\Filament\Resources\Admin\StudentResource\Pages\ViewStudent;
use App\Filament\Resources\Admin\StudentResource\Pages\ListStudents;
use App\Filament\Resources\Admin\StudentResource\Pages\CreateStudent;
use App\Helpers\SectionsHelper;
use App\Helpers\YearLevelsHelper;
use App\Models\Strand;
use App\Models\Teacher;
use Filament\Forms\Components\FileUpload;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $slug = 'students';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal Profile')
                    ->collapsible()
                    ->schema([
                        TextInput::make('student_uid')
                            ->label('Student ID')
                            ->autocomplete()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('lrn')
                            ->label('Learning Reference Number (LRN)')
                            ->autocomplete()
                            ->required()
                            ->columnSpan(2)
                            ->maxLength(255),
                        TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('middle_name')
                            ->maxLength(50),
                        TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        Select::make('suffix')
                            ->options(array_keys(SuffixesHelper::NAME_SUFFIXES))
                            ->searchable(),
                        Select::make('sex')
                            ->options([
                                'Male' => 'Male', 
                                'Female' => 'Female'
                            ])
                            ->required(),
                        DatePicker::make('date_of_birth')
                            ->native(false)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $dob = Carbon::parse($state);
                                $age = $dob->age;
                                $set('age', $age);
                        }),
                        TextInput::make('age')
                            ->integer()
                            ->live()
                            ->readOnly()
                            ->required(),
                        TextInput::make('place_of_birth')
                            ->required()
                            ->maxLength(255), 
                        Select::make('nationality')
                            ->options(NationalityHelper::NATIONALITIES)
                            ->searchable()
                            ->required(),
                ])->columns(3),
                
                Section::make('Address')
                    ->collapsible()
                    ->schema([
                        Select::make('region_id')
                            ->relationship(name: 'region', titleAttribute: 'region_name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function(Set $set) {
                                    $set('province_id', null);
                                    $set('municipality_id', null);
                                    $set('barangay_id', null);
                                })
                            ->required(),
                        Select::make('province_id')
                            ->label('Province')
                            ->options(fn (Get $get): Collection => Province::query()
                                ->where('region_id', $get('region_id'))
                                ->pluck('province_name', 'province_id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                    $set('municipality_id', null);
                                    $set('barangay_id', null);
                                })
                            ->required(),
                        Select::make('municipality_id')
                            ->label('Municipality')
                            ->options(fn (Get $get): Collection => Municipality::query()
                                ->where('province_id', $get('province_id'))
                                ->pluck('municipality_name', 'municipality_id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('barangay_id', null))
                            ->required(),
                        Select::make('barangay_id')
                            ->label('Barangay')
                            ->options(fn (Get $get): Collection => Barangay::query()
                                ->where('municipality_id', $get('municipality_id'))
                                ->pluck('barangay_name', 'barangay_id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                        TextInput::make('street_address')
                            ->label('Street Address')
                            ->autocomplete()
                            ->maxLength(255)
                            ->default(null)
                            ->columnSpanFull(),
                ])->columns(2),
                
                Section::make('Contact Information')
                    ->collapsible()
                    ->schema([
                        PhoneInput::make('contact_no')
                            ->countryStatePath('phone_country')
                            ->defaultCountry('PH')
                            ->required(),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                ])->columns(2),
                
                Group::make()
                    ->extraAttributes(['class' => 'bg-gray-100 p-4 rounded shadow dark:bg-gray-800 dark:text-white'])
                    ->schema([
                        Placeholder::make('Family Information'),
                        Section::make('Father')
                            ->collapsible()
                            ->schema([
                                TextInput::make('father_first_name')
                                    ->maxLength(255)
                                    ->default(null),
                                TextInput::make('father_middle_name')
                                    ->maxLength(255)
                                    ->default(null),
                                TextInput::make('father_last_name')
                                    ->maxLength(255)
                                    ->default(null),
                                TextInput::make('father_occupation')
                                    ->maxLength(255)
                                    ->default(null),
                                TextInput::make('father_contact_no')
                                    ->maxLength(255)
                                    ->default(null),
                        ])->columns(3),
                        Section::make('Mother')
                            ->collapsible()
                            ->schema([
                                TextInput::make('mother_first_name')
                                    ->maxLength(255)
                                    ->default(null),
                                TextInput::make('mother_middle_name')
                                    ->maxLength(255)
                                    ->default(null),
                                TextInput::make('mother_last_name')
                                    ->maxLength(255)
                                    ->default(null),
                                TextInput::make('mother_occupation')
                                    ->maxLength(255)
                                    ->default(null),
                                TextInput::make('mother_contact_no')
                                    ->maxLength(255)
                                    ->default(null),
                        ])->columns(3),
                        Section::make('Guardian')
                            ->collapsible()
                            ->schema([
                                TextInput::make('guardian_first_name')
                                    ->maxLength(255)
                                    ->default(null),
                                TextInput::make('guardian_middle_name')
                                    ->maxLength(255)
                                    ->default(null),
                                TextInput::make('guardian_last_name')
                                    ->maxLength(255)
                                    ->default(null),
                                TextInput::make('guardian_occupation')
                                    ->maxLength(255)
                                    ->default(null),
                                TextInput::make('guardian_contact_no')
                                    ->maxLength(255)
                                    ->default(null),
                                TextInput::make('guardian_relation')
                                    ->maxLength(255)
                                    ->default(null),
                        ])->columns(3),
                ])->columnSpanFull(),
                
                Section::make('Academics Information')
                    ->collapsible()
                    ->schema([
                        TextInput::make('previous_school_name')
                            ->required()
                            ->maxLength(255),
                        FileUpload::make('birth_certificate')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->default(null),
                        FileUpload::make('report_card')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->default(null),
                        Select::make('teacher_id')
                            ->label('Adviser')
                            ->options(Teacher::query()
                                ->where('designation', 'Adviser')
                                ->selectRaw("CONCAT(first_name, ' ', middle_name, ' ', last_name) as full_name, teacher_id")
                                ->pluck('full_name', 'teacher_id'))
                            ->live()
                            ->preload()
                            ->searchable()
                            ->required(),
                            Select::make('strand_type')
                            ->options([
                                'Academic Track' => 'Academic Track',
                                'TVL Track' => 'TVL Track',
                            ])
                            ->afterStateUpdated(function ($state, $set) {
                                $set('strand_id', null);
                            }),
                        Select::make('strand_id')
                            ->options(function ($get) {
                                $strandType = $get('strand_type');
                                return Strand::query()
                                    ->where('strand_type', $strandType)
                                    ->get()
                                    ->pluck('strand_name', 'strand_id');
                            })
                            ->live()
                            ->searchable()
                            ->label('Strand')
                            ->required(),                        
                        Select::make('current_year_level')
                            ->options(YearLevelsHelper::YEAR_LEVELS)
                            ->searchable()
                            ->required(),
                        Select::make('section')
                            ->options(SectionsHelper::SECTIONS)
                            ->searchable()
                            ->required(),
                        TextInput::make('school_year')
                            ->required(),
                        Select::make('enrollment_status')
                            ->options([
                                'Stopped' => 'Stopped',
                                'Continuing' => 'Continuing',
                                'Graduated' => 'Graduated',
                            ])
                            ->default('Continuing')
                            ->required(),
                ])->columns(3),
                Section::make('Account Information')
                ->schema([
                    TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->required(fn ($get) => !$get('student_id')),
                    TextInput::make('confirm_password')
                        ->password()
                        ->revealable()
                        ->required(fn ($get) => !$get('student_id')),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                    TextColumn::make('student_uid')
                    ->searchable(),
                    TextColumn::make('lrn')
                    ->searchable(),
                    TextColumn::make('first_name')
                    ->searchable(),
                    TextColumn::make('middle_name')
                    ->searchable(),
                    TextColumn::make('last_name')
                    ->searchable(),
                    TextColumn::make('suffix')
                    ->searchable(),
                    TextColumn::make('sex'),
                    TextColumn::make('age')
                    ->numeric()
                    ->sortable(),
                    TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable(),
                    TextColumn::make('place_of_birth')
                    ->searchable(),
                    TextColumn::make('region_id')
                    ->numeric()
                    ->sortable(),
                    TextColumn::make('province_id')
                    ->numeric()
                    ->sortable(),
                    TextColumn::make('municipality_id')
                    ->numeric()
                    ->sortable(),
                    TextColumn::make('barangay_id')
                    ->numeric()
                    ->sortable(),
                    TextColumn::make('street_address')
                    ->searchable(),
                    TextColumn::make('contact_no')
                    ->searchable(),
                    TextColumn::make('email')
                    ->searchable(),
                    TextColumn::make('father_first_name')
                    ->searchable(),
                    TextColumn::make('father_middle_name')
                    ->searchable(),
                    TextColumn::make('father_last_name')
                    ->searchable(),
                    TextColumn::make('father_occupation')
                    ->searchable(),
                    TextColumn::make('father_contact_no')
                    ->searchable(),
                    TextColumn::make('mother_first_name')
                    ->searchable(),
                    TextColumn::make('mother_middle_name')
                    ->searchable(),
                    TextColumn::make('mother_last_name')
                    ->searchable(),
                    TextColumn::make('mother_occupation')
                    ->searchable(),
                    TextColumn::make('mother_contact_no')
                    ->searchable(),
                    TextColumn::make('guardian_first_name')
                    ->searchable(),
                    TextColumn::make('guardian_middle_name')
                    ->searchable(),
                    TextColumn::make('guardian_last_name')
                    ->searchable(),
                    TextColumn::make('guardian_occupation')
                    ->searchable(),
                    TextColumn::make('guardian_contact_no')
                    ->searchable(),
                    TextColumn::make('guardian_relation')
                    ->searchable(),
                    TextColumn::make('previous_school_name')
                    ->searchable(),
                    TextColumn::make('birth_certificate')
                    ->searchable(),
                    TextColumn::make('teacher_id')
                    ->numeric()
                    ->sortable(),
                    TextColumn::make('report_card')
                    ->searchable(),
                    TextColumn::make('current_year_level')
                    ->searchable(),
                    TextColumn::make('strand_id')
                    ->numeric()
                    ->sortable(),
                    TextColumn::make('section')
                    ->searchable(),
                    TextColumn::make('school_year')
                    ->searchable(),
                    TextColumn::make('enrollment_status'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'view' => Pages\ViewStudent::route('/{record}'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
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
