<?php

namespace App\Filament\Hr\Resources\PayrollEntityResource\Pages;

use App\Filament\Hr\Resources\PayrollEntityResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPayrollEntity extends EditRecord
{
    protected static string $resource = PayrollEntityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
