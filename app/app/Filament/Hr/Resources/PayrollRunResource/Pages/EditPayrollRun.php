<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources\PayrollRunResource\Pages;

use App\Filament\Hr\Resources\PayrollRunResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPayrollRun extends EditRecord
{
    protected static string $resource = PayrollRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
