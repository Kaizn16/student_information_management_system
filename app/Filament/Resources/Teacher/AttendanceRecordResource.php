<?php

namespace App\Filament\Resources\Teacher;

use Filament\Forms;
use Filament\Tables;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Teacher;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\AttendanceRecord;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Teacher\AttendanceRecordResource\Pages\EditAttendanceRecord;
use App\Filament\Resources\Teacher\AttendanceRecordResource\Pages\ViewAttendanceRecord;
use App\Filament\Resources\Teacher\AttendanceRecordResource\Pages\ListAttendanceRecords;
use App\Filament\Resources\Teacher\AttendanceRecordResource\Pages\CreateAttendanceRecord;

class AttendanceRecordResource extends Resource
{
    protected static ?string $model = AttendanceRecord::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $slug = 'Attendance-Records';
    protected static ?string $navigationGroup = 'Class Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('class_id')
                    ->label('Class')
                    ->options(function () {
                        $teacher = Teacher::where('user_id', '=', Auth::id())->first();
                        if ($teacher) {
                            return Classes::query()
                                ->where('teacher_id', '=', $teacher->teacher_id) 
                                ->pluck('class_name', 'class_id');
                        }
                        return collect();
                    })                    
                    ->reactive()
                    ->preload()
                    ->live()
                    ->required(),
                DateTimePicker::make('attendance_date')
                    ->label('Date')
                    ->date()
                    ->withoutTime()
                    ->format('Y-m-d')
                    ->required(),
                    
                Select::make('present_students')
                    ->label('Students (Lastname, Firstname, Middlename')
                    ->options(function ($get) {
                        $classId = $get('class_id');
                        if ($classId) {
                            $class = Classes::find($classId);
                            if ($class && isset($class->students)) {
                                $studentIds = $class->students;
                                $students = Student::whereIn('student_id', $studentIds)
                                ->select(DB::raw("CONCAT(last_name, ' ', first_name, ' ', 
                                                    IFNULL(SUBSTRING(middle_name, 1, 1), ''), 
                                                    CASE WHEN middle_name IS NOT NULL THEN '.' ELSE '' END) AS full_name"), 'student_id')
                                ->orderBy('last_name', 'asc')
                                ->pluck('full_name', 'student_id');
                                return $students;
                            }
                        }
                        return [];
                    })
                    ->helperText('Choose student that is present')
                    ->multiple()
                    ->searchable()
                    ->live()
                    ->preload()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $auth_user = Auth::id();
        $teacher = Teacher::where('user_id', $auth_user)->first();
        $classIds = Classes::where('teacher_id', $teacher->teacher_id)->pluck('class_id');

        return $table
            ->query(
                AttendanceRecord::query()->whereIn('class_id', $classIds)
            )
            ->columns([
                TextColumn::make('classes.class_name')
                   ->sortable()
                   ->searchable()
                   ->label('Class'),
                TextColumn::make('attendance_date')
                    ->label('Date')
                    ->date(),
                TextColumn::make('present_students')
                    ->label('Present')
                    ->getStateUsing(function ($record) {
                        
                        $presentStudents = $record->present_students;
                        
                        return is_array($presentStudents) ? count($presentStudents) : 0;
                    }),                
                TextColumn::make('students')
                    ->label('Absent')
                    ->getStateUsing(function ($record) {

                        $teacher = Teacher::where('user_id', '=', Auth::id())->first();

                        $totalStudents = 0;
                        $presentStudents = 0;
                        $absentStudent = 0;

                        if ($teacher) {
                            $class = DB::table('classes')
                                ->select('students')
                                ->where('class_id', $record->class_id)
                                ->where('teacher_id', $teacher->teacher_id)
                                ->first();

                            if ($class) {
                                $students = json_decode($class->students, true);
                                $totalStudents = is_array($students) ? count($students) : 0;

                                $presentStudents = $record->present_students ? count($record->present_students) : 0;

                                $absentStudent = $totalStudents - $presentStudents;
                            }
                        }

                        
                        return $absentStudent;
                        
                    }),
                TextColumn::make('%')
                    ->getStateUsing(function ($record) {

                        $teacher = Teacher::where('user_id',Auth::id())->first();

                        $totalStudents = 0;
                        $presentStudents = 0;
                        $attendance_percentage = 0;

                        if ($teacher) {
                            $class = DB::table('classes')
                                ->select('students')
                                ->where('class_id', $record->class_id)
                                ->where('teacher_id', $teacher->teacher_id)
                                ->first();

                            if ($class) {
                                $students = json_decode($class->students, true);
                                $totalStudents = is_array($students) ? count($students) : 0;
                                $presentStudents = $record->present_students ? count($record->present_students) : 0;

                                $attendance_percentage = ($presentStudents / $totalStudents) * 100;
                            }
                        }
                        
                        return $attendance_percentage . '%';
                    }),
                
                
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
            
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendanceRecords::route('/'),
            'create' => CreateAttendanceRecord::route('/create'),
            'view' => ViewAttendanceRecord::route('/{record}'),
            'edit' => EditAttendanceRecord::route('/{record}/edit'),
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
