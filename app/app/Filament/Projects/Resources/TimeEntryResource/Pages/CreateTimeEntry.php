<?php

declare(strict_types=1);

namespace App\Filament\Projects\Resources\TimeEntryResource\Pages;

use App\Filament\Projects\Resources\TimeEntryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTimeEntry extends CreateRecord
{
    protected static string $resource = TimeEntryResource::class;
}
