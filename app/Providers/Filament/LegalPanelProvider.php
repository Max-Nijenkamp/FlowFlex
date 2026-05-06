<?php

namespace App\Providers\Filament;

use App\Http\Middleware\AuthenticateTenant;
use Filament\Http\Middleware\Authenticate;
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

class LegalPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('legal')
            ->path('legal')
            ->brandName('FlowFlex')
            ->colors([
                'primary' => Color::hex('#DC2626'),
                'gray'    => Color::Slate,
            ])
            ->discoverResources(in: app_path('Filament/Legal/Resources'), for: 'App\Filament\Legal\Resources')
            ->discoverPages(in: app_path('Filament/Legal/Pages'), for: 'App\Filament\Legal\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Legal/Widgets'), for: 'App\Filament\Legal\Widgets')
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

