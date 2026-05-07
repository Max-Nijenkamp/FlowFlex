<?php

namespace App\Filament\Crm\Resources\CrmCompanyResource\Pages;

use App\Filament\Crm\Resources\CrmCompanyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCrmCompany extends EditRecord
{
    protected static string $resource = CrmCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
