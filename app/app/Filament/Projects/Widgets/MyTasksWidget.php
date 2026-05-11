<?php

declare(strict_types=1);

namespace App\Filament\Projects\Widgets;

use App\Models\Projects\Task;
use App\Support\Services\CompanyContext;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MyTasksWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $ctx = app(CompanyContext::class);

        if (! $ctx->hasCompany() || ! auth()->check()) {
            return [];
        }

        $companyId = $ctx->currentId();
        $userId = auth()->id();

        $myOpenTasks = Task::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('assignee_id', $userId)
            ->whereNotIn('status', ['done', 'cancelled'])
            ->count();

        $dueToday = Task::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('assignee_id', $userId)
            ->whereDate('due_date', today())
            ->where('status', '!=', 'done')
            ->count();

        $overdue = Task::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('assignee_id', $userId)
            ->whereDate('due_date', '<', today())
            ->where('status', '!=', 'done')
            ->count();

        return [
            Stat::make('My Open Tasks', $myOpenTasks)
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary'),
            Stat::make('Due Today', $dueToday)
                ->icon('heroicon-o-calendar')
                ->color('warning'),
            Stat::make('Overdue', $overdue)
                ->icon('heroicon-o-exclamation-triangle')
                ->color($overdue > 0 ? 'danger' : 'gray'),
        ];
    }
}
