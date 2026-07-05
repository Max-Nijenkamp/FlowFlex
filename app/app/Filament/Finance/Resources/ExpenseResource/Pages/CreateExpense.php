<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\ExpenseResource\Pages;

use App\Filament\Finance\Resources\ExpenseResource;
use App\Support\Services\CompanyContext;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;

    /** @param  array<string, mixed>  $data @return array<string, mixed> */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = app(CompanyContext::class)->currentId();
        $data['user_id'] = Auth::id();

        return $data;
    }
}
