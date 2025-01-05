<?php

namespace App\Filament\Teacher\Widgets;

use App\Models\User;
use App\Models\Classes;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;

class AdvancedTeacherStatsOverviewWidget extends BaseWidget
{

    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('CLASSES', function () {
                $teacher = Teacher::where('user_id', Auth::id())->first();

                if ($teacher) {
                    return Classes::where('teacher_id', $teacher->teacher_id)->count();
                }
                return 0;

            })            
            ->backgroundColor('info')
            ->icon('heroicon-o-calendar-days')
            ->iconColor('info')
            ->iconBackgroundColor('info'),

            Stat::make('SUBJECTS', function () {
                $teacher = Teacher::where('user_id', Auth::id())->first();

                if ($teacher) {
                    return count($teacher->subjects_handle ?? []);
                }
                return 0;

            }) 
            ->backgroundColor('success')
            ->icon('heroicon-o-book-open')
            ->iconColor('success')
            ->iconBackgroundColor('success'),

            Stat::make('STUDENT', function () {
                $teacher = Teacher::where('user_id', Auth::id())->first();
            
                if ($teacher) {
                    $students = Classes::where('teacher_id', $teacher->teacher_id)
                        ->pluck('students')
                        ->filter()
                        ->flatMap(function ($studentData) {
                            $studentsArray = is_string($studentData) ? json_decode($studentData, true) : $studentData;
                            return $studentsArray ?? [];
                        })
                        ->unique();
            
                    return $students->count();
                }
            
                return 0;
            })
            ->backgroundColor('warning')
            ->icon('heroicon-o-academic-cap')
            ->iconBackgroundColor('warning')
            ->iconColor('warning')
            
        ];
    }
}
