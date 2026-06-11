<?php

declare(strict_types=1);

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
    | Dunning
    |--------------------------------------------------------------------------
    | Payment retry schedule in days after an invoice goes past_due.
    | After the final retry fails, the company is suspended.
    */
    'dunning_retry_days' => [3, 7, 14],

];
