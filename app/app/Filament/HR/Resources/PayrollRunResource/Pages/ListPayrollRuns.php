<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\PayrollRunResource\Pages;

use App\Filament\HR\Resources\PayrollRunResource;
use Filament\Resources\Pages\ListRecords;

class ListPayrollRuns extends ListRecords
{
    protected static string $resource = PayrollRunResource::class;
}
