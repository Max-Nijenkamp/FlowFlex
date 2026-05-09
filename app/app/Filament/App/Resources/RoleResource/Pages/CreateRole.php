<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\RoleResource\Pages;

use App\Filament\App\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;
}
