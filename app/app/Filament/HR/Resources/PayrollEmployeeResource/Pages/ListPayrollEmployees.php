<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\PayrollEmployeeResource\Pages;

use App\Filament\HR\Resources\PayrollEmployeeResource;
use Filament\Resources\Pages\ListRecords;

class ListPayrollEmployees extends ListRecords
{
    protected static string $resource = PayrollEmployeeResource::class;
}
