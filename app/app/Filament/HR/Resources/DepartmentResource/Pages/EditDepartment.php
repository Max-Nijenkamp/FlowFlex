<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\DepartmentResource\Pages;

use App\Filament\HR\Resources\DepartmentResource;
use Filament\Resources\Pages\EditRecord;

class EditDepartment extends EditRecord
{
    protected static string $resource = DepartmentResource::class;
}
