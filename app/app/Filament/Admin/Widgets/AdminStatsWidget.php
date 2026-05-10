<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\Company;
use App\Models\Core\BillingSubscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class AdminStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $mrr = BillingSubscription::withoutGlobalScopes()
            ->where('status', 'active')
            ->sum('monthly_amount');

        $activeCompanies = Company::withoutGlobalScopes()
            ->where('status', 'active')
            ->count();

        $failedJobs = DB::table('failed_jobs')->count();

        $pendingJobs = DB::table('jobs')->count();

        return [
            Stat::make('Monthly Recurring Revenue', '€' . number_format((float) $mrr / 100, 2))
                ->icon('heroicon-o-currency-euro')
                ->color('success'),
            Stat::make('Active Companies', $activeCompanies)
                ->icon('heroicon-o-building-office-2')
                ->color('primary'),
            Stat::make('Failed Jobs', $failedJobs)
                ->icon('heroicon-o-exclamation-triangle')
                ->color($failedJobs > 0 ? 'danger' : 'success'),
            Stat::make('Queue Depth', $pendingJobs)
                ->icon('heroicon-o-queue-list')
                ->color('gray'),
        ];
    }
}
