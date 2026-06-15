<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Responses\GuardScopedLoginResponse;
use App\Support\Filament\SidebarFooter;
use App\Support\Services\CompanyContext;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\HorizonCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\RedisCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CompanyContext::class);

        // Guard-scoped login redirects — one shared url.intended otherwise
        // bounces customers to the staff login and vice versa.
        $this->app->bind(LoginResponseContract::class, GuardScopedLoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Catch lazy-loading bugs in local dev only (kept lenient in tests + prod).
        Model::preventLazyLoading($this->app->environment('local'));

        // UX states (architecture/patterns/ux-states.md): every empty table
        // speaks human and offers the next step — resources only override
        // when they have something better to say.
        Table::configureUsing(function (Table $table): void {
            $table
                ->emptyStateHeading('Nothing here yet')
                ->emptyStateDescription('The moment you add your first record, it shows up here.');
        });

        // Pulse dashboard: FlowFlex staff only (mirrors Horizon::auth).
        Gate::define('viewPulse', fn ($user = null): bool => $this->app->environment('local')
            || Auth::guard('admin')->check());

        // Panel auth pages: brand mark above the card (public-login parity).
        // SIMPLE_LAYOUT_START renders outside fi-simple-main — SIMPLE_PAGE_START
        // sits inside the card, which put the mark in the container.
        FilamentView::registerRenderHook(
            PanelsRenderHook::SIMPLE_LAYOUT_START,
            fn (): string => '<a href="'.url('/').'" class="ff-login-mark-link">'
                .'<img src="'.asset('images/logo/flowflex-icon.svg').'" alt="FlowFlex" class="ff-login-mark" />'
                .'</a>',
        );

        // Sidebar brand: Filament keeps the logo in the topbar when a topbar
        // exists — the design wants it at the head of the ink sidebar.
        FilamentView::registerRenderHook(
            PanelsRenderHook::SIDEBAR_LOGO_BEFORE,
            fn (): string => '<a href="'.e(Filament::getUrl()).'" class="ff-side-brand">'
                .'<img src="'.asset('images/logo/flowflex-logo-light.svg').'" alt="FlowFlex" />'
                .'</a>',
        );

        // Sidebar footer: "Your panels" switcher chips + user card (design §12).
        FilamentView::registerRenderHook(
            PanelsRenderHook::SIDEBAR_FOOTER,
            fn (): string => SidebarFooter::render(),
        );

        // Topbar: 320px search trigger with ⌘K hint — opens the Spotlight.
        FilamentView::registerRenderHook(
            PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
            fn (): string => Filament::auth()->check()
                ? '<button type="button" class="ff-topbar-search" x-data
                        x-on:click="window.dispatchEvent(new CustomEvent(\'ff-spotlight-open\'))">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="7" cy="7" r="4.5"></circle><path d="M10.5 10.5L14 14"></path></svg>
                        <span class="ph">Search this panel…</span>
                        <span class="kbd">⌘K</span>
                    </button>'
                : '',
        );

        // Spotlight: panel-scoped quick search on ⌘K / Ctrl+K (all panels,
        // authenticated pages only — login pages have no panel user).
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn (): string => Filament::auth()->check()
                ? Blade::render("@livewire('spotlight', ['panelId' => \$panelId])", [
                    'panelId' => Filament::getCurrentPanel()?->getId(),
                ])
                : '',
        );

        // Panel auth pages: footer strip matching the public Vue login (below the card).
        FilamentView::registerRenderHook(
            PanelsRenderHook::SIMPLE_LAYOUT_END,
            fn (): string => '<div class="ff-login-footer">'
                .'<a href="'.url('/').'">flowflex.eu</a><span aria-hidden="true">·</span>'
                .'<a href="'.url('/privacy').'">Privacy</a><span aria-hidden="true">·</span>'
                .'<a href="'.url('/terms').'">Terms</a>'
                .'</div>',
        );

        // Health checks (core.health). Infra checks skipped in the test env —
        // sqlite/array drivers have no Redis or Horizon to probe.
        $checks = [DatabaseCheck::new()];

        if (! $this->app->environment('testing')) {
            $checks = [
                ...$checks,
                RedisCheck::new(),
                HorizonCheck::new(),
                UsedDiskSpaceCheck::new()->warnWhenUsedSpaceIsAbovePercentage(70),
                QueueCheck::new()->onQueue(['domain-events', 'notifications']),
            ];
        }

        if ($this->app->isProduction()) {
            $checks[] = EnvironmentCheck::new()->expectEnvironment('production');
        }

        Health::checks($checks);
    }
}
