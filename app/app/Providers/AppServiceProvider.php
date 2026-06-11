<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\Services\CompanyContext;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Catch lazy-loading bugs in local dev only (kept lenient in tests + prod).
        Model::preventLazyLoading($this->app->environment('local'));

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
