<?php

declare(strict_types=1);

namespace App\Actions\Core;

use App\Models\Core\WebhookEndpoint;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class RotateWebhookSecretAction
{
    use AsAction;

    /** Returns the new plain secret — shown once, never retrievable again. */
    public function handle(string $endpointId): string
    {
        $secret = 'whsec_'.Str::random(40);

        WebhookEndpoint::query()->findOrFail($endpointId)->update(['secret' => $secret]);

        return $secret;
    }
}
