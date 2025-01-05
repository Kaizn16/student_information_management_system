<?php

namespace App\Filament\Resources\Teacher;

use Filament\Forms;
use Filament\Tables;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Helpers\SectionsHelper;
use Filament\Resources\Resource;
use App\Helpers\YearLevelsHelper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Teacher\TeacherClassesResource\Pages;
use App\Filament\Resources\Teacher\TeacherClassesResource\RelationManagers;
use App\Filament\Resources\Teacher\TeacherClassesResource\Pages\EditTeacherClasses;
use App\Filament\Resources\Teacher\TeacherClassesResource\Pages\ListTeacherClasses;
use App\Filament\Resources\Teacher\TeacherClassesResource\Pages\ViewTeacherClasses;
use App\Filament\Resources\Teacher\TeacherClassesResource\Pages\CreateTeacherClasses;

class TeacherClassesResource extends Resource
{
    protected static ?string $model = Classes::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'My Classes';
    protected static ?string $slug = 'My-Classes';
    protected static ?string $navigationGroup = 'Class Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('class_name')
                    ->required(),
                Select::make('room_id')
                    ->label('Room')
                    ->relationship('room', 'room_name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required(),
                Select::make('teacher_id')
                    ->label('Teacher')
                    ->options(Teacher::query()
                            ->where('employment_status', 'Active')
                            ->where('user_id', Auth::id())
                            ->selectRaw("CONCAT_WS(' ', first_name, middle_name, last_name) as full_name, teacher_id")
                            ->pluck('full_name', 'teacher_id')
                            ->toArray())
                    ->afterStateUpdated(function(Set $set) {
                                $set('subject_id', null);
                            })       
                    ->default(
                        Teacher::where('user_id', Auth::id())->first()?->teacher_id
                    )
                    ->afterStateUpdated(function (Set $set) {
                        $set('subject_id', null);
                    })
                    ->preload()
                    ->live()
                    ->required(),
                Select::make('subject_id')
                    ->label('Subject')
                    ->options(function (Get $get): Collection {
                
                        $teacher = Teacher::where('user_id', Auth::id())->first();
                
                        if (!$teacher || !is_array($teacher->subjects_handle)) {
                            return collect();
                        }
                
                        return Subject::query()
                            ->whereIn('subject_id', $teacher->subjects_handle)
                            ->pluck('subject_code', 'subject_id');
                    })
                    ->searchable()
                    ->live()
                    ->required(),
                Select::make('year_level')
                    ->options(YearLevelsHelper::YEAR_LEVELS)
                    ->required(),
                Select::make('section')
                    ->options(SectionsHelper::SECTIONS)
                    ->required(),
                Select::make('students')
                    ->options(function (Get $get) {
                        $year_level = $get('year_level');
                        $section = $get('section');
                
                        $query = Student::query()
                            ->whereNull('deleted_at')
                            ->selectRaw("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) as full_name, student_id");
                
                        if ($year_level) {
                            $query->where('current_year_level', $year_level);
                        }
                
                        if ($section) {
                            $query->where('section', $section);
                        }
                
                        return $query->pluck('full_name', 'student_id');
                    })
                    ->multiple()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),                                               
                Section::make('')
                    ->collapsible()
                    ->schema([
                        Repeater::make('schedule_day_time')
                            ->label('Schedule Day & Time')
                            ->schema([
                                Select::make('day')
                                    ->options([
                                        'monday' => 'Monday',
                                        'tuesday' => 'Tuesday',
                                        'wednesday' => 'Wednesday',
                                        'thursday' => 'Thursday',
                                        'friday' => 'Friday',
                                    ])
                                    ->required()
                                    ->label('Day of the Week'),
                                Section::make('Time')
                                ->schema([
                                    TimePicker::make('time_from')
                                        ->required()
                                        ->label('Time From'),
                                    TimePicker::make('time_to')
                                        ->required()
                                        ->label('Time To'),
                                ])->columns(2),
                            ])
                            ->minItems(1)
                            ->grid(2)
                            ->createItemButtonLabel('Add Schedule')
                            ->required(),
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        $auth_user = Auth::id();
        $teacher = Teacher::where('user_id', $auth_user)->first();

        return $table
            ->query(
                Classes::query()->where('teacher_id', $teacher->teacher_id)
            )
            ->columns([
                TextColumn::make('class_name')
                    ->searchable(),
                TextColumn::make('room.room_name')
                    ->label('Room')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('teacher_id')
                    ->label('Teacher')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->teacher) {
                            $name = $record->teacher->first_name;
                
                            if (!empty($record->teacher->middle_name)) {
                                $name .= ' ' . $record->teacher->middle_name;
                            }
                
                            $name .= ' ' . $record->teacher->last_name;
                
                            return $name;
                        }
                
                        return 'N/A';
                    }),
                TextColumn::make('subject.subject_code')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('year_level')
                    ->searchable(),
                TextColumn::make('section')
                    ->searchable(),
                TextColumn::make('formatted_schedule_day_time')
                    ->label('Schedule')
                    ->html()
                    ->formatStateUsing(fn ($state) => $state),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('students')
                    ->label('Total Student')
                    ->getStateUsing(function ($record) {
                        $total_student = Classes::where('class_id', $record->class_id)
                            ->selectRaw('JSON_LENGTH(students) as total_students')
                            ->value('total_students');
                            
                        return $total_student;
                    })
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
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
            'index' => ListTeacherClasses::route('/'),
            'create' => CreateTeacherClasses::route('/create'),
            'view' => ViewTeacherClasses::route('/{record}'),
            'edit' => EditTeacherClasses::route('/{record}/edit'),
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
