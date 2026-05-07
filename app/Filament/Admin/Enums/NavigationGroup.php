<?php

namespace App\Filament\Admin\Enums;

enum NavigationGroup: string
{
    case Platform         = 'platform';
    case AccessControl    = 'access_control';
    case MarketingContent = 'marketing_content';

    public function label(): string
    {
        return __("admin.navigation.groups.{$this->value}");
    }
}
