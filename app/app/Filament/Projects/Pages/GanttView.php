<?php

declare(strict_types=1);

namespace App\Filament\Projects\Pages;

use App\Models\Projects\Task;
use App\Support\Services\CompanyContext;
use Filament\Pages\Page;

class GanttView extends Page
{
    protected string $view = 'filament.projects.pages.gantt-view';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-chart-bar';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Projects';
    }

    public static function getNavigationLabel(): string
    {
        return 'Gantt';
    }

    public static function getNavigationSort(): ?int
    {
        return 6;
    }

    public function getTitle(): string
    {
        return 'Project Gantt';
    }

    public static function canAccess(): bool
    {
        if (! auth()->check()) {
            return false;
        }
        $ctx = app(CompanyContext::class);
        if (! $ctx->hasCompany()) {
            return false;
        }

        return app(\App\Services\Core\BillingService::class)
            ->enforceModuleAccess($ctx->current(), 'projects.tasks');
    }

    public function getTasks(): array
    {
        $companyId = app(CompanyContext::class)->currentId();

        return Task::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->whereNotNull('due_date')
            ->with('project', 'assignee')
            ->orderBy('due_date')
            ->get()
            ->map(fn ($task) => [
                'id'       => $task->id,
                'title'    => $task->title,
                'project'  => $task->project?->name ?? '—',
                'start'    => $task->created_at->format('Y-m-d'),
                'end'      => $task->due_date?->format('Y-m-d') ?? $task->created_at->format('Y-m-d'),
                'status'   => $task->status,
                'assignee' => $task->assignee?->email ?? '—',
            ])
            ->toArray();
    }
}
