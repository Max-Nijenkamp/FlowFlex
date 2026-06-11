<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources\DealResource\Pages;

use App\Filament\CRM\Resources\DealResource;
use Filament\Resources\Pages\EditRecord;

class EditDeal extends EditRecord
{
    protected static string $resource = DealResource::class;
}
