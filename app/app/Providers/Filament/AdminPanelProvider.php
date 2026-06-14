<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\AdminLogin;
use App\Support\Filament\AppAuthenticationWithQrFix as AppAuthentication;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * FlowFlex staff console. admin guard + Admin model — NO CompanyScope,
 * never accessible to tenant users. Production: IP-allowlisted at nginx.
 */
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(AdminLogin::class)
            ->passwordReset() // forgot-password flow on the staff console too
            ->emailVerification() // no portal access without verified email (security.md)
            ->multiFactorAuthentication(AppAuthentication::make()->recoverable()) // self-service TOTP 2FA
            ->profile(isSimple: false)
            ->authGuard('admin')
            ->authPasswordBroker('admins')
            ->brandName('FlowFlex Staff')
            ->brandLogo(asset('images/logo/flowflex-logo-light.svg')) // light wordmark — sidebar is ink in both modes
            ->darkModeBrandLogo(asset('images/logo/flowflex-logo-light.svg'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('images/logo/flowflex-icon.svg'))
            ->colors([
                'primary' => Color::Indigo,
                'gray' => Color::Slate,
            ])
            ->font('Instrument Sans')
            ->defaultThemeMode(ThemeMode::System)
            ->sidebarCollapsibleOnDesktop()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\Filament\Admin\Widgets')
            ->widgets([AccountWidget::class])
            ->navigationItems([
                NavigationItem::make('Horizon')
                    ->url('/horizon', shouldOpenInNewTab: true)
                    ->icon(Heroicon::OutlinedQueueList)
                    ->group('Monitoring'),
                NavigationItem::make('Pulse')
                    ->url('/pulse', shouldOpenInNewTab: true)
                    ->icon(Heroicon::OutlinedChartBar)
                    ->group('Monitoring'),
            ])
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
            ], isPersistent: true); // Livewire update POSTs must re-run these — deferred tables/actions 403 without tenant context
    }
}
