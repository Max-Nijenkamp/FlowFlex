<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\RoleResource\Pages;

use App\Actions\CreateRoleAction;
use App\Data\CreateRoleData;
use App\Filament\App\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return CreateRoleAction::run(new CreateRoleData(
            name: $data['name'],
            permissions: RoleResource::flattenMatrix($data['matrix'] ?? []),
        ));
    }
}
