<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\InvoiceResource\Pages;

use App\Contracts\Finance\InvoiceServiceInterface;
use App\Filament\Finance\Resources\InvoiceResource;
use App\Models\Finance\Invoice;
use App\Services\Finance\InvoiceService;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function afterSave(): void
    {
        /** @var Invoice $record */
        $record = $this->record;

        /** @var InvoiceService $service */
        $service = app(InvoiceServiceInterface::class);
        $service->recalculateTotals($record);
    }
}
