<?php

declare(strict_types=1);

namespace App\Filament\HR\Widgets;

use App\Models\HR\Employee;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class HeadcountChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Headcount growth — last 12 months';

    public static function canView(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('hr.profiles.view-any');
    }

    protected function getData(): array
    {
        // PHP date grouping — driver-safe (two-databases guide).
        $employees = Employee::query()
            ->whereNull('termination_date')
            ->get(['hire_date']);

        $labels = [];
        $values = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->startOfMonth()->subMonths($i);
            $labels[] = $month->format('M y');
            $values[] = $employees->filter(fn (Employee $e) => $e->hire_date !== null
                && $e->hire_date->lessThanOrEqualTo($month->copy()->endOfMonth()))->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Employees',
                    'data' => $values,
                    'fill' => 'start',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
