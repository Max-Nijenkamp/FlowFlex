<?php

namespace App\Filament\Crm\Enums;

enum NavigationGroup: string
{
    case Contacts = 'Contacts';
    case Sales    = 'Sales';
    case Support  = 'Support';

    public function label(): string
    {
        return match ($this) {
            self::Contacts => __('crm.navigation.groups.contacts'),
            self::Sales    => __('crm.navigation.groups.sales'),
            self::Support  => __('crm.navigation.groups.support'),
        };
    }
}
