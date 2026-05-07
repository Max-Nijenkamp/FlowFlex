<?php

namespace App\Filament\Projects\Resources\TimeEntryResource\Pages;

use App\Filament\Projects\Resources\TimeEntryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTimeEntry extends CreateRecord
{
    protected static string $resource = TimeEntryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = auth('tenant')->id();

        return $data;
    }
}
