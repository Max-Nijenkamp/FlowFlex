<?php

namespace App\Enums\Projects;

enum TaskStatus: string
{
    case Backlog    = 'backlog';
    case Todo       = 'todo';
    case InProgress = 'in_progress';
    case InReview   = 'in_review';
    case Done       = 'done';
    case Cancelled  = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Backlog    => 'Backlog',
            self::Todo       => 'To Do',
            self::InProgress => 'In Progress',
            self::InReview   => 'In Review',
            self::Done       => 'Done',
            self::Cancelled  => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Backlog    => 'gray',
            self::Todo       => 'gray',
            self::InProgress => 'warning',
            self::InReview   => 'info',
            self::Done       => 'success',
            self::Cancelled  => 'danger',
        };
    }
}
