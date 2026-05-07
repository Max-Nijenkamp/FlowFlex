<?php

namespace App\Filament\Hr\Resources\SalaryRecordResource\Pages;

use App\Filament\Hr\Resources\SalaryRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSalaryRecord extends EditRecord
{
    protected static string $resource = SalaryRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
