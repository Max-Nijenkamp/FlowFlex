<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use App\Contracts\BillingServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StripeWebhookController
{
    public function __invoke(Request $request, BillingServiceInterface $billing): JsonResponse
    {
        $billing->handleStripeWebhook($request->json()->all());

        return response()->json(['received' => true]);
    }
}
