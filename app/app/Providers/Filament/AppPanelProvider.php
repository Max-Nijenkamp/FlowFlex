<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Auth\EditProfile;
use App\Filament\Auth\PanelLogin;
use App\Filament\Auth\RequestPasswordReset;
use App\Http\Middleware\EnsureSubscriptionActive;
use App\Http\Middleware\RedirectToSetupWizard;
use App\Http\Middleware\SetCompanyContext;
use App\Http\Middleware\SetLocale;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Enums\ThemeMode;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('app')
            ->path('app')
            ->viteTheme('resources/css/filament/app/theme.css')
            ->login(PanelLogin::class)
            ->passwordReset(RequestPasswordReset::class)
            ->emailVerification()
            ->multiFactorAuthentication(AppAuthentication::make()->recoverable())
            ->profile(EditProfile::class, isSimple: false)
            ->authGuard('web')
            ->authPasswordBroker('users')
            ->brandName('FlowFlex')
            ->colors([
                'primary' => Color::hex('#38BDF8'),
                'gray' => Color::Slate,
            ])
            ->defaultThemeMode(ThemeMode::System)
            ->pages([
                Dashboard::class,
            ])
            ->renderHook(PanelsRenderHook::SIDEBAR_FOOTER, fn () => view('filament.chrome.sidebar-footer'))
            ->renderHook(PanelsRenderHook::SIDEBAR_LOGO_AFTER, fn () => view('filament.chrome.sidebar-toggle'))
            ->renderHook(PanelsRenderHook::GLOBAL_SEARCH_BEFORE, fn () => view('filament.chrome.search-trigger'))
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Filament::auth()->check()
                    ? Blade::render("@livewire('spotlight')")
                    : '',
            )
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
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
            ])
            // Persistent so Livewire update POSTs re-run the context chain —
            // without it, deferred tables/actions 403 (the null-team family).
            ->authMiddleware([
                Authenticate::class,
                SetCompanyContext::class,
                SetLocale::class,
                EnsureSubscriptionActive::class,
                RedirectToSetupWizard::class,
            ], isPersistent: true);
    }
}
