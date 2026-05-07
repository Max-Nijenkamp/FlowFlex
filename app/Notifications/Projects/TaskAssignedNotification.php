<?php

namespace App\Notifications\Projects;

use App\Models\Projects\Task;
use App\Notifications\FlowFlexNotification;
use Illuminate\Notifications\Messages\MailMessage;

class TaskAssignedNotification extends FlowFlexNotification
{
    public function __construct(public readonly Task $task) {}

    public function notificationType(): string
    {
        return 'projects.task.assigned';
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Task Assigned to You: {$this->task->title}")
            ->greeting('Hello!')
            ->line("You have been assigned the task: \"{$this->task->title}\".");

        if ($this->task->due_date) {
            $mail->line("Due date: {$this->task->due_date->toDateString()}.");
        }

        return $mail
            ->action('View Task', url('/projects/tasks/' . $this->task->id))
            ->salutation('The FlowFlex Platform');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => $this->notificationType(),
            'task_id'    => $this->task->id,
            'task_title' => $this->task->title,
            'due_date'   => $this->task->due_date?->toDateString(),
        ];
    }
}
