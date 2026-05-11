<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Filament\App\Widgets\CompanyOverviewWidget;
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

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public function getTitle(): string
    {
        $user = auth()->user();

        return "Welcome back, {$user->first_name}";
    }

    public function getWidgets(): array
    {
        return [
            CompanyOverviewWidget::class,
        ];
    }

    public function getColumns(): array|int
    {
        return 3;
    }
}
