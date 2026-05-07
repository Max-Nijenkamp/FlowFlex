<?php

namespace App\Filament\Finance\Widgets;

use App\Enums\Finance\ExpenseStatus;
use App\Enums\Finance\InvoiceStatus;
use App\Models\Finance\Expense;
use App\Models\Finance\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialSummaryWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    public ?string $period = 'this_month';

    protected function getStats(): array
    {
        [$start, $end] = $this->periodRange();

        $revenue = Invoice::query()
            ->where('status', InvoiceStatus::Paid)
            ->whereBetween('issue_date', [$start, $end])
            ->sum('total');

        $outstanding = Invoice::query()
            ->whereIn('status', [InvoiceStatus::Sent, InvoiceStatus::Overdue])
            ->sum(DB::raw('total - paid_amount'));

        $expenses = Expense::query()
            ->where('status', ExpenseStatus::Approved)
            ->whereBetween('expense_date', [$start, $end])
            ->sum('amount');

        $pl = $revenue - $expenses;

        return [
            Stat::make('Revenue', '€ ' . number_format($revenue, 2))
                ->description('Paid invoices ' . $start->format('M Y'))
                ->color($revenue > 0 ? 'success' : 'gray'),

            Stat::make('Expenses', '€ ' . number_format($expenses, 2))
                ->description('Approved expenses ' . $start->format('M Y'))
                ->color($expenses > 0 ? 'danger' : 'gray'),

            Stat::make('Net P&L', '€ ' . number_format($pl, 2))
                ->description('Revenue minus expenses')
                ->color($pl >= 0 ? 'success' : 'danger'),

            Stat::make('Outstanding', '€ ' . number_format($outstanding, 2))
                ->description('Unpaid sent & overdue invoices')
                ->color($outstanding > 0 ? 'warning' : 'success'),
        ];
    }

    private function periodRange(): array
    {
        return match ($this->period) {
            'last_month' => [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth(),
            ],
            'this_year' => [
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear(),
            ],
            default => [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ],
        };
    }
}
