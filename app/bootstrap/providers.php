<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\WorkspacePanelProvider;
use App\Providers\Foundation\FoundationServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    WorkspacePanelProvider::class,
    FoundationServiceProvider::class,
];
