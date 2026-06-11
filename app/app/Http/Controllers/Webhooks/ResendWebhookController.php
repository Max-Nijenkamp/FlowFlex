<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use App\Actions\Foundation\HandleEmailBounceAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResendWebhookController
{
    /**
     * Inbound Resend (Svix) webhook. Signature already verified by
     * VerifyResendSignature middleware on the route.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $type = (string) $request->input('type');
        $email = (string) data_get($request->input('data'), 'to.0', data_get($request->input('data'), 'email', ''));

        if ($email !== '' && in_array($type, ['email.bounced', 'email.complained'], true)) {
            $bounceType = $type === 'email.bounced'
                ? (string) data_get($request->input('data'), 'bounce.type', 'hard')
                : 'hard';

            HandleEmailBounceAction::run($email, $bounceType);
        }

        return response()->json(['received' => true]);
    }
}
