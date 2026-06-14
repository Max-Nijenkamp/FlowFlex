<?php

declare(strict_types=1);

namespace App\Filament\Finance\Widgets;

use App\Models\Finance\BankAccount;
use App\Models\Finance\Expense;
use App\Models\Finance\Invoice;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class FinanceStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('finance.ledger.view-any');
    }

    protected function getStats(): array
    {
        $cash = (int) BankAccount::query()->sum('current_balance_cents');

        $openInvoices = Invoice::query()
            ->whereIn('status', ['sent', 'partially_paid', 'overdue'])
            ->get(['total_cents', 'paid_amount_cents', 'due_date']);

        $outstanding = (int) $openInvoices->sum(fn (Invoice $i): int => $i->total_cents - $i->paid_amount_cents);
        $overdueCount = $openInvoices->filter(fn (Invoice $i): bool => $i->due_date !== null && $i->due_date->isPast())->count();

        $expensesThisMonth = (int) Expense::query()
            ->whereBetween('expense_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount_cents');

        return [
            Stat::make('Cash position', '€'.number_format($cash / 100, 0))
                ->description('Across all bank accounts'),
            Stat::make('Outstanding AR', '€'.number_format($outstanding / 100, 0))
                ->description($openInvoices->count().' open invoices')
                ->color($outstanding > 0 ? 'warning' : 'success'),
            Stat::make('Overdue invoices', (string) $overdueCount)
                ->color($overdueCount > 0 ? 'danger' : 'success'),
            Stat::make('Expenses this month', '€'.number_format($expensesThisMonth / 100, 0)),
        ];
    }
}
