<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Admin;
use App\Support\Services\CompanyContext;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Horizon\Horizon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CompanyContext::class);
    }

    public function boot(): void
    {
        $this->gateMonitoringTools();
    }

    private function gateMonitoringTools(): void
    {
        // Horizon uses its own auth callback — check the admin guard specifically
        Horizon::auth(function ($request) {
            return auth('admin')->check();
        });

        // Pulse and Telescope use Laravel Gates — same admin guard check
        Gate::define('viewPulse', function ($user = null) {
            return auth('admin')->check();
        });

        Gate::define('viewTelescope', function ($user = null) {
            return auth('admin')->check();
        });
    }
}
