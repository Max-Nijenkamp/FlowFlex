<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\BillingInvoice;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\ModuleCatalog;
use App\Models\User;
use Brick\Money\Money;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PlatformStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return Auth::guard('admin')->check();
    }

    protected function getStats(): array
    {
        $byStatus = Company::query()
            ->selectRaw('subscription_status, count(*) as total')
            ->groupBy('subscription_status')
            ->pluck('total', 'subscription_status');

        $paidThisMonth = (int) BillingInvoice::query()
            ->where('status', 'paid')
            ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('total_cents');

        $outstanding = (int) BillingInvoice::query()
            ->whereIn('status', ['open', 'past_due'])
            ->sum('total_cents');

        return [
            Stat::make('Companies', (string) $byStatus->sum())
                ->description(sprintf(
                    '%d active · %d trialing · %d suspended',
                    $byStatus->get('active', 0),
                    $byStatus->get('trialing', 0),
                    $byStatus->get('suspended', 0),
                )),
            Stat::make('Revenue this month', $this->euros($paidThisMonth))
                ->description('Paid invoices, current month')
                ->color('success'),
            Stat::make('Outstanding', $this->euros($outstanding))
                ->description('Open + past-due invoices')
                ->color($outstanding > 0 ? 'warning' : 'success'),
            Stat::make('MRR estimate', $this->euros($this->mrrCents()))
                ->description('Active paid modules × users, all non-suspended companies'),
        ];
    }

    /**
     * Mirrors generateMonthlyInvoice pricing: per company,
     * Σ(paid module price × user count).
     */
    private function mrrCents(): int
    {
        $companyIds = Company::query()
            ->where('subscription_status', '!=', 'suspended')
            ->pluck('id');

        $userCounts = User::query()
            ->selectRaw('company_id, count(*) as total')
            ->whereIn('company_id', $companyIds)
            ->groupBy('company_id')
            ->pluck('total', 'company_id');

        $subscriptions = CompanyModuleSubscription::query()
            ->whereIn('company_id', $companyIds)
            ->whereNull('deactivated_at')
            ->get(['company_id', 'module_key'])
            ->groupBy('company_id');

        $total = 0;

        foreach ($subscriptions as $companyId => $modules) {
            $users = (int) $userCounts->get($companyId, 0);

            foreach ($modules as $subscription) {
                $total += ModuleCatalog::priceCents($subscription->module_key) * $users;
            }
        }

        return $total;
    }

    private function euros(int $cents): string
    {
        return (string) Money::ofMinor($cents, 'EUR');
    }
}
