<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\DataImportResource\Pages;

use App\Filament\App\Resources\DataImportResource;
use Filament\Resources\Pages\ListRecords;

class ListDataImports extends ListRecords
{
    protected static string $resource = DataImportResource::class;
}
