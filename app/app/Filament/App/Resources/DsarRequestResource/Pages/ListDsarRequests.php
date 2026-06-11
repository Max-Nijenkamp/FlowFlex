<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\DsarRequestResource\Pages;

use App\Filament\App\Resources\DsarRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListDsarRequests extends ListRecords
{
    protected static string $resource = DsarRequestResource::class;
}
