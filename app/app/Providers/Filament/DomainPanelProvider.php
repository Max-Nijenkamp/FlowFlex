<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Auth\PanelLogin;
use App\Http\Middleware\EnsureSubscriptionActive;
use App\Http\Middleware\SetCompanyContext;
use App\Http\Middleware\SetLocale;
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

/**
 * Shared config for every domain panel (/hr, /finance, /crm, ...): same
 * Switchboard+ chrome as /app (sidebar footer, toggle, workspace switcher,
 * spotlight), domain accent color, access.{id} gate enforced by
 * User::canAccessPanel(). One concrete subclass per domain sets the four
 * properties (architecture/domain-panels.md template, code convention
 * access.{domain} not access.{domain}-panel).
 */
abstract class DomainPanelProvider extends PanelProvider
{
    protected string $panelId;

    protected string $panelPath;

    /** Panel accent from domain-panels.md color table. */
    protected string $accentHex;

    /** PascalCase Filament namespace segment, e.g. 'Crm'. */
    protected string $domainNamespace;

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id($this->panelId)
            ->path($this->panelPath)
            ->viteTheme("resources/css/filament/{$this->panelId}/theme.css")
            ->login(PanelLogin::class)
            ->authGuard('web')
            ->brandName('FlowFlex')
            ->colors([
                'primary' => Color::hex($this->accentHex),
                'gray' => Color::Slate,
            ])
            ->defaultThemeMode(ThemeMode::System)
            ->pages([
                Dashboard::class,
            ])
            ->discoverResources(in: app_path("Filament/{$this->domainNamespace}/Resources"), for: "App\\Filament\\{$this->domainNamespace}\\Resources")
            ->discoverPages(in: app_path("Filament/{$this->domainNamespace}/Pages"), for: "App\\Filament\\{$this->domainNamespace}\\Pages")
            ->discoverWidgets(in: app_path("Filament/{$this->domainNamespace}/Widgets"), for: "App\\Filament\\{$this->domainNamespace}\\Widgets")
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
            // Persistent so Livewire update POSTs re-run the context chain
            // (tenant-context-pitfalls.md — the null-team 403 family).
            ->authMiddleware([
                Authenticate::class,
                SetCompanyContext::class,
                SetLocale::class,
                EnsureSubscriptionActive::class,
            ], isPersistent: true);
    }
}
