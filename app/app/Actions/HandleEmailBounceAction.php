<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Hard bounce -> flag every user with that address undeliverable.
 * Runs outside tenant context (an address may exist in several companies);
 * soft bounces / complaints are a tracked roadmap feature (email suppression list).
 */
class HandleEmailBounceAction
{
    use AsAction;

    public function handle(string $email): int
    {
        return User::query()
            ->withoutGlobalScopes()
            ->where('email', $email)
            ->update(['email_deliverable' => false]);
    }
}
