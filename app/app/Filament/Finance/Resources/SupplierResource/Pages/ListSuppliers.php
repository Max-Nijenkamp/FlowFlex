<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\SupplierResource\Pages;

use App\Filament\Finance\Resources\SupplierResource;
use Filament\Resources\Pages\ListRecords;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;
}
