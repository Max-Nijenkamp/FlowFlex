<?php

namespace App\Filament\Finance\Enums;

enum NavigationGroup: string
{
    case Invoices = 'Invoices';
    case Expenses = 'Expenses';
    case Reports  = 'Reports';

    public function label(): string
    {
        return match ($this) {
            self::Invoices => __('finance.navigation.groups.invoices'),
            self::Expenses => __('finance.navigation.groups.expenses'),
            self::Reports  => __('finance.navigation.groups.reports'),
        };
    }
}
