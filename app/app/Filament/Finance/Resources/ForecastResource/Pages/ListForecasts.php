<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\ForecastResource\Pages;

use App\Filament\Finance\Resources\ForecastResource;
use Filament\Resources\Pages\ListRecords;

class ListForecasts extends ListRecords
{
    protected static string $resource = ForecastResource::class;
}
