<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\ShiftResource\Pages;

use App\Filament\HR\Resources\ShiftResource;
use Filament\Resources\Pages\ListRecords;

class ListShifts extends ListRecords
{
    protected static string $resource = ShiftResource::class;
}
