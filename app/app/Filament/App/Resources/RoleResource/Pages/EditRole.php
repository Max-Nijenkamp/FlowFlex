<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\RoleResource\Pages;

use App\Actions\CreateRoleAction;
use App\Filament\App\Resources\RoleResource;
use App\Support\Services\BuiltInRoles;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    /** @param array<string, mixed> $data @return array<string, mixed> */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Role $role */
        $role = $this->getRecord();

        $matrix = [];
        foreach ($role->permissions->pluck('name') as $name) {
            $module = str_replace('.', '_', str($name)->beforeLast('.')->toString());
            $matrix[$module][] = $name;
        }

        $data['matrix'] = $matrix;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Role $record */
        $permissions = RoleResource::flattenMatrix($data['matrix'] ?? []);

        CreateRoleAction::assertPermissionsBelongToActiveModules($permissions);

        if (! BuiltInRoles::isBuiltIn($record->name) && isset($data['name'])) {
            $record->update(['name' => $data['name']]);
        }

        $record->syncPermissions($permissions);

        return $record;
    }
}
