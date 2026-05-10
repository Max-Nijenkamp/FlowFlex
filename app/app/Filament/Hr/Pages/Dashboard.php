<?php

declare(strict_types=1);

namespace App\Filament\Hr\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-home';
    }

    public static function getNavigationLabel(): string
    {
        return 'Dashboard';
    }

    public static function canAccess(): bool
    {
        return auth()->check();
    }
}
