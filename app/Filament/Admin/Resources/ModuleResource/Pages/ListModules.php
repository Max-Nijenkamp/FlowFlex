<?php

namespace App\Filament\Admin\Resources\ModuleResource\Pages;

use App\Filament\Admin\Resources\ModuleResource;
use Filament\Resources\Pages\ListRecords;

class ListModules extends ListRecords
{
    protected static string $resource = ModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
