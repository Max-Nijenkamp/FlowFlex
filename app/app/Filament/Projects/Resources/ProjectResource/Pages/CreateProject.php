<?php

declare(strict_types=1);

namespace App\Filament\Projects\Resources\ProjectResource\Pages;

use App\Filament\Projects\Resources\ProjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;
}
