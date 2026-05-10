<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\Core\BillingSubscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MrrStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $mrr = BillingSubscription::withoutGlobalScopes()
            ->where('status', 'active')
            ->sum('monthly_amount');

        $activeCount = BillingSubscription::withoutGlobalScopes()
            ->where('status', 'active')
            ->count();

        $pastDueCount = BillingSubscription::withoutGlobalScopes()
            ->where('status', 'past_due')
            ->count();

        return [
            Stat::make('Total MRR', '€' . number_format((float) $mrr / 100, 2))
                ->icon('heroicon-o-currency-euro')
                ->color('success'),
            Stat::make('Active Subscriptions', $activeCount)
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Past Due', $pastDueCount)
                ->icon('heroicon-o-exclamation-circle')
                ->color($pastDueCount > 0 ? 'warning' : 'success'),
        ];
    }
}
