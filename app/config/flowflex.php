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
        'hr.recruitment' => ['name' => 'Recruitment', 'domain' => 'hr', 'per_user_monthly_price_cents' => 200],
        'hr.performance' => ['name' => 'Performance Reviews', 'domain' => 'hr', 'per_user_monthly_price_cents' => 150],
        'hr.time' => ['name' => 'Time & Attendance', 'domain' => 'hr', 'per_user_monthly_price_cents' => 150],
        'hr.shifts' => ['name' => 'Shift Scheduling', 'domain' => 'hr', 'per_user_monthly_price_cents' => 150],
        'hr.compensation' => ['name' => 'Compensation & Benefits', 'domain' => 'hr', 'per_user_monthly_price_cents' => 100],
        'hr.analytics' => ['name' => 'HR Analytics', 'domain' => 'hr', 'per_user_monthly_price_cents' => 100],
        'hr.workforce' => ['name' => 'Workforce Planning', 'domain' => 'hr', 'per_user_monthly_price_cents' => 100],
        'hr.feedback' => ['name' => 'Employee Feedback', 'domain' => 'hr', 'per_user_monthly_price_cents' => 50],
        'hr.dei' => ['name' => 'DEI Metrics', 'domain' => 'hr', 'per_user_monthly_price_cents' => 50],
        // Finance & Accounting
        'finance.ledger' => ['name' => 'General Ledger', 'domain' => 'finance', 'per_user_monthly_price_cents' => 200],
        'finance.invoicing' => ['name' => 'Invoicing', 'domain' => 'finance', 'per_user_monthly_price_cents' => 250],
        'finance.expenses' => ['name' => 'Expenses', 'domain' => 'finance', 'per_user_monthly_price_cents' => 150],
        'finance.bank' => ['name' => 'Bank Accounts', 'domain' => 'finance', 'per_user_monthly_price_cents' => 100],
        'finance.ar' => ['name' => 'Accounts Receivable', 'domain' => 'finance', 'per_user_monthly_price_cents' => 100], // *(assumed)*
        'finance.ap' => ['name' => 'Accounts Payable', 'domain' => 'finance', 'per_user_monthly_price_cents' => 100], // *(assumed)*
        'finance.budgets' => ['name' => 'Budgets', 'domain' => 'finance', 'per_user_monthly_price_cents' => 100], // *(assumed)*
        'finance.reporting' => ['name' => 'Financial Reporting', 'domain' => 'finance', 'per_user_monthly_price_cents' => 150], // *(assumed)*
        'finance.tax' => ['name' => 'Tax Management', 'domain' => 'finance', 'per_user_monthly_price_cents' => 100], // *(assumed)*
        'finance.cashflow' => ['name' => 'Cash Flow', 'domain' => 'finance', 'per_user_monthly_price_cents' => 100], // *(assumed)*
        'finance.assets' => ['name' => 'Fixed Assets', 'domain' => 'finance', 'per_user_monthly_price_cents' => 100], // *(assumed)*
        'finance.forecasting' => ['name' => 'Forecasting', 'domain' => 'finance', 'per_user_monthly_price_cents' => 100], // *(assumed)*
        'finance.currency' => ['name' => 'Multi-Currency', 'domain' => 'finance', 'per_user_monthly_price_cents' => 50], // *(assumed)*
        // CRM & Sales
        'crm.contacts' => ['name' => 'Contacts', 'domain' => 'crm', 'per_user_monthly_price_cents' => 200],
        'crm.deals' => ['name' => 'Deals', 'domain' => 'crm', 'per_user_monthly_price_cents' => 250],
        'crm.pipeline' => ['name' => 'Pipeline Board', 'domain' => 'crm', 'per_user_monthly_price_cents' => 100],
        'crm.activities' => ['name' => 'Activities', 'domain' => 'crm', 'per_user_monthly_price_cents' => 50],
        'crm.quotes' => ['name' => 'Quotes', 'domain' => 'crm', 'per_user_monthly_price_cents' => 150],
        'crm.forecasting' => ['name' => 'Sales Forecasting', 'domain' => 'crm', 'per_user_monthly_price_cents' => 100], // *(assumed)*
        'crm.segments' => ['name' => 'Customer Segments', 'domain' => 'crm', 'per_user_monthly_price_cents' => 50], // *(assumed)*
        'crm.scheduling' => ['name' => 'Appointment Scheduling', 'domain' => 'crm', 'per_user_monthly_price_cents' => 100], // *(assumed)*
        'crm.deal-rooms' => ['name' => 'Deal Rooms', 'domain' => 'crm', 'per_user_monthly_price_cents' => 150], // *(assumed)*
        'crm.contracts' => ['name' => 'Contracts', 'domain' => 'crm', 'per_user_monthly_price_cents' => 100], // *(assumed)*
        'crm.email' => ['name' => 'Email Integration', 'domain' => 'crm', 'per_user_monthly_price_cents' => 150], // *(assumed)*
        'crm.sequences' => ['name' => 'Sales Sequences', 'domain' => 'crm', 'per_user_monthly_price_cents' => 150], // *(assumed)*
        'crm.pricing' => ['name' => 'Price Management', 'domain' => 'crm', 'per_user_monthly_price_cents' => 100], // *(assumed)*
        'crm.referrals' => ['name' => 'Referral Program', 'domain' => 'crm', 'per_user_monthly_price_cents' => 100], // *(assumed)*
        'crm.revenue-intelligence' => ['name' => 'Revenue Intelligence', 'domain' => 'crm', 'per_user_monthly_price_cents' => 200], // *(assumed)*
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
