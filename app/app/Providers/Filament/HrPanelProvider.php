<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Http\Middleware\EnsureSubscriptionActive;
use App\Http\Middleware\SetCompanyContext;
use App\Http\Middleware\SetLocale;
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
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * HR & People domain panel (violet). Users need access.hr-panel; per-resource
 * gating via canAccess() on each artifact.
 */
class HrPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('hr')
            ->path('hr')
            ->authGuard('web')
            ->emailVerification() // no portal access without verified email (security.md)
            ->multiFactorAuthentication(AppAuthentication::make()->recoverable()) // self-service TOTP 2FA
            ->profile(isSimple: false)
            ->brandName('FlowFlex — HR & People')
            ->brandLogo(fn () => new HtmlString(
                '<img src="'.asset('images/logo/flowflex-logo-dark.svg').'" alt="FlowFlex" class="h-8 dark:hidden" />'
                .'<img src="'.asset('images/logo/flowflex-logo-light.svg').'" alt="FlowFlex" class="h-8 hidden dark:block" />',
            ))
            ->favicon(asset('images/logo/flowflex-icon.svg'))
            ->colors([
                'primary' => Color::Violet,
                'gray' => Color::Slate,
            ])
            ->font('Inter')
            ->defaultThemeMode(ThemeMode::System)
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->viteTheme('resources/css/filament/hr/theme.css')
            ->discoverResources(in: app_path('Filament/HR/Resources'), for: 'App\Filament\HR\Resources')
            ->discoverPages(in: app_path('Filament/HR/Pages'), for: 'App\Filament\HR\Pages')
            ->pages([Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/HR/Widgets'), for: 'App\Filament\HR\Widgets')
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
            ]);
        // Panel-level access: User::canAccessPanel requires access.hr-panel.
    }
}
