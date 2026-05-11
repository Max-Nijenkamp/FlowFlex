<?php

use App\Providers\AppServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\WorkspacePanelProvider;
use App\Providers\Foundation\FoundationServiceProvider;

return [
    AppServiceProvider::class,
    EventServiceProvider::class,
    AdminPanelProvider::class,
    WorkspacePanelProvider::class,
    \App\Providers\Filament\HrPanelProvider::class,
    \App\Providers\Filament\ProjectsPanelProvider::class,
    FoundationServiceProvider::class,
    \App\Providers\HR\HrServiceProvider::class,
    \App\Providers\Projects\ProjectsServiceProvider::class,
];
