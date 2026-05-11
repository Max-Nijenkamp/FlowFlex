<?php

declare(strict_types=1);

namespace App\Filament\Projects\Resources\SprintResource\Pages;

use App\Filament\Projects\Resources\SprintResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSprint extends CreateRecord
{
    protected static string $resource = SprintResource::class;
}
