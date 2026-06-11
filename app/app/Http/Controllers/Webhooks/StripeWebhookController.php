<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use App\Contracts\Core\BillingServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StripeWebhookController
{
    public function __construct(
        private readonly BillingServiceInterface $billing,
    ) {}

    /** Signature verified by VerifyStripeSignature middleware on the route. */
    public function __invoke(Request $request): JsonResponse
    {
        $this->billing->handleStripeWebhook($request->json()->all());

        return response()->json(['received' => true]);
    }
}
