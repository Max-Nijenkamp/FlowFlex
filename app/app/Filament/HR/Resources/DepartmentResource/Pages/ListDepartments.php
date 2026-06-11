<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\DepartmentResource\Pages;

use App\Filament\HR\Resources\DepartmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDepartments extends ListRecords
{
    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
