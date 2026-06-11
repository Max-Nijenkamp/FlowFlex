<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\InvoiceResource\Pages;

use App\Contracts\Finance\InvoiceServiceInterface;
use App\Data\Finance\CreateInvoiceData;
use App\Filament\Finance\Resources\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    /** Totals computed by InvoiceService (brick/money). */
    protected function handleRecordCreation(array $data): Model
    {
        return app(InvoiceServiceInterface::class)->create(CreateInvoiceData::from($data));
    }
}
