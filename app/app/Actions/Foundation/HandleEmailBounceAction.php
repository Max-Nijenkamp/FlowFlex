<?php

declare(strict_types=1);

namespace App\Actions\Foundation;

use App\Models\User;
use App\Support\Scopes\CompanyScope;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class HandleEmailBounceAction
{
    use AsAction;

    /**
     * Hard bounce → flag the user undeliverable (across all tenants — bounce is
     * address-level, resolved by email). Soft bounce → log only.
     */
    public function handle(string $email, string $bounceType): void
    {
        if ($bounceType !== 'hard') {
            Log::info('Soft email bounce', ['email' => $email, 'type' => $bounceType]);

            return;
        }

        User::query()
            ->withoutGlobalScope(CompanyScope::class)
            ->where('email', $email)
            ->update(['email_deliverable' => false]);

        Log::warning('Hard email bounce — user flagged undeliverable', ['email' => $email]);
    }
}
