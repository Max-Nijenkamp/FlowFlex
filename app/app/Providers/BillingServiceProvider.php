<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\BillingServiceInterface;
use App\Services\BillingService;
use Illuminate\Support\ServiceProvider;

class BillingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BillingServiceInterface::class, BillingService::class);
        $this->app->singleton(BillingService::class);
    }
}
