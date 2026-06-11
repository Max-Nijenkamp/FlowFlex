<?php

declare(strict_types=1);

namespace App\Actions\Core;

use App\Exceptions\Core\CannotDeleteBuiltInRoleException;
use Lorisleiva\Actions\Concerns\AsAction;
use RuntimeException;
use Spatie\Permission\Models\Role;

class DeleteRoleAction
{
    use AsAction;

    /** @var list<string> */
    private const array BUILT_IN = ['owner', 'admin', 'manager', 'employee'];

    public function handle(string $roleId): void
    {
        $role = Role::query()->findOrFail($roleId);

        if (in_array($role->name, self::BUILT_IN, true)) {
            throw new CannotDeleteBuiltInRoleException($role->name);
        }

        if ($role->users()->exists()) {
            throw new RuntimeException('Role still has users assigned — reassign them first.');
        }

        $role->delete();
    }
}
