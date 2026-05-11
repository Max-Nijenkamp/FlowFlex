<?php

declare(strict_types=1);

namespace App\Filament\Hr\Pages;

use App\Filament\Hr\Widgets\DepartmentBreakdownWidget;
use App\Filament\Hr\Widgets\HeadcountWidget;
use App\Filament\Hr\Widgets\LeaveStatsWidget;
use App\Services\Core\BillingService;
use App\Support\Services\CompanyContext;
use Filament\Pages\Page;

class HrAnalyticsPage extends Page
{
    public function getView(): string
    {
        return 'filament.hr.pages.hr-analytics';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-chart-bar';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Analytics';
    }

    public static function getNavigationLabel(): string
    {
        return 'HR Analytics';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function canAccess(): bool
    {
        if (! auth()->check()) {
            return false;
        }
        $ctx = app(CompanyContext::class);
        if (! $ctx->hasCompany()) {
            return false;
        }

        return app(BillingService::class)
            ->enforceModuleAccess($ctx->current(), 'hr.analytics');
    }

    protected function getWidgets(): array
    {
        return [
            HeadcountWidget::class,
            DepartmentBreakdownWidget::class,
            LeaveStatsWidget::class,
        ];
    }

    public function getTitle(): string
    {
        return 'HR Analytics';
    }
}
