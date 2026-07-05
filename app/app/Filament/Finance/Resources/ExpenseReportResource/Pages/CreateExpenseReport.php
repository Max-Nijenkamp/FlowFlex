<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\ExpenseReportResource\Pages;

use App\Filament\Finance\Resources\ExpenseReportResource;
use App\Support\Services\CompanyContext;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateExpenseReport extends CreateRecord
{
    protected static string $resource = ExpenseReportResource::class;

    /** @param  array<string, mixed>  $data @return array<string, mixed> */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = app(CompanyContext::class)->currentId();
        $data['user_id'] = Auth::id();

        return $data;
    }
}
