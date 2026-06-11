<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\DeliverWebhookJob;
use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use Lorisleiva\Actions\Concerns\AsAction;

class SendTestWebhookAction
{
    use AsAction;

    public function handle(string $endpointId): WebhookDelivery
    {
        $endpoint = WebhookEndpoint::query()->findOrFail($endpointId);

        $payload = [
            'event' => 'webhook.test',
            'company_id' => $endpoint->company_id,
            'occurred_at' => now()->toIso8601String(),
            'data' => ['message' => 'FlowFlex test delivery'],
        ];

        $delivery = WebhookDelivery::create([
            'endpoint_id' => $endpoint->id,
            'company_id' => $endpoint->company_id,
            'event_type' => 'webhook.test',
            'payload' => $payload,
        ]);

        DeliverWebhookJob::dispatch($endpoint->id, 'webhook.test', $payload, $delivery->id);

        return $delivery;
    }
}
