<?php

namespace App\Filament\Hr\Resources\SalaryRecordResource\Pages;

use App\Filament\Hr\Resources\SalaryRecordResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSalaryRecords extends ListRecords
{
    protected static string $resource = SalaryRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
