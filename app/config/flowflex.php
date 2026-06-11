<?php

declare(strict_types=1);
use App\Events\CompanySubscriptionSuspended;
use App\Events\ModuleActivated;
use App\Notifications\ModuleActivatedNotification;
use App\Notifications\SubscriptionSuspendedNotification;

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
    'modules' => [
        // HR & People *(prices assumed — pricing-model ADR at launch)*
        'hr.profiles' => ['name' => 'Employee Profiles', 'domain' => 'hr', 'per_user_monthly_price_cents' => 200],
        'hr.org' => ['name' => 'Org Chart', 'domain' => 'hr', 'per_user_monthly_price_cents' => 50],
        'hr.self-service' => ['name' => 'Employee Self-Service', 'domain' => 'hr', 'per_user_monthly_price_cents' => 100],
        'hr.leave' => ['name' => 'Leave Management', 'domain' => 'hr', 'per_user_monthly_price_cents' => 150],
        'hr.onboarding' => ['name' => 'Onboarding', 'domain' => 'hr', 'per_user_monthly_price_cents' => 100],
        'hr.payroll' => ['name' => 'Payroll', 'domain' => 'hr', 'per_user_monthly_price_cents' => 300],
        // Finance & Accounting
        'finance.ledger' => ['name' => 'General Ledger', 'domain' => 'finance', 'per_user_monthly_price_cents' => 200],
        'finance.invoicing' => ['name' => 'Invoicing', 'domain' => 'finance', 'per_user_monthly_price_cents' => 250],
        'finance.expenses' => ['name' => 'Expenses', 'domain' => 'finance', 'per_user_monthly_price_cents' => 150],
        'finance.bank' => ['name' => 'Bank Accounts', 'domain' => 'finance', 'per_user_monthly_price_cents' => 100],
        // CRM & Sales
        'crm.contacts' => ['name' => 'Contacts', 'domain' => 'crm', 'per_user_monthly_price_cents' => 200],
        'crm.deals' => ['name' => 'Deals', 'domain' => 'crm', 'per_user_monthly_price_cents' => 250],
        'crm.pipeline' => ['name' => 'Pipeline Board', 'domain' => 'crm', 'per_user_monthly_price_cents' => 100],
        'crm.activities' => ['name' => 'Activities', 'domain' => 'crm', 'per_user_monthly_price_cents' => 50],
        'crm.quotes' => ['name' => 'Quotes', 'domain' => 'crm', 'per_user_monthly_price_cents' => 150],
    ],

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
