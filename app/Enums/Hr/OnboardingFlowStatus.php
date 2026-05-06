<?php

namespace App\Enums\Hr;

enum OnboardingFlowStatus: string
{
    case NotStarted = 'not_started';
    case InProgress = 'in_progress';
    case Completed  = 'completed';
    case Paused     = 'paused';

    public function label(): string
    {
        return match($this) {
            self::NotStarted => 'Not Started',
            self::InProgress => 'In Progress',
            self::Completed  => 'Completed',
            self::Paused     => 'Paused',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::NotStarted => 'gray',
            self::InProgress => 'info',
            self::Completed  => 'success',
            self::Paused     => 'warning',
        };
    }
}
