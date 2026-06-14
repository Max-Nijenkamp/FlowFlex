<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\ExchangeRateResource\Pages;

use App\Filament\Finance\Resources\ExchangeRateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExchangeRates extends ListRecords
{
    protected static string $resource = ExchangeRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
