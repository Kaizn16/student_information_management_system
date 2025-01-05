<?php

namespace App\Filament\Resources\Admin;

use Closure;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Subject;
use App\Models\Teacher;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Barangay;
use App\Models\Province;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Municipality;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use App\Helpers\NationalityHelper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use App\Helpers\RelationshipTypeHelper;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Admin\TeacherResource\Pages;
use App\Filament\Resources\Admin\TeacherResource\RelationManagers;
use App\Filament\Resources\Admin\TeacherResource\Pages\EditTeacher;
use App\Filament\Resources\Admin\TeacherResource\Pages\ViewTeacher;
use App\Filament\Resources\Admin\TeacherResource\Pages\ListTeachers;
use App\Filament\Resources\Admin\TeacherResource\Pages\CreateTeacher;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $slug = 'teachers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal Information')
                    ->collapsible()
                    ->schema([
                        TextInput::make('first_name')
                            ->autocomplete()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('middle_name')
                            ->autocomplete()
                            ->maxLength(50),
                        TextInput::make('last_name')
                            ->autocomplete()
                            ->required()
                            ->maxLength(255),
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
                        Select::make('sex')
                            ->options([
                                'Male' => 'Male', 
                                'Female' => 'Female'
                            ])
                            ->required(),
                        TextInput::make('civil_status')
                            ->autocomplete()
                            ->datalist([
                                'Single', 
                                'Married', 
                                'Divorced', 
                                'Widowed', 
                                'Separated', 
                                'In a Domestic Partnership', 
                                'Engaged'
                            ])                        
                            ->required()
                            ->maxLength(255),
                        Select::make('nationality')
                            ->options(NationalityHelper::NATIONALITIES)
                            ->searchable()
                            ->required(),
                ])->columns(3),
                
                Section::make('Contact Information')
                    ->collapsible()
                    ->schema([
                        PhoneInput::make('contact_no')
                            ->label('Contact No.')
                            ->countryStatePath('phone_country')
                            ->defaultCountry('PH')
                            ->required(),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                ])->columns(2),
                
                Section::make('Address Information')
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
                
                Section::make('Emergency Contact Information')
                    ->collapsible()
                    ->schema([
                        TextInput::make('emergency_contact_name')
                            ->label('Name')
                            ->autocomplete()
                            ->required()
                            ->maxLength(255),
                        Select::make('emergency_contact_relation')
                            ->label('Relation')
                            ->options(RelationshipTypeHelper::RELATIONSHIP_TYPES)
                            ->searchable()
                            ->required(),
                        PhoneInput::make('emergency_contact_no')
                            ->label('Contact No.')
                            ->countryStatePath('phone_country')
                            ->defaultCountry('PH')
                            ->required(),
                ])->columns(3),
                
                Section::make('Government Information')
                    ->collapsible()
                    ->schema([
                        TextInput::make('tin_id')
                            ->label('TIN Number')
                            ->autocomplete()
                            ->numeric()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('sss_number')
                            ->label('SSS Number')
                            ->autocomplete()
                            ->numeric()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('pagibig_number')
                            ->label('PAG-IBIG Number')
                            ->autocomplete()
                            ->numeric()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('philhealth_number')
                            ->label('Philhealth Number')
                            ->autocomplete()
                            ->numeric()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('prc_license_number')
                            ->label('PRC License Number')
                            ->autocomplete()
                            ->numeric()
                            ->required()
                            ->maxLength(255),
                        DatePicker::make('prc_license_expiration_date')
                            ->label('PRC License Expiry Date')
                            ->native(false)
                            ->required(),
                ])->columns(3),
                
                Section::make('Educational Background')
                    ->collapsible()
                    ->schema([
                        TextInput::make('highest_degree')
                            ->required()
                            ->autocomplete()
                            ->maxLength(255),
                        TextInput::make('field_of_specialiation')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('university_graduated_name')
                            ->required()
                            ->autocomplete()
                            ->maxLength(255),
                        TextInput::make('year_graduated')
                            ->numeric()
                            ->required(),
                        TextInput::make('additional_course_training')
                            ->maxLength(255)
                            ->default(null),
                ])->columns(3),
                
                Section::make('Employment Information')
                    ->collapsible()
                    ->schema([ 
                        TextInput::make('faculty_uid')
                            ->label('Faculty ID')
                            ->required(),
                        Select::make('designation')
                            ->options([
                                'Adviser' => 'Adviser',
                                'Teacher' => 'Teacher',
                            ])
                            ->required(),
                        Select::make('subjects_handle')
                            ->label('Subjects Handle (Ex. Math, English)')
                            ->options(fn (Get $get): Collection => Subject::query()
                                ->pluck('subject_code', 'subject_id'))
                            ->multiple()
                            ->preload()
                            ->required(),
                        Select::make('employment_type')
                            ->options([
                                'Part-Time' => 'Part-Time',
                                'Full-Time' => 'Full-Time'
                            ])
                            ->required(),
                        DatePicker::make('date_hired')
                            ->native(false)
                            ->required(),
                        Select::make('employment_status')
                            ->options([
                                'Active' => 'Active',
                                'Inactive' => 'Inactive'
                        ])
                        ->default('Active'),
                ])->columns(3),
                Section::make('Account Information')
                    ->collapsible()        
                    ->schema([
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->required(fn ($get) => !$get('teacher_id')),
                        TextInput::make('password_confirmation')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->required(fn ($get) => !$get('teacher_id'))
                            ->label('Confirm Password'),
                ])->columns(2),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('faculty_uid')
                ->label('Faculty ID')
                    ->searchable(),
                TextColumn::make('first_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('middle_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('last_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('age')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sex')
                ->sortable(),
                TextColumn::make('civil_status')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable(),
                TextColumn::make('nationality')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contact_no')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('region.region_name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('province.province_name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('municipality.municipality_name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('barangay.barangay_name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('street_address')
                    ->searchable(),
                TextColumn::make('emergency_contact_name')
                    ->searchable(),
                TextColumn::make('emergency_contact_relation')
                    ->searchable(),
                TextColumn::make('emergency_contact_no')
                    ->searchable(),
                TextColumn::make('prc_license_number')
                    ->searchable(),
                TextColumn::make('prc_license_expiration_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('highest_degree')
                    ->searchable(),
                TextColumn::make('field_of_specialiation')
                    ->searchable(),
                TextColumn::make('university_graduated_name')
                    ->searchable(),
                TextColumn::make('year_graduated')
                    ->date()
                    ->sortable(),
                TextColumn::make('additional_course_training')
                    ->searchable(),
                TextColumn::make('designation'),
                TextColumn::make('employment_type'),
                TextColumn::make('date_hired')
                    ->date()
                    ->sortable(),
                TextColumn::make('employment_status'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
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
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'view' => Pages\ViewTeacher::route('/{record}'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
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
