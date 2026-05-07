<?php

namespace App\Filament\Projects\Enums;

enum NavigationGroup: string
{
    case Tasks        = 'Tasks';
    case TimeTracking = 'Time Tracking';
    case Documents    = 'Documents';

    public function label(): string
    {
        return match ($this) {
            self::Tasks        => __('projects.navigation.groups.tasks'),
            self::TimeTracking => __('projects.navigation.groups.time_tracking'),
            self::Documents    => __('projects.navigation.groups.documents'),
        };
    }
}
