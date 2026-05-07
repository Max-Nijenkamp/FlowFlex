<?php

namespace App\Filament\Crm\Resources\CrmContactResource\Pages;

use App\Filament\Crm\Resources\CrmContactResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCrmContact extends EditRecord
{
    protected static string $resource = CrmContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
