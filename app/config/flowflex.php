<?php

declare(strict_types=1);
use App\Events\Core\CompanySubscriptionSuspended;
use App\Events\Core\ModuleActivated;
use App\Notifications\Core\ModuleActivatedNotification;
use App\Notifications\Core\SubscriptionSuspendedNotification;

return [

    /*
    |--------------------------------------------------------------------------
    | Module catalog — paid domain modules
    |--------------------------------------------------------------------------
    | The free Core Platform set is defined on ModuleCatalog::FREE_CORE.
    | Paid domain modules register here as their domains ship:
    |
    | 'hr.payroll' => [
    |     'name' => 'Payroll',
    |     'domain' => 'hr',
    |     'per_user_monthly_price_cents' => 250,
    |     'is_active' => true,
    | ],
    */
    'modules' => [],

    /*
    |--------------------------------------------------------------------------
    | Webhook events (the event-bus map)
    |--------------------------------------------------------------------------
    | Cross-domain events subscribable by outbound webhooks. Domains append
    | their events as they ship. WebhookDispatcher is registered as a listener
    | for every entry (CoreServiceProvider).
    */
    'webhook_events' => [
        ModuleActivated::class,
        CompanySubscriptionSuspended::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification types
    |--------------------------------------------------------------------------
    | Registry of preference-managed notification types. Domains append their
    | own entries as they ship. Key = type key (class), value = display label.
    */
    'notification_types' => [
        ModuleActivatedNotification::class => 'Module activated',
        SubscriptionSuspendedNotification::class => 'Subscription suspended',
    ],

    /*
    |--------------------------------------------------------------------------
    | Dunning
    |--------------------------------------------------------------------------
    | Payment retry schedule in days after an invoice goes past_due.
    | After the final retry fails, the company is suspended.
    */
    'dunning_retry_days' => [3, 7, 14],

];
