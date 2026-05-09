<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ModuleCatalogResource\Pages;

use App\Filament\Admin\Resources\ModuleCatalogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateModuleCatalog extends CreateRecord
{
    protected static string $resource = ModuleCatalogResource::class;
}
