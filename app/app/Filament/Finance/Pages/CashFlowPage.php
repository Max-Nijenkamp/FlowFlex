<?php

declare(strict_types=1);

namespace App\Filament\Finance\Pages;

use App\Contracts\BillingServiceInterface;
use App\Models\Finance\CashflowProjection;
use App\Services\Finance\CashFlowService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/** 13-week rolling cash projection (ui-strategy custom page). */
class CashFlowPage extends Page
{
    protected string $view = 'filament.finance.pages.cash-flow';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Reports';

    protected static ?string $title = 'Cash Flow';

    /** Deferred first paint — blade shows a skeleton until wire:init fires. */
    public bool $readyToLoad = false;

    public function loadData(): void
    {
        app(CashFlowService::class)->rebuild();
        $this->readyToLoad = true;
    }

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('finance.cashflow.view')
            && app(BillingServiceInterface::class)->hasModule('finance.cashflow');
    }

    /** @return Collection<int, CashflowProjection> */
    public function getWeeks(): Collection
    {
        return app(CashFlowService::class)->projection();
    }
}
