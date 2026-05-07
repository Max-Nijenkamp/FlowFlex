<?php

namespace App\Filament\Hr\Enums;

enum NavigationGroup: string
{
    case People     = 'People';
    case Onboarding = 'Onboarding';
    case Leave      = 'Leave';
    case Payroll    = 'Payroll';

    public function label(): string
    {
        return match ($this) {
            self::People     => __('hr.navigation.groups.people'),
            self::Onboarding => __('hr.navigation.groups.onboarding'),
            self::Leave      => __('hr.navigation.groups.leave'),
            self::Payroll    => __('hr.navigation.groups.payroll'),
        };
    }
}
