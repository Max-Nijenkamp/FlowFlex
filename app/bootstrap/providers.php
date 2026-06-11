<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\CoreServiceProvider;
use App\Providers\CrmServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\AppPanelProvider;
use App\Providers\Filament\CrmPanelProvider;
use App\Providers\Filament\FinancePanelProvider;
use App\Providers\Filament\HrPanelProvider;
use App\Providers\FinanceServiceProvider;
use App\Providers\HorizonServiceProvider;
use App\Providers\HRServiceProvider;

return [
    AppServiceProvider::class,
    CoreServiceProvider::class,
    AdminPanelProvider::class,
    AppPanelProvider::class,
    HrPanelProvider::class,
    FinancePanelProvider::class,
    CrmPanelProvider::class,
    HorizonServiceProvider::class,
    HRServiceProvider::class,
    FinanceServiceProvider::class,
    CrmServiceProvider::class,
];
