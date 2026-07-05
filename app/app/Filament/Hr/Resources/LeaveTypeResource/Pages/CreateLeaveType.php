<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources\LeaveTypeResource\Pages;

use App\Filament\Hr\Resources\LeaveTypeResource;
use App\Support\Services\CompanyContext;
use Filament\Resources\Pages\CreateRecord;

class CreateLeaveType extends CreateRecord
{
    protected static string $resource = LeaveTypeResource::class;

    /** @param  array<string, mixed>  $data @return array<string, mixed> */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = app(CompanyContext::class)->currentId();

        return $data;
    }
}
