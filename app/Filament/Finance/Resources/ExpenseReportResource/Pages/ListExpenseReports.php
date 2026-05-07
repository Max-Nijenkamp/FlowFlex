<?php

namespace App\Filament\Finance\Resources\ExpenseReportResource\Pages;

use App\Filament\Finance\Resources\ExpenseReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExpenseReports extends ListRecords
{
    protected static string $resource = ExpenseReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
