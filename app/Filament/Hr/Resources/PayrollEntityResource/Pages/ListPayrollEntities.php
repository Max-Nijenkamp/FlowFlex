<?php

namespace App\Filament\Hr\Resources\PayrollEntityResource\Pages;

use App\Filament\Hr\Resources\PayrollEntityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPayrollEntities extends ListRecords
{
    protected static string $resource = PayrollEntityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
