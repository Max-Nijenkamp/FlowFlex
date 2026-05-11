<?php

declare(strict_types=1);

namespace App\Services\Projects;

use App\Contracts\Projects\TaskServiceInterface;
use App\Data\Projects\CreateTaskData;
use App\Events\Projects\TaskCompleted;
use App\Events\Projects\TaskCreated;
use App\Models\Projects\Task;
use App\Support\Services\CompanyContext;

class TaskService implements TaskServiceInterface
{
    public function __construct(
        private readonly CompanyContext $companyContext,
    ) {}

    public function create(CreateTaskData $data): Task
    {
        $task = Task::create([
            'project_id'     => $data->project_id,
            'parent_id'      => $data->parent_id,
            'title'          => $data->title,
            'description'    => $data->description,
            'assignee_id'    => $data->assignee_id,
            'created_by'     => $data->created_by,
            'priority'       => $data->priority,
            'status'         => $data->status,
            'due_date'       => $data->due_date,
            'start_date'     => $data->start_date,
            'estimate_hours' => $data->estimate_hours,
            'story_points'   => $data->story_points,
            'labels'         => $data->labels,
        ]);

        event(new TaskCreated($task->company, $task));

        return $task;
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task->fresh();
    }

    public function complete(Task $task): Task
    {
        $task->update([
            'status'       => 'done',
            'completed_at' => now(),
        ]);

        event(new TaskCompleted($task->company, $task));

        return $task->fresh();
    }

    public function reorder(array $taskIds): void
    {
        if (empty($taskIds)) {
            return;
        }

        $bindings = [];
        $whenClauses = [];

        foreach ($taskIds as $sortOrder => $taskId) {
            $whenClauses[] = 'WHEN ? THEN ?';
            $bindings[] = $taskId;
            $bindings[] = (int) $sortOrder;
        }

        $inPlaceholders = implode(',', array_fill(0, count($taskIds), '?'));
        $whenSql = implode(' ', $whenClauses);

        // Add taskIds again for the IN clause
        $allBindings = array_merge($bindings, array_values($taskIds));

        \Illuminate\Support\Facades\DB::update(
            "UPDATE tasks SET sort_order = CASE id {$whenSql} ELSE sort_order END WHERE id IN ({$inPlaceholders})",
            $allBindings
        );
    }
}
