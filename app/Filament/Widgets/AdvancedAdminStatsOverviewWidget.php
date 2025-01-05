<?php

namespace App\Filament\Widgets;

use App\Models\User;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;

class AdvancedAdminStatsOverviewWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('USERS', User::query()->count())
            ->backgroundColor('info')
            ->icon('heroicon-o-users')
            ->iconColor('info')
            ->iconBackgroundColor('info'),

            Stat::make('TEACHERS', User::whereHas('role', function($query) {
                    $query->where('role_type', 'teacher');
                })->count())
                ->backgroundColor('success')
                ->icon('heroicon-o-user')
                ->iconColor('success')
                ->iconBackgroundColor('success'),

            Stat::make('STUDENTS', User::whereHas('role', function($query) {
                    $query->where('role_type', 'student');
                })->count())
                ->backgroundColor('warning')
                ->icon('heroicon-o-academic-cap')
                ->iconBackgroundColor('warning')
                ->iconColor('warning'),
        ];
    }
}
