<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Contracts\Core\BillingServiceInterface;
use App\Data\Core\ActivateModuleData;
use App\Data\Core\MarketplaceModuleData;
use App\Exceptions\Core\CannotDeactivateCoreModuleException;
use App\Exceptions\Core\ModuleAlreadyActiveException;
use App\Models\Core\ModuleCatalog;
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

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquaresPlus;

    protected static string|UnitEnum|null $navigationGroup = 'Billing';

    protected static ?string $title = 'Module Marketplace';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('core.marketplace.view')
            && app(BillingServiceInterface::class)->hasModule('core.marketplace');
    }

    /** @return array<string, list<MarketplaceModuleData>> grouped by domain */
    public function getModulesByDomain(): array
    {
        $billing = app(BillingServiceInterface::class);
        $active = $billing->activeModuleKeys();
        $userCount = User::query()->count();

        return collect(ModuleCatalog::entries())
            ->filter(fn (array $m) => $m['is_active'])
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

    public function activate(string $moduleKey): void
    {
        abort_unless($this->canManage(), 403);

        try {
            app(BillingServiceInterface::class)->activateModule(new ActivateModuleData($moduleKey));
            Notification::make()->success()->title("Module activated")->send();
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
