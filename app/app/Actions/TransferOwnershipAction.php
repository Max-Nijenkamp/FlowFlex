<?php

declare(strict_types=1);

namespace App\Actions;

use App\Events\OwnershipTransferred;
use App\Models\User;
use App\Support\Services\AuditLogger;
use App\Support\Services\CompanyContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * The ONLY way ownership moves (core.rbac/ownership): atomically promotes
 * the new owner and demotes the previous one to admin — no window with zero
 * or two owners.
 */
class TransferOwnershipAction
{
    use AsAction;

    public function handle(string $newOwnerId): void
    {
        $company = app(CompanyContext::class)->current();
        setPermissionsTeamId($company->id);

        /** @var User|null $newOwner */
        $newOwner = User::query()->find($newOwnerId);

        if ($newOwner === null) {
            throw new InvalidArgumentException('The new owner must be a member of this company.');
        }

        if ($newOwner->email_verified_at === null) {
            throw new InvalidArgumentException('The new owner must have a verified email address.');
        }

        DB::transaction(function () use ($company, $newOwner): void {
            $currentOwnerId = DB::table('model_has_roles')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->where('roles.name', 'owner')
                ->where('roles.company_id', $company->id)
                ->lockForUpdate()
                ->value('model_has_roles.model_id');

            if ($currentOwnerId === $newOwner->id) {
                return; // already the owner — nothing to transfer
            }

            $newOwner->assignRole('owner');
            $newOwner->removeRole('admin');

            if ($currentOwnerId !== null) {
                /** @var User $previous */
                $previous = User::query()->findOrFail($currentOwnerId);
                $previous->removeRole('owner');
                $previous->assignRole('admin');
            }

            $causer = Auth::user();
            app(AuditLogger::class)->log(
                'core.ownership-transferred',
                $company,
                $causer instanceof User ? $causer : null,
                ['from' => $currentOwnerId, 'to' => $newOwner->id],
            );

            OwnershipTransferred::dispatch($company->id, (string) $currentOwnerId, $newOwner->id);
        });
    }
}
