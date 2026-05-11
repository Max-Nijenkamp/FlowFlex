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

class ProjectsPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('projects')
            ->path('projects')
            ->authGuard('web')
            ->login()
            ->passwordReset()
            ->brandName('FlowFlex Projects')
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->maxContentWidth(Width::Full)
            ->sidebarCollapsibleOnDesktop()
            ->viteTheme('resources/css/filament/projects/theme.css')
            ->navigationGroups([
                NavigationGroup::make('Projects'),
                NavigationGroup::make('My Work'),
                NavigationGroup::make('Planning'),
                NavigationGroup::make('Time'),
                NavigationGroup::make('Analytics'),
                NavigationGroup::make('Settings'),
            ])
            ->discoverResources(
                in: app_path('Filament/Projects/Resources'),
                for: 'App\\Filament\\Projects\\Resources',
            )
            ->discoverPages(
                in: app_path('Filament/Projects/Pages'),
                for: 'App\\Filament\\Projects\\Pages',
            )
            ->discoverWidgets(
                in: app_path('Filament/Projects/Widgets'),
                for: 'App\\Filament\\Projects\\Widgets',
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
