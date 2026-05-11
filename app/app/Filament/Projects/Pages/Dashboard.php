<?php

declare(strict_types=1);

namespace App\Filament\Projects\Pages;

use App\Filament\Projects\Widgets\ActiveSprintsWidget;
use App\Filament\Projects\Widgets\MyTasksWidget;
use App\Filament\Projects\Widgets\ProjectsOverviewWidget;
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
            ActiveSprintsWidget::class,
            MyTasksWidget::class,
            ProjectsOverviewWidget::class,
        ];
    }

    public function getColumns(): array|int
    {
        return 3;
    }
}
