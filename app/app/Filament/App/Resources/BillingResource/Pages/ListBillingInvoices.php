<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\BillingResource\Pages;

use App\Filament\App\Resources\BillingResource;
use Filament\Resources\Pages\ListRecords;

class ListBillingInvoices extends ListRecords
{
    protected static string $resource = BillingResource::class;
}
