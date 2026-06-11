<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\BillingServiceInterface;
use App\Listeners\WebhookDispatcher;
use App\Services\BillingService;
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
