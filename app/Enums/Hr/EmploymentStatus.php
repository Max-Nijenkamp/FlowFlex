<?php

namespace App\Enums\Hr;

enum EmploymentStatus: string
{
    case Active     = 'active';
    case OnLeave    = 'on_leave';
    case Probation  = 'probation';
    case Terminated = 'terminated';

    public function label(): string
    {
        return match($this) {
            self::Active     => 'Active',
            self::OnLeave    => 'On Leave',
            self::Probation  => 'Probation',
            self::Terminated => 'Terminated',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Active     => 'success',
            self::OnLeave    => 'warning',
            self::Probation  => 'info',
            self::Terminated => 'danger',
        };
    }
}
