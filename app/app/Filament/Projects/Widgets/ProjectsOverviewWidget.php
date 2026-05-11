<?php

declare(strict_types=1);

namespace App\Filament\Projects\Widgets;

use App\Models\Projects\Project;
use App\Models\Projects\Task;
use App\Support\Services\CompanyContext;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProjectsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $ctx = app(CompanyContext::class);

        if (! $ctx->hasCompany()) {
            return [];
        }

        $companyId = $ctx->currentId();

        $activeProjects = Project::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->count();

        $totalTasks = Task::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->count();

        $completedTasks = Task::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('status', 'done')
            ->count();

        return [
            Stat::make('Active Projects', $activeProjects)
                ->icon('heroicon-o-folder-open')
                ->color('success'),
            Stat::make('Total Tasks', $totalTasks)
                ->icon('heroicon-o-bars-3')
                ->color('info'),
            Stat::make('Completed Tasks', $completedTasks)
                ->icon('heroicon-o-check')
                ->color('success'),
        ];
    }
}
