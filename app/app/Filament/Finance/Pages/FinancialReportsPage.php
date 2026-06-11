<?php

declare(strict_types=1);

namespace App\Filament\Finance\Pages;

use App\Contracts\BillingServiceInterface;
use App\Contracts\Finance\ReportingServiceInterface;
use BackedEnum;
use Carbon\CarbonImmutable;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/** ui-strategy: custom report page — P&L, balance sheet, cash flow. */
class FinancialReportsPage extends Page
{
    protected string $view = 'filament.finance.pages.financial-reports';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Reports';

    protected static ?string $title = 'Financial Reports';

    /** Deferred first paint — blade shows a skeleton until wire:init fires. */
    public bool $readyToLoad = false;

    public function loadData(): void
    {
        $this->readyToLoad = true;
    }

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('finance.reporting.view')
            && app(BillingServiceInterface::class)->hasModule('finance.reporting');
    }

    /** @return array{revenue_cents: int, expense_cents: int, net_profit_cents: int, by_account: array<string, int>} */
    public function getProfitLoss(): array
    {
        return app(ReportingServiceInterface::class)->profitLoss(
            CarbonImmutable::now()->startOfYear(),
            CarbonImmutable::now(),
        );
    }

    /** @return array{assets_cents: int, liabilities_cents: int, equity_cents: int} */
    public function getBalanceSheet(): array
    {
        return app(ReportingServiceInterface::class)->balanceSheet(CarbonImmutable::now());
    }
}
