<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Models\CompanyModuleSubscription;
use App\Models\User;
use App\Support\Services\CompanyContext;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CompanyOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $ctx = app(CompanyContext::class);

        if (! $ctx->hasCompany()) {
            return [];
        }

        $companyId = $ctx->currentId();

        $teamMembers = User::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->count();

        $activeModules = CompanyModuleSubscription::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->count();

        $companyName = $ctx->current()->name;

        return [
            Stat::make('Team Members', $teamMembers)
                ->icon('heroicon-o-users')
                ->color('success'),
            Stat::make('Active Modules', $activeModules)
                ->icon('heroicon-o-puzzle-piece')
                ->color('primary'),
            Stat::make('Company', $companyName)
                ->icon('heroicon-o-building-office')
                ->color('info'),
        ];
    }
}
