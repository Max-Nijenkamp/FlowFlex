<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\Services\CompanyContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

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
    }
}
