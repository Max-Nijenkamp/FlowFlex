<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\FixedAssetResource\Pages;

use App\Filament\Finance\Resources\FixedAssetResource;
use Filament\Resources\Pages\ListRecords;

class ListFixedAssets extends ListRecords
{
    protected static string $resource = FixedAssetResource::class;
}
