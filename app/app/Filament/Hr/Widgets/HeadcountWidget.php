<?php

declare(strict_types=1);

namespace App\Filament\Hr\Widgets;

use App\Models\HR\Employee;
use App\Support\Services\CompanyContext;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HeadcountWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $ctx = app(CompanyContext::class);

        if (! $ctx->hasCompany()) {
            return [];
        }

        $companyId = $ctx->currentId();

        $totalActive = Employee::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->count();

        $newHiresThisMonth = Employee::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->whereYear('hire_date', now()->year)
            ->whereMonth('hire_date', now()->month)
            ->count();

        $leaversThisMonth = Employee::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->whereNotNull('termination_date')
            ->whereYear('termination_date', now()->year)
            ->whereMonth('termination_date', now()->month)
            ->count();

        return [
            Stat::make('Active Employees', $totalActive)
                ->icon('heroicon-o-users')
                ->color('success'),
            Stat::make('New Hires This Month', $newHiresThisMonth)
                ->icon('heroicon-o-user-plus')
                ->color('primary'),
            Stat::make('Leavers This Month', $leaversThisMonth)
                ->icon('heroicon-o-user-minus')
                ->color($leaversThisMonth > 0 ? 'warning' : 'gray'),
        ];
    }
}
