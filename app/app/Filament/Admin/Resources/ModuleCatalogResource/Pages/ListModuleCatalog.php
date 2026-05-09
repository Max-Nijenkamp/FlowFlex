<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ModuleCatalogResource\Pages;

use App\Filament\Admin\Resources\ModuleCatalogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListModuleCatalog extends ListRecords
{
    protected static string $resource = ModuleCatalogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
