<?php

declare(strict_types=1);

namespace App\Providers\Core;

use App\Contracts\Core\BillingServiceInterface;
use App\Listeners\Core\WebhookDispatcher;
use App\Services\Core\BillingService;
use App\Support\Import\ImporterRegistry;
use App\Support\Privacy\PersonalDataRegistry;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BillingServiceInterface::class, BillingService::class);
        $this->app->singleton(ImporterRegistry::class);
        $this->app->singleton(PersonalDataRegistry::class);
    }

    public function boot(): void
    {
        // Outbound webhooks: every event-bus entry fans out via the dispatcher.
        foreach (config('flowflex.webhook_events', []) as $event) {
            Event::listen($event, WebhookDispatcher::class);
        }
    }
}
