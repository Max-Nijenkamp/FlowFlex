<?php

namespace App\Filament\Hr\Resources\PayslipResource\Pages;

use App\Filament\Hr\Resources\PayslipResource;
use Filament\Resources\Pages\ListRecords;

class ListPayslips extends ListRecords
{
    protected static string $resource = PayslipResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
