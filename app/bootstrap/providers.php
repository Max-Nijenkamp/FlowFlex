<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\BillingServiceProvider;
use App\Providers\Crm\CrmServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\AppPanelProvider;
use App\Providers\Filament\CrmPanelProvider;
use App\Providers\Filament\FinancePanelProvider;
use App\Providers\Filament\HrPanelProvider;
use App\Providers\HorizonServiceProvider;

return [
    AppServiceProvider::class,
    BillingServiceProvider::class,
    CrmServiceProvider::class,
    AdminPanelProvider::class,
    AppPanelProvider::class,
    CrmPanelProvider::class,
    FinancePanelProvider::class,
    HrPanelProvider::class,
    HorizonServiceProvider::class,
];
