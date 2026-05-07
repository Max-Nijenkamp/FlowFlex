<?php

namespace App\Listeners\Projects;

use App\Events\Projects\TaskAssigned;
use App\Notifications\Projects\TaskAssignedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyAssigneeTaskAssigned implements ShouldQueue
{
    public function handle(TaskAssigned $event): void
    {
        $assignee = $event->task->assignee ?? $event->assignee;

        if (! $assignee) {
            return;
        }

        $assignee->notify(new TaskAssignedNotification($event->task));
    }
}
