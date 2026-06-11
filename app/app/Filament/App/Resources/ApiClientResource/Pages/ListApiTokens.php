<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\ApiClientResource\Pages;

use App\Filament\App\Resources\ApiClientResource;
use Filament\Resources\Pages\ListRecords;

class ListApiTokens extends ListRecords
{
    protected static string $resource = ApiClientResource::class;
}
