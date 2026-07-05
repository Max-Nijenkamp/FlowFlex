<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\ChartOfAccountsResource\Pages;

use App\Filament\Finance\Resources\ChartOfAccountsResource;
use App\Support\Services\CompanyContext;
use Filament\Resources\Pages\CreateRecord;

class CreateAccount extends CreateRecord
{
    protected static string $resource = ChartOfAccountsResource::class;

    /** @param  array<string, mixed>  $data @return array<string, mixed> */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = app(CompanyContext::class)->currentId();

        return $data;
    }
}
