<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Contracts\BillingServiceInterface;
use App\Data\MarketplaceModuleData;
use App\Models\ModuleCatalogEntry;
use App\Models\User;
use App\Services\BillingService;
use App\Support\Services\CompanyContext;
use Brick\Money\Money;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Throwable;

/**
 * The switchboard (core.module-marketplace): catalog grid grouped by
 * domain, live price preview (unit x active users, brick/money), and
 * confirm-gated activate/deactivate that delegates to BillingService —
 * this page never writes a subscription row itself.
 */
class ModuleMarketplacePage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-plus';

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    protected static ?string $navigationLabel = 'Marketplace';

    protected static ?string $title = 'Module marketplace';

    protected static ?string $slug = 'module-marketplace-page';

    protected string $view = 'filament.app.pages.module-marketplace';

    /** Domain squares per the design system palette. */
    public const DOMAIN_COLORS = [
        'hr' => '#8B5CF6', 'finance' => '#10B981', 'crm' => '#F43F5E',
        'projects' => '#6366F1', 'comms' => '#3B82F6', 'support' => '#F97316',
        'core' => '#38BDF8',
    ];

    public string $search = '';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('core.marketplace.view')
            && app(BillingService::class)->hasModule('core.marketplace');
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);
    }

    /** @return Collection<string, Collection<int, MarketplaceModuleData>> grouped by domain */
    public function getModulesProperty(): Collection
    {
        $companyId = app(CompanyContext::class)->current()->id;
        $userCount = max(1, app(CompanyContext::class)->current()->users()->count());
        $active = app(BillingServiceInterface::class)->activeModules($companyId);
        $needle = mb_strtolower(trim($this->search));

        /** @var \Illuminate\Database\Eloquent\Collection<int, ModuleCatalogEntry> $entries */
        $entries = ModuleCatalogEntry::query()
            ->where('is_active', true)
            ->orderBy('domain')
            ->orderBy('name')
            ->get();

        return $entries
            ->filter(fn (ModuleCatalogEntry $entry): bool => $needle === ''
                || str_contains(mb_strtolower($entry->name), $needle)
                || str_contains(mb_strtolower($entry->module_key), $needle)
                || str_contains(mb_strtolower($entry->domain), $needle))
            ->map(fn (ModuleCatalogEntry $entry): MarketplaceModuleData => new MarketplaceModuleData(
                module_key: $entry->module_key,
                domain: $entry->domain,
                name: $entry->name,
                per_user_monthly_price: $entry->per_user_monthly_price,
                price_preview: $entry->isFree()
                    ? 'Included'
                    : Money::ofMinor($entry->per_user_monthly_price, 'EUR')->multipliedBy($userCount)->formatToLocale('nl_NL').' / month',
                is_free: $entry->isFree(),
                is_subscribed: in_array($entry->module_key, $active, true),
            ))
            ->groupBy('domain');
    }

    public function activateAction(): Action
    {
        return Action::make('activate')
            ->label('Activate')
            ->requiresConfirmation()
            ->modalHeading('Activate module')
            ->modalDescription(fn (array $arguments): string => 'Billing starts on your next monthly invoice for "'.($arguments['name'] ?? $arguments['key']).'".')
            ->visible(function (): bool {
                $user = Auth::user();

                return $user instanceof User && $user->can('core.billing.activate-module');
            })
            ->action(function (array $arguments): void {
                $user = Auth::user();

                try {
                    app(BillingServiceInterface::class)->activateModule((string) $arguments['key'], $user instanceof User ? $user : throw new \RuntimeException('No user'));
                    Notification::make()->success()->title('Module activated')->send();
                } catch (Throwable $e) {
                    Notification::make()->danger()->title($e->getMessage())->send();
                }
            });
    }

    public function deactivateAction(): Action
    {
        return Action::make('deactivate')
            ->label('Deactivate')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Deactivate module')
            ->modalDescription('Access switches off for your whole team. Your data is kept and comes back on reactivation.')
            ->visible(function (): bool {
                $user = Auth::user();

                return $user instanceof User && $user->can('core.billing.deactivate-module');
            })
            ->action(function (array $arguments): void {
                try {
                    app(BillingServiceInterface::class)->deactivateModule((string) $arguments['key']);
                    Notification::make()->success()->title('Module deactivated')->send();
                } catch (Throwable $e) {
                    Notification::make()->danger()->title($e->getMessage())->send();
                }
            });
    }
}
