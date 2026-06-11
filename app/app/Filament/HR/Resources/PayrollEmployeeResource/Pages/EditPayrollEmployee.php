<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\PayrollEmployeeResource\Pages;

use App\Filament\HR\Resources\PayrollEmployeeResource;
use Filament\Resources\Pages\EditRecord;

class EditPayrollEmployee extends EditRecord
{
    protected static string $resource = PayrollEmployeeResource::class;
}
