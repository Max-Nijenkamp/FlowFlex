<?php

declare(strict_types=1);

namespace App\Filament\Finance\Widgets;

use App\Models\Finance\Expense;
use App\Models\Finance\Invoice;
use App\Models\User;
use App\Services\BillingService;
use Brick\Money\Money;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/** Finance dashboard stats: outstanding AR, overdue, pending expenses. */
class InvoiceStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '60s';

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('finance.invoices.view-any')
            && app(BillingService::class)->hasModule('finance.invoicing');
    }

    /** @return array<int, Stat> */
    protected function getStats(): array
    {
        $outstanding = (int) Invoice::query()
            ->whereIn('status', ['sent', 'partially_paid', 'overdue'])
            ->selectRaw('COALESCE(SUM(total_cents - paid_amount_cents), 0) as open_cents')
            ->value('open_cents');

        $overdueCount = Invoice::query()->where('status', 'overdue')->count();

        $pendingExpenses = Expense::query()->where('status', 'submitted')->count();

        return [
            Stat::make('Outstanding', Money::ofMinor($outstanding, 'EUR')->formatToLocale('nl_NL')),
            Stat::make('Overdue invoices', (string) $overdueCount)
                ->color($overdueCount > 0 ? 'danger' : 'success'),
            Stat::make('Expenses to approve', (string) $pendingExpenses)
                ->color($pendingExpenses > 0 ? 'warning' : 'success'),
        ];
    }
}
