<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\AssignRolesData;
use App\Exceptions\CannotRemoveLastOwnerException;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class AssignRolesAction
{
    use AsAction;

    public function handle(AssignRolesData $data): void
    {
        $user = User::query()->findOrFail($data->user_id);

        // Removing `owner` from the only owner is forbidden.
        if ($user->hasRole('owner') && ! in_array('owner', $data->roles, true) && $this->isLastOwner($user)) {
            throw new CannotRemoveLastOwnerException;
        }

        $user->syncRoles($data->roles);
    }

    private function isLastOwner(User $user): bool
    {
        return User::query()
            ->whereHas('roles', fn ($q) => $q->where('name', 'owner'))
            ->whereKeyNot($user->id)
            ->doesntExist();
    }
}
