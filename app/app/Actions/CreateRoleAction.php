<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\CreateRoleData;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Permission\Models\Role;

class CreateRoleAction
{
    use AsAction;

    public function handle(CreateRoleData $data): Role
    {
        $role = Role::create(['name' => $data->name, 'guard_name' => 'web']);
        $role->syncPermissions($data->permissions);

        return $role;
    }
}
