<?php

namespace App\Filament\Crm\Resources\CrmCompanyResource\Pages;

use App\Filament\Crm\Resources\CrmCompanyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCrmCompanies extends ListRecords
{
    protected static string $resource = CrmCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
