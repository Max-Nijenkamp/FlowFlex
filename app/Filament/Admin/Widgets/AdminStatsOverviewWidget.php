<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Company;
use App\Models\Marketing\DemoRequest;
use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class AdminStatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $now = now();

        // Companies
        $totalCompanies  = Company::withoutGlobalScopes()->count();
        $activeCompanies = Company::withoutGlobalScopes()->where('is_enabled', true)->count();
        $newThisMonth    = Company::withoutGlobalScopes()->where('created_at', '>=', $now->copy()->startOfMonth())->count();
        $newLastMonth    = Company::withoutGlobalScopes()
            ->whereBetween('created_at', [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()])
            ->count();

        // Daily new companies for past 7 days (sparkline)
        $companySpark = collect(range(6, 0))->map(
            fn (int $daysAgo) => Company::withoutGlobalScopes()
                ->whereDate('created_at', $now->copy()->subDays($daysAgo))
                ->count()
        )->toArray();

        // Tenants
        $totalTenants   = Tenant::withoutGlobalScopes()->count();
        $activeTenants  = Tenant::withoutGlobalScopes()->where('is_enabled', true)->count();
        $newTenants7d   = Tenant::withoutGlobalScopes()->where('created_at', '>=', $now->copy()->subDays(7))->count();

        $tenantSpark = collect(range(6, 0))->map(
            fn (int $daysAgo) => Tenant::withoutGlobalScopes()
                ->whereDate('created_at', $now->copy()->subDays($daysAgo))
                ->count()
        )->toArray();

        // Module activations
        $moduleActivations = DB::table('company_module')->where('is_enabled', true)->count();
        $avgModulesPerCompany = $totalCompanies > 0
            ? round($moduleActivations / max($totalCompanies, 1), 1)
            : 0;

        // Demo requests
        $openDemoRequests  = DemoRequest::withoutGlobalScopes()->where('status', 'new')->count();
        $demoRequests7d    = DemoRequest::withoutGlobalScopes()->where('created_at', '>=', $now->copy()->subDays(7))->count();

        $demoSpark = collect(range(6, 0))->map(
            fn (int $daysAgo) => DemoRequest::withoutGlobalScopes()
                ->whereDate('created_at', $now->copy()->subDays($daysAgo))
                ->count()
        )->toArray();

        // Month-over-month growth label
        $growthLabel = match(true) {
            $newLastMonth === 0 && $newThisMonth > 0 => '+' . $newThisMonth . ' vs last month',
            $newLastMonth === 0                      => 'First month tracked',
            default => ($newThisMonth >= $newLastMonth ? '+' : '') .
                       round((($newThisMonth - $newLastMonth) / $newLastMonth) * 100) . '% vs last month',
        };

        return [
            Stat::make('Total Companies', number_format($totalCompanies))
                ->description("{$activeCompanies} active · {$growthLabel}")
                ->descriptionIcon($newThisMonth >= $newLastMonth ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($companySpark)
                ->color($activeCompanies === $totalCompanies ? 'success' : 'primary')
                ->chartColor('primary'),

            Stat::make('Workspace Users', number_format($totalTenants))
                ->description("{$activeTenants} active · +{$newTenants7d} this week")
                ->descriptionIcon('heroicon-m-users')
                ->chart($tenantSpark)
                ->color('success')
                ->chartColor('success'),

            Stat::make('Module Activations', number_format($moduleActivations))
                ->description("Avg {$avgModulesPerCompany} modules per company")
                ->descriptionIcon('heroicon-m-puzzle-piece')
                ->color('warning'),

            Stat::make('Open Demo Requests', $openDemoRequests)
                ->description("+{$demoRequests7d} received this week")
                ->descriptionIcon('heroicon-m-calendar-days')
                ->chart($demoSpark)
                ->color($openDemoRequests > 10 ? 'danger' : ($openDemoRequests > 0 ? 'warning' : 'success'))
                ->chartColor('warning')
                ->url(route('filament.admin.resources.marketing.demo-requests.index')),
        ];
    }
}
