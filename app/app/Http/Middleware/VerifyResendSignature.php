<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resend webhooks are svix-signed: HMAC-SHA256 over "{id}.{timestamp}.{body}"
 * with the base64 secret from the whsec_ signing key. Invalid or missing
 * signature -> 403, nothing processed.
 */
class VerifyResendSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = (string) config('services.resend.webhook_secret');
        $id = (string) $request->header('svix-id');
        $timestamp = (string) $request->header('svix-timestamp');
        $signatures = (string) $request->header('svix-signature');

        if ($secret === '' || $id === '' || $timestamp === '' || $signatures === '') {
            abort(403, 'Invalid webhook signature.');
        }

        $key = base64_decode(str_replace('whsec_', '', $secret), true);
        if ($key === false) {
            abort(403, 'Invalid webhook signature.');
        }

        $expected = base64_encode(
            hash_hmac('sha256', "{$id}.{$timestamp}.{$request->getContent()}", $key, true)
        );

        foreach (explode(' ', $signatures) as $candidate) {
            $value = str_contains($candidate, ',') ? explode(',', $candidate, 2)[1] : $candidate;

            if (hash_equals($expected, $value)) {
                return $next($request);
            }
        }

        abort(403, 'Invalid webhook signature.');
    }
}
