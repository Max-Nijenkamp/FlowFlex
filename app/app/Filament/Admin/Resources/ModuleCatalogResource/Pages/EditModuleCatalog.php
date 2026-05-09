<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ModuleCatalogResource\Pages;

use App\Filament\Admin\Resources\ModuleCatalogResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditModuleCatalog extends EditRecord
{
    protected static string $resource = ModuleCatalogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
