<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Http\Middleware\EnsureSubscriptionActive;
use App\Http\Middleware\RedirectToSetupWizard;
use App\Http\Middleware\SetCompanyContext;
use App\Http\Middleware\SetLocale;
use App\Models\User;
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
use Illuminate\Support\HtmlString;
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
            ->login()
            ->emailVerification() // no portal access without verified email (security.md)
            ->multiFactorAuthentication(AppAuthentication::make()->recoverable()) // self-service TOTP 2FA
            ->profile(isSimple: false)
            ->authGuard('web')
            ->authPasswordBroker('users')
            ->brandName('FlowFlex')
            ->brandLogo(fn () => new HtmlString(
                '<img src="'.asset('images/logo/flowflex-logo-dark.svg').'" alt="FlowFlex" class="h-8 dark:hidden" />'
                .'<img src="'.asset('images/logo/flowflex-logo-light.svg').'" alt="FlowFlex" class="h-8 hidden dark:block" />',
            ))
            ->favicon(asset('images/logo/flowflex-icon.svg'))
            ->colors([
                'primary' => Color::hex('#38BDF8'), // FlowFlex sky
                'gray' => Color::Slate,
            ])
            ->font('Inter')
            ->defaultThemeMode(ThemeMode::System)
            ->sidebarCollapsibleOnDesktop()
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
            ]);
    }
}
