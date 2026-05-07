<?php

namespace App\Filament\Crm\Resources\CrmContactResource\Pages;

use App\Filament\Crm\Resources\CrmContactResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCrmContacts extends ListRecords
{
    protected static string $resource = CrmContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
