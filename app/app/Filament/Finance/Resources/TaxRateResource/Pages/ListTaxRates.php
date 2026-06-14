<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\TaxRateResource\Pages;

use App\Filament\Finance\Resources\TaxRateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTaxRates extends ListRecords
{
    protected static string $resource = TaxRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
