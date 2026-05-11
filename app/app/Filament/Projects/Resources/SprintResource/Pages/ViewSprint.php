<?php

declare(strict_types=1);

namespace App\Filament\Projects\Resources\SprintResource\Pages;

use App\Filament\Projects\Resources\SprintResource;
use App\Models\Projects\Sprint;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class ViewSprint extends Page
{
    protected static string $resource = SprintResource::class;

    protected string $view = 'filament.projects.pages.view-sprint';

    public Sprint $record;

    public function mount(Sprint $record): void
    {
        $this->record = $record->load(['project', 'tasks.assignee']);
    }

    public function getTitle(): string
    {
        return $this->record->name;
    }

    public function getBreadcrumbs(): array
    {
        return [
            SprintResource::getUrl() => 'Sprints',
            $this->record->name,
        ];
    }

    public function getTasksByStatus(): array
    {
        $columns = [
            'todo'        => ['label' => 'To Do',      'color' => 'gray',    'tasks' => []],
            'in_progress' => ['label' => 'In Progress', 'color' => 'info',    'tasks' => []],
            'in_review'   => ['label' => 'In Review',   'color' => 'warning', 'tasks' => []],
            'done'        => ['label' => 'Done',         'color' => 'success', 'tasks' => []],
        ];

        foreach ($this->record->tasks as $task) {
            $status = $task->status;
            if (isset($columns[$status])) {
                $columns[$status]['tasks'][] = $task;
            } else {
                $columns['todo']['tasks'][] = $task;
            }
        }

        return $columns;
    }

    public function getSprintProgress(): int
    {
        $tasks = $this->record->tasks;
        $total = $tasks->count();
        if ($total === 0) {
            return 0;
        }
        $done = $tasks->where('status', 'done')->count();

        return (int) round(($done / $total) * 100);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('start_sprint')
                ->label('Start Sprint')
                ->icon('heroicon-o-play')
                ->color('success')
                ->visible(fn () => $this->record->status === 'planning')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->update([
                        'status'     => 'active',
                        'start_date' => $this->record->start_date ?? now()->toDateString(),
                    ]);
                    $this->record->refresh();
                    Notification::make()->title('Sprint started')->success()->send();
                }),
            Action::make('complete_sprint')
                ->label('Complete Sprint')
                ->icon('heroicon-o-check')
                ->color('gray')
                ->visible(fn () => $this->record->status === 'active')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->update([
                        'status'   => 'completed',
                        'end_date' => $this->record->end_date ?? now()->toDateString(),
                    ]);
                    $this->record->refresh();
                    Notification::make()->title('Sprint completed')->success()->send();
                }),
            Action::make('edit')
                ->label('Edit Sprint')
                ->icon('heroicon-o-pencil')
                ->url(SprintResource::getUrl('edit', ['record' => $this->record])),
        ];
    }
}
