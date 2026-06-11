<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verifies the Stripe-Signature header before the controller runs.
 * Secret: services.stripe.webhook_secret. Rejects invalid requests with 400.
 */
class VerifyStripeSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = (string) config('services.stripe.webhook_secret');

        abort_if($secret === '', 400, 'Stripe webhook secret not configured.');

        try {
            Webhook::constructEvent(
                $request->getContent(),
                (string) $request->header('Stripe-Signature'),
                $secret,
            );
        } catch (SignatureVerificationException|\UnexpectedValueException) {
            abort(400, 'Invalid signature.');
        }

        return $next($request);
    }
}
