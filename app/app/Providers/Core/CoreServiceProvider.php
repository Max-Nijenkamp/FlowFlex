<?php

declare(strict_types=1);

namespace App\Providers\Core;

use App\Contracts\Core\BillingServiceInterface;
use App\Services\Core\BillingService;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BillingServiceInterface::class, BillingService::class);
    }
}
