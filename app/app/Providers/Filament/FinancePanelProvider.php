<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Http\Middleware\EnsureSubscriptionActive;
use App\Http\Middleware\SetCompanyContext;
use App\Http\Middleware\SetLocale;
use App\Support\Filament\PanelSwitchItems;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * Finance & Accounting domain panel (violet). Users need access.finance-panel; per-resource
 * gating via canAccess() on each artifact.
 */
class FinancePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('finance')
            ->path('finance')
            ->authGuard('web')
            ->emailVerification() // no portal access without verified email (security.md)
            ->multiFactorAuthentication(AppAuthentication::make()->recoverable()) // self-service TOTP 2FA
            ->profile(isSimple: false)
            ->brandName('FlowFlex — Finance & Accounting')
            ->brandLogo(asset('images/logo/flowflex-logo-dark.svg'))
            ->darkModeBrandLogo(asset('images/logo/flowflex-logo-light.svg'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('images/logo/flowflex-icon.svg'))
            ->colors([
                'primary' => Color::Emerald,
                'gray' => Color::Slate,
            ])
            ->font('Inter')
            ->defaultThemeMode(ThemeMode::System)
            ->sidebarCollapsibleOnDesktop()
            ->userMenuItems(PanelSwitchItems::make('finance')) // cross-panel switcher
            ->globalSearchKeyBindings(['mod+k'])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->viteTheme('resources/css/filament/finance/theme.css')
            ->discoverResources(in: app_path('Filament/Finance/Resources'), for: 'App\Filament\Finance\Resources')
            ->discoverPages(in: app_path('Filament/Finance/Pages'), for: 'App\Filament\Finance\Pages')
            ->pages([Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/Finance/Widgets'), for: 'App\Filament\Finance\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                SetCompanyContext::class,
                SetLocale::class,
                EnsureSubscriptionActive::class,
            ], isPersistent: true); // Livewire update POSTs must re-run these — deferred tables/actions 403 without tenant context
        // Panel-level access: User::canAccessPanel requires access.finance-panel.
    }
}
