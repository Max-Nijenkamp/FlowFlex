<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\EmailSuppression;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Hard bounce or spam complaint -> suppress the address immediately
 * (foundation.email suppression list). Bounces additionally flag any user
 * accounts with that address so the panels can surface the broken email.
 * Runs outside tenant context: deliverability is address-global.
 */
class SuppressEmailAction
{
    use AsAction;

    public function handle(string $email, string $reason): void
    {
        EmailSuppression::query()->updateOrCreate(
            ['email' => $email],
            ['reason' => $reason, 'suppressed_at' => now()],
        );

        if ($reason === 'bounce') {
            HandleEmailBounceAction::run($email);
        }
    }
}
