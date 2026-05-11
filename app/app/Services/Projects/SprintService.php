<?php

declare(strict_types=1);

namespace App\Services\Projects;

use App\Contracts\Projects\SprintServiceInterface;
use App\Models\Projects\Project;
use App\Models\Projects\Sprint;
use App\Models\Projects\Task;

class SprintService implements SprintServiceInterface
{
    public function createSprint(Project $project, array $data): Sprint
    {
        return Sprint::create(array_merge($data, [
            'project_id' => $project->id,
            'status'     => $data['status'] ?? 'planning',
        ]));
    }

    public function startSprint(Sprint $sprint): Sprint
    {
        $sprint->update([
            'status'     => 'active',
            'start_date' => $sprint->start_date ?? now()->toDateString(),
        ]);

        return $sprint->fresh();
    }

    public function completeSprint(Sprint $sprint): Sprint
    {
        $completedPoints = $sprint->tasks()
            ->where('status', 'done')
            ->sum('story_points');

        $sprint->update([
            'status'   => 'completed',
            'end_date' => $sprint->end_date ?? now()->toDateString(),
            'velocity' => $completedPoints ?: null,
        ]);

        return $sprint->fresh();
    }

    public function addTask(Sprint $sprint, Task $task): void
    {
        $sprint->tasks()->syncWithoutDetaching([$task->id]);
    }
}
