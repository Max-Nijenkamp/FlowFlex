<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Models\Company;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * The four built-in roles every company carries (core.rbac/security):
 * owner holds every permission; admin everything except ownership transfer;
 * manager/employee start empty *(assumed)* and are shaped per company.
 */
class BuiltInRoles
{
    public const NAMES = ['owner', 'admin', 'manager', 'employee'];

    public static function isBuiltIn(string $name): bool
    {
        return in_array($name, self::NAMES, true);
    }

    /** Idempotent — safe on provisioning and re-seeds. */
    public static function ensure(Company $company): void
    {
        setPermissionsTeamId($company->id);

        $all = Permission::query()->where('guard_name', 'web')->get();

        $owner = Role::query()->firstOrCreate(
            ['name' => 'owner', 'guard_name' => 'web', 'company_id' => $company->id],
        );
        $owner->syncPermissions($all);

        $admin = Role::query()->firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web', 'company_id' => $company->id],
        );
        $admin->syncPermissions($all->reject(
            fn (Permission $permission): bool => $permission->name === 'core.rbac.transfer-ownership',
        ));

        foreach (['manager', 'employee'] as $name) {
            Role::query()->firstOrCreate(
                ['name' => $name, 'guard_name' => 'web', 'company_id' => $company->id],
            );
        }
    }
}
