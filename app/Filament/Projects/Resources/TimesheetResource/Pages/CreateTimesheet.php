<?php

namespace App\Filament\Projects\Resources\TimesheetResource\Pages;

use App\Filament\Projects\Resources\TimesheetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTimesheet extends CreateRecord
{
    protected static string $resource = TimesheetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = auth('tenant')->id();

        return $data;
    }
}
