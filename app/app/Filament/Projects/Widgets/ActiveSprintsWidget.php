<?php

declare(strict_types=1);

namespace App\Filament\Projects\Widgets;

use App\Models\Projects\Sprint;
use App\Models\Projects\Task;
use App\Support\Services\CompanyContext;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ActiveSprintsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $ctx = app(CompanyContext::class);

        if (! $ctx->hasCompany()) {
            return [];
        }

        $companyId = $ctx->currentId();

        $activeSprints = Sprint::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->count();

        $activeSprintIds = Sprint::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->pluck('id');

        $tasksInProgress = Task::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('status', 'in_progress')
            ->whereHas('sprint', fn ($q) => $q->whereIn('sprints.id', $activeSprintIds)->withoutGlobalScopes())
            ->count();

        $tasksSprintDone = Task::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('status', 'done')
            ->whereHas('sprint', fn ($q) => $q->whereIn('sprints.id', $activeSprintIds)->withoutGlobalScopes())
            ->count();

        return [
            Stat::make('Active Sprints', $activeSprints)
                ->icon('heroicon-o-rocket-launch')
                ->color('success'),
            Stat::make('Tasks In Progress', $tasksInProgress)
                ->icon('heroicon-o-arrow-path')
                ->color('info'),
            Stat::make('Sprint Tasks Done', $tasksSprintDone)
                ->icon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
