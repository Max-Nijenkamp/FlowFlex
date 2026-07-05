<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\InvoiceResource\Pages;

use App\Contracts\Finance\InvoiceServiceInterface;
use App\Filament\Finance\Resources\InvoiceResource;
use App\Models\Finance\Customer;
use App\Models\Finance\Invoice;
use App\Services\Finance\InvoiceService;
use App\Support\Services\CompanyContext;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    /** @param  array<string, mixed>  $data @return array<string, mixed> */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = app(CompanyContext::class)->currentId();

        if (($data['due_date'] ?? null) === null) {
            $terms = Customer::query()->find($data['customer_id'])->payment_terms_days ?? 14;
            $data['due_date'] = now()->parse($data['issue_date'] ?? now())->addDays((int) $terms)->toDateString();
        }

        if (($data['recurring_schedule'] ?? null) !== null) {
            $data['next_recurring_at'] = InvoiceService::nextRecurringDate(
                now()->parse($data['issue_date'] ?? now())->toImmutable(),
                $data['recurring_schedule'],
            );
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var Invoice $record */
        $record = $this->record;

        /** @var InvoiceService $service */
        $service = app(InvoiceServiceInterface::class);
        $service->recalculateTotals($record);
    }
}
