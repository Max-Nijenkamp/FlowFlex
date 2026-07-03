<?php

declare(strict_types=1);

namespace App\Providers;

use App\Filament\Auth\PanelScopedLoginResponse;
use App\Support\Services\CompanyContext;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CompanyContext::class);

        // Cross-panel intended-URL bleed fix — see PanelScopedLoginResponse.
        $this->app->bind(LoginResponse::class, PanelScopedLoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
