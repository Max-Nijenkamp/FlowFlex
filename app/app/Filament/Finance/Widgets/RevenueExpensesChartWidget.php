<?php

declare(strict_types=1);

namespace App\Filament\Finance\Widgets;

use App\Models\Finance\Expense;
use App\Models\Finance\Invoice;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class RevenueExpensesChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Revenue vs expenses — last 12 months';

    public static function canView(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('finance.ledger.view-any');
    }

    protected function getData(): array
    {
        // PHP date grouping — driver-safe (two-databases guide).
        $since = now()->startOfMonth()->subMonths(11);

        $revenue = Invoice::query()
            ->where('status', 'paid')
            ->where('issue_date', '>=', $since)
            ->get(['issue_date', 'total_cents'])
            ->groupBy(fn (Invoice $i): string => $i->issue_date->format('Y-m'));

        $expenses = Expense::query()
            ->where('expense_date', '>=', $since)
            ->get(['expense_date', 'amount_cents'])
            ->groupBy(fn (Expense $e): string => $e->expense_date->format('Y-m'));

        $labels = [];
        $revenueData = [];
        $expenseData = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->startOfMonth()->subMonths($i);
            $key = $month->format('Y-m');
            $labels[] = $month->format('M y');
            $revenueData[] = round(((int) ($revenue->get($key)?->sum('total_cents') ?? 0)) / 100, 2);
            $expenseData[] = round(((int) ($expenses->get($key)?->sum('amount_cents') ?? 0)) / 100, 2);
        }

        return [
            'datasets' => [
                ['label' => 'Revenue (EUR)', 'data' => $revenueData, 'fill' => 'start'],
                ['label' => 'Expenses (EUR)', 'data' => $expenseData],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
