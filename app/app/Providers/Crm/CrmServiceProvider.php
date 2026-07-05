<?php

declare(strict_types=1);

namespace App\Providers\Crm;

use App\Contracts\Crm\ContactServiceInterface;
use App\Contracts\Crm\DealServiceInterface;
use App\Services\Crm\ContactService;
use App\Services\Crm\DealService;
use Illuminate\Support\ServiceProvider;

class CrmServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ContactServiceInterface::class, ContactService::class);
        $this->app->bind(DealServiceInterface::class, DealService::class);
    }
}
