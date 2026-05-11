<?php

declare(strict_types=1);

namespace App\Services\Core;

use App\Jobs\DeliverWebhookJob;
use App\Models\Core\WebhookEndpoint;

class WebhookDeliveryService
{
    public function dispatch(string $companyId, string $event, array $payload): void
    {
        WebhookEndpoint::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->whereJsonContains('events', $event)
            ->each(function (WebhookEndpoint $endpoint) use ($event, $payload): void {
                DeliverWebhookJob::dispatch($endpoint, $event, $payload);
            });
    }

    public function deliver(WebhookEndpoint $endpoint, string $event, array $payload): void
    {
        $body      = json_encode($payload, JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $body, $endpoint->secret);

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Content-Type'          => 'application/json',
            'X-FlowFlex-Event'      => $event,
            'X-FlowFlex-Signature'  => $signature,
        ])->post($endpoint->url, $payload);

        $endpoint->update([
            'last_triggered_at' => now(),
            'failure_count'     => $response->successful() ? 0 : $endpoint->failure_count + 1,
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException(
                "Webhook delivery failed for endpoint {$endpoint->id}: HTTP {$response->status()}"
            );
        }
    }
}
