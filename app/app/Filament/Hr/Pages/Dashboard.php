<?php

declare(strict_types=1);

namespace App\Filament\Hr\Pages;

use App\Filament\Hr\Widgets\DepartmentBreakdownWidget;
use App\Filament\Hr\Widgets\HeadcountWidget;
use App\Filament\Hr\Widgets\LeaveStatsWidget;
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

    public function getWidgets(): array
    {
        return [
            HeadcountWidget::class,
            DepartmentBreakdownWidget::class,
            LeaveStatsWidget::class,
        ];
    }

    public function getColumns(): array|int
    {
        return 2;
    }
}
