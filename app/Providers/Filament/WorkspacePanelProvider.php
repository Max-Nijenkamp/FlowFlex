<?php

namespace App\Providers\Filament;

use App\Filament\Workspace\Pages\Auth\Login;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use App\Filament\Workspace\Widgets\ActiveModulesWidget;
use App\Filament\Workspace\Widgets\WorkspaceStatsWidget;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use App\Http\Middleware\SetLocaleFromCompany;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class WorkspacePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('workspace')
            ->path('workspace')
            ->login(Login::class)
            ->brandName('FlowFlex')
            ->brandLogo(fn () => new HtmlString($this->brandLogoSvg()))
            ->brandLogoHeight('30px')
            ->colors([
                'primary' => Color::hex('#2199C8'),
                'gray'    => Color::Slate,
            ])
            ->databaseNotifications()
            ->navigationGroups([
                NavigationGroup::make('Settings')->icon('heroicon-o-cog-6-tooth'),
            ])
            ->discoverResources(in: app_path('Filament/Workspace/Resources'), for: 'App\Filament\Workspace\Resources')
            ->discoverPages(in: app_path('Filament/Workspace/Pages'), for: 'App\Filament\Workspace\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Workspace/Widgets'), for: 'App\Filament\Workspace\Widgets')
            ->widgets([
                WorkspaceStatsWidget::class,
                ActiveModulesWidget::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => new HtmlString($this->loginPageAssets()),
            )
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
                Authenticate::class,
                SetLocaleFromCompany::class,
            ]);
    }

    private function brandLogoSvg(): string
    {
        return <<<'SVG'
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 148 32" height="30" role="img" aria-label="FlowFlex">
            <path d="M4,16 C8,7 15,7 19,16 C23,25 30,25 34,16"
                  stroke="#2199C8" stroke-width="2.6" fill="none" stroke-linecap="round" aria-hidden="true"/>
            <path d="M8,21 C13,10 21,10 26,21 C31,32 39,32 44,21"
                  stroke="#4BB3DC" stroke-width="2" fill="none" stroke-linecap="round" opacity="0.55"
                  aria-hidden="true"/>
            <text x="52" y="22"
                  font-family="Inter,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif"
                  font-size="17" font-weight="700" fill="currentColor" letter-spacing="-0.02em">Flow</text>
            <text x="91" y="22"
                  font-family="Inter,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif"
                  font-size="17" font-weight="700" fill="#2199C8" letter-spacing="-0.02em">Flex</text>
        </svg>
        SVG;
    }

    private function loginPageAssets(): string
    {
        return <<<'HTML'
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>
            /* â”€â”€ FlowFlex workspace panel â€” login overrides â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
               fi-simple-layout   = page wrapper (sets background)
               fi-simple-main     = THE card (bg-white, shadow, ring, rounded)
               fi-simple-header   = logo + heading + subheading block
            â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */

            /* Page background: slate-100 (#F3F4F6) */
            body,
            .fi-simple-layout {
                background-color: #F3F4F6;
            }

            /* Card: override fi-simple-main
               Filament default: bg-white shadow-xs ring-1 ring-gray-950/5 sm:rounded-xl px-6 py-12 sm:px-12 my-16 max-w-lg
               We want: border-1 #E5E7EB, radius-lg (8px), shadow-sm, space-8 (32px) padding, max 440px */
            .fi-simple-main {
                max-width: 440px !important;
                padding: 32px !important;
                border-radius: 8px !important;
                border: 1px solid #E5E7EB !important;
                background-color: #FFFFFF !important;
                /* Reset Tailwind ring + replace with shadow-sm */
                --tw-ring-shadow: 0 0 #0000 !important;
                --tw-shadow: 0 1px 3px rgba(10, 15, 20, 0.10), 0 1px 2px rgba(10, 15, 20, 0.06) !important;
                box-shadow: 0 1px 3px rgba(10, 15, 20, 0.10), 0 1px 2px rgba(10, 15, 20, 0.06) !important;
                animation: ff-card-enter 250ms cubic-bezier(0.4, 0, 0.2, 1) both;
            }

            /* Header: Filament renders flex-col items-center â€” just add bottom margin */
            .fi-simple-header {
                margin-bottom: 20px;
            }

            /* Logo spacing: override Filament's mb-4 (16px) â†’ 20px */
            .fi-simple-header .fi-logo {
                margin-bottom: 20px !important;
            }

            /* Heading: text-h3 â€” 22px / 600 / slate-900
               Filament default: text-2xl font-bold text-gray-950 */
            .fi-simple-header-heading {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
                font-size: 22px !important;
                font-weight: 600 !important;
                line-height: 1.4 !important;
                letter-spacing: -0.01em !important;
                color: #111827 !important;
            }

            /* Subheading: text-body-sm â€” 13px / 400 / slate-500
               Filament default: text-sm (14px) text-gray-500 mt-2 */
            .fi-simple-header-subheading {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
                font-size: 13px !important;
                font-weight: 400 !important;
                line-height: 1.5 !important;
                color: #6B7280 !important;
                margin-top: 4px !important;
            }

            /* Sidebar logo: SVG text + paths â†’ white on ocean-900 bg */
            .fi-sidebar .fi-logo svg text {
                fill: #FFFFFF !important;
            }
            .fi-sidebar .fi-logo svg path {
                stroke: #FFFFFF !important;
            }

            /* Card entrance: opacity + subtle upward shift (250ms, single element) */
            @keyframes ff-card-enter {
                from { opacity: 0; transform: translateY(6px); }
                to   { opacity: 1; transform: translateY(0); }
            }

            @media (prefers-reduced-motion: reduce) {
                .fi-simple-main {
                    animation: none !important;
                }
            }

            /* â”€â”€ Dark mode (.dark on <html> â€” Filament's class-based approach) â”€â”€ */
            .dark body,
            .dark .fi-simple-layout {
                background-color: #0F1117;
            }

            .dark .fi-simple-main {
                background-color: #1A1F2E !important;
                border-color: #2D3348 !important;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.30) !important;
            }

            .dark .fi-simple-header-heading {
                color: #F9FAFB !important;
            }

            .dark .fi-simple-header-subheading {
                color: #9CA3AF !important;
            }
        </style>
        HTML;
    }
}

