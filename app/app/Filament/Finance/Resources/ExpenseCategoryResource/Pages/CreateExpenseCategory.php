<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\ExpenseCategoryResource\Pages;

use App\Filament\Finance\Resources\ExpenseCategoryResource;
use App\Support\Services\CompanyContext;
use Filament\Resources\Pages\CreateRecord;

class CreateExpenseCategory extends CreateRecord
{
    protected static string $resource = ExpenseCategoryResource::class;

    /** @param  array<string, mixed>  $data @return array<string, mixed> */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = app(CompanyContext::class)->currentId();

        return $data;
    }
}
