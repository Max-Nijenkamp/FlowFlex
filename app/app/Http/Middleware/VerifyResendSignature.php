<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verifies the Svix signature on inbound Resend webhooks before the controller runs.
 * Rejects unsigned / invalid requests with 403. Secret: env RESEND_WEBHOOK_SECRET
 * (Svix format: "whsec_<base64>"). See architecture/security #stripe-webhook (same pattern).
 */
class VerifyResendSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = (string) config('services.resend.webhook_secret');
        $svixId = (string) $request->header('svix-id');
        $svixTimestamp = (string) $request->header('svix-timestamp');
        $svixSignature = (string) $request->header('svix-signature');

        abort_if($secret === '' || $svixId === '' || $svixTimestamp === '' || $svixSignature === '', 403, 'Missing signature.');

        $key = base64_decode(str_starts_with($secret, 'whsec_') ? substr($secret, 6) : $secret, true);
        abort_if($key === false, 403, 'Bad signing secret.');

        $signedContent = "{$svixId}.{$svixTimestamp}.{$request->getContent()}";
        $expected = base64_encode(hash_hmac('sha256', $signedContent, $key, true));

        // svix-signature header may carry multiple space-separated "v1,<sig>" values.
        $valid = collect(explode(' ', $svixSignature))
            ->map(fn (string $part) => str_contains($part, ',') ? explode(',', $part, 2)[1] : $part)
            ->contains(fn (string $sig) => hash_equals($expected, $sig));

        abort_unless($valid, 403, 'Invalid signature.');

        return $next($request);
    }
}
