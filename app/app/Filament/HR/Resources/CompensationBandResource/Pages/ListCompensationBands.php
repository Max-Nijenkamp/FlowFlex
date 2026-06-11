<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\CompensationBandResource\Pages;

use App\Filament\HR\Resources\CompensationBandResource;
use Filament\Resources\Pages\ListRecords;

class ListCompensationBands extends ListRecords
{
    protected static string $resource = CompensationBandResource::class;
}
