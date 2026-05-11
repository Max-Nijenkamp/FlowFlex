<?php

declare(strict_types=1);

namespace App\Contracts\Projects;

use App\Data\Projects\CreateTaskData;
use App\Models\Projects\Task;

interface TaskServiceInterface
{
    public function create(CreateTaskData $data): Task;

    public function update(Task $task, array $data): Task;

    public function complete(Task $task): Task;

    public function reorder(array $taskIds): void;
}
