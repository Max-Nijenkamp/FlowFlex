<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources\PayrollRunResource\Pages;

use App\Filament\Hr\Resources\PayrollRunResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePayrollRun extends CreateRecord
{
    protected static string $resource = PayrollRunResource::class;
}
