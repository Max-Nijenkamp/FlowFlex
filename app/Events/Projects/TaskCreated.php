<?php

namespace App\Events\Projects;

use App\Models\Projects\Task;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Task $task) {}
}
