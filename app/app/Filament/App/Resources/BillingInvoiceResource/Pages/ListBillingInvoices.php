<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\BillingInvoiceResource\Pages;

use App\Filament\App\Resources\BillingInvoiceResource;
use Filament\Resources\Pages\ListRecords;

class ListBillingInvoices extends ListRecords
{
    protected static string $resource = BillingInvoiceResource::class;
}
