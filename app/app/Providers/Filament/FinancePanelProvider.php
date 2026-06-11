<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Http\Middleware\EnsureSubscriptionActive;
use App\Http\Middleware\SetCompanyContext;
use App\Http\Middleware\SetLocale;
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
            ->brandName('FlowFlex — Finance & Accounting')
            ->colors([
                'primary' => Color::Emerald,
                'gray' => Color::Slate,
            ])
            ->font('Inter')
            ->defaultThemeMode(ThemeMode::System)
            ->sidebarCollapsibleOnDesktop()
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
            ]);
        // Panel-level access: User::canAccessPanel requires access.finance-panel.
    }
}
