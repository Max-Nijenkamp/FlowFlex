<?php

namespace App\Enums\Hr;

enum OnboardingTaskStatus: string
{
    case Pending    = 'pending';
    case InProgress = 'in_progress';
    case Completed  = 'completed';
    case Overdue    = 'overdue';
    case Skipped    = 'skipped';

    public function label(): string
    {
        return match($this) {
            self::Pending    => 'Pending',
            self::InProgress => 'In Progress',
            self::Completed  => 'Completed',
            self::Overdue    => 'Overdue',
            self::Skipped    => 'Skipped',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pending    => 'gray',
            self::InProgress => 'info',
            self::Completed  => 'success',
            self::Overdue    => 'danger',
            self::Skipped    => 'warning',
        };
    }
}
