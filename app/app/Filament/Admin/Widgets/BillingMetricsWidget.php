<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Contracts\BillingServiceInterface;
use App\Models\Company;
use Carbon\CarbonImmutable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * MRR / churn / active companies (core.billing-engine/admin-metrics).
 * Staff panel only; read-only; 60s poll.
 */
class BillingMetricsWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $billing = app(BillingServiceInterface::class);

        return [
            Stat::make('MRR', $billing->mrr()->formatToLocale('nl_NL'))
                ->description('Active paid modules × users'),
            Stat::make('Churn', number_format($billing->churnRate(CarbonImmutable::now()) * 100, 1).'%')
                ->description('Companies with a paid deactivation this month'),
            Stat::make('Companies', (string) Company::query()->count())
                ->description(Company::query()->where('subscription_status', 'suspended')->count().' suspended'),
        ];
    }
}
