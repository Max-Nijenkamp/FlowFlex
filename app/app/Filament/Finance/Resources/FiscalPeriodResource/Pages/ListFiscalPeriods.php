<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\FiscalPeriodResource\Pages;

use App\Filament\Finance\Resources\FiscalPeriodResource;
use Filament\Resources\Pages\ListRecords;

class ListFiscalPeriods extends ListRecords
{
    protected static string $resource = FiscalPeriodResource::class;
}
