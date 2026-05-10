<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Http\Middleware\SetCompanyContext;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class HrPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('hr')
            ->path('hr')
            ->authGuard('web')
            ->login()
            ->passwordReset()
            ->brandName('FlowFlex HR')
            ->colors([
                'primary' => Color::Violet,
            ])
            ->maxContentWidth(Width::Full)
            ->sidebarCollapsibleOnDesktop()
            ->viteTheme('resources/css/filament/hr/theme.css')
            ->navigationGroups([
                NavigationGroup::make('Employees'),
                NavigationGroup::make('Leave'),
                NavigationGroup::make('Payroll'),
                NavigationGroup::make('Analytics'),
                NavigationGroup::make('Settings'),
            ])
            ->discoverResources(
                in: app_path('Filament/Hr/Resources'),
                for: 'App\\Filament\\Hr\\Resources',
            )
            ->discoverPages(
                in: app_path('Filament/Hr/Pages'),
                for: 'App\\Filament\\Hr\\Pages',
            )
            ->discoverWidgets(
                in: app_path('Filament/Hr/Widgets'),
                for: 'App\\Filament\\Hr\\Widgets',
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\SetLocale::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                SetCompanyContext::class,
            ]);
    }
}
