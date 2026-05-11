<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources\PayrollRunResource\Pages;

use App\Filament\Hr\Resources\PayrollRunResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPayrollRuns extends ListRecords
{
    protected static string $resource = PayrollRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
