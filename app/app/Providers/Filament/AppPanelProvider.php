<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Auth\PanelLogin;
use App\Http\Middleware\EnsureSubscriptionActive;
use App\Http\Middleware\RedirectToSetupWizard;
use App\Http\Middleware\SetCompanyContext;
use App\Http\Middleware\SetLocale;
use App\Models\User;
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
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * Tenant company workspace. web guard + User model + CompanyScope active.
 * Domain panels (hr, finance, ...) inherit these conventions.
 */
class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('app')
            ->path('app')
            ->login(PanelLogin::class)
            ->passwordReset() // forgot-password flow inside the workspace panel
            ->emailVerification() // no portal access without verified email (security.md)
            ->multiFactorAuthentication(AppAuthentication::make()->recoverable()) // self-service TOTP 2FA
            ->profile(isSimple: false)
            ->authGuard('web')
            ->authPasswordBroker('users')
            ->brandName('FlowFlex')
            ->brandLogo(asset('images/logo/flowflex-logo-dark.svg'))
            ->darkModeBrandLogo(asset('images/logo/flowflex-logo-light.svg'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('images/logo/flowflex-icon.svg'))
            ->colors([
                'primary' => Color::hex('#38BDF8'), // FlowFlex sky
                'gray' => Color::Slate,
            ])
            ->font('Inter')
            ->defaultThemeMode(ThemeMode::System)
            ->sidebarCollapsibleOnDesktop()
            ->userMenuItems(PanelSwitchItems::make('app')) // cross-panel switcher
            ->globalSearchKeyBindings(['mod+k'])
            ->viteTheme('resources/css/filament/app/theme.css')
            ->databaseNotifications() // bell + inbox (ui-strategy row #10; Reverb later)
            ->databaseNotificationsPolling('30s')
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\Filament\App\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\Filament\App\Pages')
            ->pages([Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\Filament\App\Widgets')
            ->widgets([AccountWidget::class])
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
                Authenticate::class,     // establishes the user first
                SetCompanyContext::class, // then sets tenant context (filament-patterns #7)
                SetLocale::class,        // locale from settings — needs context first
                EnsureSubscriptionActive::class, // suspended companies blocked
                RedirectToSetupWizard::class, // owners with incomplete setup → wizard
            ], isPersistent: true); // Livewire update POSTs must re-run these — deferred tables/actions 403 without tenant context
    }
}
