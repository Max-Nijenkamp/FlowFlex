<?php

declare(strict_types=1);

namespace App\Actions;

use App\Exceptions\CannotDeleteBuiltInRoleException;
use App\Support\Services\BuiltInRoles;
use App\Support\Services\CompanyContext;
use InvalidArgumentException;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Permission\Models\Role;

class DeleteRoleAction
{
    use AsAction;

    public function handle(string $roleId): void
    {
        setPermissionsTeamId(app(CompanyContext::class)->current()->id);

        /** @var Role $role */
        $role = Role::query()->findOrFail($roleId);

        if (BuiltInRoles::isBuiltIn($role->name)) {
            throw CannotDeleteBuiltInRoleException::make($role->name);
        }

        if ($role->users()->exists()) {
            throw new InvalidArgumentException(
                "The role [{$role->name}] still has users assigned — reassign them first.",
            );
        }

        $role->delete();
    }
}
