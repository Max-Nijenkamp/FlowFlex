<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources\ContractResource\Pages;

use App\Filament\CRM\Resources\ContractResource;
use Filament\Resources\Pages\ListRecords;

class ListContracts extends ListRecords
{
    protected static string $resource = ContractResource::class;
}
