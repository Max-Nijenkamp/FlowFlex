<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\CreateRoleData;
use App\Services\BillingService;
use App\Support\Services\CompanyContext;
use InvalidArgumentException;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Permission\Models\Role;

class CreateRoleAction
{
    use AsAction;

    public function handle(CreateRoleData $data): Role
    {
        $company = app(CompanyContext::class)->current();
        setPermissionsTeamId($company->id);

        self::assertPermissionsBelongToActiveModules($data->permissions);

        $role = Role::query()->create([
            'name' => $data->name,
            'guard_name' => 'web',
            'company_id' => $company->id,
        ]);

        $role->syncPermissions($data->permissions);

        return $role;
    }

    /**
     * Module-scoped permission bound (core.rbac/module-scoped-permissions):
     * a custom role may only hold permissions of ACTIVE modules. Permission
     * names are `{domain}.{module}.{action}` → module key `{domain}.{module}`.
     * `core.rbac`/`core.settings`-style free-core modules are always active
     * via their subscription rows.
     *
     * @param  list<string>  $permissions
     */
    public static function assertPermissionsBelongToActiveModules(array $permissions): void
    {
        $companyId = app(CompanyContext::class)->currentId();

        if ($companyId === null) {
            throw new InvalidArgumentException('No tenant context.');
        }

        $active = app(BillingService::class)->activeModules($companyId);

        foreach ($permissions as $permission) {
            $moduleKey = str($permission)->beforeLast('.')->toString();

            if (! in_array($moduleKey, $active, true)) {
                throw new InvalidArgumentException(
                    "Permission [{$permission}] belongs to module [{$moduleKey}] which is not active for this company.",
                );
            }
        }
    }
}
