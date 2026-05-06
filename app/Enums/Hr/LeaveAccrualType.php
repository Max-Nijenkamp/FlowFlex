<?php

namespace App\Enums\Hr;

enum LeaveAccrualType: string
{
    case Monthly          = 'monthly';
    case PerPayPeriod     = 'per_pay_period';
    case AnniversaryBased = 'anniversary_based';
    case Immediate        = 'immediate';

    public function label(): string
    {
        return match($this) {
            self::Monthly          => 'Monthly',
            self::PerPayPeriod     => 'Per Pay Period',
            self::AnniversaryBased => 'Anniversary Based',
            self::Immediate        => 'Immediate (Full entitlement on start)',
        };
    }
}
