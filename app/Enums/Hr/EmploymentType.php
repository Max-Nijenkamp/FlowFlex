<?php

namespace App\Enums\Hr;

enum EmploymentType: string
{
    case FullTime   = 'full_time';
    case PartTime   = 'part_time';
    case Contractor = 'contractor';
    case ZeroHours  = 'zero_hours';

    public function label(): string
    {
        return match($this) {
            self::FullTime   => 'Full-time',
            self::PartTime   => 'Part-time',
            self::Contractor => 'Contractor',
            self::ZeroHours  => 'Zero Hours',
        };
    }
}
