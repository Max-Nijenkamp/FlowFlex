<?php

declare(strict_types=1);

namespace App\Listeners\Foundation;

use App\Events\Foundation\CompanyCreated;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SyncOwnerPermissionsListener
{
    public function handle(CompanyCreated $event): void
    {
        $ownerRole = Role::where('name', 'owner')
            ->where('guard_name', 'web')
            ->where('team_id', $event->company->id)
            ->first();

        if ($ownerRole) {
            $ownerRole->syncPermissions(Permission::all());
        }
    }
}
