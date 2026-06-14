<?php

declare(strict_types=1);

namespace App\Filament\CRM\Widgets;

use App\Models\CRM\Deal;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class WonDealsChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Won revenue — last 12 months';

    public static function canView(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('crm.deals.view-any');
    }

    protected function getData(): array
    {
        // PHP date grouping — driver-safe (two-databases guide).
        $won = Deal::query()
            ->where('status', 'won')
            ->where('actual_close_date', '>=', now()->startOfMonth()->subMonths(11))
            ->get(['actual_close_date', 'value_cents'])
            ->groupBy(fn (Deal $d): string => $d->actual_close_date?->format('Y-m') ?? '');

        $labels = [];
        $values = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->startOfMonth()->subMonths($i);
            $key = $month->format('Y-m');
            $labels[] = $month->format('M y');
            $values[] = round(((int) ($won->get($key)?->sum('value_cents') ?? 0)) / 100, 2);
        }

        return [
            'datasets' => [
                ['label' => 'Won (EUR)', 'data' => $values, 'fill' => 'start'],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
