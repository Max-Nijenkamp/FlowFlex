<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\BankAccountResource\Pages;

use App\Filament\Finance\Resources\BankAccountResource;
use App\Support\Services\CompanyContext;
use Filament\Resources\Pages\CreateRecord;

class CreateBankAccount extends CreateRecord
{
    protected static string $resource = BankAccountResource::class;

    /** @param  array<string, mixed>  $data @return array<string, mixed> */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = app(CompanyContext::class)->currentId();
        $data['iban_last4'] = isset($data['iban']) && is_string($data['iban']) && strlen($data['iban']) >= 4
            ? substr($data['iban'], -4)
            : null;

        return $data;
    }
}
