<?php

namespace App\Events\Projects;

use App\Models\Projects\Task;
use App\Models\Tenant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskAssigned
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Task $task,
        public readonly ?Tenant $assignee,
    ) {}
}
