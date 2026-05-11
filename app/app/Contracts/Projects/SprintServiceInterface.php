<?php

declare(strict_types=1);

namespace App\Contracts\Projects;

use App\Models\Projects\Project;
use App\Models\Projects\Sprint;
use App\Models\Projects\Task;

interface SprintServiceInterface
{
    public function createSprint(Project $project, array $data): Sprint;

    public function startSprint(Sprint $sprint): Sprint;

    public function completeSprint(Sprint $sprint): Sprint;

    public function addTask(Sprint $sprint, Task $task): void;
}
