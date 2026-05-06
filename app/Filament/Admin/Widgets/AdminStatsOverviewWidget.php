<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Company;
use App\Models\Module;
use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalCompanies   = Company::count();
        $activeCompanies  = Company::where('is_enabled', true)->count();
        $totalTenants     = Tenant::count();
        $activeTenants    = Tenant::where('is_enabled', true)->count();
        $moduleActivations = \DB::table('company_module')->where('is_enabled', true)->count();
        $newCompanies30d  = Company::where('created_at', '>=', now()->subDays(30))->count();

        return [
            Stat::make('Companies', $totalCompanies)
                ->description("{$activeCompanies} active")
                ->descriptionIcon('heroicon-o-building-office-2')
                ->color('primary'),

            Stat::make('Workspace Users', $totalTenants)
                ->description("{$activeTenants} active")
                ->descriptionIcon('heroicon-o-users')
                ->color('success'),

            Stat::make('Module Activations', $moduleActivations)
                ->description('Across all companies')
                ->descriptionIcon('heroicon-o-puzzle-piece')
                ->color('warning'),

            Stat::make('New Companies (30d)', $newCompanies30d)
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('info'),
        ];
    }
}
