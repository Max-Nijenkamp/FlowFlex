<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources\DepartmentResource\Pages;

use App\Filament\Hr\Resources\DepartmentResource;
use App\Support\Services\CompanyContext;
use Filament\Resources\Pages\CreateRecord;

class CreateDepartment extends CreateRecord
{
    protected static string $resource = DepartmentResource::class;

    /** @param  array<string, mixed>  $data @return array<string, mixed> */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = app(CompanyContext::class)->currentId();

        return $data;
    }
}
