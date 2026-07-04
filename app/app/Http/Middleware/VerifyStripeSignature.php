<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\WebhookSignature;
use Symfony\Component\HttpFoundation\Response;

/** Stripe webhook edge: signature-verified before any handler runs. */
class VerifyStripeSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = (string) config('services.stripe.webhook_secret');

        if ($secret === '') {
            abort(503, 'Stripe webhooks are not configured.');
        }

        try {
            WebhookSignature::verifyHeader(
                $request->getContent(),
                (string) $request->header('Stripe-Signature'),
                $secret,
            );
        } catch (SignatureVerificationException) {
            abort(403, 'Invalid Stripe signature.');
        }

        return $next($request);
    }
}
