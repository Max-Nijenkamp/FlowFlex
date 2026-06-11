<?php

declare(strict_types=1);

namespace App\Filament\CRM\Pages;

use App\Contracts\BillingServiceInterface;
use App\Services\CRM\SalesForecastService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ForecastPage extends Page
{
    protected string $view = 'filament.crm.pages.forecast';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static string|UnitEnum|null $navigationGroup = 'Sales';

    protected static ?string $title = 'Forecast';

    /** Deferred first paint — blade shows a skeleton until wire:init fires. */
    public bool $readyToLoad = false;

    public function loadData(): void
    {
        $this->readyToLoad = true;
    }

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('crm.forecasting.view-team')
            && app(BillingServiceInterface::class)->hasModule('crm.forecasting');
    }

    /** @return array{categories: array<string, int>, weighted_cents: int, quota_cents: int, attainment: float, coverage: float} */
    public function getForecast(): array
    {
        return app(SalesForecastService::class)->forecast(now()->format('Y').'-Q'.now()->quarter);
    }
}
