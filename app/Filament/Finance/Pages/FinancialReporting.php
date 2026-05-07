<?php

namespace App\Filament\Finance\Pages;

use App\Filament\Finance\Enums\NavigationGroup;
use App\Filament\Finance\Widgets\FinancialSummaryWidget;
use Filament\Pages\Page;

class FinancialReporting extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.finance.pages.financial-reporting';

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Reports->label();
    }

    public static function getNavigationLabel(): string
    {
        return __('finance.resources.financial_reporting.nav_label');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('finance.resources.financial_reporting.page_title');
    }

    public static function canAccess(): bool
    {
        return auth('tenant')->user()?->can('finance.reports.view') ?? false;
    }

    public function getHeaderWidgets(): array
    {
        return [
            FinancialSummaryWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 2;
    }
}
