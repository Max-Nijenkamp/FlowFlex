<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\AssignRolesData;
use App\Exceptions\CannotRemoveLastOwnerException;
use App\Models\User;
use App\Support\Services\AuditLogger;
use App\Support\Services\CompanyContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Lorisleiva\Actions\Concerns\AsAction;

class AssignRolesAction
{
    use AsAction;

    public function handle(AssignRolesData $data): void
    {
        $company = app(CompanyContext::class)->current();
        setPermissionsTeamId($company->id);

        /** @var User $user */
        $user = User::query()->findOrFail($data->userId);

        if (in_array('owner', $data->roles, true) && ! $user->hasRole('owner')) {
            // Exactly one owner — the role only moves via TransferOwnershipAction.
            throw new InvalidArgumentException('Ownership changes only via transfer, never by assigning the owner role.');
        }

        DB::transaction(function () use ($company, $user, $data): void {
            // Last-owner invariant re-checked under lock: two concurrent
            // demotions of the remaining owners cannot both pass this gate.
            $ownerAssignments = DB::table('model_has_roles')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->where('roles.name', 'owner')
                ->where('roles.company_id', $company->id)
                ->lockForUpdate()
                ->pluck('model_has_roles.model_id');

            $isSoleOwner = $ownerAssignments->count() === 1 && $ownerAssignments->first() === $user->id;

            if ($isSoleOwner && ! in_array('owner', $data->roles, true)) {
                throw CannotRemoveLastOwnerException::make();
            }

            $user->syncRoles($data->roles);

            $causer = Auth::user();
            app(AuditLogger::class)->log(
                'core.roles-assigned',
                $user,
                $causer instanceof User ? $causer : null,
                ['roles' => $data->roles],
            );
        });
    }
}
