<?php

namespace App\Enums\Projects;

enum TaskPriority: string
{
    case Critical = 'p1_critical';
    case High     = 'p2_high';
    case Medium   = 'p3_medium';
    case Low      = 'p4_low';

    public function label(): string
    {
        return match ($this) {
            self::Critical => 'Critical',
            self::High     => 'High',
            self::Medium   => 'Medium',
            self::Low      => 'Low',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Critical => 'danger',
            self::High     => 'warning',
            self::Medium   => 'info',
            self::Low      => 'gray',
        };
    }
}
