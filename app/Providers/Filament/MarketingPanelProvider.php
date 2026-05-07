<?php

namespace App\Providers\Filament;

use App\Http\Middleware\AuthenticateTenant;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use App\Http\Middleware\SetLocaleFromCompany;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class MarketingPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('marketing')
            ->path('marketing')
            ->brandName('FlowFlex')
            ->colors([
                'primary' => Color::hex('#DB2777'),
                'gray'    => Color::Slate,
            ])
            ->discoverResources(in: app_path('Filament/Marketing/Resources'), for: 'App\Filament\Marketing\Resources')
            ->discoverPages(in: app_path('Filament/Marketing/Pages'), for: 'App\Filament\Marketing\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Marketing/Widgets'), for: 'App\Filament\Marketing\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
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
            ->authGuard('tenant')
            ->viteTheme('resources/css/filament/theme.css')
            ->authMiddleware([
                AuthenticateTenant::class,
                SetLocaleFromCompany::class,
            ]);
    }
}

