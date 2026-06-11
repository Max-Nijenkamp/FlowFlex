<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\BillingInvoice;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class RevenueChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Revenue — last 12 months';

    public static function canView(): bool
    {
        return Auth::guard('admin')->check();
    }

    protected function getData(): array
    {
        // Group in PHP, not SQL — date functions diverge between pgsql and
        // sqlite (two-databases guide).
        $paid = BillingInvoice::query()
            ->where('status', 'paid')
            ->where('paid_at', '>=', now()->startOfMonth()->subMonths(11))
            ->get(['paid_at', 'total_cents'])
            ->groupBy(fn (BillingInvoice $invoice): string => $invoice->paid_at->format('Y-m'));

        $labels = [];
        $values = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->startOfMonth()->subMonths($i);
            $key = $month->format('Y-m');

            $labels[] = $month->format('M y');
            $values[] = round(((int) ($paid->get($key)?->sum('total_cents') ?? 0)) / 100, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Paid revenue (EUR)',
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
