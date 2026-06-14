<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Contracts\BillingServiceInterface;
use App\Data\ActivateModuleData;
use App\Data\MarketplaceModuleData;
use App\Exceptions\CannotDeactivateCoreModuleException;
use App\Exceptions\ModuleAlreadyActiveException;
use App\Models\ModuleCatalog;
use App\Models\User;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Activation UI over core.billing — no business logic of its own.
 * ui-strategy row #3-style custom page (grid, no drag).
 */
class ModuleMarketplacePage extends Page
{
    protected string $view = 'filament.app.pages.module-marketplace';

    /** Deferred first paint — blade shows a skeleton until wire:init fires. */
    public bool $readyToLoad = false;

    /** Live search over module name / key / domain. */
    public string $search = '';

    public function loadData(): void
    {
        $this->readyToLoad = true;
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquaresPlus;

    protected static string|UnitEnum|null $navigationGroup = 'Billing';

    protected static ?string $title = 'Module Marketplace';

    public static function canAccess(): bool
    {
        // Owner-only: module activation changes the company's bill (founder
        // decision 2026-06-11) — permission alone is not enough.
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->hasRole('owner')
            && Auth::guard('web')->user()->can('core.marketplace.view')
            && app(BillingServiceInterface::class)->hasModule('core.marketplace');
    }

    /** @return array<string, list<MarketplaceModuleData>> grouped by domain */
    public function getModulesByDomain(): array
    {
        $billing = app(BillingServiceInterface::class);
        $active = $billing->activeModuleKeys();
        $userCount = User::query()->count();

        $term = mb_strtolower(trim($this->search));

        return collect(ModuleCatalog::entries())
            ->filter(fn (array $m) => $m['is_active'])
            ->filter(fn (array $m) => $term === ''
                || str_contains(mb_strtolower($m['name']), $term)
                || str_contains(mb_strtolower($m['module_key']), $term)
                || str_contains(mb_strtolower($m['domain']), $term))
            ->map(fn (array $m) => new MarketplaceModuleData(
                module_key: $m['module_key'],
                name: $m['name'],
                domain: $m['domain'],
                per_user_monthly_price_cents: $m['per_user_monthly_price_cents'],
                price_preview_cents: $m['per_user_monthly_price_cents'] * $userCount,
                is_active_for_company: in_array($m['module_key'], $active, true),
                is_free_core: $m['is_free_core'],
            ))
            ->groupBy('domain')
            ->map(fn ($group) => $group->values()->all())
            ->all();
    }

    public function canManage(): bool
    {
        return Auth::guard('web')->user()->can('core.billing.activate-module');
    }

    /** Monthly cost preview for the currently active paid modules. */
    public function getActiveMonthlyCents(): int
    {
        $active = app(BillingServiceInterface::class)->activeModuleKeys();
        $userCount = User::query()->count();

        return collect($active)
            ->map(fn (string $key): int => ModuleCatalog::priceCents($key) * $userCount)
            ->sum();
    }

    public function getActiveCount(): int
    {
        return count(app(BillingServiceInterface::class)->activeModuleKeys());
    }

    public function activate(string $moduleKey): void
    {
        abort_unless($this->canManage(), 403);

        try {
            app(BillingServiceInterface::class)->activateModule(new ActivateModuleData($moduleKey));
            Notification::make()->success()->title('Module activated')->send();
        } catch (ModuleAlreadyActiveException|\InvalidArgumentException $e) {
            Notification::make()->danger()->title($e->getMessage())->send();
        }
    }

    public function deactivate(string $moduleKey): void
    {
        abort_unless(Auth::guard('web')->user()->can('core.billing.deactivate-module'), 403);

        try {
            app(BillingServiceInterface::class)->deactivateModule($moduleKey);
            Notification::make()->success()->title('Module deactivated — data retained')->send();
        } catch (CannotDeactivateCoreModuleException $e) {
            Notification::make()->danger()->title($e->getMessage())->send();
        }
    }
}
