<?php

declare(strict_types=1);

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Core\BillingSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StripeWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $sig    = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        if ($secret) {
            try {
                \Stripe\Webhook::constructEvent($request->getContent(), $sig, $secret);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        }

        $payload = $request->json()->all();
        $type    = $payload['type'] ?? '';

        match ($type) {
            'invoice.payment_succeeded'        => $this->handlePaymentSucceeded($payload),
            'invoice.payment_failed'           => $this->handlePaymentFailed($payload),
            'customer.subscription.updated'    => $this->handleSubscriptionUpdated($payload),
            'customer.subscription.deleted'    => $this->handleSubscriptionDeleted($payload),
            default                            => null,
        };

        return response()->json(['received' => true]);
    }

    private function handlePaymentSucceeded(array $payload): void
    {
        $stripeSubId = $payload['data']['object']['subscription'] ?? null;

        if (! $stripeSubId) {
            return;
        }

        BillingSubscription::withoutGlobalScopes()
            ->where('stripe_subscription_id', $stripeSubId)
            ->update(['status' => 'active']);
    }

    private function handlePaymentFailed(array $payload): void
    {
        $stripeSubId = $payload['data']['object']['subscription'] ?? null;

        if (! $stripeSubId) {
            return;
        }

        BillingSubscription::withoutGlobalScopes()
            ->where('stripe_subscription_id', $stripeSubId)
            ->update(['status' => 'past_due']);
    }

    private function handleSubscriptionUpdated(array $payload): void
    {
        $object      = $payload['data']['object'] ?? [];
        $stripeSubId = $object['id'] ?? null;
        $status      = $object['status'] ?? null;

        if (! $stripeSubId || ! $status) {
            return;
        }

        BillingSubscription::withoutGlobalScopes()
            ->where('stripe_subscription_id', $stripeSubId)
            ->update(['status' => $status]);
    }

    private function handleSubscriptionDeleted(array $payload): void
    {
        $stripeSubId = $payload['data']['object']['id'] ?? null;

        if (! $stripeSubId) {
            return;
        }

        BillingSubscription::withoutGlobalScopes()
            ->where('stripe_subscription_id', $stripeSubId)
            ->update([
                'status'  => 'canceled',
                'ends_at' => now(),
            ]);
    }
}
