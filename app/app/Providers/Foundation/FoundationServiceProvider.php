<?php

declare(strict_types=1);

namespace App\Providers\Foundation;

use App\Contracts\Foundation\CompanyServiceInterface;
use App\Services\Foundation\CompanyService;
use Illuminate\Support\ServiceProvider;

class FoundationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            CompanyServiceInterface::class,
            CompanyService::class,
        );
    }

    public function boot(): void
    {
        //
    }
}
