<?php

namespace App\Filament\Finance\Resources\ExpenseReportResource\Pages;

use App\Filament\Finance\Resources\ExpenseReportResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExpenseReport extends EditRecord
{
    protected static string $resource = ExpenseReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
