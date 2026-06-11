<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\DepartmentResource\Pages;

use App\Filament\HR\Resources\DepartmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDepartment extends CreateRecord
{
    protected static string $resource = DepartmentResource::class;
}
