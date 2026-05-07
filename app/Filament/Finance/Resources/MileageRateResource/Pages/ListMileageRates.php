<?php

namespace App\Filament\Finance\Resources\MileageRateResource\Pages;

use App\Filament\Finance\Resources\MileageRateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMileageRates extends ListRecords
{
    protected static string $resource = MileageRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
