<?php

namespace App\Filament\Widgets;

use App\Models\Teacher;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class AdminChartOverviewForTeacher extends ChartWidget
{
    protected static ?string $heading = 'Teachers Chart';

    protected static string $color = 'info';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Trend::model(Teacher::class)
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perWeek()
            ->count();
    
        return [
            'datasets' => [
                [
                    'label' => 'Teacher',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
