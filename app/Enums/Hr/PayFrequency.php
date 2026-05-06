<?php

namespace App\Enums\Hr;

enum PayFrequency: string
{
    case Weekly     = 'weekly';
    case BiWeekly   = 'bi_weekly';
    case Monthly    = 'monthly';
    case FourWeekly = 'four_weekly';

    public function label(): string
    {
        return match($this) {
            self::Weekly     => 'Weekly',
            self::BiWeekly   => 'Bi-weekly',
            self::Monthly    => 'Monthly',
            self::FourWeekly => '4-Weekly',
        };
    }
}
