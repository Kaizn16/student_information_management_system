<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class AdminChartOverviewForStudent extends ChartWidget
{
    protected static ?string $heading = 'Students Chart';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Trend::model(Student::class)
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perWeek()
            ->count();
    
        return [
            'datasets' => [
                [
                    'label' => 'Student',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
