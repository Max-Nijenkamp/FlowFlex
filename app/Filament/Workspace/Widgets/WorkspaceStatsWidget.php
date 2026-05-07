<?php

namespace App\Filament\Workspace\Widgets;

use App\Models\ApiKey;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WorkspaceStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $company = auth('tenant')->user()->company;

        $activeModules = $company->modules()->wherePivot('is_enabled', true)->count();
        $totalMembers  = $company->tenants()->where('is_enabled', true)->count();
        $apiKeys       = ApiKey::where('company_id', $company->id)->count();

        return [
            Stat::make('Active Modules', $activeModules)
                ->description('Modules enabled in this workspace')
                ->descriptionIcon('heroicon-m-puzzle-piece')
                ->color('primary'),

            Stat::make('Team Members', $totalMembers)
                ->description('Active users in this workspace')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('API Keys', $apiKeys)
                ->description('Configured integrations')
                ->descriptionIcon('heroicon-m-key')
                ->color('warning')
                ->url(route('filament.workspace.pages.manage-api-keys')),
        ];
    }
}
